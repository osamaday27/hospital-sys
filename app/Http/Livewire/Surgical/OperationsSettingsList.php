<?php

namespace App\Http\Livewire\surgical;
use Livewire\Component;
use App\models\finance\servicelevel;
use App\models\finance\servicesublevel;
use App\models\finance\service;
use Livewire\WithPagination;
class OperationsSettingsList extends Component
{
    use WithPagination;
    public $levels;
    public $sublevels = [];

    public $level1_id = '';
    public $sublevel_id = '';
    public $search = '';

    public function mount()
    {
        $this->levels = servicelevel::where('level_type', 'operations')->get();
    }

    public function updatedLevel1Id()
    {
        $this->sublevel_id = '';
        $this->sublevels = servicesublevel::where('level_id', $this->level1_id)->get();
    }

    // app/Http/Livewire/Surgical/OperationsSettingsList.php

    public function render()
    {
        $query = service::with(['sublevel.level1', 'pricelistServices'])->whereHas('sublevel.level1', function ($q) {
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
            $query->where(function ($q) {
                $q->where('name->ar', 'like', '%' . $this->search . '%')->orWhere('name->en', 'like', '%' . $this->search . '%');
            });
        }

        // ✅ استخدم paginate بدل get
        $services = $query->paginate(15); // أو أي رقم للصفحة

        return view('livewire.surgical.operations-settings-list', [
            'services' => $services,
        ]);
    }
    public function resetFilters()
    {
        $this->reset(['level1_id', 'sublevel_id', 'search']);
        $this->sublevels = [];
    }
}