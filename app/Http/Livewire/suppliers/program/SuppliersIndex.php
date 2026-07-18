<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Suppliers\suppliersdata;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierPayment;
use App\Models\Suppliers\SupplierOpeningBalance;
use App\Models\Suppliers\supplieralert;
use App\Models\Suppliers\suppliersgroup;
use Carbon\Carbon;

class SuppliersIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $groupFilter = '';
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subYear()->toDateString();
        $this->dateTo = Carbon::now()->toDateString();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getSuppliersProperty()
    {
        return suppliersdata::with(['group'])
            ->when($this->search, function ($q) {
                $q->where('name_ar', 'like', '%' . $this->search . '%')
                    ->orWhere('name_en', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->groupFilter, function ($q) {
                $q->where('suppliersgroup_id', $this->groupFilter);
            })
            ->paginate(15);
    }

    private function getInvoices($supplierId)
    {
        return SupplierInvoice::where('supplier_id', $supplierId)
            ->whereBetween('invoice_date', [Carbon::parse($this->dateFrom)->startOfDay(), Carbon::parse($this->dateTo)->endOfDay()])
            ->get();
    }

  private function getPayments($supplierId)
{
    return SupplierPayment::whereHas('invoice', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })
        ->whereBetween('created_at', [
            Carbon::parse($this->dateFrom)->startOfDay(),
            Carbon::parse($this->dateTo)->endOfDay()
        ])
        ->get();
}

private function getAlerts($supplierId)
{
    return supplieralert::where('supplier_id', $supplierId)
        ->whereBetween('notify_date', [
            Carbon::parse($this->dateFrom)->startOfDay(),
            Carbon::parse($this->dateTo)->endOfDay()
        ])
        ->get();
}

    private function getOpening($supplierId)
    {
        return SupplierOpeningBalance::where('supplier_id', $supplierId)->get();
    }

    public function calculateSummary($supplierId)
    {
        $invoices = $this->getInvoices($supplierId);
        $payments = $this->getPayments($supplierId);
        $alerts = $this->getAlerts($supplierId);
        $opening = $this->getOpening($supplierId);

        $totalInvoices = $invoices->sum('total_amount');
        $totalPayments = $payments->sum('amount');
        $openingCredit = $opening->sum('credit_balance');
        $openingDebit = $opening->sum('debit_balance');

        // إشعارات الخصم والإضافة
        $totalAdditions = $alerts->where('type', 'addition')->sum('value');
        $totalDiscounts = $alerts->where('type', 'discount')->sum('value');

        $balance = $totalInvoices + $openingDebit + $totalAdditions - ($totalPayments + $openingCredit + $totalDiscounts);

        return [
            'invoices' => $totalInvoices,
            'payments' => $totalPayments,
            'opening' => $openingDebit - $openingCredit,
            'additions' => $totalAdditions,
            'discounts' => $totalDiscounts,
            'balance' => $balance,
        ];
    }

    public function render()
    {
        $groups = suppliersgroup::all();

        return view('livewire.suppliers.program.suppliers-index', [
            'suppliers' => $this->suppliers,
            'groups' => $groups,
        ]);
    }
}