<?php

namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;
use App\Models\supply_chain\supplychaingeneralsetting;

class GeneralSettings extends Component
{
    public $settings;
    public $auto_substitution = false;
    public $selectedDays = []; // لتخزين الأيام المختارة كمصفوفة

    protected function rules()
    {
        return [
            'settings.cost_policy'            => 'required|in:last_purchase,primary_purchase',
            'settings.sale_policy'            => 'required|in:last_sale,primary_sale',
            'settings.item_coding_method'     => 'required|in:groups,system_serial,manual',
            'settings.item_code_length'       => 'required|in:3,4,5',
            'settings.gift_cost'              => 'required|in:actual,no_cost',
            'settings.barcode_labels'         => 'required|in:item_code,balance_serial',
            'settings.store_user_link'        => 'required|in:yes,no',
            'settings.general_serial_method'  => 'required|in:automatic,manual',
            'settings.financial_year_code'    => 'required|integer',
            'settings.dont_update_sale_price' => 'nullable|boolean',
            'settings.expenditure_days'       => 'nullable|array',
            'settings.expenditure_days.*'     => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
        ];
    }

    public function mount()
    {
        $this->settings = supplychaingeneralsetting::first();

        if (!$this->settings) {
            $this->settings = new supplychaingeneralsetting([
                'auto_substitution'       => false,
                'dont_update_sale_price'  => false,
                // نخزن الأيام كمصفوفة فارغة
                'expenditure_days'        => [],
            ]);
        }

        // تعيين auto_substitution بناءً على الإعداد المحفوظ
        $this->auto_substitution = (bool)$this->settings->auto_substitution;

        // إذا كانت auto_substitution مفعل، نسترجع الأيام، وإلا نترك selectedDays فارغًا
        $this->selectedDays = $this->auto_substitution
            ? ($this->settings->expenditure_days ?: [])
            : [];
    }

    // عند تغيير قيمة auto_substitution من الواجهة
    public function updatedAutoSubstitution($value)
    {
        if (!$value) {
            // إذا تم إلغاء تفعيل auto_substitution يتم تفريغ الأيام
            $this->selectedDays = [];
        }
    }

    public function saveSettings()
    {
        // تحقق مبدئي: إذا كان auto_substitution مفعل ولكن selectedDays فارغة، أضف خطأ وأوقف العملية
        if ($this->auto_substitution && count($this->selectedDays) === 0) {
            $this->addError('selectedDays', __('supply_chain_trans.select_at_least_one_day'));
            return;
        }

        // تحديث إعداد auto_substitution
        $this->settings->auto_substitution = $this->auto_substitution;

        // إذا كان auto_substitution مفعل يتم تخزين الأيام المختارة، وإلا نخزن مصفوفة فارغة
        $this->settings->expenditure_days = $this->auto_substitution
            ? $this->selectedDays
            : [];

        // التحقق من صحة باقي البيانات
        $this->validate();

        // حفظ الإعدادات
        $this->settings->save();

        session()->flash('success', __('supply_chain_trans.settings_saved'));
    }

    public function render()
    {
        return view('livewire.supply-chain.settings.general-settings');
    }
}
