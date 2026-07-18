<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\models\assets\assetitem as AssetItem;
use App\Models\supply_chain\productstorebalance as StoreBalance;
use App\Models\supply_chain\productdata as Product;
use App\Models\supply_chain\productunit as ProductUnit;
use App\Models\supply_chain\receivinginspecting as ReceivingInspecting;
use App\Models\supply_chain\receivinginspectingitem as ReceivingItem;
use App\models\supply_chain\storemovement as StoreMovement;

class convert_to_assetscontroller extends Controller
{
    /** قائمة محاضر Approved مع رصيد متاح (يدعم DataTables ServerSide) */
    public function index(Request $r)
    {
        // DataTables JSON
        if ($r->wantsJson()) {
            $draw   = (int)$r->get('draw', 1);
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            $search = $r->input('search.value');
            $supplierId = $r->input('supplier_id');
            $storeId    = $r->input('store_id');

            $baseQ = ReceivingInspecting::query()
                ->with(['supplier:id,name_ar,name_en','store:id,name_ar,name_en'])
                ->where('status', 'approved')
                ->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('receiving_inspecting_items as rii')
                        ->join('product_store_balances as psb', function ($j) {
                            $j->on('psb.product_id', '=', 'rii.product_id');
                        })
                        ->whereColumn('rii.receiving_inspecting_id', 'receiving_inspecting.id')
                        ->whereColumn('psb.store_id', 'receiving_inspecting.store_id')
                        ->where('psb.quantity_in_base_unit', '>', 0);
                });

            $recordsTotal = (clone $baseQ)->count();

            if ($supplierId) {
                $baseQ->where('supplier_id', (int)$supplierId);
            }
            if ($storeId) {
                $baseQ->where('store_id', (int)$storeId);
            }
            if ($search) {
                $s = trim($search);
                $baseQ->where(function ($w) use ($s) {
                    $w->where('code', 'like', "%$s%")
                      ->orWhereHas('supplier', function ($w2) use ($s) {
                          $w2->where('name_ar', 'like', "%$s%")
                             ->orWhere('name_en', 'like', "%$s%");
                      });
                });
            }

            $recordsFiltered = (clone $baseQ)->count();

            $rows = $baseQ->orderByDesc('id')
                ->skip($start)
                ->take($length)
                ->get();

            $data = [];
            $i = $start + 1;
            foreach ($rows as $d) {
                $data[] = [
                    'rownum'   => $i++,
                    'code'     => $d->code,
                    'date'     => optional($d->document_date)->format('Y-m-d'),
                    'supplier' => $d->supplier->name_ar ?? $d->supplier->name_en ?? '',
                    'store'    => $d->store->name_ar ?? $d->store->name_en ?? '',
                    'status'   => '<span class="badge bg-success">'.e(trans('assets.convert_to_assets.status_approved')).'</span>',
                    'action'   => '<a href="'.route('assets.programs.convert_to_assets.show', $d->id).'" class="btn btn-sm btn-primary"><i class="ri-arrow-right-line"></i> '.e(trans('assets.convert_to_assets.open_for_conversion')).'</a>',
                ];
            }

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        // لأجل Select2 في الفلاتر (مقدمًا)
        $suppliers = DB::table((new \App\Models\Suppliers\SuppliersData)->getTable())
            ->select('id', DB::raw("COALESCE(name_ar, name_en) as name"))
            ->orderBy('name')->get();

        $stores = DB::table((new \App\Models\supply_chain\storemaindata)->getTable())
            ->select('id', DB::raw("COALESCE(name_ar, name_en) as name"))
            ->orderBy('name')->get();

        return view('assets.programs.convert_to_assets.index', compact('suppliers','stores'));
    }

    /** عرض محضر + أرصدة متاحة لكل صنف */
    public function show($docId)
    {
        $doc = ReceivingInspecting::with([
            'supplier:id,name_ar,name_en',
            'store:id,name_ar,name_en',
            'items' => function ($q) {
                $q->with([
                    'product:id,name_ar,name_en,product_code',
                    'acceptedUnit:id,name_ar,name_en',
                ])->where('accepted_qty', '>', 0);
            },
        ])->findOrFail($docId);

        $balancesByProduct = [];
        $productIds = $doc->items->pluck('product_id')->unique()->values();

        if ($productIds->count()) {
            $balances = StoreBalance::query()
                ->with(['unit:id,name_ar,name_en'])
                ->where('store_id', $doc->store_id)
                ->whereIn('product_id', $productIds)
                ->where('quantity_in_base_unit', '>', 0)
                ->orderBy('expiry_date')
                ->get();

            foreach ($balances as $b) {
                $balancesByProduct[$b->product_id][] = $b;
            }
        }

        $smallUnits = $this->getSmallUnitsForProducts($productIds);

        return view('assets.programs.convert_to_assets.show', compact('doc','balancesByProduct','smallUnits'));
    }

    /** تنفيذ التحويل */
    public function convert($docId, Request $r)
    {
        $doc = ReceivingInspecting::with(['items.product'])->findOrFail($docId);

        $lines = $r->input('lines', []);
        if (!is_array($lines) || empty($lines)) {
            return back()->withErrors(['lines' => trans('assets.convert_to_assets.err_no_lines')])->withInput();
        }

        $results = ['converted'=>[], 'skipped'=>[]];

        DB::transaction(function () use ($lines, $doc, &$results) {
            foreach ($lines as $idx => $L) {

                // ❶ التحقق من الاختيار
                $selected = isset($L['selected']) && (string)$L['selected'] === '1';
                if (!$selected) {
                    $results['skipped'][] = ['idx'=>$idx,'reason'=>trans('assets.convert_to_assets.skip_not_selected')];
                    continue;
                }

                $productId  = (int)($L['product_id'] ?? 0);
                $balanceId  = (int)($L['balance_id'] ?? 0);
                $qtyBase    = (float)($L['qty_in_base'] ?? 0);
                $itemId     = (int)($L['receiving_item_id'] ?? 0);

                if ($productId <= 0 || $balanceId <= 0 || $qtyBase <= 0 || $itemId <= 0) {
                    $results['skipped'][] = ['idx'=>$idx,'reason'=>trans('assets.convert_to_assets.skip_incomplete')];
                    continue;
                }

                /** @var StoreBalance|null $balance */
                $balance = StoreBalance::lockForUpdate()->find($balanceId);
                if (!$balance || $balance->product_id != $productId || $balance->store_id != $doc->store_id) {
                    $results['skipped'][] = ['idx'=>$idx,'reason'=>trans('assets.convert_to_assets.skip_balance')];
                    continue;
                }

                if ($balance->quantity_in_base_unit < $qtyBase) {
                    $results['skipped'][] = ['idx'=>$idx,'reason'=>trans('assets.convert_to_assets.skip_insufficient')];
                    continue;
                }

                /** @var ReceivingItem|null $recItem */
                $recItem = ReceivingItem::find($itemId);
                if (!$recItem || $recItem->product_id != $productId || $recItem->receiving_inspecting_id != $doc->id) {
                    $results['skipped'][] = ['idx'=>$idx,'reason'=>trans('assets.convert_to_assets.skip_mismatch')];
                    continue;
                }

                $costPerBase = $this->costPerBaseFromReceivingItem($recItem, $productId);

                $product = $doc->items->firstWhere('id', $itemId)?->product ?? Product::find($productId);
                if (!$product) {
                    $results['skipped'][] = ['idx'=>$idx,'reason'=>trans('assets.convert_to_assets.skip_product_missing')];
                    continue;
                }

                $assetCode = $product->product_code;
                if (!$assetCode) {
                    $results['skipped'][] = ['idx'=>$idx,'reason'=>trans('assets.convert_to_assets.skip_code_missing')];
                    continue;
                }

                // ❷ ضمان وحدة الحركة (حل مشكلة units_id=null)
                $movementUnitId = $this->smallUnitId($productId);
                if (!$movementUnitId) {
                    // fallback: وحدة الرصيد -> ثم الوحدة المقبولة -> ثم وحدة السطر بالمحضر
                    $movementUnitId = $balance->units_id ?: ($recItem->accepted_unit_id ?: $recItem->unit_id);
                }
                if (!$movementUnitId) {
                    $results['skipped'][] = ['idx'=>$idx,'reason'=>trans('assets.convert_to_assets.skip_unit_missing')];
                    continue;
                }

                // أصل موجود؟ (code -> name)
                $asset = AssetItem::where('code', $assetCode)->first();
                if (!$asset) {
                    $asset = AssetItem::where(function ($q) use ($product) {
                        $q->where('name_ar', $product->name_ar ?? '')
                          ->orWhere('name_en', $product->name_en ?? '');
                    })->first();
                }

                if ($asset) {
                    $asset->qty = (int)$asset->qty + (int)round($qtyBase);
                    if (!$asset->status)        $asset->status        = 'in_service';
                    if (!$asset->purchase_date) $asset->purchase_date = $doc->document_date;
                    if (!$asset->supplier_id)   $asset->supplier_id   = $doc->supplier_id;
                    if (!$asset->store_id)      $asset->store_id      = $doc->store_id;
                    $asset->save();
                } else {
                    $asset = AssetItem::create([
                        'code'          => $assetCode,
                        'name_ar'       => $product->name_ar ?: $product->name_en ?: $assetCode,
                        'name_en'       => $product->name_en ?: $product->name_ar ?: $assetCode,
                        'status'        => 'in_service',
                        'qty'           => (int)round($qtyBase),
                        'cost'          => $costPerBase,
                        'purchase_date' => $doc->document_date,
                        'supplier_id'   => $doc->supplier_id,
                        'store_id'      => $doc->store_id,
                    ]);
                }

                /**
                 * ❸ خصم الرصيد مع معالجة الاستهلاك الكامل:
                 * - نحسب الكمية الجديدة بدقة (تقريب 6 خانات لتجنب السالب العائم)
                 * - عند الوصول للصفر: نعيّن الكميتين = 0، نحفظ، ثم SoftDelete
                 * - عند المتبقي: نحدّث الكميتين فقط
                 */
                $newQtyBase = round((float)$balance->quantity_in_base_unit - (float)$qtyBase, 6);

                if ($newQtyBase <= 0) {
                    // استهلاك كامل: صفّر القيم واحفظ ثم احذف (SoftDelete)
                    $balance->quantity_in_base_unit = 0;
                    $balance->quantity = 0;
                    $balance->save();   // مهم قبل الحذف حتى لو رجعناه بـ restore يبدأ من 0
                    $balance->delete(); // soft delete
                } else {
                    // متبقي: حدّث القيم ثم احفظ
                    $balance->quantity_in_base_unit = $newQtyBase;
                    $balance->quantity = $this->recalculateDisplayQty(
                        $balance->product_id,
                        $balance->units_id,
                        $newQtyBase
                    );
                    $balance->save();
                }

                // حركة مخزنية: withdrawal (بوحدة مؤكدة)
                StoreMovement::create([
                    'product_id'      => $productId,
                    'store_id'        => $doc->store_id,
                    'units_id'        => $movementUnitId, // ✅ لن تكون null الآن
                    'supplier_id'     => $doc->supplier_id,
                    'quantity'        => $qtyBase,
                    'batch_no'        => $balance->batch_no,
                    'expiry_date'     => $balance->expiry_date,
                    'movement_type'   => 'withdrawal',
                    'movement_number' => null,
                    'reference_no'    => 'RI:'.$doc->code,
                    'movement_date'   => now(),
                    'cost_price'      => $costPerBase,
                    'sale_price'      => null,
                    'notes'           => trans('assets.convert_to_assets.mv_note_prefix').' '.$doc->code,
                    'user_id'         => Auth::id(),
                ]);

                // سجل التحويل
                DB::table('asset_conversions')->insert([
                    'receiving_inspecting_id' => $doc->id,
                    'receiving_item_id'       => $recItem->id,
                    'product_id'              => $productId,
                    'store_id'                => $doc->store_id,
                    'batch_no'                => $balance->batch_no,
                    'expiry_date'             => $balance->expiry_date,
                    'unit_id'                 => $movementUnitId,
                    'qty_in_unit'             => $qtyBase,
                    'qty_in_base'             => $qtyBase,
                    'cost_price'              => $costPerBase,
                    'asset_item_id'           => $asset->id ?? null,
                    'conversion_date'         => now(),
                    'user_id'                 => Auth::id(),
                    'notes'                   => null,
                    'status'                  => 'active',
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);

                $results['converted'][] = [
                    'product_id' => $productId,
                    'qty_base'   => $qtyBase,
                    'asset_id'   => $asset->id,
                ];
            }
        });

        if (empty($results['converted'])) {
            return back()->withErrors(['convert' => trans('assets.convert_to_assets.err_nothing_converted')])->withInput();
        }

        return redirect()
            ->route('assets.programs.convert_to_assets.show', $doc->id)
            ->with('success', trans('assets.convert_to_assets.success_msg', ['n' => count($results['converted'])]))
            ->with('skipped', $results['skipped']);
    }

    /** ========== Helpers ========== */

    protected function getSmallUnitsForProducts($productIds)
    {
        if (!$productIds || count($productIds) === 0) return [];
        $rows = ProductUnit::whereIn('product_id', $productIds)
            ->where('unit_type', 'small')
            ->get(['product_id','unit_small_id']);
        $map = [];
        foreach ($rows as $r) $map[$r->product_id] = $r->unit_small_id;
        return $map;
    }

    protected function smallUnitId($productId)
    {
        static $cache = [];
        if (!isset($cache[$productId])) {
            $row = ProductUnit::where('product_id', $productId)->where('unit_type','small')->first();
            $cache[$productId] = $row?->unit_small_id;
        }
        return $cache[$productId] ?? null;
    }

    protected function toBaseQty($productId, $unitId, $qty)
    {
        if ($qty <= 0 || !$unitId) return 0;
        $small = ProductUnit::where('product_id',$productId)->where('unit_type','small')->first();
        if ($small && (int)$small->unit_small_id === (int)$unitId) {
            return (float)$qty;
        }
        $large = ProductUnit::where('product_id',$productId)->where('unit_large_id',$unitId)->first();
        if ($large && $large->conversion_rate > 0) {
            return (float)$qty * (float)$large->conversion_rate;
        }
        return (float)$qty;
    }

    protected function recalculateDisplayQty($productId, $unitId, $qtyBase)
    {
        if ($qtyBase <= 0) return 0;
        $small = ProductUnit::where('product_id',$productId)->where('unit_type','small')->first();
        if ($small && (int)$small->unit_small_id === (int)$unitId) {
            return $qtyBase;
        }
        $large = ProductUnit::where('product_id',$productId)->where('unit_large_id',$unitId)->first();
        if ($large && $large->conversion_rate > 0) {
            return $qtyBase / $large->conversion_rate;
        }
        return $qtyBase;
    }

    protected function costPerBaseFromReceivingItem(ReceivingItem $ri, $productId)
    {
        $cost = (float)($ri->cost_price ?? 0);
        if ($cost <= 0) return 0.0;

        $accUnitId = $ri->accepted_unit_id ?: $ri->unit_id;

        $small = ProductUnit::where('product_id',$productId)->where('unit_type','small')->first();
        if ($small && (int)$small->unit_small_id === (int)$accUnitId) {
            return $cost;
        }
        $large = ProductUnit::where('product_id',$productId)->where('unit_large_id',$accUnitId)->first();
        if ($large && $large->conversion_rate > 0) {
            return $cost / $large->conversion_rate;
        }
        return $cost;
    }
}
