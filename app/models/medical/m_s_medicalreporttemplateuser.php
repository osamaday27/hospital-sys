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
use App\models\medical\m_d_medicalreporttemplate;

class m_s_medicalreporttemplateuser extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    protected $table = 'm_s_medical_report_template_user';

    protected $fillable = ['template_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
      public function template()
    {
        return $this->belongsTo(m_d_medicalreporttemplate::class, 'template_id');
    }
}