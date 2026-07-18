<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class overheadpool extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'overhead_pools';

    protected $fillable = [
        'code','name','category','period_start','period_end',
        'amount','status','notes','user_id',
    ];

    public $translatable = ['name'];

    public static function categories(): array
    {
        return ['utilities','maintenance','administration','cleaning','rent','it','security','other'];
    }
protected $casts = [
    'period_start' => 'date',
    'period_end'   => 'date',
];

    public static function statuses(): array
    {
        return ['draft','approved','posted'];
    }

    // Helpers
    public function scopeInPeriod($q, $from, $to)
    {
        return $q->where(function($qq) use ($from, $to) {
            $qq->whereBetween('period_start', [$from, $to])
               ->orWhereBetween('period_end', [$from, $to])
               ->orWhere(function($q3) use ($from, $to) {
                   $q3->where('period_start', '<=', $from)
                      ->where('period_end', '>=', $to);
               });
        });
    }
}