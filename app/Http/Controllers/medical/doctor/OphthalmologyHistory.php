<?php
namespace App\Http\Controllers\medical\doctor;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\mbdiseaseshispat;



class OphthalmologyHistory extends Component
{
    use WithPagination;

    public $diagnosis;
    public $eye = 'OD'; // ✅ قم بتعريف المتغير مع قيمة افتراضية
    public $for_number;
    public $for_period = 'D'; // ✅ تأكد من تعريف for_period أيضًا
    public $remarks;
    public $editingId = null; // لمعرفة ما إذا كنا في وضع التعديل

    protected $rules = [
        'diagnosis' => 'required|string|max:255',
        'eye' => 'required|string|max:10',
        'for_number' => 'nullable|integer',
        'for_period' => 'required|string|max:2',
        'remarks' => 'nullable|string',
    ];

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $record = mbdiseaseshispat::find($this->editingId);
            $record->update($this->getData());
            $this->editingId = null;
        } else {
            mbdiseaseshispat::create($this->getData());
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = mbdiseaseshispat::find($id);
        $this->editingId = $id;
        $this->diagnosis = $record->diseases;
        $this->eye = $record->eye;
        $this->for_number = $record->for_number;
        $this->for_period = $record->for_period;
        $this->remarks = $record->remarks;
    }

    public function delete($id)
    {
        mbdiseaseshispat::find($id)->delete();
    }

    private function getData()
    {
        return [
            'diseases' => $this->diagnosis,
            'eye' => $this->eye,
            'for_number' => $this->for_number,
            'for_period' => $this->for_period,
            'remarks' => $this->remarks,
            'user_id' => Auth::id(),
        ];
    }

    public function resetInput()
    {
        $this->diagnosis = '';
        $this->eye = 'OD'; // ✅ إعادة تعيين القيمة الافتراضية
        $this->for_number = '';
        $this->for_period = 'D';
        $this->remarks = '';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.ophthalmology-history', [
            'histories' => mbdiseaseshispat::latest()->paginate(10),
        ]);
    }
}