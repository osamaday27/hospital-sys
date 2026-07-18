<?php

namespace App\Services\Costing;

use App\Models\costing\CostAdjustment;
use App\Models\costing\CostTransaction;
use Illuminate\Support\Facades\DB;

class AdjustmentService
{
    /**
     * ترحيل تسوية واحدة (ينشئ سطر cost_transactions بالقيمة موجبة/سالبة حسب adj_type)
     */
    public function postOne(CostAdjustment $adj): void
    {
        if ($adj->status !== 'approved') {
            throw new \RuntimeException(__('adjustments_trans.err_post_requires_approved'));
        }

        $amount = (float) $adj->amount;
        if ($adj->adj_type === 'decrease') {
            $amount = -1 * abs($amount);
        }

        DB::transaction(function () use ($adj, $amount) {
            CostTransaction::create([
                'batch_id'       => $adj->batch_id,
                'case_type'      => $adj->case_type,
                'case_id'        => $adj->case_id,
                'service_id'     => $adj->service_id,
                'cost_center_id' => $adj->cost_center_id,
                'line_type'      => $adj->line_type,     // direct | overhead
                'item_type'      => $adj->item_type,     // supply/drug/resource/misc/overhead
                'description'    => 'Adjustment: '.$adj->reference,
                'qty'            => 1,
                'unit_cost'      => $amount,
                'total_cost'     => $amount,
                'meta'           => [
                    'adjustment_id' => $adj->id,
                    'reason' => $adj->reason,
                    'effective_date' => optional($adj->effective_date)->format('Y-m-d'),
                ],
            ]);

            $adj->update(['status' => 'posted']);
        });
    }

    /**
     * ترحيل كل التسويات المعتمدة المرتبطة بدفعة معينة
     */
    public function postBatchAdjustments(int $batchId): int
    {
        $count = 0;
        $items = CostAdjustment::where('batch_id', $batchId)
            ->where('status', 'approved')
            ->get();

        foreach ($items as $adj) {
            $this->postOne($adj);
            $count++;
        }
        return $count;
    }
}