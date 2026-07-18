<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

// Models
use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\productcategory as Category;
use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\unit as Unit;
use App\Services\SupplyChain\UnitConversionService;

class item_additions_reportcontroller extends Controller
{
    /** أنواع الحركات المحتسبة كـ "إضافة" للمخزون */
    private function inboundTypes(): array
    {
        // عدّل القائمة حسب نظامك إن لزم
        return [
            'addition', 'receipt', 'purchase', 'return_in',
            'transfer_in', 'adjustment_plus', 'opening_balance', 'production_in'
        ];
    }

    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // ===== Print =====
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.item_additions_report.print', compact('rows', 'meta'));
        }

        // ===== Export Excel =====
        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // ===== AJAX (DataTables) =====
        if ($r->wantsJson() || $r->ajax()) {
            if (!$this->hasAnyFilter($r)) {
                return response()->json([
                    'draw' => intval($r->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $q = $this->baseQuery($r);

            // بحث عام
            $search = trim((string)($r->input('search.value') ?? $r->get('q')));
            if ($search !== '') {
                $q->where(function ($w) use ($search) {
                    $w->where('s.manual_code', 'like', "%{$search}%")
                      ->orWhere('s.name_ar', 'like', "%{$search}%")
                      ->orWhere('s.name_en', 'like', "%{$search}%")
                      ->orWhere('p.system_code', 'like', "%{$search}%")
                      ->orWhere('p.name_ar', 'like', "%{$search}%")
                      ->orWhere('p.name_en', 'like', "%{$search}%")
                      ->orWhere('c.name_ar', 'like', "%{$search}%")
                      ->orWhere('c.name_en', 'like', "%{$search}%")
                      ->orWhere('m.movement_number', 'like', "%{$search}%")
                      ->orWhere('m.reference_no', 'like', "%{$search}%")
                      ->orWhere('u.name', 'like', "%{$search}%")
                      ->orWhere('m.batch_no', 'like', "%{$search}%");
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 11)); // تاريخ الإضافة افتراضياً
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0  => 'm.id',                // مسلسل (عرض فقط)
                1  => 's.manual_code',       // كود المخزن
                2  => 's.name_ar',           // اسم المخزن
                3  => 'p.system_code',       // كود الصنف
                4  => 'p.name_ar',           // اسم الصنف
                5  => 'c.name_ar',           // المجموعة
                6  => 'm.batch_no',          // الباتش
                7  => null,                  // سعر البيع (محسوب/تحويل)
                8  => null,                  // سعر التكلفة (محسوب/تحويل)
                9  => null,                  // الوحدة (أصغر) - عرض
                10 => null,                  // الكمية (بالأصغر) - محسوبة
                11 => 'm.movement_date',     // تاريخ الإضافة
                12 => 'm.movement_number',   // رقم الحركة
                13 => 'u.name',              // المستخدم
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'm.movement_date';
            if ($orderBy) $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $pageRows = $q->skip($start)->take($length)->get();

            // تحويل النتائج لصفوف العرض (تحويل كميات وأسعار لأصغر وحدة)
            [$data] = $this->mapRows($pageRows);

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // ===== HTML (تحميل القوائم) =====
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

        $categories = Category::select('id','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($c)=>['id'=>$c->id,'name'=> ($c->name_ar ?: $c->name_en ?: (string)$c->id)])->values();

        return view('supply_chain.reports.item_additions_report.index', [
            'stores'     => $stores,
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('store_id') ||
            $r->filled('products') ||
            $r->filled('categories') ||
            $r->filled('date_from') ||
            $r->filled('date_to') ||
            $r->filled('q') ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي (Query Builder بدون سكوبات Eloquent) */
    private function baseQuery(Request $r)
    {
        $q = DB::table('store_movements as m')
            ->leftJoin('products_data as p', 'p.id', '=', 'm.product_id')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.product_category_id')
            ->leftJoin('store_main_date as s', 's.id', '=', 'm.store_id')
            ->leftJoin('units as un', 'un.id', '=', 'm.units_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.user_id');

        // SoftDelete لو العمود موجود
        if (Schema::hasColumn('store_movements', 'deleted_at')) {
            $q->whereNull('m.deleted_at');
        }

        // فقط حركات "إضافة" وبكميات موجبة
        $q->whereIn('m.movement_type', $this->inboundTypes())
          ->where('m.quantity', '>', 0);

        $q->select([
            'm.id','m.product_id','m.store_id','m.units_id','m.quantity','m.batch_no',
            'm.movement_date','m.movement_type','m.movement_number','m.reference_no',
            'm.sale_price','m.cost_price','m.created_at','m.user_id',

            'p.system_code as p_code','p.name_ar as p_name_ar','p.name_en as p_name_en',
            'c.name_ar as c_name_ar','c.name_en as c_name_en',

            's.manual_code as s_manual_code','s.name_ar as s_name_ar','s.name_en as s_name_en',

            'un.name_ar as un_name_ar','un.name_en as un_name_en',
            'u.name as u_name',
        ]);

        // فلاتر
        if ($ids = $this->arrify($r->get('store_id'))) {
            $q->whereIn('m.store_id', $ids);
        }
        if ($ids = $this->arrify($r->get('products'))) {
            $q->whereIn('m.product_id', $ids);
        }
        if ($ids = $this->arrify($r->get('categories'))) {
            $q->whereIn('p.product_category_id', $ids);
        }
        if ($r->filled('date_from')) {
            $q->whereDate('m.movement_date', '>=', $r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('m.movement_date', '<=', $r->get('date_to'));
        }

        return $q;
    }

    /** تحويل النتائج لصفوف العرض (التحويل لأصغر وحدة + تسعير بالقاعدة) */
    private function mapRows($rows): array
    {
        $svc = app(UnitConversionService::class);

        $baseUnitNameCache = []; // [product_id => unit_name]
        $factorCache       = []; // ["pid:fromUnit" => float]

        $data = [];
        foreach ($rows as $row) {
            $pid = (int)($row->product_id ?? 0);
            $fromUnit = (int)($row->units_id ?? 0);

            // اسم أصغر وحدة
            if ($pid && !array_key_exists($pid, $baseUnitNameCache)) {
                try {
                    $baseId = $svc->smallestUnitId($pid);
                    $baseUnitNameCache[$pid] = $baseId ? (optional(Unit::find($baseId))->name_ar ?? '—') : '—';
                } catch (\Throwable $e) {
                    $baseUnitNameCache[$pid] = '—';
                }
            }

            // عامل تحويل 1 من وحدة السطر لأصغر وحدة
            $factorKey = $pid.':'.$fromUnit;
            if (!isset($factorCache[$factorKey])) {
                try {
                    $factorCache[$factorKey] = $pid && $fromUnit ? max(1e-9, $svc->toBase($pid, 1.0, $fromUnit)) : 1.0;
                } catch (\Throwable $e) {
                    $factorCache[$factorKey] = 1.0;
                }
            }

            // كمية/سعر بالقاعدة (أصغر وحدة)
            $qtyBase = (float)($row->quantity ?? 0) * (float)$factorCache[$factorKey];
            $salePerBase = is_null($row->sale_price) ? null : ((float)$row->sale_price / (float)$factorCache[$factorKey]);
            $costPerBase = is_null($row->cost_price) ? null : ((float)$row->cost_price / (float)$factorCache[$factorKey]);

            $prodName  = trim(($row->p_name_ar ?? '') . ' / ' . ($row->p_name_en ?? ''));
            $catName   = trim(($row->c_name_ar ?? '') . ' / ' . ($row->c_name_en ?? ''));
            $storeName = trim(($row->s_name_ar ?? '') . ' / ' . ($row->s_name_en ?? ''));

            $data[] = [
                // 0 مسلسل يُحسب في الواجهة
                'store_manual_code' => $row->s_manual_code ?? '—',
                'store_name'        => $storeName ?: '—',
                'product_code'      => $row->p_code ?? '—',
                'product_name'      => $prodName ?: '—',
                'category_name'     => ($catName !== ' / ') ? $catName : '—',
                'batch_no'          => $row->batch_no ?: '—',
                'sale_price'        => is_null($salePerBase) ? '—' : number_format((float)$salePerBase, 2),
                'cost_price'        => is_null($costPerBase) ? '—' : number_format((float)$costPerBase, 2),
                'unit_name'         => $baseUnitNameCache[$pid] ?? ($row->un_name_ar ?? '—'),
                'quantity'          => number_format((float)$qtyBase, 2),
                'movement_date'     => $row->movement_date ? Carbon::parse($row->movement_date)->format('Y-m-d') : '—',
                'movement_number'   => $row->movement_number ?: ($row->reference_no ?: '—'),
                'user_name'         => $row->u_name ?: '—',
            ];
        }

        return [$data, count($data)];
    }

    /** للطباعة/الإكسل: اجلب كل النتائج */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [[], $this->buildMeta($r, [])];
        }

        $q = $this->baseQuery($r)->orderBy('m.movement_date','desc');
        $rows = $q->limit(200000)->get();
        [$mapped] = $this->mapRows($rows);

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    private function buildMeta(Request $r, array $rows): array
    {
        $storeNames = [];
        if ($ids = $this->arrify($r->get('store_id'))) {
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }
        $productNames = [];
        if ($ids = $this->arrify($r->get('products'))) {
            $productNames = Product::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }
        $categoryNames = [];
        if ($ids = $this->arrify($r->get('categories'))) {
            $categoryNames = Category::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }

        return [
            'title'          => trans('supply_chain_trans.item_additions_report_title'),
            'generated_at'   => Carbon::now()->format('Y-m-d H:i'),
            'date_from'      => $r->get('date_from'),
            'date_to'        => $r->get('date_to'),
            'store_names'    => $storeNames,
            'product_names'  => $productNames,
            'category_names' => $categoryNames,
            'count'          => count($rows),
        ];
    }

    /** تصدير Excel */
    private function exportExcel(array $rows, array $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('supply_chain_trans.table_store_manual_code'),
            trans('supply_chain_trans.table_store_name'),
            trans('supply_chain_trans.table_product_code'),
            trans('supply_chain_trans.table_product_name'),
            trans('supply_chain_trans.table_category_name'),
            trans('supply_chain_trans.table_batch_no'),
            trans('supply_chain_trans.table_sale_price'),
            trans('supply_chain_trans.table_cost_price'),
            trans('supply_chain_trans.table_unit_small'),
            trans('supply_chain_trans.table_quantity'),
            trans('supply_chain_trans.table_movement_date'),
            trans('supply_chain_trans.table_movement_number'),
            trans('supply_chain_trans.table_user'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['store_manual_code'],
                $r['store_name'],
                $r['product_code'],
                $r['product_name'],
                $r['category_name'],
                $r['batch_no'],
                $r['sale_price'],
                $r['cost_price'],
                $r['unit_name'],
                $r['quantity'],
                $r['movement_date'],
                $r['movement_number'],
                $r['user_name'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','M') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Item_Additions_Report_' . now()->format('Ymd_His') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function arrify($v): array
    {
        if (is_null($v) || $v === '') return [];
        return is_array($v) ? array_values(array_filter($v, fn($x)=>$x!=='')) : [$v];
    }
}
