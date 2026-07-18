<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Administrative\Surgery_Medical_Team;
use App\Models\Administrative\Adm_B_Surgery_Bookings;
use App\Models\Hr\Employee;
use App\Models\User;
use Livewire\WithPagination;

class Surgerysedicalteam extends Component
{
    use WithPagination;

    public $booking_id;
    public $medical_team_id;
    public $role;
    public $status = 'active';
    public $editingId = null;
    public $search = '';

    protected $rules = [
        'medical_team_id' => 'required|exists:employees,id',
        'role' => 'required|string|max:255',
    ];

    protected $listeners = ['resetSelect2' => 'resetForm'];

    public function mount($booking_id)
    {
        $this->booking_id = $booking_id;
    }

    public function save()
    {
        $this->validate();

        $exists = Surgery_Medical_Team::where('surgery_bookings_id', $this->booking_id)
            ->where('medical_team_id', $this->medical_team_id)
            ->where('role', $this->role)
            ->when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'warning',
                'message' => '⚠️ هذا العضو مضاف بالفعل في الفريق بنفس الدور.'
            ]);
            return;
        }

        Surgery_Medical_Team::updateOrCreate(
            ['id' => $this->editingId],
            [
                'surgery_bookings_id' => $this->booking_id,
                'medical_team_id' => $this->medical_team_id,
                'role' => $this->role,
                'user_id' => auth()->id(),
                'status' => 'assigned',
            ]
        );

        $msg = $this->editingId ? '✅ تم تحديث بيانات العضو بنجاح' : '✅ تم إضافة عضو جديد للفريق الطبي';

        $this->resetForm();

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => $msg
        ]);

        // إعادة تهيئة الـ select2 من الجهة الأمامية
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function delete($id)
    {
        Surgery_Medical_Team::findOrFail($id)->delete();

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'danger',
            'message' => '🗑️ تم حذف عضو الفريق الطبي بنجاح'
        ]);
    }

    public function edit($id)
    {
        $team = Surgery_Medical_Team::findOrFail($id);
        $this->editingId = $team->id;
        $this->medical_team_id = $team->medical_team_id;
        $this->role = $team->role;

        // تحميل القيم داخل select2 عند التعديل
        $this->dispatchBrowserEvent('fill-select2', [
            'medical_team_id' => $this->medical_team_id,
            'role' => $this->role,
        ]);
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->medical_team_id = '';
        $this->role = '';
    }

    public function render()
    {
        $teams = Surgery_Medical_Team::with(['medical_team', 'user'])
            ->where('surgery_bookings_id', $this->booking_id)
            ->when($this->search, function ($q) {
                $q->whereHas('medical_team', function ($sub) {
                    $sub->where('full_name', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.surgical.surgerysedicalteam', [
            'teams' => $teams,
            'employees' => Employee::orderBy('full_name')->get(),
        ]);
    }
}