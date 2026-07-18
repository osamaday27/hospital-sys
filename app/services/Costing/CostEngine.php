<?php

namespace App\Services\Costing;

use App\Models\costing\CostBatch;
use App\Models\costing\CostTransaction;
use App\Models\costing\overheadpool;
use App\Models\costing\costcenter;
use Illuminate\Support\Facades\DB;

class CostEngine
{
    /**
     * حساب التكلفة المباشرة (نموذج توضيحي).
     * استبدل بـ (BOM + استخدام فعلي) لاحقًا.
     */
    public function calculateDirectCosts(CostBatch $batch): array
    {
        $created = 0;
        $total   = 0;

        // أمثلة ثابتة؛ غيّرها ببياناتك
        $sampleCases = [
            ['case_type'=>'opd',     'case_id'=>1001, 'service_id'=>null, 'cost_center_id'=>null, 'desc'=>'OPD supplies',       'qty'=>1,  'unit_cost'=>150, 'item_type'=>'supply'],
            ['case_type'=>'surgery', 'case_id'=>5005, 'service_id'=>null, 'cost_center_id'=>null, 'desc'=>'OR room minutes',     'qty'=>90, 'unit_cost'=>10,  'item_type'=>'resource'],
        ];

        DB::transaction(function () use ($batch, $sampleCases, &$created, &$total) {
            foreach ($sampleCases as $c) {
                $lineTotal = round($c['qty'] * $c['unit_cost'], 6);

                CostTransaction::create([
                    'batch_id'       => $batch->id,
                    'case_type'      => $c['case_type'],
                    'case_id'        => $c['case_id'],
                    'service_id'     => $c['service_id'],
                    'cost_center_id' => $c['cost_center_id'],
                    'line_type'      => 'direct',
                    'item_type'      => $c['item_type'],
                    'description'    => $c['desc'],
                    'qty'            => $c['qty'],
                    'unit_cost'      => $c['unit_cost'],
                    'total_cost'     => $lineTotal,
                    'meta'           => null,
                ]);

                $created++;
                $total += $lineTotal;
            }
        });

        return ['count' => $created, 'total' => $total];
    }

    /**
     * توزيع غير المباشر (نموذج توضيحي 60/40 على مركزين).
     * اربطه لاحقًا بقواعد التوزيع/Drivers.
     */
    public function allocateOverhead(CostBatch $batch): array
    {
        $created = 0;
        $total   = 0;

        $pools = overheadpool::query()
            ->where('status', 'approved')
            ->where('period_start', '<=', $batch->period_end)
            ->where('period_end', '>=', $batch->period_start)
            ->get();

        if ($pools->isEmpty()) {
            $pools = collect([ (object)['id'=>0,'amount'=>10000,'category'=>'administration'] ]);
        }

        $centers = costcenter::query()->orderBy('id')->limit(2)->get(['id']);
        if ($centers->count() < 2) {
            $centers = collect([(object)['id'=>1], (object)['id'=>2]]);
        }

        $shares = [0.6, 0.4];

        DB::transaction(function () use ($batch, $pools, $centers, $shares, &$created, &$total) {
            foreach ($pools as $pool) {
                foreach ($centers as $idx => $cc) {
                    $share = round($pool->amount * $shares[$idx % count($shares)], 6);

                    CostTransaction::create([
                        'batch_id'       => $batch->id,
                        'case_type'      => 'other',
                        'case_id'        => null,
                        'service_id'     => null,
                        'cost_center_id' => $cc->id,
                        'line_type'      => 'overhead',
                        'item_type'      => 'overhead',
                        'description'    => 'Overhead: '.$pool->category,
                        'qty'            => 1,
                        'unit_cost'      => $share,
                        'total_cost'     => $share,
                        'meta'           => ['pool_id' => $pool->id],
                    ]);

                    $created++;
                    $total += $share;
                }
            }
        });

        return ['count' => $created, 'total' => $total];
    }

    public function finalize(CostBatch $batch): void
    {
        $agg = $batch->transactions()
            ->selectRaw("SUM(CASE WHEN line_type='direct' THEN total_cost ELSE 0 END) as direct_total")
            ->selectRaw("SUM(CASE WHEN line_type='overhead' THEN total_cost ELSE 0 END) as overhead_total")
            ->first();

        $directCount   = $batch->transactions()->where('line_type','direct')->count();
        $overheadCount = $batch->transactions()->where('line_type','overhead')->count();

        $batch->update([
            'direct_count'   => $directCount,
            'overhead_count' => $overheadCount,
            'direct_total'   => $agg->direct_total ?? 0,
            'overhead_total' => $agg->overhead_total ?? 0,
            'grand_total'    => ($agg->direct_total ?? 0) + ($agg->overhead_total ?? 0),
            'status'         => 'completed',
        ]);
    }
}