<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\models\assets\assetconversion as AssetConversion;
use App\models\assets\assetitem as AssetItem;
use App\Models\supply_chain\productstorebalance as StoreBalance;
use App\models\supply_chain\storemovement as StoreMovement;

class asset_conversionscontroller extends Controller
{
    public function index(Request $r)
    {
        if ($r->wantsJson()) {
            $draw   = (int)$r->get('draw', 1);
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            $search = $r->input('search.value');

            $baseQ = AssetConversion::query()
                ->with(['product:id,name_ar,name_en,product_code','store:id,name_ar,name_en','asset:id,code,name_ar,name_en'])
                ->orderByDesc('id');

            if ($search) {
                $s = trim($search);
                $baseQ->where(function ($w) use ($s) {
                    $w->where('batch_no','like',"%$s%")
                      ->orWhereHas('product', function($q) use($s){
                          $q->where('name_ar','like',"%$s%")->orWhere('name_en','like',"%$s%")->orWhere('product_code','like',"%$s%");
                      })
                      ->orWhereHas('store', function($q) use($s){
                          $q->where('name_ar','like',"%$s%")->orWhere('name_en','like',"%$s%");
                      });
                });
            }

            $recordsTotal    = (clone $baseQ)->count();
            $recordsFiltered = $recordsTotal;

            $rows = $baseQ->skip($start)->take($length)->get();

            $data = [];
            $i = $start + 1;
            foreach ($rows as $x) {
                $data[] = [
                    'rownum'   => $i++,
                    'product'  => e(($x->product->name_ar ?? $x->product->name_en ?? '').' ('.$x->product->product_code.')'),
                    'store'    => e($x->store->name_ar ?? $x->store->name_en ?? ''),
                    'batch'    => e($x->batch_no ?? '-'),
                    'expiry'   => optional($x->expiry_date)->format('Y-m-d') ?? '-',
                    'qty'      => (float)$x->qty_in_base,
                    'status'   => $x->status === 'active'
                                    ? '<span class="badge bg-success">'.e(trans('assets.asset_conversions.status_active')).'</span>'
                                    : '<span class="badge bg-secondary">'.e(trans('assets.asset_conversions.status_cancelled')).'</span>',
                    'action'   => '<a href="'.route('assets.programs.asset_conversions.show', $x->id).'" class="btn btn-sm btn-primary">'.e(trans('assets.asset_conversions.btn_show')).'</a>',
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }

        return view('assets.programs.asset_conversions.index');
    }

    public function show($id)
    {
        $row = AssetConversion::with(['product','store','asset','receiving','receivingItem'])->findOrFail($id);
        return view('assets.programs.asset_conversions.show', compact('row'));
    }

public function cancel($id, Request $r)
{
    $reason = trim($r->input('reason'));

    DB::transaction(function () use ($id, $reason) {
        /** @var AssetConversion $conv */
        $conv = AssetConversion::lockForUpdate()->findOrFail($id);
        if ($conv->status === 'cancelled') {
            return;
        }

        // Helper صغير داخل الكتلة لحساب الكمية العرضية حسب وحدة السجل
        $recalculateDisplayQty = function (int $productId, ?int $unitId, float $qtyBase): float {
            if ($qtyBase <= 0 || !$unitId) return 0.0;

            $small = \App\Models\supply_chain\productunit::where('product_id', $productId)
                ->where('unit_type', 'small')
                ->first();

            if ($small && (int)$small->unit_small_id === (int)$unitId) {
                return $qtyBase; // أصغر وحدة = نفس الأساس
            }

            $large = \App\Models\supply_chain\productunit::where('product_id', $productId)
                ->where('unit_large_id', $unitId)
                ->first();

            if ($large && (float)$large->conversion_rate > 0) {
                return $qtyBase / (float)$large->conversion_rate;
            }

            return $qtyBase; // fallback
        };

        // Helper لتحديد أصغر وحدة عند الحاجة
        $smallUnitId = (function (int $productId): ?int {
            $row = \App\Models\supply_chain\productunit::where('product_id', $productId)
                ->where('unit_type', 'small')
                ->first();
            return $row?->unit_small_id;
        })($conv->product_id);

        // 1) إعادة الكمية إلى رصيد المخزن (batch/expiry)
        $balanceQuery = StoreBalance::withTrashed()->where([
            'product_id' => $conv->product_id,
            'store_id'   => $conv->store_id,
            'batch_no'   => $conv->batch_no,
        ]);

        // دعم expiry_date = NULL
        if ($conv->expiry_date) {
            $balanceQuery->whereDate('expiry_date', $conv->expiry_date);
        } else {
            $balanceQuery->whereNull('expiry_date');
        }

        /** @var StoreBalance|null $balance */
        $balance = $balanceQuery->first();

        if ($balance) {
            if ($balance->trashed()) {
                // restore ثم صفّر القيم القديمة لتجنب التضاعف
                $balance->restore();
                $balance->quantity_in_base_unit = 0;
                $balance->quantity = 0;
            }

            // تأكيد الوحدة: إما المسجلة أو وحدة التحويل أو أصغر وحدة
            $balance->units_id = $balance->units_id ?: ($conv->unit_id ?: $smallUnitId);

            // جمع الكمية (أساس) بتقريب يمنع السالب/العائم
            $balance->quantity_in_base_unit = round((float)$balance->quantity_in_base_unit + (float)$conv->qty_in_base, 6);

            // حساب الكمية العرضية حسب وحدة السجل
            $balance->quantity = $recalculateDisplayQty(
                (int)$conv->product_id,
                (int)$balance->units_id,
                (float)$balance->quantity_in_base_unit
            );

            $balance->save();
        } else {
            // لا يوجد رصيد مطابق: إنشئ سطر جديد
            $unitsId = $conv->unit_id ?: $smallUnitId;

            StoreBalance::create([
                'product_id'               => $conv->product_id,
                'store_id'                 => $conv->store_id,
                'batch_no'                 => $conv->batch_no,
                'expiry_date'              => $conv->expiry_date,
                'quantity_in_base_unit'    => (float)$conv->qty_in_base,
                'units_id'                 => $unitsId,
                'quantity'                 => $recalculateDisplayQty(
                    (int)$conv->product_id,
                    (int)$unitsId,
                    (float)$conv->qty_in_base
                ),
            ]);
        }

        // 2) إنقاص كمية الأصل
        if ($conv->asset_item_id) {
            $asset = AssetItem::find($conv->asset_item_id);
            if ($asset) {
                $asset->qty = max(0, (int)$asset->qty - (int)round((float)$conv->qty_in_base));
                $asset->save();
            }
        }

        // 3) حركة مخزنية معاكسة: adjustment_plus (إعادة للمخزن)
        $movementUnitId = $conv->unit_id ?: $smallUnitId;
        StoreMovement::create([
            'product_id'      => $conv->product_id,
            'store_id'        => $conv->store_id,
            'units_id'        => $movementUnitId,
            'supplier_id'     => null,
            'quantity'        => (float)$conv->qty_in_base,
            'batch_no'        => $conv->batch_no,
            'expiry_date'     => $conv->expiry_date,
            'movement_type'   => 'adjustment_plus',
            'movement_number' => null,
            'reference_no'    => 'AC:'.$conv->id,
            'movement_date'   => now(),
            'cost_price'      => $conv->cost_price,
            'sale_price'      => null,
            'notes'           => trans('assets.asset_conversions.mv_cancel_note').' #'.$conv->id,
            'user_id'         => Auth::id(),
        ]);

        // 4) تحديث حالة التحويل
        $conv->status        = 'cancelled';
        $conv->cancelled_at  = now();
        $conv->cancel_reason = $reason ?: null;
        $conv->save();
    });

    return back()->with('success', trans('assets.asset_conversions.msg_cancelled'));
}


    // (اختياري) تعديل الكمية: يطبّق فرق الكمية على المخزن والأصل ويسجل حركة ضبط مناسبة
    public function update($id, Request $r)
    {
        // يمكن تنفيذها لاحقاً بحسب السياسة المطلوبة للتعديل
        return back()->withErrors(['update' => trans('assets.asset_conversions.err_not_implemented')]);
    }
}
