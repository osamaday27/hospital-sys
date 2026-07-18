<?php
namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User;
use App\models\finance\service;

class m_d_colorvision extends Model
{
   use HasFactory, SoftDeletes;

   
    protected $table = 'm_d_colorvision'; // اسم الجدول
    protected $fillable = [
        'id_patient',
        'id_reservation',
        'id_visit',
        'type_visit',
        'od_colors',
        'os_colors',
        'od_comment',
        'os_comment',
        'created_by',
    ];

    protected $casts = [
        'od_colors' => 'array',
        'os_colors' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}