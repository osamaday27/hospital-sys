<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierInvoiceItem;
use App\Models\Suppliers\SupplierPaymentInstallment; // ✅ ده الجدول اللي انت معرفه

class ViewServiceInvoice extends Component
{
    public $invoice_id;
    public $invoice;
    public $items = [];
    public $payments = [];

    public function mount($invoice_id)
    {
        $this->invoice = SupplierInvoice::with(['supplier', 'items.service', 'items'])
            ->findOrFail($invoice_id);

        $this->items = $this->invoice->items;

        // ✅ نجيب الدفعات من جدول SupplierPaymentInstallment
        $this->payments = SupplierPaymentInstallment::where('supplier_invoice_id', $invoice_id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.suppliers.program.view-service-invoice', [
            'invoice'  => $this->invoice,
            'items'    => $this->items,
            'payments' => $this->payments,
        ]);
    }
}