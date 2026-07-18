<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

// Models
use App\models\assets\assetitem;
use App\models\assets\assetcategory;
use App\models\assets\assetlocation;
use App\models\assets\assetdocument;
use App\Models\Suppliers\suppliersdata as Supplier;

// Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class documents_reportcontroller extends Controller
{
    /** ================= Entry ================= */
    public function index(Request $r)
    {
        $tab    = $r->get('tab', 'lines'); // lines | by_type | by_asset
        $action = $r->get('action');       // print | export_excel | kpis | (ajax|html)

        // KPIs
        if ($action === 'kpis') {
            return response()->json($this->computeKpis($r));
        }

        // Print
        if ($action === 'print') {
            [$rows, $meta, $headers] = $this->fetchForTab($r, $tab, true);
            return view('assets.reports.documents_report.print', compact('rows','meta','headers','tab'));
        }

        // Export Excel
        if ($action === 'export_excel') {
            [$rows, $meta, $headers] = $this->fetchForTab($r, $tab, true);
            return $this->exportExcel($rows, $headers, $tab);
        }

        // DataTables AJAX
        if ($r->wantsJson() || $r->ajax()) {
            if (!$this->hasAnyFilter($r) && $r->get('no_data')) {
                return response()->json([
                    'draw' => intval($r->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $draw   = (int) $r->get('draw', 1);
            $start  = (int) $r->get('start', 0);
            $length = (int) $r->get('length', 10);
            $search = trim((string) data_get($r->all(), 'search.value', ''));

            if ($tab === 'by_type') {
                $q = $this->byTypeBaseQuery($r, $search);
                $orderMap = [1=>'doc_type',2=>'docs_count',3=>'with_file',4=>'without_file',5=>'total_cost',6=>'avg_days_since'];
            } elseif ($tab === 'by_asset') {
                $q = $this->byAssetBaseQuery($r, $search);
                $orderMap = [1=>'code',2=>'name',3=>'category_name',4=>'location_name',5=>'docs_count',6=>'with_file',7=>'total_cost',8=>'latest_doc_date'];
            } else {
                $q = $this->linesBaseQuery($r, $search);
                $orderMap = [1=>'ai_code',2=>'name',3=>'category_name',4=>'location_name',5=>'doc_type',6=>'doc_no',7=>'doc_date',8=>'issuer',9=>'cost',10=>'has_file',11=>'days_since'];
            }

            $orderColIdx = (int) data_get($r->all(), 'order.0.column', 0);
            $orderDir    = data_get($r->all(), 'order.0.dir', 'asc');
            if ($orderColIdx > 0 && isset($orderMap[$orderColIdx])) {
                $q->orderBy($orderMap[$orderColIdx], $orderDir);
            }

            $recordsFiltered = (clone $q)->count();
            $rows = $q->skip($start)->take($length)->get();

            $data = $rows->map(function($x) use ($tab) {
                if ($tab === 'by_type') {
                    return [
                        'doc_type'    => $x->doc_type ?: '—',
                        'docs_count'  => (int)$x->docs_count,
                        'with_file'   => (int)$x->with_file,
                        'without_file'=> (int)$x->without_file,
                        'total_cost'  => number_format((float)$x->total_cost, 2),
                        'avg_days'    => $x->avg_days_since !== null ? round($x->avg_days_since, 1) : null,
                    ];
                } elseif ($tab === 'by_asset') {
                    return [
                        'code'        => $x->code,
                        'name'        => $x->name,
                        'category'    => $x->category_name,
                        'location'    => $x->location_name,
                        'docs_count'  => (int)$x->docs_count,
                        'with_file'   => (int)$x->with_file,
                        'total_cost'  => number_format((float)$x->total_cost, 2),
                        'latest_date' => $x->latest_doc_date ? Carbon::parse($x->latest_doc_date)->format('Y-m-d') : '—',
                    ];
                } else { // lines
                    return [
                        'ai_code'    => $x->ai_code,
                        'name'       => $x->name,
                        'category'   => $x->category_name,
                        'location'   => $x->location_name,
                        'doc_type'   => $x->doc_type ?: '—',
                        'doc_no'     => $x->doc_no ?: '—',
                        'doc_date'   => $x->doc_date ? Carbon::parse($x->doc_date)->format('Y-m-d') : '—',
                        'issuer'     => $x->issuer ?: '—',
                        'cost'       => number_format((float)($x->cost ?? 0), 2),
                        'has_file'   => $x->has_file ? trans('assets.doc_has_file') : trans('assets.doc_no_file'),
                        'days_since' => $x->days_since !== null ? (int)$x->days_since : null,
                        'notes'      => $x->notes ?: '—',
                    ];
                }
            })->values();

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $recordsFiltered,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        // HTML
        $filters = $this->getFiltersData();
        return view('assets.reports.documents_report.index', compact('filters'));
    }

    /** ============== Base Queries ============== */
    protected function linesBaseQuery(Request $r, string $search)
    {
        $todaySql = DB::raw("CURRENT_DATE"); // MySQL
        $q = DB::table('asset_documents as d')
            ->leftJoin('asset_items as ai', 'ai.id', '=', 'd.asset_id')
            ->leftJoin('asset_categories as ac', 'ac.id', '=', 'ai.category_id')
            ->leftJoin('asset_locations as al', 'al.id', '=', 'ai.location_id')
            ->leftJoin('suppliers_data as s', 's.id', '=', 'd.supplier_id');
            // لا يوجد deleted_at في الجدول، لذا لا نضع whereNull('d.deleted_at')

        $q->select([
            'd.id',
            'ai.code as ai_code',
            DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
            DB::raw("COALESCE(ac.name_ar, ac.name_en) as category_name"),
            DB::raw("COALESCE(al.name_ar, al.name_en) as location_name"),
            'd.doc_type','d.doc_no','d.doc_date',
            DB::raw("COALESCE(s.name_ar, s.name_en) as issuer"),
            'd.cost','d.file_path','d.notes',
            DB::raw("CASE WHEN d.file_path IS NULL OR d.file_path = '' THEN 0 ELSE 1 END as has_file"),
            DB::raw("CASE WHEN d.doc_date IS NULL THEN NULL ELSE DATEDIFF($todaySql, d.doc_date) END as days_since"),
        ]);

        // Filters
        if ($types = $this->arr($r->get('doc_types'))) $q->whereIn('d.doc_type', $types);
        if ($sup = $this->arr($r->get('supplier_ids'))) $q->whereIn('d.supplier_id', $sup);
        if ($ids = $this->arr($r->get('category_ids'))) $q->whereIn('ai.category_id', $ids);
        if ($ids = $this->arr($r->get('location_ids'))) $q->whereIn('ai.location_id', $ids);
        if ($ids = $this->arr($r->get('asset_ids')))    $q->whereIn('ai.id', $ids);

        // تاريخ الإصدار (doc_date)
        if ($f = $r->get('date_from')) $q->whereDate('d.doc_date','>=',$f);
        if ($t = $r->get('date_to'))   $q->whereDate('d.doc_date','<=',$t);

        // مرفقات؟ (has_file: yes/no)
        if (($hf = $r->get('has_file')) !== null && $hf !== '') {
            if (in_array($hf, ['1','true',1,true], true)) {
                $q->whereNotNull('d.file_path')->where('d.file_path','<>','');
            } else {
                $q->where(function($w){
                    $w->whereNull('d.file_path')->orWhere('d.file_path','=','');
                });
            }
        }

        if ($search !== '') {
            $q->where(function($x) use ($search){
                $x->where('ai.code','like',"%{$search}%")
                  ->orWhere('ai.name_ar','like',"%{$search}%")
                  ->orWhere('ai.name_en','like',"%{$search}%")
                  ->orWhere('d.doc_no','like',"%{$search}%")
                  ->orWhere('d.doc_type','like',"%{$search}%")
                  ->orWhere(DB::raw("COALESCE(s.name_ar, s.name_en)"),'like',"%{$search}%");
            });
        }

        return $q;
    }

protected function byTypeBaseQuery(Request $r, string $search)
{
    $todaySql = DB::raw("CURRENT_DATE");

    $q = DB::table('asset_documents as d')
        ->leftJoin('asset_items as ai', 'ai.id', '=', 'd.asset_id')
        ->leftJoin('asset_categories as ac', 'ac.id', '=', 'ai.category_id')
        ->leftJoin('asset_locations as al', 'al.id', '=', 'ai.location_id');
        // لا نستخدم deleted_at لأنه غير موجود بجدول asset_documents

    // ===== نفس فلاتر خطوط المستندات لكن بدون select لأعمدة إضافية =====
    if ($types = $this->arr($r->get('doc_types')))   $q->whereIn('d.doc_type', $types);
    if ($sup   = $this->arr($r->get('supplier_ids'))) $q->whereIn('d.supplier_id', $sup);
    if ($ids   = $this->arr($r->get('category_ids'))) $q->whereIn('ai.category_id', $ids);
    if ($ids   = $this->arr($r->get('location_ids'))) $q->whereIn('ai.location_id', $ids);
    if ($ids   = $this->arr($r->get('asset_ids')))    $q->whereIn('ai.id', $ids);

    if ($f = $r->get('date_from')) $q->whereDate('d.doc_date','>=',$f);
    if ($t = $r->get('date_to'))   $q->whereDate('d.doc_date','<=',$t);

    if (($hf = $r->get('has_file')) !== null && $hf !== '') {
        if (in_array($hf, ['1','true',1,true], true)) {
            $q->whereNotNull('d.file_path')->where('d.file_path','<>','');
        } else {
            $q->where(function($w){
                $w->whereNull('d.file_path')->orWhere('d.file_path','=','');
            });
        }
    }

    // ===== تجميع صحيح مع أعمدة مُجمّعة فقط =====
    $q->selectRaw("
        d.doc_type                                                          as doc_type,
        COUNT(d.id)                                                         as docs_count,
        SUM(CASE WHEN d.file_path IS NULL OR d.file_path='' THEN 0 ELSE 1 END) as with_file,
        SUM(CASE WHEN d.file_path IS NULL OR d.file_path='' THEN 1 ELSE 0 END) as without_file,
        SUM(COALESCE(d.cost,0))                                             as total_cost,
        AVG(CASE WHEN d.doc_date IS NULL THEN NULL ELSE DATEDIFF($todaySql, d.doc_date) END) as avg_days_since
    ")
    ->groupBy('d.doc_type');

    // بحث عام على نوع الوثيقة فقط (ويمكن توسيعه عند الحاجة)
    if ($search !== '') {
        $q->havingRaw("doc_type LIKE ?", ["%{$search}%"]);
    }

    return $q;
}


    protected function byAssetBaseQuery(Request $r, string $search)
    {
        $q = DB::table('asset_documents as d')
            ->leftJoin('asset_items as ai', 'ai.id', '=', 'd.asset_id')
            ->leftJoin('asset_categories as ac', 'ac.id', '=', 'ai.category_id')
            ->leftJoin('asset_locations as al', 'al.id', '=', 'ai.location_id');
            // لا يوجد deleted_at في الجدول

        // نفس الفلاتر العامة
        if ($types = $this->arr($r->get('doc_types'))) $q->whereIn('d.doc_type', $types);
        if ($sup = $this->arr($r->get('supplier_ids'))) $q->whereIn('d.supplier_id', $sup);
        if ($ids = $this->arr($r->get('category_ids'))) $q->whereIn('ai.category_id', $ids);
        if ($ids = $this->arr($r->get('location_ids'))) $q->whereIn('ai.location_id', $ids);
        if ($ids = $this->arr($r->get('asset_ids')))    $q->whereIn('ai.id', $ids);
        if ($f = $r->get('date_from')) $q->whereDate('d.doc_date','>=',$f);
        if ($t = $r->get('date_to'))   $q->whereDate('d.doc_date','<=',$t);
        if (($hf = $r->get('has_file')) !== null && $hf !== '') {
            if (in_array($hf, ['1','true',1,true], true)) {
                $q->whereNotNull('d.file_path')->where('d.file_path','<>','');
            } else {
                $q->where(function($w){
                    $w->whereNull('d.file_path')->orWhere('d.file_path','=','');
                });
            }
        }

        // تجميع حسب الأصل
        $q->groupBy('ai.id','ai.code','ai.name_ar','ai.name_en','ac.name_ar','ac.name_en','al.name_ar','al.name_en');

        $q->selectRaw("
            ai.id,
            ai.code,
            COALESCE(ai.name_ar, ai.name_en) as name,
            COALESCE(ac.name_ar, ac.name_en) as category_name,
            COALESCE(al.name_ar, al.name_en) as location_name,
            COUNT(d.id) as docs_count,
            SUM(CASE WHEN d.file_path IS NULL OR d.file_path='' THEN 0 ELSE 1 END) as with_file,
            SUM(COALESCE(d.cost,0)) as total_cost,
            MAX(d.doc_date) as latest_doc_date
        ");

        // بحث عام
        if ($search !== '') {
            $q->havingRaw("(ai.code LIKE ? OR COALESCE(ai.name_ar, ai.name_en) LIKE ? OR COALESCE(ac.name_ar, ac.name_en) LIKE ? OR COALESCE(al.name_ar, al.name_en) LIKE ?)",
                ["%{$search}%","%{$search}%","%{$search}%","%{$search}%"]);
        }

        return $q;
    }

    /** ============== KPIs/Filters/Helpers ============== */
    protected function computeKpis(Request $r): array
    {
        $base = $this->linesBaseQuery($r, '');
        $rows = (clone $base)->get();

        $total = $rows->count();
        $withFile = $rows->where('has_file', 1)->count();
        $without = $total - $withFile;
        $costSum = round($rows->sum('cost'), 2);

        // توزيع حسب النوع
        $byType = $rows->groupBy('doc_type')->map(function($g){
            return [
                'count'     => $g->count(),
                'with_file' => $g->where('has_file',1)->count(),
                'cost'      => round($g->sum('cost'), 2),
            ];
        })->sortKeys()->toArray();

        return [
            'total_docs'        => $total,
            'with_attachment'   => $withFile,
            'without_attachment'=> $without,
            'total_cost'        => $costSum,
            'by_type'           => $byType,
        ];
    }

    protected function getFiltersData(): array
    {
        // أنواع الوثائق (distinct) — بدون deleted_at
        $docTypes = assetdocument::query()
            ->whereNotNull('doc_type')
            ->distinct()
            ->pluck('doc_type')
            ->filter()
            ->values();

        return [
            'doc_types'  => $docTypes,
            'categories' => assetcategory::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'locations'  => assetlocation::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'assets'     => assetitem::select('id','code','name_ar','name_en')->orderBy('name_ar')->limit(5000)->get(),
            'suppliers'  => Supplier::select('id','name_ar','name_en')->orderBy('name_ar')->limit(5000)->get(),
        ];
    }

    protected function buildMeta(Request $r, $rows, string $tab): array
    {
        return [
            'title'         => trans('assets.documents_report_title'),
            'generated_at'  => Carbon::now()->format('Y-m-d H:i'),
            'date_from'     => $r->get('date_from'),
            'date_to'       => $r->get('date_to'),
            'doc_types'     => $r->get('doc_types'),
            'supplier_ids'  => $r->get('supplier_ids'),
            'category_ids'  => $r->get('category_ids'),
            'location_ids'  => $r->get('location_ids'),
            'asset_ids'     => $r->get('asset_ids'),
            'has_file'      => $r->get('has_file'),
            'count'         => $rows->count(),
            'tab'           => $tab,
        ];
    }

    protected function hasAnyFilter(Request $r): bool
    {
        return (bool) collect([
            'doc_types','supplier_ids','category_ids','location_ids','asset_ids','date_from','date_to','has_file','search.value'
        ])->some(fn($k)=> filled($r->get($k)));
    }

    protected function exportExcel($rows, array $headers, string $tab)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([$headers], NULL, 'A1');
        $rowIndex = 2;
        $sr = 1;

        foreach ($rows as $r) {
            $sheet->fromArray([array_merge([$sr], array_values($r))], NULL, 'A'.$rowIndex);
            $rowIndex++; $sr++;
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Documents_'.$tab.'_'.now()->format('Ymd_His').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /** ============== Print/Excel rows per tab ============== */
    protected function fetchForTab(Request $r, string $tab, bool $forExport=false): array
    {
        if ($tab === 'by_type') {
            $q = $this->byTypeBaseQuery($r, '');
            $rows = $q->orderBy('doc_type')->get()->map(function($x){
                return [
                    'doc_type'    => $x->doc_type ?: '—',
                    'docs_count'  => (int)$x->docs_count,
                    'with_file'   => (int)$x->with_file,
                    'without_file'=> (int)$x->without_file,
                    'total_cost'  => (float)$x->total_cost,
                    'avg_days'    => $x->avg_days_since !== null ? round($x->avg_days_since, 1) : null,
                ];
            });

            $headers = [
                '#',
                trans('assets.col_doc_type'),
                trans('assets.col_docs_count'),
                trans('assets.col_with_file'),
                trans('assets.col_without_file'),
                trans('assets.col_total_cost'),
                trans('assets.col_avg_days_since'),
            ];
        } elseif ($tab === 'by_asset') {
            $q = $this->byAssetBaseQuery($r, '');
            $rows = $q->orderBy('ai.code')->get()->map(function($x){
                return [
                    'code'        => $x->code,
                    'name'        => $x->name,
                    'category'    => $x->category_name,
                    'location'    => $x->location_name,
                    'docs_count'  => (int)$x->docs_count,
                    'with_file'   => (int)$x->with_file,
                    'total_cost'  => (float)$x->total_cost,
                    'latest_date' => $x->latest_doc_date ? Carbon::parse($x->latest_doc_date)->format('Y-m-d') : '—',
                ];
            });

            $headers = [
                '#',
                trans('assets.col_code'),
                trans('assets.col_name'),
                trans('assets.col_category'),
                trans('assets.col_location'),
                trans('assets.col_docs_count'),
                trans('assets.col_with_file'),
                trans('assets.col_total_cost'),
                trans('assets.col_latest_doc_date'),
            ];
        } else {
            $q = $this->linesBaseQuery($r, '');
            $rows = $q->orderBy('d.doc_date','desc')->get()->map(function($x){
                return [
                    'ai_code'    => $x->ai_code,
                    'name'       => $x->name,
                    'category'   => $x->category_name,
                    'location'   => $x->location_name,
                    'doc_type'   => $x->doc_type ?: '—',
                    'doc_no'     => $x->doc_no ?: '—',
                    'doc_date'   => $x->doc_date ? Carbon::parse($x->doc_date)->format('Y-m-d') : '—',
                    'issuer'     => $x->issuer ?: '—',
                    'cost'       => (float)($x->cost ?? 0),
                    'has_file'   => $x->has_file ? trans('assets.doc_has_file') : trans('assets.doc_no_file'),
                    'days_since' => $x->days_since !== null ? (int)$x->days_since : null,
                    'notes'      => $x->notes ?: '—',
                ];
            });

            $headers = [
                '#',
                trans('assets.col_code'),
                trans('assets.col_name'),
                trans('assets.col_category'),
                trans('assets.col_location'),
                trans('assets.col_doc_type'),
                trans('assets.col_doc_no'),
                trans('assets.col_doc_date'),
                trans('assets.col_issuer'),
                trans('assets.col_cost'),
                trans('assets.col_has_file'),
                trans('assets.col_days_since'),
                trans('assets.col_notes'),
            ];
        }

        $meta = $this->buildMeta($r, $rows, $tab);
        return [$rows, $meta, $headers];
    }

    /** ============== Helpers ============== */
    private function arr($val): array
    {
        if (is_null($val) || $val==='') return [];
        if (is_array($val)) return array_filter($val, fn($x)=>$x!=='' && $x!==null);
        return array_filter(explode(',', $val), fn($x)=>$x!=='');
    }
}
