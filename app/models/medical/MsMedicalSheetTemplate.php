<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MsMedicalSheetTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ms_medical_sheet_templates';

    protected $fillable = [
        'template_name',
        'category',
        'description',
        'version',
        'structure',
        'model_name',
        'speciality_id',
        'doctor_id',
        'display_order',
        'tags',
        'visibility',
        'status',
        'usage_count',
        'last_used_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'structure' => 'array',
        'tags' => 'array',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function speciality()
    {
        return $this->belongsTo(\App\models\general\specialties::class, 'speciality_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\hr\Employee::class, 'doctor_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeGlobal($query)
    {
        return $query->where('visibility', 'global');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helpers
    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }
}