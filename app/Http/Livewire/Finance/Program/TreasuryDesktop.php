<?php
namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\Invoices;
use App\Models\Finance\Receipts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\Web\get_contract;
use App\Models\Finance\AdvanceReceipt;

class TreasuryDesktop extends Component
{
use WithPagination, get_contract;

public $selected_date;
public $search = '';
public $statusFilter = '';
public $perPage = 10;
public $cancellation_reason = '';
public $cancellation_notes = '';
public $selected_invoice_id;
public $advance_amount = 0;
public $advance_patient_id = null;

protected $queryString = ['selected_date', 'search', 'statusFilter'];

public function mount()
{
$this->selected_date = Carbon::now()->format('Y-m-d');
}

public function updatingSelectedDate()
{
$this->resetPage();
}

public function updatingSearch()
{
$this->resetPage();
}

public function updatingStatusFilter()
{
$this->resetPage();
}

public function confirmCancellation($invoiceId)
{
$this->selected_invoice_id = $invoiceId;
$this->dispatchBrowserEvent('show-cancellation-modal');
}

public function submitCancellation()
{
$invoice = Invoices::findOrFail($this->selected_invoice_id);
$invoice->status = 'pending_cancellation';
$invoice->cancellation_reason = $this->cancellation_reason;
$invoice->cancellation_notes = $this->cancellation_notes;
$invoice->save();

$this->reset(['cancellation_reason', 'cancellation_notes', 'selected_invoice_id']);
$this->dispatchBrowserEvent('hide-cancellation-modal');
session()->flash('success', __('lockers_trans.cancellation_request_sent'));
}

public function submitAdvanceReceipt()
{
$this->validate([
'advance_patient_id' => 'required|exists:patients,id',
'advance_amount' => 'required|numeric|min:1',
]);

AdvanceReceipt::create([
'patient_id' => $this->advance_patient_id,
'amount' => $this->advance_amount,
'date' => now(),
'created_by' => Auth::id(),
]);

$this->reset(['advance_amount', 'advance_patient_id']);
session()->flash('success', __('lockers_trans.advance_payment_saved'));
}

public function refreshData()
{
$this->resetPage();
}
public function render()
{
    $treasuries = Auth::user()->treasuries ?? collect();
    $treasury   = $treasuries->first()->name_ar ?? '-';

   $query = Invoices::with(['patient', 'reservations'])
    ->withCount('receipts') // يضيف receipts_count تلقائيًا
    ->whereDate('created_at', $this->selected_date)
    ->when($this->search, fn ($q) =>
        $q->where(function ($sub) {
            $sub->whereHas('patient', fn ($q2) =>
                $q2->where('full_name', 'like', "%{$this->search}%")
            )->orWhere('number', 'like', "%{$this->search}%");
        })
    )
    ->when($this->statusFilter, fn ($q) =>
        $q->where('status', $this->statusFilter)
    );


    $dailyInvoices            = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    $total_paid               = $query->sum('paid');
    $total_residual           = $query->sum('residual');
    $total_discount           = $query->sum('discount');
    $total_invoices           = $query->count();
    $currency                 = optional($dailyInvoices->first())->currency ?? '--';

    $cancelled_receipts_count = Receipts::whereDate('created_at', $this->selected_date)->onlyTrashed()->count();
    $pending_discount_requests = Invoices::whereDate('created_at', $this->selected_date)->where('status', 'pending_discount')->count();
    $pending_cancellation_requests = Invoices::whereDate('created_at', $this->selected_date)->where('status', 'pending_cancellation')->count();

    return view('livewire.finance.program.treasury-desktop', [
        'dailyInvoices' => $dailyInvoices,
        'treasury' => $treasury,
        'total_paid' => $total_paid,
        'total_residual' => $total_residual,
        'total_discount' => $total_discount,
        'total_invoices' => $total_invoices,
        'currency' => $currency,
        'cancelled_receipts_count' => $cancelled_receipts_count,
        'pending_discount_requests' => $pending_discount_requests,
        'pending_cancellation_requests' => $pending_cancellation_requests,
    ]);
}

}