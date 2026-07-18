<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Finance\Invoices;
use App\Models\Finance\Service;
use App\Models\Administrative\SurgicalServiceVisit;
use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Finance\Receipts;
use App\models\administrative\medical_record;

// NEW: الاعتماد على ترايت التسعير من العقد
use App\Traits\Web\get_price_service;

class SurgicalServiceForm extends Component
{
    use get_price_service; // تفعيل دوال التسعير من العقد

    public $surgery_booking_id, $visit_id, $patient_id, $contract_id;
    public $service_id, $quantity = 1;

    public $services = [];       // خدمات جديدة (لم تحفظ بعد)
    public $saved_services = []; // خدمات محفوظة من قبل

    public $total = 0, $contract_value = 0, $patient_value = 0;
    public $discount = 0, $paid = 0, $unpaid = 0;

    public function mount($surgery_booking_id)
    {
        $this->surgery_booking_id = $surgery_booking_id;

        $booking = adm_b_surgery_bookings::findOrFail($surgery_booking_id);
        $this->visit_id   = $booking->visit_id;
        $this->patient_id = $booking->patient_id;
        $patient = medical_record::findOrFail($booking->patient_id);


        // محاولة تحديد العقد المرتبط بالحجز (لو العمود موجود)
        // في حال عدم وجود العمود/القيمة سيظل null وسيتم fallback لسعر الخدمة مباشرة
        if (isset($patient->insurance_contracting)) {
            $this->contract_id = $patient->insurance_contracting;
        }

        $this->loadSavedServices();
        $this->calculate();
    }

    protected function loadSavedServices()
    {
        // نحاول نجيب الفاتورة الخاصة بحجز العملية (السجل الأحدث لو فيه أكتر من واحدة)
        $invoice = Invoices::where('reservation_id', $this->surgery_booking_id)->latest('id')->first();

        $query = SurgicalServiceVisit::with('service');

        // لو فيه فاتورة، اعرض الصفوف المرتبطة بيها
        if ($invoice) {
            $query->where('invoice_id', $invoice->id);
        } else {
            // احتياطي: بنفس الزيارة والمريض
            $query->where('visit_id', $this->visit_id)->where('patient_id', $this->patient_id);
        }

        $this->saved_services = $query
            ->get()
            ->map(function ($item) {
                return [
                    'row_id'         => $item->id,
                    'service_id'     => $item->service_id,
                    'name'           => $item->service->name ?? '',
                    'quantity'       => (int) $item->quantity,
                    'total'          => (float) $item->total,
                    'patient_value'  => (float) $item->patient_value,
                    'contract_value' => (float) $item->contract_value,
                    'discount'       => (float) $item->discount,
                    'service_type'   => $item->service_type ?? null,
                    'notes'          => $item->notes ?? null,
                    'is_saved'       => true,
                ];
            })
            ->toArray();
    }
public function addService()
{
    if (!$this->service_id) {
        return;
    }

    $service = Service::findOrFail($this->service_id);

    // السعر الأساسي الافتراضي
    $basePrice = (float) ($service->price ?? 0);
    $calc_total = $basePrice;
    $calc_patient = $basePrice;
    $calc_contract = 0.0;

    // ✅ تطبيق العقد لو موجود
    if (!empty($this->contract_id)) {
        $pricing = $this->getPrice($this->contract_id, $service->id);

        if (is_array($pricing) && isset($pricing['original_price'], $pricing['amount_to_pay'])) {
            $original_price = (float) $pricing['original_price'];
            $amount_to_pay  = (float) $pricing['amount_to_pay'];

            $calc_total    = $original_price;
            $calc_patient  = $amount_to_pay;
            $calc_contract = max(0, $calc_total - $calc_patient);
        }
    }

    // ✅ الحساب بالكمية
    $qty = (int) $this->quantity;

    $this->services[] = [
        'service_id'     => $service->id,
        'name'           => $service->name,
        'total'          => $calc_total * $qty,        // إجمالي الخدمة × الكمية
        'patient_value'  => $calc_patient * $qty,      // نصيب المريض × الكمية
        'contract_value' => $calc_contract * $qty,     // نصيب الجهة × الكمية
        'discount'       => 0.0,
        'quantity'       => $qty,
        'is_saved'       => false,
    ];

    // تفريغ فورم الإضافة
    $this->reset(['service_id', 'quantity']);
    $this->quantity = 1;

    $this->calculate();
}

    public function removeService($index)
    {
        // إزالة من قائمة «الجديد فقط»
        if (isset($this->services[$index])) {
            unset($this->services[$index]);
            $this->services = array_values($this->services);
            $this->calculate();
        }
    }

    // اختيارية: حذف خدمة محفوظة (لو احتجتها لاحقًا)
    public function deleteSavedService($rowId)
    {
        $row = SurgicalServiceVisit::find($rowId);
        if ($row) {
            $row->delete();
            $this->loadSavedServices();
            $this->calculate();
            session()->flash('success', __('تم حذف الخدمة المحفوظة بنجاح'));
        }
    }

    public function updatedDiscount()
    {
        $this->calculate();
    }

    public function updatedPaid()
    {
        $this->calculate();
    }

    public function calculate()
    {
        // نجمع للعرض فقط: المحفوظ + الجديد
        $this->total = 0;
        $this->patient_value = 0;
        $this->contract_value = 0;

        $rows = array_merge($this->saved_services, $this->services);

        foreach ($rows as $row) {
            $qty                 = (int) ($row['quantity'] ?? 1);
            $row_total           = (float) ($row['total'] ?? 0);
            $row_patient_value   = (float) ($row['patient_value'] ?? 0);
            $row_contract_value  = (float) ($row['contract_value'] ?? 0);

            $this->total          += $row_total * $qty;
            $this->patient_value  += $row_patient_value * $qty;
            $this->contract_value += $row_contract_value * $qty;
        }

        $this->unpaid = max(0, $this->patient_value - (float) $this->discount - (float) $this->paid);
    }

    public function save()
    {
        // نربط على الفاتورة الخاصة بحجز العملية
        $invoice = Invoices::where('reservation_id', $this->surgery_booking_id)->first();

        if (!$invoice) {
            $invoice = Invoices::create([
                'reservation_id'        => $this->surgery_booking_id,
                'visit_id'              => $this->visit_id,
                'patient_id'            => $this->patient_id,
                'status'                => 'unpaid',
                'total'                 => 0,
                'contract_contribution' => 0,
                'patient_contribution'  => 0,
                'discount'              => 0,
                'paid'                  => 0,
                'residual'              => 0,
            ]);
        }

        // إدراج «الجديد فقط»
        $newTotals = [
            'total'   => 0.0,
            'patient' => 0.0,
            'contract'=> 0.0,
        ];

        foreach ($this->services as $item) {
            SurgicalServiceVisit::create([
                'invoice_id'     => $invoice->id,
                'visit_id'       => $this->visit_id,
                'service_id'     => $item['service_id'],
                'total'          => (float) $item['total'],           // الإجمالي المرجعي (قبل توزيع التحمل)
                'contract_value' => (float) $item['contract_value'],  // نصيب جهة التعاقد (يشمل خصم القائمة)
                'patient_value'  => (float) $item['patient_value'],   // نصيب المريض بعد العقد
                'discount'       => (float) $item['discount'],
                'quantity'       => (int)   $item['quantity'],
                'patient_id'     => $this->patient_id,
            ]);

            $qty                 = (int) $item['quantity'];
            $newTotals['total']   += ((float) $item['total'])           * $qty;
            $newTotals['patient'] += ((float) $item['patient_value'])   * $qty;
            $newTotals['contract']+= ((float) $item['contract_value'])  * $qty;
        }

        // تحديث الفاتورة بمجاميع «الجديد فقط»
        $invoice->total                 += $newTotals['total'];
        $invoice->contract_contribution += $newTotals['contract'];
        $invoice->patient_contribution  += $newTotals['patient'];
        $invoice->discount              += (float) $this->discount;
        $invoice->paid                  += (float) $this->paid;
        $invoice->residual              += max(0, $newTotals['patient'] - (float) $this->discount - (float) $this->paid);

        if ($invoice->patient_contribution <= $invoice->paid) {
            $invoice->status = 'paid';
        }

        $invoice->save();

        // لا نفرّغ الجدول: نعيد تحميل المحفوظ + نفرّغ «الجديد» فقط
        $this->services = []; // نفرّغ الجديد فقط
        $this->loadSavedServices(); // نعيد تحميل المحفوظ
        $this->calculate();

        session()->flash('success', __('تم حفظ الخدمات بنجاح'));
    }

    public function render()
    {
        return view('livewire.surgical.surgical-service-form', [
            'serviceOptions' => Service::all(),
            'allRows'        => array_merge($this->saved_services, $this->services),
        ]);
    }
}