<?php

namespace App\Http\Livewire\SupplyChain\Inventory;

use Livewire\Component;
use App\Models\supply_chain\InventoryAudit;

class ShowInventoryAudit extends Component
{
    public $audit;

    public function mount($id)
    {
        $this->audit = InventoryAudit::with(['store', 'details.product', 'details.unit'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.supply-chain.inventory.show-inventory-audit');
    }
}