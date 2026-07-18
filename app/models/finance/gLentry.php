<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class glentry extends Model
{
    use SoftDeletes;

    protected $table = 'fin_b_gl_entries';

    protected $fillable = [
        'date',
        'reference_type',
        'reference_id',
        'description',
        'group_key',    // NEW
        'is_settled',   // NEW
        'settled_at',   // NEW
        'created_by',
    ];

    protected $casts = [
        'is_settled' => 'boolean',
        'settled_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(glentryline::class, 'entry_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function scopeGroupKey($q, $key)
    {
        return $q->where('group_key', $key);
    }

    public function fullTitle()
    {
        return "{$this->date} - {$this->description}";
    }
}