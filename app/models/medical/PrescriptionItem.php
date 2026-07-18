<?php

namespace App\models\medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = ['prescription_id', 'medication_id', 'quantity', 'unit_id', 'duration_id', 'taken_id', 'timer', 'eye', 'notes', 'medication_text', 'unit_text', 'duration_text', 'taken_text'];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}