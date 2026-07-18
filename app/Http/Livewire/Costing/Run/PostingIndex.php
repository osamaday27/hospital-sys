<?php

namespace App\Http\Livewire\costing\run;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\costing\CostBatch;
use App\Models\finance\GlJournalBatch;
use App\Services\Costing\GlPostingService;

class PostingIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $status = '';
    public $perPage = 10;

    public $selected_cost_batch_id = '';
    public $notes = '';

    protected $queryString = [
        'search'=>['except'=>''],
        'status'=>['except'=>''],
        'perPage'=>['except'=>10],
    ];

    public function postToGl(GlPostingService $svc)
    {
        $this->validate([
            'selected_cost_batch_id' => ['required','exists:cost_batches,id']
        ],[],[
            'selected_cost_batch_id'=>__('costing_trans.cost_batch')
        ]);

        try {
            $glBatch = $svc->postCostBatchToGl((int)$this->selected_cost_batch_id, $this->notes);
            session()->flash('success', __('costing_trans.msg_posted_ok', ['code'=>$glBatch->code]));
            $this->reset('notes','selected_cost_batch_id');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function getRowsProperty()
    {
        $q = GlJournalBatch::query()->withCount('entries');

        if ($this->search) {
            $term = '%'.$this->search.'%';
            $q->where(function($qq) use ($term){
                $qq->where('code','like',$term)
                   ->orWhere('notes','like',$term)
                   ->orWhere('source','like',$term);
            });
        }
        if ($this->status) $q->where('status',$this->status);

        return $q->orderByDesc('id')->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.costing.run.posting-index', [
            'rows'    => $this->rows,
            'batches' => CostBatch::orderByDesc('period_start')->get(['id','code','period_start','period_end','status']),
            'statuses'=> ['draft','posted'],
        ])->layout('layouts.masterliveware');
    }
}