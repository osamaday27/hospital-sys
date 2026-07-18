<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GLAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_s_gl_accounts';

    protected $fillable = [
        'code','name_ar','name_en','description',
        'type',            // asset|liability|equity|income|expense
        'category',        // cash|bank|customer|supplier|tax|other|null
        'parent_id','is_group',
        'opening_balance','current_balance','balance_type', // debit|credit (الطبيعة)
        'status','created_by',
    ];

    protected $casts = [
        'is_group'        => 'boolean',
        'status'          => 'boolean',
        'opening_balance' => 'decimal:4',
        'current_balance' => 'decimal:4',
    ];

    protected $appends = [
        'type_color',
        'type_translated',
        'category_translated',
        'calculated_balance',
    ];

    /* =========================
     * شجرة الحسابات
     * ========================= */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        // هنحَمّل مجاميع الحركة للأولاد كمان (مفيد للشجرة)
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('code')
            ->withSum('debitEntries as debit_total', 'debit')
            ->withSum('creditEntries as credit_total', 'credit')
            ->withCount(['debitEntries as debit_entries_count','creditEntries as credit_entries_count']);
    }

    /* =========================
     * حركة القيود (على سطور اليومية)
     * ========================= */
    public function journalLines()
    {
        return $this->hasMany(\App\Models\finance\glentryline::class, 'account_id', 'id')
                    ->with('entry');
    }

    public function debitEntries()
    {
        return $this->hasMany(\App\Models\finance\glentryline::class, 'account_id', 'id')
                    ->where('debit', '>', 0)
                    ->with('entry');
    }

    public function creditEntries()
    {
        return $this->hasMany(\App\Models\finance\glentryline::class, 'account_id', 'id')
                    ->where('credit', '>', 0)
                    ->with('entry');
    }

    /* =========================
     * Accessors للعرض
     * ========================= */
    public function fullName(): string
    {
        $name = $this->name_ar ?: $this->name_en ?: '';
        $code = $this->code ? "{$this->code} - " : '';
        return trim($code . $name);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'asset'     => 'success',
            'liability' => 'danger',
            'equity'    => 'info',
            'income'    => 'primary',
            'expense'   => 'warning',
            default     => 'secondary',
        };
    }

    public function getTypeTranslatedAttribute(): string
    {
        return match ($this->type) {
            'asset'     => __('glaccounts_trans.asset'),
            'liability' => __('glaccounts_trans.liability'),
            'equity'    => __('glaccounts_trans.equity'),
            'income'    => __('glaccounts_trans.income'),
            'expense'   => __('glaccounts_trans.expense'),
            default     => $this->type ?? '-',
        };
    }

    public function getCategoryTranslatedAttribute(): ?string
    {
        if (is_null($this->category) || $this->category === '') return null;

        return match ($this->category) {
            'cash'     => __('glaccounts_trans.cash'),
            'bank'     => __('glaccounts_trans.bank'),
            'customer' => __('glaccounts_trans.customer'),
            'supplier' => __('glaccounts_trans.supplier'),
            'tax'      => __('glaccounts_trans.tax'),
            'other'    => __('glaccounts_trans.other'),
            default    => $this->category,
        };
    }

    /**
     * رصيد محسوب = Opening ± حركة
     * - لو طبيعة الحساب "debit" (أصل/مصروف): opening + debit_total - credit_total
     * - لو "credit" (التزامات/حقوق/إيراد):   opening - debit_total + credit_total
     */
    public function getCalculatedBalanceAttribute(): float
    {
        $opening = (float) ($this->opening_balance ?? 0);
        $debit   = (float) ($this->debit_total   ?? 0);
        $credit  = (float) ($this->credit_total  ?? 0);

        if (($this->balance_type ?? 'debit') === 'debit') {
            return round($opening + $debit - $credit, 2);
        }
        return round($opening - $debit + $credit, 2);
    }
}