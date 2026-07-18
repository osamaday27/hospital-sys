<?php

namespace App\Models\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Models\Finance\service; 

class BookingSurgery extends Model
{
    protected $table = 'booking_surgeries';

    protected $fillable = [
        'booking_id',
        'surgery_id',
        'side', // يمين / شمال / الاثنين
    ];

    public function booking()
    {
        return $this->belongsTo(adm_b_surgery_bookings::class, 'booking_id');
    }

  public function surgery()
{
    return $this->belongsTo(service::class, 'surgery_id');
}

}