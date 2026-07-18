<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\InventoryAudit;

class IndexInventoryAudit extends Component
{
    public $audits;

    public function mount()
    {
        $this->audits = InventoryAudit::with(['store', 'user'])
            ->orderByDesc('audit_date')
            ->get();
    }

    public function render()
    {
        return view('livewire.supply-chain.Program.index-inventory-audit');
    }
} 