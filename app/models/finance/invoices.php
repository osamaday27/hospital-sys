<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use App\Traits\finance\AutoJournalEntries;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\administrative\medical_record;
use App\models\administrative\reservations;
use App\Models\User;

class invoices extends Model
{
    use HasFactory, TracksChanges, SoftDeletes, AutoJournalEntries;

    protected $fillable = [
        'number','claim_id','patient_id','reservation_id','status','total',
        'patient_contribution','contract_contribution','discount','currency',
        'reservation_type','treasury','paid','residual','user_id','branch',
        'is_cancel','reason','contract_id'
    ];

    protected $table = 'fin_invoices';
    protected $dates = ['deleted_at'];
    public $timestamps = true;

    public function patient(){ return $this->belongsTo(medical_record::class, 'patient_id'); }
    public function reservations(){ return $this->belongsTo(reservations::class, 'reservation_id'); }
    public function user(){ return $this->belongsTo(User::class, 'user_id'); }
    public function services(){ return $this->hasMany(finservicevisit::class, 'id_invoices'); }
    public function receipts(){ return $this->hasMany(\App\Models\Finance\Receipts::class, 'invoices_id', 'id'); }
    public function contract(){ return $this->belongsTo(\App\Models\Finance\contract_last_level::class, 'contract_id'); }
    public function claim(){ return $this->belongsTo(\App\Models\Finance\Claim::class, 'claim_id'); }

    public function getComputedStatusAttribute(): string
    {
        if (in_array($this->status, ['canceled','pending_cancellation','pending_discount'])) return $this->status;

        $receiptsCount = isset($this->receipts_count) ? (int)$this->receipts_count : (int)$this->receipts()->count();
        $paid     = (float) $this->paid;
        $residual = (float) $this->residual;

        if ($residual <= 0 && ($paid > 0 || $receiptsCount > 0)) return 'paid';
        if ($paid > 0 && $residual > 0) return 'partial';
        if ($paid == 0 && $receiptsCount === 0) return 'unpaid';
        return $this->status ?: 'pending';
    }

    /**
     * ربط كل قيود الفاتورة والإيصالات بجروب واحد
     */
    public function getJournalGroupKey(string $action): ?string
    {
        return 'INV-' . $this->id;
    }

    /**
     * قيد الإثبات وقت إنشاء الفاتورة فقط (يظل مفتوح حتى السداد)
     *   Dr AR  = (total - discount)
     *   Cr Revenue = (total - discount)
     */
    public function getJournalLines(string $action): array
    {
        if ($action !== 'created') return [];

        $ACC_AR      = 12; // الذمم المدينة (بدّل للأكواد الحقيقية عندك)
        $ACC_REVENUE = 14; // الإيرادات

        $gross    = (float) ($this->total ?? 0);
        $discount = (float) ($this->discount ?? 0);
        $net      = max(0, $gross - $discount);

        if ($net == 0.0) return [];

        return [
            ['account_id' => $ACC_AR,      'debit' => $net, 'credit' => 0],
            ['account_id' => $ACC_REVENUE, 'debit' => 0,    'credit' => $net],
        ];
    }
}