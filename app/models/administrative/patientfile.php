<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class patientfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_files';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'file_path',
        'file_name',
        'file_mime',
        'file_extension',
        'size_in_kb',
        'document_type',
        'notes',
        'user_id',
    ];

    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }

    public function visit()
    {
        return $this->belongsTo(patient_visits::class, 'visit_id');
    }
}