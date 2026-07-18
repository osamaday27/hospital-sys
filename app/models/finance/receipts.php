<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\finance\AutoJournalEntries;

class receipts extends Model
{
    use SoftDeletes, AutoJournalEntries;

    protected $table = 'fin_receipts';
    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'invoices_number',
        'invoices_id',
        'id_reservation',
        'pay_type',
        'id_contract',
        'total_paid',
        'total_remainder',
        'id_patient',
        'branches',
        'id_user',
        'account_id',
        'treasury',
        'notes',
        'reviewed',
        'bank_id','bank_machine_id','transaction_number',
        'card_holder_name','card_number','pay_meta',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoices::class, 'invoices_id');
    }

    public function locker()
    {
        return $this->belongsTo(slocker::class, 'treasury');
    }

    public function service()
    {
        return $this->belongsToMany(Service::class, 'reservation_services')
            ->withPivot('quantity','price');
    }

    public function bank(){ return $this->belongsTo(\App\Models\Finance\Bank::class, 'bank_id'); }
    public function machine(){ return $this->belongsTo(\App\Models\Finance\BankMachine::class, 'bank_machine_id'); }

    /**
     * نفس جروب الفاتورة
     */
    public function getJournalGroupKey(string $action): ?string
    {
        // مهم: اربط إيصال الفاتورة بنفس Group Key للفاتورة
        return 'INV-' . $this->invoices_id;
    }

    /**
     * قيد السداد:
     * Dr Cash/Bank/POS = total_paid
     * Cr AR            = total_paid
     */
    public function getJournalLines(string $action): array
    {
        if ($action !== 'created') return [];

        $ACC_AR   = 12;  // الذمم
        $ACC_CASH = 10;  // خزنة
        $ACC_BANK = 11;  // بنك
        $ACC_POS  = 13;  // فيزا/POS

        $amount = (float) ($this->total_paid ?? 0);
        if ($amount == 0.0) return [];

        $type = strtolower((string) $this->pay_type);
        $debitAcc = match ($type) {
            'cash'              => $ACC_CASH,
            'bank','transfer'   => $ACC_BANK,
            'visa','pos','card' => $ACC_POS,
            default             => $ACC_CASH,
        };

        return [
            ['account_id' => $debitAcc, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $ACC_AR,   'debit' => 0,       'credit' => $amount],
        ];
    }

    /**
     * بعد ترحيل قيد الإيصال: لو المتبقي = 0 → اقفل كل قيود المجموعة
     */
    public function afterJournalPosted(string $action, \App\Models\finance\glentry $header): void
    {
        // لو النموذج بيخزن المتبقي الجديد في total_remainder نستخدمه مباشرة
        $remain = (float) ($this->total_remainder ?? 0);

        // fallback: لو مش واثقين، نقرأ من الفاتورة (لو محدثة)
        if ($remain <= 0 && $this->invoice) {
            try {
                $this->invoice->refresh();
                $remain = (float) ($this->invoice->residual ?? $remain);
            } catch (\Throwable $e) { /* تجاهل */ }
        }

        if ($remain <= 0) {
            $group = 'INV-' . $this->invoices_id;
            \App\Models\finance\glentry::where('group_key', $group)
                ->update(['is_settled' => true, 'settled_at' => now()]);
        }
    }
}