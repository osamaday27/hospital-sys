<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;

class GlJournalLine extends Model
{
    protected $table = 'gl_journal_lines';

    protected $fillable = [
        'entry_id','line_no','account','debit','credit','description',
        'cost_center_id','service_id','case_type','case_id'
    ];

    protected $casts = [
        'debit'  => 'decimal:6',
        'credit' => 'decimal:6',
    ];

    public function entry() { return $this->belongsTo(GlJournalEntry::class, 'entry_id'); }
}