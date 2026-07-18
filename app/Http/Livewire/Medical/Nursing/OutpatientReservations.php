<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\reservations;
use App\Models\administrative\admsclinic;
use App\Models\hr\Employee;
use App\Traits\web\get_contract;
use Carbon\Carbon;

class OutpatientReservations extends Component
{
    use WithPagination, get_contract;

    public $search = '';
    public $clinic_id = '';
    public $doctor_id = '';
    public $status = '';

    public $cancelId;
    public $cancelReason = '';

    public $clinics = [];
    public $doctors = [];

    public $lastRefresh;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshTable' => 'refreshData'];

    public function mount()
{
    $this->clinics = admsclinic::all();
    $this->doctors = Employee::where('employee_type', 'physician')->get();
    $this->lastRefresh = now()->format('h:i A'); // <-- تعديل هنا
}

public function refreshData()
{
    $this->lastRefresh = now()->format('h:i A'); // <-- تعديل هنا
}


    public function updatingSearch()
    {
        $this->resetPage();
    }

   

    public function cancelModal($id)
    {
        $this->cancelId = $id;
        $this->dispatchBrowserEvent('show-cancel-modal');
    }

    public function confirmCancel()
    {
        $this->validate([
            'cancelReason' => 'required|string|min:3',
        ]);

        $reservation = reservations::find($this->cancelId);

        if ($reservation) {
            $reservation->status = 'cancelled';
            $reservation->reason = $this->cancelReason;
            $reservation->save();

            $this->dispatchBrowserEvent('hide-cancel-modal');
            $this->emit('refreshTable');
        }
    }

    public function render()
    {
        $today = now()->toDateString();

        $query = reservations::with(['doctor', 'clinic', 'patient', 'patient_visits'])
            ->where('date_reservations', $today);

        if ($this->clinic_id) {
            $query->where('clinic_id', $this->clinic_id);
        }

        if ($this->doctor_id) {
            $query->where('id_doctor', $this->doctor_id);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->whereHas('patient', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('medical_number', 'like', '%' . $this->search . '%');
            });
        }

        $reservations = $query->paginate(10);

        $stats = [
            'total' => reservations::where('date_reservations', $today)->count(),
            'reserved' => reservations::where('date_reservations', $today)->where('status', 'unconfirmed')->count(),
            'pending' => reservations::where('date_reservations', $today)
                          ->whereIn('status', ['confirm_waiting_for_payment','confirmed_paid'])
                          ->count(),
            'nursing_session_ended' => reservations::where('date_reservations', $today)->where('status', 'nursing_session_ended')->count(),
            'cancelled' => reservations::where('date_reservations', $today)->where('status', 'cancelled')->count(),
        ];
        

        return view('livewire.medical.nursing.outpatient-reservations', compact('reservations', 'stats'));
    }
}