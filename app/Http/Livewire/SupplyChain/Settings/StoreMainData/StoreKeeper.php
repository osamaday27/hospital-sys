<?php

namespace App\Http\Livewire\SupplyChain\Settings\StoreMainData;

use Livewire\Component;
use App\Models\supply_chain\storekeepermodel;
use App\Models\User;

class StoreKeeper extends Component
{
    public $storeId;

    // نموذج الإدخال
    public $user_id;
    public $status = 'active';
    public $can_issue = false;
    public $can_add = false;
    public $can_inventory = false;
    public $can_report = false;
    public $is_main_keeper = false;

    public $edit_id = null;
    public $delete_id = null;

    protected $listeners = [
        'storeIdUpdated' => 'updateStoreId',
    ];

    public function mount($storeId = null)
    {
        $this->storeId = $storeId;
    }

    public function updateStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    public function toggleAllPermissions()
    {
        $state = $this->is_main_keeper;
        $this->can_issue = $state;
        $this->can_add = $state;
        $this->can_inventory = $state;
        $this->can_report = $state;
    }

    public function save()
    {
        if (!$this->storeId) {
            $this->addError('storeId', __('supply_chain_trans.no_store_selected'));
            return;
        }

        $this->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive'
        ]);

        $exists = storekeepermodel::where('store_id', $this->storeId)
            ->where('user_id', $this->user_id)
            ->when($this->edit_id, fn($q) => $q->where('id', '!=', $this->edit_id))
            ->exists();

        if ($exists) {
            $this->addError('user_id', __('supply_chain_trans.user_already_assigned_to_store'));
            return;
        }

        storekeepermodel::updateOrCreate(
            ['id' => $this->edit_id],
            [
                'store_id' => $this->storeId,
                'user_id' => $this->user_id,
                'status' => $this->status,
                'can_issue' => $this->can_issue,
                'can_add' => $this->can_add,
                'can_inventory' => $this->can_inventory,
                'can_report' => $this->can_report,
                'is_main_keeper' => $this->is_main_keeper,
            ]
        );

        $this->dispatchBrowserEvent('toast', [
            'message' => $this->edit_id
                ? __('supply_chain_trans.keeper_updated_successfully')
                : __('supply_chain_trans.keeper_added_successfully'),
            'type' => 'success'
        ]);

        $this->resetForm();
    }

    public function edit($id)
    {
        $keeper = storekeepermodel::findOrFail($id);

        $this->edit_id = $keeper->id;
        $this->user_id = $keeper->user_id;
        $this->status = $keeper->status;
        $this->can_issue = $keeper->can_issue;
        $this->can_add = $keeper->can_add;
        $this->can_inventory = $keeper->can_inventory;
        $this->can_report = $keeper->can_report;
        $this->is_main_keeper = $keeper->is_main_keeper;
    }

    public function confirmDelete($id)
    {
        $this->delete_id = $id;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function delete()
    {
        storekeepermodel::findOrFail($this->delete_id)->delete();

        $this->dispatchBrowserEvent('toast', [
            'message' => __('supply_chain_trans.keeper_deleted_successfully'),
            'type' => 'danger'
        ]);

        $this->delete_id = null;

        $this->dispatchBrowserEvent('hide-delete-modal');
    }

    public function resetForm()
    {
        $this->reset([
            'user_id', 'status', 'can_issue', 'can_add',
            'can_inventory', 'can_report', 'is_main_keeper', 'edit_id'
        ]);
        $this->status = 'active';
    }

    public function render()
    {
        return view('livewire.supply-chain.settings.store-main-data.store-keeper', [
            'users' => User::all(),
            'keepers' => storekeepermodel::with('user')->where('store_id', $this->storeId)->get()
        ]);
    }
}
