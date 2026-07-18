<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MedicationDuration extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'days',
        'is_active',
    ];

    public $translatable = ['name'];

    protected $casts = [
        'is_active' => 'boolean',
        'days' => 'integer',
    ];

    // ✅ العلاقات
    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class, 'duration_id');
    }

    public function medicationsAsDefault()
    {
        return $this->hasMany(Medication::class, 'default_duration_id');
    }

    // ✅ سكوبات جاهزة
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

    public function scopeOrderByDays($query, $direction = 'asc')
    {
        return $query->orderBy('days', $direction);
    }

    // ✅ الاسم المعروض
    public function getDisplayNameAttribute()
    {
        $name = $this->getTranslation('name', app()->getLocale());
        return $this->days ? "{$name} ({$this->days} " . __('days') . ")" : $name;
    }
}