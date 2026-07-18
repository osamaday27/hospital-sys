<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdvancePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'advance_payments';

    protected $fillable = [
        'patient_id',
        'booking_id',
        'visit_id',
        'amount',
        'currency',
        'payment_method',
        'payment_date',
        'notes',
        'locker_id',
        'card_number',
        'card_bank',
        'transfer_number',
        'transfer_bank',
        'bank_id',
        'bank_machine_id',
        'user_id',
    ];

    // علاقة بالمريض
    public function patient()
    {
        return $this->belongsTo(\App\Models\Administrative\medical_record::class, 'patient_id');
    }

    // علاقة بالحجز
    public function booking()
    {
        return $this->belongsTo(\App\Models\Administrative\adm_b_surgery_bookings::class, 'booking_id');
    }

    // علاقة بالموظف (المستخدم)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // علاقة بالخزنة
    public function locker()
    {
        return $this->belongsTo(\App\Models\Finance\slocker::class, 'locker_id');
    }

    // علاقة بالبنك عند التحويل البنكي
    public function bank()
    {
        return $this->belongsTo(\App\Models\Finance\Bank::class, 'bank_id');
    }

    // علاقة بالماكينة عند الدفع بالفيزا
    public function bankMachine()
    {
        return $this->belongsTo(\App\Models\Finance\BankMachine::class, 'bank_machine_id');
    }
}