<?php

namespace App\Http\Livewire\SupplyChain\Settings\StoreMainData;

use Livewire\Component;
use App\Models\supply_chain\StoreMainData;

class StoreRelations extends Component
{
    public $storeId;
    public $selectedStores = [];
    public $deleteId = null;

    protected $listeners = [
        'refreshStore' => '$refresh',
        'storeIdUpdated' => 'updateStoreId', 
        'deleteConfirmed' => 'removeRelation',
    ];

    public function mount($storeId = null)
    {
        $this->storeId = $storeId;
    }

    public function updateStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    public function addRelations()
    {
        if (!$this->storeId) {
            $this->addError('storeId', __('supply_chain_trans.no_store_selected'));
            return;
        }

        $this->validate([
            'selectedStores' => 'array',
        ]);

        $store = StoreMainData::findOrFail($this->storeId);
        $current = $store->related_stores_id ?? [];

        $new = array_unique(array_merge($current, $this->selectedStores));
        $store->related_stores_id = $new;
        $store->save();

        $this->selectedStores = [];

        $this->dispatchBrowserEvent('toast', [
            'message' => __('supply_chain_trans.related_stores_added_successfully'),
            'type' => 'success'
        ]);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function removeRelation()
    {
        if (!$this->storeId) {
            $this->addError('storeId', __('supply_chain_trans.no_store_selected'));
            return;
        }

        if ($this->deleteId) {
            $store = StoreMainData::findOrFail($this->storeId);
            $current = $store->related_stores_id ?? [];

            $updated = array_values(array_filter($current, fn($x) => $x != $this->deleteId));

            $store->related_stores_id = $updated;
            $store->save();

            $this->deleteId = null;

            $this->dispatchBrowserEvent('toast', [
                'message' => __('supply_chain_trans.related_store_deleted_successfully'),
                'type' => 'danger'
            ]);

            $this->dispatchBrowserEvent('close-delete-modal');
        }
    }

    public function render()
    {
        $allOtherStores = StoreMainData::when(
            $this->storeId,
            fn($q) => $q->where('id', '!=', $this->storeId)
        )->get();

        $relatedIds = StoreMainData::find($this->storeId)?->related_stores_id ?? [];

        $relatedStores = StoreMainData::whereIn('id', $relatedIds)->get();

        return view('livewire.supply-chain.settings.store-main-data.store-relations', [
            'allOtherStores' => $allOtherStores,
            'relatedStores'  => $relatedStores,
        ]);
    }
}
