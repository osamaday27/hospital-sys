<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use App\Models\finance\{
    invoices,
    receipts,
    finservicevisit,
    discountrequest,
    invoicecancellationrequest
};
use App\Models\supply_chain\AdmBVisitSupply;

class InvoiceDetails extends Component
{
    public $invoice_id;
    public $invoice;
    public $services = [];
    public $discount_requests = [];
    public $cancellation_request;
    public $receipts = [];
    public $supplies = [];

    public function mount($invoice_id)
    {
        $this->invoice_id = $invoice_id;

        $this->invoice = invoices::with([
            'patient',
            'reservations.doctor',
            'reservations.clinic',
            'reservations.service'
        ])->findOrFail($invoice_id);

        $this->services = finservicevisit::with('service')
            ->where('id_invoices', $invoice_id)
            ->get();

        $this->discount_requests = discountrequest::where('invoice_id', $invoice_id)->get();

        $this->cancellation_request = invoicecancellationrequest::with('reason')
            ->where('invoice_id', $invoice_id)
            ->where('status', 'pending')
            ->first();

        $this->receipts = receipts::where('invoices_id', $invoice_id)
            ->orderBy('created_at')
            ->get();

        // جلب مستلزمات الزيارة المرتبطة بالفاتورة (عن طريق reservation_id)
        $this->supplies = AdmBVisitSupply::with(['product', 'unit', 'store'])
            ->where('reservation_id', $this->invoice->reservation_id)
            ->get();
    }

    public function render()
    {
        return view('livewire.finance.program.invoice-details', [
            'supplies' => $this->supplies,
        ]);
    }
}