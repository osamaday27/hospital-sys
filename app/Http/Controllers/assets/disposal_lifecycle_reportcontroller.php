<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

// Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class disposal_lifecycle_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $tab    = $r->get('tab', 'requests'); // requests | executions | approvals | by_reason
        $action = $r->get('action');          // print | export_excel | (ajax|html)

        // Print
        if ($action === 'print') {
            [$rows, $meta, $headers] = $this->fetchForTab($r, $tab);
            return view('assets.reports.disposal_lifecycle_report.print', compact('rows','meta','headers','tab'));
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

            if ($tab === 'executions') {
                $q = $this->executionsBaseQuery($r, $search);
                $orderMap = [1=>'request_no',2=>'method',3=>'reason',4=>'executed_at',5=>'assets_count',6=>'nbv_total',7=>'proceeds',8=>'profit'];
            } elseif ($tab === 'approvals') {
                $q = $this->approvalsBaseQuery($r, $search);
                $orderMap = [1=>'request_no',2=>'level',3=>'role_or_committee',4=>'decision',5=>'decided_at'];
            } elseif ($tab === 'by_reason') {
                $q = $this->byReasonBaseQuery($r, $search);
                $orderMap = [1=>'reason',2=>'ops_count',3=>'nbv_total',4=>'proceeds',5=>'profit'];
            } else {
                $q = $this->requestsBaseQuery($r, $search);
                $orderMap = [1=>'request_no',2=>'status',3=>'requested_at',4=>'method',5=>'assets_count',6=>'qty',7=>'nbv_total',8=>'proceeds',9=>'profit'];
            }

            $orderColIdx = (int) data_get($r->all(), 'order.0.column', 0);
            $orderDir    = data_get($r->all(), 'order.0.dir', 'asc');
            if ($orderColIdx > 0 && isset($orderMap[$orderColIdx])) {
                $q->orderBy($orderMap[$orderColIdx], $orderDir);
            }

            $recordsFiltered = (clone $q)->count();
            $rows = $q->skip($start)->take($length)->get();

            $data = $rows->map(function($x) use ($tab){
                if ($tab === 'executions') {
                    return [
                        'request_no'  => $x->request_no,
                        'method'      => $this->methodLabel($x->method),
                        'reason'      => $x->reason ?: '—',
                        'executed_at' => $x->executed_at ? Carbon::parse($x->executed_at)->format('Y-m-d') : '—',
                        'assets_count'=> (int)($x->assets_count ?? 0),
                        'nbv_total'   => number_format((float)($x->nbv_total ?? 0), 2),
                        'proceeds'    => number_format((float)($x->proceeds ?? 0), 2),
                        'profit'      => number_format((float)(($x->proceeds ?? 0) - ($x->nbv_total ?? 0)), 2),
                    ];
                } elseif ($tab === 'approvals') {
                    return [
                        'request_no'  => $x->request_no,
                        'level'       => (int) $x->level,
                        'role'        => $x->role_or_committee ?: '—',
                        'assignee'    => $x->assignee_name ?: '—',
                        'approver'    => $x->approver_name ?: '—',
                        'decision'    => $this->decisionLabel($x->decision),
                        'decided_at'  => $x->decided_at ? Carbon::parse($x->decided_at)->format('Y-m-d H:i') : '—',
                    ];
                } elseif ($tab === 'by_reason') {
                    return [
                        'reason'     => $x->reason ?: '—',
                        'ops_count'  => (int) $x->ops_count,
                        'nbv_total'  => number_format((float)$x->nbv_total, 2),
                        'proceeds'   => number_format((float)$x->proceeds, 2),
                        'profit'     => number_format((float)($x->proceeds - $x->nbv_total), 2),
                    ];
                } else { // requests
                    return [
                        'request_no'  => $x->request_no,
                        'status'      => $this->statusLabel($x->status),
                        'requested_at'=> $x->requested_at ? Carbon::parse($x->requested_at)->format('Y-m-d') : '—',
                        'method'      => $this->methodLabel($x->method),
                        'reason'      => $x->reason ?: '—',
                        'created_by'  => $x->created_by_name ?: '—',
                        'assets_count'=> (int)($x->assets_count ?? 0),
                        'qty'         => (int)($x->qty ?? 0),
                        'nbv_total'   => number_format((float)($x->nbv_total ?? 0), 2),
                        'proceeds'    => number_format((float)($x->proceeds ?? 0), 2),
                        'profit'      => number_format((float)(($x->proceeds ?? 0) - ($x->nbv_total ?? 0)), 2),
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
        return view('assets.reports.disposal_lifecycle_report.index', compact('filters'));
    }

    /* ================= Base Queries ================= */

    protected function requestsBaseQuery(Request $r, string $search)
    {
        // Subquery: تجميع بنود الطلب
        $itemsAgg = DB::table('disposal_request_items as dri')
            ->selectRaw("
                dri.request_id,
                COUNT(DISTINCT dri.asset_id) as assets_count,
                SUM(COALESCE(dri.qty_approved, COALESCE(dri.qty_requested,1))) as qty,
                SUM(COALESCE(dri.snapshot_nbv,0) * COALESCE(dri.qty_approved, COALESCE(dri.qty_requested,1))) as nbv_total
            ")
            ->whereNull('dri.deleted_at')
            ->groupBy('dri.request_id');

        // Base
        $q = DB::table('disposal_requests as dr')
            ->leftJoinSub($itemsAgg, 'ia', 'ia.request_id', '=', 'dr.id')
            ->leftJoin('disposals as ds','ds.request_id','=','dr.id') // قد لا يوجد تنفيذ
            ->leftJoin('users as u','u.id','=','dr.created_by')
            ->whereNull('dr.deleted_at');

        // فلاتر عامة
        $this->applyCommonFiltersForRequests($q, $r);

        // بحث عام
        if ($search !== '') {
            $q->where(function($w) use ($search){
                $w->where('dr.request_no','like',"%{$search}%")
                  ->orWhere('dr.reason','like',"%{$search}%")
                  ->orWhere('u.name','like',"%{$search}%");
            });
        }

        // Select (بدون GROUP BY)
        $q->selectRaw("
            dr.id,
            dr.request_no,
            dr.status,
            dr.method,
            dr.reason,
            dr.requested_at,
            COALESCE(u.name,'') as created_by_name,
            COALESCE(ia.assets_count,0) as assets_count,
            COALESCE(ia.qty,0) as qty,
            COALESCE(ia.nbv_total,0) as nbv_total,
            COALESCE(ds.net_amount,0) as proceeds
        ");

        return $q;
    }

    protected function executionsBaseQuery(Request $r, string $search)
    {
        // Subquery: تجميع بنود التنفيذ (لو احتجناها)
        $diAgg = DB::table('disposal_items as di')
            ->selectRaw("
                di.disposal_id,
                COUNT(DISTINCT di.asset_id) as assets_count,
                SUM(COALESCE(di.exec_snapshot_nbv,0) * COALESCE(di.qty_executed,1)) as nbv_total_items
            ")
            ->whereNull('di.deleted_at')
            ->groupBy('di.disposal_id');

        $q = DB::table('disposals as ds')
            ->leftJoin('disposal_requests as dr','dr.id','=','ds.request_id')
            ->leftJoinSub($diAgg, 'dia', 'dia.disposal_id', '=', 'ds.id')
            ->whereNull('ds.deleted_at');

        // فلاتر عامة
        $this->applyCommonFiltersForExecutions($q, $r);

        // بحث
        if ($search !== '') {
            $q->where(function($w) use ($search){
                $w->where('dr.request_no','like',"%{$search}%")
                  ->orWhere('dr.reason','like',"%{$search}%");
            });
        }

        // Select
        $q->selectRaw("
            ds.id,
            dr.request_no,
            COALESCE(dr.reason,'') as reason,
            ds.method,
            ds.executed_at,
            COALESCE(dia.assets_count,0) as assets_count,
            COALESCE(NULLIF(ds.exec_snapshot_total_nbv, NULL), COALESCE(dia.nbv_total_items,0)) as nbv_total,
            COALESCE(ds.net_amount,0) as proceeds
        ");

        return $q;
    }

    protected function approvalsBaseQuery(Request $r, string $search)
    {
        $q = DB::table('approvals as ap')
            ->leftJoin('disposal_requests as dr','dr.id','=','ap.request_id')
            ->leftJoin('users as u1','u1.id','=','ap.assignee_user_id')
            ->leftJoin('users as u2','u2.id','=','ap.approver_user_id')
            ->where('ap.context','disposal')
            ->whereNull('ap.deleted_at')
            ->whereNull('dr.deleted_at');

        // فلاتر
        if ($f = $r->get('date_from')) $q->whereDate('ap.decided_at','>=',$f);
        if ($t = $r->get('date_to'))   $q->whereDate('ap.decided_at','<=',$t);

        if ($st = $this->arr($r->get('status')))   $q->whereIn('dr.status', $st);
        if ($rs = $this->arr($r->get('reasons')))  $q->whereIn('dr.reason', $rs);
        if ($mt = $this->arr($r->get('methods')))  $q->whereIn('dr.method', $mt);

        if ($search !== '') {
            $q->where(function($w) use ($search){
                $w->where('dr.request_no','like',"%{$search}%")
                  ->orWhere('ap.role_or_committee','like',"%{$search}%")
                  ->orWhere('u1.name','like',"%{$search}%")
                  ->orWhere('u2.name','like',"%{$search}%");
            });
        }

        $q->selectRaw("
            dr.request_no,
            ap.level,
            ap.role_or_committee,
            COALESCE(u1.name,'') as assignee_name,
            COALESCE(u2.name,'') as approver_name,
            ap.decision,
            ap.decided_at
        ");

        return $q;
    }

    protected function byReasonBaseQuery(Request $r, string $search)
    {
        $q = DB::table('disposals as ds')
            ->leftJoin('disposal_requests as dr','dr.id','=','ds.request_id')
            ->whereNull('ds.deleted_at');

        // فلاتر
        if ($f = $r->get('date_from')) $q->whereDate('ds.executed_at','>=',$f);
        if ($t = $r->get('date_to'))   $q->whereDate('ds.executed_at','<=',$t);
        if ($mt = $this->arr($r->get('methods')))  $q->whereIn('ds.method', $mt);
        if ($rs = $this->arr($r->get('reasons')))  $q->whereIn('dr.reason', $rs);

        // تصفية حسب الفئة/الموقع عبر عناصر التنفيذ
        $this->applyCategoryLocationFilterViaDisposalItems($q, $r);

        // تجميع
        $q->groupBy('dr.reason');

        $q->selectRaw("
            COALESCE(dr.reason,'') as reason,
            COUNT(ds.id) as ops_count,
            SUM(COALESCE(NULLIF(ds.exec_snapshot_total_nbv, NULL), 0)) as nbv_total,
            SUM(COALESCE(ds.net_amount,0)) as proceeds
        ");

        // بحث
        if ($search !== '') {
            $q->havingRaw("reason LIKE ?", ["%{$search}%"]);
        }

        return $q;
    }

    /* ================= Filters helpers ================= */

    protected function applyCommonFiltersForRequests($q, Request $r): void
    {
        if ($f = $r->get('date_from')) $q->whereDate('dr.requested_at','>=',$f);
        if ($t = $r->get('date_to'))   $q->whereDate('dr.requested_at','<=',$t);

        if ($st = $this->arr($r->get('status')))   $q->whereIn('dr.status', $st);
        if ($mt = $this->arr($r->get('methods')))  $q->whereIn('dr.method', $mt);
        if ($rs = $this->arr($r->get('reasons')))  $q->whereIn('dr.reason', $rs);

        // تصفية حسب الفئة/الموقع عبر بنود الطلب
        if ($ids = $this->arr($r->get('category_ids'))) {
            $q->whereExists(function($w) use ($ids){
                $w->from('disposal_request_items as dri')
                  ->join('asset_items as ai','ai.id','=','dri.asset_id')
                  ->whereColumn('dri.request_id','dr.id')
                  ->whereNull('dri.deleted_at')
                  ->whereIn('ai.category_id', $ids)
                  ->select(DB::raw(1));
            });
        }
        if ($ids = $this->arr($r->get('location_ids'))) {
            $q->whereExists(function($w) use ($ids){
                $w->from('disposal_request_items as dri')
                  ->join('asset_items as ai','ai.id','=','dri.asset_id')
                  ->whereColumn('dri.request_id','dr.id')
                  ->whereNull('dri.deleted_at')
                  ->whereIn('ai.location_id', $ids)
                  ->select(DB::raw(1));
            });
        }
    }

    protected function applyCommonFiltersForExecutions($q, Request $r): void
    {
        if ($f = $r->get('date_from')) $q->whereDate('ds.executed_at','>=',$f);
        if ($t = $r->get('date_to'))   $q->whereDate('ds.executed_at','<=',$t);

        if ($mt = $this->arr($r->get('methods')))  $q->whereIn('ds.method', $mt);
        if ($rs = $this->arr($r->get('reasons')))  $q->whereIn('dr.reason', $rs);

        // تصفية حسب الفئة/الموقع عبر عناصر التنفيذ
        $this->applyCategoryLocationFilterViaDisposalItems($q, $r);
    }

    protected function applyCategoryLocationFilterViaDisposalItems($q, Request $r): void
    {
        if ($ids = $this->arr($r->get('category_ids'))) {
            $q->whereExists(function($w) use ($ids){
                $w->from('disposal_items as di')
                  ->join('asset_items as ai','ai.id','=','di.asset_id')
                  ->whereColumn('di.disposal_id','ds.id')
                  ->whereNull('di.deleted_at')
                  ->whereIn('ai.category_id', $ids)
                  ->select(DB::raw(1));
            });
        }
        if ($ids = $this->arr($r->get('location_ids'))) {
            $q->whereExists(function($w) use ($ids){
                $w->from('disposal_items as di')
                  ->join('asset_items as ai','ai.id','=','di.asset_id')
                  ->whereColumn('di.disposal_id','ds.id')
                  ->whereNull('di.deleted_at')
                  ->whereIn('ai.location_id', $ids)
                  ->select(DB::raw(1));
            });
        }
    }

    /* ================= Print/Excel ================= */

    protected function fetchForTab(Request $r, string $tab): array
    {
        if ($tab === 'executions') {
            $q = $this->executionsBaseQuery($r, '');
            $rows = $q->orderBy('ds.executed_at','desc')->get()->map(function($x){
                return [
                    'request_no'  => $x->request_no,
                    'method'      => $this->methodLabel($x->method),
                    'reason'      => $x->reason ?: '—',
                    'executed_at' => $x->executed_at ? Carbon::parse($x->executed_at)->format('Y-m-d') : '—',
                    'assets_count'=> (int)($x->assets_count ?? 0),
                    'nbv_total'   => (float)($x->nbv_total ?? 0),
                    'proceeds'    => (float)($x->proceeds ?? 0),
                    'profit'      => (float)(($x->proceeds ?? 0) - ($x->nbv_total ?? 0)),
                ];
            });
            $headers = [
                '#',
                trans('assets.col_request_no'),
                trans('assets.col_method'),
                trans('assets.col_reason'),
                trans('assets.col_executed_at'),
                trans('assets.col_assets_count'),
                trans('assets.col_nbv_total'),
                trans('assets.col_proceeds'),
                trans('assets.col_profit'),
            ];
        } elseif ($tab === 'approvals') {
            $q = $this->approvalsBaseQuery($r, '');
            $rows = $q->orderBy('ap.decided_at','desc')->get()->map(function($x){
                return [
                    'request_no' => $x->request_no,
                    'level'      => (int) $x->level,
                    'role'       => $x->role_or_committee ?: '—',
                    'assignee'   => $x->assignee_name ?: '—',
                    'approver'   => $x->approver_name ?: '—',
                    'decision'   => $this->decisionLabel($x->decision),
                    'decided_at' => $x->decided_at ? Carbon::parse($x->decided_at)->format('Y-m-d H:i') : '—',
                ];
            });
            $headers = [
                '#',
                trans('assets.col_request_no'),
                trans('assets.col_level'),
                trans('assets.col_role'),
                trans('assets.col_assignee'),
                trans('assets.col_approver'),
                trans('assets.col_decision'),
                trans('assets.col_decided_at'),
            ];
        } elseif ($tab === 'by_reason') {
            $q = $this->byReasonBaseQuery($r, '');
            $rows = $q->orderBy('reason')->get()->map(function($x){
                return [
                    'reason'    => $x->reason ?: '—',
                    'ops_count' => (int) $x->ops_count,
                    'nbv_total' => (float) $x->nbv_total,
                    'proceeds'  => (float) $x->proceeds,
                    'profit'    => (float) ($x->proceeds - $x->nbv_total),
                ];
            });
            $headers = [
                '#',
                trans('assets.col_reason'),
                trans('assets.col_ops_count'),
                trans('assets.col_nbv_total'),
                trans('assets.col_proceeds'),
                trans('assets.col_profit'),
            ];
        } else {
            $q = $this->requestsBaseQuery($r, '');
            $rows = $q->orderBy('dr.requested_at','desc')->get()->map(function($x){
                return [
                    'request_no'  => $x->request_no,
                    'status'      => $this->statusLabel($x->status),
                    'requested_at'=> $x->requested_at ? Carbon::parse($x->requested_at)->format('Y-m-d') : '—',
                    'method'      => $this->methodLabel($x->method),
                    'reason'      => $x->reason ?: '—',
                    'created_by'  => $x->created_by_name ?: '—',
                    'assets_count'=> (int)($x->assets_count ?? 0),
                    'qty'         => (int)($x->qty ?? 0),
                    'nbv_total'   => (float)($x->nbv_total ?? 0),
                    'proceeds'    => (float)($x->proceeds ?? 0),
                    'profit'      => (float)(($x->proceeds ?? 0) - ($x->nbv_total ?? 0)),
                ];
            });
            $headers = [
                '#',
                trans('assets.col_request_no'),
                trans('assets.col_status'),
                trans('assets.col_requested_at'),
                trans('assets.col_method'),
                trans('assets.col_reason'),
                trans('assets.col_created_by'),
                trans('assets.col_assets_count'),
                trans('assets.col_qty'),
                trans('assets.col_nbv_total'),
                trans('assets.col_proceeds'),
                trans('assets.col_profit'),
            ];
        }

        $meta = $this->buildMeta($r, $rows, $tab);
        return [$rows, $meta, $headers];
    }

    protected function exportExcel($rows, array $headers, string $tab)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([$headers], NULL, 'A1');
        $rowIndex = 2; $sr = 1;
        foreach ($rows as $r) {
            $sheet->fromArray([array_merge([$sr], array_values($r))], NULL, 'A'.$rowIndex);
            $rowIndex++; $sr++;
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'DisposalLifecycle_'.$tab.'_'.now()->format('Ymd_His').'.xlsx';
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

    /* ================= Filters data / meta ================= */

    protected function getFiltersData(): array
    {
        $methods = DB::table('disposal_requests')->whereNull('deleted_at')->whereNotNull('method')->distinct()->pluck('method')->values();
        $reasons = DB::table('disposal_requests')->whereNull('deleted_at')->whereNotNull('reason')->distinct()->pluck('reason')->values();

        return [
            'methods'    => $methods,
            'reasons'    => $reasons,
            'categories' => DB::table('asset_categories')->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'locations'  => DB::table('asset_locations')->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'statuses'   => DB::table('disposal_requests')->whereNull('deleted_at')->whereNotNull('status')->distinct()->pluck('status')->values(),
        ];
    }

    protected function buildMeta(Request $r, $rows, string $tab): array
    {
        return [
            'title'        => trans('assets.disposal_lifecycle_report_title'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'date_from'    => $r->get('date_from'),
            'date_to'      => $r->get('date_to'),
            'status'       => $r->get('status'),
            'methods'      => $r->get('methods'),
            'reasons'      => $r->get('reasons'),
            'category_ids' => $r->get('category_ids'),
            'location_ids' => $r->get('location_ids'),
            'count'        => $rows->count(),
            'tab'          => $tab,
        ];
    }

    protected function hasAnyFilter(Request $r): bool
    {
        return (bool) collect([
            'date_from','date_to','status','methods','reasons','category_ids','location_ids','search.value'
        ])->some(fn($k)=> filled($r->get($k)));
    }

    /* ================= Labels ================= */

    private function statusLabel(?string $s): string
    {
        $map = [
            'pending'  => trans('assets.disposal_status_pending'),
            'approved' => trans('assets.disposal_status_approved'),
            'rejected' => trans('assets.disposal_status_rejected'),
            'executed' => trans('assets.disposal_status_executed'),
            'closed'   => trans('assets.disposal_status_closed'),
            'cancelled'   => trans('assets.disposal_status_cancelled'),

        ];
        return $map[$s] ?? ($s ?: '—');
    }

    private function methodLabel(?string $m): string
    {
        $map = [
            'sale'     => trans('assets.disposal_method_sale'),
            'scrap'    => trans('assets.disposal_method_scrap'),
            'donation' => trans('assets.disposal_method_donation'),
        ];
        return $map[$m] ?? ($m ?: '—');
    }

    private function decisionLabel(?string $d): string
    {
        $map = [
            'approved' => trans('assets.approval_approved'),
            'rejected' => trans('assets.approval_rejected'),
            'pending'  => trans('assets.approval_pending'),
        ];
        return $map[$d] ?? ($d ?: '—');
    }

    /* ================= Misc ================= */

    private function arr($val): array
    {
        if (is_null($val) || $val==='') return [];
        if (is_array($val)) return array_filter($val, fn($x)=>$x!=='' && $x!==null);
        return array_filter(explode(',', $val), fn($x)=>$x!=='');
    }
}
