<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Traits\Finance\invoiceservicetraitoprtion;

// حجوزات العمليات
use App\models\administrative\adm_b_surgery_bookings;
use App\Models\Administrative\BookingSurgery;

// الفاتورة وتفاصيل الخدمات
use App\models\finance\invoices;
use App\models\finance\finservicevisit;

// الدفعات المقدمة
use App\models\finance\AdvancePayment;

// تفاصيل الفاتورة (الأتعاب/التكاليف للعمليات)
use App\Models\Finance\operation_invoice_detail;

// جدول الخدمات
use App\models\finance\service as FinanceService;

// تسعير العملية بالعقد
use App\Models\Finance\operation_contract_pricing;

// خدمات افتراضية مرتبطة بالعملية
use App\Models\Medical\SurgeryServiceItem;

class SurgicalInvoiceManager extends Component
{
    use invoiceservicetraitoprtion;

    // المُدخل الرئيسي
    public int $reservation_id;

    // رأس الفاتورة
    public ?invoices $invoice = null;
    public ?object $reservation = null;
    public ?object $patient = null;
    public ?int $visit_id = null;
    public ?int $contract_id = null;

    // العمليات المرتبطة بالحجز (متعدد)
    /** @var array<int,array{id:int,name:string,side:?string}> */
    public array $booking_surgeries = [];
    public ?int $active_surgery_id = null;

    // تفاصيل العملية (لكل عملية)
    /** @var array<int,array> */
    public array $surgery_details = [];

    // إجمالي دفعات مقدّمة + القائمة التفصيلية
    public float $advance_paid = 0;
    public array $advance_payments = [];

    // جدول الخدمات كبنود فاتورة (مشترك)
    public array $services = [];

    // قوائم للمساعدة
    public $allServices = [];
    public $surgeryServices = [];

    // بنود لا تتكرر (IDs للبنود الفريدة)
    public array $nonRepeatableServiceIds = [
        // مثال: 101, 202
    ];

    // التاب الجديد: خدمات مرتبطة بكل عملية
    /** @var array<int,array<int,array{service_id:int,service_name:string,quantity:float,unit_price:float,notes:?string}>> */
    public array $surgery_linked_services = [];

    protected $rules = [
        'reservation_id'            => 'required|integer',
        'services.*.service_id'     => 'nullable|integer',
        'services.*.quantity'       => 'nullable|numeric|min:0',
        'services.*.included_qty'   => 'nullable|numeric|min:0',
        'services.*.unit_price'     => 'nullable|numeric|min:0',
        'services.*.total_price'    => 'nullable|numeric|min:0',
        'services.*.contract_value' => 'nullable|numeric|min:0',
        'services.*.patient_value'  => 'nullable|numeric|min:0',
        'services.*.discount'       => 'nullable|numeric|min:0',
        'services.*.note'           => 'nullable|string|max:255',
    ];

    public function mount($reservationId)
    {
        $this->reservation_id = (int) $reservationId;

        // تحميل الحجز + المريض + الزيارة
        $this->reservation = adm_b_surgery_bookings::with(['bookingSurgeries.surgery'])->findOrFail($this->reservation_id);
        $this->patient     = $this->reservation->patient ?? null;
        $this->visit_id    = $this->reservation->visit_id ?? null;
        $this->contract_id = $this->patient->insurance_contracting ?? null;

        // جلب العمليات المتعددة من جدول الربط booking_surgeries
        $this->booking_surgeries = [];
        foreach ($this->reservation->bookingSurgeries as $row) {
            $this->booking_surgeries[] = [
                'id'   => (int) $row->surgery_id,
                'name' => $row->surgery?->getTranslation('name', app()->getLocale()) ?? ('#'.$row->surgery_id),
                'side' => $row->side,
            ];
        }

        // عملية نشطة افتراضيًا (أول واحدة إن وجدت)
        $this->active_surgery_id = $this->booking_surgeries[0]['id'] ?? null;

        // ضمان وجود فاتورة خاصة بالحجز
        $this->invoice = $this->getOrCreateInvoiceForReservation($this->reservation_id);

        // مصادر الاختيارات
        $this->allServices     = FinanceService::select('id','name')->orderBy('id','asc')->limit(1000)->get();
        $this->surgeryServices = $this->allServices;

        // حمّل تفاصيل كل عملية
        $this->bootSurgeryDetailsFromDbOrDefaults();

        // حمّل الخدمات المسجلة سابقًا كبنود فاتورة (مشتركة)
        $existing = finservicevisit::where('id_invoices', $this->invoice->id)->get();
        if ($existing->count() > 0) {
            $this->services = $existing->map(function ($row) {
                return [
                    'service_id'     => (int) $row->id_services,
                    'quantity'       => (float) $row->quantity,
                    'included_qty'   => 0.0, // لم تكن محفوظة سابقًا
                    'unit_price'     => $row->quantity > 0 ? (float) $row->total_pricing / (float) $row->quantity : 0,
                    'total_price'    => (float) $row->total_pricing,
                    'contract_value' => (float) ($row->contract_contribution ?? 0),
                    'patient_value'  => (float) ($row->patient_contribution ?? 0),
                    'discount'       => (float) ($row->discount ?? 0),
                    'note'           => null,
                ];
            })->toArray();
        }

        // دفعات مقدّمة
        $this->refreshAdvanceAndInvoiceStatus(false);
        $this->loadAdvancePaymentsList();

        // **الخدمات المرتبطة بالعمليات** (للتاب الجديد)
        $this->loadLinkedServicesForAllSurgeries();
    }

    /** تحميل تفاصيل كل عملية من DB أو تهيئة افتراضية ثم إعادة حساب */
    protected function bootSurgeryDetailsFromDbOrDefaults(): void
    {
        foreach ($this->booking_surgeries as $sx) {
            $sid = $sx['id'];

            $detail = operation_invoice_detail::where('invoice_id', $this->invoice->id)
                ->where('reservation_id', $this->reservation_id)
                ->where('surgery_id', $sid)
                ->first();

            if ($detail) {
                $this->surgery_details[$sid] = [
                    'surgeon_fee'         => (float) $detail->surgeon_fee,
                    'assistant_fee'       => (float) $detail->assistant_fee,
                    'anesthesia_fee'      => (float) $detail->anesthesia_fee,
                    'anesthesia_type'     => $detail->anesthesia_type,
                    'nursing_fee'         => (float) $detail->nursing_fee,
                    'operation_room_fee'  => (float) $detail->operation_room_fee,
                    'additional_time_fee' => (float) $detail->additional_time_fee,
                    'supplies_cost'       => (float) $detail->supplies_cost,
                    'other_services'      => (float) $detail->other_services,
                    'discount'            => (float) $detail->discount,
                    'vat_rate'            => (float) $detail->vat_rate,
                    'vat_amount'          => (float) $detail->vat_amount,
                    'subtotal'            => (float) $detail->subtotal,
                    'total_price'         => (float) $detail->total_price,
                    'patient_value'       => (float) $detail->patient_value,
                    'contract_value'      => (float) $detail->contract_value,
                    'notes'               => $detail->notes,
                ];
            } else {
                // تهيئة افتراضية ثم محاولة تحميل تسعير العقد
                $this->surgery_details[$sid] = $this->blankDetail();

                // لو فيه عقد، حمل تسعير العملية دي
                $this->loadContractForSurgery($sid, false);

                // افتراض: إن لم يوجد تسعير، نسيبها أصفار (يقدر يعدلها المستخدم)
                $this->recalcOneSurgeryTotals($sid);
            }
        }
    }

    /** نموذج فارغ لتفاصيل عملية */
    protected function blankDetail(): array
    {
        return [
            'surgeon_fee'         => 0.0,
            'assistant_fee'       => 0.0,
            'anesthesia_fee'      => 0.0,
            'anesthesia_type'     => null,
            'nursing_fee'         => 0.0,
            'operation_room_fee'  => 0.0,
            'additional_time_fee' => 0.0,
            'supplies_cost'       => 0.0,
            'other_services'      => 0.0,
            'discount'            => 0.0,
            'vat_rate'            => 0.0,
            'vat_amount'          => 0.0,
            'subtotal'            => 0.0,
            'total_price'         => 0.0,
            'patient_value'       => 0.0,
            'contract_value'      => 0.0,
            'notes'               => null,
        ];
    }

    /** تحميل تسعير عقد لعملية محددة */
    protected function loadContractForSurgery(int $surgeryId, bool $flash = true): void
    {
        if (!$this->contract_id) return;

        $pricing = operation_contract_pricing::where('contract_id', $this->contract_id)
            ->where('surgery_id', $surgeryId)
            ->orderByDesc('id')
            ->first();

        if ($pricing) {
            $d = &$this->surgery_details[$surgeryId];

            $d['surgeon_fee']         = (float) $pricing->surgeon_fee;
            $d['assistant_fee']       = (float) $pricing->assistant_fee;
            $d['anesthesia_fee']      = (float) $pricing->anesthesia_fee;
            $d['anesthesia_type']     = $pricing->anesthesia_type;
            $d['nursing_fee']         = (float) $pricing->nursing_fee;
            $d['operation_room_fee']  = (float) $pricing->operation_room_fee;
            $d['additional_time_fee'] = (float) $pricing->additional_time_fee;
            $d['supplies_cost']       = (float) $pricing->supplies_cost;
            $d['other_services']      = (float) $pricing->other_services;

            $d['discount']            = (float) ($pricing->discount ?? 0);
            $d['vat_rate']            = (float) ($pricing->vat ?? 0);

            $this->recalcOneSurgeryTotals($surgeryId);

            // افتراض: العقد يغطي تفاصيل العملية بالكامل
            $d['patient_value']  = 0.0;
            $d['contract_value'] = (float) $d['total_price'];

            if ($flash) {
                session()->flash('success', __('finance_trans.loaded_from_contract'));
            }
        }
    }

    /** زر إعادة تحميل تسعير العقد + البنود الافتراضية للعملية النشطة */
    public function reloadDefaults(): void
    {
        if (!$this->active_surgery_id) return;

        // تسعير العقد للعملية النشطة
        $this->loadContractForSurgery($this->active_surgery_id, false);

        // البنود الافتراضية (مشمولة ضمن العملية) تُحمّل كصفوف بخانة included_qty
        $this->loadDefaultItemsForSurgery($this->active_surgery_id);

        // تحديث حالة الفاتورة والرصيد وفق الدفعات (بدون كتابة حالة)
        $this->refreshAdvanceAndInvoiceStatus(false);
        $this->loadAdvancePaymentsList();

        // تحديث الخدمات المرتبطة (عرض التاب)
        $this->loadLinkedServicesForAllSurgeries();

        session()->flash('success', __('finance_trans.loaded_from_contract'));
    }

    /** تحميل عناصر الخدمات الافتراضية للـ surgery وإضافتها لجدول الخدمات (مشمولة) */
    protected function loadDefaultItemsForSurgery(int $surgeryId): void
    {
        $items = SurgeryServiceItem::where('surgery_id', $surgeryId)->get();

        foreach ($items as $it) {
            $srv = FinanceService::find($it->service_id);
            $unitPrice = $srv ? $this->guessServiceUnitPrice($srv) : 0.0;
            $included = (float) ($it->quantity ?? 0);
            $qty = $included;

            $this->services[] = [
                'service_id'     => (int) $it->service_id,
                'quantity'       => $qty,
                'included_qty'   => $included, // الكمية المشمولة ضمن العملية
                'unit_price'     => $unitPrice,
                'total_price'    => 0.0, // يُحسب فقط على الزيادة
                'contract_value' => 0.0,
                'patient_value'  => 0.0,
                'discount'       => 0.0,
                'note'           => __('finance_trans.included_in_surgery_price') ?? 'Included in surgery price',
            ];
        }

        $this->recalcServiceLines();
    }

    /** تقدير سعر خدمة من جدول الخدمات */
    protected function guessServiceUnitPrice($service): float
    {
        return (float) (
            $service->price
            ?? $service->unit_price
            ?? $service->amount
            ?? 0
        );
    }

    /** إضافة صف خدمة */
    public function addServiceRow(): void
    {
        $this->services[] = [
            'service_id'     => null,
            'quantity'       => 1,
            'included_qty'   => 0,
            'unit_price'     => 0,
            'total_price'    => 0,
            'contract_value' => 0,
            'patient_value'  => 0,
            'discount'       => 0,
            'note'           => null,
        ];
    }

    /** حذف صف خدمة */
    public function removeServiceRow($index): void
    {
        unset($this->services[$index]);
        $this->services = array_values($this->services);
        $this->recalcServiceLines();
        $this->refreshAdvanceAndInvoiceStatus(false);
    }

    /** كل تحديث */
    public function updated($prop): void
    {
        if (str_starts_with($prop, 'services.')) {
            $this->recalcServiceLines();
        } elseif (preg_match('/^surgery_details\.(\d+)\./', $prop, $m)) {
            $sid = (int) $m[1];
            $this->recalcOneSurgeryTotals($sid);
        }
        $this->refreshAdvanceAndInvoiceStatus(false);
    }

    /** تغيير العملية النشطة */
    public function updatedActiveSurgeryId(): void
    {
        // لا شيء—الزر Reload كافي
    }

    /** حساب إجماليات سطور الخدمات مع دعم included_qty + منع التكرار */
    protected function recalcServiceLines(): void
    {
        $seenUnique = [];

        foreach ($this->services as $i => $row) {
            $sid   = (int) ($row['service_id'] ?? 0);
            $qty   = (float) ($row['quantity'] ?? 0);
            $inc   = max(0.0, (float) ($row['included_qty'] ?? 0));
            $unit  = (float) ($row['unit_price'] ?? 0);
            $disc  = max(0.0, (float) ($row['discount'] ?? 0));

            // منع التكرار للبنود الفريدة
            if ($sid && in_array($sid, $this->nonRepeatableServiceIds, true)) {
                if (!isset($seenUnique[$sid])) {
                    $seenUnique[$sid] = true; // أول واحد مسموح
                } else {
                    // صف مكرر: صفّره
                    $this->services[$i]['quantity']        = 0.0;
                    $this->services[$i]['included_qty']    = 0.0;
                    $this->services[$i]['total_price']     = 0.0;
                    $this->services[$i]['contract_value']  = 0.0;
                    $this->services[$i]['patient_value']   = 0.0;
                    $this->services[$i]['discount']        = 0.0;
                    $this->services[$i]['note']            = trim(($row['note'] ?? '').' | '.((__('finance_trans.duplicate_removed')) ?? 'Duplicate non-repeatable removed'));
                    continue;
                }
            }

            // احسب الزيادة فقط عن المشمول
            $extra = max(0.0, round($qty - $inc, 2));
            $gross = round($extra * $unit, 2);

            // الخصم لا يتجاوز الـ gross
            $disc = min($disc, $gross);
            $net  = round($gross - $disc, 2);

            $this->services[$i]['discount']    = $disc;
            $this->services[$i]['total_price'] = $net;

            // توزيع افتراضي لو مفيش توزيع ومبلغ > 0
            $pat  = (float) ($row['patient_value']  ?? 0);
            $cont = (float) ($row['contract_value'] ?? 0);
            if ($pat == 0.0 && $cont == 0.0 && $net > 0) {
                $this->services[$i]['patient_value']  = $net;
                $this->services[$i]['contract_value'] = 0.0;
            }

            // لا سالب
            $this->services[$i]['patient_value']  = max(0.0, (float)$this->services[$i]['patient_value']);
            $this->services[$i]['contract_value'] = max(0.0, (float)$this->services[$i]['contract_value']);
        }
    }

    /** حساب إجماليات عملية واحدة */
    protected function recalcOneSurgeryTotals(int $surgeryId): void
    {
        if (!isset($this->surgery_details[$surgeryId])) return;

        $d = &$this->surgery_details[$surgeryId];

        $sum = (float)$d['surgeon_fee']
            + (float)$d['assistant_fee']
            + (float)$d['anesthesia_fee']
            + (float)$d['nursing_fee']
            + (float)$d['operation_room_fee']
            + (float)$d['additional_time_fee']
            + (float)$d['supplies_cost']
            + (float)$d['other_services'];

        $d['subtotal'] = round($sum, 2);

        $afterDiscount = max(0, $d['subtotal'] - (float)$d['discount']);
        $d['vat_amount'] = round($afterDiscount * ((float)$d['vat_rate'] / 100), 2);
        $d['total_price'] = round($afterDiscount + $d['vat_amount'], 2);

        // توزيع افتراضي لو لسه الاتنين صفر
        if ((float)$d['patient_value'] == 0 && (float)$d['contract_value'] == 0 && $d['total_price'] > 0) {
            $d['patient_value']  = $d['total_price'];
            $d['contract_value'] = 0.0;
        }
    }

    /** حساب المجمّع على مستوى كل العمليات */
    protected function aggregateOperationsTotals(): array
    {
        $details_total = 0.0;
        $patient_sum   = 0.0;
        $contract_sum  = 0.0;

        foreach ($this->surgery_details as $sid => $d) {
            $details_total += (float) $d['total_price'];
            $patient_sum   += (float) $d['patient_value'];
            $contract_sum  += (float) $d['contract_value'];
        }

        return [
            'details_total' => round($details_total, 2),
            'patient_sum'   => round($patient_sum, 2),
            'contract_sum'  => round($contract_sum, 2),
        ];
    }

    /** تحميل قائمة الدفعات المقدّمة التفصيلية للعرض */
    protected function loadAdvancePaymentsList(): void
    {
        $list = AdvancePayment::where('patient_id', $this->invoice->patient_id ?? null)
            ->where('booking_id', $this->reservation_id)
            ->when(Schema::hasColumn('advance_payments','status'), function ($q) {
                $q->where('status', 'confirmed');
            })
            ->orderByDesc('payment_date')
            ->get(['amount','payment_method','payment_date','notes']);

        $this->advance_payments = $list->map(function ($r) {
            return [
                'amount'        => (float) $r->amount,
                'payment_method'=> $r->payment_method,
                'payment_date'  => $r->payment_date,
                'notes'         => $r->notes,
            ];
        })->toArray();
    }

    /** حساب الدفعات المقدمة وتحديث حالة الفاتورة */
    protected function refreshAdvanceAndInvoiceStatus(bool $updateInvoice = true): void
    {
        if (!$this->invoice) return;

        // إجماليات بنود الخدمات المحفوظة في DB
        $services_total_db    = (float) finservicevisit::where('id_invoices', $this->invoice->id)->sum('total_pricing');
        $services_patient_db  = (float) finservicevisit::where('id_invoices', $this->invoice->id)->sum('patient_contribution');
        $services_contract_db = (float) finservicevisit::where('id_invoices', $this->invoice->id)->sum('contract_contribution');

        // مجمّع تفاصيل العمليات الحالية (بالشاشة)
        $agg = $this->aggregateOperationsTotals();
        $details_total   = $agg['details_total'];
        $details_patient = $agg['patient_sum'];
        $details_contract= $agg['contract_sum'];

        // إجمالي الفاتورة المتوقع
        $invoice_total    = round($details_total + $services_total_db, 2);
        $invoice_patient  = round($details_patient + $services_patient_db, 2);
        $invoice_contract = round($details_contract + $services_contract_db, 2);

        // دفعات مقدمة مؤكدة للحجز الحالي
        $this->advance_paid = (float) AdvancePayment::where('patient_id', $this->invoice->patient_id)
            ->where('booking_id', $this->reservation_id)
            ->when(Schema::hasColumn('advance_payments','status'), function ($q) {
                $q->where('status', 'confirmed');
            })
            ->sum('amount');

        $residual = max(0, $invoice_patient - $this->advance_paid);
        $status   = $residual <= 0 ? 'paid' : ($this->advance_paid > 0 ? 'partial' : 'unpaid');

        if ($updateInvoice) {
            $this->invoice->update([
                'total'                 => $invoice_total,
                'patient_contribution'  => $invoice_patient,
                'contract_contribution' => $invoice_contract,
                'residual'              => $residual,
                'status'                => $status,
            ]);
        }
    }

    /** =========================
     *  التاب الجديد: تحميل الخدمات المرتبطة بكل عملية
     *  ========================= */
    protected function loadLinkedServicesForAllSurgeries(): void
    {
        $this->surgery_linked_services = [];

        foreach ($this->booking_surgeries as $sx) {
            $sid = (int) $sx['id'];

            $items = SurgeryServiceItem::where('surgery_id', $sid)->get();
            if ($items->isEmpty()) {
                $this->surgery_linked_services[$sid] = [];
                continue;
            }

            $rows = [];
            foreach ($items as $it) {
                $srv  = FinanceService::find($it->service_id);
                $unit = $srv ? $this->guessServiceUnitPrice($srv) : 0.0;

                $rows[] = [
                    'service_id'   => (int) $it->service_id,
                    'service_name' => $srv?->getTranslation('name', app()->getLocale()) ?? ('#'.$it->service_id),
                    'quantity'     => (float) ($it->quantity ?? 0),
                    'unit_price'   => (float) $unit,
                    'notes'        => $it->notes,
                ];
            }

            $this->surgery_linked_services[$sid] = $rows;
        }
    }

    /** أضف خدمة مرتبطة (لعملية معينة) إلى جدول بنود الفاتورة */
    public function addLinkedItemToInvoice(int $surgeryId, int $serviceId): void
    {
        // دور على الـ row من المصفوفة المجهزة
        $rows = $this->surgery_linked_services[$surgeryId] ?? [];
        $row  = collect($rows)->firstWhere('service_id', $serviceId);
        if (!$row) return;

        $this->services[] = [
            'service_id'     => (int) $row['service_id'],
            'quantity'       => (float) $row['quantity'],
            'included_qty'   => (float) $row['quantity'], // المشمول = الكمية المعرّفة على العملية
            'unit_price'     => (float) $row['unit_price'],
            'total_price'    => 0.0, // يتحسب على الزيادة فقط
            'contract_value' => 0.0,
            'patient_value'  => 0.0,
            'discount'       => 0.0,
            'note'           => __('finance_trans.included_in_surgery_price') ?? 'Included in surgery price',
        ];

        $this->recalcServiceLines();
        $this->refreshAdvanceAndInvoiceStatus(false);

        session()->flash('success', __('finance_trans.added_to_invoice_successfully') ?? 'Added to invoice.');
    }

    /** أضف كل خدمات عملية محددة إلى جدول بنود الفاتورة */
    public function addAllLinkedItemsOfSurgery(int $surgeryId): void
    {
        $rows = $this->surgery_linked_services[$surgeryId] ?? [];
        if (empty($rows)) return;

        foreach ($rows as $row) {
            $this->services[] = [
                'service_id'     => (int) $row['service_id'],
                'quantity'       => (float) $row['quantity'],
                'included_qty'   => (float) $row['quantity'],
                'unit_price'     => (float) $row['unit_price'],
                'total_price'    => 0.0,
                'contract_value' => 0.0,
                'patient_value'  => 0.0,
                'discount'       => 0.0,
                'note'           => __('finance_trans.included_in_surgery_price') ?? 'Included in surgery price',
            ];
        }

        $this->recalcServiceLines();
        $this->refreshAdvanceAndInvoiceStatus(false);

        session()->flash('success', __('finance_trans.all_added_to_invoice_successfully') ?? 'All linked services added.');
    }

    /** حفظ كل شيء */
    public function saveAll(): void
    {
        // قواعد ديناميكية لتفاصيل كل عملية
        $surgeryRules = [];
        foreach ($this->surgery_details as $sid => $d) {
            $prefix = "surgery_details.$sid.";
            $surgeryRules[$prefix.'surgeon_fee']         = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'assistant_fee']       = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'anesthesia_fee']      = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'nursing_fee']         = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'operation_room_fee']  = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'additional_time_fee'] = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'supplies_cost']       = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'other_services']      = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'discount']            = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'vat_rate']            = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'patient_value']       = 'nullable|numeric|min:0';
            $surgeryRules[$prefix.'contract_value']      = 'nullable|numeric|min:0';
        }

        $this->validate(array_merge($this->rules, $surgeryRules));

        DB::transaction(function () {
            // تأكيد وجود الفاتورة
            $this->invoice = $this->getOrCreateInvoiceForReservation($this->reservation_id);

            // حفظ/تحديث تفاصيل كل عملية كسطر مستقل
            foreach ($this->surgery_details as $sid => $d) {
                // إعادة حساب للتأكيد
                $this->recalcOneSurgeryTotals($sid);

                operation_invoice_detail::updateOrCreate(
                    [
                        'invoice_id'     => $this->invoice->id,
                        'reservation_id' => $this->reservation_id,
                        'surgery_id'     => $sid, // ✅ مفتاح مركب لدعم تعدد العمليات
                    ],
                    [
                        'surgeon_fee'         => (float) $d['surgeon_fee'],
                        'assistant_fee'       => (float) $d['assistant_fee'],
                        'anesthesia_fee'      => (float) $d['anesthesia_fee'],
                        'anesthesia_type'     => $d['anesthesia_type'],
                        'nursing_fee'         => (float) $d['nursing_fee'],
                        'operation_room_fee'  => (float) $d['operation_room_fee'],
                        'additional_time_fee' => (float) $d['additional_time_fee'],
                        'supplies_cost'       => (float) $d['supplies_cost'],
                        'other_services'      => (float) $d['other_services'],
                        'subtotal'            => (float) $d['subtotal'],
                        'discount'            => (float) $d['discount'],
                        'vat_rate'            => (float) $d['vat_rate'],
                        'vat_amount'          => (float) $d['vat_amount'],
                        'total_price'         => (float) $d['total_price'],
                        'patient_value'       => (float) $d['patient_value'],
                        'contract_value'      => (float) $d['contract_value'],
                        'notes'               => $d['notes'],
                        'user_id'             => auth()->id(),
                    ]
                );
            }

            // حذف وإعادة بناء بنود الخدمات حسب الجدول على الشاشة
            finservicevisit::where('id_invoices', $this->invoice->id)->delete();

            $this->recalcServiceLines();

            $servicesList = [];
            foreach ($this->services as $row) {
                if (empty($row['service_id'])) continue;

                $servicesList[] = [
                    'service_id'     => (int) $row['service_id'],
                    'quantity'       => (float) $row['quantity'],
                    'unit_price'     => (float) $row['unit_price'],
                    'total_price'    => (float) $row['total_price'],
                    'contract_value' => (float) ($row['contract_value'] ?? 0),
                    'patient_value'  => (float) ($row['patient_value'] ?? 0),
                    'discount'       => (float) ($row['discount'] ?? 0),
                ];
            }

            if (!empty($servicesList)) {
                // استخدام الدالة من الترايت
                $this->addServicesToInvoice($this->reservation_id, $servicesList);
            }

            // تحديث حالة الفاتورة والرصيد وفق الدفعات
            $this->refreshAdvanceAndInvoiceStatus(true);
            $this->loadAdvancePaymentsList();

            // تحديث التاب المرتبط (في حال حصلت تغييرات)
            $this->loadLinkedServicesForAllSurgeries();

            session()->flash('success', __('finance_trans.saved_successfully'));
        });
    }
// اجعل التاب الافتراضي هو overview
public string $active_tab = 'overview';

// لتغيير التاب النشط
public function setActiveTab(string $tab): void
{
    $this->active_tab = $tab;
}

    public function render()
    {
        // إجماليات الخدمات المحفوظة في DB للعرض
        $services_total_db    = (float) finservicevisit::where('id_invoices', $this->invoice->id ?? 0)->sum('total_pricing');
        $services_patient_db  = (float) finservicevisit::where('id_invoices', $this->invoice->id ?? 0)->sum('patient_contribution');

        // مجمّع تفاصيل العمليات الحالية (بالشاشة)
        $agg = $this->aggregateOperationsTotals();

        $invoice_total_preview   = round($agg['details_total'] + $services_total_db, 2);
        $invoice_patient_preview = round($agg['patient_sum']   + $services_patient_db, 2);
        $residual_preview        = max(0, $invoice_patient_preview - $this->advance_paid);

        return view('livewire.finance.program.surgical-invoice-manager', [
            'allServices'              => $this->allServices,
            'surgeryServices'          => $this->surgeryServices,
            'services_total'           => $services_total_db,
            'invoice_total_preview'    => $invoice_total_preview,
            'invoice_patient_preview'  => $invoice_patient_preview,
            'residual_preview'         => $residual_preview,
            'advance_payments'         => $this->advance_payments,
            'nonRepeatableServiceIds'  => $this->nonRepeatableServiceIds,
            'surgery_linked_services'  => $this->surgery_linked_services, // 👈 تمرير بيانات التاب الجديد
        ]);
    }
}