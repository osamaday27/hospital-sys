<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierService;
use App\Models\Suppliers\SupplierInvoiceItem;
use App\Models\Suppliers\suppliersdata as Supplier;
use App\Models\General\unit;
use App\Models\General\branch;

class ServiceInvoices extends Component
{
    use WithPagination;

    public $supplier_id, $branch_id, $invoice_no, $invoice_date, $due_date;
    public $subtotal = 0, $discount = 0, $tax = 0, $total_amount = 0, $paid_amount = 0, $remaining_amount = 0;
    public $status = 'new', $payment_method = 'cash', $reference_no, $notes;
    public $bank_name, $bank_details;

    public $items = [];

    protected $rules = [
        'supplier_id'          => 'required|integer|exists:suppliers_data,id',
        'branch_id'            => 'nullable|integer|exists:branches,id',
        'invoice_no'           => 'required|string|unique:supplier_invoices,invoice_no',
        'invoice_date'         => 'required|date',
        'items.*.service_id'   => 'required|integer|exists:suppliers_services,id',
        'items.*.qty'          => 'required|numeric|min:1',
        'items.*.unit_price'   => 'required|numeric|min:0',
        'items.*.tax_type'     => 'required|in:percent,value',
        'items.*.tax'          => 'nullable|numeric|min:0',
        'items.*.discount'     => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->invoice_date = now()->toDateString();
        $this->items = [];
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'service_id'  => null,
            'description' => '',
            'qty'         => 1,
            'unit_price'  => 0,
            'discount'    => 0,
            'tax_type'    => 'percent',
            'tax'         => 0,
            'tax_value'   => 0,
            'total'       => 0,
            'unit_name'   => '',
        ];
    }

    public function updatedItems($value, $name)
    {
        if (str_contains($name, 'service_id')) {
            [$index, $_] = explode('.', $name);
            $service = SupplierService::find($value);
            if ($service) {
                $this->items[$index]['unit_price']  = $service->price ?? 0;
                $this->items[$index]['description'] = $service->name_ar ?? '';
                $this->items[$index]['unit_name']   = $service->unit->name_ar ?? '';
                $this->items[$index]['tax_type']    = 'percent'; // افتراضي
                $this->items[$index]['tax']         = 0;
                $this->items[$index]['tax_value']   = 0;
                $this->items[$index]['total']       = 0;
            }
        }
        $this->calculateTotals();
    }

    public function updatedPaidAmount()
    {
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        $this->discount = 0;
        $this->tax = 0;
        $this->total_amount = 0;

        foreach ($this->items as $key => $item) {
            $qty       = floatval($item['qty'] ?? 0);
            $price     = floatval($item['unit_price'] ?? 0);
            $discount  = floatval($item['discount'] ?? 0);
            $tax       = floatval($item['tax'] ?? 0);

            $base = ($qty * $price) - $discount;

            if (($item['tax_type'] ?? 'percent') === 'percent') {
                $tax_value = ($base * $tax) / 100;
            } else {
                $tax_value = $tax;
            }

            $total = $base + $tax_value;

            $this->items[$key]['tax_value'] = $tax_value;
            $this->items[$key]['total']     = $total;

            $this->subtotal += $qty * $price;
            $this->discount += $discount;
            $this->tax      += $tax_value;
        }

        $this->total_amount    = ($this->subtotal - $this->discount) + $this->tax;
        $this->remaining_amount = ($this->total_amount - ($this->paid_amount ?? 0));
    }

    public function saveInvoice()
    {
        $this->validate();
        $this->calculateTotals();

        $invoice = SupplierInvoice::create([
            'supplier_id'      => $this->supplier_id,
            'invoice_type'     => 'services',
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
            'reference_no'     => $this->payment_method !== 'cash' ? $this->reference_no : null,
            'bank_name'        => $this->payment_method === 'bank' ? $this->bank_name : null,
            'bank_details'     => $this->payment_method === 'bank' ? $this->bank_details : null,
            'user_id'          => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            SupplierInvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'item_type'   => 'service',
                'service_id'  => $item['service_id'],
                'description' => $item['description'],
                'qty'         => $item['qty'],
                'unit_price'  => $item['unit_price'],
                'discount'    => $item['discount'],
                'tax_type'    => $item['tax_type'] ?? 'percent',
                'tax'         => $item['tax'],
                'total'       => $item['total'],
                'unit_name'   => $item['unit_name'] ?? null,
            ]);
        }

        session()->flash('success', 'تم حفظ فاتورة الخدمة بنجاح');

        $this->reset();
        $this->items = [];
        $this->addItem();

        // إعادة تهيئة الـ select2 بعد الحفظ
        $this->emit('reinitSelect2');
    }

    public function render()
    {
        $invoices  = SupplierInvoice::with(['supplier','items.service'])
                    ->where('invoice_type','service')
                    ->latest()
                    ->paginate(10);

        $suppliers = Supplier::all();
        $services  = SupplierService::get();
        $branches  = Branch::all();

        return view('livewire.suppliers.program.service-invoices', [
            'invoices'  => $invoices,
            'suppliers' => $suppliers,
            'services'  => $services,
            'branches'  => $branches,
        ]);
    }
}