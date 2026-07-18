<?php

namespace App\Http\Livewire\Radiology\Settings\Rooms;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\radiologyroomssettings;
use App\Models\general\adm_s_buildings;
use App\Models\general\adm_s_floors;
use App\Models\general\branch;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['page', 'search', 'activeTab'];

    public $search = '';
    public $activeTab = 'list';

    // 🏥 خصائص الغرفة
    public $room_id, $code;
    public $name = ['ar' => '', 'en' => ''];
    public $branch_id, $building_id, $floor_id;
    public $capacity_per_hour = 0;
    public $status = 'active';

    // بيانات المساعدة
    public $branches = [], $buildings = [], $floors = [];

    protected function rules()
    {
        return [
            'code' => ['required','string','max:255',
                Rule::unique('rad_s_rooms', 'code')->ignore($this->room_id, 'id')],
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'building_id' => 'required|exists:adm_s_buildings,id',
            'floor_id' => 'required|exists:adm_s_floors,id',
            'capacity_per_hour' => 'required|integer|min:0',
        ];
    }

    // ✅ الرسائل التحذيرية بالعربي
    protected $messages = [
        'code.required' => '⚠️ كود الغرفة مطلوب.',
        'code.unique'   => '⚠️ كود الغرفة مستخدم من قبل.',
        'code.string'   => '⚠️ كود الغرفة يجب أن يكون نصًا.',
        'code.max'      => '⚠️ كود الغرفة يجب ألا يزيد عن 255 حرفًا.',

        'name.ar.required' => '⚠️ اسم الغرفة بالعربية مطلوب.',
        'name.ar.string'   => '⚠️ اسم الغرفة بالعربية يجب أن يكون نصًا.',
        'name.ar.max'      => '⚠️ اسم الغرفة بالعربية يجب ألا يزيد عن 255 حرفًا.',

        'name.en.required' => '⚠️ اسم الغرفة بالإنجليزية مطلوب.',
        'name.en.string'   => '⚠️ اسم الغرفة بالإنجليزية يجب أن يكون نصًا.',
        'name.en.max'      => '⚠️ اسم الغرفة بالإنجليزية يجب ألا يزيد عن 255 حرفًا.',

        'branch_id.required' => '⚠️ اختيار الفرع مطلوب.',
        'branch_id.exists'   => '⚠️ الفرع المحدد غير موجود.',

        'building_id.required' => '⚠️ اختيار المبنى مطلوب.',
        'building_id.exists'   => '⚠️ المبنى المحدد غير موجود.',

        'floor_id.required' => '⚠️ اختيار الدور مطلوب.',
        'floor_id.exists'   => '⚠️ الدور المحدد غير موجود.',

        'capacity_per_hour.required' => '⚠️ السعة لكل ساعة مطلوبة.',
        'capacity_per_hour.integer'  => '⚠️ السعة لكل ساعة يجب أن تكون رقمًا صحيحًا.',
        'capacity_per_hour.min'      => '⚠️ السعة لكل ساعة يجب ألا تقل عن 0.',
    ];

    public function mount()
    {
        $this->branches = branch::all();
    }

    public function updatingSearch() { $this->resetPage(); }

    public function updatedBranchId($value)
    {
        $this->buildings = adm_s_buildings::where('branch_id', $value)->get();
        $this->building_id = null;
        $this->floors = [];
        $this->floor_id = null;
    }

    public function updatedBuildingId($value)
    {
        $this->floors = adm_s_floors::where('building_id', $value)->get();
        $this->floor_id = null;
    }

    // ✅ حفظ / تعديل
    public function save()
    {
        $this->validate();

        if ($this->room_id) {
            $room = radiologyroomssettings::findOrFail($this->room_id);
            $room->code = $this->code;
            $room->setTranslations('name', [
                'ar' => $this->name['ar'],
                'en' => $this->name['en'],
            ]);
            $room->branch_id = $this->branch_id;
            $room->building_id = $this->building_id;
            $room->floor_id = $this->floor_id;
            $room->capacity_per_hour = $this->capacity_per_hour;
            $room->status = $this->status;
            $room->save();

            session()->flash('success', '✅ تم تعديل الغرفة بنجاح');
        } else {
            $room = new radiologyroomssettings();
            $room->code = $this->code;
            $room->setTranslations('name', [
                'ar' => $this->name['ar'],
                'en' => $this->name['en'],
            ]);
            $room->branch_id = $this->branch_id;
            $room->building_id = $this->building_id;
            $room->floor_id = $this->floor_id;
            $room->capacity_per_hour = $this->capacity_per_hour;
            $room->status = $this->status;
            $room->save();

            session()->flash('success', '✅ تم إضافة الغرفة بنجاح');
        }

        $this->resetInput();
        $this->activeTab = 'list';
    }

    public function edit($id)
    {
        $room = radiologyroomssettings::findOrFail($id);

        $this->room_id = $room->id;
        $this->code = $room->code;
        $this->name = [
            'ar' => $room->getTranslation('name', 'ar'),
            'en' => $room->getTranslation('name', 'en'),
        ];
        $this->branch_id = $room->branch_id;
        $this->building_id = $room->building_id;
        $this->floor_id = $room->floor_id;
        $this->capacity_per_hour = $room->capacity_per_hour;
        $this->status = $room->status;

        $this->buildings = adm_s_buildings::where('branch_id', $room->branch_id)->get();
        $this->floors = adm_s_floors::where('building_id', $room->building_id)->get();

        $this->activeTab = 'form';
    }

    public function delete($id)
    {
        radiologyroomssettings::findOrFail($id)->delete();
        session()->flash('success', '✅ تم حذف الغرفة بنجاح');
    }

    public function resetInput()
    {
        $this->room_id = null;
        $this->code = '';
        $this->name = ['ar' => '', 'en' => ''];
        $this->branch_id = null;
        $this->building_id = null;
        $this->floor_id = null;
        $this->capacity_per_hour = 0;
        $this->status = 'active';
        $this->buildings = [];
        $this->floors = [];
    }

    public function render()
    {
        $rooms = radiologyroomssettings::with(['branch', 'building', 'floor'])
            ->when($this->search, fn($query) =>
                $query->where('code','like','%'.$this->search.'%')
                      ->orWhere('name->ar','like','%'.$this->search.'%')
                      ->orWhere('name->en','like','%'.$this->search.'%'))
            ->paginate(10);

        return view('livewire.radiology.settings.rooms.index', ['rooms' => $rooms]);
    }
}
