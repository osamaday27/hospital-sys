<?php

namespace App\Traits\Finance;

use Illuminate\Support\Facades\DB;
use App\models\administrative\reservations;
use App\models\finance\invoices;
use App\models\finance\finservicevisit;

trait invoiceservicetrait
{
    /**
     * جلب أو إنشاء فاتورة واحدة للحجز (ضمان فاتورة واحدة فقط)
     *
     * @param int $reservationId
     * @return \App\models\finance\invoices
     */
    public function getOrCreateInvoiceForReservation(int $reservationId): invoices
    {
        return DB::transaction(function () use ($reservationId) {
            $reservation = reservations::findOrFail($reservationId);

            // ابحث عن فاتورة للحجز
            $invoice = invoices::where('reservation_id', $reservationId)->first();
            if ($invoice) {
                return $invoice;
            }

            // إنشاء فاتورة جديدة مرتبطة بالحجز (واحدة فقط)
            return invoices::create([
                'reservation_id'         => $reservationId,
                'patient_id'             => $reservation->patient_id,
                'visit_id'               => $reservation->visit_id,
                'number'                 => invoices::max('id') + 1, // أو من سيكوانس حسب نظامك
                'date'                   => now()->toDateString(),
                'status'                 => 'unpaid',
                'currency'               => 'EGP',
                'total'                  => 0,
                'contract_contribution'  => 0,
                'patient_contribution'   => 0,
                'residual'               => 0,
            ]);
        });
    }

    /**
     * إضافة خدمات الباكدج دفعة واحدة للفاتورة (بدون اختيار يدوي)
     * المبدأ الافتراضي: العقد يغطي الخدمة بالكامل (patient_value = 0)
     *
     * @param int   $reservationId
     * @param array $servicesList  [['service_id','quantity','unit_price','total_price','contract_value','patient_value','discount']]
     * @return void
     */
    public function addPackageServicesToInvoice(int $reservationId, array $servicesList): void
    {
        DB::transaction(function () use ($reservationId, $servicesList) {
            $reservation = reservations::findOrFail($reservationId);

            // ضمان فاتورة واحدة فقط للحجز
            $invoice = $this->getOrCreateInvoiceForReservation($reservationId);

            $sumTotal    = 0.0;
            $sumPatient  = 0.0;
            $sumContract = 0.0;

            foreach ($servicesList as $row) {
                finservicevisit::create([
                    'id_invoices'           => $invoice->id,
                    'id_visit'              => $reservation->visit_id,
                    'id_services'           => $row['service_id'],
                    'quantity'              => $row['quantity'],
                    'total_pricing'         => $row['total_price'],
                    'contract_contribution' => $row['contract_value'],
                    'patient_contribution'  => $row['patient_value'],
                    'discount'              => $row['discount'] ?? 0,
                    'currency'              => 'EGP',
                    'id_patient'            => $reservation->patient_id,
                ]);

                $sumTotal    += (float) $row['total_price'];
                $sumPatient  += (float) $row['patient_value'];
                $sumContract += (float) $row['contract_value'];
            }

            // تحديث الفاتورة
            $invoice->increment('total', $sumTotal);
            $invoice->increment('patient_contribution', $sumPatient);
            $invoice->increment('contract_contribution', $sumContract);

            // المتبقي على المريض (حسب منطقك الحالي)
            $invoice->decrement('residual', $sumPatient);

            // الحالة
            if ($invoice->residual == 0 && ($invoice->patient_contribution ?? 0) > 0) {
                $invoice->update(['status' => 'paid']);
            } elseif ($invoice->status !== 'partial' && ($invoice->patient_contribution ?? 0) > 0) {
                $invoice->update(['status' => 'partial']);
            }
        });
    }

    /**
     * (كما أرسلت سابقًا) إضافة خدمات مُختارة يدويًا — أبقيتها كما هي دون حذف
     */
    public function addServicesToInvoice(int $reservationId, array $servicesList): void
    {
        DB::transaction(function () use ($reservationId, $servicesList) {
            // جلب بيانات الحجز والفاتورة
            $reservation = reservations::findOrFail($reservationId);
            $invoice     = invoices::where('reservation_id', $reservationId)->firstOrFail();

            // مجاميع الأسعار
            $sumTotal    = 0;
            $sumPatient  = 0;
            $sumContract = 0;

            foreach ($servicesList as $row) {
                // إنشاء سجل الخدمة
                finservicevisit::create([
                    'id_invoices'           => $invoice->id,
                    'id_visit'              => $reservation->visit_id,
                    'id_services'           => $row['service_id'],
                    'quantity'              => $row['quantity'],
                    'total_pricing'         => $row['total_price'],
                    'contract_contribution' => $row['contract_value'],
                    'patient_contribution'  => $row['patient_value'],
                    'discount'              => $row['unit_price'] - $row['patient_value'],
                    'currency'              => 'EGP',
                    'id_patient'            => $reservation->patient_id,
                ]);

                // تحديث المجاميع
                $sumTotal    += $row['total_price'];
                $sumPatient  += $row['patient_value'];
                $sumContract += $row['contract_value'];
            }

            // تحديث الفاتورة
            $invoice->increment('total', $sumTotal);
            $invoice->increment('patient_contribution', $sumPatient);
            $invoice->increment('contract_contribution', $sumContract);
            $invoice->decrement('residual', $sumPatient);

            // تحديث حالة الفاتورة
            if ($invoice->residual == 0) {
                $invoice->update(['status' => 'paid']);
            } elseif ($invoice->status !== 'partial') {
                $invoice->update(['status' => 'partial']);
            }
        });
    }

    /**
     * (كما أرسلت سابقًا) حذف خدمة من الفاتورة — أبقيتها كما هي دون حذف
     */
    public function removeServiceFromInvoice(int $reservationId, array $item): void
    {
        DB::transaction(function () use ($reservationId, $item) {
            // حذف السجل
            finservicevisit::destroy($item['record_id']);

            // جلب الفاتورة
            $invoice = invoices::where('reservation_id', $reservationId)->firstOrFail();

            // تحديث قيم الفاتورة
            $invoice->decrement('total', $item['total_price']);
            $invoice->decrement('contract_contribution', $item['contract_value']);
            $invoice->decrement('patient_contribution', $item['patient_value']);
            $invoice->increment('residual', $item['patient_value']);

            // تعديل الحالة إذا كانت مدفوعة بالكامل
            if ($invoice->status === 'paid') {
                $invoice->update(['status' => 'partial']);
            }
        });
    }
}