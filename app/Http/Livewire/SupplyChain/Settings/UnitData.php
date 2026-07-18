<?php

namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;
use App\Models\supply_chain\unit;

class UnitData extends Component
{
    public $name_en;
    public $name_ar;
    public $unit_type;
    public $unitId;
    public $isEditMode = false;

    protected $listeners = ['deleteUnit']; // هذا هو المفتاح لتفعيل زر الحذف

    protected $rules = [
        'name_en'   => 'required|string',
        'name_ar'   => 'required|string',
        'unit_type' => 'required|in:small,medium,large',
    ];

    public function resetForm()
    {
        $this->reset(['name_en', 'name_ar', 'unit_type', 'unitId', 'isEditMode']);
    }

    public function saveUnit()
    {
        $this->validate();

        if ($this->isEditMode && $this->unitId) {
            $unit = Unit::find($this->unitId);
            if ($unit) {
                $unit->update([
                    'name_en'   => $this->name_en,
                    'name_ar'   => $this->name_ar,
                    'unit_type' => $this->unit_type,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            Unit::create([
                'name_en'   => $this->name_en,
                'name_ar'   => $this->name_ar,
                'unit_type' => $this->unit_type,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    public function editUnit($id)
    {
        $unit = Unit::find($id);
        if ($unit) {
            $this->unitId     = $unit->id;
            $this->name_en    = $unit->name_en;
            $this->name_ar    = $unit->name_ar;
            $this->unit_type  = $unit->unit_type;
            $this->isEditMode = true;
        }
    }

    public function deleteUnit($id)
{
    $unit = Unit::find($id);

    if (!$unit) return;

    // التحقق من وجود ارتباطات
    $isUsed = $unit->productUnitsLarge()->exists() || $unit->productUnitsSmall()->exists();

    if ($isUsed) {
        // ✅ إغلاق المودال عند الفشل أيضًا
        $this->dispatchBrowserEvent('hide-delete-modal');

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'error',
            'message' => trans('supply_chain_trans.unit_used_in_other_records'),
        ]);
        return;
    }

    $unit->delete();

    // ✅ إغلاق المودال عند النجاح
    $this->dispatchBrowserEvent('hide-delete-modal');

    $this->dispatchBrowserEvent('show-toast', [
        'type' => 'success',
        'message' => trans('general.deleted_successfully'),
    ]);
}

    public function render()
    {
        $units = Unit::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.unit-data', compact('units'));
    }
}
