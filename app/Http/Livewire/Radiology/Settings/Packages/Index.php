<?php

namespace App\Http\Livewire\radiology\settings\packages;

use Livewire\Component;
use Livewire\WithPagination;
use App\models\administrative\radiologypackages;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirmed'];

    public $package_id;
    public $name = ['ar' => '', 'en' => ''];
    public $description = ['ar' => '', 'en' => ''];
    public $services = [];
    public $start_date;
    public $end_date;
    public $age_from;
    public $age_to;
    public $gender = 'both';
    public $status = 'active';

    public $search = '';

    protected $rules = [
        'name.ar'        => 'required|string|max:255',
        'name.en'        => 'required|string|max:255',
        'description.ar' => 'nullable|string',
        'description.en' => 'nullable|string',
        'services'       => 'nullable|array',
        'start_date'     => 'required|date',
        'end_date'       => 'required|date|after_or_equal:start_date',
        'age_from'       => 'nullable|integer|min:0',
        'age_to'         => 'nullable|integer|min:0|gte:age_from',
        'gender'         => 'required|in:male,female,both',
        'status'         => 'required|in:active,inactive',
    ];

    protected $messages = [
        'name.ar.required'        => 'الاسم بالعربية مطلوب.',
        'name.en.required'        => 'الاسم بالإنجليزية مطلوب.',
        'start_date.required'     => 'تاريخ البداية مطلوب.',
        'end_date.required'       => 'تاريخ النهاية مطلوب.',
        'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية.',
        'gender.required'         => 'يجب تحديد الجنس.',
    ];

  public function save()
{
    $this->validate();

    $data = [
        'name'        => $this->name,
        'description' => $this->description,
        'services'    => $this->services,
        'start_date'  => $this->start_date,
        'end_date'    => $this->end_date,
        'age_from'    => $this->age_from,
        'age_to'      => $this->age_to,
        'gender'      => $this->gender,
        'status'      => $this->status,
    ];

    if ($this->package_id) {
        $package = radiologypackages::findOrFail($this->package_id);
        $package->update($data);
        session()->flash('success', '✅ تم تحديث الباقة بنجاح.');
    } else {
        radiologypackages::create($data);
        session()->flash('success', '✅ تم إنشاء الباقة بنجاح.');
    }

    // ✅ إعادة تهيئة الحقول
    $this->resetInput();

    // ✅ إعادة الصفحة الأولى عشان يظهر السجل الجديد
    $this->resetPage();
}

    public function edit($id)
    {
        $package = radiologypackages::findOrFail($id);

        $this->package_id  = $package->id;
        $this->name        = [
            'ar' => $package->getTranslation('name', 'ar'),
            'en' => $package->getTranslation('name', 'en'),
        ];
        $this->description = [
            'ar' => $package->getTranslation('description', 'ar'),
            'en' => $package->getTranslation('description', 'en'),
        ];
        $this->services    = $package->services ?? [];
        $this->start_date  = $package->start_date;
        $this->end_date    = $package->end_date;
        $this->age_from    = $package->age_from;
        $this->age_to      = $package->age_to;
        $this->gender      = $package->gender;
        $this->status      = $package->status;
    }

    public function deleteConfirmed($id)
    {
        $package = radiologypackages::findOrFail($id);
        $package->delete();
        session()->flash('success', '✅ تم حذف الباقة بنجاح.');
    }

    public function resetInput()
    {
        $this->package_id  = null;
        $this->name        = ['ar' => '', 'en' => ''];
        $this->description = ['ar' => '', 'en' => ''];
        $this->services    = [];
        $this->start_date  = null;
        $this->end_date    = null;
        $this->age_from    = null;
        $this->age_to      = null;
        $this->gender      = 'both';
        $this->status      = 'active';
    }

   public function render()
{
    $packages = radiologypackages::where(function ($q) {
        $q->where('name->ar', 'like', "%{$this->search}%")
          ->orWhere('name->en', 'like', "%{$this->search}%");
    })
    ->latest() // ✅ يجيب أحدث السجلات أولاً
    ->paginate(10);

    return view('livewire.radiology.settings.packages.index', compact('packages'));
}

}
