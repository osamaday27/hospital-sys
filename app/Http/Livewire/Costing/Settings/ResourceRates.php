<?php

namespace App\Http\Livewire\costing\settings;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\costing\resourcerate;
use App\Models\costing\costcenter;

class ResourceRates extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $type = '';
    public $status = '';
    public $perPage = 10;

    // form
    public $editing_id = null;
    public $code, $name_ar, $name_en, $type_input = 'other',
           $cost_center_id, $unit = 'minute', $rate = 0, $status_input = 'active';

    protected $queryString = ['search','type','status','perPage'];

    public function rules()
    {
        return [
            'code' => [
                'required','string','max:50',
                Rule::unique('resource_rates','code')->ignore($this->editing_id)
            ],
            'name_ar' => ['required','string','max:255'],
            'name_en' => ['nullable','string','max:255'],
            'type_input' => ['required', Rule::in(resourcerate::types())],
            'unit' => ['required', Rule::in(resourcerate::units())],
            'rate' => ['required','numeric','min:0'],
            'status_input' => ['required', Rule::in(resourcerate::statuses())],
            'cost_center_id' => ['nullable','exists:cost_centers,id'],
        ];
    }

    public function resetForm()
    {
        $this->editing_id = null;
        $this->code = $this->name_ar = $this->name_en = '';
        $this->type_input = 'other';
        $this->cost_center_id = null;
        $this->unit = 'minute';
        $this->rate = 0;
        $this->status_input = 'active';
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatchBrowserEvent('open-res-modal');
    }

    public function edit($id)
    {
        $row = resourcerate::findOrFail($id);
        $this->editing_id = $row->id;
        $this->code = $row->code;
        $this->name_ar = $row->getTranslation('name','ar');
        $this->name_en = $row->getTranslation('name','en');
        $this->type_input = $row->type;
        $this->cost_center_id = $row->cost_center_id;
        $this->unit = $row->unit;
        $this->rate = $row->rate;
        $this->status_input = $row->status;

        $this->dispatchBrowserEvent('open-res-modal');
    }

    public function save()
    {
        $this->validate();
        $payload = [
            'code'=>$this->code,
            'type'=>$this->type_input,
            'cost_center_id'=>$this->cost_center_id,
            'unit'=>$this->unit,
            'rate'=>$this->rate,
            'status'=>$this->status_input,
            'user_id'=>Auth::id(),
        ];
        $names = [
            'ar'=>$this->name_ar,
            'en'=>$this->name_en ?: $this->name_ar,
        ];

        if ($this->editing_id) {
            $row = resourcerate::findOrFail($this->editing_id);
            $row->fill($payload);
            $row->setTranslations('name',$names);
            $row->save();
            session()->flash('success', __('costing_trans.updated_success'));
        } else {
            $row = new resourcerate($payload);
            $row->setTranslations('name',$names);
            $row->save();
            session()->flash('success', __('costing_trans.created_success'));
        }

        $this->dispatchBrowserEvent('close-res-modal');
        $this->resetForm();
    }

    public function delete($id)
    {
        resourcerate::findOrFail($id)->delete();
        session()->flash('success', __('costing_trans.deleted_success'));
    }

    public function getRowsProperty()
    {
        $q = resourcerate::query();
        if ($this->search) {
            $term = '%'.$this->search.'%';
            $q->where('code','like',$term)
              ->orWhere('name->ar','like',$term)
              ->orWhere('name->en','like',$term);
        }
        if ($this->type) $q->where('type',$this->type);
        if ($this->status) $q->where('status',$this->status);
        return $q->orderBy('code')->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.costing.settings.resource-rates', [
            'rows'=>$this->rows,
            'types'=>resourcerate::types(),
            'units'=>resourcerate::units(),
            'statuses'=>resourcerate::statuses(),
            'centers'=>costcenter::orderBy('code')->get(['id','code','name']),
        ])->layout('layouts.masterliveware');
    }
}