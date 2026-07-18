<?php

namespace App\Http\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\finance\contracts_details_last_level;
use App\Models\Finance\service;

class ContractDetailsForm extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $contract; // سجل تفاصيل العقد (إن وجد)
    public $last_level_id, $status, $user_id;

    public $discount_percentage_all_services, $patient_contribution_all_services;
    public $discount_percentage_clinic_services, $patient_contribution_clinic_services;
    public $discount_percentage_inpatient_services, $patient_contribution_inpatient_services;
    public $discount_percentage_surgical_services, $patient_contribution_surgical_services;
    public $discount_percentage_lab_services, $patient_contribution_lab_services;
    public $discount_percentage_emergency_services, $patient_contribution_emergency_services;
    public $discount_percentage_rad_services, $patient_contribution_rad_services;
    public $discount_percentage_medications, $patient_contribution_medications;
    public $discount_percentage_supplies, $patient_contribution_supplies;

    public $services_require_approval_ids = [];
    public $services_not_covered_ids = [];

    /**
     * يدعم 3 سيناريوهات:
     * 1) mount($id) حيث $id هو ID لسجل تفاصيل موجود => تعديل
     * 2) mount($id) حيث $id هو last_level_id (ومافيش سجل تفاصيل) => إنشاء جديد مرتبط بهذا العقد
     * 3) mount($id, $last_level_id) لو الراوت يمرر باراميترين
     */
    public function mount($id = null, $last_level_id = null)
    {
        $record = null;

        if ($id) {
            $record = contracts_details_last_level::find($id);
        }

        if ($record) {
            // وضع التعديل
            $this->contract = $record;
            $this->fill($record->toArray());

            $this->services_require_approval_ids = $record->services_require_approval
                ? $this->parseIds($record->services_require_approval)
                : [];

            $this->services_not_covered_ids = $record->services_not_covered
                ? $this->parseIds($record->services_not_covered)
                : [];
        } else {
            // وضع الإنشاء
            // أولوية لتعيين last_level_id من البراميتر الصريح إن وُجد
            if ($last_level_id) {
                $this->last_level_id = $last_level_id;
            }
            // لو اتبعت قيمة واحدة فقط في $id وكانت تخص last_level_id (مش لاقيين سجل بالتفاصيل)
            if (!$this->last_level_id && $id) {
                $this->last_level_id = $id;
            }
        }

        // تعيين المستخدم الحالي تلقائيًا لو مش متحدد
        if (!$this->user_id) {
            $this->user_id = Auth::id();
        }

        // حالة افتراضية عند الإنشاء
        if (!$this->status) {
            $this->status = 'active';
        }
    }

    public function rules()
    {
        return [
            'last_level_id' => 'required|exists:fin_contract_last_level,id',
            'status' => 'nullable|in:active,inactive',
            'user_id' => 'nullable|exists:users,id',

            'discount_percentage_all_services' => 'nullable|numeric',
            'patient_contribution_all_services' => 'nullable|numeric',

            'discount_percentage_clinic_services' => 'nullable|numeric',
            'patient_contribution_clinic_services' => 'nullable|numeric',

            'discount_percentage_inpatient_services' => 'nullable|numeric',
            'patient_contribution_inpatient_services' => 'nullable|numeric',

            'discount_percentage_surgical_services' => 'nullable|numeric',
            'patient_contribution_surgical_services' => 'nullable|numeric',

            'discount_percentage_lab_services' => 'nullable|numeric',
            'patient_contribution_lab_services' => 'nullable|numeric',

            'discount_percentage_emergency_services' => 'nullable|numeric',
            'patient_contribution_emergency_services' => 'nullable|numeric',

            'discount_percentage_rad_services' => 'nullable|numeric',
            'patient_contribution_rad_services' => 'nullable|numeric',

            'discount_percentage_medications' => 'nullable|numeric',
            'patient_contribution_medications' => 'nullable|numeric',

            'discount_percentage_supplies' => 'nullable|numeric',
            'patient_contribution_supplies' => 'nullable|numeric',
        ];
    }

    public function save()
    {
        // ضمان الربط بالعقد قبل الفاليديشن/الحفظ
        if (!$this->last_level_id) {
            // في حالة نادرة لو المستخدم مسح القيمة من الواجهة
            $this->addError('last_level_id', trans('validation.required', ['attribute' => 'last_level_id']));
            return;
        }

        // تعيين user_id الحالي لو مش مبعوت
        if (!$this->user_id) {
            $this->user_id = Auth::id();
        }

        $validated = $this->validate();

        // فرض last_level_id (حماية من أي تلاعب في الواجهة)
        $validated['last_level_id'] = $this->last_level_id;

        // تحويل المصفوفات إلى نص مفصول بفواصل
        $validated['services_require_approval'] = implode(',', array_filter($this->services_require_approval_ids ?? []));
        $validated['services_not_covered']     = implode(',', array_filter($this->services_not_covered_ids ?? []));

        if ($this->contract) {
            // تحديث
            $this->contract->update($validated);

            $this->dispatchBrowserEvent('show-success', [
                'message' => trans('contract_settings.updated_successfully'),
            ]);
        } else {
            // إنشاء
            $validated['status'] = $validated['status'] ?? 'active';
            $validated['user_id'] = $validated['user_id'] ?? Auth::id();

            $this->contract = contracts_details_last_level::create($validated);

            $this->dispatchBrowserEvent('show-success', [
                'message' => trans('contract_settings.created_successfully'),
            ]);
        }
    }

    private function parseIds($string)
    {
        return $string
            ? array_values(array_filter(array_map('trim', explode(',', $string)), fn ($v) => $v !== ''))
            : [];
    }

    public function render()
    {
        $all_services = service::select('id', 'name')->paginate(20);
        return view('livewire.finance.contract-details-form', compact('all_services'));
    }
}