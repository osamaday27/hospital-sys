<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Arr;

// Models
use App\models\supply_chain\storemovement as Movement;
use App\Models\supply_chain\productstorebalance as Balance;
use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\storemaindata as Store;
use App\Services\SupplyChain\UnitConversionService;

class item_consumption_rate_reportcontroller extends Controller
{
    /** أنواع الحركات السالبة المحتسبة كاستهلاك */
    private function outboundTypes(): array
    {
        return ['withdrawal', 'transfer', 'adjustment_minus', 'writeoff'];
    }

    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        if ($action === 'print') {
            [$rows, $meta] = $this->buildRows($r);
            return view('supply_chain.reports.item_consumption_rate_report.print', compact('rows', 'meta'));
        }

        if ($action === 'export_excel') {
            [$rows, $meta] = $this->buildRows($r);
            return $this->exportExcel($rows, $meta);
        }

        if ($r->wantsJson() || $r->ajax()) {
            if (!$this->hasAnyFilter($r)) {
                return response()->json([
                    'draw' => intval($r->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            [$allRows, $meta] = $this->buildRows($r); // مصفوفة جاهزة للعرض

            // بحث عام
            $search = trim((string)($r->input('search.value') ?? $r->get('q')));
            if ($search !== '') {
                $allRows = array_values(array_filter($allRows, function ($row) use ($search) {
                    $hay = mb_strtolower(implode(' ', [
                        $row['store_name'] ?? '',
                        $row['product_code'] ?? '',
                        $row['product_name'] ?? '',
                        $row['batch_no'] ?? '',
                        $row['unit_name'] ?? '',
                        $row['suffices_until'] ?? '',
                    ]));
                    return str_contains($hay, mb_strtolower($search));
                }));
            }

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 6));
            $orderDir = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            $orderMap = [
                0  => null,                 // مسلسل
                1  => 'store_name',
                2  => 'product_code',
                3  => 'product_name',
                4  => 'batch_no',
                5  => 'sale_price',
                6  => 'cost_price',
                7  => 'unit_name',
                8  => 'balance',            // الرصيد الكلي (بالأصغر)
                9  => 'rate_period',        // معدل الفترة (يومي)
                10 => 'rate_monthly',
                11 => 'rate_yearly',
                12 => 'suffices_until',
            ];
            $key = $orderMap[$orderColumnIndex] ?? 'rate_period';

            if ($key) {
                usort($allRows, function ($a, $b) use ($key, $orderDir) {
                    $va = $a[$key] ?? null;
                    $vb = $b[$key] ?? null;
                    if (is_numeric($va) && is_numeric($vb)) {
                        $cmp = $va <=> $vb;
                    } else {
                        $cmp = strcmp((string)$va, (string)$vb);
                    }
                    return $orderDir === 'asc' ? $cmp : -$cmp;
                });
            }

            // تقسيم الصفحات
            $totalFiltered = count($allRows);
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;
            $pageRows = array_slice($allRows, $start, $length);

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $pageRows,
            ]);
        }

        // تحميل قوائم الفلاتر
        $stores = Store::select('id','manual_code','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s)=>[
                'id'=>$s->id,
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id),
                'manual_code' => $s->manual_code,
            ])->values();

        $products = Product::select('id','system_code','name_ar','name_en')
            ->orderBy('name_ar')->limit(4000)->get()
            ->map(fn($p)=>[
                'id'=>$p->id,
                'name'=> ($p->name_ar ?: $p->name_en ?: (string)$p->id) . ($p->system_code ? ' - '.$p->system_code : ''),
            ])->values();

        return view('supply_chain.reports.item_consumption_rate_report.index', [
            'stores'   => $stores,
            'products' => $products,
        ]);
    }

    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('store_id') ||
            $r->filled('products') ||
            $r->filled('date_from') ||
            $r->filled('date_to') ||
            $r->filled('q') ||
            ($r->input('search.value'))
        );
    }

    /**
     * يبني صفوف التقرير كاملة (تستخدم للـ AJAX/Print/Excel)
     * المخرجات: [ [row...], meta[] ]
     */
    private function buildRows(Request $r): array
    {
        $storeIds = $this->arrify($r->get('store_id'));
        $productIds = $this->arrify($r->get('products'));
        $aggregate = in_array((string)$r->get('aggregate', '0'), ['1','true','on'], true);
        $priceSource = in_array($r->get('price_source'), ['latest','avg','units'], true) ? $r->get('price_source') : 'latest';

        // فترة التاريخ (على movement_date)
        $from = $r->filled('date_from') ? Carbon::parse($r->get('date_from'))->startOfDay() : null;
        $to   = $r->filled('date_to')   ? Carbon::parse($r->get('date_to'))->endOfDay()   : null;

        // عدد الأيام للفترة
        $days = 0;
        if ($from && $to) {
            $days = max(1, $from->diffInDays($to) + 1);
        } elseif ($from xor $to) {
            $days = 1;
        }

        $svc = app(UnitConversionService::class);

        // ===== 1) حركات الاستهلاك داخل الفترة =====
        $mv = Movement::query()
            ->select(['product_id','store_id','units_id','batch_no','movement_date','quantity','sale_price','cost_price'])
            ->whereIn('movement_type', $this->outboundTypes())
            ->where('quantity', '<', 0); // نضمن الخروج

        if (!empty($storeIds)) $mv->whereIn('store_id', $storeIds);
        if (!empty($productIds)) $mv->whereIn('product_id', $productIds);
        if ($from) $mv->whereDate('movement_date', '>=', $from->toDateString());
        if ($to)   $mv->whereDate('movement_date', '<=', $to->toDateString());

        // حد أمان
        $mvRows = $mv->orderBy('movement_date')->limit(300000)->get();

        // نجمع استهلاك الفترة مع أسعار بحسب المصدر
        $cons = []; // key => ['cons_base'=>..., 'latest_row'=>..., 'sum_sale_base'=>..., 'sum_cost_base'=>..., 'cnt'=>...]
        foreach ($mvRows as $row) {
            $key = $this->makeKey($aggregate ? 0 : (int)$row->store_id, (int)$row->product_id, (string)($row->batch_no ?? ''));

            $qtyBase = 0.0;
            try {
                $qtyBase = $svc->toBase((int)$row->product_id, abs((float)$row->quantity), (int)$row->units_id);
            } catch (\Throwable $e) {
                // تجاهل سطر لا يمكن تحويله
                continue;
            }

            if (!isset($cons[$key])) {
                $cons[$key] = [
                    'cons_base' => 0.0,
                    'latest_row' => null,
                    'sum_sale_base' => 0.0,
                    'sum_cost_base' => 0.0,
                    'cnt' => 0,
                ];
            }
            $cons[$key]['cons_base'] += $qtyBase;

            // أسعار لكل 1 أصغر وحدة من سطر الحركة
            $unitFactor = 1.0;
            try {
                $unitFactor = max(1e-9, $svc->toBase((int)$row->product_id, 1.0, (int)$row->units_id));
            } catch (\Throwable $e) {}

            $salePerBase = is_null($row->sale_price) ? null : ((float)$row->sale_price / $unitFactor);
            $costPerBase = is_null($row->cost_price) ? null : ((float)$row->cost_price / $unitFactor);

            // latest داخل الفترة (مقارنة آمنة بـ Carbon)
$hasLatest = !empty($cons[$key]['latest_row']);

$currTs = $row->movement_date
    ? Carbon::parse($row->movement_date)->getTimestamp()
    : 0;

// استخدم nullsafe لاستخراج التاريخ بأمان
$latestMovementDate = $cons[$key]['latest_row']?->movement_date ?? null;

$latestTs = $latestMovementDate
    ? Carbon::parse($latestMovementDate)->getTimestamp()
    : 0;

if (!$hasLatest || ($currTs > $latestTs)) {
    $cons[$key]['latest_row'] = $row;
    $cons[$key]['latest_row']->_sale_per_base = $salePerBase;
    $cons[$key]['latest_row']->_cost_per_base = $costPerBase;
}

            // متوسط
            if (!is_null($salePerBase)) $cons[$key]['sum_sale_base'] += $salePerBase;
            if (!is_null($costPerBase)) $cons[$key]['sum_cost_base'] += $costPerBase;
            $cons[$key]['cnt'] += 1;
        }

        // ===== 2) أرصدة حالية من جدول الأرصدة =====
        $balQ = Balance::query()
            ->select(['product_id','store_id','batch_no','quantity_in_base_unit','units_id','quantity']);

        if (!empty($storeIds)) $balQ->whereIn('store_id', $storeIds);
        if (!empty($productIds)) $balQ->whereIn('product_id', $productIds);

        // نجمع بالتصنيف المطلوب
        $balanceMap = []; // key => balance_base
        $balRows = $balQ->limit(400000)->get();
        foreach ($balRows as $br) {
            $k = $this->makeKey($aggregate ? 0 : (int)$br->store_id, (int)$br->product_id, (string)($br->batch_no ?? ''));
            $balanceMap[$k] = ($balanceMap[$k] ?? 0.0) + (float)($br->quantity_in_base_unit ?? 0.0);
        }

        // ===== 3) مفاتيح نهائية (من الاستهلاك + الأرصدة) =====
        $keys = array_unique(array_merge(array_keys($cons), array_keys($balanceMap)));

        // تحميل بيانات المنتجات + أسماء المخازن + أصغر وحدة + أسعار من جدول الوحدات لو لزم
        $keyParts = array_map(fn($k)=>$this->splitKey($k), $keys);
        $prodIdsAll = array_values(array_unique(array_map(fn($x)=>$x['product_id'], $keyParts)));
        $storeIdsAll = array_values(array_unique(array_filter(array_map(fn($x)=>$x['store_id'], $keyParts))));

        $productsInfo = Product::whereIn('id', $prodIdsAll)->get(['id','system_code','name_ar','name_en'])
            ->keyBy('id');

        $storesInfo = Store::whereIn('id', $storeIdsAll)->get(['id','manual_code','name_ar','name_en'])
            ->keyBy('id');

        // أسعار من جدول الوحدات (للخيار الثالث)
        $unitBaseNameCache = []; // product_id => name_ar
        $unitsPriceCache = [];   // product_id => ['cost'=>?, 'sale'=>?] لكل أصغر وحدة
        foreach ($prodIdsAll as $pid) {
            try {
                $baseId = $svc->smallestUnitId((int)$pid);
                if ($baseId) {
                    $unitBaseNameCache[$pid] = optional(\App\Models\supply_chain\unit::find($baseId))->name_ar ?? '—';
                } else {
                    $unitBaseNameCache[$pid] = '—';
                }
                if ($priceSource === 'units') {
                    $units = $svc->unitsForProduct((int)$pid, 'name_ar');
                    $baseLine = $units->firstWhere('is_base', true);
                    if ($baseLine) {
                        $unitsPriceCache[$pid] = [
                            'sale' => $baseLine['sale_per_unit'] ?? null,
                            'cost' => $baseLine['cost_per_unit'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                $unitBaseNameCache[$pid] = '—';
            }
        }

        // ===== 4) بناء الصفوف =====
        $rows = [];
        $today = Carbon::today();

        foreach ($keys as $k) {
            $p = $this->splitKey($k);
            $pid = $p['product_id'];
            $sid = $p['store_id'];
            $batch = $p['batch_no'];

            $prod = $productsInfo[$pid] ?? null;
            $prodCode = $prod->system_code ?? '—';
            $prodName = trim(($prod->name_ar ?? '') . ' / ' . ($prod->name_en ?? ''));

            $storeName = $aggregate
                ? 'كل المخازن المختارة'
                : (($storesInfo[$sid]->name_ar ?? '') );

            $balanceBase = (float)($balanceMap[$k] ?? 0.0);
            $consInfo = $cons[$k] ?? ['cons_base'=>0.0,'latest_row'=>null,'sum_sale_base'=>0.0,'sum_cost_base'=>0.0,'cnt'=>0];
            $consBase = (float)$consInfo['cons_base'];

            // معدل الفترة (يومي)
            $rateDaily = $days > 0 ? ($consBase / $days) : 0.0;
            $rateMonthly = $rateDaily * 30.0;
            $rateYearly  = $rateDaily * 365.0;

            // أسعار بحسب المصدر
            $salePerBase = null; $costPerBase = null;
            $chosenPriceSource = $priceSource; // لا نعدل الأصل

            if ($chosenPriceSource === 'units') {
                $salePerBase = $unitsPriceCache[$pid]['sale'] ?? null;
                $costPerBase = $unitsPriceCache[$pid]['cost'] ?? null;
            } elseif ($chosenPriceSource === 'avg') {
                if (($consInfo['cnt'] ?? 0) > 0) {
                    $saleSum = $consInfo['sum_sale_base'];
                    $costSum = $consInfo['sum_cost_base'];
                    $cnt = max(1, (int)$consInfo['cnt']);
                    $salePerBase = $saleSum > 0 ? ($saleSum / $cnt) : null;
                    $costPerBase = $costSum > 0 ? ($costSum / $cnt) : null;
                }
                // في حال لم يوجد حركات لتسعيرها خلال الفترة، نهبط لـ latest
                if (is_null($salePerBase) && is_null($costPerBase)) {
                    $chosenPriceSource = 'latest';
                }
            }
            if ($chosenPriceSource === 'latest') {
                if ($consInfo['latest_row'] ?? null) {
                    $salePerBase = $consInfo['latest_row']->_sale_per_base ?? null;
                    $costPerBase = $consInfo['latest_row']->_cost_per_base ?? null;
                } else {
                    // Fallback: آخر حركة (خارج الفترة) ضمن نفس النطاق
                    $fallback = Movement::query()
                        ->select(['product_id','store_id','units_id','batch_no','movement_date','sale_price','cost_price'])
                        ->when(!$aggregate, fn($q)=>$q->where('store_id', $sid))
                        ->when($aggregate && !empty($storeIds), fn($q)=>$q->whereIn('store_id', $storeIds))
                        ->where('product_id', $pid)
                        ->where('batch_no', $batch)
                        ->orderBy('movement_date','desc')
                        ->first();

                    if ($fallback) {
                        try {
                            $unitFactor = max(1e-9, $svc->toBase((int)$fallback->product_id, 1.0, (int)$fallback->units_id));
                            $salePerBase = is_null($fallback->sale_price) ? null : ((float)$fallback->sale_price / $unitFactor);
                            $costPerBase = is_null($fallback->cost_price) ? null : ((float)$fallback->cost_price / $unitFactor);
                        } catch (\Throwable $e) {}
                    }
                }
            }

            // تاريخ يكفي حتى
            $suffDate = '—';
            if ($rateDaily > 0 && $balanceBase > 0) {
                $daysLeft = (int)ceil($balanceBase / $rateDaily);
                $suffDate = $today->copy()->addDays($daysLeft)->format('Y-m-d');
            }

            $rows[] = [
                'store_name'   => $storeName ?: '—',
                'product_code' => $prodCode,
                'product_name' => $prodName ?: '—',
                'batch_no'     => $batch ?: '—',
                'sale_price'   => is_null($salePerBase) ? '—' : number_format((float)$salePerBase, 2),
                'cost_price'   => is_null($costPerBase) ? '—' : number_format((float)$costPerBase, 2),
                'unit_name'    => $unitBaseNameCache[$pid] ?? '—',
                'balance'      => number_format((float)$balanceBase, 2),
                'rate_period'  => number_format((float)$rateDaily, 2),
                'rate_monthly' => number_format((float)$rateMonthly, 2),
                'rate_yearly'  => number_format((float)$rateYearly, 2),
                'suffices_until'=> $suffDate,
            ];
        }

        // ميتا للطباعة/الإكسل
        $meta = $this->buildMeta($r, $rows);

        return [$rows, $meta];
    }

    private function arrify($v): array
    {
        if (is_null($v) || $v === '') return [];
        return is_array($v) ? array_values(array_filter($v, fn($x)=>$x!=='')) : [$v];
    }

    private function makeKey(int $store_id, int $product_id, string $batch_no): string
    {
        return $store_id.'|'.$product_id.'|'.$batch_no;
    }
    private function splitKey(string $key): array
    {
        [$sid, $pid, $batch] = explode('|', $key, 3);
        return ['store_id'=>(int)$sid, 'product_id'=>(int)$pid, 'batch_no'=>$batch];
    }

    /** معلومات الهيدر للطباعة/الإكسل */
    private function buildMeta(Request $r, array $rows)
    {
        $storeNames = [];
        if ($stores = $r->get('store_id')) {
            $ids = is_array($stores)? $stores : [$stores];
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }
        $productNames = [];
        if ($prods = $r->get('products')) {
            $ids = is_array($prods)? $prods : [$prods];
            $productNames = Product::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }

        return [
            'title'        => trans('supply_chain_trans.item_consumption_rate_report_title', [], app()->getLocale()) ?? 'تقرير معدل استهلاك الأصناف',
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'date_from'    => $r->get('date_from'),
            'date_to'      => $r->get('date_to'),
            'store_names'  => $storeNames,
            'product_names'=> $productNames,
            'aggregate'    => in_array((string)$r->get('aggregate','0'), ['1','true','on'], true),
            'price_source' => $r->get('price_source','latest'),
            'count'        => count($rows),
        ];
    }

    /** تصدير Excel */
    private function exportExcel(array $rows, array $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('supply_chain_trans.table_store_name', [], app()->getLocale()) ?? 'المخزن',
            trans('supply_chain_trans.table_product_code', [], app()->getLocale()) ?? 'كود الصنف',
            trans('supply_chain_trans.table_product_name', [], app()->getLocale()) ?? 'اسم الصنف',
            trans('supply_chain_trans.table_batch_no', [], app()->getLocale()) ?? 'الباتش',
            trans('supply_chain_trans.table_sale_price', [], app()->getLocale()) ?? 'سعر البيع',
            trans('supply_chain_trans.table_cost_price', [], app()->getLocale()) ?? 'سعر التكلفة',
            trans('supply_chain_trans.table_unit_small', [], app()->getLocale()) ?? 'الوحدة (أصغر)',
            trans('supply_chain_trans.table_balance_total', [], app()->getLocale()) ?? 'الرصيد الكلي',
            trans('supply_chain_trans.table_rate_period', [], app()->getLocale()) ?? 'معدل الفترة (يومي)',
            trans('supply_chain_trans.table_rate_monthly', [], app()->getLocale()) ?? 'معدل شهري',
            trans('supply_chain_trans.table_rate_yearly', [], app()->getLocale()) ?? 'معدل سنوي',
            trans('supply_chain_trans.table_suffices_until', [], app()->getLocale()) ?? 'يكفي حتى',
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $row) {
            $sheet->fromArray([[
                $row['store_name'],
                $row['product_code'],
                $row['product_name'],
                $row['batch_no'],
                $row['sale_price'],
                $row['cost_price'],
                $row['unit_name'],
                $row['balance'],
                $row['rate_period'],
                $row['rate_monthly'],
                $row['rate_yearly'],
                $row['suffices_until'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','L') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Item_Consumption_Rate_' . now()->format('Ymd_His') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
