<?php

namespace App\Http\Livewire\SupplyChain\Settings\StoreMainData;

use Livewire\Component;
use App\Models\supply_chain\StoreTransaction;

class StoreTransactions extends Component
{
    public $storeId;
    public $selectAll = false;
    public $transactions = [
        'opening_balances'           => false,
        'add_items'                  => false,
        'return_to_supplier'         => false,
        'return_from_department'     => false,
        'return_from_customer'       => false,
        'return_balance_from_store'  => false,
        'issue_to_customer'          => false,
        'issue_to_department'        => false,
        'transfer_balance_to_store'  => false,
        'inventory'                  => false,
        'items_destruction'          => false,
        'issue_to_employee'          => false,
        'return_from_employee'       => false,
        'balance_adjustment_increase'=> false,
        'balance_adjustment_decrease'=> false,
        'issue_to_supplier'          => false,
        'return_balance_to_store'    => false,
        'additional_opening_balance' => false,
        'issue_to_another_branch'    => false,
        'add_from_another_branch'    => false,
    ];

    protected $listeners = [
        'storeIdUpdated' => 'updateStoreId',
    ];

    public function mount($storeId = null)
    {
        $this->storeId = $storeId;

        if ($this->storeId) {
            $this->loadTransactions();
        }
    }

    public function updateStoreId($storeId)
    {
        $this->storeId = $storeId;
        $this->loadTransactions();
    }

    private function loadTransactions()
    {
        $storeTransaction = StoreTransaction::where('store_id', $this->storeId)->first();

        if ($storeTransaction) {
            foreach ($this->transactions as $key => $value) {
                $this->transactions[$key] = (bool) $storeTransaction->$key;
            }
        } else {
            foreach ($this->transactions as $key => $value) {
                $this->transactions[$key] = false;
            }
        }
    }

    public function updatedSelectAll($value)
    {
        foreach ($this->transactions as $key => $val) {
            $this->transactions[$key] = $value;
        }
    }

    public function save()
    {
        if (!$this->storeId) {
            $this->addError('storeId', __('supply_chain_trans.no_store_selected'));
            return;
        }

        $storeTransaction = StoreTransaction::firstOrNew(['store_id' => $this->storeId]);

        foreach ($this->transactions as $key => $value) {
            $storeTransaction->$key = $value;
        }

        $storeTransaction->save();

        // عرض الرسالة باستخدام toast عوضًا عن session flash
        $this->dispatchBrowserEvent('toast', [
            'message' => __('supply_chain_trans.settings_saved_successfully'),
            'type'    => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.supply-chain.settings.store-main-data.store-transactions');
    }

    public function getArabicLabel($key)
    {
        return __('supply_chain_trans.' . $key);
    }
}
