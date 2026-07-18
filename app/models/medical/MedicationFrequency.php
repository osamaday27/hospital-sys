<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MedicationFrequency extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'times_per_day',
        'is_active',
    ];
    
    protected $table = 'medicationfrequency';

    public $translatable = ['name'];

    protected $casts = [
        'is_active' => 'boolean',
        'times_per_day' => 'integer',
    ];

    // ✅ العلاقات
    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class, 'frequency_id');
    }

    public function medicationsAsDefault()
    {
        return $this->hasMany(Medication::class, 'default_frequency_id');
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
              ->orWhere('name->en', 'like', "%{$search}%");
        });
    }

    public function scopeOrderByTimes($query, $direction = 'asc')
    {
        return $query->orderBy('times_per_day', $direction);
    }

    // ✅ الاسم المعروض
    public function getDisplayNameAttribute()
    {
        $name = $this->getTranslation('name', app()->getLocale());
        return $this->times_per_day ? "{$name} ({$this->times_per_day}x/day)" : $name;
    }
}