<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\damageditemrequest;
use Livewire\WithPagination;

class DamagedRequestsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $requests = damageditemrequest::with(['store', 'user'])
            ->where('request_number', 'like', '%' . $this->search . '%')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.supply-chain.program.damaged-requests-index', compact('requests'));
    }
}