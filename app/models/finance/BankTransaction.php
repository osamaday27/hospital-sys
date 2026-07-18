<?php
namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_bank_transactions';

    protected $fillable = [
        'bank_id','account_id','transaction_date','type','amount',
        'reference','related_invoice_id','notes','user_id'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function account()
    {
        return $this->belongsTo(BankAccount::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Finance\Invoice::class, 'related_invoice_id');
    }
}