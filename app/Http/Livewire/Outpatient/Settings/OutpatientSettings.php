<?php

namespace App\Http\Livewire\Outpatient\Settings;
use Livewire\Component;
use App\Models\finance\glaccount;
use App\Models\administrative\outpatientsetting;

class OutpatientSettings extends Component
{
    public $gl_account_id;
    public $glAccounts;
    public $setting;

    public function mount()
    {
        // جلب كل الحسابات للعرض في القائمة
        $this->glAccounts = glaccount::all();

        // جلب الإعداد الحالي (أول سجل)
        $this->setting = outpatientsetting::first();

        if ($this->setting) {
            $this->gl_account_id = $this->setting->gl_account_id;
        }
    }

    public function save()
    {
        $this->validate([
            'gl_account_id' => 'required|exists:fin_s_gl_accounts,id',
        ]);

        if ($this->setting) {
            $this->setting->update([
                'gl_account_id' => $this->gl_account_id,
            ]);
        } else {
            $this->setting = outpatientsetting::create([
                'gl_account_id' => $this->gl_account_id,
            ]);
        }

        session()->flash('success', 'تم حفظ إعدادات العيادات الخارجية بنجاح');
    }

    public function render()
    {
        return view('livewire.Outpatient.Settings.outpatient-settings');
    }
}