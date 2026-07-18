<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use App\Models\Suppliers\suppliersdata;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierPayment;
use App\Models\Suppliers\SupplierOpeningBalance;
use App\Models\Suppliers\supplieralert;
use Carbon\Carbon;

class SupplierDetails extends Component
{
    public $supplier_id;
    public $supplier;
    public $dateFrom;
    public $dateTo;

    public $invoices = [];
    public $payments = [];
    public $openingBalances = [];
    public $alerts = [];

    public $totalInvoices = 0;
    public $totalPayments = 0;
    public $totalOpening = 0;
    public $totalAdditions = 0;
    public $totalDiscounts = 0;
    public $finalBalance = 0;

    public function mount($supplier_id)
    {
        $this->supplier_id = $supplier_id;
        $this->dateFrom = Carbon::now()->subYear()->toDateString();
        $this->dateTo   = Carbon::now()->toDateString();

        $this->loadData();
    }

    public function updatedDateFrom()
    {
        $this->loadData();
    }

    public function updatedDateTo()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->supplier = suppliersdata::with(['group', 'branch'])->find($this->supplier_id);

        if (!$this->supplier) {
            return;
        }

        $this->invoices = SupplierInvoice::where('supplier_id', $this->supplier_id)
            ->whereBetween('invoice_date', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ])
            ->get();

        $this->payments = SupplierPayment::whereHas('invoice', function ($q) {
                $q->where('supplier_id', $this->supplier_id);
            })
            ->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ])
            ->get();

        $this->alerts = supplieralert::where('supplier_id', $this->supplier_id)
            ->whereBetween('notify_date', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ])
            ->get();

        $this->openingBalances = SupplierOpeningBalance::where('supplier_id', $this->supplier_id)->get();

        // حساب الإجماليات
        $this->totalInvoices  = $this->invoices->sum('total_amount');
        $this->totalPayments  = $this->payments->sum('amount');
        $this->totalOpening   = $this->openingBalances->sum('debit_balance') - $this->openingBalances->sum('credit_balance');
        $this->totalAdditions = $this->alerts->where('type', 'addition')->sum('value');
        $this->totalDiscounts = $this->alerts->where('type', 'discount')->sum('value');

        $this->finalBalance = ($this->totalInvoices + $this->totalOpening + $this->totalAdditions)
                            - ($this->totalPayments + $this->totalDiscounts);
    }

    public function render()
    {
        return view('livewire.suppliers.program.supplier-details');
    }
}