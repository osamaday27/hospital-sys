<?php
namespace App\models\administrative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class patient_visits extends Model
{
    protected $fillable = [
        'patient_id',
        'date_visit',
        'reservation_id', // ✅ لربط الزيارة بحجز العملية
        'type',
        'status',
        'user_id',
        'account_id',
    ];
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'patient_visits';
    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }

    public function booking()
    {
        return $this->belongsTo(adm_b_surgery_bookings::class, 'reservation_id');
    }
  

}