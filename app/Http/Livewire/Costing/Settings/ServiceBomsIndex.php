<?php

namespace App\Http\Livewire\costing\settings;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\costing\servicebom;
use App\Models\costing\servicebomitem;
use App\Models\finance\service;
use App\Models\finance\servicesublevel;
use App\Models\finance\servicelevel;
use App\Models\costing\resourcerate;
use App\Models\costing\costcenter;
use DB;

class ServiceBomsIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // فلاتر
    public $search = '';
    public $level_id = '';
    public $sublevel_id = '';
    public $service_id = '';
    public $status = '';
    public $perPage = 10;

    // فورم BOM
    public $editing_id = null;
    public $code = '';
    public $name_ar = '';
    public $name_en = '';
    public $version = 1;
    public $status_input = 'draft';
    public $is_active = true;
    public $notes = '';

    // عناصر BOM
    public $items = []; // array of rows

    protected $queryString = [
        'search' => ['except' => ''],
        'level_id' => ['except' => ''],
        'sublevel_id' => ['except' => ''],
        'service_id' => ['except' => ''],
        'status' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function rules()
    {
        return [
            'service_id'   => ['required','exists:fin_services,id'],
            'code'         => ['required','string','max:50', Rule::unique('service_boms','code')->ignore($this->editing_id)],
            'name_ar'      => ['required','string','max:255'],
            'name_en'      => ['nullable','string','max:255'],
            'version'      => ['required','integer','min:1'],
            'status_input' => ['required', Rule::in(servicebom::statuses())],
            'is_active'    => ['boolean'],
            'notes'        => ['nullable','string'],

            // عناصر
            'items.*.item_type'   => ['required', Rule::in(['supply','drug','resource','misc'])],
            'items.*.qty'         => ['required','numeric','min:0'],
            'items.*.unit_cost'   => ['required','numeric','min:0'],
            'items.*.total_cost'  => ['required','numeric','min:0'],
        ];
    }

    public function resetForm()
    {
        $this->editing_id = null;
        $this->code = '';
        $this->name_ar = '';
        $this->name_en = '';
        $this->version = 1;
        $this->status_input = 'draft';
        $this->is_active = true;
        $this->notes = '';
        $this->items = [];
        $this->service_id = '';
        $this->sublevel_id = '';
        $this->level_id = '';
    }

    public function updatedLevelId()
    {
        $this->sublevel_id = '';
        $this->service_id = '';
    }

    public function updatedSublevelId()
    {
        $this->service_id = '';
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatchBrowserEvent('open-bom-modal');
    }

    public function edit($id)
    {
        $row = servicebom::with('items','service.sublevel.level1')->findOrFail($id);

        $this->editing_id   = $row->id;
        $this->service_id   = $row->service_id;
        $this->sublevel_id  = $row->service->sublevel_id ?? '';
        $this->level_id     = $row->service->sublevel->level_id ?? '';
        $this->code         = $row->code;
        $this->name_ar      = $row->getTranslation('name','ar');
        $this->name_en      = $row->getTranslation('name','en');
        $this->version      = $row->version;
        $this->status_input = $row->status;
        $this->is_active    = (bool) $row->is_active;
        $this->notes        = $row->notes;

        // عناصر
        $this->items = [];
        foreach ($row->items as $i => $it) {
            $this->items[] = [
                'line_no'          => $it->line_no ?: $i+1,
                'item_type'        => $it->item_type,
                'inventory_item_id'=> $it->inventory_item_id,
                'resource_rate_id' => $it->resource_rate_id,
                'cost_center_id'   => $it->cost_center_id,
                'description_ar'   => $it->getTranslation('description','ar'),
                'description_en'   => $it->getTranslation('description','en'),
                'unit'             => $it->unit,
                'qty'              => (float)$it->qty,
                'unit_cost'        => (float)$it->unit_cost,
                'total_cost'       => (float)$it->total_cost,
                'duration_minutes' => $it->duration_minutes,
            ];
        }

        $this->dispatchBrowserEvent('open-bom-modal');
    }

    public function addItem($type = 'supply')
    {
        $this->items[] = [
            'line_no'          => count($this->items) + 1,
            'item_type'        => $type,
            'inventory_item_id'=> null,
            'resource_rate_id' => null,
            'cost_center_id'   => null,
            'description_ar'   => '',
            'description_en'   => '',
            'unit'             => '',
            'qty'              => 1,
            'unit_cost'        => 0,
            'total_cost'       => 0,
            'duration_minutes' => null,
        ];
    }

    public function removeItem($index)
    {
        if (isset($this->items[$index])) {
            array_splice($this->items, $index, 1);
            foreach ($this->items as $i => &$it) $it['line_no'] = $i + 1;
        }
    }

    public function recalcRow($index)
    {
        if (!isset($this->items[$index])) return;
        $qty  = (float)($this->items[$index]['qty'] ?? 0);
        $unit = (float)($this->items[$index]['unit_cost'] ?? 0);
        $this->items[$index]['total_cost'] = round($qty * $unit, 2);
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $payload = [
                'service_id' => $this->service_id,
                'code'       => $this->code,
                'name'       => [
                    'ar' => $this->name_ar,
                    'en' => $this->name_en ?: $this->name_ar,
                ],
                'version'    => (int)$this->version,
                'status'     => $this->status_input,
                'is_active'  => (bool)$this->is_active,
                'notes'      => $this->notes,
                'user_id'    => Auth::id(),
            ];

            if ($this->editing_id) {
                $bom = servicebom::findOrFail($this->editing_id);
                $bom->fill($payload)->save();
                servicebomitem::where('bom_id', $bom->id)->delete();
            } else {
                $bom = servicebom::create($payload);
            }

            foreach ($this->items as $i => $it) {
                servicebomitem::create([
                    'bom_id'          => $bom->id,
                    'line_no'         => $it['line_no'] ?? ($i+1),
                    'item_type'       => $it['item_type'],
                    'inventory_item_id'=> $it['inventory_item_id'] ?: null,
                    'resource_rate_id'=> $it['resource_rate_id'] ?: null,
                    'cost_center_id'  => $it['cost_center_id'] ?: null,
                    'description'     => [
                        'ar' => $it['description_ar'] ?? '',
                        'en' => $it['description_en'] ?? ($it['description_ar'] ?? ''),
                    ],
                    'unit'            => $it['unit'] ?: null,
                    'qty'             => (float)$it['qty'],
                    'unit_cost'       => (float)$it['unit_cost'],
                    'total_cost'      => (float)$it['total_cost'],
                    'duration_minutes'=> $it['duration_minutes'] ?: null,
                ]);
            }
        });

        session()->flash('success', __('bom_trans.saved_success'));
        $this->dispatchBrowserEvent('close-bom-modal');
        $this->resetForm();
    }

    public function delete($id)
    {
        servicebom::findOrFail($id)->delete();
        session()->flash('success', __('bom_trans.deleted_success'));
    }

    public function getRowsProperty()
    {
        $q = servicebom::query()->with('service');

        if ($this->search) {
            $term = '%'.$this->search.'%';
            $q->where(function($qq) use ($term) {
                $qq->where('code','like',$term)
                   ->orWhere('name->ar','like',$term)
                   ->orWhere('name->en','like',$term)
                   ->orWhereHas('service', function($qs) use ($term) {
                       $qs->where('name->ar','like',$term)
                          ->orWhere('name->en','like',$term);
                   });
            });
        }
        if ($this->status) $q->where('status',$this->status);
        if ($this->service_id) $q->where('service_id',$this->service_id);

        return $q->orderByDesc('id')->paginate($this->perPage);
    }

    public function render()
    {
        $levels    = servicelevel::orderBy('id')->get(['id','name']);
        $sublevels = $this->level_id
            ? servicesublevel::where('level_id',$this->level_id)->orderBy('id')->get(['id','name'])
            : collect();
        $services  = $this->sublevel_id
            ? service::where('sublevel_id',$this->sublevel_id)->orderBy('id')->get(['id','name'])
            : collect();

        return view('livewire.costing.settings.service-boms-index', [
            'rows'      => $this->rows,
            'levels'    => $levels,
            'sublevels' => $sublevels,
            'services'  => $services,
            'statuses'  => servicebom::statuses(),
            'rates'     => resourcerate::orderBy('code')->get(['id','code','name','unit','rate']),
            'centers'   => costcenter::orderBy('code')->get(['id','code','name']),
        ])->layout('layouts.masterliveware');
    }
}