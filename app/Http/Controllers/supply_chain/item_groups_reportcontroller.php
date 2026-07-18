<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Models
use App\Models\supply_chain\productcategory as Category;

class item_groups_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // ===== Print =====
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.item_groups_report.print', compact('rows', 'meta'));
        }

        // ===== Export Excel =====
        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // ===== AJAX (DataTables) =====
        if ($r->wantsJson() || $r->ajax()) {
            $q = $this->baseQuery();

            // بحث عام
            $search = trim((string)($r->input('search.value') ?? $r->get('q')));
            if ($search !== '') {
                $q->where(function ($w) use ($search) {
                    $w->where('c.name_ar', 'like', "%{$search}%")
                      ->orWhere('c.name_en', 'like', "%{$search}%")
                      ->orWhere('c.type', 'like', "%{$search}%")
                      ->orWhere('parent.name_ar', 'like', "%{$search}%")
                      ->orWhere('parent.name_en', 'like', "%{$search}%");
                });
            }

            $totalFiltered = (clone $q)->count(); // صف واحد لكل مجموعة (لا توجد مكررات)

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 1));
            $orderDir         = strtolower($r->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
            $orderMap = [
                0 => 'c.id',
                1 => 'c.name_ar',
                2 => 'c.type',
                3 => 'c.status',
                4 => 'products_count',
                5 => 'parent.name_ar',
                6 => 'c.sort',
                7 => 'c.created_at',
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'c.name_ar';
            $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $rows = $q->skip($start)->take($length)->get();

            // تجهيز الرد
            $data = [];
            foreach ($rows as $row) {
                $catName   = trim(($row->name_ar ?? '') . ' / ' . ($row->name_en ?? ''));
                $parentNm  = trim(($row->parent_name_ar ?? '') . ' / ' . ($row->parent_name_en ?? ''));
                $statusLbl = ((int)($row->status ?? 0) === 1)
                    ? trans('supply_chain_trans.status_active')
                    : trans('supply_chain_trans.status_inactive');

                $data[] = [
                    // 0 مسلسل يحسب في الواجهة
                    'category_name'  => $catName ?: '—',
                    'type'           => $row->type ?: '—',
                    'status_label'   => $statusLbl,
                    'products_count' => (int)($row->products_count ?? 0),
                    'parent_name'    => ($parentNm !== ' / ') ? $parentNm : '—',
                    'sort'           => is_null($row->sort) ? '—' : (string)$row->sort,
                    'created_at'     => $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i') : '—',
                ];
            }

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // ===== HTML =====
        return view('supply_chain.reports.item_groups_report.index');
    }

    /** الاستعلام الأساسي (بدون فلاتر) */
    private function baseQuery()
    {
        // ملاحظة مهمة: نستخدم alias + تعطيل SoftDeletes لتفادي خطأ "Unknown column … deleted_at"
        $q = Category::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->from('product_categories as c')
            ->whereNull('c.deleted_at')
            ->leftJoin('product_categories as parent', 'parent.id', '=', 'c.category')
            ->select([
                'c.id',
                'c.name_ar',
                'c.name_en',
                'c.type',
                'c.status',
                'c.sort',
                'c.category',
                'c.created_at',
                'parent.name_ar as parent_name_ar',
                'parent.name_en as parent_name_en',
            ])
            ->selectSub(function ($sub) {
                $sub->from('products_data as p')
                    ->selectRaw('COUNT(*)')
                    ->whereNull('p.deleted_at')
                    ->whereColumn('p.product_category_id', 'c.id');
            }, 'products_count');

        return $q;
    }

    /** تجهيز كل الصفوف للطباعة/الإكسل */
    private function fetchAllForReport(Request $r): array
    {
        $rows = $this->baseQuery()
            ->orderBy('c.name_ar')
            ->limit(200000)
            ->get()
            ->map(function ($row) {
                $catName   = trim(($row->name_ar ?? '') . ' / ' . ($row->name_en ?? ''));
                $parentNm  = trim(($row->parent_name_ar ?? '') . ' / ' . ($row->parent_name_en ?? ''));
                $statusLbl = ((int)($row->status ?? 0) === 1)
                    ? trans('supply_chain_trans.status_active')
                    : trans('supply_chain_trans.status_inactive');

                return [
                    'category_name'  => $catName ?: '—',
                    'type'           => $row->type ?: '—',
                    'status_label'   => $statusLbl,
                    'products_count' => (int)($row->products_count ?? 0),
                    'parent_name'    => ($parentNm !== ' / ') ? $parentNm : '—',
                    'sort'           => is_null($row->sort) ? '—' : (string)$row->sort,
                    'created_at'     => $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i') : '—',
                ];
            });

        $meta = [
            'title'        => trans('supply_chain_trans.item_groups_report_title'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'count'        => $rows->count(),
        ];

        return [$rows, $meta];
    }

    /** تصدير Excel */
    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('supply_chain_trans.table_category_name'),
            trans('supply_chain_trans.table_type'),
            trans('supply_chain_trans.table_status'),
            trans('supply_chain_trans.table_products_count'),
            trans('supply_chain_trans.table_parent_category'),
            trans('supply_chain_trans.table_sort'),
            trans('supply_chain_trans.table_created_at'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['category_name'],
                $r['type'],
                $r['status_label'],
                $r['products_count'],
                $r['parent_name'],
                $r['sort'],
                $r['created_at'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','G') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Item_Groups_' . now()->format('Ymd_His') . '.xlsx';

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
