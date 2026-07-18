<?php

namespace App\Http\Livewire\Costing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\costing\costcenter;
use App\Models\costing\costcenterassignment;
use App\models\hr\Department;
use App\models\hr\section;
use App\models\administrative\admsclinic;
use App\models\general\adm_s_rooms_surgical;

class CostCenterLinking extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $type = 'department';
    public $entity_id;
    public $search = '';
    public $statusFilter = '';
    public $dateFrom;
    public $dateTo;
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $cost_center_id;
    public $effective_from;
    public $effective_to;
    public $status = 'active';
    public $notes;

    protected $typesMap = [
        'department' => Department::class,
        'section'    => section::class,
        'clinic'     => admsclinic::class,
        'room'       => adm_s_rooms_surgical::class,
    ];

    protected function rules()
    {
        return [
            'type'           => ['required', Rule::in(array_keys($this->typesMap))],
            'entity_id'      => ['required', 'integer'],
            'cost_center_id' => ['required', 'integer', 'exists:cost_centers,id'],
            'status'         => ['required', Rule::in(['active','inactive'])],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
            'notes'          => ['nullable', 'string'],
        ];
    }

    public function updatedType()
    {
        $this->reset(['entity_id', 'search']);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getEntitiesProperty()
    {
        $model = $this->typesMap[$this->type] ?? null;
        if (!$model) return collect();

        $query = $model::query();

        if ($this->search) {
            $query->where(function ($q) {
                $term = "%{$this->search}%";
                $q->where('name_ar', 'like', $term)
                  ->orWhere('name_en', 'like', $term)
                  ->orWhere('name', 'like', $term)
                  ->orWhere('clinic_code', 'like', $term);
            });
        }

        return $query->orderBy('id', 'desc')->limit(50)->get();
    }

    public function getCostCentersProperty()
    {
        return costcenter::orderBy('code')->limit(100)->get();
    }

    public function createLink()
    {
        $this->validate();

        $exists = costcenterassignment::where('cost_center_id', $this->cost_center_id)
            ->where('assignable_type', $this->typesMap[$this->type])
            ->where('assignable_id', $this->entity_id)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            session()->flash('error', __('costing_links_trans.already_linked'));
            return;
        }

        costcenterassignment::create([
            'cost_center_id' => $this->cost_center_id,
            'assignable_type'=> $this->typesMap[$this->type],
            'assignable_id'  => $this->entity_id,
            'status'         => $this->status,
            'effective_from' => $this->effective_from,
            'effective_to'   => $this->effective_to,
            'notes'          => $this->notes,
            'user_id'        => Auth::id(),
        ]);

        $this->reset(['cost_center_id','effective_from','effective_to','status','notes','entity_id']);
        $this->status = 'active';

        session()->flash('success', __('costing_links_trans.created_ok'));
    }

    public function toggleStatus($id)
    {
        $row = costcenterassignment::findOrFail($id);
        $row->status = $row->status === 'active' ? 'inactive' : 'active';
        $row->save();

        session()->flash('success', __('costing_links_trans.status_toggled'));
    }

    public function deleteLink($id)
    {
        $row = costcenterassignment::findOrFail($id);
        $row->delete();
        session()->flash('success', __('costing_links_trans.deleted_ok'));
        $this->resetPage();
    }

    public function render()
    {
        $rows = costcenterassignment::with(['costcenter'])
            ->when($this->type, fn($q) => $q->where('assignable_type', $this->typesMap[$this->type]))
            ->when($this->entity_id, fn($q) => $q->where('assignable_id', $this->entity_id))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('effective_from', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('effective_to', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.costing.cost-center-linking', [
            'entities'     => $this->entities,
            'costCenters'  => $this->costCenters,
            'rows'         => $rows,
        ]);
    }
}