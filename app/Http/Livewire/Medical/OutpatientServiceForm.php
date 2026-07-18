<?php

namespace App\Http\Livewire\Medical;

use Livewire\Component;
use App\Models\finance\invoices;
use App\Models\finance\finservicevisit;
use App\Models\finance\service;
use App\Models\administrative\reservations;
use Illuminate\Support\Facades\Auth;

class OutpatientServiceForm extends Component
{
    public $visit_id, $type_visit;

    public $reservation_id, $patient_id;
    public $services = [];
    public $selected_service;
    public $quantity = 1;
    public $discount = 0;

    public $total = 0;
    public $contract_value = 0;
    public $patient_value = 0;
    public $paid = 0;
    public $unpaid = 0;
    public function mount($reservation_id = null, $patient_id = null, $visit_id = null, $type = null)
    {
        $this->reservation_id = $reservation_id;
        $this->patient_id = $patient_id;
        $this->visit_id = $visit_id;
        $this->type_visit = $type;
    }
    
    protected $listeners = ['setReservationData' => 'setReservation'];

    public function setReservation($reservationId, $patientId)
    {
        $this->reservation_id = $reservationId;
        $this->patient_id = $patientId;
    }

    public function updatedPaid()
    {
        $this->calculateTotals();
    }

    public function addService()
    {
        if (!$this->selected_service) {
            $this->addError('selected_service', 'اختر خدمة أولاً');
            return;
        }

        $service = service::find($this->selected_service);
        if (!$service) return;

        $total_price = $service->price * $this->quantity;
        $contract_part = $total_price * 0.4;
        $patient_part = $total_price - $contract_part;

        $this->services[] = [
            'service_id' => $service->id,
            'name' => $service->name,
            'total' => $total_price,
            'contracting_value' => $contract_part,
            'patient_value' => $patient_part,
            'discount' => $this->discount,
            'quantity' => $this->quantity,
        ];

        $this->reset(['selected_service', 'quantity', 'discount']);
        $this->calculateTotals();
    }

    public function removeService($index)
    {
        unset($this->services[$index]);
        $this->services = array_values($this->services);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->total = $this->contract_value = $this->patient_value = 0;

        foreach ($this->services as $service) {
            $this->total += $service['total'];
            $this->contract_value += $service['contracting_value'];
            $this->patient_value += $service['patient_value'];
        }

        $this->unpaid = $this->patient_value - $this->discount - $this->paid;
    }

    public function save()
    {
        $this->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'patient_id' => 'required|exists:patients,id',
        ]);

        $invoice = invoices::firstOrCreate(
            ['reservation_id' => $this->reservation_id],
            [
                'number' => 'INV-' . now()->format('YmdHis'),
                'patient_id' => $this->patient_id,
                'status' => 'unpaid',
                'currency' => 'EGP',
                'user_id' => Auth::id(),
            ]
        );

        $reservation = reservations::find($this->reservation_id);

        foreach ($this->services as $service) {
            finservicevisit::create([
                'id_invoices' => $invoice->id,
                'id_visit' => $reservation->visit_id,
                'id_services' => $service['service_id'],
                'total_pricing' => $service['total'],
                'contract_contribution' => $service['contracting_value'],
                'patient_contribution' => $service['patient_value'],
                'discount' => $service['discount'],
                'quantity' => $service['quantity'],
                'id_patient' => $this->patient_id,
                'currency' => 'EGP',
                'id_user' => Auth::id(),
            ]);
        }

        $invoice->update([
            'total' => $invoice->total + $this->total,
            'contract_contribution' => $invoice->contract_contribution + $this->contract_value,
            'patient_contribution' => $invoice->patient_contribution + $this->patient_value,
            'discount' => $invoice->discount + $this->discount,
            'paid' => $invoice->paid + $this->paid,
            'residual' => $invoice->residual + $this->unpaid,
            'status' => ($invoice->patient_contribution + $this->patient_value) == ($invoice->paid + $this->paid) ? 'paid' : 'unpaid'
        ]);

        session()->flash('success', 'تم حفظ البيانات بنجاح ✅');
        $this->reset(['services', 'total', 'contract_value', 'patient_value', 'paid', 'unpaid']);
    }

    public function render()
    {
        return view('livewire.medical.outpatient-service-form', [
            'available_services' => service::all()
        ]);
    }
}