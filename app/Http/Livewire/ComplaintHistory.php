<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\mbcomplaintshispat;
use App\models\medical\ms_medical_complaints;

class ComplaintHistory extends Component
{
    use WithPagination;

    // علشان ondblclick: Livewire.emit('edit', id) يشتغل
    protected $listeners = [
        'edit' => 'edit',
        'loadLatest' => 'loadLatestComplaint',
    ];

    public $complaints,
        $eye = 'OD',
        $for_number,
        $for_period = 'D',
        $onset,
        $course,
        $remarks;
    public $complan_date;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $suggestions = [];

    // خيارات الإكمال التلقائي
    public $onsetOptions = ['Sudden', 'Gradual', 'Accidental discovery', 'Since birth', 'Since Childhood', 'Unknown'];
    public $courseOptions = ['Progressive', 'Regressive', 'Stationary', 'Remission exacerbation', 'Recurrent'];
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    // استقبال القيم عند تحميل الكومبوننت
    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient    = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit    = $type_visit;
        $this->id_visit      = $id_visit;

        // ✅ اعرض آخر شكوى في الحقول تلقائياً
        $this->loadLatestComplaint();
    }

    // ✅ تحميل أحدث شكوى لهذا المريض وملء الحقول
    public function loadLatestComplaint()
    {
        $latest = mbcomplaintshispat::where('id_patient', $this->id_patient)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latest) {
            // نعيد استخدام منطق edit علشان نقسم الـ duration ونملأ كل الحقول
            $this->edit($latest->id);
        } else {
            // لو مفيش سجلات نرجّع القيم الافتراضية
            $this->resetInput();
        }
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    protected $rules = [
        'complaints'     => 'required|string|max:255',
        'eye'            => 'required|string|max:10',
        'for_number'     => 'required|integer|min:1',
        'for_period'     => 'required|string|max:2',
        'onset'          => 'nullable|string|max:255',
        'course'         => 'nullable|string|max:255',
        'remarks'        => 'nullable|string',
        'complan_date'   => 'nullable|date',
        'id_patient'     => 'required',
        'id_reservation' => 'required',
        'type_visit'     => 'required',
        'id_visit'       => 'required',
    ];

    public function updatedComplaints()
    {
        if (!empty($this->complaints)) {
            $this->suggestions = ms_medical_complaints::where('name', 'like', "%{$this->complaints}%")
                ->pluck('name')
                ->unique()
                ->take(10)
                ->toArray();
        } else {
            $this->suggestions = [];
        }
    }

    public function selectComplaint($value)
    {
        $this->complaints = $value;
        $this->suggestions = [];
    }

    public function save()
    {
        $this->validate();

        // التأكد من حفظ المدة بصيغة `5 D`
        $duration = "{$this->for_number} {$this->for_period}";

        $data = [
            'id_patient'     => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit'       => $this->id_visit,
            'type_visit'     => $this->type_visit,
            'complaints'     => $this->complaints,
            'eye'            => $this->eye,
            'duration'       => $duration, // ✅ حفظ المدة هنا
            'onset'          => $this->onset,
            'course'         => $this->course,
            'remarks'        => $this->remarks,
            'complants_id'   => ms_medical_complaints::where('name', $this->complaints)->value('id'),
            'complan_date'   => $this->complan_date,
            'user_id'        => Auth::id(),
        ];

        if ($this->editingId) {
            mbcomplaintshispat::find($this->editingId)->update($data);
        } else {
            mbcomplaintshispat::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = mbcomplaintshispat::find($id);
        if (!$record) return;

        $this->editingId = $id;
        $this->complaints = $record->complaints;
        $this->eye = $record->eye;

        // تقسيم `duration` إلى `for_number` و `for_period`
        if (!empty($record->duration)) {
            $parts = explode(' ', trim($record->duration));
            $this->for_number = isset($parts[0]) ? (int)$parts[0] : null;
            $this->for_period = isset($parts[1]) ? $parts[1] : 'D';
        } else {
            $this->for_number = null;
            $this->for_period = 'D';
        }

        $this->onset       = $record->onset;
        $this->course      = $record->course;
        $this->remarks     = $record->remarks;
        $this->complan_date = $record->complan_date; // ✅ كانت ناقصة، أضفناها لعرض التاريخ في الحقل
    }

    public function resetInput()
    {
        $this->complaints  = '';
        $this->eye         = 'OD';
        $this->for_number  = 1;
        $this->for_period  = 'D';
        $this->onset       = '';
        $this->course      = '';
        $this->complan_date = now()->format('Y-m-d'); // ✅ تاريخ اليوم كافتراضي
        $this->remarks     = '';
        $this->editingId   = null;
        $this->suggestions = [];
    }

    public function delete($id)
    {
        $record = mbcomplaintshispat::find($id);

        if ($record) {
            $record->delete();
            session()->flash('message', 'Complaint deleted successfully.');
            // ✅ بعد الحذف اعرض آخر شكوى الحالية
            $this->loadLatestComplaint();
        } else {
            session()->flash('error', 'Complaint not found.');
        }
    }

    public function render()
    {
        return view('livewire.complaint-history', [
            'histories' => mbcomplaintshispat::where('complaints', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}