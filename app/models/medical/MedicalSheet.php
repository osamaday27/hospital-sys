<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalSheet extends Model
{
    use HasFactory;

    protected $fillable = ['sheet_type', 'patient_id', 'visit_id', 'data', 'created_by'];

    protected $casts = ['data' => 'array'];
}