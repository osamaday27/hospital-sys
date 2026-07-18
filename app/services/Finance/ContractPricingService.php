<?php
// app/Services/Finance/ContractPricingService.php

namespace App\Services\Finance;

use App\models\finance\service as FinService;
use App\models\finance\price_lists as FinPriceList;
use App\models\finance\pricelistservice as FinPriceListService;
use App\models\finance\contract_frist_level as FinContractFirstLevel;
use App\models\finance\contract_second_level as FinContractSecondLevel;
use App\models\finance\contract_last_level as FinContractLastLevel;
use App\models\finance\contracts_details_second_level as FinContractsDetailsSecondLevel;
use App\models\finance\contracts_details_last_level as FinContractsDetailsLastLevel;

/**
 * خدمة تسعير الخدمة بناءً على التعاقد (طبقات: First/Second/Last)
 * - تجيب سعر اللائحة (hospital_price/special_price)
 * - تحدد الخصم ومساهمة المريض (عام أو حسب التصنيف)
 * - تحدد إن كانت الخدمة مغطاة أم غير مغطاة
 * - تحدد إن كانت تحتاج موافقة
 * - تحسب صافي جهة السداد ومبلغ المريض المطلوب
 */
class ContractPricingService
{
    /**
     * احسب تسعير خدمة ضمن تعاقد.
     *
     * @param  int|\App\models\finance\service $service   معرّف الخدمة أو الموديل
     * @param  array $contractRef [
     *     'level' => 'last'|'second'|'first',  // مستوى التعاقد الذي تتعامل معه مباشرة
     *     'id'    => (int),                    // ID للتعاقد في المستوى المحدد
     *     // اختياري: لو حابب تمرّر price_list_id مباشرة وتلغي عملية الاستنتاج
     *     'price_list_id' => (int|null),
     * ]
     * @param  array $options [
     *     'category' => null|string,          // تصنيف الخدمة لو مش موجود بجدول الخدمة
     *     'prefer_special_price' => bool,     // اختيار special إذا متاح (افتراضي true)
     *     'qty' => float|int                  // كمية الخدمة (افتراضي 1)
     * ]
     * @return array
     */
    public function calculate($service, array $contractRef, array $options = []): array
    {
        // 0) جلب موديل الخدمة لو وصل ID
        $serviceModel = $service instanceof FinService ? $service : FinService::find($service);
        if (!$serviceModel) {
            return $this->resultError('service_not_found', 'الخدمة غير موجودة.');
        }

        $qty  = (float)($options['qty'] ?? 1);
        $pref = (bool)($options['prefer_special_price'] ?? true);
        $category = $options['category'] ?? ($serviceModel->category ?? null);

        // 1) تحميل التعاقد + تفاصيله حسب المستوى
        $level = strtolower($contractRef['level'] ?? 'last');
        $id    = (int)($contractRef['id'] ?? 0);

        [$contract, $details] = $this->loadContractAndDetails($level, $id);
        if (!$contract) {
            return $this->resultError('contract_not_found', 'التعاقد المحدد غير موجود.');
        }

        // 2) تحديد لائحة الأسعار
        $priceListId = $contractRef['price_list_id'] ?? $this->resolvePriceListId($contract, $level);
        if (!$priceListId) {
            $priceListId = $this->fallbackResolvePriceListId($contract, $level);
        }
        if (!$priceListId) {
            return $this->resultError('price_list_not_found', 'تعذّر تحديد لائحة الأسعار للتعاقد.');
        }

        // 3) سعر الخدمة من اللائحة
        $pls = FinPriceListService::where('price_list_id', $priceListId)
            ->where('service_id', $serviceModel->id)
            ->first();

        if (!$pls) {
            // غير مُسعّرة في اللائحة ⇒ خارج التعاقد
            $basePrice       = 0.0;
            $covered         = false;
            $outsideContract = true;
        } else {
            $basePrice       = $this->chooseBasePrice($pls, $pref);
            $covered         = true;
            $outsideContract = false;
        }

        // 4) فحص التغطية/الاعتماد من تفاصيل التعاقد (إن وُجدت)
        [$requiresApproval, $isCoveredOverride, $reasonTag] = $this->checkCoverageAndApproval($details, $serviceModel->id);
        if ($isCoveredOverride === false) {
            $covered = false;
            $outsideContract = true;
        }

        // 5) نسب الخصم ومساهمة المريض (عام ثم override بالتصنيف)
        [$discPct, $patientPct, $sourceKeys] = $this->resolvePercentages($details, $category);

        // 6) الحسابات
        $gross = round($basePrice * $qty, 2);

        if (!$covered || $outsideContract) {
            // خارج التعاقد: المريض يدفع كامل المبلغ
            return $this->formatResult([
                'covered'                      => false,
                'outside_contract'             => true,
                'requires_approval'            => $requiresApproval,
                'price_list_id'                => $priceListId,
                'hospital_price'               => $pls->hospital_price ?? null,
                'special_price'                => $pls->special_price ?? null,
                'used_price'                   => $basePrice,
                'qty'                          => $qty,
                'gross'                        => $gross,
                'discount_percentage'          => 0.0,
                'patient_contribution_percent' => 100.0,
                'discount_amount'              => 0.0,
                'patient_contribution_amount'  => $gross,
                'payer_net'                    => 0.0,
                'net_payable'                  => $gross,
                'notes'                        => $reasonTag ?: 'خارج نطاق التعاقد/غير مُسعّر في اللائحة.',
                'percent_source'               => $sourceKeys,
            ]);
        }

        // داخل التعاقد
        $discAmount    = round(($discPct / 100.0) * $gross, 2);
        $afterDiscount = max(0.0, round($gross - $discAmount, 2));

        $patientAmount = round(($patientPct / 100.0) * $afterDiscount, 2);
        $payerNet      = max(0.0, round($afterDiscount - $patientAmount, 2));
        $netPayable    = round($patientAmount + $payerNet, 2);

        return $this->formatResult([
            'covered'                      => true,
            'outside_contract'             => false,
            'requires_approval'            => $requiresApproval,
            'price_list_id'                => $priceListId,
            'hospital_price'               => $pls->hospital_price ?? null,
            'special_price'                => $pls->special_price ?? null,
            'used_price'                   => $basePrice,
            'qty'                          => $qty,
            'gross'                        => $gross,
            'discount_percentage'          => $discPct,
            'patient_contribution_percent' => $patientPct,
            'discount_amount'              => $discAmount,
            'patient_contribution_amount'  => $patientAmount,
            'payer_net'                    => $payerNet,
            'net_payable'                  => $netPayable,
            'notes'                        => $reasonTag,
            'percent_source'               => $sourceKeys,
        ]);
    }

    /* =========================
       يساعدات داخلية
    ==========================*/

    /**
     * تحميل التعاقد + التفاصيل حسب المستوى.
     * ملاحظة: موديل contract_second_level عندك مفيهوش علاقة details، فنجيبها يدويًا.
     */
    protected function loadContractAndDetails(string $level, int $id): array
    {
        switch ($level) {
            case 'last':
                // last → secondLevel → frist_level → priceList
                $c = FinContractLastLevel::with([
                    'details',
                    'secondLevel.frist_level.priceList',
                ])->find($id);
                return [$c, $c?->details];

            case 'second':
                $c = FinContractSecondLevel::with([
                    'frist_level.priceList',
                ])->find($id);

                $details = $c ? FinContractsDetailsSecondLevel::where('second_level_id', $c->id)->first() : null;
                return [$c, $details];

            case 'first':
                $c = FinContractFirstLevel::with(['priceList'])->find($id);
                return [$c, null];

            default:
                return [null, null];
        }
    }

    /**
     * تحديد أقرب لائحة أسعار متاحة من نفس المستوى.
     */
    protected function resolvePriceListId($contract, string $level): ?int
    {
        if ($level === 'first' && !empty($contract?->price_list_id)) {
            return (int)$contract->price_list_id;
        }

        if ($level === 'second') {
            // في موديلك مفيش price_list_id على Second Level
            // ناخد من first (عبر frist_level)
            if (!empty($contract?->frist_level?->priceList?->id)) {
                return (int)$contract->frist_level->priceList->id;
            }
            // احتياط لو مسميها firstLevel أيضًا
            if (!empty($contract?->firstLevel?->priceList?->id)) {
                return (int)$contract->firstLevel->priceList->id;
            }
        }

        if ($level === 'last') {
            // last → secondLevel → frist_level → priceList
            if (!empty($contract?->secondLevel?->frist_level?->priceList?->id)) {
                return (int)$contract->secondLevel->frist_level->priceList->id;
            }
            // احتياط لو مسميها firstLevel
            if (!empty($contract?->secondLevel?->firstLevel?->priceList?->id)) {
                return (int)$contract->secondLevel->firstLevel->priceList->id;
            }
        }

        return null;
    }

    /**
     * محاولات بديلة لتحديد لائحة الأسعار من أي مستوى أعلى/أسفل.
     */
    protected function fallbackResolvePriceListId($contract, string $level): ?int
    {
        // مباشرة إن وُجدت علاقة priceList على الكيان الحالي
        if (!empty($contract?->priceList?->id)) {
            return (int)$contract->priceList->id;
        }

        // لو Last Level
        if (!empty($contract?->secondLevel?->frist_level?->priceList?->id)) {
            return (int)$contract->secondLevel->frist_level->priceList->id;
        }
        if (!empty($contract?->secondLevel?->firstLevel?->priceList?->id)) {
            return (int)$contract->secondLevel->firstLevel->priceList->id;
        }

        // لو Second Level
        if (!empty($contract?->frist_level?->priceList?->id)) {
            return (int)$contract->frist_level->priceList->id;
        }
        if (!empty($contract?->firstLevel?->priceList?->id)) {
            return (int)$contract->firstLevel->priceList->id;
        }

        return null;
    }

    protected function chooseBasePrice(FinPriceListService $pls, bool $preferSpecial): float
    {
        $h = (float)($pls->hospital_price ?? 0);
        $s = (float)($pls->special_price ?? 0);

        if ($preferSpecial && $s > 0) {
            return round($s, 2);
        }
        return round($h, 2);
    }

    /**
     * فحص قوائم الخدمات التي تحتاج موافقة أو غير مغطاة (IDs مفصولة بفواصل/أسطر).
     */
    protected function checkCoverageAndApproval($details, int $serviceId): array
    {
        $requiresApproval = false;
        $coveredOverride  = null; // true/false/null
        $reasonTag        = null;

        if (!$details) {
            return [$requiresApproval, $coveredOverride, $reasonTag];
        }

        $approvalList = $this->explodeList($details->services_require_approval);
        $notCovered   = $this->explodeList($details->services_not_covered);

        if (in_array((string)$serviceId, $approvalList, true)) {
            $requiresApproval = true;
            $reasonTag = 'الخدمة تتطلب موافقة مسبقة حسب بنود التعاقد.';
        }

        if (in_array((string)$serviceId, $notCovered, true)) {
            $coveredOverride = false;
            $reasonTag = 'الخدمة غير مغطاة حسب بنود التعاقد.';
        }

        return [$requiresApproval, $coveredOverride, $reasonTag];
    }

    protected function explodeList(?string $text): array
    {
        if (!$text) return [];
        $parts = preg_split('/[,\r\n]+/u', $text) ?: [];
        return array_values(array_filter(array_map('trim', $parts), fn($v) => $v !== ''));
    }

    /**
     * تحديد نسب الخصم ومساهمة المريض (عام ثم حسب التصنيف إن وجد).
     * التصنيفات المدعومة: clinic, inpatient, surgical, lab, emergency, rad, medications, supplies
     */
    protected function resolvePercentages($details, ?string $category): array
    {
        $discPct   = 0.0;
        $patientPt = 0.0;
        $source    = ['base' => 'default(0)'];

        if ($details) {
            if ($this->isNumber($details->discount_percentage_all_services)) {
                $discPct = (float)$details->discount_percentage_all_services;
                $source['discount'] = 'discount_percentage_all_services';
            }
            if ($this->isNumber($details->patient_contribution_all_services)) {
                $patientPt = (float)$details->patient_contribution_all_services;
                $source['patient']  = 'patient_contribution_all_services';
            }

            $map = [
                'clinic'      => ['discount' => 'discount_percentage_clinic_services',     'patient' => 'patient_contribution_clinic_services'],
                'inpatient'   => ['discount' => 'discount_percentage_inpatient_services',  'patient' => 'patient_contribution_inpatient_services'],
                'surgical'    => ['discount' => 'discount_percentage_surgical_services',   'patient' => 'patient_contribution_surgical_services'],
                'lab'         => ['discount' => 'discount_percentage_lab_services',        'patient' => 'patient_contribution_lab_services'],
                'emergency'   => ['discount' => 'discount_percentage_emergency_services',  'patient' => 'patient_contribution_emergency_services'],
                'rad'         => ['discount' => 'discount_percentage_rad_services',        'patient' => 'patient_contribution_rad_services'],
                'medications' => ['discount' => 'discount_percentage_medications',         'patient' => 'patient_contribution_medications'],
                'supplies'    => ['discount' => 'discount_percentage_supplies',            'patient' => 'patient_contribution_supplies'],
            ];

            $key = $category ? strtolower($category) : null;
            if ($key && isset($map[$key])) {
                $dKey = $map[$key]['discount'];
                $pKey = $map[$key]['patient'];

                if ($this->isNumber($details->{$dKey} ?? null)) {
                    $discPct = (float)$details->{$dKey};
                    $source['discount'] = $dKey;
                }
                if ($this->isNumber($details->{$pKey} ?? null)) {
                    $patientPt = (float)$details->{$pKey};
                    $source['patient'] = $pKey;
                }
            }
        }

        $discPct   = max(0.0, min(100.0, $discPct));
        $patientPt = max(0.0, min(100.0, $patientPt));

        return [$discPct, $patientPt, $source];
    }

    protected function isNumber($v): bool
    {
        if ($v === null) return false;
        return is_numeric($v);
    }

    protected function formatResult(array $data): array
    {
        $defaults = [
            'covered'                      => false,
            'outside_contract'             => true,
            'requires_approval'            => false,
            'price_list_id'                => null,
            'hospital_price'               => null,
            'special_price'                => null,
            'used_price'                   => 0.0,
            'qty'                          => 1,
            'gross'                        => 0.0,
            'discount_percentage'          => 0.0,
            'patient_contribution_percent' => 0.0,
            'discount_amount'              => 0.0,
            'patient_contribution_amount'  => 0.0,
            'payer_net'                    => 0.0,
            'net_payable'                  => 0.0,
            'notes'                        => null,
            'percent_source'               => [],
        ];
        return array_replace($defaults, $data);
    }

    protected function resultError(string $code, string $message): array
    {
        return $this->formatResult([
            'notes' => $code . ': ' . $message,
        ]);
    }
}