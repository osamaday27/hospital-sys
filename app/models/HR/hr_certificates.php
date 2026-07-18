<?php

namespace App\models\HR;

use Illuminate\Database\Eloquent\Model;

class hr_certificates extends Model
{
    protected $table = 'hr_certificates';
    protected $fillable = [
        'employee_id',
        'certificate_name',
        'certificate_type_id',
        'graduation_date',
        'university_id',
        'certificate_file',
    ];

    /**
     * Get the certificate type that owns the certificate.
     */
    public function certificateType()
    {
        return $this->belongsTo(hr_certificates_types::class, 'certificate_type_id');
    }

    /**
     * Get the university that owns the certificate.
     */
    public function university()
    {
        return $this->belongsTo(hr_universities::class, 'university_id');
    }

    /**
     * Get the employee that owns the certificate.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
} 