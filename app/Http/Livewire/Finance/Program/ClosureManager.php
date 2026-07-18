<?php
namespace App\Http\Livewire\Finance\Program;
use Livewire\Component;
use App\Models\finance\FinBGlClosure;
use App\Models\finance\FinBGlClosureLine;
use App\Models\finance\glentryline as FinBGlEntryLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ClosureManager extends Component
{
    public $closures = [];
    public $type, $period_start, $period_end;

    public function mount() {
        $this->closures = FinBGlClosure::latest()->get();
    }

    public function createClosure() {
        $this->validate([
            'type' => 'required|in:monthly,yearly',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        DB::transaction(function () {
            $closure = FinBGlClosure::create([
                'period_start' => $this->period_start,
                'period_end' => $this->period_end,
                'type' => $this->type,
                'status' => 'closed',
                'created_by' => Auth::id(),
            ]);

            // حساب أرصدة الحسابات للفترة
            $lines = FinBGlEntryLine::select('account_id',
                        DB::raw('SUM(debit) as debit'),
                        DB::raw('SUM(credit) as credit'))
                ->whereHas('entry', function($q) {
                    $q->whereBetween('date', [$this->period_start, $this->period_end])
                      ->where('status', 'posted');
                })
                ->groupBy('account_id')
                ->get();

            foreach ($lines as $line) {
                FinBGlClosureLine::create([
                    'closure_id' => $closure->id,
                    'account_id' => $line->account_id,
                    'debit' => $line->debit,
                    'credit' => $line->credit,
                ]);
            }
        });

        session()->flash('success', 'تم إنشاء إقفال الفترة بنجاح.');
        $this->closures = FinBGlClosure::latest()->get();
    }

    public function render() {
        return view('livewire.finance.program.closure-manager');
    }
}