<?php

namespace App\Models\suppliers;

use App\models\general\branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class suppliersgroup extends Model
{
    use SoftDeletes;
    protected $table = 'suppliers_groups';

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'description',
        'branch_id',
        'status',
        'created_by',
    ];

    // العلاقات
    public function branch()
    {
        return $this->belongsTo(branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
