<?php

namespace App\Http\Livewire\costing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\costing\costcenter;

class Costcenters extends Component
{
    use WithPagination;

    // لو بتستخدم Bootstrap للترقيم في Livewire 2
    protected $paginationTheme = 'bootstrap';

    // فلاتر/بحث
    public $search = '';
    public $type = '';
    public $status = '';
    public $perPage = 10;
    public $showTrashed = false;

    // فورم
    public $editing_id = null;
    public $parent_id = null;
    public $code = '';
    public $name_ar = '';
    public $name_en = '';
    public $type_input = 'other';
    public $status_input = 'active';
    public $notes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'status' => ['except' => ''],
        'showTrashed' => ['except' => false],
        'perPage' => ['except' => 10],
    ];

    public function rules()
    {
        return [
            'parent_id'   => ['nullable','exists:cost_centers,id'],
            'code'        => [
                'required','string','max:50',
                Rule::unique('cost_centers','code')->ignore($this->editing_id)
            ],
            'name_ar'     => ['required','string','max:255'],
            'name_en'     => ['nullable','string','max:255'],
            'type_input'  => ['required', Rule::in(costcenter::types())],
            'status_input'=> ['required', Rule::in(costcenter::statuses())],
            'notes'       => ['nullable','string'],
        ];
    }

    public function updated($prop)
    {
        if (in_array($prop, ['search','type','status','showTrashed','perPage'])) {
            $this->resetPage();
        }
    }

    public function resetForm()
    {
        $this->editing_id = null;
        $this->parent_id = null;
        $this->code = '';
        $this->name_ar = '';
        $this->name_en = '';
        $this->type_input = 'other';
        $this->status_input = 'active';
        $this->notes = '';
    }

    public function create()
    {
        $this->resetForm();
        // Livewire v2: نفتح المودال بحدث للمتصفح
        $this->dispatchBrowserEvent('open-center-modal');
    }

    public function edit($id)
    {
        $row = costcenter::withTrashed()->findOrFail($id);
        $this->editing_id  = $row->id;
        $this->parent_id   = $row->parent_id;
        $this->code        = $row->code;
        $this->name_ar     = $row->getTranslation('name','ar') ?? '';
        $this->name_en     = $row->getTranslation('name','en') ?? '';
        $this->type_input  = $row->type;
        $this->status_input= $row->status;
        $this->notes       = $row->notes;

        $this->dispatchBrowserEvent('open-center-modal');
    }

    public function save()
    {
        $this->validate();

        $payload = [
            'parent_id' => $this->parent_id ?: null,
            'code'      => $this->code,
            'type'      => $this->type_input,
            'status'    => $this->status_input,
            'notes'     => $this->notes,
            'user_id'   => Auth::id(),
        ];

        $names = [
            'ar' => $this->name_ar,
            'en' => $this->name_en ?: $this->name_ar,
        ];

        if ($this->editing_id) {
            $row = costcenter::withTrashed()->findOrFail($this->editing_id);
            $row->fill($payload);
            $row->setTranslations('name', $names);
            $row->save();
            session()->flash('success', __('costing_trans.updated_success'));
        } else {
            $row = new costcenter($payload);
            $row->setTranslations('name',$names);
            $row->save();
            session()->flash('success', __('costing_trans.created_success'));
        }

        // إغلاق المودال بحدث للمتصفح
        $this->dispatchBrowserEvent('close-center-modal');
        $this->resetForm();
    }

    public function delete($id)
    {
        $row = costcenter::findOrFail($id);
        $row->delete();
        session()->flash('success', __('costing_trans.deleted_success'));
    }

    public function restore($id)
    {
        $row = costcenter::withTrashed()->findOrFail($id);
        if ($row->trashed()) {
            $row->restore();
            session()->flash('success', __('costing_trans.restored_success'));
        }
    }

    public function getParentsProperty()
    {
        return costcenter::orderBy('code')->get(['id','parent_id','name','code']);
    }

    public function getRowsProperty()
    {
        $q = costcenter::query()->with('parent');

        if ($this->showTrashed) {
            $q->withTrashed();
        }

        if ($this->search) {
            $term = '%'.$this->search.'%';
            $q->where(function($qq) use ($term) {
                $qq->where('code','like',$term)
                   ->orWhere('name->ar','like',$term)
                   ->orWhere('name->en','like',$term);
            });
        }

        if ($this->type)   $q->where('type',$this->type);
        if ($this->status) $q->where('status',$this->status);

        return $q->orderBy('code')->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.costing.costcenters', [
            'rows'    => $this->rows,
            'parents' => $this->parents,
            'types'   => costcenter::types(),
            'statuses'=> costcenter::statuses(),
        ])->layout('layouts.master');
    }
}