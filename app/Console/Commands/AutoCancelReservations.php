<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\administrative\reservations;
use Carbon\Carbon;

class AutoCancelReservations extends Command
{
    protected $signature = 'reservations:auto-cancel';
    protected $description = 'إلغاء الحجوزات التي لم يحضر أصحابها في يوم الحجز';

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        $reservations = reservations::whereDate('date_visit', $today)
            ->whereNull('arrival_confirmed_at')
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->status = 'cancelled';
            $reservation->cancel_reason_id = null; // يتسجل لاحقًا من الكول سنتر
            $reservation->cancel_note = 'إلغاء أوتوماتيكي لعدم الحضور';
            $reservation->save();
        }

        $this->info("تم إلغاء {$reservations->count()} حجز لعدم الحضور.");
    }
}