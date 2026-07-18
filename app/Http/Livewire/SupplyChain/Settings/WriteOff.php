<?php

namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;
use App\Models\supply_chain\writeoffmodel;

class WriteOff extends Component
{
    public $name_en;
    public $name_ar;
    public $status;
    public $writeOffId;
    public $isEditMode = false;

    // قواعد التحقق: الحقول مطلوبة، والحالة يجب أن تكون active أو inactive
    protected $rules = [
        'name_en' => 'required|string',
        'name_ar' => 'required|string',
        'status'  => 'required|in:active,inactive',
    ];

    // إعادة تعيين النموذج
    public function resetForm()
    {
        $this->reset(['name_en', 'name_ar', 'status', 'writeOffId', 'isEditMode']);
    }

    // دالة لحفظ WriteOff جديدة أو تحديث موجودة
    public function saveWriteOff()
    {
        $this->validate();

        if ($this->isEditMode && $this->writeOffId) {
            $writeOff = writeoffmodel::find($this->writeOffId);
            if ($writeOff) {
                $writeOff->update([
                    'name_en' => $this->name_en,
                    'name_ar' => $this->name_ar,
                    'status'  => $this->status,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            writeoffmodel::create([
                'name_en' => $this->name_en,
                'name_ar' => $this->name_ar,
                'status'  => $this->status,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    // دالة لتحميل بيانات WriteOff من أجل التعديل
    public function editWriteOff($id)
    {
        $writeOff = writeoffmodel::find($id);
        if ($writeOff) {
            $this->writeOffId = $writeOff->id;
            $this->name_en   = $writeOff->name_en;
            $this->name_ar   = $writeOff->name_ar;
            $this->status    = $writeOff->status;
            $this->isEditMode = true;
        }
    }

    // دالة لحذف WriteOff
    public function deleteWriteOff($id)
    {
        $writeOff = writeoffmodel::find($id);
        if ($writeOff) {
            $writeOff->delete();
            session()->flash('success', __('general.deleted_successfully'));
        }
    }

    public function render()
    {
        $writeOffs = writeoffmodel::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.write-off', compact('writeOffs'));
    }
}
