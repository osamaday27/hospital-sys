<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class summarymodel extends Model
{
    protected $table = 'reassessment_summaries';

    protected $fillable = [
        'cough',
        'cough_onset',
        'fever',
        'fever_onset',
        'rash',
        'rash_onset',
        'travel',
        'travel_destination',
        'action_mask',
        'action_distance',
        'action_notify',
        'action_accelerate',
        'notify_doctor',
    ];
}