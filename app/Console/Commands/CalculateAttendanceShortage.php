<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\hr\hr_attendance;
use App\models\hr\Employee;
use Carbon\Carbon;

class CalculateAttendanceShortage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:calculate-shortage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and update shortage for all attendance records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to calculate shortage for all attendance records...');

        $attendanceRecords = hr_attendance::with(['employee.schedule.days'])->get();
        $updatedCount = 0;

        foreach ($attendanceRecords as $attendance) {
            $shortage = 0;
            
            if ($attendance->employee && $attendance->employee->schedule) {
                $checkInDate = Carbon::parse($attendance->check_in);
                $dayOfWeek = $checkInDate->dayOfWeek + 1; // Carbon uses 0-6, we use 1-7
                $scheduleDay = $attendance->employee->schedule->days->where('day_of_week', $dayOfWeek)->first();
                
                if ($scheduleDay && $scheduleDay->start_time && $scheduleDay->end_time) {
                    $scheduleStart = Carbon::parse($scheduleDay->start_time);
                    $scheduleEnd = Carbon::parse($scheduleDay->end_time);
                    $scheduledHours = $scheduleEnd->diffInMinutes($scheduleStart) / 60;
                    $shortage = max(0, $scheduledHours - $attendance->worked_hours);
                }
            }

            $attendance->update(['shortage' => number_format($shortage, 2)]);
            $updatedCount++;
            
            if ($updatedCount % 100 == 0) {
                $this->info("Processed {$updatedCount} records...");
            }
        }

        $this->info("Successfully updated shortage for {$updatedCount} attendance records!");
        return 0;
    }
} 