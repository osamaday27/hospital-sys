<?php
// app/Http/Livewire/Finance/Program/SubToMainLockerTransfer.php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\finance\Slocker;
use App\Models\finance\Lockertransaction;
use App\Models\finance\receipts;
use Carbon\Carbon;

class SubToMainLockerTransfer extends Component
{
    public $lockers;
    public $selectedLocker;
    public $employees = [];
    public $fromDate;
    public $toDate;
    public $selectedEmployee;
    public $deliveredAmount;
    public $shortageReason;
    public $searchPaid = '';
    public $searchDue = '';
    public $searchCanceled = '';
    public $showInvoiceId;
    public $selectedReceipts = [];

    protected $rules = [
        'selectedLocker'   => 'required|exists:fin_s_locker,id',
        'selectedEmployee' => 'required|exists:users,id',
        'deliveredAmount'  => 'required|numeric|min:0',
        'shortageReason'   => 'nullable|string',
    ];

    public function mount()
    {
        $this->lockers  = Slocker::where('type', 'sub')->get();
        $today          = Carbon::today()->format('Y-m-d');
        $this->fromDate = $today;
        $this->toDate   = $today;
    }

    public function updatedSelectedLocker($id)
    {
        $this->employees = $id
            ? Slocker::with('users')->find($id)->users
            : [];
        $this->selectedEmployee = null;
        $this->selectedReceipts = [];
    }

    public function getReceiptsProperty()
    {
        return receipts::withTrashed()
            ->with('invoice')
            ->where('treasury', $this->selectedLocker)
            ->where('reviewed', false) // استبعاد الإيصالات التي تم استلامها
            ->when($this->fromDate, fn($q) => $q->whereDate('created_at', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('created_at', '<=', $this->toDate))
            ->get();
    }

    public function getPaidReceiptsProperty()
    {
        return $this->receipts
            ->whereNull('deleted_at')
            ->where('total_remainder', 0)
            ->when($this->searchPaid, fn($col) => $col->filter(fn($r) => stripos($r->invoices_number . $r->id, $this->searchPaid) !== false));
    }

    public function getDueReceiptsProperty()
    {
        return $this->receipts
            ->whereNull('deleted_at')
            ->where('total_remainder', '>', 0)
            ->when($this->searchDue, fn($col) => $col->filter(fn($r) => stripos($r->invoices_number . $r->id, $this->searchDue) !== false));
    }

    public function getCanceledReceiptsProperty()
    {
        return $this->receipts
            ->whereNotNull('deleted_at')
            ->when($this->searchCanceled, fn($col) => $col->filter(fn($r) => stripos($r->invoices_number . $r->id, $this->searchCanceled) !== false));
    }

    public function reviewInvoice($id)
    {
        $this->showInvoiceId = $id;
    }

    public function selectAll()
    {
        $this->selectedReceipts = $this->receipts->pluck('id')->toArray();
    }

    public function confirmReview()
    {
        receipts::whereIn('id', $this->selectedReceipts)->update(['reviewed' => true]);
        session()->flash('message', __('locker_trans.review_confirmed'));
        $this->selectedReceipts = [];
    }

    // اجمالي مبالغ الايصالات المحددة
    public function getSelectedReceiptsAmountProperty()
    {
        return receipts::whereIn('id', $this->selectedReceipts)->sum('total_paid');
    }

    public function receive()
    {
        $sumPaid = $this->paidReceipts->sum('total_paid');
        $this->rules['shortageReason'] =$this->deliveredAmount <$sumPaid ? 'required|string' : 'nullable';
       $this->validate();

       $sub = Slocker::findOrFail($this->selectedLocker);
       $main = Slocker::where('type', 'main')->firstOrFail();
       $diff = round($sumPaid -$this->deliveredAmount, 2);

        DB::transaction(function () use ($sub,$main,$sumPaid,$diff) {
            Lockertransaction::create([
                'locker_id'        =>$sub->id,
                'transaction_type' => 'transfer_out',
                'direction'        => 'out',
                'amount'           =>$sumPaid,
                'reference_type'   =>App\Models\User::class,
                'reference_id'     => Auth::id(),
                'gl_account_id'    =>$sub->gl_account_id,
                'notes'            => __('locker_trans.receipts_handover'),
                'created_by'       => Auth::id(),
            ]);

            Lockertransaction::create([
                'locker_id'        =>$main->id,
                'transaction_type' => 'transfer_in',
                'direction'        => 'in',
                'amount'           =>$this->deliveredAmount,
                'reference_type'   =>App\Models\User::class,
                'reference_id'     => Auth::id(),
                'gl_account_id'    =>$main->gl_account_id,
                'notes'            => __('locker_trans.receipts_received'),
                'created_by'       => Auth::id(),
            ]);

            if ($diff > 0) {
                Lockertransaction::create([
                    'locker_id'        =>$sub->id,
                    'transaction_type' => 'transfer_out',
                    'direction'        => 'out',
                    'amount'           =>$diff,
                    'reference_type'   =>App\Models\User::class,
                    'reference_id'     => Auth::id(),
                    'gl_account_id'    =>$sub->gl_account_id,
                    'notes'            =>$this->shortageReason,
                    'created_by'       => Auth::id(),
                ]);
            }
        });

        session()->flash('message', __('locker_trans.receive_success'));
       $this->reset(['selectedLocker','selectedEmployee','employees','deliveredAmount','shortageReason','selectedReceipts']);
    }

    public function render()
    {
        return view('livewire.finance.program.sub-to-main-locker-transfer', [
            'lockers'               =>$this->lockers,
            'employees'             =>$this->employees,
            'paidReceipts'          =>$this->paidReceipts,
            'dueReceipts'           =>$this->dueReceipts,
            'canceledReceipts'      =>$this->canceledReceipts,
            'showInvoiceId'         =>$this->showInvoiceId,
            'selectedReceipts'      =>$this->selectedReceipts,
            'selectedReceiptsAmount'=>$this->selectedReceiptsAmount,
        ]);
    }
}