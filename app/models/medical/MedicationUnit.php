<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MedicationUnit extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'abbreviation',
        'is_active',
    ];

    public $translatable = ['name', 'abbreviation'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ✅ العلاقات
    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class, 'unit_id');
    }

    public function medicationsAsDefault()
    {
        return $this->hasMany(Medication::class, 'default_unit_id');
    }

    // ✅ سكوبات
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name->ar', 'like', "%{$search}%")
              ->orWhere('name->en', 'like', "%{$search}%")
              ->orWhere('abbreviation->ar', 'like', "%{$search}%")
              ->orWhere('abbreviation->en', 'like', "%{$search}%");
        });
    }

    // ✅ الاسم المعروض
    public function getDisplayNameAttribute()
    {
        $name = $this->getTranslation('name', app()->getLocale());
        $abbr = $this->getTranslation('abbreviation', app()->getLocale());
        return $abbr ? "{$name} ({$abbr})" : $name;
    }
}