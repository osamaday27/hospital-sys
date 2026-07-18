<?php

namespace App\Services\Costing;

use App\Models\finance\GlJournalBatch;
use App\Models\finance\GlJournalEntry;
use App\Models\finance\GlJournalLine;
use App\Models\finance\CostingGlMapping;
use App\Models\costing\CostBatch;
use App\Models\costing\CostTransaction;
use App\Models\costing\CostAdjustment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GlPostingService
{
    /**
     * إنشاء باتش يومية من باتش تكلفة، وترحيل كل سطور التكلفة + التسويات المعتمدة
     */
    public function postCostBatchToGl(int $costBatchId, ?string $notes = null): GlJournalBatch
    {
        $costBatch = CostBatch::findOrFail($costBatchId);

        // لا ننشئ أكثر من باتش GL لنفس cost_batch إلا إذا حابب تكرار
        $existing = GlJournalBatch::where('cost_batch_id', $costBatchId)->where('status','posted')->first();
        if ($existing) {
            throw new \RuntimeException(__('costing_trans.err_already_posted'));
        }

        return DB::transaction(function () use ($costBatch, $notes) {
            $batch = GlJournalBatch::create([
                'code'         => 'GLB-'.now()->format('Ymd-His'),
                'source'       => 'COSTING',
                'period_start' => $costBatch->period_start,
                'period_end'   => $costBatch->period_end,
                'status'       => 'draft',
                'cost_batch_id'=> $costBatch->id,
                'notes'        => $notes,
                'user_id'      => Auth::id(),
            ]);

            // قيد مجمّع واحد للفترة (تقدر تفصل لأكثر من قيد حسب سياساتك)
            $entry = GlJournalEntry::create([
                'batch_id'    => $batch->id,
                'reference'   => 'COST-'.$costBatch->code,
                'entry_date'  => $costBatch->period_end ?? now()->toDateString(),
                'description' => 'Costing posting for batch '.$costBatch->code,
                'total_debit' => 0,
                'total_credit'=> 0,
            ]);

            $lineNo = 1;
            $totDr = 0; $totCr = 0;

            // 1) سطور التكلفة
            $ctrs = CostTransaction::where('batch_id', $costBatch->id)->get();
            foreach ($ctrs as $t) {
                $amount = (float) $t->total_cost;
                if ($amount == 0) continue;

                $map = $this->resolveMap($t->line_type, $t->item_type, $t->cost_center_id);

                // بحسب سياسة: Dr: تكلفة / Cr: مخزون/مستحقات (مثال)
                // لو سطر تكلفة موجبة → Dr (debit_account) ، Cr (credit_account)
                // لو سالبة (تصحيح عكسي) هنبدّل الجهة.
                if ($amount > 0) {
                    $totDr += $amount; $totCr += $amount;

                    GlJournalLine::create([
                        'entry_id' => $entry->id, 'line_no' => $lineNo++,
                        'account' => $map['debit'], 'debit' => $amount, 'credit'=>0,
                        'description' => 'Cost: '.$t->description,
                        'cost_center_id' => $t->cost_center_id,
                        'service_id'     => $t->service_id,
                        'case_type'      => $t->case_type,
                        'case_id'        => $t->case_id,
                    ]);
                    GlJournalLine::create([
                        'entry_id' => $entry->id, 'line_no' => $lineNo++,
                        'account' => $map['credit'], 'debit' => 0, 'credit'=>$amount,
                        'description' => 'Cost: '.$t->description,
                        'cost_center_id' => $t->cost_center_id,
                        'service_id'     => $t->service_id,
                        'case_type'      => $t->case_type,
                        'case_id'        => $t->case_id,
                    ]);
                } else {
                    $amount = abs($amount);
                    $totDr += $amount; $totCr += $amount;

                    GlJournalLine::create([
                        'entry_id' => $entry->id, 'line_no' => $lineNo++,
                        'account' => $map['credit'], 'debit' => $amount, 'credit'=>0,
                        'description' => 'Reversal: '.$t->description,
                        'cost_center_id' => $t->cost_center_id,
                        'service_id'     => $t->service_id,
                        'case_type'      => $t->case_type,
                        'case_id'        => $t->case_id,
                    ]);
                    GlJournalLine::create([
                        'entry_id' => $entry->id, 'line_no' => $lineNo++,
                        'account' => $map['debit'], 'debit' => 0, 'credit'=>$amount,
                        'description' => 'Reversal: '.$t->description,
                        'cost_center_id' => $t->cost_center_id,
                        'service_id'     => $t->service_id,
                        'case_type'      => $t->case_type,
                        'case_id'        => $t->case_id,
                    ]);
                }
            }

            // 2) التسويات المعتمدة وغير المُرحّلة ضمن الفترة
            $adjs = CostAdjustment::where('batch_id', $costBatch->id)->whereIn('status',['approved','posted'])->get();
            foreach ($adjs as $a) {
                // لو لسة approved ولم تُرحّل لسطر تكلفة، ممكن تقلبها أولاً لسطر تكلفة في نظامك (اختياري)
                // هنا هنرحلها مباشرة على GL:
                $amount = (float) $a->amount;
                if ($a->adj_type === 'decrease') $amount = -1 * abs($amount);
                if ($amount == 0) continue;

                $map = $this->resolveMap($a->line_type, $a->item_type, $a->cost_center_id);

                if ($amount > 0) {
                    $totDr += $amount; $totCr += $amount;
                    GlJournalLine::create([
                        'entry_id' => $entry->id, 'line_no' => $lineNo++,
                        'account' => $map['debit'], 'debit' => $amount, 'credit'=>0,
                        'description' => 'Adjustment: '.$a->reference,
                        'cost_center_id' => $a->cost_center_id,
                        'service_id'     => $a->service_id,
                        'case_type'      => $a->case_type,
                        'case_id'        => $a->case_id,
                    ]);
                    GlJournalLine::create([
                        'entry_id' => $entry->id, 'line_no' => $lineNo++,
                        'account' => $map['credit'], 'debit' => 0, 'credit'=>$amount,
                        'description' => 'Adjustment: '.$a->reference,
                        'cost_center_id' => $a->cost_center_id,
                        'service_id'     => $a->service_id,
                        'case_type'      => $a->case_type,
                        'case_id'        => $a->case_id,
                    ]);
                } else {
                    $amount = abs($amount);
                    $totDr += $amount; $totCr += $amount;
                    GlJournalLine::create([
                        'entry_id' => $entry->id, 'line_no' => $lineNo++,
                        'account' => $map['credit'], 'debit' => $amount, 'credit'=>0,
                        'description' => 'Adj Reversal: '.$a->reference,
                        'cost_center_id' => $a->cost_center_id,
                        'service_id'     => $a->service_id,
                        'case_type'      => $a->case_type,
                        'case_id'        => $a->case_id,
                    ]);
                    GlJournalLine::create([
                        'entry_id' => $entry->id, 'line_no' => $lineNo++,
                        'account' => $map['debit'], 'debit' => 0, 'credit'=>$amount,
                        'description' => 'Adj Reversal: '.$a->reference,
                        'cost_center_id' => $a->cost_center_id,
                        'service_id'     => $a->service_id,
                        'case_type'      => $a->case_type,
                        'case_id'        => $a->case_id,
                    ]);
                }
            }

            // تحديث الإجماليات
            $entry->update(['total_debit'=>$totDr, 'total_credit'=>$totCr]);
            $batch->update(['total_debit'=>$totDr, 'total_credit'=>$totCr, 'status'=>'posted']);

            return $batch;
        });
    }

    /**
     * اختيار خريطة الترحيل المناسبة (line_type + item_type + cost_center_id)
     * إن لم توجد خريطة بمركز تكلفة، نرجع لأقرب خريطة عامة.
     */
    protected function resolveMap(string $lineType, string $itemType, ?int $costCenterId): array
    {
        $q = CostingGlMapping::query()
            ->where('active', true)
            ->where('line_type', $lineType)
            ->where('item_type', $itemType);

        $map = null;
        if ($costCenterId) {
            $map = (clone $q)->where('cost_center_id', $costCenterId)->first();
        }
        if (!$map) {
            $map = (clone $q)->whereNull('cost_center_id')->first();
        }

        if (!$map) {
            throw new \RuntimeException(__('costing_trans.err_missing_mapping'));
        }

        return ['debit'=>$map->debit_account, 'credit'=>$map->credit_account];
    }
}