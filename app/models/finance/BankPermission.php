<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankPermission extends Model
{
    use HasFactory;

    protected $table = 'fin_bank_permissions';

    protected $fillable = [
        'user_id',
        'can_issue_cheque',
        'can_approve_cheque',
        'can_clear_cheque',
        'can_cancel_cheque',
        'can_bounce_cheque',
        'can_reconcile_cheque',
        'can_view_reports',
    ];

    public function user()
    {
        return $this->belongsTo(\App\models\User::class, 'user_id');
    }
}