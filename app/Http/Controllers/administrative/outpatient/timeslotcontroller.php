<?php

namespace App\Http\Controllers\administrative\outpatient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\administrative\scheduling_settings;
use App\models\administrative\reservations;
use Carbon\Carbon;

class timeslotcontroller extends Controller
{
    public function getTimeSlots(Request $request)
    {
        $date = $request->input('booking_date');
        $dayName = Carbon::parse($date)->format('l');

        $scheduling_settings = scheduling_settings::where('id_doctor', $request->input('id_doctor'))
            ->where('id_clinic', $request->input('id_clinic'))
            ->where('status', 'active')
            ->where('day', $dayName)
            ->latest()
            ->first();

        $time_slot_reservations = reservations::where('id_doctor', $request->input('id_doctor'))
            ->where('date_reservations', $date)
            ->whereNot('status', 'cancelled')
            ->select('time_slot')
            ->get();

        if ($scheduling_settings) {
            $start_time = $scheduling_settings->first_shift_from;
            $end_time = $scheduling_settings->first_shift_to;
            $duration = 20;
            $break = 0;

            // تحديد الوقت الحالي لو التاريخ هو اليوم
            $current_time = null;
            if (Carbon::parse($date)->isToday()) {
                $current_time = Carbon::now()->format('H:i');
            }

            // Call the show_time_slots function with the current_time parameter
            $time_slots = $this->show_time_slots($start_time, $end_time, $duration, $break, $current_time);

            return response()->json([
                'time_slots' => $time_slots,
                'time_slot_reservations' => $time_slot_reservations
            ]);
        } else {
            return response()->json('no data');
        }
    }

    public function show_time_slots($start_time, $end_time, $duration, $break, $current_time = null)
    {
        $time_slots = [];

        $start = \DateTime::createFromFormat('H:i', $start_time);
        $end = \DateTime::createFromFormat('H:i', $end_time);

        $total_duration = $end->diff($start)->h * 60 + $end->diff($start)->i;
        $number_of_slots = floor(($total_duration - $break) / $duration);

        for ($i = 0; $i < $number_of_slots; $i++) {
            $slot_start = clone $start;
            $slot_start->add(new \DateInterval('PT' . ($duration * $i) . 'M'));

            $slot_end = clone $slot_start;
            $slot_end->add(new \DateInterval('PT' . $duration . 'M'));
            $slot_end->add(new \DateInterval('PT' . $break . 'M'));

            $slot_start_formatted = $slot_start->format('H:i');
            $slot_end_formatted = $slot_end->format('h:i A');

            // لو في current_time وتوقيت النهاية أقل منه، تخطي هذا الموعد
            if ($current_time) {
                if ($slot_end->format('H:i') <= $current_time) {
                    continue;
                }
            }

            $time_slots[] = [
                $slot_end_formatted
            ];
        }

        return $time_slots;
    }
}