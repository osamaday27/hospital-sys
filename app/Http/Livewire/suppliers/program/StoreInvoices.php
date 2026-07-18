<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\suppliersdata as Supplier;
use App\Models\general\branch;


class StoreInvoices extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $branch_id = null;
    public $filter_status = null;
    public $filter_payment_status = null; // حالة الدفع
    public $filter_payment_method = null; // طريقة الدفع
    public $filter_date_from;
    public $filter_date_to;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirmed' => 'delete'];

    public $deleteId;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function updatingBranchId()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterPaymentStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterPaymentMethod()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatchBrowserEvent('show-delete-confirmation');
    }

    public function delete()
    {
        $invoice = SupplierInvoice::findOrFail($this->deleteId);
        $invoice->delete();
        $this->dispatchBrowserEvent('deleted');
    }

    public function approve($id)
    {
        $invoice = SupplierInvoice::findOrFail($id);
        $invoice->status = 'approved';
        $invoice->approved_by = auth()->id();
        $invoice->save();

        $this->dispatchBrowserEvent('approved');
    }

    public function render()
    {
        $invoices = SupplierInvoice::with(['supplier', 'store'])
        ->where('invoice_type', 'products')
            ->when($this->search, function ($q) {
                $q->where('invoice_no', 'like', '%' . $this->search . '%')
                  ->orWhereHas('supplier', fn($qq) => $qq->where('name_ar', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('store', fn($qq) => $qq->where('name_ar', 'like', '%' . $this->search . '%'));
            })
            ->when($this->branch_id, fn($q) => $q->where('store_id', $this->branch_id))
            ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
            ->when($this->filter_payment_method, fn($q) => $q->where('payment_method', $this->filter_payment_method))
            ->when($this->filter_date_from, fn($q) => $q->whereDate('invoice_date', '>=', $this->filter_date_from))
            ->when($this->filter_date_to, fn($q) => $q->whereDate('invoice_date', '<=', $this->filter_date_to))
            ->when($this->filter_payment_status, function ($q) {
                if ($this->filter_payment_status === 'paid') {
                    $q->where('remaining_amount', 0);
                } elseif ($this->filter_payment_status === 'partial') {
                    $q->where('paid_amount', '>', 0)->where('remaining_amount', '>', 0);
                } elseif ($this->filter_payment_status === 'unpaid') {
                    $q->where('paid_amount', 0)->where('remaining_amount', '>', 0);
                }
            })
            ->latest()
            ->paginate($this->perPage);

        $suppliers = Supplier::all();
        $branches = Branch::all();

        return view('livewire.suppliers.program.store-invoices', compact('invoices', 'suppliers', 'branches'));
    }
}