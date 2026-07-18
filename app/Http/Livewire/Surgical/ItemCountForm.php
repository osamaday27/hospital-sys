<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Administrative\SurgicalItemCount;
use App\Models\Administrative\Adm_b_surgery_bookings;
use App\Models\Assets\AssetItem;
use App\Models\Administrative\Surgicaldatasetting;
use App\Models\Administrative\Surgery_Medical_Team;
use App\Models\Hr\Employee;
use Illuminate\Support\Facades\Auth;

class ItemCountForm extends Component
{
    public $booking_id;
    public $items = [];
    public $existingItems = [];
    public $availableAssets = [];
    public $allAssets = [];
    public $assetLabels = [];

    public $final_count_verified = false;
    public $notes;
    public $count_date;
    public $count_time;

    public $surgeon_id;
    public $surgeon_signature;
    public $surgeonSearch = '';
    public $surgeons = [];

    public $nurse_id;
    public $nurse_signature;
    public $nurseSearch = '';
    public $nurses = [];

    public $countAlerts = [];

    protected $rules = [
        'final_count_verified' => 'boolean',
        'notes' => 'nullable|string',
        'count_date' => 'nullable|date',
        'count_time' => 'nullable',
        'surgeon_id' => 'nullable|integer|exists:employees,id',
        'nurse_id' => 'nullable|integer|exists:employees,id',
    ];

    public function updated($propertyName)
    {
        if (str_contains($propertyName, 'items.') && str_contains($propertyName, 'final_count')) {
            $index = explode('.', $propertyName)[1] ?? null;
            if (isset($this->items[$index])) {
                $this->checkCountIntegrity($index, $this->items[$index]);
            }
        }

        if (str_contains($propertyName, 'existingItems.') && str_contains($propertyName, 'final_count')) {
            $index = explode('.', $propertyName)[1] ?? null;
            if (isset($this->existingItems[$index])) {
                $this->checkCountIntegrity($index, $this->existingItems[$index]);
            }
        }
    }

    public function checkCountIntegrity($index, $row)
    {
        if (!isset($row['item_name']) || empty($row['item_name'])) {
            unset($this->countAlerts[$index]);
            return;
        }

        $initial = (int)($row['initial_count'] ?? 0);
        $additional = (int)($row['additional_count'] ?? 0);
        $closure = (int)($row['closure_count'] ?? 0);
        $final = (int)($row['final_count'] ?? 0);
        $expected = $initial + $additional - $closure;

        $displayName = $row['display_name'] ?? ($this->assetLabels[$row['item_name']] ?? 'أداة غير معروفة');

        if ($final !== $expected) {
            $this->countAlerts[$index] = [
                'status' => 'error',
             'message' => "⚠️ يوجد خلل في العد للأداة: {$displayName} (المتوقعة: {$expected}، المدخلة: {$final})"

            ];
        } else {
            unset($this->countAlerts[$index]);
        }
    }

    public function updatedSurgeonSearch() { $this->loadSurgeons(); }
    public function updatedNurseSearch() { $this->loadNurses(); }

    public function deleteExistingItem($id)
    {
        $item = SurgicalItemCount::find($id);
        if ($item) {
            $item->delete();
            $this->existingItems = SurgicalItemCount::where('surgery_booking_id', $this->booking_id)
                ->get()
                ->map(fn($itm) => $this->mapExistingItem($itm))->toArray();

            $this->dispatchBrowserEvent('show-toast', ['type' => 'success', 'message' => '🗑️ تم حذف الأداة بنجاح']);
        } else {
            $this->dispatchBrowserEvent('show-toast', ['type' => 'warning', 'message' => '⚠️ لم يتم العثور على الأداة المطلوبة']);
        }
    }

    public function removeItem($index)
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }

        if (isset($this->countAlerts[$index])) unset($this->countAlerts[$index]);

        $this->dispatchBrowserEvent('show-toast', ['type' => 'info', 'message' => '🗑️ تم حذف الصف بنجاح.']);
    }

    public function loadSurgeons()
    {
        $this->surgeons = Employee::query()
            ->where('employee_type', 'physician')
            ->where(function ($q) {
                $q->where('full_name', 'like', "%{$this->surgeonSearch}%")
                    ->orWhere('employee_code', 'like', "%{$this->surgeonSearch}%");
            })
            ->limit(20)->get(['id', 'full_name', 'employee_code']);
    }

    public function loadNurses()
    {
        $this->nurses = Employee::query()
            ->where(function ($q) {
                $q->where('employee_type', 'nursing')
                    ->orWhere('department', 'like', '%تمريض%');
            })
            ->where(function ($q) {
                $q->where('full_name', 'like', "%{$this->nurseSearch}%")
                    ->orWhere('employee_code', 'like', "%{$this->nurseSearch}%");
            })
            ->limit(20)->get(['id', 'full_name', 'employee_code']);
    }

    public function mount($booking_id)
    {
        $this->booking_id = $booking_id;
        $this->count_date = now()->format('Y-m-d');
        $this->count_time = now()->format('H:i');

        $booking = Adm_b_surgery_bookings::with(['services:id'])->find($booking_id);
        $serviceIds = collect();
        if ($booking && $booking->relationLoaded('services')) {
            $serviceIds = $booking->services->pluck('id')->filter()->unique();
        }
        if ($booking && $booking->surgical_id) {
            $serviceIds->push((int)$booking->surgical_id)->unique();
        }

        $toolIds = [];
        if ($serviceIds->isNotEmpty()) {
            $settings = Surgicaldatasetting::whereIn('service_id', $serviceIds)->get();
            foreach ($settings as $set) {
                $toolIds = array_merge($toolIds, $this->normalizeToolIds($set->surgical_tools));
            }
        }

        $toolIds = array_values(array_unique(array_filter($toolIds)));
        $this->availableAssets = !empty($toolIds)
            ? AssetItem::where('status', 'in_service')->where('category_id', '50')->whereIn('id', $toolIds)->get()
            : collect();

        $this->allAssets = AssetItem::where('status', 'in_service')->where('category_id', '50')->get();

        $this->assetLabels = [];
        $mergeAssets = $this->allAssets->keyBy('id')->merge($this->availableAssets->keyBy('id'));
        foreach ($mergeAssets as $asset) {
            $this->assetLabels[$asset->id] = $this->makeAssetLabel($asset);
        }

        $this->existingItems = SurgicalItemCount::where('surgery_booking_id', $this->booking_id)
            ->get()
            ->map(fn($item) => $this->mapExistingItem($item))
            ->toArray();

        $existingIds = collect($this->existingItems)->pluck('item_name')->toArray();
        $this->items = [];
        foreach ($this->availableAssets as $asset) {
            if (!in_array((string)$asset->id, $existingIds, true)) {
                $this->items[] = [
                    'item_name' => (string)$asset->id,
                    'display_name' => $this->assetLabels[$asset->id] ?? '#' . $asset->id,
                    'initial_count' => '',
                    'additional_count' => '',
                    'closure_count' => '',
                    'final_count' => '',
                    'is_existing' => false,
                ];
            }
        }

        $defaultSurgeon = Surgery_Medical_Team::with('medical_team')
            ->where('surgery_bookings_id', $this->booking_id)
            ->where('role', 'surgeon')->first();

        if ($defaultSurgeon) {
            $this->surgeon_id = $defaultSurgeon->medical_team_id;
            $this->surgeon_signature = $defaultSurgeon->medical_team->full_name ?? '';
        }

        if (Auth::check()) {
            $user = Auth::user();
            $employee = Employee::where('id', $user->employee_id)->first();
            if ($employee) {
                $this->nurse_id = $employee->id;
                $this->nurse_signature = $employee->full_name;
            } else {
                $this->nurse_signature = $user->name;
            }
        }

        $this->loadSurgeons();
        $this->loadNurses();
    }

    protected function mapExistingItem($item)
    {
        return [
            'id' => $item->id,
            'item_name' => (string)$item->item_name,
            'display_name' => $this->assetLabels[$item->item_name] ?? '#' . $item->item_name,
            'initial_count' => $item->initial_count,
            'additional_count' => $item->additional_count,
            'closure_count' => $item->closure_count,
            'final_count' => $item->final_count,
            'is_existing' => true,
        ];
    }

    protected function normalizeToolIds($raw)
    {
        if (is_array($raw)) return $raw;
        if (is_string($raw)) return array_filter(explode(',', $raw));
        return [];
    }

    protected function makeAssetLabel($asset)
    {
        foreach (['name', 'asset_name', 'name_ar', 'name_en', 'title', 'label', 'code', 'serial', 'model'] as $col) {
            if (!empty($asset->$col)) return (string)$asset->$col;
        }
        return 'Asset #' . $asset->id;
    }

    public function addItem()
    {
        $this->items[] = [
            'item_name' => '',
            'display_name' => '',
            'initial_count' => '',
            'additional_count' => '',
            'closure_count' => '',
            'final_count' => '',
            'is_existing' => false,
        ];
    }

    public function save()
    {
        foreach ($this->existingItems as $row) {
            SurgicalItemCount::where('surgery_booking_id', $this->booking_id)
                ->where('item_name', $row['item_name'])
                ->update([
                    'initial_count' => (int)$row['initial_count'],
                    'additional_count' => (int)$row['additional_count'],
                    'closure_count' => (int)$row['closure_count'],
                    'final_count' => (int)$row['final_count'],
                    'final_count_verified' => $this->final_count_verified,
                    'notes' => $this->notes,
                    'count_date' => $this->count_date,
                    'count_time' => $this->count_time,
                    'surgeon_signature' => $this->surgeon_id,
                    'nurse_signature' => $this->nurse_id,
                ]);
        }

        foreach ($this->items as $row) {
            if (empty($row['item_name'])) continue;

            $clean = [
                'initial_count' => $row['initial_count'] === '' ? null : (int)$row['initial_count'],
                'additional_count' => $row['additional_count'] === '' ? null : (int)$row['additional_count'],
                'closure_count' => $row['closure_count'] === '' ? null : (int)$row['closure_count'],
                'final_count' => $row['final_count'] === '' ? null : (int)$row['final_count'],
            ];

            SurgicalItemCount::updateOrCreate(
                [
                    'surgery_booking_id' => $this->booking_id,
                    'item_name' => $row['item_name'],
                ],
                [
                    'initial_count' => $clean['initial_count'],
                    'additional_count' => $clean['additional_count'],
                    'closure_count' => $clean['closure_count'],
                    'final_count' => $clean['final_count'],
                    'final_count_verified' => $this->final_count_verified,
                    'notes' => $this->notes,
                    'count_date' => $this->count_date,
                    'count_time' => $this->count_time,
                    'surgeon_signature' => $this->surgeon_id,
                    'nurse_signature' => $this->nurse_id,
                ]
            );
        }

        if ($this->surgeon_id) {
            Surgery_Medical_Team::updateOrCreate(
                ['surgery_bookings_id' => $this->booking_id, 'role' => 'surgeon'],
                ['medical_team_id' => $this->surgeon_id, 'user_id' => Auth::id()]
            );
        }

        if ($this->nurse_id) {
            Surgery_Medical_Team::updateOrCreate(
                ['surgery_bookings_id' => $this->booking_id, 'role' => 'nurse'],
                ['medical_team_id' => $this->nurse_id, 'user_id' => Auth::id()]
            );
        }

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => '✅ تم حفظ بيانات العد بنجاح.'
        ]);
    }

    public function render()
    {
        return view('livewire.surgical.item-count-form', [
            'allAssets' => $this->allAssets,
            'assetLabels' => $this->assetLabels,
            'surgeons' => $this->surgeons,
            'nurses' => $this->nurses,
        ]);
    }
}