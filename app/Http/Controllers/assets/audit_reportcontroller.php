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
use App\models\assets\assetaudit;
use App\models\assets\assetauditline;

// Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class audit_reportcontroller extends Controller
{
    /** ================= Entry ================= */
    public function index(Request $r)
    {
        $tab    = $r->get('tab', 'lines'); // lines | audits | unreviewed
        $action = $r->get('action');       // print | export_excel | kpis | (ajax|html)

        // KPIs bar
        if ($action === 'kpis') {
            return response()->json($this->computeKpis($r));
        }

        // Print
        if ($action === 'print') {
            [$rows, $meta, $headers] = $this->fetchForTab($r, $tab);
            return view('assets.reports.audit_report.print', compact('rows','meta','headers','tab'));
        }

        // Export Excel
        if ($action === 'export_excel') {
            [$rows, $meta, $headers] = $this->fetchForTab($r, $tab);
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

            if ($tab === 'audits') {
                $q = $this->auditsBaseQuery($r, $search);
                $orderMap = [
                    1=>'a.code', 2=>'a.title', 3=>'a.status', 4=>'a.started_at', 5=>'a.finished_at',
                    6=>'a.expected_count', 7=>'a.scanned_count', 8=>'a.matched_count', 9=>'a.mismatch_count',
                    10=>'a.missing_count', 11=>'a.extra_count'
                ];
            } elseif ($tab === 'unreviewed') {
                $q = $this->unreviewedBaseQuery($r, $search);
                $orderMap = [
                    1=>'ai.code', 2=>'name', 3=>'category_name', 4=>'location_name', 5=>'ai.status', 6=>'nbv'
                ];
            } else {
                $q = $this->linesBaseQuery($r, $search);
                $orderMap = [
                    1=>'a.code', 2=>'al.asset_code', 3=>'name', 4=>'category_name',
                    5=>'el_name', 6=>'fl_name', 7=>'al.result', 8=>'al.scan_count', 9=>'al.last_scanned_at',
                    10=>'al.scanned_by', 11=>'nbv_at_audit'
                ];
            }

            $orderColIdx = (int) data_get($r->all(), 'order.0.column', 0);
            $orderDir    = data_get($r->all(), 'order.0.dir', 'asc');
            if ($orderColIdx > 0 && isset($orderMap[$orderColIdx])) {
                $q->orderBy($orderMap[$orderColIdx], $orderDir);
            }

            $recordsFiltered = (clone $q)->count();
            $rows = $q->skip($start)->take($length)->get();

            $data = $rows->map(function($x) use ($tab) {
                if ($tab === 'audits') {
                    $scanned = (int)($x->scanned_count ?? 0);
                    $den = max(1, $scanned);
                    $compliance = round(((int)($x->matched_count ?? 0)) * 100.0 / $den, 2);

                    return [
                        'code'           => $x->code,
                        'title'          => $x->title ?? '—',
                        'status'         => $this->auditStatusLabel($x->status),
                        'started_at'     => $x->started_at ? Carbon::parse($x->started_at)->format('Y-m-d H:i') : '—',
                        'finished_at'    => $x->finished_at ? Carbon::parse($x->finished_at)->format('Y-m-d H:i') : '—',
                        'expected_count' => (int)($x->expected_count ?? 0),
                        'scanned_count'  => (int)($x->scanned_count ?? 0),
                        'matched_count'  => (int)($x->matched_count ?? 0),
                        'mismatch_count' => (int)($x->mismatch_count ?? 0),
                        'missing_count'  => (int)($x->missing_count ?? 0),
                        'extra_count'    => (int)($x->extra_count ?? 0),
                        'compliance'     => number_format($compliance, 2) . '%',
                    ];
                } elseif ($tab === 'unreviewed') {
                    return [
                        'asset_code' => $x->code,
                        'asset_name' => $x->name,
                        'category'   => $x->category_name,
                        'location'   => $x->location_name,
                        'status'     => $this->assetStatusLabel($x->status),
                        'nbv'        => number_format((float)$x->nbv, 2),
                    ];
                } else { // lines
                    return [
                        'audit_code'  => $x->audit_code,
                        'asset_code'  => $x->asset_code_used,
                        'asset_name'  => $x->name,
                        'category'    => $x->category_name,
                        'exp_loc'     => $x->el_name ?: '—',
                        'found_loc'   => $x->fl_name ?: '—',
                        'result'      => $this->auditResultLabel($x->result),
                        'scan_count'  => (int)($x->scan_count ?? 0),
                        'last_scan'   => $x->last_scanned_at ? Carbon::parse($x->last_scanned_at)->format('Y-m-d H:i') : '—',
                        'user'        => $x->scanned_by ?: '—',
                        'nbv'         => number_format((float)$x->nbv_at_audit, 2),
                        'notes'       => $x->notes ?: '—',
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
        return view('assets.reports.audit_report.index', compact('filters'));
    }

    /** ============== Base Queries ============== */
    protected function linesBaseQuery(Request $r, string $search)
    {
        // ملاحظة: نعتمد expected_* و found_* كما في fillable
        $q = DB::table('asset_audit_lines as al')
            ->leftJoin('asset_audits as a','a.id','=','al.audit_id')
            ->leftJoin('asset_items as ai','ai.id','=','al.asset_id')
            ->leftJoin('asset_categories as ac','ac.id','=','ai.category_id')
            ->leftJoin('asset_locations as el','el.id','=','al.expected_location_id')
            ->leftJoin('asset_locations as fl','fl.id','=','al.found_location_id')
            ->whereNull('al.deleted_at');

        // NBV بتاريخ الجرد (أقرب سطر إهلاك قبل/حتى انتهى الجرد، وإلا NBV الحالية)
        $nbvExpr = "COALESCE(
            (SELECT dl.nbv_after
             FROM asset_depreciation_lines dl
             JOIN asset_depreciation_runs dr ON dr.id=dl.run_id
             WHERE dl.asset_id = ai.id
               AND dr.period <= COALESCE(a.finished_at, a.started_at)
             ORDER BY dr.period DESC
             LIMIT 1),
            COALESCE(ai.net_book_value, ai.cost - ai.accumulated_depreciation)
        )";

        $q->select([
            'al.id',
            'a.code as audit_code',
            DB::raw("COALESCE(al.asset_code, ai.code) as asset_code_used"),
            DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
            'ac.name_ar as category_name',
            DB::raw("COALESCE(el.name_ar, el.name_en) as el_name"),
            DB::raw("COALESCE(fl.name_ar, fl.name_en) as fl_name"),
            'al.result','al.scan_count','al.last_scanned_at','al.scanned_by','al.notes',
            DB::raw("$nbvExpr as nbv_at_audit"),
        ]);

        // فلاتر
        $this->applyCommonFilters($q, $r); // code/period/location/category/result

        if ($search !== '') {
            $q->where(function($x) use ($search){
                $x->where('a.code','like',"%{$search}%")
                  ->orWhere('al.asset_code','like',"%{$search}%")
                  ->orWhere('ai.code','like',"%{$search}%")
                  ->orWhere('ai.name_ar','like',"%{$search}%")
                  ->orWhere('ai.name_en','like',"%{$search}%")
                  ->orWhere(DB::raw("COALESCE(el.name_ar, el.name_en)"),'like',"%{$search}%")
                  ->orWhere(DB::raw("COALESCE(fl.name_ar, fl.name_en)"),'like',"%{$search}%");
            });
        }

        return $q;
    }

    protected function auditsBaseQuery(Request $r, string $search)
    {
        $q = DB::table('asset_audits as a')->whereNull('a.deleted_at');

        $q->select([
            'a.id','a.code','a.title','a.status','a.started_at','a.finished_at',
            'a.expected_count','a.scanned_count','a.matched_count','a.mismatch_count','a.missing_count','a.extra_count',
        ]);

        // فلاتر على كيان الجرد نفسه
        if ($codes = $this->arr($r->get('audit_codes'))) $q->whereIn('a.code', $codes);
        $dateOn = $r->get('date_on', 'finished_at');
        $col = in_array($dateOn, ['started_at','finished_at']) ? $dateOn : 'finished_at';
        if ($f = $r->get('date_from')) $q->whereDate("a.$col", '>=', $f);
        if ($t = $r->get('date_to'))   $q->whereDate("a.$col", '<=', $t);

        if ($search !== '') {
            $q->where(function($x) use ($search){
                $x->where('a.code','like',"%{$search}%")
                  ->orWhere('a.title','like',"%{$search}%")
                  ->orWhere('a.notes','like',"%{$search}%");
            });
        }

        return $q;
    }

    protected function unreviewedBaseQuery(Request $r, string $search)
    {
        // آخر جرد مكتمل (أو آخر finished_at)
        $lastAudit = DB::table('asset_audits')->whereNull('deleted_at')->whereNotNull('finished_at')
            ->orderByDesc('finished_at')->first();

        $lastId = $lastAudit->id ?? null;

        $q = DB::table('asset_items as ai')
            ->leftJoin('asset_categories as ac','ac.id','=','ai.category_id')
            ->leftJoin('asset_locations as al','al.id','=','ai.location_id')
            ->leftJoin('asset_audit_lines as aln', function($j) use ($lastId){
                $j->on('aln.asset_id','=','ai.id');
                if ($lastId) $j->where('aln.audit_id', '=', $lastId);
                $j->whereNull('aln.deleted_at');
            })
            ->whereNull('ai.deleted_at')
            ->whereNull('aln.id'); // لم يدخل آخر جرد

        $q->select([
            'ai.id','ai.code',
            DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
            'ai.status',
            DB::raw("COALESCE(ac.name_ar, ac.name_en) as category_name"),
            DB::raw("COALESCE(al.name_ar, al.name_en) as location_name"),
            DB::raw("COALESCE(ai.net_book_value, ai.cost - ai.accumulated_depreciation) as nbv"),
        ]);

        // فلاتر منطقية للأصول
        if ($ids = $this->arr($r->get('category_ids'))) $q->whereIn('ai.category_id',$ids);
        if ($ids = $this->arr($r->get('location_ids'))) $q->whereIn('ai.location_id',$ids);

        if ($search !== '') {
            $q->where(function($x) use ($search){
                $x->where('ai.code','like',"%{$search}%")
                  ->orWhere('ai.name_ar','like',"%{$search}%")
                  ->orWhere('ai.name_en','like',"%{$search}%")
                  ->orWhere(DB::raw("COALESCE(ac.name_ar, ac.name_en)"),'like',"%{$search}%")
                  ->orWhere(DB::raw("COALESCE(al.name_ar, al.name_en)"),'like',"%{$search}%");
            });
        }

        return $q;
    }

    /** ============== Filters, Meta & Helpers ============== */
    protected function applyCommonFilters($q, Request $r): void
    {
        // على مستوى سطور الجرد
        if ($codes = $this->arr($r->get('audit_codes'))) $q->whereIn('a.code', $codes);

        $dateOn = $r->get('date_on', 'finished_at'); // started_at | finished_at
        $col = in_array($dateOn, ['started_at','finished_at']) ? $dateOn : 'finished_at';
        if ($f = $r->get('date_from')) $q->whereDate("a.$col", '>=', $f);
        if ($t = $r->get('date_to'))   $q->whereDate("a.$col", '<=', $t);

        // موقع/فرع (نوسّعها على المتوقع أو الموجود)
        if ($locs = $this->arr($r->get('location_ids'))) {
            $q->where(function($x) use ($locs){
                $x->whereIn('al.expected_location_id', $locs)
                  ->orWhereIn('al.found_location_id', $locs);
            });
        }

        if ($cats = $this->arr($r->get('category_ids'))) $q->whereIn('ai.category_id', $cats);

        if ($results = $this->arr($r->get('results'))) $q->whereIn('al.result', $results);
    }

    protected function getFiltersData(): array
    {
        // نتائج/حالات البند
        $results = [
            ['value'=>'matched',            'label'=>$this->auditResultLabel('matched')],
            ['value'=>'missing',            'label'=>$this->auditResultLabel('missing')],
            ['value'=>'extra',              'label'=>$this->auditResultLabel('extra')],
            ['value'=>'location_mismatch',  'label'=>$this->auditResultLabel('location_mismatch')],
            ['value'=>'store_mismatch',     'label'=>$this->auditResultLabel('store_mismatch')],
            ['value'=>'status_mismatch',    'label'=>$this->auditResultLabel('status_mismatch')],
        ];

        return [
            'categories' => assetcategory::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'locations'  => assetlocation::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'audits'     => assetaudit::query()->orderByDesc('finished_at')->limit(500)->get(['id','code','title','finished_at']),
            'results'    => $results,
        ];
    }

    protected function buildMeta(Request $r, $rows, string $tab): array
    {
        return [
            'title'         => trans('assets.audit_report_title'),
            'generated_at'  => Carbon::now()->format('Y-m-d H:i'),
            'date_on'       => $r->get('date_on','finished_at'),
            'date_from'     => $r->get('date_from'),
            'date_to'       => $r->get('date_to'),
            'audit_codes'   => $r->get('audit_codes'),
            'location_ids'  => $r->get('location_ids'),
            'category_ids'  => $r->get('category_ids'),
            'results'       => $r->get('results'),
            'count'         => $rows->count(),
            'tab'           => $tab,
        ];
    }

    protected function hasAnyFilter(Request $r): bool
    {
        return (bool) collect([
            'audit_codes','date_from','date_to','location_ids','category_ids','results','search.value'
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

        $filename = 'Audit_'.$tab.'_'.now()->format('Ymd_His').'.xlsx';
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
    protected function fetchForTab(Request $r, string $tab): array
    {
        if ($tab === 'audits') {
            $q = $this->auditsBaseQuery($r, '');
            $rows = $q->orderBy('a.finished_at','desc')->get()->map(function($x){
                $scanned = (int)($x->scanned_count ?? 0);
                $den = max(1, $scanned);
                $compliance = $scanned ? round(((int)($x->matched_count ?? 0)) * 100.0 / $den, 2) : 0.0;
                return [
                    'code'           => $x->code,
                    'title'          => $x->title ?? '—',
                    'status'         => $this->auditStatusLabel($x->status),
                    'started_at'     => $x->started_at ? Carbon::parse($x->started_at)->format('Y-m-d H:i') : '—',
                    'finished_at'    => $x->finished_at ? Carbon::parse($x->finished_at)->format('Y-m-d H:i') : '—',
                    'expected_count' => (int)($x->expected_count ?? 0),
                    'scanned_count'  => (int)($x->scanned_count ?? 0),
                    'matched_count'  => (int)($x->matched_count ?? 0),
                    'mismatch_count' => (int)($x->mismatch_count ?? 0),
                    'missing_count'  => (int)($x->missing_count ?? 0),
                    'extra_count'    => (int)($x->extra_count ?? 0),
                    'compliance'     => $compliance,
                ];
            });

            $headers = [
                '#',
                trans('assets.col_audit_code'),
                trans('assets.col_title'),
                trans('assets.col_status'),
                trans('assets.col_started_at'),
                trans('assets.col_finished_at'),
                trans('assets.col_expected_count'),
                trans('assets.col_scanned_count'),
                trans('assets.col_matched_count'),
                trans('assets.col_mismatch_count'),
                trans('assets.col_missing_count'),
                trans('assets.col_extra_count'),
                trans('assets.col_compliance'),
            ];
        } elseif ($tab === 'unreviewed') {
            $q = $this->unreviewedBaseQuery($r, '');
            $rows = $q->orderBy('ai.code')->get()->map(function($x){
                return [
                    'asset_code' => $x->code,
                    'asset_name' => $x->name,
                    'category'   => $x->category_name,
                    'location'   => $x->location_name,
                    'status'     => $this->assetStatusLabel($x->status),
                    'nbv'        => (float)$x->nbv,
                ];
            });

            $headers = [
                '#',
                trans('assets.col_asset_code'),
                trans('assets.col_asset_name'),
                trans('assets.col_category'),
                trans('assets.col_location'),
                trans('assets.col_status'),
                trans('assets.col_nbv'),
            ];
        } else {
            $q = $this->linesBaseQuery($r, '');
            $rows = $q->orderBy('a.code','asc')->get()->map(function($x){
                return [
                    'audit_code'  => $x->audit_code,
                    'asset_code'  => $x->asset_code_used,
                    'asset_name'  => $x->name,
                    'category'    => $x->category_name,
                    'exp_loc'     => $x->el_name ?: '—',
                    'found_loc'   => $x->fl_name ?: '—',
                    'result'      => $this->auditResultLabel($x->result),
                    'scan_count'  => (int)($x->scan_count ?? 0),
                    'last_scan'   => $x->last_scanned_at ? Carbon::parse($x->last_scanned_at)->format('Y-m-d H:i') : '—',
                    'user'        => $x->scanned_by ?: '—',
                    'nbv'         => (float)$x->nbv_at_audit,
                    'notes'       => $x->notes ?: '—',
                ];
            });

            $headers = [
                '#',
                trans('assets.col_audit_code'),
                trans('assets.col_asset_code'),
                trans('assets.col_asset_name'),
                trans('assets.col_category'),
                trans('assets.col_exp_location'),
                trans('assets.col_found_location'),
                trans('assets.col_result'),
                trans('assets.col_scan_count'),
                trans('assets.col_last_scanned_at'),
                trans('assets.col_user'),
                trans('assets.col_nbv_at_audit'),
                trans('assets.col_notes'),
            ];
        }

        $meta = $this->buildMeta($r, $rows, $tab);
        return [$rows, $meta, $headers];
    }

    /** ============== KPIs ============== */
    protected function computeKpis(Request $r): array
    {
        $base = $this->linesBaseQuery($r, '');

        $all = (clone $base)->get(['result', DB::raw('1 as one'),
            DB::raw('COALESCE(nbv_at_audit,0) as nbv') // from select alias
        ]);

        $total = $all->count();
        $matched = $all->where('result','matched')->count();
        $missing = $all->where('result','missing')->count();
        $lostValue = round($all->where('result','missing')->sum('nbv'), 2);

        // آخر جرد
        $lastAudit = DB::table('asset_audits')->whereNull('deleted_at')->whereNotNull('finished_at')
            ->orderByDesc('finished_at')->first();
        $lastId = $lastAudit->id ?? null;

        $unreviewedCount = 0;
        if ($lastId) {
            $sub = DB::table('asset_audit_lines')->whereNull('deleted_at')->where('audit_id',$lastId)->groupBy('asset_id')->pluck('asset_id');
            $unreviewedCount = DB::table('asset_items')->whereNull('deleted_at')
                ->whereNotIn('id', $sub)->count();
        }

        $compliance = $total ? round($matched * 100.0 / $total, 2) : 0.0;

        return [
            'compliance_pct' => $compliance,
            'missing_count'  => $missing,
            'missing_value'  => $lostValue,
            'unreviewed'     => $unreviewedCount,
        ];
    }

    /** ============== Labels ============== */
    private function auditStatusLabel(?string $s): string
    {
        $s = $s ?: '';
        $map = [
            'draft'     => trans('assets.audit_s_draft'),
            'running'   => trans('assets.audit_s_running'),
            'finished'  => trans('assets.audit_s_finished'),
            'cancelled' => trans('assets.audit_s_cancelled'),
        ];
        return $map[$s] ?? $s;
    }

    private function auditResultLabel(?string $r): string
    {
        $r = $r ?: '';
        $map = [
            'matched'           => trans('assets.audit_r_matched'),
            'missing'           => trans('assets.audit_r_missing'),
            'extra'             => trans('assets.audit_r_extra'),
            'location_mismatch' => trans('assets.audit_r_location_mismatch'),
            'store_mismatch'    => trans('assets.audit_r_store_mismatch'),
            'status_mismatch'   => trans('assets.audit_r_status_mismatch'),
        ];
        return $map[$r] ?? $r;
    }

    private function assetStatusLabel(?string $s): string
    {
        $s = $s ?: '';
        $map = [
            'in_service' => trans('assets.status_in_service'),
            'maintenance'=> trans('assets.status_maintenance'),
            'retired'    => trans('assets.status_retired'),
            'scrapped'   => trans('assets.status_scrapped'),
        ];
        return $map[$s] ?? $s;
    }

    private function arr($val): array
    {
        if (is_null($val) || $val==='') return [];
        if (is_array($val)) return array_filter($val, fn($x)=>$x!=='' && $x!==null);
        return array_filter(explode(',', $val), fn($x)=>$x!=='');
    }
}
