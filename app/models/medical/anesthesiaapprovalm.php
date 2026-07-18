<?php
namespace App\Models\medical;

use Illuminate\Database\Eloquent\Model;

class anesthesiaapprovalm extends Model
{
    protected $table = 'm_d_anesthesia_approvals';

    protected $fillable = [
        'surgery_booking_id',
        'doctor_name',
        'approval_date',
        'notes',
        'approval_file',
        'status',
        'user_id',
    ];

    public function booking()
    {
        return $this->belongsTo(\App\Models\administrative\adm_b_surgery_bookings::class, 'surgery_booking_id');
    }
}