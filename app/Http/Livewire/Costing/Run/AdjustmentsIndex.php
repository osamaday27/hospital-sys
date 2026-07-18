<?php

namespace App\Http\Livewire\costing\run;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\costing\CostAdjustment;
use App\Models\costing\CostBatch;
use App\Models\costing\costcenter;
use App\Services\Costing\AdjustmentService;

class AdjustmentsIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // فلاتر
    public $search = '';
    public $status = '';
    public $batch_id = '';
    public $line_type = '';
    public $case_type = '';
    public $from_date = '';
    public $to_date   = '';
    public $perPage = 10;

    // فورم
    public $editing_id = null;
    public $form_batch_id = '';
    public $form_case_type = 'other';
    public $form_case_id   = '';
    public $form_service_id = '';
    public $form_cost_center_id = '';
    public $form_line_type = 'direct';
    public $form_item_type = 'misc';
    public $form_adj_type  = 'increase';
    public $form_amount    = 0;
    public $form_effective_date = '';
    public $form_reference = '';
    public $form_reason    = '';
    public $form_status    = 'draft';

    protected $queryString = [
        'search' => ['except'=>''],
        'status' => ['except'=>''],
        'batch_id' => ['except'=>''],
        'line_type' => ['except'=>''],
        'case_type' => ['except'=>''],
        'from_date' => ['except'=>''],
        'to_date'   => ['except'=>''],
        'perPage'   => ['except'=>10],
    ];

    public function mount(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $this->from_date = Carbon::parse($today)->startOfMonth()->format('Y-m-d');
        $this->to_date   = Carbon::parse($today)->endOfMonth()->format('Y-m-d');
        $this->form_effective_date = $today;
    }

    public function rules(): array
    {
        return [
            'form_batch_id'      => ['nullable','exists:cost_batches,id'],
            'form_case_type'     => ['required', Rule::in(CostAdjustment::caseTypes())],
            'form_case_id'       => ['nullable','integer'],
            'form_service_id'    => ['nullable','integer'],
            'form_cost_center_id'=> ['nullable','integer'],
            'form_line_type'     => ['required', Rule::in(CostAdjustment::lineTypes())],
            'form_item_type'     => ['required', Rule::in(CostAdjustment::itemTypes())],
            'form_adj_type'      => ['required', Rule::in(CostAdjustment::adjTypes())],
            'form_amount'        => ['required','numeric'],
            'form_effective_date'=> ['nullable','date'],
            'form_reference'     => ['nullable','string','max:100'],
            'form_reason'        => ['nullable','string'],
            'form_status'        => ['required', Rule::in(CostAdjustment::statuses())],
        ];
    }

    public function updated($prop)
    {
        if (in_array($prop, ['search','status','batch_id','line_type','case_type','from_date','to_date','perPage'])) {
            $this->resetPage();
        }
    }

    public function resetForm(): void
    {
        $this->editing_id = null;
        $this->form_batch_id = $this->batch_id ?: '';
        $this->form_case_type = 'other';
        $this->form_case_id = '';
        $this->form_service_id = '';
        $this->form_cost_center_id = '';
        $this->form_line_type = 'direct';
        $this->form_item_type = 'misc';
        $this->form_adj_type = 'increase';
        $this->form_amount = 0;
        $this->form_effective_date = Carbon::today()->format('Y-m-d');
        $this->form_reference = '';
        $this->form_reason = '';
        $this->form_status = 'draft';
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatchBrowserEvent('open-adj-modal');
    }

    public function edit($id)
    {
        $row = CostAdjustment::findOrFail($id);
        $this->editing_id = $row->id;
        $this->form_batch_id = $row->batch_id;
        $this->form_case_type = $row->case_type;
        $this->form_case_id   = $row->case_id;
        $this->form_service_id = $row->service_id;
        $this->form_cost_center_id = $row->cost_center_id;
        $this->form_line_type = $row->line_type;
        $this->form_item_type = $row->item_type;
        $this->form_adj_type  = $row->adj_type;
        $this->form_amount    = $row->amount;
        $this->form_effective_date = optional($row->effective_date)->format('Y-m-d');
        $this->form_reference = $row->reference;
        $this->form_reason    = $row->reason;
        $this->form_status    = $row->status;

        $this->dispatchBrowserEvent('open-adj-modal');
    }

    public function save()
    {
        // تحقق أساسي
        $validator = \Validator::make([
            'form_batch_id'        => $this->form_batch_id,
            'form_case_type'       => $this->form_case_type,
            'form_case_id'         => $this->form_case_id,
            'form_service_id'      => $this->form_service_id,
            'form_cost_center_id'  => $this->form_cost_center_id,
            'form_line_type'       => $this->form_line_type,
            'form_item_type'       => $this->form_item_type,
            'form_adj_type'        => $this->form_adj_type,
            'form_amount'          => $this->form_amount,
            'form_effective_date'  => $this->form_effective_date,
            'form_reference'       => $this->form_reference,
            'form_reason'          => $this->form_reason,
            'form_status'          => $this->form_status,
        ], $this->rules());

        // تحقق إضافي: لازم واحد على الأقل من (case_id أو service_id أو cost_center_id)
        $validator->after(function ($v) {
            $noCase   = empty($this->form_case_id);
            $noServ   = empty($this->form_service_id);
            $noCenter = empty($this->form_cost_center_id);

            if ($noCase && $noServ && $noCenter) {
                $v->errors()->add('target', __('adjustments_trans.err_target_required'));
            }

            if (!empty($this->form_case_id) && $this->form_case_type === 'other') {
                $v->errors()->add('form_case_type', __('adjustments_trans.err_case_type_required'));
            }
        });

        $validator->validate();

        // لا تعديل بعد posted
        if ($this->editing_id) {
            $row = CostAdjustment::findOrFail($this->editing_id);
            if ($row->status === 'posted') {
                session()->flash('error', __('adjustments_trans.msg_no_edit_posted'));
                return;
            }
        }

        $payload = [
            'batch_id'       => $this->form_batch_id ?: null,
            'case_type'      => $this->form_case_type,
            'case_id'        => $this->form_case_id ?: null,
            'service_id'     => $this->form_service_id ?: null,
            'cost_center_id' => $this->form_cost_center_id ?: null,
            'line_type'      => $this->form_line_type,
            'item_type'      => $this->form_item_type,
            'adj_type'       => $this->form_adj_type,
            'amount'         => $this->form_amount,
            'effective_date' => $this->form_effective_date ?: null,
            'reference'      => $this->form_reference,
            'reason'         => $this->form_reason,
            'status'         => $this->form_status,
            'user_id'        => Auth::id(),
        ];

        if ($this->editing_id) {
            CostAdjustment::findOrFail($this->editing_id)->update($payload);
            session()->flash('success', __('adjustments_trans.updated_success'));
        } else {
            CostAdjustment::create($payload);
            session()->flash('success', __('adjustments_trans.created_success'));
        }

        $this->dispatchBrowserEvent('close-adj-modal');
        $this->resetForm();
    }

    public function approve($id)
    {
        $row = CostAdjustment::findOrFail($id);
        if ($row->status !== 'draft') {
            session()->flash('error', __('adjustments_trans.msg_approve_only_draft'));
            return;
        }
        $row->update(['status' => 'approved']);
        session()->flash('success', __('adjustments_trans.approved_success'));
    }

    public function post($id, AdjustmentService $svc)
    {
        $row = CostAdjustment::findOrFail($id);
        try {
            $svc->postOne($row);
            session()->flash('success', __('adjustments_trans.posted_success'));
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $row = CostAdjustment::findOrFail($id);
        if ($row->status === 'posted') {
            session()->flash('error', __('adjustments_trans.msg_no_delete_posted'));
            return;
        }
        $row->delete();
        session()->flash('success', __('adjustments_trans.deleted_success'));
    }

    public function getRowsProperty()
    {
        $q = CostAdjustment::query()->with('batch');

        if ($this->search) {
            $term = '%'.$this->search.'%';
            $q->where(function($qq) use ($term){
                $qq->where('reference','like',$term)
                   ->orWhere('reason','like',$term);
            });
        }
        if ($this->status)    $q->where('status', $this->status);
        if ($this->batch_id)  $q->where('batch_id', $this->batch_id);
        if ($this->line_type) $q->where('line_type', $this->line_type);
        if ($this->case_type) $q->where('case_type', $this->case_type);

        if ($this->from_date) $q->whereDate('effective_date','>=',$this->from_date);
        if ($this->to_date)   $q->whereDate('effective_date','<=',$this->to_date);

        return $q->orderByDesc('id')->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.costing.run.adjustments-index', [
            'rows'     => $this->rows,
            'batches'  => CostBatch::orderByDesc('period_start')->limit(100)->get(['id','code','period_start','period_end','status']),
            'centers'  => costcenter::orderBy('code')->get(['id','code','name']),
            'statuses' => CostAdjustment::statuses(),
            'caseTypes'=> CostAdjustment::caseTypes(),
            'lineTypes'=> CostAdjustment::lineTypes(),
            'itemTypes'=> CostAdjustment::itemTypes(),
            'adjTypes' => CostAdjustment::adjTypes(),
        ])->layout('layouts.masterliveware');
    }
}