<?php

namespace App\models\hr;
use App\models\hr\schedule_days;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class hr_schedules extends Model
{
    use HasFactory;
    protected $table = 'hr_schedules';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'total_hours',
    ];

    public function days()
    {
        return $this->hasMany(schedule_days::class, 'schedule_id');
    }

    /**
     * Calculate total hours from schedule days
     */
    public function calculateTotalHours()
    {
        $totalHours = 0;
        $daysWithHours = 0;

        foreach ($this->days as $day) {
            if ($day->start_time && $day->end_time) {
                $startTime = Carbon::parse($day->start_time);
                $endTime = Carbon::parse($day->end_time);
                
                // Calculate hours difference
                $hours = $endTime->diffInHours($startTime);
                $minutes = $endTime->diffInMinutes($startTime) % 60;
                $totalHours += $hours + ($minutes / 60);
                $daysWithHours++;
            }
        }

        // Return average hours if there are days with hours, otherwise 0
        return $daysWithHours > 0 ? round($totalHours / $daysWithHours, 2) : 0;
    }

    /**
     * Update total_hours field
     */
    public function updateTotalHours()
    {
        $this->total_hours = $this->calculateTotalHours();
        $this->save();
    }
}
