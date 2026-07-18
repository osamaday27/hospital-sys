<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MedicationTaken extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ✅ العلاقات
    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class, 'taken_id');
    }

    public function medicationsAsDefault()
    {
        return $this->hasMany(Medication::class, 'default_taken_id');
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
              ->orWhere('description->ar', 'like', "%{$search}%")
              ->orWhere('description->en', 'like', "%{$search}%");
        });
    }

    // ✅ عدد الاستخدامات
    public function getUsageCountAttribute()
    {
        return $this->prescriptionItems()->count();
    }

    // ✅ الاسم المعروض
    public function getDisplayNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}