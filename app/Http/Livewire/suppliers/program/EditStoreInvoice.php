<?php

namespace App\Http\Livewire\Suppliers\Program;


use Livewire\Component;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierInvoiceItem;
use App\Models\Suppliers\suppliersdata as Supplier;
use App\Models\general\branch;
use App\Models\supply_chain\unit;
use App\Models\supply_chain\productdata as Product;

class EditStoreInvoice extends Component
{
    public $invoice_id;
    public $supplier_id, $branch_id, $store_id, $invoice_no, $invoice_date, $due_date;
    public $subtotal = 0, $discount = 0, $tax = 0, $total_amount = 0, $paid_amount = 0, $remaining_amount = 0;
    public $status, $payment_method, $reference_no, $notes;
    public $bank_name, $bank_details;

    public $items = [];

    protected $rules = [
        'supplier_id'  => 'required|integer|exists:suppliers_data,id',
        'invoice_no'   => 'required|string',
        'invoice_date' => 'required|date',
        'items.*.product_id' => 'required|integer|exists:products_data,id',
        'items.*.unit_id'    => 'required|integer|exists:units,id',
        'items.*.qty'        => 'required|numeric|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.discount'   => 'nullable|numeric|min:0',
        'items.*.tax'        => 'nullable|numeric|min:0',
    ];

    public function mount($id)
    {
        $invoice = SupplierInvoice::with('items')->findOrFail($id);

        $this->invoice_id      = $invoice->id;
        $this->supplier_id     = $invoice->supplier_id;
        $this->branch_id       = $invoice->branch_id;
        $this->store_id        = $invoice->store_id;
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
                'product_id'  => $item->product_id,
                'unit_id'     => $item->product_unit_id,
                'description' => $item->description,
                'qty'         => $item->qty,
                'unit_price'  => $item->unit_price,
                'discount'    => $item->discount,
                'tax'         => $item->tax,
                'total'       => $item->total,
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
            $total = $base + $tax;

            $this->items[$key]['total'] = $total;
            $this->subtotal += $qty * $price;
            $this->discount += $discount;
            $this->tax      += $tax;
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
            'store_id'         => $this->store_id,
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

        foreach ($this->items as $item) {
            SupplierInvoiceItem::updateOrCreate(
                ['id' => $item['id'] ?? null],
                [
                    'invoice_id'  => $invoice->id,
                    'product_id'  => $item['product_id'],
                    'product_unit_id'     => $item['unit_id'],
                    'description' => $item['description'],
                    'qty'         => $item['qty'],
                    'unit_price'  => $item['unit_price'],
                    'discount'    => $item['discount'],
                    'tax'         => $item['tax'],
                    'total'       => $item['total'],
                ]
            );
        }

        session()->flash('success', 'تم تعديل فاتورة المخزن بنجاح');
        return redirect()->route('warehouse_invoices.index');
    }

    public function render()
    {
        $suppliers = Supplier::all();
        $products  = Product::all();
        $units     = Unit::all();
        $branches  = Branch::all();

        return view('livewire.Suppliers.program.edit-store-invoice', compact('suppliers','products','units','branches'));
    }
}