<?php

namespace App\Http\Livewire\costing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\costing\overheadpool;
use Carbon\Carbon;

class overheadpools extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // فلاتر/بحث
    public $search = '';
    public $category = '';
    public $status = '';
    public $perPage = 10;
    public $showTrashed = false;

    // فورم
    public $editing_id = null;
    public $code = '';
    public $name_ar = '';
    public $name_en = '';
    public $category_input = 'other';
    public $period_start = '';
    public $period_end = '';
    public $amount = 0;
    public $status_input = 'draft';
    public $notes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'status' => ['except' => ''],
        'showTrashed' => ['except' => false],
        'perPage' => ['except' => 10],
    ];

    public function rules()
    {
        return [
            'code' => [
                'required','string','max:50',
                Rule::unique('overhead_pools','code')->ignore($this->editing_id)
            ],
            'name_ar' => ['required','string','max:255'],
            'name_en' => ['nullable','string','max:255'],
            'category_input' => ['required', Rule::in(overheadpool::categories())],
            'period_start' => ['required','date'],
            'period_end' => ['required','date','after_or_equal:period_start'],
            'amount' => ['required','numeric','min:0'],
            'status_input' => ['required', Rule::in(overheadpool::statuses())],
            'notes' => ['nullable','string'],
        ];
    }

    public function updated($prop)
    {
        if (in_array($prop, ['search','category','status','showTrashed','perPage'])) {
            $this->resetPage();
        }
    }

    public function resetForm()
    {
        $this->editing_id = null;
        $this->code = '';
        $this->name_ar = '';
        $this->name_en = '';
        $this->category_input = 'other';
        $this->period_start = '';
        $this->period_end = '';
        $this->amount = 0;
        $this->status_input = 'draft';
        $this->notes = '';
    }

    public function create()
    {
        $this->resetForm();
        // Livewire v2: Browser Events
        $this->dispatchBrowserEvent('open-ohp-modal');
    }

    public function edit($id)
    {
        $row = overheadpool::withTrashed()->findOrFail($id);
        $this->editing_id = $row->id;
        $this->code = $row->code;
        $this->name_ar = $row->getTranslation('name','ar') ?? '';
        $this->name_en = $row->getTranslation('name','en') ?? '';
        $this->category_input = $row->category;
        $this->period_start = optional($row->period_start)->format('Y-m-d');
        $this->period_end = optional($row->period_end)->format('Y-m-d');
        $this->amount = $row->amount;
        $this->status_input = $row->status;
        $this->notes = $row->notes;

        $this->dispatchBrowserEvent('open-ohp-modal');
    }

    public function save()
    {
        $this->validate();

        // تنبيه بسيط لو الفترة > 92 يوم (3 أشهر تقريبًا)
        $start = Carbon::parse($this->period_start);
        $end   = Carbon::parse($this->period_end);
        if ($start->diffInDays($end) > 120) {
            // تحذير فقط، مش مانع للحفظ — لو تحب نخليها Validation Rule قولّي
        }

        $payload = [
            'code'         => $this->code,
            'category'     => $this->category_input,
            'period_start' => $this->period_start,
            'period_end'   => $this->period_end,
            'amount'       => $this->amount,
            'status'       => $this->status_input,
            'notes'        => $this->notes,
            'user_id'      => Auth::id(),
        ];

        $names = [
            'ar' => $this->name_ar,
            'en' => $this->name_en ?: $this->name_ar,
        ];

        if ($this->editing_id) {
            $row = overheadpool::withTrashed()->findOrFail($this->editing_id);
            $row->fill($payload);
            $row->setTranslations('name', $names);
            $row->save();
            session()->flash('success', __('costing_trans.updated_success'));
        } else {
            $row = new overheadpool($payload);
            $row->setTranslations('name', $names);
            $row->save();
            session()->flash('success', __('costing_trans.created_success'));
        }

        $this->dispatchBrowserEvent('close-ohp-modal');
        $this->resetForm();
    }

    public function delete($id)
    {
        $row = overheadpool::findOrFail($id);
        $row->delete();
        session()->flash('success', __('costing_trans.deleted_success'));
    }

    public function restore($id)
    {
        $row = overheadpool::withTrashed()->findOrFail($id);
        if ($row->trashed()) {
            $row->restore();
            session()->flash('success', __('costing_trans.restored_success'));
        }
    }

    public function post($id)
    {
        $row = overheadpool::findOrFail($id);
        if ($row->status === 'approved') {
            $row->status = 'posted';
            $row->save();
            session()->flash('success', __('costing_trans.posted_success'));
        } else {
            session()->flash('error', __('costing_trans.post_only_approved'));
        }
    }

    public function approve($id)
    {
        $row = overheadpool::findOrFail($id);
        if ($row->status === 'draft') {
            $row->status = 'approved';
            $row->save();
            session()->flash('success', __('costing_trans.approved_success'));
        }
    }

    public function getRowsProperty()
    {
        $q = overheadpool::query();

        if ($this->showTrashed) $q->withTrashed();

        if ($this->search) {
            $term = '%'.$this->search.'%';
            $q->where(function($qq) use ($term) {
                $qq->where('code', 'like', $term)
                   ->orWhere('name->ar', 'like', $term)
                   ->orWhere('name->en', 'like', $term);
            });
        }

        if ($this->category) $q->where('category', $this->category);
        if ($this->status)   $q->where('status', $this->status);

        return $q->orderByDesc('period_start')->orderBy('code')->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.costing.overheadpools', [
            'rows'       => $this->rows,
            'categories' => overheadpool::categories(),
            'statuses'   => overheadpool::statuses(),
        ])->layout('layouts.master');
    }

     
}