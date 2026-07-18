<?php

namespace App\Traits\finance;

use App\Models\finance\glentry;
use App\Models\finance\glentryline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait AutoJournalEntries
{
    public static function bootAutoJournalEntries()
    {
        static::created(function ($model) {
            $model->generateJournalEntries('created');
        });

        static::updated(function ($model) {
            $model->generateJournalEntries('updated');
        });

        static::deleted(function ($model) {
            $model->generateJournalEntries('deleted');
        });
    }

    protected function generateJournalEntries(string $action): void
    {
        // 1) إبني السطور
        $lines = [];
        if (method_exists($this, 'getJournalLines')) {
            $lines = (array) $this->getJournalLines($action);
        }
        if (empty($lines)) {
            if (!isset($this->debit_account_id, $this->credit_account_id, $this->amount)) return;
            $amount = (float) $this->amount;
            if ($amount == 0.0) return;

            $lines = [
                ['account_id' => (int) $this->debit_account_id,  'debit' => $amount, 'credit' => 0],
                ['account_id' => (int) $this->credit_account_id, 'debit' => 0,       'credit' => $amount],
            ];
        }

        // 2) توازن
        $td = 0.0; $tc = 0.0;
        foreach ($lines as $ln) {
            $td += (float) ($ln['debit']  ?? 0);
            $tc += (float) ($ln['credit'] ?? 0);
        }
        if (round($td, 4) !== round($tc, 4)) return;

        // 3) وصف + group_key
        $description = __("journal_trans.{$action}_description", [
            'model' => class_basename($this),
            'id'    => $this->getKey(),
        ]);
        $groupKey = method_exists($this, 'getJournalGroupKey')
            ? ($this->getJournalGroupKey($action) ?? null)
            : null;

        // 4) مانع تكرار بسيط (نفس المرجع + نفس الوصف في نفس اليوم)
        $exists = glentry::where('reference_type', get_class($this))
            ->where('reference_id', $this->getKey())
            ->where('description', $description)
            ->whereDate('date', now()->toDateString())
            ->exists();
        if ($exists) return;

        // 5) إنشاء القيد (هيدر + سطور)
        $header = DB::transaction(function () use ($description, $lines, $groupKey) {
            $header = glentry::create([
                'date'           => now()->toDateString(),
                'reference_type' => get_class($this),
                'reference_id'   => $this->getKey(),
                'description'    => $description,
                'group_key'      => $groupKey,   // NEW
                'is_settled'     => false,       // NEW: دايمًا مفتوح مبدأيًا
                'created_by'     => Auth::id(),
            ]);

            foreach ($lines as $ln) {
                glentryline::create([
                    'entry_id'   => $header->id,
                    'account_id' => (int) ($ln['account_id']),
                    'debit'      => (float) ($ln['debit']  ?? 0),
                    'credit'     => (float) ($ln['credit'] ?? 0),
                ]);
            }

            return $header;
        });

        // 6) Hook اختياري بعد القيد (للإيصال يقفل الجروب لو المتبقي = 0)
        if (method_exists($this, 'afterJournalPosted')) {
            $this->afterJournalPosted($action, $header);
        }
    }
}