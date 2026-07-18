<?php
// Model: mbdentalcomplaint.php

namespace App\models\medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class mbdentalcomplaint extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_b_dental_complaints';

    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
        'complaint_date',
        'complaint_description',
        'affected_tooth',
        'complaint_type',
        'complaint_status',
        'physician_name',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'complaint_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}