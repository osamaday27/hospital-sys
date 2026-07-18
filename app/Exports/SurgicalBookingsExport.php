<?php
namespace App\Exports;

use App\models\administrative\adm_b_surgery_bookings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Traits\web\get_contract;

class SurgicalBookingsExport implements FromCollection, WithHeadings
{
    use get_contract;

    public function collection()
    {
        // نجلب الحجوزات مع العلاقات اللازمة (المريض، الغرفة، الخدمات + pivot للاتجاه)
        $bookings = adm_b_surgery_bookings::with([
            'patient',
            'room',
            'services' => function ($q) {
                // للتأكيد على جلب بيانات الـ pivot (الطرف/الاتجاه)
                $q->withPivot('side');
            },
        ])->get();

        return $bookings->map(function ($booking) {
            $contractId   = $booking->patient->insurance_contracting ?? null;
            $contractName = $contractId ? $this->getcontractname($contractId) : '-';

            // اسم الغرفة بحسب اللغة
            $roomName = '-';
            if ($booking->room) {
                $roomName = app()->getLocale() === 'ar'
                    ? ($booking->room->name_ar ?? ($booking->room->name_en ?? '-'))
                    : ($booking->room->name_en ?? ($booking->room->name_ar ?? '-'));
            }

            // تجهيز أسماء الخدمات مع الاتجاه لكل خدمة
            // مثال: "Appendectomy - Right, Hernia Repair - Left"
            $servicesWithSide = $booking->services && $booking->services->count()
                ? $booking->services->map(function ($srv) {
                    $side = $srv->pivot->side ?? null;
                    $label = $this->sideLabel($side);
                    // في حالة عدم وجود اتجاه نكتفي بالاسم فقط
                    return trim($srv->name . ($label !== null ? (' - ' . $label) : ''));
                })->implode(', ')
                : '-';

            // عمود مستقل للاتجاهات فقط (في حال احتجته)
            // مثال: "Right, Left" موازي لترتيب الخدمات
            $sidesOnly = $booking->services && $booking->services->count()
                ? $booking->services->map(function ($srv) {
                    return $this->sideLabel($srv->pivot->side ?? null) ?? '-';
                })->implode(', ')
                : '-';

            return [
                'ID'             => $booking->id,
                'Patient Name'   => $booking->patient->full_name ?? '-',
                'Medical Number' => $booking->patient->medical_number ?? '-',
                'Age'            => $booking->patient->age ?? '-',
                'Phone'          => $booking->patient->phone_number ?? '-',
                'Contract'       => $contractName,
                'Room'           => $roomName,
                'Service(s)'     => $servicesWithSide, // الاسم + الاتجاه
                'Side(s)'        => $sidesOnly,        // الاتجاهات فقط كعمود مستقل
                'Booking Date'   => $booking->booking_date,
                'Status'         => $booking->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patient Name',
            'Medical Number',
            'Age',
            'Phone',
            'Contract',
            'Room',
            'Service(s)', // أسماء الخدمات مع الاتجاه
            'Side(s)',    // الاتجاهات فقط
            'Booking Date',
            'Status',
        ];
    }

    /**
     * تحويل قيمة الـ side إلى تسمية واضحة.
     * يقبل: right | left | both | null/غير ذلك
     */
    private function sideLabel(?string $side): ?string
    {
        if (!$side) {
            return null; // نُعيد null عشان ما نحطش " - " بدون داعي
        }

        return match ($side) {
            'right' => 'Right',
            'left'  => 'Left',
            'both'  => 'Both',
            default => 'Not specified',
        };
    }
}