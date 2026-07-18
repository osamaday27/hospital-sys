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
use App\models\assets\assetdepreciationline;
use App\models\assets\assetdepreciationrun;

// Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class depreciation_reportcontroller extends Controller
{
    /** ================= Entry ================= */
    public function index(Request $r)
    {
        $tab = $r->get('tab', 'lines'); // lines | runs | missing
        $action = $r->get('action');    // print | export_excel | (ajax|html)

        // Print
        if ($action === 'print') {
            [$rows, $meta, $headers] = $this->fetchForTab($r, $tab, forExport:true);
            return view('assets.reports.depreciation_report.print', compact('rows','meta','headers','tab'));
        }

        // Export Excel
        if ($action === 'export_excel') {
            [$rows, $meta, $headers] = $this->fetchForTab($r, $tab, forExport:true);
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

            if ($tab === 'runs') {
                $q = $this->runsBaseQuery($r, $search);
                // عمود 0 = مسلسل => يبدأ الترتيب من 1
                $orderMap = [
                    1=>'dr.period', 2=>'dr.status', 3=>'lines_count', 4=>'total_amount', 5=>'dr.posted_at'
                ];
            } elseif ($tab === 'missing') {
                $q = $this->missingBaseQuery($r, $search);
                $orderMap = [
                    1=>'ai.code', 2=>'name', 3=>'category_name', 4=>'location_name',
                    5=>'ai.dep_method', 6=>'ai.in_service_date', 7=>'nbv', 8=>'ai.last_depreciation_date'
                ];
            } else {
                $q = $this->linesBaseQuery($r, $search);
                $orderMap = [
                    1=>'ai.code', 2=>'name', 3=>'category_name', 4=>'location_name',
                    5=>'dl.dep_method', 6=>'dl.useful_life_months', 7=>'dl.dep_rate',
                    8=>'dr.period', 9=>'dl.amount', 10=>'dl.nbv_after'
                ];
            }

            $orderColIdx = (int) data_get($r->all(), 'order.0.column', 0);
            $orderDir    = data_get($r->all(), 'order.0.dir', 'asc');

            // تجاهل ترتيب عمود المسلسل (#)
            if ($orderColIdx > 0) {
                $orderBy = $orderMap[$orderColIdx] ?? array_values($orderMap)[0];
                $q->orderBy($orderBy, $orderDir);
            }

            $recordsFiltered = (clone $q)->count();
            $rows = $q->skip($start)->take($length)->get();

            $data = $rows->map(function($r) use ($tab) {
                if ($tab === 'runs') {
                    return [
                        'period'       => $r->period ? Carbon::parse($r->period)->format('Y-m') : '—',
                        'status'       => $this->runStatusLabel($r->status),
                        'lines_count'  => (int) ($r->lines_count ?? 0),
                        'total_amount' => number_format((float)($r->total_amount ?? 0), 2),
                        'posted_at'    => $r->posted_at ? Carbon::parse($r->posted_at)->format('Y-m-d H:i') : '—',
                    ];
                } elseif ($tab === 'missing') {
                    return [
                        'code'        => $r->code,
                        'name'        => $r->name,
                        'category'    => $r->category_name,
                        'location'    => $r->location_name,
                        'dep_method'  => $this->depMethodLabel($r->dep_method),
                        'in_service'  => $r->in_service_date ? Carbon::parse($r->in_service_date)->format('Y-m-d') : '—',
                        'nbv'         => number_format((float)$r->nbv, 2),
                        'last_dep'    => $r->last_depreciation_date ? Carbon::parse($r->last_depreciation_date)->format('Y-m-d') : '—',
                    ];
                } else { // lines
                    return [
                        'code'       => $r->code,
                        'name'       => $r->name,
                        'category'   => $r->category_name,
                        'location'   => $r->location_name,
                        'dep_method' => $this->depMethodLabel($r->dep_method),
                        'useful_life'=> (int) $r->useful_life_months,
                        'dep_rate'   => $r->dep_rate !== null ? rtrim(rtrim(number_format((float)$r->dep_rate, 3), '0'), '.') : '—',
                        'period'     => $r->period ? Carbon::parse($r->period)->format('Y-m') : '—',
                        'amount'     => number_format((float)$r->amount, 2),
                        'nbv_after'  => number_format((float)$r->nbv_after, 2),
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
        return view('assets.reports.depreciation_report.index', compact('filters'));
    }

    /** ===== Tabs Fetcher for Print/Excel ===== */
    protected function fetchForTab(Request $r, string $tab, bool $forExport=false): array
    {
        if ($tab === 'runs') {
            $q = $this->runsBaseQuery($r, '');
            $rows = $q->orderBy('dr.period','asc')->get()->map(function($x){
                return [
                    'period'       => $x->period ? Carbon::parse($x->period)->format('Y-m') : '—',
                    'status'       => $this->runStatusLabel($x->status),
                    'lines_count'  => (int) ($x->lines_count ?? 0),
                    'total_amount' => (float) ($x->total_amount ?? 0),
                    'posted_at'    => $x->posted_at ? Carbon::parse($x->posted_at)->format('Y-m-d H:i') : '—',
                ];
            });
            $headers = [
                '#',
                trans('assets.col_period'),
                trans('assets.col_status'),
                trans('assets.col_lines_count'),
                trans('assets.col_total_amount'),
                trans('assets.col_posted_at'),
            ];
        } elseif ($tab === 'missing') {
            $q = $this->missingBaseQuery($r, '');
            $rows = $q->orderBy('ai.code')->get()->map(function($x){
                return [
                    'code'       => $x->code,
                    'name'       => $x->name,
                    'category'   => $x->category_name,
                    'location'   => $x->location_name,
                    'dep_method' => $this->depMethodLabel($x->dep_method),
                    'in_service' => $x->in_service_date ? Carbon::parse($x->in_service_date)->format('Y-m-d') : '—',
                    'nbv'        => (float) $x->nbv,
                    'last_dep'   => $x->last_depreciation_date ? Carbon::parse($x->last_depreciation_date)->format('Y-m-d') : '—',
                ];
            });
            $headers = [
                '#',
                trans('assets.col_code'),
                trans('assets.col_name'),
                trans('assets.col_category'),
                trans('assets.col_location'),
                trans('assets.col_dep_method'),
                trans('assets.col_in_service'),
                trans('assets.col_nbv'),
                trans('assets.col_last_dep'),
            ];
        } else {
            $q = $this->linesBaseQuery($r, '');
            $rows = $q->orderBy('dr.period','asc')->get()->map(function($x){
                return [
                    'code'        => $x->code,
                    'name'        => $x->name,
                    'category'    => $x->category_name,
                    'location'    => $x->location_name,
                    'dep_method'  => $this->depMethodLabel($x->dep_method),
                    'useful_life' => (int) $x->useful_life_months,
                    'dep_rate'    => $x->dep_rate !== null ? (float) $x->dep_rate : null,
                    'period'      => $x->period ? Carbon::parse($x->period)->format('Y-m') : '—',
                    'amount'      => (float) $x->amount,
                    'nbv_after'   => (float) $x->nbv_after,
                ];
            });
            $headers = [
                '#',
                trans('assets.col_code'),
                trans('assets.col_name'),
                trans('assets.col_category'),
                trans('assets.col_location'),
                trans('assets.col_dep_method'),
                trans('assets.col_useful_life'),
                trans('assets.col_dep_rate'),
                trans('assets.col_period'),
                trans('assets.col_amount'),
                trans('assets.col_nbv_after'),
            ];
        }

        $meta = $this->buildMeta($r, $rows, $tab);
        return [$rows, $meta, $headers];
    }

    /** ============== Base Queries ============== */
    protected function linesBaseQuery(Request $r, string $search)
    {
        $q = DB::table('asset_depreciation_lines as dl')
            ->leftJoin('asset_depreciation_runs as dr','dr.id','=','dl.run_id')
            ->leftJoin('asset_items as ai','ai.id','=','dl.asset_id')
            ->leftJoin('asset_categories as ac','ac.id','=','ai.category_id')
            ->leftJoin('asset_locations as al','al.id','=','dl.location_id') // snapshot at run
            ->whereNull('dl.deleted_at');

        $q->select([
            'dl.id',
            'ai.code',
            DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
            'ac.name_ar as category_name',
            DB::raw("COALESCE(al.name_ar, al.name_en) as location_name"),
            'dl.dep_method','dl.useful_life_months','dl.dep_rate',
            'dr.period',
            'dl.amount','dl.nbv_after',
        ]);

        // Filters
        if ($f = $r->get('period_from')) $q->whereDate('dr.period','>=',$f);
        if ($t = $r->get('period_to'))   $q->whereDate('dr.period','<=',$t);

        if ($ids = $this->arr($r->get('category_ids'))) $q->whereIn('ai.category_id',$ids);
        if ($ids = $this->arr($r->get('location_ids'))) $q->whereIn('dl.location_id',$ids);
        if ($ids = $this->arr($r->get('asset_ids')))    $q->whereIn('dl.asset_id',$ids);

        if ($methods = $this->arr($r->get('dep_methods'))) $q->whereIn('dl.dep_method', $methods);

        if ($search !== '') {
            $q->where(function($x) use ($search){
                $x->where('ai.code','like',"%{$search}%")
                  ->orWhere('ai.name_ar','like',"%{$search}%")
                  ->orWhere('ai.name_en','like',"%{$search}%")
                  ->orWhere('ac.name_ar','like',"%{$search}%")
                  ->orWhere(DB::raw("COALESCE(al.name_ar, al.name_en)"),'like',"%{$search}%");
            });
        }

        return $q;
    }

    protected function runsBaseQuery(Request $r, string $search)
    {
        $q = DB::table('asset_depreciation_runs as dr')
            ->leftJoin('asset_depreciation_lines as dl','dl.run_id','=','dr.id')
            ->whereNull('dr.deleted_at');

        $q->select([
            'dr.id','dr.period','dr.status','dr.posted_at',
            DB::raw('COUNT(dl.id) as lines_count'),
            DB::raw('SUM(dl.amount) as total_amount'),
        ])->groupBy('dr.id','dr.period','dr.status','dr.posted_at');

        // Filters
        if ($f = $r->get('period_from')) $q->whereDate('dr.period','>=',$f);
        if ($t = $r->get('period_to'))   $q->whereDate('dr.period','<=',$t);
        if ($search !== '') $q->where('dr.notes','like',"%{$search}%");

        return $q;
    }

    protected function missingBaseQuery(Request $r, string $search)
    {
        // الأصول التي لم يظهر لها أي سطر إهلاك خلال الفترة
        $from = $r->get('period_from');
        $to   = $r->get('period_to');

        $linesInPeriod = DB::table('asset_depreciation_lines as dl')
            ->join('asset_depreciation_runs as dr','dr.id','=','dl.run_id')
            ->when($from, fn($qq)=>$qq->whereDate('dr.period','>=',$from))
            ->when($to,   fn($qq)=>$qq->whereDate('dr.period','<=',$to))
            ->whereNull('dl.deleted_at')
            ->groupBy('dl.asset_id')
            ->pluck('dl.asset_id')->all();

        $q = DB::table('asset_items as ai')
            ->leftJoin('asset_categories as ac','ac.id','=','ai.category_id')
            ->leftJoin('asset_locations as al','al.id','=','ai.location_id')
            ->whereNull('ai.deleted_at');

        if (!empty($linesInPeriod)) {
            $q->whereNotIn('ai.id', $linesInPeriod);
        }

        // شرط: الأصل في الخدمة قبل "إلى"
        $toDate = $to ?: Carbon::now()->toDateString();
        $q->whereDate('ai.in_service_date','<=',$toDate);

        // استبعاد حالات: تقاعد/خردة
        $q->whereNotIn('ai.status', ['retired','scrapped']);

        $q->select([
            'ai.id','ai.code','ai.dep_method','ai.in_service_date','ai.last_depreciation_date',
            DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
            'ac.name_ar as category_name',
            DB::raw("COALESCE(al.name_ar, al.name_en) as location_name"),
            DB::raw("COALESCE(ai.net_book_value, ai.cost - ai.accumulated_depreciation) as nbv"),
        ]);

        // Filters
        if ($ids = $this->arr($r->get('category_ids'))) $q->whereIn('ai.category_id',$ids);
        if ($ids = $this->arr($r->get('location_ids'))) $q->whereIn('ai.location_id',$ids);
        if ($ids = $this->arr($r->get('asset_ids')))    $q->whereIn('ai.id',$ids);
        if ($methods = $this->arr($r->get('dep_methods'))) $q->whereIn('ai.dep_method', $methods);

        if ($search !== '') {
            $q->where(function($x) use ($search){
                $x->where('ai.code','like',"%{$search}%")
                  ->orWhere('ai.name_ar','like',"%{$search}%")
                  ->orWhere('ai.name_en','like',"%{$search}%")
                  ->orWhere('ac.name_ar','like',"%{$search}%")
                  ->orWhere(DB::raw("COALESCE(al.name_ar, al.name_en)"),'like',"%{$search}%");
            });
        }

        return $q;
    }

    /** ============== Filters & Meta ============== */
    protected function getFiltersData(): array
    {
        return [
            'categories' => assetcategory::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'locations'  => assetlocation::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'assets'     => assetitem::select('id','code','name_ar','name_en')->orderBy('name_ar')->limit(5000)->get(),
            'methods'    => $this->distinctMethods(),
        ];
    }

    protected function buildMeta(Request $r, $rows, string $tab): array
    {
        return [
            'title'         => trans('assets.depreciation_report_title'),
            'generated_at'  => Carbon::now()->format('Y-m-d H:i'),
            'period_from'   => $r->get('period_from'),
            'period_to'     => $r->get('period_to'),
            'category_ids'  => $r->get('category_ids'),
            'location_ids'  => $r->get('location_ids'),
            'asset_ids'     => $r->get('asset_ids'),
            'methods'       => $r->get('dep_methods'),
            'count'         => $rows->count(),
            'tab'           => $tab,
        ];
    }

    /** ========= طرق الإهلاك: توليد قائمة مميزة بعناوين مترجمة ========= */
    protected function distinctMethods(): array
    {
        $fromLines  = assetdepreciationline::query()->whereNotNull('dep_method')->distinct()->pluck('dep_method')->toArray();
        $fromAssets = assetitem::query()->whereNotNull('dep_method')->distinct()->pluck('dep_method')->toArray();
        $all = array_values(array_unique(array_filter(array_merge($fromLines, $fromAssets))));

        $result = [];
        foreach ($all as $m) {
            $result[] = [
                'value' => $m,
                'label' => $this->depMethodLabel($m),
            ];
        }

        // رتب حسب التسمية المعروضة (المترجمة)
        usort($result, fn($a,$b)=>strcmp($a['label'],$b['label']));

        return $result;
    }

    protected function hasAnyFilter(Request $r): bool
    {
        return (bool) collect([
            'period_from','period_to','category_ids','location_ids','asset_ids','dep_methods','search.value'
        ])->some(fn($k)=> filled($r->get($k)));
    }

    /** ============== Labels ============== */
    /**
     * إرجاع تسمية مترجمة لطرق الإهلاك بناءً على مفاتيحك:
     *  - assets.dep_method_SL
     *  - assets.dep_method_DDB
     *  - assets.dep_method_UOP
     * مع دعم قيم قديمة مثل straight_line, declining_balance, uop, units_of_production.
     */
    private function depMethodLabel(?string $m): string
    {
        if ($m === null || $m === '') return '—';

        // طبيعـة/تطبيع: إزالة المسافات، تحويل إلى Upper، استبدال الشرطات بمسطرة سفلية
        $norm = strtoupper(trim($m));
        $norm = str_replace(['-', ' '], ['_', '_'], $norm);

        // خرائط القيم المحتملة إلى مفاتيح الترجمة الجديدة
        $map = [
            // القسط الثابت
            'SL'                => 'assets.dep_method_SL',
            'S/L'               => 'assets.dep_method_SL',
            'S_L'               => 'assets.dep_method_SL',
            'STRAIGHT_LINE'     => 'assets.dep_method_SL',

            // الميزان/الرصيد المتناقص (DDB غالبًا)
            'DDB'               => 'assets.dep_method_DDB',
            'DB'                => 'assets.dep_method_DDB',
            'DECLINING_BALANCE' => 'assets.dep_method_DDB',
            'DOUBLE_DECLINING'  => 'assets.dep_method_DDB',
            'DOUBLE_DECLINING_BALANCE' => 'assets.dep_method_DDB',

            // وحدات الإنتاج
            'UOP'                   => 'assets.dep_method_UOP',
            'UNITS_OF_PRODUCTION'   => 'assets.dep_method_UOP',
            'UNITS'                 => 'assets.dep_method_UOP',
        ];

        // لو القيمة الأصلية من النوع القديم camelCase
        $legacy = [
            'straight_line'       => 'assets.dep_method_SL',
            'declining_balance'   => 'assets.dep_method_DDB',
            'double_declining'    => 'assets.dep_method_DDB',
            'double_declining_balance' => 'assets.dep_method_DDB',
            'uop'                 => 'assets.dep_method_UOP',
            'units_of_production' => 'assets.dep_method_UOP',
        ];

        if (isset($legacy[$m])) {
            return trans($legacy[$m]);
        }

        $key = $map[$norm] ?? null;
        return $key ? trans($key) : $m; // لو قيمة غير معروفة، اعرضها كما هي
    }

    private function runStatusLabel(?string $s): string
    {
        $map = [
            'draft'  => trans('assets.status_draft'),
            'posted' => trans('assets.status_posted'),
        ];
        return $map[$s] ?? ($s ?: '—');
    }

    private function arr($val): array
    {
        if (is_null($val) || $val==='') return [];
        if (is_array($val)) return array_filter($val, fn($x)=>$x!=='' && $x!==null);
        return array_filter(explode(',', $val), fn($x)=>$x!=='');
    }

    /** ============== Excel Export ============== */
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

        $filename = 'Depreciation_'.$tab.'_'.now()->format('Ymd_His').'.xlsx';
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
}
