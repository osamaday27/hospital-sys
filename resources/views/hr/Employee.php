<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\models\hr\Department; 
use App\models\hr\section; 
use App\models\hr\jobs; 


class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'first_name_ar',
        'second_name_ar',
        'third_name_ar',
        'fourth_name_ar',
        'first_name_en',
        'second_name_en',
        'third_name_en',
        'fourth_name_en',
        'employee_code',
        'gahar_code',
        'employee_status',
        'marital_status',
        'identification_number',
        'passport_number',
        'religion',
        'nationality',
        'country',
        'city',
        'district',
        'full_address',
        'phone',
        'email',
        'landline',
        'fax',
        'emergency_name',
        'emergency_number',
        'relationship',
        'department',
        'section',
        'job_position',
        'contract_open_date',
        'hiring_date',
        'contract_end_date',
        'cv',
        'militery',
        'graduation_certificate',
        'birth_certificate',
        'employee_photo',
    ];

    public function emp_dep()
    {
        return $this->belongsTo(Department::class, 'department');
    }
    public function emp_sec()
    {
        return $this->belongsTo(section::class, 'section');
    }
    public function emp_pos()
    {
        return $this->belongsTo(jobs::class, 'job_position');
    }
}
