<?php

namespace App\Http\Livewire\Finance\Claims;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\Claim;
use App\Models\Finance\invoices;
use App\Models\Finance\contract_last_level;
use Illuminate\Support\Facades\Auth;
use App\Traits\Web\get_contract;

class ClaimCreate extends Component
{
    use WithPagination, get_contract;

    public $from_date;
    public $to_date;
    public $contract_id;
    public $search = '';
    public $perPage = 10;

    public $totals = [];
    public $allTotals = []; // مجموع كل الفواتير في الفترة (بدون باجينشن)
    public $doSearch = false; // البحث بالزر فقط

    protected $paginationTheme = 'bootstrap';

    /* ======================= أكشن البحث ======================= */
    public function searchInvoices()
    {
        $this->doSearch = true;
        $this->resetPage();
    }

    /* ======================= Query Builder مشترك ======================= */
    protected function buildInvoiceQuery()
    {
        $from = $this->from_date ?: now()->startOfMonth()->toDateString();
        $to   = $this->to_date   ?: now()->toDateString();

        $query = invoices::with(['patient', 'reservations.clinic', 'services.service'])
            ->whereBetween('created_at', [$from, $to])
            ->whereNull('claim_id');

        // فلتر الشركة/التعاقد عبر المريض (لو التعاقد محفوظ عند المريض)
        $query->whereHas('patient', function ($q) {
            if ($this->contract_id) {
                $q->where('insurance_contracting', $this->contract_id);
            }
        });

        // فلتر النص الحر
        if (!empty($this->search)) {
            $s = '%'.$this->search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('number', 'like', $s)
                  ->orWhereHas('patient', fn($q2) => $q2->where('full_name', 'like', $s))
                  ->orWhereHas('reservations.clinic', fn($q3) => $q3->where('name', 'like', $s))
                  ->orWhereHas('services.service', fn($q4) => $q4->where('name', 'like', $s));
            });
        }

        return $query;
    }

    /* ======================= الفواتير للعرض ======================= */
    public function getInvoicesProperty()
    {
        if (!$this->doSearch) {
            return collect(); // لا تظهر نتائج قبل الضغط على بحث
        }

        $query = $this->buildInvoiceQuery();

        // إجمالي كل الفواتير (بدون باجينشن)
        $allInvoices = (clone $query)->get();
        $this->calculateAllTotals($allInvoices);

        // الفواتير المعروضة (مع باجينشن)
        $invoices = $query->paginate($this->perPage);
        $this->calculateTotals($invoices);

        return $invoices;
    }

    protected function calculateTotals($invoices)
    {
        // يدعم Paginator و Collection
        $items = method_exists($invoices, 'getCollection') ? $invoices->getCollection() : $invoices;

        $this->totals = [
            'total_claim'          => $items->sum('total'),
            'total_patient_share'  => $items->sum('patient_contribution'),
            'total_contract_share' => $items->sum('contract_contribution'),
            'total_discount'       => $items->sum('discount'),
        ];
    }

    protected function calculateAllTotals($allInvoices)
    {
        $this->allTotals = [
            'total_claim'          => $allInvoices->sum('total'),
            'total_patient_share'  => $allInvoices->sum('patient_contribution'),
            'total_contract_share' => $allInvoices->sum('contract_contribution'),
            'total_discount'       => $allInvoices->sum('discount'),
        ];
    }

    /* ======================= حفظ المطالبة مع الفترة ======================= */
    public function saveClaim()
    {
        // تحقق من الفترة (لازم تتحدد)
        $this->validate([
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
            'contract_id' => 'nullable|integer',
        ]);

        // اجلب كل فواتير الفترة (بدون باجينشن) طبقًا لنفس الفلاتر
        $allInvoices = $this->buildInvoiceQuery()->get();

        if ($allInvoices->isEmpty()) {
            session()->flash('error', __('claims_trans.no_invoices_for_claim'));
            return;
        }

        // احسب الإجماليات من نفس المجموعة لضمان التطابق
        $sumTotal   = $allInvoices->sum('total');
        $sumDisc    = $allInvoices->sum('discount');
        $sumPat     = $allInvoices->sum('patient_contribution');
        $sumContr   = $allInvoices->sum('contract_contribution');

        // إنشاء رأس المطالبة—بدون حفظ patient_id / invoice_id
        $claim = Claim::create([
            'contract_id'          => $this->contract_id,
            'status'               => 'draft',
            'period_from'          => $this->from_date,   // ✅ حفظ الفترة
            'period_to'            => $this->to_date,     // ✅ حفظ الفترة
            'submission_date'      => now(),              // لو تحب تخليها Null طالما Draft—ممكن نشيلها
            'total_claim'          => $sumTotal,
            'total_patient_share'  => $sumPat,
            'total_contract_share' => $sumContr,
            'total_discount'       => $sumDisc,
            'user_id'              => Auth::id(),
            'account_id'           => Auth::user()->id_account ?? null,
        ]);

        // ربط كل فواتير الفترة بالمطالبة
        foreach ($allInvoices as $invoice) {
            $invoice->update(['claim_id' => $claim->id]);
        }

        session()->flash('success', __('claims_trans.claim_created'));
        return redirect()->route('insurance_claims_index');
    }

    public function render()
    {
        $contracts = contract_last_level::all();
        return view('livewire.finance.claims.claim-create', [
            'contracts' => $contracts,
            'invoices'  => $this->invoices,
        ]);
    }
}