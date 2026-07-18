<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\models\general\namedictionary as gs_name_dictionary;
use App\models\administrative\medical_record as patient;
use App\models\general\IgnoredName;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PatientNameReview extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $loading = false;

    public $selectedName;
    public $selectedArabicName;
    public $selectedEnglishName;
    public $selectedGender = 'undefined';
    public $patientIds = [];
    public $showPatientIds = false;

    protected $rules = [
        'selectedEnglishName' => 'required|string|max:255',
        'selectedGender' => 'required|in:male,female,undefined',
    ];

    public function mount()
    {
        $this->loading = true;
    }

    public function loadPatientIds($name)
    {
        $this->patientIds = patient::where(function ($q) use ($name) {
            $q->where('first_name_ar', $name)
                ->orWhere('second_name_ar', $name)
                ->orWhere('third_name_ar', $name)
                ->orWhere('fourth_name_ar', $name)
                ->orWhere('first_name_en', $name)
                ->orWhere('second_name_en', $name)
                ->orWhere('third_name_en', $name)
                ->orWhere('fourth_name_en', $name);
        })
        ->pluck('medical_number')
        ->take(100)
        ->toArray();

        $this->selectedName = $name;
        $this->showPatientIds = true;
    }

    public function selectName($name, $arabicName = '', $englishName = '', $gender = 'undefined')
    {
        $this->selectedName = $name;
        $this->selectedArabicName = $arabicName ?: $name;
        $this->selectedEnglishName = $englishName ?: $name;
        $this->selectedGender = $gender ?: 'undefined';

        $this->skipRender();
    }

    public function saveName()
    {
        $this->validate();

        if (empty($this->selectedArabicName)) {
            session()->flash('error', '⚠️ يجب إدخال الاسم العربي.');
            return;
        }

        try {
            DB::transaction(function () {
                gs_name_dictionary::create([
                    'name_ar' => $this->selectedArabicName,
                    'name_en' => $this->selectedEnglishName,
                    'gender' => $this->selectedGender,
                    'status' => 'active',
                    'user_id' => Auth::id(),
                ]);
            });

            $this->reset([
                'selectedName',
                'selectedArabicName',
                'selectedEnglishName',
                'selectedGender',
                'patientIds',
                'showPatientIds'
            ]);

            $this->resetPage();

            session()->flash('message', '✅ تمت إضافة الاسم إلى القاموس بنجاح.');
            $this->dispatchBrowserEvent('close-modal');
        } catch (\Exception $e) {
            session()->flash('error', '❌ حدث خطأ أثناء حفظ الاسم: ' . $e->getMessage());
        }
    }

    public function ignoreName($name)
    {
        try {
            IgnoredName::firstOrCreate(['name' => $name]);

            $this->resetPage();

            session()->flash('message', '✅ تم تجاهل الاسم ولن يظهر مرة أخرى.');
        } catch (\Exception $e) {
            session()->flash('error', '❌ حدث خطأ أثناء تجاهل الاسم.');
        }
    }

    public function getStatsProperty()
    {
        return [
            'total_patients' => patient::count(),
            'verified_names' => gs_name_dictionary::count(),
            'pending_review' => $this->getBaseCollection()->count(),
            'ignored_names' => IgnoredName::count(),
        ];
    }

    protected function getBaseCollection()
    {
        $names = patient::query()
            ->selectRaw('
                TRIM(
                    COALESCE(
                        NULLIF(first_name_ar, ""),
                        NULLIF(second_name_ar, ""),
                        NULLIF(third_name_ar, ""),
                        NULLIF(fourth_name_ar, ""),
                        NULLIF(first_name_en, ""),
                        NULLIF(second_name_en, ""),
                        NULLIF(third_name_en, ""),
                        NULLIF(fourth_name_en, "")
                    )
                ) AS display_name,
                MIN(gender) AS gender,
                COUNT(*) AS count_records,
                MIN(user_id) AS user_id
            ')
            ->where(function ($q) {
                $q->whereNotNull('first_name_ar')
                    ->orWhereNotNull('second_name_ar')
                    ->orWhereNotNull('third_name_ar')
                    ->orWhereNotNull('fourth_name_ar')
                    ->orWhereNotNull('first_name_en')
                    ->orWhereNotNull('second_name_en')
                    ->orWhereNotNull('third_name_en')
                    ->orWhereNotNull('fourth_name_en');
            })
            ->whereRaw('
                LENGTH(TRIM(
                    COALESCE(
                        NULLIF(first_name_ar, ""),
                        NULLIF(second_name_ar, ""),
                        NULLIF(third_name_ar, ""),
                        NULLIF(fourth_name_ar, ""),
                        NULLIF(first_name_en, ""),
                        NULLIF(second_name_en, ""),
                        NULLIF(third_name_en, ""),
                        NULLIF(fourth_name_en, "")
                    )
                )) > 0
            ')
            ->groupBy('display_name')
            ->orderBy('display_name')
            ->get();

        return $names;
    }

    public function render()
    {
        $names = $this->getBaseCollection();

        $verified = gs_name_dictionary::pluck('name_ar')
            ->merge(gs_name_dictionary::pluck('name_en'))
            ->filter()
            ->unique();

        $ignored = IgnoredName::pluck('name')->filter()->unique();

        $filtered = $names->filter(function ($item) use ($verified, $ignored) {
            return !$verified->contains($item->display_name)
                && !$ignored->contains($item->display_name);
        });

        if ($this->search) {
            $filtered = $filtered->filter(function ($item) {
                return stripos($item->display_name, $this->search) !== false;
            });
        }

        $page = $this->page ?? 1;
        $perPage = $this->perPage;

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->forPage($page, $perPage),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $this->loading = false;

        return view('livewire.patient-name-review', [
            'names' => $paginated,
            'users' => User::pluck('name', 'id'),
            'stats' => [
                'total_patients' => patient::count(),
                'verified_names' => $verified->count(),
                'pending_review' => $filtered->count(),
                'ignored_names' => $ignored->count(),
            ],
        ]);
    }
}