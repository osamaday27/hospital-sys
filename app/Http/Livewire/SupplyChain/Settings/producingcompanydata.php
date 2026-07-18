<?php

namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;
use App\Models\supply_chain\producingcompany;

class producingcompanydata extends Component
{
    public $name_en;
    public $name_ar;
    public $address;
    public $notes;
    public $companyId;
    public $isEditMode = false;

    // قواعد التحقق: جميع الحقول مطلوبة ما عدا الملاحظات
    protected $rules = [
        'name_en' => 'required|string',
        'name_ar' => 'required|string',
        'address' => 'required|string',
        'notes'   => 'nullable|string',
    ];

    // إعادة تعيين النموذج
    public function resetForm()
    {
        $this->reset(['name_en', 'name_ar', 'address', 'notes', 'companyId', 'isEditMode']);
    }

    // دالة لحفظ شركة جديدة أو تحديث شركة موجودة
    public function saveCompany()
    {
        $this->validate();

        if ($this->isEditMode && $this->companyId) {
            $company = producingcompany::find($this->companyId);
            if ($company) {
                $company->update([
                    'name_en' => $this->name_en,
                    'name_ar' => $this->name_ar,
                    'address' => $this->address,
                    'notes'   => $this->notes,
                ]);
                session()->flash('success', __('supply_chain_trans.updated_successfully'));
            }
        } else {
            producingcompany::create([
                'name_en' => $this->name_en,
                'name_ar' => $this->name_ar,
                'address' => $this->address,
                'notes'   => $this->notes,
            ]);
            session()->flash('success', __('supply_chain_trans.created_successfully'));
        }

        $this->resetForm();
    }

    // دالة لتحميل بيانات الشركة من أجل التعديل
    public function editCompany($id)
    {
        $company = ProducingCompany::find($id);
        if ($company) {
            $this->companyId = $company->id;
            $this->name_en   = $company->name_en;
            $this->name_ar   = $company->name_ar;
            $this->address   = $company->address;
            $this->notes     = $company->notes;
            $this->isEditMode = true;
        }
    }

    // دالة لحذف الشركة
    public function deleteCompany($id)
    {
        $company = producingcompany::find($id);
        if ($company) {
            $company->delete();
            session()->flash('success', __('general.deleted_successfully'));
        }
    }

    public function render()
    {
        $companies = producingcompany::orderBy('created_at', 'desc')->get();
        return view('livewire.supply-chain.settings.producingcompanydata', compact('companies'));
    }
}
