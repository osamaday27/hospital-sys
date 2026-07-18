<?php

namespace App\Http\Livewire\Finance\Claims;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

use App\Models\Finance\Claim;
use App\Models\Finance\invoices;
use App\Models\Finance\contract_last_level;

class ClaimsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // فلاتر
    public $status = 'all';    // all, draft, submitted, approved, paid, rejected, cancelled
    public $from_date;
    public $to_date;
    public $contract_id;       // الشركة/التعاقد
    public $search = '';       // بحث عام (رقم مطالبة/اسم مريض/رقم فاتورة)
    public $perPage = 10;
    public $show_deleted = false;

    // تفاصيل
    public $showDetailsModal = false;
    public $selected_claim_id = null;
    public $selectedClaim = null;
    public $selectedInvoices = [];
    public $selectedLines = [];

    // ترتيب
    public $sort_field = 'id';
    public $sort_dir   = 'desc';

    public function updating($field)
    {
        if (in_array($field, ['status','from_date','to_date','contract_id','search','perPage','show_deleted'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if ($this->sort_field === $field) {
            $this->sort_dir = $this->sort_dir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_field = $field;
            $this->sort_dir = 'asc';
        }
    }

    public function render()
    {
        $claimsQuery = Claim::query()
            ->with(['patient:id,full_name,medical_number', 'contract:id,name', 'user:id,name'])
            ->when(!$this->show_deleted, fn($q) => $q->whereNull('deleted_at'))
            ->when($this->show_deleted, fn($q) => $q->withTrashed())
            ->when($this->status !== 'all', fn($q) => $q->where('status', $this->status))
            ->when($this->from_date, fn($q) => $q->whereDate('created_at', '>=', $this->from_date))
            ->when($this->to_date, fn($q) => $q->whereDate('created_at', '<=', $this->to_date))
            ->when($this->contract_id, fn($q) => $q->where('contract_id', $this->contract_id))
            ->when(trim($this->search) !== '', function($q) {
                $s = '%'.trim($this->search).'%';
                $q->where(function($qq) use ($s) {
                    $qq->where('id', 'like', $s)
                       ->orWhere('notes', 'like', $s)
                       ->orWhereHas('patient', fn($qp) => $qp->where('full_name','like',$s)->orWhere('full_name','like',$s))
                       ->orWhereHas('invoice', fn($qi) => $qi->where('number','like',$s)->orWhere('number','like',$s));
                });
            });

        // إجمالي عدد الفواتير لكل مطالبة (باستعمال علاقة invoices لو أضفتها)
        $claimsQuery->withCount('invoice');

        $claims = $claimsQuery
            ->orderBy($this->sort_field, $this->sort_dir)
            ->paginate($this->perPage);

        $contracts = contract_last_level::select('id','name')->orderBy('name')->get();

        return view('livewire.finance.claims.claims-manager', [
            'claims'    => $claims,
            'contracts' => $contracts,
        ]);
    }

    /* ===================== تفاصيل المطالبة ===================== */

    public function openDetails($id)
    {
        $this->selected_claim_id = $id;

        $this->selectedClaim = Claim::with([
            'patient:id,full_name,medical_number',
            'contract:id,name',
            'user:id,name',
            'lines.service:id,name,number',
        ])->findOrFail($id);

        $this->selectedLines = $this->selectedClaim->lines?->map(function($l){
            return [
                'id'             => $l->id,
                'service'        => $l->service->name ?? ($l->service->number ?? '-'),
                'quantity'       => (float)$l->quantity,
                'unit_price'     => (float)$l->unit_price,
                'gross'          => (float)$l->gross,
                'discount'       => (float)$l->discount,
                'patient_share'  => (float)$l->patient_share,
                'contract_share' => (float)$l->contract_share,
                'net_payable'    => (float)$l->net_payable,
                'notes'          => $l->notes,
            ];
        })->toArray() ?? [];

        // الفواتير المرتبطة (invoices.claim_id = claim.id)
        $this->selectedInvoices = invoices::with([
            'patient:id,full_name,medical_number',
            'reservations.clinic:id,name',
        ])
        ->where('claim_id', $id)
        ->orderByDesc('id')
        ->get([
            'id','number','number','total','discount','patient_contribution','contract_contribution','patient_id'
        ])->toArray();

        $this->showDetailsModal = true;
    }

    public function closeDetails()
    {
        $this->showDetailsModal = false;
        $this->selected_claim_id = null;
        $this->selectedClaim = null;
        $this->selectedInvoices = [];
        $this->selectedLines = [];
    }

    /* ===================== تغيير الحالة ===================== */

    public function submit($id)
    {
        $c = Claim::findOrFail($id);
        if ($c->status !== 'draft') {
            session()->flash('error', __('claims_trans.only_draft_can_submit'));
            return;
        }
        $c->update([
            'status' => 'submitted',
            'submission_date' => now(),
        ]);
        session()->flash('success', __('claims_trans.status_changed'));
    }

    public function approve($id)
    {
        $c = Claim::findOrFail($id);
        if ($c->status !== 'submitted') {
            session()->flash('error', __('claims_trans.only_submitted_can_approve'));
            return;
        }
        $c->update([
            'status' => 'approved',
            'approval_date' => now(),
        ]);
        session()->flash('success', __('claims_trans.status_changed'));
    }

    public function markPaid($id)
    {
        $c = Claim::findOrFail($id);
        if ($c->status !== 'approved') {
            session()->flash('error', __('claims_trans.only_approved_can_paid'));
            return;
        }
        $c->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);
        session()->flash('success', __('claims_trans.status_changed'));
    }

    public function reject($id)
    {
        $c = Claim::findOrFail($id);
        if (!in_array($c->status, ['submitted','approved'])) {
            session()->flash('error', __('claims_trans.only_submitted_or_approved_can_reject'));
            return;
        }
        $c->update(['status' => 'rejected']);
        session()->flash('success', __('claims_trans.status_changed'));
    }

    public function cancel($id)
    {
        $c = Claim::findOrFail($id);
        // يسمح بالإلغاء في أي حالة قبل paid
        if (in_array($c->status, ['paid'])) {
            session()->flash('error', __('claims_trans.cannot_cancel_paid'));
            return;
        }
        $c->update(['status' => 'cancelled']);
        session()->flash('success', __('claims_trans.status_changed'));
    }

    /* ===================== حذف/استرجاع ===================== */

    public function deleteClaim($id)
    {
        $c = Claim::findOrFail($id);
        $c->delete();
        session()->flash('success', __('claims_trans.deleted_successfully'));
    }

    public function restoreClaim($id)
    {
        $c = Claim::withTrashed()->findOrFail($id);
        $c->restore();
        session()->flash('success', __('claims_trans.restored_successfully'));
    }
}