<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierService;
use App\Models\Suppliers\SupplierInvoiceItem;
use App\Models\Suppliers\suppliersdata as Supplier;
use App\Models\General\Branch;

class EditServiceInvoice extends Component
{
    public $invoice_id;
    public $supplier_id, $branch_id, $invoice_no, $invoice_date, $due_date;
    public $subtotal = 0, $discount = 0, $tax = 0, $total_amount = 0, $paid_amount = 0, $remaining_amount = 0;
    public $status, $payment_method, $reference_no, $notes;
    public $bank_name, $bank_details;

    public $items = [];

    protected $rules = [
        'supplier_id'  => 'required|integer|exists:suppliers_data,id',
        'invoice_no'   => 'required|string',
        'invoice_date' => 'required|date',
        'items.*.service_id' => 'required|integer|exists:suppliers_services,id',
        'items.*.qty'        => 'required|numeric|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.discount'   => 'nullable|numeric|min:0',
        'items.*.tax_type'   => 'required|in:percent,value',
        'items.*.tax'        => 'nullable|numeric|min:0',
    ];

    public function mount($id)
    {
        $invoice = SupplierInvoice::with('items')->findOrFail($id);

        $this->invoice_id      = $invoice->id;
        $this->supplier_id     = $invoice->supplier_id;
        $this->branch_id       = $invoice->branch_id;
        $this->invoice_no      = $invoice->invoice_no;
        $this->invoice_date    = $invoice->invoice_date;
        $this->due_date        = $invoice->due_date;
        $this->subtotal        = $invoice->subtotal;
        $this->discount        = $invoice->discount;
        $this->tax             = $invoice->tax;
        $this->total_amount    = $invoice->total_amount;
        $this->paid_amount     = $invoice->paid_amount;
        $this->remaining_amount= $invoice->remaining_amount;
        $this->status          = $invoice->status;
        $this->payment_method  = $invoice->payment_method;
        $this->reference_no    = $invoice->reference_no;
        $this->notes           = $invoice->notes;
        $this->bank_name       = $invoice->bank_name;
        $this->bank_details    = $invoice->bank_details;

        foreach ($invoice->items as $item) {
            $this->items[] = [
                'id'          => $item->id,
                'service_id'  => $item->service_id,
                'description' => $item->description,
                'qty'         => $item->qty,
                'unit_price'  => $item->unit_price,
                'discount'    => $item->discount,
                'tax_type'    => $item->tax_type,
                'tax'         => $item->tax,
                'total'       => $item->total,
                'unit_name'   => $item->unit_name,
            ];
        }
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        $this->discount = 0;
        $this->tax      = 0;
        $this->total_amount = 0;

        foreach ($this->items as $key => $item) {
            $qty      = floatval($item['qty'] ?? 0);
            $price    = floatval($item['unit_price'] ?? 0);
            $discount = floatval($item['discount'] ?? 0);
            $tax      = floatval($item['tax'] ?? 0);

            $base = ($qty * $price) - $discount;

            if (($item['tax_type'] ?? 'percent') === 'percent') {
                $tax_value = ($base * $tax) / 100;
            } else {
                $tax_value = $tax;
            }

            $total = $base + $tax_value;

            $this->items[$key]['total'] = $total;
            $this->subtotal += $qty * $price;
            $this->discount += $discount;
            $this->tax      += $tax_value;
        }

        $this->total_amount     = ($this->subtotal - $this->discount) + $this->tax;
        $this->remaining_amount = ($this->total_amount - ($this->paid_amount ?? 0));
    }

    public function save()
    {
        $this->validate();
        $this->calculateTotals();

        $invoice = SupplierInvoice::findOrFail($this->invoice_id);
        $invoice->update([
            'supplier_id'      => $this->supplier_id,
            'branch_id'        => $this->branch_id,
            'invoice_no'       => $this->invoice_no,
            'invoice_date'     => $this->invoice_date,
            'due_date'         => $this->due_date,
            'subtotal'         => $this->subtotal,
            'discount'         => $this->discount,
            'tax'              => $this->tax,
            'total_amount'     => $this->total_amount,
            'paid_amount'      => $this->paid_amount ?? 0,
            'remaining_amount' => $this->remaining_amount,
            'status'           => $this->status,
            'payment_method'   => $this->payment_method,
            'reference_no'     => $this->reference_no,
            'notes'            => $this->notes,
            'bank_name'        => $this->payment_method === 'bank' ? $this->bank_name : null,
            'bank_details'     => $this->payment_method === 'bank' ? $this->bank_details : null,
        ]);

        // تحديث البنود
        foreach ($this->items as $item) {
            SupplierInvoiceItem::updateOrCreate(
                ['id' => $item['id'] ?? null],
                [
                    'invoice_id'  => $invoice->id,
                    'service_id'  => $item['service_id'],
                    'description' => $item['description'],
                    'qty'         => $item['qty'],
                    'unit_price'  => $item['unit_price'],
                    'discount'    => $item['discount'],
                    'tax_type'    => $item['tax_type'],
                    'tax'         => $item['tax'],
                    'total'       => $item['total'],
                    'unit_name'   => $item['unit_name'],
                ]
            );
        }

        session()->flash('success', 'تم تعديل الفاتورة بنجاح');
        return redirect()->route('serviceinvoicesindex');
    }

    public function render()
    {
        $suppliers = Supplier::all();
        $services  = SupplierService::get();
        $branches  = Branch::all();

        return view('livewire.suppliers.program.edit-service-invoice', compact('suppliers','services','branches'));
    }
}