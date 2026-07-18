<?php

namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;
use App\Models\supply_chain\financialperiodmodel;

class FinancialPeriod extends Component
{
    public $name_ar;
    public $name_en;
    public $start_date;
    public $end_date;
    public $financialPeriodId;
    public $isEditMode = false;

    // قواعد التحقق: الاسمين مطلوبان، وتواريخ البداية والنهاية مطلوبتان مع شرط أن يكون تاريخ النهاية مساويًا أو بعد تاريخ البداية
    protected $rules = [
        'name_ar'    => 'required|string',
        'name_en'    => 'required|string',
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
    ];

    // إعادة تعيين النموذج
    public function resetForm()
    {
        $this->reset(['name_ar', 'name_en', 'start_date', 'end_date', 'financialPeriodId', 'isEditMode']);
    }

    // دالة لحفظ فترة مالية جديدة أو تحديث الموجودة
    public function saveFinancialPeriod()
    {
        $this->validate();

        if ($this->isEditMode && $this->financialPeriodId) {
            $period = financialperiodmodel::find($this->financialPeriodId);
            if ($period) {
                $period->update([
                    'name_ar'    => $this->name_ar,
                    'name_en'    => $this->name_en,
                    'start_date' => $this->start_date,
                    'end_date'   => $this->end_date,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            financialperiodmodel::create([
                'name_ar'    => $this->name_ar,
                'name_en'    => $this->name_en,
                'start_date' => $this->start_date,
                'end_date'   => $this->end_date,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    // دالة لتحميل بيانات الفترة المالية من أجل التعديل
    public function editFinancialPeriod($id)
    {
        $period = financialperiodmodel::find($id);
        if ($period) {
            $this->financialPeriodId = $period->id;
            $this->name_ar    = $period->name_ar;
            $this->name_en    = $period->name_en;
            $this->start_date = $period->start_date;
            $this->end_date   = $period->end_date;
            $this->isEditMode = true;
        }
    }

    // دالة لحذف الفترة المالية
    public function deleteFinancialPeriod($id)
    {
        $period = financialperiodmodel::find($id);
        if ($period) {
            $period->delete();
            session()->flash('success', __('general.deleted_successfully'));
        }
    }

    public function render()
    {
        $financialPeriods = financialperiodmodel::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.financial-period', compact('financialPeriods'));
    }
}
