<?php

namespace App\Http\Livewire\costing\run;

use Livewire\Component;
use App\Models\costing\CostBatch;
use App\Models\costing\costcenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\Costing\CostEngine;

class CostRunWizard extends Component
{
    public $step = 1;

    public $period_start;
    public $period_end;
    public $scope_type = 'hospital'; // hospital|centers|services
    public $scope_ids = [];

    public $batch_id = null;
    public $status   = 'draft';
    public $notes    = '';

    protected $rules = [
        'period_start' => ['required','date'],
        'period_end'   => ['required','date','after_or_equal:period_start'],
        'scope_type'   => ['required','in:hospital,centers,services'],
        'scope_ids'    => ['array'],
    ];

    public function mount(): void
    {
        $today = Carbon::today();
        $this->period_start = $today->copy()->startOfMonth()->format('Y-m-d');
        $this->period_end   = $today->copy()->endOfMonth()->format('Y-m-d');
    }

    public function updatedScopeType() { $this->scope_ids = []; }

    public function next()
    {
        if ($this->step === 1) { $this->validate(); }
        $this->step = min(4, $this->step + 1);
    }

    public function back() { $this->step = max(1, $this->step - 1); }

    public function generateBatchCode(): string
    {
        $ym  = Carbon::parse($this->period_end)->format('Y-m');
        $seq = str_pad((string)(CostBatch::whereYear('period_end', Carbon::parse($this->period_end)->year)->count() + 1), 3, '0', STR_PAD_LEFT);
        return 'COST-'.$ym.'-'.$seq;
    }

    public function runEngine(CostEngine $engine)
    {
        $this->validate();

        DB::transaction(function () use ($engine) {
            if ($this->batch_id) {
                $batch = CostBatch::findOrFail($this->batch_id);
                $batch->update([
                    'period_start' => $this->period_start,
                    'period_end'   => $this->period_end,
                    'scope_type'   => $this->scope_type,
                    'scope_ids'    => $this->scope_ids,
                    'status'       => 'running',
                    'notes'        => $this->notes,
                    'user_id'      => Auth::id(),
                ]);
                $batch->transactions()->delete();
            } else {
                $batch = CostBatch::create([
                    'code'         => $this->generateBatchCode(),
                    'period_start' => $this->period_start,
                    'period_end'   => $this->period_end,
                    'scope_type'   => $this->scope_type,
                    'scope_ids'    => $this->scope_ids,
                    'status'       => 'running',
                    'notes'        => $this->notes,
                    'user_id'      => Auth::id(),
                ]);
                $this->batch_id = $batch->id;
            }

            $direct   = $engine->calculateDirectCosts($batch);
            $overhead = $engine->allocateOverhead($batch);

            $batch->update([
                'direct_count'   => $direct['count'],
                'overhead_count' => $overhead['count'],
                'direct_total'   => $direct['total'],
                'overhead_total' => $overhead['total'],
                'grand_total'    => $direct['total'] + $overhead['total'],
            ]);
        });

        $this->status = 'running';
        $this->step   = 3;
        session()->flash('success', __('costing_trans.msg_engine_ran'));
    }

    public function finalize(CostEngine $engine)
    {
        if (!$this->batch_id) return;
        $batch = CostBatch::findOrFail($this->batch_id);

        $engine->finalize($batch);
        $this->status = $batch->status;
        $this->step   = 4;

        session()->flash('success', __('costing_trans.msg_finalized'));
    }

    public function postToGL()
    {
        if (!$this->batch_id) return;
        $batch = CostBatch::findOrFail($this->batch_id);

        // TODO: ترحيل قيود التكلفة للحسابات العامة
        $batch->update(['status' => 'posted']);
        $this->status = 'posted';

        session()->flash('success', __('costing_trans.msg_posted'));
    }

    public function getBatchProperty()
    {
        return $this->batch_id ? CostBatch::withCount(['transactions'])->find($this->batch_id) : null;
    }

    public function render()
    {
        $centers = [];
        if ($this->scope_type === 'centers') {
            $centers = costcenter::orderBy('code')->get(['id','code','name']);
        }

        return view('livewire.costing.run.cost-run-wizard', [
            'batch'   => $this->batch,
            'centers' => $centers,
        ])->layout('layouts.masterliveware');
    }
}