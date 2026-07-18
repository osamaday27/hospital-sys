<?php

namespace App\Http\Livewire\SupplyChain\Settings;




use Livewire\Component;
use App\Models\supply_chain\MovementType;

class MovementTypes extends Component
{
    public $impact_balance;
    public $movement_type;
    public $unit;
    public $movementId;
    public $isEditMode = false;

    // التحقق من القيم: التأثير علي الرصيد والنوع والوحدة يجب أن تكون من القيم المحددة
    protected $rules = [
        'impact_balance' => 'required|in:increase,decrease',
        'movement_type'  => 'required|in:expense,return',
        'unit'           => 'required|in:small,medium,large',
    ];


    // إعادة تعيين النموذج
    public function resetForm()
    {
        $this->reset(['impact_balance', 'movement_type', 'unit', 'movementId', 'isEditMode']);
    }

    // دالة لحفظ حركة مخزنية جديدة أو تحديث حركة موجودة
    public function saveMovementType()
    {
        $this->validate();

        if ($this->isEditMode && $this->movementId) {
            $movement = MovementType::find($this->movementId);
            if ($movement) {
                $movement->update([
                    'impact_balance' => $this->impact_balance,
                    'movement_type'  => $this->movement_type,
                    'unit'           => $this->unit,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            MovementType::create([
                'impact_balance' => $this->impact_balance,
                'movement_type'  => $this->movement_type,
                'unit'           => $this->unit,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    // دالة لتحميل بيانات الحركة من أجل التعديل
    public function editMovementType($id)
    {
        $movement = MovementType::find($id);
        if ($movement) {
            $this->movementId    = $movement->id;
            $this->impact_balance = $movement->impact_balance;
            $this->movement_type  = $movement->movement_type;
            $this->unit           = $movement->unit;
            $this->isEditMode     = true;
        }
    }

    // دالة لحذف الحركة
    public function deleteMovementType($id)
    {
        $movement = MovementType::find($id);
        if ($movement) {
            $movement->delete();
            session()->flash('success', __('general.deleted_successfully'));
        }
    }

    public function render()
    {
        $movements = MovementType::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.movement-types', compact('movements'));
    }
}
