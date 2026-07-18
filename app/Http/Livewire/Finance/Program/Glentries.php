<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\finance\glentry;
use App\Models\finance\glaccount;
use Illuminate\Support\Facades\Auth;

class Glentries extends Component
{
    use WithPagination;

    // بيانات الرأس
    public $date, $reference_type, $reference_id, $description;
    public $showForm = false;

    // فلترة وبحث
    public $search = '';
    public $statusFilter = '';
    public $from_date, $to_date;

    // بيانات السطر
    public $searchAccount = '';
    public $selected_account;
    public $line_debit = 0;
    public $line_credit = 0;
    public $line_cost_center = '';

    // الأسطر
    public $lines = [];

    // الإجماليات
    public $total_debit = 0;
    public $total_credit = 0;

    public $selectedEntry;

    protected $rules = [
        'date' => 'required|date',
    ];

    public function render()
    {
        $query = glentry::with(['user','lines.account'])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                  ->orWhere('id', $this->search)
                  ->orWhere('reference_id', $this->search)
                  ->orWhere('reference_type', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->from_date && $this->to_date) {
            $query->whereBetween('date', [$this->from_date, $this->to_date]);
        }

        $entries = $query->paginate(10);

        // الحسابات للبحث والإضافة
        $accounts = glaccount::query()
            ->when($this->searchAccount, function ($q) {
                $q->where('name_ar', 'like', "%{$this->searchAccount}%")
                  ->orWhere('name_en', 'like', "%{$this->searchAccount}%")
                  ->orWhere('code', 'like', "%{$this->searchAccount}%");
            })
            ->limit(20)
            ->get();

        return view('livewire.finance.program.glentries', [
            'entries' => $entries,
            'accounts' => $accounts,
        ]);
    }

    public function startNewEntry()
    {
        $this->resetEntry();
        $this->showForm = true;
    }

    public function cancelEntry()
    {
        $this->resetEntry();
        $this->showForm = false;
    }

    private function resetEntry()
    {
        $this->date = now()->format('Y-m-d');
        $this->reference_type = '';
        $this->reference_id = '';
        $this->description = '';
        $this->selected_account = null;
        $this->line_debit = 0;
        $this->line_credit = 0;
        $this->line_cost_center = '';
        $this->lines = [];
        $this->total_debit = 0;
        $this->total_credit = 0;
        $this->selectedEntry = null;
    }

    public function addLine()
    {
        if (!$this->selected_account) {
            session()->flash('error', 'اختر الحساب أولاً');
            return;
        }
        if ($this->line_debit <= 0 && $this->line_credit <= 0) {
            session()->flash('error', 'أدخل قيمة مدين أو دائن');
            return;
        }

        $account = glaccount::find($this->selected_account);

        $this->lines[] = [
            'account_id'   => $account->id,
            'account_name' => $account->fullName(),
            'debit'        => $this->line_debit > 0 ? $this->line_debit : 0,
            'credit'       => $this->line_credit > 0 ? $this->line_credit : 0,
            'cost_center'  => $this->line_cost_center,
        ];

        $this->recalculateTotals();
        $this->selected_account = null;
        $this->line_debit = 0;
        $this->line_credit = 0;
        $this->line_cost_center = '';
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        $this->recalculateTotals();
    }

    private function recalculateTotals()
    {
        $this->total_debit = collect($this->lines)->sum('debit');
        $this->total_credit = collect($this->lines)->sum('credit');
    }

    public function saveEntry()
    {
        $this->validate();

        if ($this->total_debit !== $this->total_credit || $this->total_debit <= 0) {
            session()->flash('error', 'القيد غير متوازن أو فارغ');
            return;
        }

        \DB::transaction(function () {
            $entry = glentry::create([
                'date'           => $this->date,
                'reference_type' => $this->reference_type,
                'reference_id'   => $this->reference_id ?: null,
                'description'    => $this->description,
                'status'         => 'draft',
                'created_by'     => Auth::id(),
            ]);

            foreach ($this->lines as $line) {
                $entry->lines()->create([
                    'account_id'  => $line['account_id'],
                    'debit'       => $line['debit'],
                    'credit'      => $line['credit'],
                    'cost_center' => $line['cost_center'],
                ]);
            }
        });

        session()->flash('success', 'تم حفظ القيد بنجاح 🎉');
        $this->cancelEntry();
    }

    public function showDetails($id)
    {
        $this->selectedEntry = glentry::with(['lines.account','user'])->findOrFail($id);
    }
}