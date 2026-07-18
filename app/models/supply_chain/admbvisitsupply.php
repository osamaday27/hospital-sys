<?php

namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class admbvisitsupply extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * اسم الجدول المرتبط
     */
    protected $table = 'adm_b_visit_supplies';

    /**
     * الحقول القابلة للتعيين
     */
    protected $fillable = [
        'reservation_id',
        'patient_id',
        'visit_id',
        'product_id',
        'store_id',
        'unit_id',
        'quantity',
        'price',
        'created_by',
    ];

    /**
     * العلاقات
     */
    public function reservation()
    {
        return $this->belongsTo(\App\models\administrative\reservations::class, 'reservation_id');
    }

    public function patient()
    {
        return $this->belongsTo(\App\models\administrative\medical_record::class, 'patient_id');
    }

    public function visit()
    {
        return $this->belongsTo(\App\models\administrative\patient_visits::class, 'visit_id');
    }

    public function product()
    {
        return $this->belongsTo(Productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
    public function store()
    {
        return $this->belongsTo(storemaindata::class, 'store_id');
    }
}