<?php

namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;
use App\Models\supply_chain\financialyearmodel;

class FinancialYear extends Component
{
    public $year_code;
    public $periods_count;
    public $start_date;
    public $end_date;
    public $financialYearId;
    public $isEditMode = false;

    protected $rules = [
        'year_code'     => 'required|string',
        'periods_count' => 'required|integer|min:1',
        'start_date'    => 'required|date',
        'end_date'      => 'required|date|after_or_equal:start_date',
    ];

    // إعادة تعيين النموذج
    public function resetForm()
    {
        $this->reset(['year_code', 'periods_count', 'start_date', 'end_date', 'financialYearId', 'isEditMode']);
    }

    // دالة لحفظ سنة مالية جديدة أو تحديث موجودة
    public function saveFinancialYear()
    {
        $this->validate();

        if ($this->isEditMode && $this->financialYearId) {
            $year = financialyearmodel::find($this->financialYearId);
            if ($year) {
                $year->update([
                    'year_code'     => $this->year_code,
                    'periods_count' => $this->periods_count,
                    'start_date'    => $this->start_date,
                    'end_date'      => $this->end_date,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            financialyearmodel::create([
                'year_code'     => $this->year_code,
                'periods_count' => $this->periods_count,
                'start_date'    => $this->start_date,
                'end_date'      => $this->end_date,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    // دالة لتحميل بيانات السنة المالية من أجل التعديل
    public function editFinancialYear($id)
    {
        $year = financialyearmodel::find($id);
        if ($year) {
            $this->financialYearId = $year->id;
            $this->year_code     = $year->year_code;
            $this->periods_count = $year->periods_count;
            $this->start_date    = $year->start_date;
            $this->end_date      = $year->end_date;
            $this->isEditMode    = true;
        }
    }

    // دالة لحذف السنة المالية
    public function deleteFinancialYear($id)
    {
        $year = financialyearmodel::find($id);
        if ($year) {
            $year->delete();
            session()->flash('success', __('general.deleted_successfully'));
        }
    }

    public function render()
    {
        $financialYears = financialyearmodel::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.financial-year', compact('financialYears'));
    }
}
