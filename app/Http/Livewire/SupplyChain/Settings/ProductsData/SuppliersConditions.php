<?php

namespace App\Http\Livewire\SupplyChain\Settings\ProductsData;

use Livewire\Component;
use App\Models\supply_chain\productsupplierstorage as ProductSupplierStorage;
use App\Models\suppliers\SuppliersData;
use App\Models\supply_chain\StorageConditionModel;

class SuppliersConditions extends Component
{
    public $productId;
    public $allSuppliers;
    public $allConditions;

    public $selectedSupplierIds         = [];
    public $selectedStorageConditionIds = [];
    public $records                     = [];
    public $editId                      = null;
    public $deleteId                    = null;

    protected $listeners = [
        // يلتقط حدث حفظ المنتج ليعرض هذه الصفحة تلقائيًا ويحمّل البيانات
        'productSaved'    => 'setProductId',
        'deleteConfirmed' => 'deleteConfirmed',
    ];

    public function mount($productId = null)
    {
        $this->productId = $productId;
        $this->initData();
    }

    /**
     * عندما ينشئ أو يعدل المستخدم المنتج في الـ BasicData،
     * نبعث حدثًا باسم 'productSaved' مع الـ ID.
     * هنا نلتقطه لنُحدّث الـ productId ونحمّل البيانات.
     */
    public function setProductId($id)
    {
        $this->productId = $id;
        $this->initData();

        // انتقل تلقائيًا إلى تبويب الموردين وظروف التخزين (tab3)
        $this->dispatchBrowserEvent('goToTab', 'tab3');
    }

    /**
     * تحميل الموردين، ظروف التخزين، والسجلات
     */
    private function initData()
    {
        $this->allSuppliers  = SuppliersData::where('status', 'active')->get();
        $this->allConditions = StorageConditionModel::where('status', 'active')->get();
        $this->loadRecords();
    }

    private function loadRecords()
    {
        if ($this->productId) {
            $this->records = ProductSupplierStorage::where('product_id', $this->productId)
                                ->orderByDesc('id')
                                ->get();
        } else {
            $this->records = collect();
        }
    }

    public function edit($id)
    {
        $rec = ProductSupplierStorage::find($id);
        if (! $rec) return;

        $this->editId                      = $rec->id;
        $this->selectedSupplierIds         = $rec->supplier_ids;
        $this->selectedStorageConditionIds = $rec->storage_condition_ids;

        $this->dispatchBrowserEvent('reset-select2');
    }

    public function addEntry()
    {
        $this->validate([
            'selectedSupplierIds'           => 'array',
            'selectedSupplierIds.*'         => 'exists:suppliers_data,id',
            'selectedStorageConditionIds'   => 'array',
            'selectedStorageConditionIds.*' => 'exists:storage_conditions,id',
        ]);

        $data = [
            'supplier_ids'          => $this->selectedSupplierIds,
            'storage_condition_ids' => $this->selectedStorageConditionIds,
        ];

        if ($this->editId) {
            $rec = ProductSupplierStorage::find($this->editId);
            $rec->update($data);
            $msg = trans('general.updated_successfully');
        } else {
            ProductSupplierStorage::updateOrCreate(
                ['product_id' => $this->productId],
                array_merge($data, ['product_id' => $this->productId])
            );
            $msg = trans('general.saved_successfully');
        }

        $this->dispatchBrowserEvent('show-toast', [
            'message' => $msg,
            'type'    => 'success',
        ]);

        $this->editId                      = null;
        $this->selectedSupplierIds         = [];
        $this->selectedStorageConditionIds = [];
        $this->dispatchBrowserEvent('reset-select2');
        $this->loadRecords();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function deleteConfirmed()
    {
        if ($this->deleteId) {
            ProductSupplierStorage::find($this->deleteId)?->delete();
        }
        $this->dispatchBrowserEvent('close-delete-modal');
        $this->loadRecords();
    }

    public function render()
    {
        return view('livewire.supply-chain.settings.products-data.suppliers-conditions');
    }
}
