<?php
namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class surgicalnote extends Model
{
    use SoftDeletes;
    protected $table = 'surgical_notes';

    protected $fillable = ['surgery_booking_id', 'note', 'user_id'];

    public function booking()
    {
        return $this->belongsTo(adm_b_surgery_bookings::class, 'surgery_booking_id');
    }
    public function user()
{
    return $this->belongsTo(\App\models\User::class, 'user_id');
}

}