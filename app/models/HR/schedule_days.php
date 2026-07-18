<?php

namespace App\models\hr;
use App\models\hr\hr_schedules;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class schedule_days extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    // Relationship to Schedule
    public function schedule()
    {
        return $this->belongsTo(hr_schedules::class, 'schedule_id');
    }
}