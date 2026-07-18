<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class surgicalitemcount extends Model
{
    use HasFactory;

    protected $table = 'm_d_surgical_item_counts';

    protected $fillable = [
        'surgery_booking_id',
        'item_name',
        'initial_count',
        'additional_count',
        'closure_count',
        'final_count',
        'final_count_verified',
        'notes',
        'count_date',
        'count_time',
        'surgeon_signature',
        'nurse_signature',
    ];

    public function booking()
    {
        return $this->belongsTo(SurgicalBooking::class, 'surgery_booking_id');
    }
}