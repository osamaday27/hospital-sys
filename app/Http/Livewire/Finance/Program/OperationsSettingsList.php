<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\finance\servicelevel;
use App\Models\finance\servicesublevel;
use App\Models\finance\service;

class OperationsSettingsList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $levels;
    public $sublevels = [];
    public $level1_id = '';
    public $sublevel_id = '';
    public $search = '';
    public $contract_id;

    public function mount($contract_id)
    {
        $this->contract_id = $contract_id;
        $this->levels = servicelevel::where('level_type', 'operations')->get();
    }

    public function updatedLevel1Id()
    {
        $this->sublevel_id = '';
        $this->sublevels = servicesublevel::where('level_id', $this->level1_id)->get();
    }

    public function render()
    {
        $query = service::with(['sublevel.level1', 'pricelistServices'])
            ->whereHas('sublevel.level1', function ($q) {
                $q->where('level_type', 'operations');
            });

        if ($this->level1_id != '') {
            $query->whereHas('sublevel.level1', function ($q) {
                $q->where('id', $this->level1_id);
            });
        }

        if ($this->sublevel_id != '') {
            $query->where('sublevel_id', $this->sublevel_id);
        }

        if (!empty($this->search)) {
            $searchTerm = strtolower($this->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar'))) LIKE ?", ['%' . $searchTerm . '%'])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en'))) LIKE ?", ['%' . $searchTerm . '%']);
            });
        }

        $services = $query->paginate(15);

        return view('livewire.finance.program.operations-settings-list', [
            'services' => $services,
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['level1_id', 'sublevel_id', 'search']);
        $this->sublevels = [];
    }
}