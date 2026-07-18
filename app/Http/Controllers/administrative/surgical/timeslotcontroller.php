<?php
namespace App\Http\Controllers\administrative\surgical;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\administrative\adm_s_operation_schedules;
use App\models\administrative\adm_b_surgery_bookings;
use Carbon\Carbon;
class timeslotcontroller extends Controller
{
public function getTimeSlots(Request $request)
{
    $date = $request->input('booking_date');
    $dayName = Carbon::parse($date)->format('l');

    // جلب إعدادات الجدولة لغرفة العمليات بناءً على اليوم
    $scheduling_settings = adm_s_operation_schedules::where('id_room', $request->input('id_room'))
        ->where('day', $dayName)
        ->latest()
        ->first();

    // جلب جميع الحجوزات في نفس الغرفة والتاريخ
    $time_slot_reservations = adm_b_surgery_bookings::where('room_id', $request->input('id_room'))
        ->where('booking_date', $date)
        ->whereNot('status', 'cancelled')
        ->select('scheduled_from', 'scheduled_to')
        ->get();

    if ($scheduling_settings) {
        $start_time = $scheduling_settings->start_time;
        $end_time = $scheduling_settings->end_time;
        $duration = $scheduling_settings->duration;
        $break = 0;

        // إنشاء قائمة الفتحات الزمنية
        $time_slots = $this->show_time_slots($start_time, $end_time, $duration, $break);

        // التأكد من أن الفتحات المحجوزة مغلقة
        foreach ($time_slots as &$slot) {
            foreach ($time_slot_reservations as $reservation) {
                if ($this->isTimeSlotReserved($slot['start'], $slot['end'], $reservation->scheduled_from, $reservation->scheduled_to)) {
                    $slot['reserved'] = true;
                    break;
                }
            }
        }

        return response()->json(['time_slots' => $time_slots]);
    } else {
        return response()->json('no data');
    }
}

/**
 * التحقق مما إذا كانت الفتحة الزمنية متداخلة مع حجز معين
 */
private function isTimeSlotReserved($slotStart, $slotEnd, $reservedFrom, $reservedTo)
{
    return ($slotStart >= $reservedFrom && $slotStart < $reservedTo) ||
           ($slotEnd > $reservedFrom && $slotEnd <= $reservedTo) ||
           ($slotStart <= $reservedFrom && $slotEnd >= $reservedTo);
}

public function show_time_slots($start_time, $end_time, $duration, $break)
{
    $time_slots = [];

    $bookingDate = request()->input('booking_date'); // التاريخ المطلوب
    $todayDate = Carbon::today()->toDateString();     // تاريخ اليوم
    $nowTime = Carbon::now()->format('H:i');          // الوقت الحالي

    // ✳️ لو التاريخ أقدم من اليوم ➜ رجع قائمة فاضية
    if ($bookingDate < $todayDate) {
        return [];
    }

    $start = \DateTime::createFromFormat('H:i', $start_time);
    $end = \DateTime::createFromFormat('H:i', $end_time);
    $current_time = clone $start;

    while ($current_time < $end) {
        $slot_start = clone $current_time;
        $slot_end = clone $current_time;
        $slot_end->add(new \DateInterval('PT' . $duration . 'M'));

        if ($slot_end > $end) break;

        // ✳️ لو التاريخ اليوم الحالي، وحددنا أن الفترة قد مضت ➜ لا تضفها
        if ($bookingDate === $todayDate && $slot_end->format('H:i') <= $nowTime) {
            $current_time = clone $slot_end;
            $current_time->add(new \DateInterval('PT' . $break . 'M'));
            continue;
        }

        $time_slots[] = [
            'start' => $slot_start->format('H:i'),
            'end' => $slot_end->format('H:i'),
            'reserved' => false // هتتحدث لاحقًا حسب الحجوزات
        ];

        $current_time = clone $slot_end;
        $current_time->add(new \DateInterval('PT' . $break . 'M'));
    }

    return $time_slots;
}


}