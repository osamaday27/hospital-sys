<?php
// app/Http/Livewire/Finance/Program/BookingDetails.php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;

use App\models\administrative\adm_b_surgery_bookings;
use App\Models\Administrative\SurgicalServiceVisit;
use App\models\finance\invoices;
use App\models\finance\AdvancePayment;
use App\models\supply_chain\admbvisitsupply;
use App\models\administrative\surgery_medical_team;
use App\models\administrative\adm_b_surgery_supply_issue;
use App\Models\Administrative\medical_record;

use App\Models\Medical\SurgeryServiceItem;
use App\Models\Medical\SurgeryItem;
use App\Models\Finance\operation_contract_pricing;
use App\models\finance\service as FinanceService;

use App\Models\Supply_Chain\Productdata;
use Illuminate\Support\Arr;

use App\Traits\Web\get_contract;
use App\Traits\Finance\invoiceservicetrait;

class BookingDetails extends Component
{
    use get_contract, invoiceservicetrait;

    public $bookingId;

    public $booking;
    public $services;
    public $supplies;
    public $invoices;
    public $payments;
    public $medical_team;
    public $issues;
    public $issued_items;

    public $totalServices = 0.0;
    public $totalPayments = 0.0;
    public $balance       = 0.0;

    public $contractId = null;
    public $surgeryId  = null;
    public $package_price = 0.0;
    public $extras_total  = 0.0;
    public $grand_total   = 0.0;

    public $included_services = [];
    public $extra_services    = [];
    public $included_items    = [];
    public $extra_items       = [];

    public $contract_name = '-';

    // مودال إضافة خدمات العملية للفاتورة
    public $showAddPackageModal = false;
    public $packageServicesPreview = [];

    public function mount($bookingId)
    {
        $this->bookingId = $bookingId;

        $this->booking = adm_b_surgery_bookings::with(['patient','room','user'])->findOrFail($bookingId);
        $patientId = $this->booking->patient_id;

        $this->contractId = medical_record::query()
            ->where('id', $patientId)
            ->value('insurance_contracting');

        $this->contract_name = $this->contractId ? ($this->getcontractname($this->contractId) ?: '-') : '-';

        $this->services = SurgicalServiceVisit::with('service')
            ->where('patient_id', $patientId)
            ->where('visit_id', $this->booking->visit_id)
            ->get();

        $this->surgeryId = $this->booking->surgical_id
           ;

        $this->services = $this->services->map(function ($s) {
            $qty   = is_numeric($s->quantity ?? null) ? (float)$s->quantity : 0.0;
            $unitPrice =
                (is_numeric($s->price ?? null)  && (float)$s->price  > 0) ? (float)$s->price  :
                ((is_numeric($s->total ?? null) && (float)$s->total > 0) ? (float)$s->total : 0.0);

            $s->computed_unit_price = $unitPrice;
            $s->computed_total      = $unitPrice * $qty;

            return $s;
        });

        $this->supplies = admbvisitsupply::where('patient_id', $patientId)
            ->where('visit_id', $this->booking->visit_id)
            ->get();

        $this->invoices = invoices::where('patient_id', $patientId)
            ->where('reservation_id', $this->booking->id)
            ->get();

        $this->payments = AdvancePayment::where('patient_id', $patientId)
            ->where('booking_id', $this->booking->id)
            ->get();

        $this->medical_team = surgery_medical_team::with('employee')
            ->where('surgery_bookings_id', $this->booking->id)
            ->get();

        $this->issues = adm_b_surgery_supply_issue::with(['items.product','items.unit'])
            ->where('surgery_booking_id', $this->booking->id)
            ->orderByDesc('id')
            ->get();

        $this->issued_items = $this->issues->flatMap(function($issue){
            return $issue->items->map(function($it) use ($issue){
                return (object)[
                    'movement_number'   => $issue->movement_number,
                    'status'            => $issue->status,
                    'store_id'          => $issue->store_id,
                    'product_name'      => optional($it->product)->name ?? optional($it->product)->name_ar,
                    'unit_name'         => optional($it->unit)->name ?? optional($it->unit)->name_ar,
                    'qty_requested'     => $it->quantity_requested,
                    'qty_issued'        => $it->quantity_issued,
                    'batch_no'          => $it->batch_no,
                    'expiry_date'       => $it->expiry_date,
                ];
            });
        });

        $this->totalServices = $this->services->sum(fn ($s) => (float) ($s->computed_total ?? 0));
        $this->totalPayments = (float) $this->payments->sum(fn ($p) => (float) ($p->amount ?? 0));
        $this->balance = $this->totalPayments - $this->totalServices;

        $this->calculatePackageAndExtras();
        $this->distributeTeamFees();
    }

    protected function calculatePackageAndExtras(): void
    {
        $this->package_price = 0.0;
        if ($this->contractId && $this->surgeryId) {
            $pricing = operation_contract_pricing::where('contract_id', $this->contractId)
                ->where('surgery_id', $this->surgeryId)
                ->first();
            if ($pricing) {
                $this->package_price = (float) ($pricing->total_price ?? 0);
            }
        }

        $includedServiceIds = collect();
        $includedItemsQty   = collect();

        if ($this->surgeryId) {
            $includedServiceIds = SurgeryServiceItem::where('surgery_id', $this->surgeryId)
                ->pluck('service_id')
                ->unique()
                ->values();

            $includedItemsQty = SurgeryItem::where('surgery_id', $this->surgeryId)
                ->get(['item_id','quantity','unit'])
                ->mapWithKeys(fn ($row) => [(int)$row->item_id => [
                    'qty'  => (float)$row->quantity,
                    'unit' => $row->unit,
                ]]);
        }

        $perfServices = $this->services->map(function($s){
            return [
                'service_id'   => (int)$s->service_id,
                'quantity'     => (float)($s->quantity ?? 1),
                'unit_price'   => (float)($s->computed_unit_price ?? 0),
                'service_name' => optional($s->service)->name ?? ('Service#'.$s->service_id),
            ];
        });

        $perfItems = $this->issues->flatMap(function($issue){
            return $issue->items->map(function($it){
                $prod = $it->product;
                $name = optional($prod)->name ?? optional($prod)->name_ar ?? ('Item#'.$it->product_id);
                return [
                    'item_id'    => (int)$it->product_id,
                    'quantity'   => (float)($it->quantity_issued ?? 0),
                    'unit'       => optional($it->unit)->name ?? optional($it->unit)->name_ar,
                    'unit_price' => 0.0,
                    'item_name'  => $name,
                ];
            });
        })->values();

        $this->included_services = [];
        $this->extra_services    = [];
        $this->included_items    = [];
        $this->extra_items       = [];

        foreach ($perfServices as $srv) {
            $serviceId   = $srv['service_id'];
            $serviceName = $srv['service_name'];
            $qty         = $srv['quantity'];
            $uPrice      = $srv['unit_price'];

            if (!$includedServiceIds->contains($serviceId)) {
                $lineTotal = $uPrice * $qty;
                $this->extra_services[] = [
                    'service_id'   => $serviceId,
                    'service_name' => $serviceName,
                    'quantity'     => $qty,
                    'unit_price'   => $uPrice,
                    'total'        => $lineTotal,
                    'reason'       => 'خدمة غير مضمنة ضمن باكدج العملية',
                ];
            } else {
                $this->included_services[] = [
                    'service_id'   => $serviceId,
                    'service_name' => $serviceName,
                    'quantity'     => $qty,
                ];
            }
        }

        foreach ($perfItems as $it) {
            $itemId   = $it['item_id'];
            $qtyUsed  = $it['quantity'];
            $unit     = $it['unit'];
            $uPrice   = $it['unit_price'];
            $itemName = $it['item_name'];

            $included = Arr::get($includedItemsQty, $itemId, null);
            $includedQty  = $included['qty']  ?? 0;
            $includedUnit = $included['unit'] ?? null;

            if ($includedQty <= 0) {
                $lineTotal = $uPrice * $qtyUsed;
                $this->extra_items[] = [
                    'item_id'   => $itemId,
                    'item_name' => $itemName,
                    'quantity'  => $qtyUsed,
                    'unit'      => $unit,
                    'unit_price'=> $uPrice,
                    'total'     => $lineTotal,
                    'reason'    => 'مستلزم غير مضمن ضمن باكدج العملية',
                ];
            } else {
                $extraQty = max($qtyUsed - $includedQty, 0);
                $this->included_items[] = [
                    'item_id'            => $itemId,
                    'item_name'          => $itemName,
                    'included_quantity'  => $includedQty,
                    'used_quantity'      => $qtyUsed,
                    'unit'               => $unit ?? $includedUnit,
                    'note'               => $extraQty > 0
                        ? "ضمن الباكدج حتى {$includedQty}، والزيادة تتحاسب"
                        : 'ضمن الباكدج بالكامل (لا رسوم إضافية)',
                ];

                if ($extraQty > 0) {
                    $lineTotal = $uPrice * $extraQty;
                    $this->extra_items[] = [
                        'item_id'   => $itemId,
                        'item_name' => $itemName,
                        'quantity'  => $extraQty,
                        'unit'      => $unit ?? $includedUnit,
                        'unit_price'=> $uPrice,
                        'total'     => $lineTotal,
                        'reason'    => "كمية زائدة عن المضمن ({$includedQty})",
                    ];
                }
            }
        }

        $this->extras_total =
            array_sum(array_column($this->extra_services, 'total')) +
            array_sum(array_column($this->extra_items, 'total'));

        $this->grand_total = $this->package_price + $this->extras_total;
    }

    protected function distributeTeamFees(): void
    {
        if (!$this->contractId || !$this->surgeryId || $this->medical_team->isEmpty()) return;

        $pricing = operation_contract_pricing::where('contract_id', $this->contractId)
            ->where('surgery_id', $this->surgeryId)
            ->first();

        if (!$pricing) return;

        $roleTotals = [
            'surgeon'            => (float) ($pricing->surgeon_fee      ?? 0),
            'assistant_surgeon'  => (float) ($pricing->assistant_fee    ?? 0),
            'anesthesiologist'   => (float) ($pricing->anesthesia_fee   ?? 0),
            'nurse'              => (float) ($pricing->nursing_fee      ?? 0),
        ];

        $counts = $this->medical_team->groupBy(fn ($m) => strtolower(trim((string)$m->role)))->map->count();

        $this->medical_team = $this->medical_team->map(function ($member) use ($roleTotals, $counts) {
            $role = strtolower(trim((string)$member->role));
            $totalForRole = $roleTotals[$role] ?? 0.0;
            $countForRole = max(1, (int) ($counts[$role] ?? 1));
            $distributed  = $totalForRole / $countForRole;

            $member->computed_fee = $distributed;
            if (!is_numeric($member->fee) || (float)$member->fee <= 0) {
                $member->fee = $distributed;
            }
            return $member;
        });
    }

    // ====== فتح المودال وبناء معاينة الخدمات المضمنة ======
    public function openAddPackageModal(): void
    {
        $this->packageServicesPreview = [];

        // ⛳️ تأكيد وجود عملية محددة
        if (!$this->surgeryId) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'error', 'message' => 'لا توجد عملية محددة لربط خدمات الباكدج.']);
            $this->showAddPackageModal = true;
            $this->dispatchBrowserEvent('show-add-package-modal');
            return;
        }

        // لو already محضر included_services من الحسابات السابقة نستخدمه، وإلا نقرأ من جدول الربط
        if (empty($this->included_services)) {
            $included = SurgeryServiceItem::query()
                ->where('surgery_id', $this->surgeryId) // ✅ إصلاح الهاردكود 93
                ->get(['service_id','quantity']);

            if ($included->isEmpty()) {
                $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'message' => 'لا توجد خدمات مضمنة معرفة لهذه العملية.']);
                $this->showAddPackageModal = true;
                $this->dispatchBrowserEvent('show-add-package-modal');
                return;
            }

            $baseIncluded = $included->map(fn($r) => [
                'service_id'   => (int)$r->service_id,
                'service_name' => null,
                'quantity'     => (float)($r->quantity ?? 1),
            ])->values()->all();
        } else {
            $baseIncluded = $this->included_services;
        }

        // ⚙️ جلب بيانات الخدمات دفعة واحدة لتفادي N+1
        $serviceIds = array_values(array_unique(array_map(fn($r) => (int)($r['service_id'] ?? 0), $baseIncluded)));
        $servicesMap = [];
        if (!empty($serviceIds)) {
            // ❗ لا نطلب name_ar لأنه غير موجود في fin_services
            $svcRows = FinanceService::whereIn('id', $serviceIds)->get(['id','name']); // price قد لا يكون موجودًا
            foreach ($svcRows as $svc) {
                // احترام الترجمة لو Spatie متاح
                $localized = method_exists($svc, 'getTranslation')
                    ? ($svc->getTranslation('name', app()->getLocale()) ?: $svc->name)
                    : $svc->name;

                $servicesMap[(int)$svc->id] = [
                    'name'       => $localized ?: ('Service#'.$svc->id),
                    'unit_price' => (float)($svc->price ?? 0), // لو price غير موجود → 0
                ];
            }
        }

        // بناء الصفوف الأولية
        foreach ($baseIncluded as $row) {
            $serviceId   = (int)($row['service_id'] ?? 0);
            $qty         = (float)($row['quantity'] ?? 1);
            $serviceName = $row['service_name'] ?? ($servicesMap[$serviceId]['name'] ?? ('Service#'.$serviceId));
            $unitPrice   = $servicesMap[$serviceId]['unit_price'] ?? 0.0;

            // fallback من الخدمات المؤداة لو موجودة
            if ($unitPrice <= 0) {
                $fallbackSrv = $this->services->firstWhere('service_id', $serviceId);
                if ($fallbackSrv) {
                    $unitPrice = (float)($fallbackSrv->computed_unit_price ?? 0);
                }
            }

            $total          = $unitPrice * $qty;
            $contractValue  = $total;
            $patientValue   = 0.0;

            $this->packageServicesPreview[] = [
                'service_id'     => $serviceId,
                'service_name'   => $serviceName,
                'quantity'       => $qty,
                'unit_price'     => $unitPrice,
                'total_price'    => $total,
                'contract_value' => $contractValue,
                'patient_value'  => $patientValue,
                'discount'       => 0,
            ];
        }

        // 🟦 توزيع ذكي لسعر الباكدج إذا كانت الأسعار = 0
        $sumTotals = array_sum(array_column($this->packageServicesPreview, 'total_price'));
        if ($sumTotals <= 0 && $this->package_price > 0 && count($this->packageServicesPreview) > 0) {
            // وزّع حسب الكمية (ولو كل الكميات 0 أو 1 هنقسّم بالتساوي)
            $qtySum = array_sum(array_map(fn($r) => (float)$r['quantity'], $this->packageServicesPreview));
            if ($qtySum <= 0) {
                // تقسيم بالتساوي
                $equalUnit = $this->package_price / count($this->packageServicesPreview);
                foreach ($this->packageServicesPreview as &$r) {
                    $r['unit_price']     = $equalUnit;       // كمية 1 افتراضيًا
                    $r['total_price']    = $equalUnit;
                    $r['contract_value'] = $equalUnit;
                    $r['patient_value']  = 0.0;
                }
                unset($r);
            } else {
                // توزيع نسبي حسب الكمية
                $perQty = $this->package_price / $qtySum;
                foreach ($this->packageServicesPreview as &$r) {
                    $r['unit_price']     = $perQty;
                    $r['total_price']    = $perQty * (float)$r['quantity'];
                    $r['contract_value'] = $r['total_price'];
                    $r['patient_value']  = 0.0;
                }
                unset($r);
            }
        }

        $this->showAddPackageModal = true;
        $this->dispatchBrowserEvent('show-add-package-modal');
        
    }

    public function confirmAddPackageToInvoice(): void
    {
        if (empty($this->packageServicesPreview)) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'message' => 'لا توجد خدمات لإضافتها.']);
            return;
        }

        $reservationId = $this->booking->id;

        $this->addPackageServicesToInvoice($reservationId, $this->packageServicesPreview);

        $this->invoices = invoices::where('patient_id', $this->booking->patient_id)
            ->where('reservation_id', $this->booking->id)
            ->get();

        $this->totalServices = $this->services->sum(fn ($s) => (float) ($s->computed_total ?? 0));
        $this->totalPayments = (float) $this->payments->sum(fn ($p) => (float) ($p->amount ?? 0));
        $this->balance = $this->totalPayments - $this->totalServices;

        $this->showAddPackageModal = false;
        $this->dispatchBrowserEvent('hide-add-package-modal');
        $this->dispatchBrowserEvent('toastr', ['type' => 'success', 'message' => 'تم إضافة خدمات العملية للفاتورة بنجاح.']);
    }

    public function render()
    {
        return view('livewire.finance.program.booking-details');
    }
}