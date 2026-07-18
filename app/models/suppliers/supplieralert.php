<?php

 namespace App\Models\suppliers;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class supplieralert extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_alerts';

    protected $fillable = [
        'supplier_id',
        'type',/** addition -- discount */
        'value',
        'notify_date',
        'notes',
    ];

    protected $casts = [
        'value'       => 'decimal:2',
        'notify_date' => 'date',
    ];

    /**
     * علاقة الإشعار بالمورد
     */
    public function supplier()
    {
        return $this->belongsTo(SuppliersData::class, 'supplier_id');
    }
}