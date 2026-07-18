<?php

namespace App\Http\Livewire\SupplyChain\Settings;
use Livewire\Component;
use App\Models\supply_chain\storageconditionmodel;

class StorageConditions extends Component
{
    public $name_en;
    public $name_ar;
    public $status;
    public $storageConditionId;
    public $isEditMode = false;

    // قواعد التحقق: جميع الحقول مطلوبة والحالة active أو inactive
    protected $rules = [
        'name_en' => 'required|string',
        'name_ar' => 'required|string',
        'status'  => 'required|in:active,inactive',
    ];

    // إعادة تعيين النموذج
    public function resetForm()
    {
        $this->reset(['name_en', 'name_ar', 'status', 'storageConditionId', 'isEditMode']);
    }

    // دالة لحفظ StorageCondition جديدة أو تحديث موجودة
    public function saveStorageCondition()
    {
        $this->validate();

        if ($this->isEditMode && $this->storageConditionId) {
            $storage = storageconditionmodel::find($this->storageConditionId);
            if ($storage) {
                $storage->update([
                    'name_en' => $this->name_en,
                    'name_ar' => $this->name_ar,
                    'status'  => $this->status,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            storageconditionmodel::create([
                'name_en' => $this->name_en,
                'name_ar' => $this->name_ar,
                'status'  => $this->status,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    // دالة لتحميل بيانات StorageCondition من أجل التعديل
    public function editStorageCondition($id)
    {
        $storage = storageconditionmodel::find($id);
        if ($storage) {
            $this->storageConditionId = $storage->id;
            $this->name_en             = $storage->name_en;
            $this->name_ar             = $storage->name_ar;
            $this->status              = $storage->status;
            $this->isEditMode          = true;
        }
    }

    // دالة لحذف StorageCondition
    public function deleteStorageCondition($id)
    {
        $storage = storageconditionmodel::find($id);
        if ($storage) {
            $storage->delete();
            session()->flash('success', __('general.deleted_successfully'));
        }
    }

    public function render()
    {
        $storageConditions = storageconditionmodel::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.storage-conditions', compact('storageConditions'));
    }
}
