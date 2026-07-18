<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class surgicalfile extends Model
{
    use SoftDeletes;
    protected $table = 'surgical_files';

    protected $fillable = [
        'surgery_booking_id', 'file_path', 'file_name', 'file_type', 'notes', 'user_id'
    ];

    public function booking()
    {
        return $this->belongsTo(adm_b_surgery_bookings::class, 'surgery_booking_id');
    }
}