<?php

namespace App\Http\Livewire\costing\run;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\costing\CostBatch;
use App\Models\costing\costcenter;

class CostBatchesIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // فلاتر
    public $search = '';
    public $status = '';
    public $from_date = '';
    public $to_date   = '';
    public $perPage = 10;

    // فورم
    public $editing_id = null;
    public $code = '';
    public $period_start = '';
    public $period_end   = '';
    public $scope_type   = 'hospital'; // hospital|centers|services
    public $scope_ids    = [];
    public $notes        = '';

    protected $queryString = [
        'search'    => ['except' => ''],
        'status'    => ['except' => ''],
        'from_date' => ['except' => ''],
        'to_date'   => ['except' => ''],
        'perPage'   => ['except' => 10],
    ];

    public function mount(): void
    {
        // افتراض الشهر الحالي
        $today = Carbon::today();
        $this->from_date = $today->copy()->startOfMonth()->format('Y-m-d');
        $this->to_date   = $today->copy()->endOfMonth()->format('Y-m-d');
    }

    public function rules(): array
    {
        return [
            'code'         => ['nullable','string','max:50', Rule::unique('cost_batches','code')->ignore($this->editing_id)],
            'period_start' => ['required','date'],
            'period_end'   => ['required','date','after_or_equal:period_start'],
            'scope_type'   => ['required','in:hospital,centers,services'],
            'scope_ids'    => ['array'],
            'notes'        => ['nullable','string'],
        ];
    }

    // إعادة ضبط فورم
    public function resetForm(): void
    {
        $this->editing_id = null;
        $this->code = '';
        $this->period_start = '';
        $this->period_end   = '';
        $this->scope_type   = 'hospital';
        $this->scope_ids    = [];
        $this->notes        = '';
    }

    public function updated($prop)
    {
        if (in_array($prop, ['search','status','from_date','to_date','perPage'])) {
            $this->resetPage();
        }
        if ($prop === 'scope_type') {
            $this->scope_ids = [];
        }
    }

    // توليد كود تلقائي لو فارغ
    public function generateCode(): string
    {
        $ym  = $this->period_end ? Carbon::parse($this->period_end)->format('Y-m') : Carbon::today()->format('Y-m');
        $seq = str_pad(
            (string) (CostBatch::whereYear('period_end', Carbon::parse($this->period_end ?: Carbon::today())->year)->count() + 1),
            3, '0', STR_PAD_LEFT
        );
        return 'COST-'.$ym.'-'.$seq;
    }

    // إنشاء
    public function create()
    {
        $this->resetForm();
        $this->dispatchBrowserEvent('open-batch-modal');
    }

    // تعديل
    public function edit($id)
    {
        $row = CostBatch::findOrFail($id);
        $this->editing_id   = $row->id;
        $this->code         = $row->code;
        $this->period_start = optional($row->period_start)->format('Y-m-d');
        $this->period_end   = optional($row->period_end)->format('Y-m-d');
        $this->scope_type   = $row->scope_type;
        $this->scope_ids    = $row->scope_ids ?: [];
        $this->notes        = $row->notes;

        $this->dispatchBrowserEvent('open-batch-modal');
    }

    // حفظ (إنشاء/تحديث)
    public function save()
    {
        $this->validate();

        $payload = [
            'code'         => $this->code ?: $this->generateCode(),
            'period_start' => $this->period_start,
            'period_end'   => $this->period_end,
            'scope_type'   => $this->scope_type,
            'scope_ids'    => $this->scope_ids,
            'notes'        => $this->notes,
            'user_id'      => Auth::id(),
        ];

        if ($this->editing_id) {
            $row = CostBatch::findOrFail($this->editing_id);
            // لا تسمح بتعديل كود/فترة لو الدفعة Posted
            if ($row->status === 'posted') {
                session()->flash('error', __('costing_trans.msg_no_edit_posted'));
                return;
            }
            $row->fill($payload)->save();
            session()->flash('success', __('costing_trans.updated_success'));
        } else {
            $row = CostBatch::create(array_merge($payload, ['status' => 'draft']));
            $this->editing_id = $row->id;
            session()->flash('success', __('costing_trans.created_success'));
        }

        $this->dispatchBrowserEvent('close-batch-modal');
        $this->resetForm();
    }

    // حالات الدفعة
    public function setStatus($id, $to)
    {
        $row = CostBatch::findOrFail($id);
        $allowed = ['draft','running','completed','posted'];

        if (!in_array($to, $allowed)) return;

        // قواعد بسيطة:
        if ($to === 'running' && !in_array($row->status, ['draft','completed'])) {
            session()->flash('error', __('costing_trans.msg_only_from_draft_or_completed'));
            return;
        }
        if ($to === 'completed' && !in_array($row->status, ['running'])) {
            session()->flash('error', __('costing_trans.msg_only_from_running'));
            return;
        }
        if ($to === 'posted' && $row->status !== 'completed') {
            session()->flash('error', __('costing_trans.msg_post_only_completed'));
            return;
        }
        if ($row->status === 'posted' && $to !== 'posted') {
            session()->flash('error', __('costing_trans.msg_cannot_change_posted'));
            return;
        }

        $row->update(['status' => $to]);
        session()->flash('success', __('costing_trans.status_changed'));
    }

    public function delete($id)
    {
        $row = CostBatch::findOrFail($id);
        if ($row->status === 'posted') {
            session()->flash('error', __('costing_trans.msg_no_delete_posted'));
            return;
        }
        $row->delete();
        session()->flash('success', __('costing_trans.deleted_success'));
    }

    public function getRowsProperty()
    {
        $q = CostBatch::query();

        if ($this->search) {
            $term = '%'.$this->search.'%';
            $q->where(function($qq) use ($term){
                $qq->where('code','like',$term)
                   ->orWhere('notes','like',$term);
            });
        }

        if ($this->status) {
            $q->where('status', $this->status);
        }

        if ($this->from_date) {
            $q->whereDate('period_end', '>=', $this->from_date);
        }
        if ($this->to_date) {
            $q->whereDate('period_start', '<=', $this->to_date);
        }

        return $q->orderByDesc('period_start')->orderBy('code')->paginate($this->perPage);
    }

    public function render()
    {
        $centers = [];
        if ($this->scope_type === 'centers') {
            $centers = costcenter::orderBy('code')->get(['id','code','name']);
        }

        return view('livewire.costing.run.cost-batches-index', [
            'rows'    => $this->rows,
            'centers' => $centers,
            'statuses'=> ['draft','running','completed','posted'],
        ])->layout('layouts.masterliveware');
    }
}