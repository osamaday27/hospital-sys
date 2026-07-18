<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierInvoiceItem;
use App\Models\Suppliers\SupplierPayment;

class ViewStoreInvoice extends Component
{
    public $invoice_id;
    public $invoice;
    public $items = [];
    public $payments = [];

    public function mount($invoice_id)
    {
        $this->invoice = SupplierInvoice::with(['supplier', 'store', 'items.product', 'items.productUnit'])
            ->findOrFail($invoice_id);

        $this->items = $this->invoice->items;
        $this->payments = SupplierPayment::with('locker')
            ->where('supplier_invoice_id', $this->invoice_id)
            ->get();
    }

    public function render()
    {
        return view('livewire.suppliers.program.view-store-invoice', [
            'invoice'  => $this->invoice,
            'items'    => $this->items,
            'payments' => $this->payments,
        ]);
    }
}