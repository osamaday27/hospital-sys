<?php

namespace App\Services\SupplyChain;

use App\Models\supply_chain\productunit;
use App\Models\supply_chain\unit;
use App\Models\supply_chain\productstorebalance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * UnitConversionService
 *
 * ملاحظات:
 * - صف product_units يعبّر عن: 1 (unit_large_id) = conversion_rate × (unit_small_id)
 * - إن وُجد cost_price / sale_price في الصف، نعتبره سعر "الوحدة الكبيرة" لذلك الصف،
 *   ونقوم بنشر الأسعار لباقي الوحدات تلقائيًا.
 * - أصغر وحدة تُحسب كالعقدة التي لا تخرج لعقد أصغر (لا تظهر كـ unit_large_id).
 */
class UnitConversionService
{
    /** مدة الكاش (ثواني) */
    protected int $ttl = 600;

    /** ابني جراف الوحدات والعلاقات للصنف */
protected function buildGraph(int $product_id): array
{
    // اجعل الكاش يعتمد على آخر تحديث وعدد الصفوف
    $maxUpdated = productunit::where('product_id', $product_id)->max('updated_at');
    $maxUpdatedKey = $maxUpdated ? (is_string($maxUpdated) ? strtotime($maxUpdated) : $maxUpdated->getTimestamp()) : 0;
    $rowsCount = (int) productunit::where('product_id', $product_id)->count();
    $cacheKey = "unit_graph_{$product_id}_{$rowsCount}_{$maxUpdatedKey}";

    return Cache::remember($cacheKey, $this->ttl, function () use ($product_id) {
        $rows = productunit::where('product_id', $product_id)
            ->orderBy('id')
            ->get(['id','unit_large_id','unit_small_id','conversion_rate','cost_price','sale_price','updated_at']);

        $nodes = [];
        $adj   = [];      // علاقات باتجاهين
        $out   = [];      // لتحديد أصغر الوحدات
        $cands = [];      // مرشّحات الأسعار لكل unit_id

        foreach ($rows as $r) {
            $L = (int) $r->unit_large_id;
            $S = $r->unit_small_id ? (int) $r->unit_small_id : null;
            $rate = max(1.0, (float) $r->conversion_rate);

            $nodes[$L] = true;
            if ($S) $nodes[$S] = true;

            // ابني الجراف: 1 كبير = rate × صغير
            if ($S) {
                $adj[$L][] = ['to' => $S, 'w' => $rate];
                $adj[$S][] = ['to' => $L, 'w' => 1.0 / $rate];
                $out[$L] = ($out[$L] ?? 0) + 1;
            }

            // اجمع مرشّحات الأسعار:
            // - سطر Base (S=null): السعر للوحدة الكبيرة L
            // - سطر علاقة: السعر للوحدة الصغيرة S
            $targetUnitForPrice = $S ?? $L;
            if (!is_null($r->cost_price) || !is_null($r->sale_price)) {
                $cands[$targetUnitForPrice][] = $r;
            }
        }

        // اختَر سعراً صريحاً لكل وحدة مع تفضيل الأحدث
        $seedPrices = [];
        foreach ($cands as $unitId => $list) {
            $chosen = collect($list)->sortByDesc(function ($x) {
                $ts = $x->updated_at ? (is_string($x->updated_at) ? strtotime($x->updated_at) : $x->updated_at->getTimestamp()) : 0;
                return $ts.'|'.$x->id;
            })->first();

            $seedPrices[$unitId] = [
                'cost' => is_null($chosen->cost_price) ? null : (float) $chosen->cost_price,
                'sale' => is_null($chosen->sale_price) ? null : (float) $chosen->sale_price,
            ];
        }

        $unitIds  = array_keys($nodes);
        $smallest = array_values(array_diff($unitIds, array_keys($out))); // عقد بلا خروج ⇒ أصغر وحدات

        return [
            'unit_ids'   => $unitIds,
            'adj'        => $adj,
            'smallest'   => $smallest,
            'seedPrices' => $seedPrices, // مُمَثَّلة لكل unit_id مباشرة
        ];
    });
}



    /** BFS لإيجاد معامل التحويل من وحدة لأخرى عبر أي مسار */
    protected function factor(array $g, int $from, int $to): ?float
    {
        if ($from === $to) return 1.0;
        $adj = $g['adj'] ?? [];
        if (!isset($adj[$from])) return null;

        $q = [[$from, 1.0]];
        $seen = [$from => true];

        while ($q) {
            [$u, $acc] = array_shift($q);

            foreach ($adj[$u] ?? [] as $edge) {
                $v = $edge['to'];
                if (isset($seen[$v])) continue;

                $f = $acc * (float) $edge['w'];
                if ($v === $to) return $f;

                $seen[$v] = true;
                $q[] = [$v, $f];
            }
        }
        return null;
    }

    /** أصغر وحدة (نختار أول واحدة بشكل ثابت) */
    public function smallestUnitId(int $product_id): ?int
    {
        $g = $this->buildGraph($product_id);
        return $g['smallest'][0] ?? null;
    }

    /** تحويل كمية من وحدة إلى أخرى */
    public function convert(int $product_id, float $qty, int $from_unit_id, int $to_unit_id): float
    {
        $g = $this->buildGraph($product_id);
        $f = $this->factor($g, $from_unit_id, $to_unit_id);
        if (is_null($f)) {
            throw new \RuntimeException('لا يوجد مسار تحويل بين الوحدتين لهذا الصنف.');
        }
        return $qty * $f;
    }

    /** تطبيع الكمية إلى أصغر وحدة (قاعدة التخزين) */
    public function toBase(int $product_id, float $qty, int $unit_id): float
    {
        $base = $this->smallestUnitId($product_id);
        if (!$base) return $qty;
        return $this->convert($product_id, $qty, $unit_id, $base);
    }

    /** تحويل من أصغر وحدة إلى أي وحدة هدف */
    public function fromBase(int $product_id, float $qty_in_base, int $target_unit_id): float
    {
        $base = $this->smallestUnitId($product_id);
        if (!$base) return $qty_in_base;
        return $this->convert($product_id, $qty_in_base, $base, $target_unit_id);
    }

    /**
     * إرجاع جميع الوحدات المتاحة للصنف مع:
     * - factor_to_base: كم "أصغر وحدة" في 1 من هذه الوحدة
     * - factor_from_base: كم من هذه الوحدة في 1 أصغر وحدة
     * - الأسعار المحسوبة لكل وحدة (cost_per_unit, sale_per_unit)
     * $nameColumn: 'name_ar' أو 'name_en'…
     */
    public function unitsForProduct(int $product_id, string $nameColumn = 'name_ar'): Collection
    {
        $g = $this->buildGraph($product_id);
        $unitIds = $g['unit_ids'];
        $base = $this->smallestUnitId($product_id);

        $names = unit::whereIn('id', $unitIds)->pluck($nameColumn, 'id');

        // عوامل التحويل إلى أصغر وحدة
        $toBase = [];
        foreach ($unitIds as $u) {
            $toBase[$u] = $base ? $this->factor($g, $u, $base) : 1.0;
        }

        // نشر الأسعار لباقي الوحدات
        $prices = $this->propagatePrices($g);

        return collect($unitIds)->map(function (int $u) use ($names, $toBase, $base, $prices) {
            $ftb = $toBase[$u] ?: 1.0; // 1 من هذه الوحدة = ftb من أصغر وحدة
            return [
                'unit_id'            => $u,
                'unit_name'          => $names[$u] ?? "#{$u}",
                'is_base'            => $base ? ($u === $base) : false,
                'factor_to_base'     => $ftb,
                'factor_from_base'   => $ftb > 0 ? (1.0 / $ftb) : null,
                'cost_per_unit'      => $prices['cost'][$u] ?? null,
                'sale_per_unit'      => $prices['sale'][$u] ?? null,
            ];
        })->sortByDesc('factor_to_base')->values();
    }

    /** نشر الأسعار عبر الجراف */
    protected function propagatePrices(array $g): array
    {
        $adj  = $g['adj'] ?? [];
        $seed = $g['seedPrices'] ?? [];

        $cost = []; $sale = [];

        foreach ($seed as $u => $p) {
            if (!is_null($p['cost'])) $cost[$u] = (float) $p['cost'];
            if (!is_null($p['sale'])) $sale[$u] = (float) $p['sale'];
        }

        $queue = array_keys($seed);
        $seen  = [];

        while ($queue) {
            $u = array_shift($queue);
            if (isset($seen[$u])) continue;
            $seen[$u] = true;

            foreach ($adj[$u] ?? [] as $edge) {
                $v = $edge['to'];
                $f = (float) $edge['w']; // 1 u = f v  => price[v] = price[u] / f

                $updated = false;
                if (isset($cost[$u]) && !isset($cost[$v])) { $cost[$v] = $cost[$u] / $f; $updated = true; }
                if (isset($sale[$u]) && !isset($sale[$v])) { $sale[$v] = $sale[$u] / $f; $updated = true; }
                if ($updated) $queue[] = $v;
            }
        }

        return ['cost' => $cost, 'sale' => $sale];
    }

    /**
     * إجمالي الكمية المتاحة بأي وحدة مطلوبة.
     * يعتمد أولًا على عمودك القياسي quantity_in_base_unit،
     * وإن لم يتوفر (أو كان صفرًا لبعض السطور)، سيحوّل (quantity, units_id) إلى أصغر وحدة ويجمعها.
     */
    public function getAvailableQtyByUnit(
        int $product_id,
        int $target_unit_id,
        ?int $store_id = null,
        ?string $batch_no = null,
        ?string $expiry_date = null
    ): float {
        $q = productstorebalance::query()->where('product_id', $product_id);

        if (!is_null($store_id))    $q->where('store_id', $store_id);
        if (!is_null($batch_no))    $q->where('batch_no', $batch_no);
        if (!is_null($expiry_date)) $q->whereDate('expiry_date', $expiry_date);

        // نجمع مباشرة من العمود القياسي
        $sumBase = (float) $q->sum('quantity_in_base_unit');

        // لو كان صفر/NULL لبعض السطور، نحسب fallback بتحويل كل سطر يعتمد (quantity, units_id)
        if ($sumBase <= 0.0) {
            $rows = $q->get(['units_id','quantity']);
            $base = $this->smallestUnitId($product_id);
            $sum  = 0.0;

            foreach ($rows as $r) {
                $u = (int) ($r->units_id ?? 0);
                $qty = (float) ($r->quantity ?? 0);
                if ($u && $qty) {
                    try {
                        $sum += $this->toBase($product_id, $qty, $u);
                    } catch (\Throwable $e) {
                        // تجاهل سطر لا نقدر نحوله
                    }
                }
            }
            $sumBase = $sum;
        }

        return $this->fromBase($product_id, $sumBase, $target_unit_id);
    }

    /**
     * فكّ كمية (بالوحدة الأساسية/الأصغر) إلى تمثيل مختلط (مثل 3 دستة + 1 باكيت + 2 عدد).
     * مفيد للعرض.
     */
    public function decomposeFromBase(int $product_id, float $qty_base, string $nameColumn = 'name_ar'): array
    {
        $units = $this->unitsForProduct($product_id, $nameColumn)
                      ->sortByDesc('factor_from_base')
                      ->values();

        $remaining = $qty_base;
        $parts = [];

        foreach ($units as $u) {
            $f = (float) $u['factor_to_base']; // كم قاعدة في 1 من هذه الوحدة
            if ($f <= 0) continue;

            $count = intdiv((int) floor($remaining), (int) $f);
            if ($count > 0) {
                $parts[] = [
                    'unit_id'   => $u['unit_id'],
                    'unit_name' => $u['unit_name'],
                    'qty'       => $count,
                ];
                $remaining -= $count * $f;
            }
        }

        if ($remaining > 0) {
            $baseId = $this->smallestUnitId($product_id);
            $baseName = unit::find($baseId)?->{$nameColumn} ?? 'Base';
            $parts[] = ['unit_id' => $baseId, 'unit_name' => $baseName, 'qty' => $remaining];
        }

        return $parts;
    }
}
