<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\storeproductmodel;
use App\Models\supply_chain\productstorebalance;
use App\models\supply_chain\productdata;
use App\Models\supply_chain\productunit;
use App\Models\supply_chain\AdjustBalance;
use App\models\supply_chain\storemovement;

class adjust_balancecontroller extends Controller
{
    public function index()
    {
        return view('supply_chain.program.adjust_balance.index');
    }

    public function ajaxList(Request $request)
    {
        $start   = (int) $request->input('start', 0);
        $length  = (int) $request->input('length', 10);
        $length  = $length > 0 ? min($length, 500) : 10;

        $search  = trim($request->input('search.value', ''));
        $storeId = (int) $request->input('store_id', 0);
        $type    = $request->input('adjust_type', '');

        $base = DB::table('adjust_balance as ab')
            ->join('store_main_date as s', 's.id', '=', 'ab.store_id')
            ->join('products_data as p', 'p.id', '=', 'ab.product_id')
            ->leftJoin('product_units as pu', 'pu.id', '=', 'ab.product_unit_id')
            ->leftJoin('units as us', 'us.id', '=', 'pu.unit_small_id')
            ->leftJoin('units as ul', 'ul.id', '=', 'pu.unit_large_id')
            ->whereNull('ab.deleted_at');

        if ($storeId) $base->where('ab.store_id', $storeId);
        if (in_array($type, ['increase','decrease'])) $base->where('ab.adjust_type', $type);

        if ($search !== '') {
            $base->where(function($q) use ($search){
                $q->where('ab.code', 'like', "%{$search}%")
                  ->orWhere('p.name_ar', 'like', "%{$search}%")
                  ->orWhere('p.name_en', 'like', "%{$search}%")
                  ->orWhere('s.name_ar', 'like', "%{$search}%")
                  ->orWhere('s.name_en', 'like', "%{$search}%")
                  ->orWhere('ab.batch_no', 'like', "%{$search}%");
            });
        }

        $recordsTotal = (clone $base)->count();

        $rows = $base->select([
                'ab.id','ab.code','ab.movement_datetime','ab.adjust_type','ab.batch_no',
                'ab.quantity','ab.balance','ab.sale_price','ab.cost_price','ab.expiry_date','ab.notes',
                DB::raw('COALESCE(s.name_ar, s.name_en) as store_name'),
                DB::raw('COALESCE(p.name_ar, p.name_en) as product_name'),
                // ✅ اعرض اسم الوحدة الفعلي: الصغيرة إن وُجدت، وإلا الكبيرة
                DB::raw("CASE WHEN pu.unit_small_id IS NOT NULL
                        THEN COALESCE(us.name_ar, us.name_en)
                        ELSE COALESCE(ul.name_ar, ul.name_en)
                    END as unit_name"),
            ])
            ->orderByDesc('ab.movement_datetime')
            ->orderByDesc('ab.id')
            ->offset($start)->limit($length)->get();

        return response()->json([
            'draw'            => (int)$request->input('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data'            => $rows,
        ]);
    }

    public function create()
    {
        return view('supply_chain.program.adjust_balance.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'store_id'          => ['required','integer', Rule::exists('store_main_date', 'id')->where(fn($q)=>$q->where('status', 1))],
            'movement_datetime' => ['required','date'],
            'adjust_type'       => ['required','in:increase,decrease'],

            'items'             => ['required','array','min:1'],
            'items.*.product_id'      => ['required','integer', Rule::exists('products_data','id')],
            'items.*.product_unit_id' => ['required','integer', Rule::exists('product_units','id')],
            'items.*.unit_id'         => ['required','integer', Rule::exists('units','id')],
            'items.*.quantity'        => ['required','numeric','gt:0'],
            'items.*.batch_no'        => ['nullable','string','max:100'],
            'items.*.expiry_date'     => ['nullable','date'],
            'items.*.notes'           => ['nullable','string'],
            'items.*.sale_price'      => ['nullable','numeric'],
            'items.*.cost_price'      => ['nullable','numeric'],
        ];
        $data = Validator::make($request->all(), $rules)->validate();

        $storeId   = (int)$data['store_id'];
        $adjType   = $data['adjust_type'];
        $moveAt    = Carbon::parse($data['movement_datetime']);

        $seen = [];
        foreach ($data['items'] as $it) {
            $key = $it['product_id'].'__'.mb_strtolower(trim($it['batch_no'] ?? ''));
            if (isset($seen[$key])) {
                return response()->json(['message' => 'لا يمكن تكرار نفس الصنف مع نفس الباتش ضمن نفس التسوية.'], 422);
            }
            $seen[$key] = true;
        }

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $it) {
                $productId   = (int)$it['product_id'];
                /** @var productunit $productUnit */
                $productUnit = productunit::findOrFail((int)$it['product_unit_id']);

                if ((int)$productUnit->product_id !== $productId) {
                    throw new \Exception('الوحدة المختارة لا تنتمي لهذا الصنف.');
                }

                $unitId = (int)$it['unit_id'];
                if (!in_array(
                        $unitId,
                        array_filter([(int)$productUnit->unit_small_id, (int)$productUnit->unit_large_id], fn($x)=>$x>0),
                        true
                    )) {
                    throw new \Exception('الوحدة المختارة غير متطابقة مع صف الوحدة المحدد.');
                }

                $existsInStore = storeproductmodel::query()
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->exists();
                if (!$existsInStore) {
                    throw new \Exception('الصنف غير مرتبط بالمخزن المختار.');
                }

                $factorMap = $this->factorMapToSmallest($productId);
                $factor    = $factorMap[$unitId] ?? 1.0;

                $salePrice = array_key_exists('sale_price', $it) && $it['sale_price'] !== null && $it['sale_price'] !== ''
                           ? (float)$it['sale_price']
                           : ($productUnit->sale_price ?? null);
                $costPrice = array_key_exists('cost_price', $it) && $it['cost_price'] !== null && $it['cost_price'] !== ''
                           ? (float)$it['cost_price']
                           : ($productUnit->cost_price ?? null);

                $actualQtyUnit = (float)$it['quantity'];
                $actualQtyBase = round($actualQtyUnit * $factor, 5);
                $batch         = trim($it['batch_no'] ?? '');
                $expiry        = !empty($it['expiry_date']) ? Carbon::parse($it['expiry_date'])->format('Y-m-d') : null;
                $notes         = $it['notes'] ?? null;

                $psb = productstorebalance::query()
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->when(true, function($q) use ($batch){
                        if ($batch === '') {
                            $q->where(function($x){ $x->whereNull('batch_no')->orWhere('batch_no',''); });
                        } else { $q->where('batch_no', $batch); }
                    })
                    ->lockForUpdate()
                    ->first();

                $currentBase = (float)($psb->quantity_in_base_unit ?? 0);
                $currentUnit = $factor > 0 ? $currentBase / $factor : 0;

                if ($adjType === 'increase' && !($actualQtyUnit > $currentUnit)) {
                    throw new \Exception('في حالة الزيادة يجب أن تكون الكمية الفعلية أكبر من الرصيد الحالي.');
                }
                if ($adjType === 'decrease' && !($actualQtyUnit < $currentUnit)) {
                    throw new \Exception('في حالة النقص يجب أن تكون الكمية الفعلية أقل من الرصيد الحالي.');
                }

                $deltaUnit = $actualQtyUnit - $currentUnit;

                if ($psb) {
                    $psb->quantity_in_base_unit = $actualQtyBase;
                    $psb->quantity              = $actualQtyUnit;
                    $psb->units_id              = $unitId ?: $psb->units_id;
                    $psb->expiry_date           = $expiry ?? $psb->expiry_date;
                    $psb->save();
                } else {
                    if ($adjType === 'decrease') {
                        throw new \Exception('لا يوجد رصيد سابق لهذا الصنف/الباتش؛ لا يمكن عمل تسوية بالنقص.');
                    }
                    productstorebalance::create([
                        'store_id'              => $storeId,
                        'product_id'            => $productId,
                        'units_id'              => $unitId ?: null,
                        'batch_no'              => $batch ?: null,
                        'expiry_date'           => $expiry,
                        'quantity'              => $actualQtyUnit,
                        'quantity_in_base_unit' => $actualQtyBase,
                    ]);
                }

                $code = $this->generateCode();

                AdjustBalance::create([
                    'code'              => $code,
                    'store_id'          => $storeId,
                    'movement_datetime' => $moveAt,
                    'adjust_type'       => $adjType,
                    'product_id'        => $productId,
                    'product_unit_id'   => $productUnit->id,
                    'quantity'          => $actualQtyUnit,
                    'batch_no'          => $batch ?: null,
                    'balance'           => $actualQtyUnit,
                    'sale_price'        => $salePrice,
                    'cost_price'        => $costPrice,
                    'expiry_date'       => $expiry,
                    'notes'             => $notes,
                ]);

                storemovement::create([
                    'product_id'    => $productId,
                    'store_id'      => $storeId,
                    'units_id'      => $unitId ?: null,
                    'quantity'      => $deltaUnit,
                    'batch_no'      => $batch ?: null,
                    'expiry_date'   => $expiry,
                    'movement_type' => $adjType === 'increase' ? 'adjustment_plus' : 'adjustment_minus',
                    'reference_no'  => $code,
                    'movement_date' => $moveAt->toDateString(),
                    'cost_price'    => $costPrice,
                    'sale_price'    => $salePrice,
                    'notes'         => $notes,
                    'user_id'       => auth()->id() ?: null,
                ]);
            }

            DB::commit();
            return response()->json([
                'success'  => true,
                'message'  => 'تمت التسوية وتحديث الأرصدة وتسجيل الحركة بنجاح.',
                'redirect' => route('adjust_balance.index'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /* ========================= AJAX HELPERS ========================= */

    public function ajaxStores(Request $request)
    {
        $q = trim($request->input('q',''));
        $rows = storemaindata::query()
            ->where('status', 1)
            ->when($q !== '', function($qq) use ($q){
                $qq->where(function($x) use ($q){
                    $x->where('name_ar','like',"%{$q}%")
                      ->orWhere('name_en','like',"%{$q}%")
                      ->orWhere('manual_code','like',"%{$q}%");
                });
            })
            ->orderBy('name_ar')
            ->limit(30)->get();

        $res = $rows->map(fn($s)=>[
            'id'   => $s->id,
            'text' => ($s->name_ar ?? $s->name_en ?? ('#'.$s->id)).( $s->manual_code ? " - {$s->manual_code}" : '' ),
        ]);
        return response()->json($res);
    }

    public function ajaxProductsByStore(Request $request)
    {
        $storeId = (int) $request->input('store_id', 0);
        $q       = trim($request->input('q',''));
        if (!$storeId) return response()->json([]);

        $rows = DB::table('store_products as sp')
            ->join('products_data as p', 'p.id', '=', 'sp.product_id')
            ->where('sp.store_id', $storeId)
            ->whereNull('sp.deleted_at')
            ->whereNull('p.deleted_at')
            ->when($q !== '', function($qq) use ($q){
                $qq->where(function($x) use ($q){
                    $x->where('p.name_ar','like',"%{$q}%")
                      ->orWhere('p.name_en','like',"%{$q}%")
                      ->orWhere('p.product_code','like',"%{$q}%")
                      ->orWhere('p.system_code','like',"%{$q}%")
                      ->orWhere('p.barcode','like',"%{$q}%");
                });
            })
            ->select([
                'p.id',
                DB::raw("CONCAT(COALESCE(p.product_code,''),' - ',COALESCE(p.name_ar,p.name_en,'')) as text")
            ])
            ->orderBy('p.name_ar')
            ->limit(30)->get();

        return response()->json($rows);
    }

    /**
     * وحدات المنتج + عامل التحويل + الأسعار المخزّنة لكل وحدة كما هي.
     */
    public function ajaxProductUnitsBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || !count($ids)) return response()->json([]);

        $all = DB::table('product_units as pu')
            ->leftJoin('units as ul', 'ul.id', '=', 'pu.unit_large_id')
            ->leftJoin('units as us', 'us.id', '=', 'pu.unit_small_id')
            ->whereIn('pu.product_id', $ids)
            ->whereNull('pu.deleted_at')
            ->select([
                'pu.id',
                'pu.product_id',
                'pu.unit_large_id',
                'pu.unit_small_id',
                'pu.conversion_rate',
                'pu.sale_price',
                'pu.cost_price',
                'pu.unit_type',
                DB::raw('COALESCE(ul.name_ar, ul.name_en) as large_name'),
                DB::raw('COALESCE(us.name_ar, us.name_en) as small_name'),
            ])->orderBy('pu.id')->get()
            ->groupBy('product_id');

        $out = [];

        foreach ($all as $pid => $rows) {
            $edges = [];
            $largeSet = [];
            $smallSet = [];
            foreach ($rows as $r) {
                if ($r->unit_large_id) $largeSet[$r->unit_large_id] = true;
                if ($r->unit_small_id) $smallSet[$r->unit_small_id] = true;
                if ($r->unit_large_id && $r->unit_small_id && $r->conversion_rate) {
                    $edges[] = [
                        'large' => (int)$r->unit_large_id,
                        'small' => (int)$r->unit_small_id,
                        'rate'  => (float)$r->conversion_rate,
                    ];
                }
            }

            $baseCandidates = array_diff(array_keys($smallSet), array_keys($largeSet));
            $baseUnitId = count($baseCandidates) ? (int)reset($baseCandidates) : null;

            $factor = [];
            if ($baseUnitId) $factor[$baseUnitId] = 1.0;
            $changed = true; $guard=50;
            while ($changed && $guard-- > 0) {
                $changed = false;
                foreach ($edges as $e) {
                    if (isset($factor[$e['small']]) && !isset($factor[$e['large']])) {
                        $factor[$e['large']] = $factor[$e['small']] * $e['rate'];
                        $changed = true;
                    }
                }
            }

            $unitIds = array_unique(array_merge(array_keys($largeSet), array_keys($smallSet)));
            $list = [];
            foreach ($unitIds as $uid) {
                $uid = (int)$uid;

                $name = null;
                foreach ($rows as $r) {
                    if ($r->unit_large_id == $uid && $r->large_name) { $name = $r->large_name; break; }
                    if ($r->unit_small_id == $uid && $r->small_name) { $name = $r->small_name; }
                }

                $rowSmall = $rows->firstWhere('unit_small_id', $uid);
                $rowLarge = $rows->firstWhere('unit_large_id', $uid);
                $puid     = (int)optional($rowSmall)->id ?: (int)optional($rowLarge)->id;

                $f = $factor[$uid] ?? 1.0;

                $sale = optional($rowSmall)->sale_price ?? optional($rowLarge)->sale_price;
                $cost = optional($rowSmall)->cost_price ?? optional($rowLarge)->cost_price;

                $list[] = [
                    'product_unit_id' => $puid,
                    'unit_id'    => $uid,
                    'unit_name'  => $name ?: ('#'.$uid),
                    'factor'     => (float)$f,
                    'sale_price' => $sale,
                    'cost_price' => $cost,
                    'unit_type'  => ($baseUnitId && $uid === $baseUnitId) ? 'small' : 'large',
                ];
            }

            usort($list, fn($a,$b)=> $a['factor'] <=> $b['factor']);
            $out[$pid] = $list;
        }

        return response()->json($out);
    }

    // باتشات الصنف بالمخزن + الرصيد + تاريخ الانتهاء
    public function ajaxBatches(Request $request)
    {
        $storeId   = (int)$request->input('store_id', 0);
        $productId = (int)$request->input('product_id', 0);
        if (!$storeId || !$productId) return response()->json([]);

        $rows = productstorebalance::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->select([
                DB::raw("COALESCE(batch_no,'') as batch_no"),
                DB::raw("DATE_FORMAT(expiry_date, '%Y-%m-%d') as expiry"),
                'quantity_in_base_unit',
            ])
            ->orderByRaw("CASE WHEN COALESCE(batch_no,'')='' THEN 1 ELSE 0 END, batch_no")
            ->get();

        $out = [];
        foreach ($rows as $r) {
            $label = ($r->batch_no === '') ? 'بدون باتش' : $r->batch_no;
            $out[] = [
                'batch_no' => $r->batch_no,
                'label'    => $label,
                'expiry'   => $r->expiry,
                'base'     => (float)$r->quantity_in_base_unit,
            ];
        }
        return response()->json($out);
    }

    // رصيد حسب الباتش (بالأساس) + تاريخ الانتهاء
    public function ajaxCurrentBalance(Request $request)
    {
        $storeId   = (int)$request->input('store_id', 0);
        $productId = (int)$request->input('product_id', 0);
        $batch     = trim($request->input('batch_no', ''));

        if (!$storeId || !$productId) {
            return response()->json(['base' => 0, 'expiry' => null]);
        }

        $row = productstorebalance::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->when(true, function($q) use ($batch){
                if ($batch === '') {
                    $q->where(function($x){
                        $x->whereNull('batch_no')->orWhere('batch_no','');
                    });
                } else {
                    $q->where('batch_no', $batch);
                }
            })
            ->select([
                'quantity_in_base_unit',
                DB::raw("DATE_FORMAT(expiry_date, '%Y-%m-%d') as expiry"),
            ])
            ->first();

        return response()->json([
            'base'   => (float)($row->quantity_in_base_unit ?? 0),
            'expiry' => $row->expiry ?? null,
        ]);
    }

    public function ajaxNextCode()
    {
        return response()->json(['code' => $this->generateCode()]);
    }

    protected function generateCode(): string
    {
        $prefix = 'AB-'.now()->format('Ymd').'-';
        $last = AdjustBalance::where('code', 'like', $prefix.'%')->orderByDesc('id')->value('code');

        $n = 1;
        if ($last && preg_match('/^'.preg_quote($prefix,'/').'(\d{4})$/', $last, $m)) {
            $n = (int)$m[1] + 1;
        }
        return $prefix.str_pad((string)$n, 4, '0', STR_PAD_LEFT);
    }

    /** خريطة عامل التحويل التراكمي للوحدات → أصغر وحدة للصنف */
    private function factorMapToSmallest(int $productId): array
    {
        $rows = DB::table('product_units as pu')
            ->where('pu.product_id', $productId)
            ->whereNull('pu.deleted_at')
            ->get(['unit_large_id','unit_small_id','conversion_rate']);

        if ($rows->isEmpty()) return [];

        $large = $rows->pluck('unit_large_id')->filter()->unique()->values()->all();
        $small = $rows->pluck('unit_small_id')->filter()->unique()->values()->all();
        $baseSmall = null;
        foreach ($small as $sid) { if (!in_array($sid,$large,true)) { $baseSmall = $sid; break; } }
        if (!$baseSmall) $baseSmall = $rows->first()->unit_small_id ?: $rows->first()->unit_large_id;

        $factor = [];
        if ($baseSmall) $factor[$baseSmall] = 1.0;

        $changed = true; $limit=50;
        while ($changed && $limit-- > 0) {
            $changed = false;
            foreach ($rows as $r) {
                if ($r->unit_small_id && isset($factor[$r->unit_small_id]) && $r->unit_large_id && !isset($factor[$r->unit_large_id])) {
                    $factor[$r->unit_large_id] = $factor[$r->unit_small_id] * (float)$r->conversion_rate;
                    $changed = true;
                }
            }
        }
        return $factor;
    }
}
