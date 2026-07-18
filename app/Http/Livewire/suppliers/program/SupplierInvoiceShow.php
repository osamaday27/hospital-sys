<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierPaymentInstallment;

class SupplierInvoiceShow extends Component
{
    public $invoice_id;
    public $invoice;
    public $payments = [];

    public function mount($invoice_id)
    {
        $this->invoice = SupplierInvoice::with('supplier')->findOrFail($invoice_id);

        // 👇 نجيب الدفعات مباشرة من جدول supplier_payment_installments
        $this->payments = SupplierPaymentInstallment::where('supplier_invoice_id', $invoice_id)
            ->orderBy('created_at','asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.suppliers.program.supplier-invoice-show', [
            'invoice'  => $this->invoice,
            'payments' => $this->payments,
        ]);
    }
}