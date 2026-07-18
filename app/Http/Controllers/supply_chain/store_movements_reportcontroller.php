<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

// موديلاتك (بنفس المسارات المعطاة)
use App\models\supply_chain\storemovement;
use App\models\supply_chain\productdata;
use App\Models\supply_chain\storemaindata as Store;
use App\models\User as UserModel;

class store_movements_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        // تبديل أنماط الاستجابة حسب action
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // طباعة: فيو منفصل مهيأ للطابعة
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.store_movements_report.print', compact('rows', 'meta'));
        }

        // تصدير Excel عبر PhpSpreadsheet (يشمل كل النتائج المصفّاة)
        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // طلب AJAX لبيانات DataTables
        if ($r->wantsJson() || $r->ajax()) {
            // منع التحميل الافتراضي: إن لم تُطبّق أي فلاتر، أعد بيانات فارغة
            if (!$this->hasAnyFilter($r)) {
                return response()->json([
                    'draw' => intval($r->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $q = $this->baseQuery($r);

            $totalFiltered = (clone $q)->count();

            // ترتيب آمن
            $orderColumnIndex = (int)($r->input('order.0.column', 4));
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'id',               // مسلسل (لن يُستخدم فعليًا)
                1 => 'store_id',         // المخزن
                2 => 'movement_number',  // كود الحركة
                3 => 'movement_type',
                5 => 'movement_date',
                7 => 'quantity',
                8 => 'product_store_balance',
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'movement_date';
            $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $rows = $q->skip($start)->take($length)->get();

            // تجهيز الصفوف
            $data = [];
            foreach ($rows as $row) {
                $movementCode = $row->movement_number ?: ($row->reference_no ?: (string)$row->id);
                $movementTypeLabel = trans('supply_chain_trans.movements_type.' . $row->movement_type, [], app()->getLocale());

                $storeName = trim(($row->store->name_ar ?? '') . ' / ' . ($row->store->name_en ?? ''));
                $productName = trim(($row->product->name_ar ?? '') . ' / ' . ($row->product->name_en ?? ''));
                $unitName = trim(($row->unit->name_ar ?? '') . ' / ' . ($row->unit->name_en ?? ''));
                $userName = $row->user->name ?? '—';

                $data[] = [
                    // 0 مسلسل يحسب في الواجهة
                    'store_name'            => $storeName,
                    'movement_code'         => $movementCode,
                    'movement_type_label'   => $movementTypeLabel,
                    'product_name'          => $productName,
                    'movement_date'         => ($row->movement_date instanceof \Carbon\Carbon
                                                ? $row->movement_date
                                                : Carbon::parse($row->movement_date))->format('Y-m-d'),
                    'user_name'             => $userName,
                    'quantity'              => number_format((float)$row->quantity, 2),
                    'product_store_balance' => number_format((float)($row->product_store_balance ?? 0), 2),
                    'unit_name'             => $unitName,
                ];
            }

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered, // نعرض عدد المصفّى لشفافية أفضل
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // تحميل القوائم (كلها متعددة الاختيار)
        $stores = Store::select('id','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s)=>['id'=>$s->id,'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id)])->values();

        $products = productdata::select('id','name_ar','name_en')
            ->orderBy('name_ar')->limit(2000)->get()
            ->map(fn($p)=>['id'=>$p->id,'name'=> ($p->name_ar ?: $p->name_en ?: (string)$p->id)])->values();

        $users = UserModel::select('id','name')->orderBy('name')->get()
            ->map(fn($u)=>['id'=>$u->id,'name'=> $u->name ?: (string)$u->id])->values();

        $movementTypes = [
            'direct_addition'  => trans('supply_chain_trans.movements_type.direct_addition'),
            'normal_addition'  => trans('supply_chain_trans.movements_type.normal_addition'),
            'withdrawal'       => trans('supply_chain_trans.movements_type.withdrawal'),
            'transfer'         => trans('supply_chain_trans.movements_type.transfer'),
            'return'           => trans('supply_chain_trans.movements_type.return'),
            'adjustment_plus'  => trans('supply_chain_trans.movements_type.adjustment_plus'),
            'adjustment_minus' => trans('supply_chain_trans.movements_type.adjustment_minus'),
            'writeoff'         => trans('supply_chain_trans.movements_type.writeoff'),
            'inventory'        => trans('supply_chain_trans.movements_type.inventory'),
            'opening_balance'  => trans('supply_chain_trans.movements_type.opening_balance'),
        ];

        return view('supply_chain.reports.store_movements_report.index', [
            'stores'        => $stores,
            'products'      => $products,
            'users'         => $users,
            'movementTypes' => $movementTypes,
        ]);
    }

    /** ===== Helpers ===== */

    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('store_id') ||
            $r->filled('movement_type') ||
            $r->filled('user_id') ||
            $r->filled('products') ||
            $r->filled('date_from') ||
            $r->filled('date_to') ||
            $r->filled('q') ||
            ($r->input('search.value'))
        );
    }

    private function baseQuery(Request $r)
    {
        $q = storemovement::query()
            ->with([
                'product:id,name_ar,name_en',
                'store:id,name_ar,name_en',
                'unit:id,name_ar,name_en',
                'user:id,name'
            ]);

        // المتعدد: المخازن
        $stores = $r->get('store_id');
        if (is_array($stores) && count($stores)) {
            $q->whereIn('store_id', $stores);
        } elseif (!empty($stores)) {
            $q->where('store_id', $stores);
        }

        // المتعدد: أنواع الحركه
        $types = $r->get('movement_type');
        if (is_array($types) && count($types)) {
            $q->whereIn('movement_type', $types);
        } elseif (!empty($types)) {
            $q->where('movement_type', $types);
        }

        // المتعدد: المستخدمين
        $users = $r->get('user_id');
        if (is_array($users) && count($users)) {
            $q->whereIn('user_id', $users);
        } elseif (!empty($users)) {
            $q->where('user_id', $users);
        }

        // المتعدد: الأصناف
        $products = $r->get('products');
        if (is_array($products) && count($products)) {
            $q->whereIn('product_id', $products);
        }

        // التاريخ
        if ($r->filled('date_from')) {
            $q->whereDate('movement_date', '>=', $r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('movement_date', '<=', $r->get('date_to'));
        }

        // البحث العام
        $search = $r->input('search.value') ?? $r->get('q');
        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('movement_number', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($wp) use ($search) {
                      $wp->where('name_ar', 'like', "%{$search}%")
                         ->orWhere('name_en', 'like', "%{$search}%");
                  })
                  ->orWhereHas('store', function ($ws) use ($search) {
                      $ws->where('name_ar', 'like', "%{$search}%")
                         ->orWhere('name_en', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($wu) use ($search) {
                      $wu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $q;
    }

    /** إحضار كل النتائج المصفّاة للطباعة/التصدير */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            // لا شيء بدون فلاتر
            return [collect(), $this->buildMeta($r, collect())];
        }

        $q = $this->baseQuery($r)->orderBy('movement_date', 'desc');

        // حد أمان اختياري: يمكن إزالة هذا الحد لو أردت كل شيء بلا قيد
        $rows = $q->limit(200000)->get();

        // تحويل مبسّط
        $mapped = $rows->map(function ($row) {
            $movementCode = $row->movement_number ?: ($row->reference_no ?: (string)$row->id);
            return [
                'store_name'            => trim(($row->store->name_ar ?? '') . ' / ' . ($row->store->name_en ?? '')),
                'movement_code'         => $movementCode,
                'movement_type_label'   => trans('supply_chain_trans.movements_type.' . $row->movement_type),
                'product_name'          => trim(($row->product->name_ar ?? '') . ' / ' . ($row->product->name_en ?? '')),
                'movement_date'         => ($row->movement_date instanceof \Carbon\Carbon
                                            ? $row->movement_date
                                            : Carbon::parse($row->movement_date))->format('Y-m-d'),
                'user_name'             => $row->user->name ?? '—',
                'quantity'              => number_format((float)$row->quantity, 2),
                'product_store_balance' => number_format((float)($row->product_store_balance ?? 0), 2),
                'unit_name'             => trim(($row->unit->name_ar ?? '') . ' / ' . ($row->unit->name_en ?? '')),
            ];
        });

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    /** معلومات الهيدر للطباعة/التصدير */
    private function buildMeta(Request $r, $rows)
    {
        // أسماء الفلاتر المختارة (اختياري)
        $storeNames = [];
        $typeNames  = [];
        $userNames  = [];
        $productNames = [];

        if ($stores = $r->get('store_id')) {
            $ids = is_array($stores)? $stores : [$stores];
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }
        if ($types = $r->get('movement_type')) {
            $keys = is_array($types)? $types : [$types];
            foreach ($keys as $k) $typeNames[] = trans('supply_chain_trans.movements_type.' . $k);
        }
        if ($users = $r->get('user_id')) {
            $ids = is_array($users)? $users : [$users];
            $userNames = UserModel::whereIn('id', $ids)->pluck('name')->toArray();
        }
        if ($products = $r->get('products')) {
            $ids = is_array($products)? $products : [$products];
            $productNames = productdata::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }

        return [
            'title'       => trans('supply_chain_trans.store_movements_report_title'),
            'generated_at'=> Carbon::now()->format('Y-m-d H:i'),
            'date_from'   => $r->get('date_from'),
            'date_to'     => $r->get('date_to'),
            'store_names' => $storeNames,
            'type_names'  => $typeNames,
            'user_names'  => $userNames,
            'product_names'=> $productNames,
            'count'       => $rows->count(),
        ];
    }

    /** تصدير Excel باستخدام PhpSpreadsheet */
    private function exportExcel($rows, $meta)
    {
        // متطلبات: phpoffice/phpspreadsheet + ext-zip مفعّلة
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // العناوين
        $headers = [
            trans('supply_chain_trans.table_store_name'),
            trans('supply_chain_trans.table_movement_code'),
            trans('supply_chain_trans.table_movement_type'),
            trans('supply_chain_trans.table_product_name'),
            trans('supply_chain_trans.table_movement_date'),
            trans('supply_chain_trans.table_user'),
            trans('supply_chain_trans.table_quantity'),
            trans('supply_chain_trans.table_ledger_balance'),
            trans('supply_chain_trans.table_unit'),
        ];

        // رأس
        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['store_name'],
                $r['movement_code'],
                $r['movement_type_label'],
                $r['product_name'],
                $r['movement_date'],
                $r['user_name'],
                $r['quantity'],
                $r['product_store_balance'],
                $r['unit_name'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','I') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Store_Movements_' . now()->format('Ymd_His') . '.xlsx';

        // إخراج الملف كاستجابة تنزيل
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
