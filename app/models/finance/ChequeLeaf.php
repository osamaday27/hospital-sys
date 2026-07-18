<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChequeLeaf extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_cheque_leaves';

    protected $fillable = [
        'cheque_book_id',
        'cheque_number',
        'status',
        'linked_cheque_id',
    ];

    public function chequeBook()
    {
        return $this->belongsTo(ChequeBook::class, 'cheque_book_id');
    }

    public function linkedCheque()
    {
        return $this->belongsTo(BankCheque::class, 'linked_cheque_id');
    }
}