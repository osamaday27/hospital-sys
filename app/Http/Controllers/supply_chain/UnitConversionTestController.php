<?php

namespace App\Http\Controllers\Supply_Chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\supply_chain\productdata;
use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\Unit;

use App\Services\SupplyChain\UnitConversionService;

class UnitConversionTestController extends Controller
{
    public function index(Request $request)
    {
        $products = productdata::orderBy('name_ar')
            ->get(['id', 'system_code', 'name_ar'])
            ->mapWithKeys(fn($p) => [$p->id => ($p->system_code ? "{$p->system_code} / " : '') . $p->name_ar]);

        $stores = storemaindata::orderBy('name_ar')->pluck('name_ar', 'id');

        // عرض الصفحة بدون نتائج أولية
        return view('dev.unit_conversion_test', [
            'products' => $products,
            'stores'   => $stores,
            'units'    => collect(),   // تُملأ بعد POST أو عبر JSON
            'result'   => null,
            'availableByUnit' => [],
            'selected' => [
                'product_id' => null,
                'store_id'   => null,
                'batch_no'   => null,
                'expiry_date'=> null,
                'qty'        => null,
                'from_unit'  => null,
                'to_unit'    => null,
            ],
        ]);
    }

    public function unitsJson(int $product, UnitConversionService $svc)
    {
        // يرجّع وحدات الصنف مع العوامل والأسعار
        $units = $svc->unitsForProduct($product, app()->getLocale() === 'ar' ? 'name_ar' : 'name_en');
        return response()->json($units->values());
    }

    public function calc(Request $request, UnitConversionService $svc)
    {
        $data = $request->validate([
            'product_id'   => ['required','integer','exists:products_data,id'],
            'store_id'     => ['nullable','integer','exists:store_main_date,id'],
            'batch_no'     => ['nullable','string','max:100'],
            'expiry_date'  => ['nullable','date'],
            'qty'          => ['required','numeric','min:0'],
            'from_unit_id' => ['required','integer','exists:units,id'],
            'to_unit_id'   => ['required','integer','exists:units,id'],
        ]);

        $product_id   = (int) $data['product_id'];
        $store_id     = $data['store_id'] ?? null;
        $batch_no     = $data['batch_no'] ?? null;
        $expiry_date  = $data['expiry_date'] ?? null;
        $qty          = (float) $data['qty'];
        $from_unit_id = (int) $data['from_unit_id'];
        $to_unit_id   = (int) $data['to_unit_id'];

        // وحدات الصنف (للجداول والقوائم)
        $units = $svc->unitsForProduct($product_id, app()->getLocale() === 'ar' ? 'name_ar' : 'name_en');

        // تحويلات
        $qty_base   = $svc->toBase($product_id, $qty, $from_unit_id);
        $qty_target = $svc->convert($product_id, $qty, $from_unit_id, $to_unit_id);
        $decompose  = $svc->decomposeFromBase($product_id, $qty_base, app()->getLocale() === 'ar' ? 'name_ar' : 'name_en');

        // المتاح بالمخزن/الباتش/الصلاحية بكل وحدة
        $availableByUnit = [];
        foreach ($units as $u) {
            $availableByUnit[$u['unit_id']] = $svc->getAvailableQtyByUnit(
                $product_id,
                $u['unit_id'],
                $store_id,
                $batch_no,
                $expiry_date
            );
        }

        $products = productdata::orderBy('name_ar')
            ->get(['id', 'system_code', 'name_ar'])
            ->mapWithKeys(fn($p) => [$p->id => ($p->system_code ? "{$p->system_code} / " : '') . $p->name_ar]);

        $stores = storemaindata::orderBy('name_ar')->pluck('name_ar', 'id');

        return view('dev.unit_conversion_test', [
            'products' => $products,
            'stores'   => $stores,
            'units'    => $units,
            'availableByUnit' => $availableByUnit,
            'result' => [
                'qty_input'   => $qty,
                'qty_base'    => $qty_base,
                'qty_target'  => $qty_target,
                'decompose'   => $decompose,
            ],
            'selected' => [
                'product_id' => $product_id,
                'store_id'   => $store_id,
                'batch_no'   => $batch_no,
                'expiry_date'=> $expiry_date,
                'qty'        => $qty,
                'from_unit'  => $from_unit_id,
                'to_unit'    => $to_unit_id,
            ],
        ]);
    }
}
