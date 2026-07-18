<?php





namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;
use App\Models\supply_chain\returnreasonmodel;

class ReturnReasons extends Component
{
    public $name_en;
    public $name_ar;
    public $status;
    public $returnReasonId;
    public $isEditMode = false;

    // قواعد التحقق: الحقول مطلوبة والحالة يجب أن تكون active أو inactive
    protected $rules = [
        'name_en' => 'required|string',
        'name_ar' => 'required|string',
        'status'  => 'required|in:active,inactive',
    ];

    // إعادة تعيين النموذج
    public function resetForm()
    {
        $this->reset(['name_en', 'name_ar', 'status', 'returnReasonId', 'isEditMode']);
    }

    // دالة لحفظ سبب إرجاع جديد أو تحديث موجود
    public function saveReturnReason()
    {
        $this->validate();

        if ($this->isEditMode && $this->returnReasonId) {
            $reason = returnreasonmodel::find($this->returnReasonId);
            if ($reason) {
                $reason->update([
                    'name_en' => $this->name_en,
                    'name_ar' => $this->name_ar,
                    'status'  => $this->status,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            returnreasonmodel::create([
                'name_en' => $this->name_en,
                'name_ar' => $this->name_ar,
                'status'  => $this->status,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    // دالة لتحميل بيانات سبب الإرجاع من أجل التعديل
    public function editReturnReason($id)
    {
        $reason = returnreasonmodel::find($id);
        if ($reason) {
            $this->returnReasonId = $reason->id;
            $this->name_en       = $reason->name_en;
            $this->name_ar       = $reason->name_ar;
            $this->status        = $reason->status;
            $this->isEditMode    = true;
        }
    }

    // دالة لحذف سبب الإرجاع
    public function deleteReturnReason($id)
    {
        $reason = returnreasonmodel::find($id);
        if ($reason) {
            $reason->delete();
            session()->flash('success', __('general.deleted_successfully'));
        }
    }

    public function render()
    {
        $returnReasons = returnreasonmodel::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.return-reasons', compact('returnReasons'));
    }
}
