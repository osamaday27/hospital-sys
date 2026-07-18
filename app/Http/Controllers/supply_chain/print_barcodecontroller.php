<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\supply_chain\productcategory;
use App\Models\supply_chain\storemaindata;
use Milon\Barcode\DNS1D;

class print_barcodecontroller extends Controller
{
    /**
     * صفحة الطباعة + DataTables + عمليات المساعدة (ids/details/preview).
     */
    public function index(Request $request)
    {
        // عمليات مساعدة عبر نفس الراوت:
        $op = $request->query('op');
        if ($op === 'ids')     return $this->opIds($request);
        if ($op === 'details') return $this->opDetails($request);
        if ($op === 'preview') return $this->opPreview($request); // يُرجع صور الباركود

        // DataTables
        if ($request->ajax() || $request->has('draw')) {
            return $this->ajaxList($request);
        }

        // قوائم المجموعات والمخازن (مع fallback للأسامي)
        $categories = productcategory::query()
            ->select('id', 'name_ar', 'name_en')
            ->orderBy(DB::raw('COALESCE(NULLIF(name_ar,""), NULLIF(name_en,""), id)'))
            ->get()
            ->map(function ($r) {
                return [
                    'id'   => $r->id,
                    'name' => $r->name_ar ?: ($r->name_en ?: ('#' . $r->id)),
                ];
            });

        $stores = storemaindata::query()
            ->select('id', 'name_ar', 'name_en')
            ->orderBy(DB::raw('COALESCE(NULLIF(name_ar,""), NULLIF(name_en,""), id)'))
            ->get()
            ->map(function ($s) {
                return [
                    'id'   => $s->id,
                    'name' => $s->name_ar ?: ($s->name_en ?: ('#' . $s->id)),
                ];
            });

        return view('supply_chain.program.print_barcode.index', compact('categories', 'stores'));
    }

    /** الاستعلام الأساسي مع الفلاتر */
    protected function baseQuery(Request $request, bool $withSearch = true)
    {
        $mode       = $request->input('mode', 'all');
        $categoryId = (int) $request->input('category_id', 0);
        $storeId    = (int) $request->input('store_id', 0);
        $codeType   = $request->input('code_type', 'system_code');

        $displayExpr = match ($codeType) {
            'system_code'  => 'p.system_code',
            'barcode'      => 'p.barcode',
            'product_code' => 'p.product_code',
            default        => 'p.system_code',
        };

        $q = DB::table('products_data as p')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.product_category_id')
            ->whereNull('p.deleted_at');

        if ($mode === 'category' && $categoryId > 0) {
            $q->where('p.product_category_id', $categoryId);
        } elseif ($mode === 'store' && $storeId > 0) {
            $q->join('store_products as sp', function ($j) use ($storeId) {
                $j->on('sp.product_id', '=', 'p.id')
                  ->whereNull('sp.deleted_at')
                  ->where('sp.store_id', '=', $storeId);
            });
        }

        if ($withSearch) {
            $search = trim($request->input('search.value', ''));
            if ($search === '') $search = trim($request->input('q', ''));
            if ($search !== '') {
                $q->where(function ($w) use ($search) {
                    $w->where('p.name_ar', 'like', "%{$search}%")
                        ->orWhere('p.name_en', 'like', "%{$search}%")
                        ->orWhere('p.product_code', 'like', "%{$search}%")
                        ->orWhere('p.barcode', 'like', "%{$search}%")
                        ->orWhere('p.system_code', 'like', "%{$search}%");
                });
            }
        }

        $q->select([
            DB::raw('p.id as id'),
            DB::raw('COALESCE(p.name_ar, p.name_en) as product_name'),
            DB::raw("{$displayExpr} as display_code"),
        ]);

        return $q;
    }

    /** DataTables */
    protected function ajaxList(Request $request)
    {
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 500) : 10;

        $recordsTotal    = (clone $this->baseQuery($request, false))->count();
        $recordsFiltered = (clone $this->baseQuery($request, true))->count();

        $orderIdx = (int) $request->input('order.0.column', 3);
        $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderable = [ 2 => 'display_code', 3 => 'product_name' ];
        $orderCol  = $orderable[$orderIdx] ?? 'product_name';

        $rowsQ = $this->baseQuery($request, true);
        $orderCol === 'display_code'
            ? $rowsQ->orderBy('display_code', $orderDir)->orderBy('id', 'asc')
            : $rowsQ->orderBy('product_name', $orderDir)->orderBy('id', 'asc');

        $rows = $rowsQ->offset($start)->limit($length)->get();

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $rows,
        ]);
    }

    /** IDs لكل النتائج المطابقة للفلتر/البحث الحالي */
    protected function opIds(Request $request)
    {
        $ids = $this->baseQuery($request, true)->pluck('id')->toArray();
        return response()->json(['ids' => $ids]);
    }

    /** تفاصيل الأصناف المختارة (بدون صور) */
    protected function opDetails(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['rows' => []]);
        }

        $codeType = $request->input('code_type', 'system_code');
        $displayExpr = match ($codeType) {
            'system_code'  => 'p.system_code',
            'barcode'      => 'p.barcode',
            'product_code' => 'p.product_code',
            default        => 'p.system_code',
        };

        $rows = DB::table('products_data as p')
            ->whereNull('p.deleted_at')
            ->whereIn('p.id', $ids)
            ->select([
                DB::raw('p.id as id'),
                DB::raw('COALESCE(p.name_ar, p.name_en) as product_name'),
                DB::raw("{$displayExpr} as display_code"),
            ])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['rows' => $rows]);
    }

    /**
     * معاينة الطباعة: ترجع SVGs عبر milon/barcode كـ data URL
     * params: ids[], code_type, size (مثل 38x25)
     */
    protected function opPreview(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['ok' => false, 'message' => 'لم يتم اختيار أي عناصر'], 422);
        }

        $codeType = $request->input('code_type', 'system_code');
        $size     = $request->input('size', '38x25');

        // ارتفاع الباركود (px) لكل مقاس ملصق
        $heightMap = [
            '38x25' => 62,
            '50x30' => 72,
            '58x40' => 96,
            '70x35' => 84,
            '70x50' => 120,
        ];
        $barHeight  = $heightMap[$size] ?? 72;
        $widthScale = 1.2; // سمك الأعمدة النسبي

        $displayExpr = match ($codeType) {
            'system_code'  => 'p.system_code',
            'barcode'      => 'p.barcode',
            'product_code' => 'p.product_code',
            default        => 'p.system_code',
        };

        $rows = DB::table('products_data as p')
            ->whereNull('p.deleted_at')
            ->whereIn('p.id', $ids)
            ->select([
                DB::raw('p.id as id'),
                DB::raw('COALESCE(p.name_ar, p.name_en) as product_name'),
                DB::raw("{$displayExpr} as display_code"),
            ])
            ->orderBy('id', 'asc')
            ->get();

        // تحقق من توفر الكود
        $missing = [];
        foreach ($rows as $r) {
            if (!($r->display_code && trim($r->display_code) !== '')) $missing[] = $r->id;
        }
        if (!empty($missing)) {
            return response()->json([
                'ok' => false,
                'message' => 'لا يتوفر الكود المختار لبعض الأصناف المحددة.',
                'missing_ids' => $missing
            ], 422);
        }

        // توليد SVG كـ data URL
        $labels = [];
        $dns = new DNS1D();
        foreach ($rows as $r) {
            $code = (string) $r->display_code;
            try {
                $svg = $dns->getBarcodeSVG($code, 'C128', $widthScale, $barHeight, 'black', false);
                $dataUrl = 'data:image/svg+xml;base64,' . base64_encode($svg);
            } catch (\Throwable $e) {
                $dataUrl = null;
            }

            $labels[] = [
                'id'    => (int) $r->id,
                'name'  => (string) ($r->product_name ?? ''),
                'code'  => $code,
                'svg'   => $dataUrl,
            ];
        }

        return response()->json([
            'ok'     => true,
            'labels' => $labels,
        ]);
    }
}
