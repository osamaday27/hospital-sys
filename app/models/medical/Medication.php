<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Medication extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $fillable = [
        'name',
        'scientific_name',
        'category',
        'description',
        'default_unit_id',
        'default_duration_id',
        'default_taken_id',
        'default_dose',
        'is_favorite',
        'is_active',
        'warnings',
        'side_effects',
        'contraindications',
    ];

    public $translatable = ['name', 'description', 'warnings', 'side_effects', 'contraindications'];

    protected $casts = [
        'is_favorite' => 'boolean',
        'is_active' => 'boolean',
        'default_dose' => 'decimal:2',
    ];

    /**
     * Get all prescription items for this medication
     */
    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class, 'medication_id');
    }

    /**
     * Get prescriptions that include this medication
     */
    public function prescriptions()
    {
        return $this->hasManyThrough(
            Prescription::class,
            PrescriptionItem::class,
            'medication_id',
            'id',
            'id',
            'prescription_id'
        );
    }

    /**
     * Get the default unit for this medication
     */
    public function defaultUnit()
    {
        return $this->belongsTo(MedicationUnit::class, 'default_unit_id');
    }

    /**
     * Get the default duration for this medication
     */
    public function defaultDuration()
    {
        return $this->belongsTo(MedicationDuration::class, 'default_duration_id');
    }

    /**
     * Get the default administration route for this medication
     */
    public function defaultTaken()
    {
        return $this->belongsTo(MedicationTaken::class, 'default_taken_id');
    }

    /**
     * Scope to get only active medications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only favorite medications
     */
    public function scopeFavorite($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to search medications
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name->ar', 'like', "%{$search}%")
              ->orWhere('name->en', 'like', "%{$search}%")
              ->orWhere('scientific_name', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by category
     */
    public function scopeCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Get usage count for this medication
     */
    public function getUsageCountAttribute()
    {
        return $this->prescriptionItems()->count();
    }

    /**
     * Check if medication is frequently used
     */
    public function isFrequentlyUsed()
    {
        return $this->prescriptionItems()
            ->whereHas('prescription', function($q) {
                $q->where('created_at', '>=', now()->subMonths(3));
            })
            ->count() >= 10;
    }
}