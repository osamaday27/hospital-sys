<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Administrative\medical_record;
use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Finance\slocker;
use App\Models\Finance\lockertransaction;
use App\Models\Finance\lockerdailybalance;
use App\Models\Finance\AdvancePayment;
use App\Models\Finance\Bank;
use App\Models\Finance\BankMachine;
use App\Traits\Web\get_contract;
use App\Models\Finance\operation_contract_pricing;
use App\models\finance\contracts_details_last_level;

// ✅ السيرفيس الجديد
use App\Services\Finance\ContractPricingService;

class ManageAdvancePayments extends Component
{
    use get_contract, WithPagination;

    public $booking_id;
    public $booking;
    public $patient;

    public $amount;
    public $currency = 'EGP';
    public $payment_method = 'cash';
    public $payment_date;
    public $notes;
    public $locker_id;

    // Visa
    public $card_number;
    public $card_bank;
    public $bank_machine_id;

    // Bank Transfer
    public $transfer_number;
    public $transfer_bank;
    public $bank_id;

    public $lockers;
    public $banks;
    public $bankMachines = [];

    /* ==========================
       تفاصيل العقد/التسعير
    ========================== */
    public $contract_name = '-';

    // إجماليات/خلاصات
    public $package_price = 0.0;              // إجمالي الصافي بعد الخصم (يظهر في كارت المريض)
    public $discount_percentage = 0;          // متوسط مرجّح (للعرض فقط)
    public $patient_contribution = 0;         // متوسط مرجّح (للعرض فقط)

    // تفصيلي لكل عملية
    public $servicesPricing = [];             // صفوف مفصّلة
    public $total_gross_price = 0.0;          // إجمالي قبل الخصم
    public $total_net_price = 0.0;            // إجمالي بعد الخصم
    public $total_patient_amount = 0.0;       // نصيب المريض
    public $total_insurer_amount = 0.0;       // نصيب جهة التعاقد

    // ✅ احتفاظ بنسخة نتيجة السيرفيس (للاستخدامات المستقبلية إن احتجت)
    protected ContractPricingService $pricingService;

    public function mount($bookingId)
    {
        $this->pricingService = app(ContractPricingService::class);

        $this->booking_id = (int) $bookingId;
        $this->booking = adm_b_surgery_bookings::with(['room','services'])->find($this->booking_id);

        if (!$this->booking) {
            session()->flash('error', 'لم يتم العثور على الحجز المطلوب.');
            return;
        }

        $patientId = $this->booking->patient_id ?? $this->booking->medical_record_id ?? null;
        $this->patient = $patientId ? medical_record::find($patientId) : null;

        if ($this->patient) {
            $this->contract_name = $this->patient->insurance_contracting
                ? ($this->getcontractname($this->patient->insurance_contracting) ?: '-')
                : '-';
        }

        $this->payment_date = date('Y-m-d');
        $this->lockers      = slocker::all();
        $this->banks        = Bank::all();
        $this->bankMachines = BankMachine::all(); // لواجهة الفيزا

        // حساب التسعير التفصيلي عبر السيرفيس
        $this->computePricing();
    }

    /* ==========================
       مساعد لتحديد مرجع التعاقد
       نفترض أن حقل insurance_contracting لدى المريض يمثّل Contract Last Level ID
       لو عندك منطق مختلف، عدّل هنا فقط.
    ========================== */
    private function resolveContractRef(): ?array
    {
        $contractId = $this->patient?->insurance_contracting;
        if (!$contractId) {
            return null;
        }
        return [
            'level' => 'last', // نفترض آخر مستوى
            'id'    => (int) $contractId,
            // 'price_list_id' => null, // اتركها ليختارها السيرفيس تلقائياً
        ];
    }

    /* ==========================
       حساب التسعير للعمليات (many-to-many) باستخدام السيرفيس
    ========================== */
    private function computePricing(): void
    {
        $this->servicesPricing      = [];
        $this->total_gross_price    = 0.0;
        $this->total_net_price      = 0.0;
        $this->total_patient_amount = 0.0;
        $this->total_insurer_amount = 0.0;

        $contractRef = $this->resolveContractRef();
        $services    = $this->booking->services ?? collect();

        // ✅ لو مفيش عمليات مرتبطة (قديمة: عمود surgical_id)
        if ($services->isEmpty() && !empty($this->booking->surgical_id)) {
            if ($contractRef) {
                $res = $this->pricingService->calculate(
                    $this->booking->surgical_id,
                    $contractRef,
                    [
                        'category' => 'surgical',
                        'prefer_special_price' => true,
                        'qty' => 1
                    ]
                );

                $serviceName     = $this->booking->service->name ?? __('finance_trans.unknown_service');
                $gross           = (float) ($res['gross'] ?? 0);
                $discPct         = (float) ($res['discount_percentage'] ?? 0);
                $net             = (float) ($res['net_payable'] ?? 0);
                $patientPercent  = (float) ($res['patient_contribution_percent'] ?? 0);
                $patientAmount   = (float) ($res['patient_contribution_amount'] ?? 0);
                $insurerAmount   = (float) ($res['payer_net'] ?? 0);

                $this->servicesPricing[] = [
                    'service_name'        => $serviceName,
                    'gross_price'         => $gross,
                    'discount_percentage' => $discPct,
                    'net_price'           => $net,
                    'patient_percentage'  => $patientPercent,
                    'patient_amount'      => $patientAmount,
                    'insurer_amount'      => $insurerAmount,
                ];

                $this->total_gross_price    += $gross;
                $this->total_net_price      += $net;
                $this->total_patient_amount += $patientAmount;
                $this->total_insurer_amount += $insurerAmount;
            }
        }

        // ✅ المسار الأساسي: لكل خدمة على حدة عبر السيرفيس
        foreach ($services as $srv) {
            if (!$contractRef) {
                // بدون عقد: نعتبرها خارج التعاقد (مريض يدفع كامل القيمة) — لكن بدون سعر قاعدة لن يكون هناك gross
                // هنا نقدر نخلي كل القيم صفر، أو نجيب سعر اللائحة العامة لو عندك Default PriceList
                $this->servicesPricing[] = [
                    'service_name'        => $srv->name ?? __('finance_trans.unknown_service'),
                    'gross_price'         => 0,
                    'discount_percentage' => 0,
                    'net_price'           => 0,
                    'patient_percentage'  => 0,
                    'patient_amount'      => 0,
                    'insurer_amount'      => 0,
                ];
                continue;
            }

            $res = $this->pricingService->calculate(
                $srv->id,
                $contractRef,
                [
                    'category' => 'surgical',         // لو عندك تصنيف من جدول الخدمة اشيله وخليه null
                    'prefer_special_price' => true,
                    'qty' => 1
                ]
            );

            $gross           = (float) ($res['gross'] ?? 0);
            $discPct         = (float) ($res['discount_percentage'] ?? 0);
            $net             = (float) ($res['net_payable'] ?? 0);
            $patientPercent  = (float) ($res['patient_contribution_percent'] ?? 0);
            $patientAmount   = (float) ($res['patient_contribution_amount'] ?? 0);
            $insurerAmount   = (float) ($res['payer_net'] ?? 0);

            $this->servicesPricing[] = [
                'service_name'        => $srv->name ?? __('finance_trans.unknown_service'),
                'gross_price'         => $gross,
                'discount_percentage' => $discPct,
                'net_price'           => $net,
                'patient_percentage'  => $patientPercent,
                'patient_amount'      => $patientAmount,
                'insurer_amount'      => $insurerAmount,
            ];

            $this->total_gross_price    += $gross;
            $this->total_net_price      += $net;
            $this->total_patient_amount += $patientAmount;
            $this->total_insurer_amount += $insurerAmount;
        }

        // تعيين الـ package_price = إجمالي الصافي بعد الخصم
        $this->package_price = $this->total_net_price;

        // متوسطات مرجّحة (للعرض إن احتجتها)
        if ($this->total_gross_price > 0) {
            $totalDiscountAmount = $this->total_gross_price - $this->total_net_price;
            $this->discount_percentage = round(($totalDiscountAmount / $this->total_gross_price) * 100, 2);
        } else {
            $this->discount_percentage = 0;
        }

        if ($this->total_net_price > 0) {
            $this->patient_contribution = round(($this->total_patient_amount / $this->total_net_price) * 100, 2);
        } else {
            $this->patient_contribution = 0;
        }
    }

    protected function rules()
    {
        $lockerTable = (new slocker)->getTable();

        $base = [
            'amount'         => 'required|numeric|min:0.10',
            'currency'       => 'required|string|in:EGP,USD,EUR,SAR,AED,QAR,KWD,OMR,BHD',
            'payment_method' => 'required|in:cash,visa,bank_transfer',
            'payment_date'   => 'required|date',
            'locker_id'      => 'required|integer|' . Rule::exists($lockerTable, 'id'),
        ];

        if ($this->payment_method === 'visa') {
            $base['card_number']     = ['required', 'regex:/^\d{4}$/']; // آخر 4 أرقام
            $base['card_bank']       = ['required', 'string', 'min:2'];
            $base['bank_machine_id'] = ['required', 'exists:fin_bank_machines,id'];
        }

        if ($this->payment_method === 'bank_transfer') {
            $base['transfer_number'] = ['required', 'string', 'min:3'];
            $base['transfer_bank']   = ['required', 'string', 'min:2'];
            $base['bank_id']         = ['required', 'exists:fin_banks,id'];
        }

        return $base;
    }

    public function updated($property)
    {
        // صياغة المبلغ
        if ($property === 'amount' && $this->amount !== null) {
            $this->amount = (float) number_format((float) $this->amount, 2, '.', '');
        }

        // لما تتغير طريقة الدفع نفضّي تفاصيلها
        if ($property === 'payment_method') {
            $this->resetPaymentDetails();
        }

        // حمّل ماكينات الـPOS فقط لما تكون Visa
        if (in_array($property, ['payment_method', 'card_bank']) && $this->payment_method === 'visa') {
            $this->bankMachines = BankMachine::all();
        }
    }

    private function resetPaymentDetails()
    {
        $this->card_number     = null;
        $this->card_bank       = null;
        $this->bank_machine_id = null;

        $this->transfer_number = null;
        $this->transfer_bank   = null;
        $this->bank_id         = null;
    }

    public function resetForm()
    {
        $this->amount         = null;
        $this->currency       = 'EGP';
        $this->payment_method = 'cash';
        $this->payment_date   = date('Y-m-d');
        $this->notes          = null;
        $this->locker_id      = null;

        $this->resetPaymentDetails();
    }

    public function save()
    {
        if (!$this->patient || !$this->booking) {
            session()->flash('error', 'لا يمكن حفظ الدفعة: المريض أو الحجز غير محدد.');
            return;
        }

        $this->validate();

        try {
            DB::transaction(function () {
                // احفظ الدفعة كـ confirmed عشان الشاشات التانية تلتقطها
                $payment = AdvancePayment::create([
                    'patient_id'      => $this->patient->id,
                    'booking_id'      => $this->booking_id,
                    'visit_id'        => $this->booking->visit_id ?? null,
                    'amount'          => $this->amount,
                    'currency'        => $this->currency,
                    'payment_method'  => $this->payment_method,
                    'payment_date'    => $this->payment_date,
                    'notes'           => $this->notes,
                    'locker_id'       => $this->locker_id,
                    'status'          => 'confirmed',

                    'card_number'     => $this->payment_method === 'visa' ? $this->card_number : null,
                    'card_bank'       => $this->payment_method === 'visa' ? $this->card_bank : null,
                    'bank_machine_id' => $this->payment_method === 'visa' ? $this->bank_machine_id : null,

                    'transfer_number' => $this->payment_method === 'bank_transfer' ? $this->transfer_number : null,
                    'transfer_bank'   => $this->payment_method === 'bank_transfer' ? $this->transfer_bank : null,
                    'bank_id'         => $this->payment_method === 'bank_transfer' ? $this->bank_id : null,

                    'user_id'         => Auth::id(),
                ]);

                lockertransaction::create([
                    'locker_id'        => $this->locker_id,
                    'transaction_type' => 'advance_payment',
                    'amount'           => $this->amount,
                    'direction'        => 'in',
                    'reference_type'   => AdvancePayment::class,
                    'reference_id'     => $payment->id,
                    'gl_account_id'    => null,
                    'notes'            => __('finance_trans.advance_payment_note') . ' #' . $payment->id,
                    'created_by'       => Auth::id(),
                ]);

                // رصيد يومي للخزنة
                $daily = lockerdailybalance::firstOrCreate(
                    [
                        'locker_id' => $this->locker_id,
                        'date'      => now()->format('Y-m-d'),
                    ],
                    [
                        'closing_balance' => 0,
                        'actual_cash'     => 0,
                        'created_by'      => Auth::id(),
                    ]
                );

                $daily->increment('closing_balance', $this->amount);
                $daily->increment('actual_cash', $this->amount);
            });

            session()->flash('message', __('finance_trans.payment_saved_successfully'));
            $this->resetForm();

        } catch (\Throwable $e) {
            session()->flash('error', __('finance_trans.unexpected_error') . ' - ' . $e->getMessage());
        }
    }

    public function render()
    {
        $history = collect();
        if ($this->booking_id) {
            $history = AdvancePayment::with(['user', 'bank', 'bankMachine'])
                ->where('booking_id', $this->booking_id)
                ->orderBy('payment_date', 'desc')
                ->get();
        }

        return view('livewire.finance.program.manage-advance-payments', [
            'history'              => $history,
            'patient'              => $this->patient,
            'booking'              => $this->booking,
            'lockers'              => $this->lockers,
            'banks'                => $this->banks,
            'bankMachines'         => $this->bankMachines,

            // تفاصيل/إجماليات التسعير
            'servicesPricing'      => $this->servicesPricing,
            'total_gross_price'    => $this->total_gross_price,
            'total_net_price'      => $this->total_net_price,
            'total_patient_amount' => $this->total_patient_amount,
            'total_insurer_amount' => $this->total_insurer_amount,

            'discount_percentage'  => $this->discount_percentage,
            'patient_contribution' => $this->patient_contribution,
            'package_price'        => $this->package_price,
            'contract_name'        => $this->contract_name,
            'currency'             => $this->currency,
        ]);
    }
}