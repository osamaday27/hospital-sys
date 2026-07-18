<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlJournalBatch extends Model
{
    use SoftDeletes;

    protected $table = 'gl_journal_batches';

    protected $fillable = [
        'code','source','period_start','period_end','status',
        'total_debit','total_credit','cost_batch_id','notes','user_id'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'total_debit'  => 'decimal:6',
        'total_credit' => 'decimal:6',
    ];

    public function entries() { return $this->hasMany(GlJournalEntry::class, 'batch_id'); }
}