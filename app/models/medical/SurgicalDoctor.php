<?php

namespace App\Models\medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurgicalDoctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surgical_doctors';

    protected $fillable = [
        'name',
        'specialization',
        'license_number',
        'phone',
        'email',
        'address',
        'national_id',
        'date_of_birth',
        'gender',
        'qualifications',
        'experience',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'qualifications' => 'array',
        'experience' => 'array'
    ];

    // Relationships
    public function surgeries()
    {
        return $this->hasMany(SurgeryBooking::class, 'surgeon_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Accessors & Mutators
    public function getFullNameAttribute()
    {
        return "Dr. {$this->name}";
    }

    public function getSpecializationWithNameAttribute()
    {
        return "Dr. {$this->name} - {$this->specialization}";
    }
}
