<?php
namespace App\Http\Livewire\surgical;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\medical\anesthesiaapprovalm;
use App\Models\administrative\adm_b_surgery_bookings;
use Illuminate\Support\Facades\Auth;
use App\Traits\web\file_storage;

class AnesthesiaApproval extends Component
{
    use WithFileUploads, file_storage;

    public $booking_id;
    public $doctor_name;
    public $approval_date;
    public $notes;
    public $approval_file;
    public $approval_data;
    public $tab = 'form'; // ✅ للتحكم في التبويبات

    public function mount($booking_id)
    {
        $this->booking_id = $booking_id;
        $this->doctor_name = Auth::user()->name ?? '';

        // جلب بيانات الموافقة إن وجدت
        $this->approval_data = anesthesiaapprovalm::where('surgery_booking_id', $booking_id)->first();

        if ($this->approval_data) {
            $this->approval_date = $this->approval_data->approval_date;
            $this->notes = $this->approval_data->notes;
        } else {
            $this->approval_date = now()->format('Y-m-d');
        }
    }

 public function save()
{
    $this->validate([
        'approval_date' => 'required|date',
        'approval_file' => $this->approval_data
            ? 'nullable|file|mimes:pdf,jpg,jpeg,png'
            : 'required|file|mimes:pdf,jpg,jpeg,png',
    ]);

    $path = $this->approval_data->approval_file ?? null;

    if ($this->approval_file) {
        // ✅ هنا التعديل الصحيح
        $path = $this->file_storage($this->approval_file, 'anesthesia_approvals');
    }

    anesthesiaapprovalm::updateOrCreate(
        ['surgery_booking_id' => $this->booking_id],
        [
            'doctor_name' => Auth::user()->name,
            'approval_date' => $this->approval_date,
            'notes' => $this->notes,
            'approval_file' => $path,
            'status' => 'approved',
            'user_id' => Auth::id(),
        ]
    );

    $this->approval_data = anesthesiaapprovalm::where('surgery_booking_id', $this->booking_id)->first();

    $this->dispatchBrowserEvent('show-toast', [
        'type' => 'success',
        'message' => __('surgical_trans.anesthesia_saved_successfully'),
    ]);

    $this->tab = 'view';
}


    public function render()
    {
        return view('livewire.surgical.anesthesia-approval');
    }
}