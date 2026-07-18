<?php
namespace App\Services\SupplyChain;

use App\Models\supply_chain\productunit;
use App\Models\supply_chain\unit;

class ProductUnitService
{
    public function calculateSmallestUnits($product_id, $unit_id)
    {
        $units = productunit::where('product_id', $product_id)->get();

        if ($units->isEmpty()) {
            return null;
        }

        $rate = 1;
        $current_large_unit_id = $unit_id;
        $loop_count = 0;

        while ($loop_count < 10) {
            // تخطي أي صف علاقته ناقصة
            $current = $units
                ->where('unit_large_id', $current_large_unit_id)
                ->first(fn($u) => !is_null($u->unit_small_id));

            if (!$current) break;

            $rate *= $current->conversion_rate;
            $current_large_unit_id = $current->unit_small_id;
            $loop_count++;
        }

        // جلب اسم أصغر وحدة
        $smallest_unit = unit::find($current_large_unit_id)?->name_ar ?? 'غير معروف';

        return [
            'total_small_units' => $rate,
            'smallest_unit' => $smallest_unit,
        ];
    }
}
