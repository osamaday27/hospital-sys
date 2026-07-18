<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;

class GlJournalEntry extends Model
{
    protected $table = 'gl_journal_entries';

    protected $fillable = [
        'batch_id','reference','entry_date','description',
        'total_debit','total_credit'
    ];

    protected $casts = [
        'entry_date'   => 'date',
        'total_debit'  => 'decimal:6',
        'total_credit' => 'decimal:6',
    ];

    public function lines() { return $this->hasMany(GlJournalLine::class, 'entry_id'); }
    public function batch() { return $this->belongsTo(GlJournalBatch::class, 'batch_id'); }
}