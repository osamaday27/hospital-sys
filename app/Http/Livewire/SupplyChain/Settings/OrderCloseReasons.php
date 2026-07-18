<?php




namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;
use App\Models\supply_chain\orderclosereasonsmodel;

class orderclosereasons extends Component
{
    public $name_ar;
    public $name_en;
    public $status;
    public $orderCloseReasonId;
    public $isEditMode = false;

    // قواعد التحقق: جميع الحقول مطلوبة والحالة يجب أن تكون active أو inactive
    protected $rules = [
        'name_ar' => 'required|string',
        'name_en' => 'required|string',
        'status'  => 'required|in:active,inactive',
    ];

    // إعادة تعيين النموذج
    public function resetForm()
    {
        $this->reset(['name_ar', 'name_en', 'status', 'orderCloseReasonId', 'isEditMode']);
    }

    // دالة لحفظ سبب إغلاق طلب جديد أو تحديث موجود
    public function saveOrderCloseReason()
    {
        $this->validate();

        if ($this->isEditMode && $this->orderCloseReasonId) {
            $reason = orderclosereasonsmodel::find($this->orderCloseReasonId);
            if ($reason) {
                $reason->update([
                    'name_ar' => $this->name_ar,
                    'name_en' => $this->name_en,
                    'status'  => $this->status,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            orderclosereasonsmodel::create([
                'name_ar' => $this->name_ar,
                'name_en' => $this->name_en,
                'status'  => $this->status,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    // دالة لتحميل بيانات سبب إغلاق الطلب من أجل التعديل
    public function editOrderCloseReason($id)
    {
        $reason = orderclosereasonsmodel::find($id);
        if ($reason) {
            $this->orderCloseReasonId = $reason->id;
            $this->name_ar            = $reason->name_ar;
            $this->name_en            = $reason->name_en;
            $this->status             = $reason->status;
            $this->isEditMode         = true;
        }
    }

    // دالة لحذف سبب إغلاق الطلب
    public function deleteOrderCloseReason($id)
    {
        $reason = orderclosereasonsmodel::find($id);
        if ($reason) {
            $reason->delete();
            session()->flash('success', __('general.deleted_successfully'));
        }
    }

    public function render()
    {
        $orderCloseReasons = orderclosereasonsmodel::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.orderclosereasons', compact('orderCloseReasons'));
    }
}
