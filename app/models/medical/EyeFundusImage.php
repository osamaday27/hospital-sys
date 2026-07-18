<?php

namespace App\Models\Medical;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class EyeFundusImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'visit_id',
        'reservation_id',
        'image_path',
        'notes',
        'created_by',
    ];
}