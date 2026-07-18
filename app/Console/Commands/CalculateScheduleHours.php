<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\hr\hr_schedules;

class CalculateScheduleHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedules:calculate-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and update total_hours for all schedules';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to calculate total hours for all schedules...');

        $schedules = hr_schedules::with('days')->get();
        $updatedCount = 0;

        foreach ($schedules as $schedule) {
            $oldHours = $schedule->total_hours;
            $schedule->updateTotalHours();
            
            if ($oldHours != $schedule->total_hours) {
                $updatedCount++;
                $this->line("Schedule '{$schedule->name}': {$oldHours} → {$schedule->total_hours} hours");
            }
        }

        $this->info("Completed! Updated {$updatedCount} schedules.");
        return 0;
    }
} 