<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

// Models
use App\models\assets\assetitem;
use App\models\assets\assetlocation;
use App\models\assets\assetmaintenance;
use App\models\assets\assetmaintenancetask;
use App\Models\Suppliers\suppliersdata as Supplier;

// Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class maintenance_reportcontroller extends Controller
{
    /** ================= Entry ================= */
    public function index(Request $r)
    {
        $tab    = $r->get('tab', 'orders'); // orders | tasks
        $action = $r->get('action');        // print | export_excel | kpis | (ajax|html)

        // KPIs bar
        if ($action === 'kpis') {
            return response()->json($this->computeKpis($r));
        }

        // Print
        if ($action === 'print') {
            [$rows, $meta, $headers] = $this->fetchForTab($r, $tab);
            return view('assets.reports.maintenance_report.print', compact('rows','meta','headers','tab'));
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

            if ($tab === 'tasks') {
                $q = $this->tasksBaseQuery($r, $search);
                $orderMap = [
                    1=>'ai.code', 2=>'name', 3=>'m.code', 4=>'m.maintenance_type',
                    5=>'t.performed_at', 6=>'t.technician_name', 7=>'t.hours', 8=>'t.cost'
                ];
            } else { // orders
                $q = $this->ordersBaseQuery($r, $search);
                // ملاحظة: بعض الأعمدة محسوبة في PHP، نتجاهل ترتيبها
                $orderMap = [
                    1=>'ai.code', 2=>'name', 3=>'m.maintenance_type', 4=>'m.code',
                    5=>'m.reported_at', 6=>'m.started_at', 7=>'m.finished_at',
                    8=>'m.status', 9=>'m.priority', 12=>'pm.last_pm_date', 14=>'m.cost_total'
                ];
            }

            $orderColIdx = (int) data_get($r->all(), 'order.0.column', 0);
            $orderDir    = data_get($r->all(), 'order.0.dir', 'asc');
            if ($orderColIdx > 0 && isset($orderMap[$orderColIdx])) {
                $q->orderBy($orderMap[$orderColIdx], $orderDir);
            }

            $recordsFiltered = (clone $q)->count();
            $rows = $q->skip($start)->take($length)->get();

            $data = $rows->map(function($x) use ($tab){
                if ($tab === 'tasks') {
                    return [
                        'asset_code'   => $x->asset_code,
                        'asset_name'   => $x->name,
                        'wo_code'      => $x->wo_code,
                        'type'         => $this->maintenanceTypeLabel($x->maintenance_type),
                        'performed_at' => $x->performed_at ? Carbon::parse($x->performed_at)->format('Y-m-d') : '—',
                        'technician'   => $x->technician_name ?: '—',
                        'hours'        => $x->hours !== null ? number_format((float)$x->hours, 2) : '0.00',
                        'cost'         => $x->cost !== null ? number_format((float)$x->cost, 2) : '0.00',
                        'task_desc'    => $x->task_desc ?: '—',
                    ];
                } else {
                    $durationHours = $this->hoursDiff($x->started_at, $x->finished_at);
                    $mttrHours     = (in_array(strtolower((string)$x->maintenance_type), ['corrective','cm']) && $durationHours !== null)
                                        ? $durationHours : null;
                    $overdue = $this->isOverdue($x->maintenance_type, $x->status, $x->next_due_date, $x->finished_at);

                    $tasksCost = (float)($x->tasks_cost_sum ?? 0);
                    $totalCost = (float)($x->cost_total ?? 0) + $tasksCost;

                    return [
                        'asset_code'  => $x->asset_code,
                        'asset_name'  => $x->name,
                        'type'        => $this->maintenanceTypeLabel($x->maintenance_type),
                        'wo_code'     => $x->wo_code,
                        'reported_at' => $x->reported_at ? Carbon::parse($x->reported_at)->format('Y-m-d') : '—',
                        'started_at'  => $x->started_at ? Carbon::parse($x->started_at)->format('Y-m-d') : '—',
                        'finished_at' => $x->finished_at ? Carbon::parse($x->finished_at)->format('Y-m-d') : '—',
                        'status'      => $this->maintenanceStatusLabel($x->status),
                        'priority'    => $this->priorityLabel($x->priority),
                        'duration'    => $durationHours !== null ? number_format($durationHours, 2) : '—',
                        'mttr'        => $mttrHours !== null ? number_format($mttrHours, 2) : '—',
                        'last_pm'     => $x->last_pm_date ? Carbon::parse($x->last_pm_date)->format('Y-m-d') : '—',
                        'overdue'     => $overdue ? trans('assets.yes') : trans('assets.no'),
                        'technicians' => $x->techs ?: '—',
                        'cost_total'  => number_format($totalCost, 2),
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
        return view('assets.reports.maintenance_report.index', compact('filters'));
    }

    /** ================= Base queries ================= */
    protected function ordersBaseQuery(Request $r, string $search)
    {
        // تجميعة مهام لكل أمر عمل
        $tasksAgg = DB::table('asset_maintenance_tasks as t')
            ->select([
                't.maintenance_id',
                DB::raw('SUM(t.hours) as hours_sum'),
                DB::raw('SUM(t.cost)  as cost_sum'),
                DB::raw("GROUP_CONCAT(DISTINCT t.technician_name ORDER BY t.technician_name SEPARATOR ', ') as techs"),
            ])
            ->whereNull('t.deleted_at')
            ->groupBy('t.maintenance_id');

        // آخر PM مكتمل لكل أصل
        $pmLast = DB::table('asset_maintenances as mm')
            ->select([
                'mm.asset_id',
                DB::raw('MAX(mm.finished_at) as last_pm_date')
            ])
            ->whereNull('mm.deleted_at')
            ->whereIn(DB::raw('LOWER(mm.maintenance_type)'), ['preventive','pm'])
            ->groupBy('mm.asset_id');

        $q = DB::table('asset_maintenances as m')
            ->leftJoin('asset_items as ai','ai.id','=','m.asset_id')
            ->leftJoin('asset_locations as al','al.id','=','ai.location_id')
            ->leftJoinSub($tasksAgg, 'ta', 'ta.maintenance_id','=','m.id')
            ->leftJoinSub($pmLast,  'pm', 'pm.asset_id','=','ai.id')
            ->whereNull('m.deleted_at');

        $q->select([
            'm.id',
            'ai.code as asset_code',
            DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
            'm.maintenance_type','m.code as wo_code',
            'm.reported_at','m.started_at','m.finished_at','m.next_due_date',
            'm.status','m.priority',
            'm.cost_total',
            'ta.hours_sum as tasks_hours_sum',
            'ta.cost_sum  as tasks_cost_sum',
            'ta.techs',
            'pm.last_pm_date',
        ]);

        // فلاتر
        $this->applyCommonFilters($q, $r, 'm');
        // فلتر الفنيين
        if ($techs = $this->arr($r->get('technicians'))) {
            $q->whereExists(function($qq) use ($techs){
                $qq->select(DB::raw(1))
                   ->from('asset_maintenance_tasks as tt')
                   ->whereColumn('tt.maintenance_id','m.id')
                   ->whereNull('tt.deleted_at')
                   ->whereIn('tt.technician_name', $techs);
            });
        }

        // بحث عام
        if ($search !== '') {
            $q->where(function($x) use ($search){
                $x->where('m.code','like',"%{$search}%")
                  ->orWhere('ai.code','like',"%{$search}%")
                  ->orWhere('ai.name_ar','like',"%{$search}%")
                  ->orWhere('ai.name_en','like',"%{$search}%")
                  ->orWhere(DB::raw("COALESCE(al.name_ar, al.name_en)"),'like',"%{$search}%");
            });
        }

        return $q;
    }

    protected function tasksBaseQuery(Request $r, string $search)
    {
        $q = DB::table('asset_maintenance_tasks as t')
            ->leftJoin('asset_maintenances as m','m.id','=','t.maintenance_id')
            ->leftJoin('asset_items as ai','ai.id','=','m.asset_id')
            ->leftJoin('asset_locations as al','al.id','=','ai.location_id')
            ->whereNull('t.deleted_at')
            ->whereNull('m.deleted_at');

        $q->select([
            't.id','t.description as task_desc','t.performed_at','t.hours','t.cost','t.technician_name',
            'm.code as wo_code','m.maintenance_type','m.status','m.priority',
            'ai.code as asset_code',
            DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
            DB::raw("COALESCE(al.name_ar, al.name_en) as location_name"),
        ]);

        // نطاق التاريخ لمهام: على performed_at
        if ($f = $r->get('date_from')) $q->whereDate('t.performed_at','>=',$f);
        if ($t = $r->get('date_to'))   $q->whereDate('t.performed_at','<=',$t);

        // بقية الفلاتر المشتركة على جدول m/ai
        $this->applyCommonFilters($q, $r, 'm');

        if ($techs = $this->arr($r->get('technicians'))) $q->whereIn('t.technician_name', $techs);

        if ($search !== '') {
            $q->where(function($x) use ($search){
                $x->where('t.description','like',"%{$search}%")
                  ->orWhere('t.technician_name','like',"%{$search}%")
                  ->orWhere('m.code','like',"%{$search}%")
                  ->orWhere('ai.code','like',"%{$search}%")
                  ->orWhere('ai.name_ar','like',"%{$search}%")
                  ->orWhere('ai.name_en','like',"%{$search}%");
            });
        }

        return $q;
    }

    /** فلاتر مشتركة */
    protected function applyCommonFilters($q, Request $r, string $mAlias = 'm'): void
    {
        // date_on: reported_at | started_at | finished_at | next_due_date (للأوامر)
        $dateOn = $r->get('date_on', 'reported_at');
        $dateCol = in_array($dateOn, ['reported_at','started_at','finished_at','next_due_date']) ? $dateOn : 'reported_at';
        if ($f = $r->get('date_from')) $q->whereDate("$mAlias.$dateCol", '>=', $f);
        if ($t = $r->get('date_to'))   $q->whereDate("$mAlias.$dateCol", '<=', $t);

        if ($types = $this->arr($r->get('types')))       $q->whereIn(DB::raw("LOWER($mAlias.maintenance_type)"), array_map('strtolower',$types));
        if ($prior = $this->arr($r->get('priorities')))  $q->whereIn($mAlias.'.priority', $prior);
        if ($locs = $this->arr($r->get('location_ids'))) $q->whereIn('ai.location_id', $locs);
        if ($assets = $this->arr($r->get('asset_ids')))  $q->whereIn("$mAlias.asset_id", $assets);
        if ($vendors = $this->arr($r->get('vendor_ids'))) $q->whereIn("$mAlias.vendor_id", $vendors);
    }

    /** ================= Print/Excel helpers ================= */
    protected function fetchForTab(Request $r, string $tab): array
    {
        if ($tab === 'tasks') {
            $q = $this->tasksBaseQuery($r, '');
            $rows = $q->orderBy('t.performed_at','asc')->get()->map(function($x){
                return [
                    'asset_code'   => $x->asset_code,
                    'asset_name'   => $x->name,
                    'wo_code'      => $x->wo_code,
                    'type'         => $this->maintenanceTypeLabel($x->maintenance_type),
                    'performed_at' => $x->performed_at ? Carbon::parse($x->performed_at)->format('Y-m-d') : '—',
                    'technician'   => $x->technician_name ?: '—',
                    'hours'        => (float) ($x->hours ?? 0),
                    'cost'         => (float) ($x->cost ?? 0),
                    'task_desc'    => $x->task_desc ?: '—',
                ];
            });
            $headers = [
                '#',
                trans('assets.col_asset_code'),
                trans('assets.col_asset_name'),
                trans('assets.col_wo_code'),
                trans('assets.col_maint_type'),
                trans('assets.col_performed_at'),
                trans('assets.col_technician'),
                trans('assets.col_hours'),
                trans('assets.col_cost'),
                trans('assets.col_task_desc'),
            ];
        } else {
            $q = $this->ordersBaseQuery($r, '');
            $rows = $q->orderBy('m.reported_at','asc')->get()->map(function($x){
                $durationHours = $this->hoursDiff($x->started_at, $x->finished_at);
                $mttrHours     = (in_array(strtolower((string)$x->maintenance_type), ['corrective','cm']) && $durationHours !== null)
                                    ? $durationHours : null;
                $overdue = $this->isOverdue($x->maintenance_type, $x->status, $x->next_due_date, $x->finished_at);
                $tasksCost = (float)($x->tasks_cost_sum ?? 0);
                $totalCost = (float)($x->cost_total ?? 0) + $tasksCost;

                return [
                    'asset_code'  => $x->asset_code,
                    'asset_name'  => $x->name,
                    'type'        => $this->maintenanceTypeLabel($x->maintenance_type),
                    'wo_code'     => $x->wo_code,
                    'reported_at' => $x->reported_at ? Carbon::parse($x->reported_at)->format('Y-m-d') : '—',
                    'started_at'  => $x->started_at ? Carbon::parse($x->started_at)->format('Y-m-d') : '—',
                    'finished_at' => $x->finished_at ? Carbon::parse($x->finished_at)->format('Y-m-d') : '—',
                    'status'      => $this->maintenanceStatusLabel($x->status),
                    'priority'    => $this->priorityLabel($x->priority),
                    'duration'    => $durationHours !== null ? (float)$durationHours : null,
                    'mttr'        => $mttrHours !== null ? (float)$mttrHours : null,
                    'last_pm'     => $x->last_pm_date ? Carbon::parse($x->last_pm_date)->format('Y-m-d') : '—',
                    'overdue'     => $overdue ? trans('assets.yes') : trans('assets.no'),
                    'technicians' => $x->techs ?: '—',
                    'cost_total'  => (float)$totalCost,
                ];
            });
            $headers = [
                '#',
                trans('assets.col_asset_code'),
                trans('assets.col_asset_name'),
                trans('assets.col_maint_type'),
                trans('assets.col_wo_code'),
                trans('assets.col_reported_at'),
                trans('assets.col_started_at'),
                trans('assets.col_finished_at'),
                trans('assets.col_status'),
                trans('assets.col_priority'),
                trans('assets.col_duration_h'),
                trans('assets.col_mttr_h'),
                trans('assets.col_last_pm'),
                trans('assets.col_overdue'),
                trans('assets.col_technicians'),
                trans('assets.col_total_cost'),
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
        $rowIndex = 2;
        $sr = 1;

        foreach ($rows as $r) {
            $sheet->fromArray([array_merge([$sr], array_values($r))], NULL, 'A'.$rowIndex);
            $rowIndex++; $sr++;
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Maintenance_'.$tab.'_'.now()->format('Ymd_His').'.xlsx';
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

    /** ================= Filters & Meta ================= */
    protected function getFiltersData(): array
    {
        // أنواع الصيانة (ثابتة)
        $types = [
            ['value'=>'preventive', 'label'=>$this->maintenanceTypeLabel('preventive')],
            ['value'=>'corrective', 'label'=>$this->maintenanceTypeLabel('corrective')],
        ];

        // أولويات مميزة من البيانات
        $priorities = assetmaintenance::query()
            ->whereNull('deleted_at')
            ->whereNotNull('priority')
            ->distinct()
            ->orderBy('priority')
            ->pluck('priority')
            ->map(fn($p)=>['value'=>$p,'label'=>$this->priorityLabel($p)])
            ->values();

        // فنيون من المهام
        $techs = assetmaintenancetask::query()
            ->whereNull('deleted_at')
            ->whereNotNull('technician_name')
            ->distinct()
            ->orderBy('technician_name')
            ->limit(5000)
            ->pluck('technician_name')
            ->map(fn($t)=>['value'=>$t,'label'=>$t])
            ->values();

        return [
            'locations'  => assetlocation::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'assets'     => assetitem::select('id','code','name_ar','name_en')->orderBy('name_ar')->limit(5000)->get(),
            'vendors'    => Supplier::select('id','name_ar','name_en')->orderBy('name_ar')->limit(5000)->get(),
            'types'      => $types,
            'priorities' => $priorities,
            'technicians'=> $techs,
        ];
    }

    protected function buildMeta(Request $r, $rows, string $tab): array
    {
        return [
            'title'         => trans('assets.maintenance_report_title'),
            'generated_at'  => Carbon::now()->format('Y-m-d H:i'),
            'date_on'       => $r->get('date_on', 'reported_at'),
            'date_from'     => $r->get('date_from'),
            'date_to'       => $r->get('date_to'),
            'types'         => $r->get('types'),
            'location_ids'  => $r->get('location_ids'),
            'asset_ids'     => $r->get('asset_ids'),
            'vendor_ids'    => $r->get('vendor_ids'),
            'priorities'    => $r->get('priorities'),
            'technicians'   => $r->get('technicians'),
            'count'         => $rows->count(),
            'tab'           => $tab,
        ];
    }

    protected function hasAnyFilter(Request $r): bool
    {
        return (bool) collect([
            'date_from','date_to','types','location_ids','asset_ids','vendor_ids','priorities','technicians','search.value'
        ])->some(fn($k)=> filled($r->get($k)));
    }

    /** ================= KPIs ================= */
    protected function computeKpis(Request $r): array
    {
        // أساس التاريخ
        $dateOn = $r->get('date_on', 'reported_at');
        $dateCol = in_array($dateOn, ['reported_at','started_at','finished_at','next_due_date']) ? $dateOn : 'reported_at';

        $base = DB::table('asset_maintenances as m')
            ->leftJoin('asset_items as ai','ai.id','=','m.asset_id')
            ->whereNull('m.deleted_at');

        // فلاتر مشتركة
        $this->applyCommonFilters($base, $r, 'm');

        // PM compliance: (مكتملة/إجمالي الوقائي)
        $pmBase = (clone $base)->whereIn(DB::raw('LOWER(m.maintenance_type)'), ['preventive','pm']);
        $pmTotal = (clone $pmBase)->count();
        $pmClosed = (clone $pmBase)->whereNotNull('m.finished_at')->count();
        $pmCompliance = $pmTotal > 0 ? round($pmClosed * 100.0 / $pmTotal, 2) : 0.0;

        // MTTR (تصحيحية فقط، أوامر بها started & finished)
        $cm = (clone $base)
            ->whereIn(DB::raw('LOWER(m.maintenance_type)'), ['corrective','cm'])
            ->whereNotNull('m.started_at')
            ->whereNotNull('m.finished_at')
            ->get(['m.started_at','m.finished_at']);
        $mttrHours = 0.0; $cmCount = 0;
        foreach ($cm as $row) {
            $h = $this->hoursDiff($row->started_at, $row->finished_at);
            if ($h !== null) { $mttrHours += $h; $cmCount++; }
        }
        $mttrHoursAvg = $cmCount > 0 ? round($mttrHours / $cmCount, 2) : 0.0;

        // إجمالي ساعات التوقف (لكل الأوامر التي لها started & finished)
        $allDone = (clone $base)
            ->whereNotNull('m.started_at')
            ->whereNotNull('m.finished_at')
            ->get(['m.started_at','m.finished_at']);
        $downTotal = 0.0;
        foreach ($allDone as $row) {
            $h = $this->hoursDiff($row->started_at, $row->finished_at);
            if ($h !== null) $downTotal += $h;
        }
        $downtimeTotal = round($downTotal, 2);

        // أعلى 10 أصول تكلفة
        $tasksCost = DB::table('asset_maintenance_tasks as t')
            ->select('t.maintenance_id', DB::raw('SUM(t.cost) as tcost'))
            ->whereNull('t.deleted_at')
            ->groupBy('t.maintenance_id');

        $topAssets = DB::table('asset_maintenances as m')
            ->leftJoin('asset_items as ai','ai.id','=','m.asset_id')
            ->leftJoinSub($tasksCost, 'tc', 'tc.maintenance_id','=','m.id')
            ->whereNull('m.deleted_at');

        $this->applyCommonFilters($topAssets, $r, 'm');

        $topAssets = $topAssets
            ->groupBy('ai.id','ai.code','ai.name_ar','ai.name_en')
            ->select([
                'ai.id','ai.code',
                DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
                DB::raw("SUM(COALESCE(m.cost_total,0) + COALESCE(tc.tcost,0)) as total_cost"),
                DB::raw("COUNT(m.id) as wo_count")
            ])
            ->orderByDesc('total_cost')
            ->limit(10)
            ->get();

        return [
            'pm_compliance_pct' => $pmCompliance, // %
            'mttr_hours'        => $mttrHoursAvg, // ساعات
            'downtime_hours'    => $downtimeTotal,
            'top_assets'        => $topAssets,
        ];
    }

    /** ================= Labels & helpers ================= */
    private function maintenanceTypeLabel(?string $v): string
    {
        $v = strtolower((string)$v);
        $map = [
            'preventive' => trans('assets.maintenance_type_preventive'),
            'pm'         => trans('assets.maintenance_type_preventive'),
            'corrective' => trans('assets.maintenance_type_corrective'),
            'cm'         => trans('assets.maintenance_type_corrective'),
        ];
        return $map[$v] ?? ($v ?: '—');
    }

    private function maintenanceStatusLabel(?string $v): string
    {
        $v = strtolower((string)$v);
        $map = [
            'open'        => trans('assets.maintenance_status_open'),
            'in_progress' => trans('assets.maintenance_status_in_progress'),
            'closed'      => trans('assets.maintenance_status_closed'),
        ];
        return $map[$v] ?? ($v ?: '—');
    }

    private function priorityLabel(?string $v): string
    {
        $v = strtolower((string)$v);
        $map = [
            'low'    => trans('assets.priority_low'),
            'medium' => trans('assets.priority_medium'),
            'high'   => trans('assets.priority_high'),
            'urgent' => trans('assets.priority_urgent'),
        ];
        return $map[$v] ?? ($v ?: ($v===null?'—':$v));
    }

    private function hoursDiff($start, $end): ?float
    {
        if (!$start || !$end) return null;
        try {
            $s = Carbon::parse($start);
            $e = Carbon::parse($end);
            if ($e->lessThanOrEqualTo($s)) return 0.0;
            return round($e->floatDiffInMinutes($s)/60.0, 2);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function isOverdue($type, $status, $next_due, $finished): bool
    {
        $type = strtolower((string)$type);
        $status = strtolower((string)$status);
        if (!in_array($type, ['preventive','pm'])) return false;
        if (!$next_due) return false;

        $due = Carbon::parse($next_due)->toDateString();
        $today = Carbon::now()->toDateString();

        // لو مغلق بعد موعد الاستحقاق أو لم يُغلق والموعد مضى
        if ($finished) {
            return Carbon::parse($finished)->toDateString() > $due;
        }
        return $today > $due;
    }

    private function arr($val): array
    {
        if (is_null($val) || $val==='') return [];
        if (is_array($val)) return array_filter($val, fn($x)=>$x!=='' && $x!==null);
        return array_filter(explode(',', $val), fn($x)=>$x!=='');
    }
}
