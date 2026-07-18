<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\hr\hr_attendance;
use App\models\hr\Employee;
use Carbon\Carbon;

class CalculateAttendancePenalty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:calculate-penalty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and update penalty for all attendance records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to calculate penalty for all attendance records...');

        $attendanceRecords = hr_attendance::with(['employee.schedule.days'])->get();
        $updatedCount = 0;

        foreach ($attendanceRecords as $attendance) {
            $penalty = 0;
            
            if ($attendance->shortage > 0) {
                $shortageMinutes = $attendance->shortage * 60; // Convert hours to minutes
                
                if ($shortageMinutes >= 15 && $shortageMinutes < 120) {
                    $penalty = 0.25; // 0.25 days
                } elseif ($shortageMinutes >= 120 && $shortageMinutes < 240) {
                    $penalty = 0.5; // 0.5 days
                } elseif ($shortageMinutes >= 240) {
                    $penalty = 1.0; // 1 day
                }
            }

            $attendance->update(['penalty' => $penalty]);
            $updatedCount++;
            
            if ($updatedCount % 100 == 0) {
                $this->info("Processed {$updatedCount} records...");
            }
        }

        $this->info("Successfully updated penalty for {$updatedCount} attendance records!");
        return 0;
    }
} 