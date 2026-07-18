<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\models\assets\assetitem;
use App\models\assets\assetdepreciationrun as Run;
use App\models\assets\assetdepreciationline as Line;
use Illuminate\Support\Facades\Auth;

class depreciationcontroller extends Controller
{
public function index(Request $r)
{
    if ($r->wantsJson()) {
        $q = Run::query()
            ->withCount('lines')
            ->orderByDesc('period')->orderByDesc('id');

        if ($r->get('status')) $q->where('status', $r->get('status'));
        if ($r->get('from'))  $q->whereDate('period', '>=', $r->get('from'));
        if ($r->get('to'))    $q->whereDate('period', '<=', $r->get('to'));

        $pag = $q->paginate(20);

        // نحول التجميعة قبل الإرجاع لضمان period/status بصيغة العرض
        $pag->getCollection()->transform(function($run){
            return [
                'id'           => $run->id,
                'period'       => optional($run->period)->toDateString(), // ← YYYY-MM-DD
                'status'       => $run->status,
                'status_label' => trans('assets.depreciation.status_'.$run->status), // ← مترجم
                'lines_count'  => $run->lines_count,
                'notes'        => $run->notes,
                'posted_at'    => optional($run->posted_at)->toDateTimeString(),
                'posted_by'    => $run->posted_by,
                'created_at'   => optional($run->created_at)->toDateTimeString(),
            ];
        });

        return response()->json($pag);
    }

    return view('assets.programs.depreciation.index');
}


    public function create()
    {
        $defaultPeriod = Carbon::now()->startOfMonth()->toDateString();
        return view('assets.programs.depreciation.create', compact('defaultPeriod'));
    }

    /**
     * معاينة (لا تحفظ) — تُحسب القيم وتُعاد كـ JSON لعرضها في الجدول قبل الحفظ
     */
    public function preview(Request $r)
    {
        $r->validate([
            'period' => 'required|date',
        ]);

        $period = Carbon::parse($r->period)->startOfMonth();
        $eligibleStatuses = ['in_service','maintenance'];

        // هل هناك تشغيل مُرحّل لهذه الفترة؟
        $postedCount = Run::whereDate('period', $period->toDateString())
                          ->where('status','posted')
                          ->count();

        // إجمالي وحدات UOP المستخدمة حتى تاريخه (تشغيلات مُرحّلة فقط)
        $usedUnitsMap = Line::query()
            ->whereNotNull('uop_units_period')
            ->whereHas('run', fn($q)=>$q->where('status','posted'))
            ->select('asset_id', DB::raw('SUM(uop_units_period) as used'))
            ->groupBy('asset_id')
            ->pluck('used','asset_id');

        // تضمين UOP حتى بدون life/rate
        $assets = assetitem::query()
            ->where('is_active', true)
            ->whereIn('status', $eligibleStatuses)
            ->whereNotNull('dep_method')
            ->where(function($q){
                // SL/DDB: يلزم life أو rate
                $q->where(function($x){
                    $x->whereIn('dep_method', ['SL','DDB'])
                      ->where(function($y){
                          $y->where('useful_life_months','>',0)
                            ->orWhereNotNull('dep_rate');
                      });
                })
                // OR UOP: لا نلزم life/rate لكن نلزم إجمالي وحدات العمر
                ->orWhere(function($x){
                    $x->where('dep_method','UOP')
                      ->whereNotNull('uop_total_estimated_units')
                      ->where('uop_total_estimated_units','>',0);
                });
            })
            ->get();

        // وحدات الفترة القادمة من المعاينة
        $perAssetUopUnits = collect($r->get('uop_units', [])); // [asset_id => units]

        $rows = [];
        foreach ($assets as $a) {
            $qty = max((int)$a->qty, 1);
            $costTotal = (float)$a->cost;
            $residual  = (float)($a->residual_value ?? 0);
            $inService = $a->in_service_date ?: $a->purchase_date;
            if (!$inService) continue;
            if (Carbon::parse($inService)->gt($period->copy()->endOfMonth())) continue; // لم يدخل الخدمة بعد

            $depMethod = $a->dep_method; // SL | DDB | UOP
            $life = (int)$a->useful_life_months ?: null;
            $rate = $a->dep_rate !== null ? (float)$a->dep_rate : null;

            $accumBefore = (float)($a->accumulated_depreciation ?? 0);
            $nbvBefore   = max($costTotal - $accumBefore, 0);

            // الأساس القابل للإهلاك
            $depreciableBaseTotal = max($costTotal - $residual, 0);
            if ($depreciableBaseTotal <= 0) continue;

            // لا نحسب أي شيء لو صافي القيمة الدفترية عند/أقل من القيمة المتبقية
            $maxAllowed = max($nbvBefore - $residual, 0);
            if ($maxAllowed <= 0) continue;

            // حساب مبلغ الشهر
            $amount = 0;
            $uopUsedToDate = null;
            $uopRemaining  = null;
            $uopUnitsThis  = null;

            if ($depMethod === 'SL') {
                if ($life && $life > 0) {
                    $amount = $depreciableBaseTotal / $life;
                } elseif ($rate !== null) {
                    $amount = ($depreciableBaseTotal * ($rate/100)) / 12;
                }
            }
            elseif ($depMethod === 'DDB') {
                $monthlyRate = null;
                if ($rate !== null && $rate > 0) {
                    $monthlyRate = ($rate/100) / 12;
                } elseif ($life && $life > 0) {
                    $monthlyRate = (2 * (1/$life));
                }
                if ($monthlyRate !== null) {
                    $amount = $nbvBefore * $monthlyRate;
                }
            }
            elseif ($depMethod === 'UOP') {
                $estTotal    = (int)($a->uop_total_estimated_units ?? 0);
                $uopUsedToDate = (int)($usedUnitsMap[$a->id] ?? 0);
                $uopRemaining  = max($estTotal - $uopUsedToDate, 0);

                $uopUnitsThis = (int)($perAssetUopUnits->get($a->id) ?? 0);
                // لا نتجاوز المتبقي
                if ($uopUnitsThis > $uopRemaining) $uopUnitsThis = $uopRemaining;

                if ($estTotal > 0 && $uopUnitsThis > 0) {
                    $amount = ($depreciableBaseTotal * $uopUnitsThis) / $estTotal;
                } else {
                    $amount = 0; // نعرض الصف حتى لو 0 لتعبئة الوحدات
                }
            }

            // لا نتجاوز NBV فوق المتبقي
            $amount = max(min($amount, $maxAllowed), 0);

            $accumAfter = $accumBefore + $amount;
            $nbvAfter   = max($costTotal - $accumAfter, 0);

            // لا نحذف صف UOP لو المبلغ = 0؛ نحذف الباقي فقط
            if ($depMethod !== 'UOP' && $amount <= 0) continue;

            $rows[] = [
                'asset_id'            => $a->id,
                'asset_code'          => $a->code,
                'asset_name'          => $a->name_ar ?: $a->name_en,
                'dep_method'          => $depMethod,
                'useful_life_months'  => $life,
                'dep_rate'            => $rate,
                'residual_value'      => round($residual,2),
                'cost_basis'          => round($costTotal,2),
                'qty'                 => $qty,
                'uop_estimated_total' => $a->uop_total_estimated_units,
                'uop_used_to_date'    => $uopUsedToDate,
                'uop_remaining'       => $uopRemaining,
                'uop_units_period'    => $depMethod==='UOP' ? (int)($uopUnitsThis ?? 0) : null,
                'amount'              => round($amount,2),
                'accum_before'        => round($accumBefore,2),
                'accum_after'         => round($accumAfter,2),
                'nbv_before'          => round($nbvBefore,2),
                'nbv_after'           => round($nbvAfter,2),
                'location_id'         => $a->location_id,
                'branch_id'           => optional($a->location)->branch_id ?? null,
                'in_service_date'     => $inService ? (string)$inService : null,
            ];
        }

        return response()->json([
            'period'      => $period->toDateString(),
            'rows'        => array_values($rows),
            'count'       => count($rows),
            'total'       => round(array_sum(array_column($rows,'amount')),2),
            'has_posted'  => $postedCount > 0,
            'posted_count'=> $postedCount,
        ]);
    }

    /**
     * حفظ تشغيل Draft + بنوده (من معاينة العميل)
     */
    public function store(Request $r)
    {
        $r->validate([
            'period' => 'required|date',
            'lines'  => 'required|array|min:1',
        ]);

        $period = Carbon::parse($r->period)->startOfMonth();

        DB::transaction(function() use ($r, $period) {
            $run = Run::create([
                'period'     => $period->toDateString(),
                'status'     => 'draft',
                'notes'      => $r->get('notes'),
                'created_by' => Auth::id(),
            ]);

            foreach ($r->lines as $row) {
                Line::create([
                    'run_id'             => $run->id,
                    'asset_id'           => $row['asset_id'],
                    'dep_method'         => $row['dep_method'] ?? null,
                    'useful_life_months' => $row['useful_life_months'] ?? null,
                    'dep_rate'           => $row['dep_rate'] ?? null,
                    'residual_value'     => $row['residual_value'] ?? 0,
                    'cost_basis'         => $row['cost_basis'] ?? 0,
                    'qty'                => $row['qty'] ?? 1,
                    'uop_estimated_total'=> $row['uop_estimated_total'] ?? null,
                    'uop_units_period'   => $row['uop_units_period'] ?? null,
                    'amount'             => $row['amount'] ?? 0,
                    'accum_before'       => $row['accum_before'] ?? 0,
                    'accum_after'        => $row['accum_after'] ?? 0,
                    'nbv_before'         => $row['nbv_before'] ?? 0,
                    'nbv_after'          => $row['nbv_after'] ?? 0,
                    'location_id'        => $row['location_id'] ?? null,
                    'branch_id'          => $row['branch_id'] ?? null,
                    'in_service_date'    => $row['in_service_date'] ?? null,
                    'created_by'         => Auth::id(),
                ]);
            }
        });

        return back()->with('success', trans('assets.depreciation.saved_draft'));
    }

    public function show($id)
    {
        $run = Run::with('lines.asset')->findOrFail($id);
        return view('assets.programs.depreciation.show', compact('run'));
    }

    /**
     * ترحيل — يحدث الأصول ويثبت مبالغ الإهلاك
     */
    public function post($id)
    {
        $run = Run::with('lines')->findOrFail($id);
        if ($run->status === 'posted') {
            return back()->with('warning', trans('assets.depreciation.already_posted'));
        }

        // منع وجود تشغيلين posted لنفس الفترة قبل محاولة الحفظ (لتفادي خطأ UNIQUE)
        $existsOtherPosted = Run::where('period', $run->period)
                                ->where('status','posted')
                                ->where('id','<>',$run->id)
                                ->exists();
        if ($existsOtherPosted) {
            return back()->with('warning', trans('assets.depreciation.period_already_posted'));
        }

        DB::transaction(function() use ($run) {
            $periodYm = Carbon::parse($run->period)->format('Y-m');

            foreach ($run->lines as $line) {
                /** @var assetitem $asset */
                $asset = assetitem::lockForUpdate()->find($line->asset_id);
                if (!$asset) continue;

                // تأكد أن الأصل ما زال مؤهلاً
                if (!$asset->is_active || in_array($asset->status, ['retired','scrapped'])) {
                    continue;
                }

                // عدم تكرار نفس الشهر للأصل
                if ($asset->last_depreciation_date) {
                    $lastYm = Carbon::parse($asset->last_depreciation_date)->format('Y-m');
                    if ($lastYm === $periodYm) {
                        continue;
                    }
                }

                // قيود حماية لحظة الترحيل (حتى لو المعاينة قديمة)
                $cost   = (float)($asset->cost ?? 0);
                $resid  = (float)($asset->residual_value ?? 0);
                $accumB = round((float)($asset->accumulated_depreciation ?? 0), 2);
                $nbvB   = round(max($cost - $accumB, 0), 2);
                $maxAllowed = max($nbvB - $resid, 0);

                $amount = min(max((float)$line->amount, 0), $maxAllowed);
                $accumA = round($accumB + $amount, 2);
                $nbvA   = round(max($cost - $accumA, 0), 2);

                // حدّث السطر ليطابق الواقع
                $line->amount       = $amount;
                $line->accum_before = $accumB;
                $line->accum_after  = $accumA;
                $line->nbv_before   = $nbvB;
                $line->nbv_after    = $nbvA;
                $line->save();

                // ثم حدّث الأصل
                $asset->accumulated_depreciation = $accumA;
                $asset->net_book_value           = $nbvA;
                $asset->last_depreciation_date   = $run->period;
                $asset->save();
            }

            $run->status    = 'posted';
            $run->posted_by = auth()->id();
            $run->posted_at = now();
            $run->save();
        });

        return back()->with('success', trans('assets.depreciation.posted_ok'));
    }

    /**
     * إلغاء الترحيل — يرجع القيم كما كانت
     */
    public function unpost($id)
    {
        $run = Run::with('lines')->findOrFail($id);
        if ($run->status !== 'posted') {
            return back()->with('warning', trans('assets.depreciation.not_posted'));
        }

        DB::transaction(function() use ($run) {
            foreach ($run->lines as $line) {
                $asset = assetitem::lockForUpdate()->find($line->asset_id);
                if (!$asset) continue;

                $asset->accumulated_depreciation = round(max(($asset->accumulated_depreciation ?? 0) - $line->amount, 0), 2);
                $asset->net_book_value = round(max(($asset->cost ?? 0) - $asset->accumulated_depreciation, 0), 2);

                // لو تاريخ آخر إهلاك يساوي فترة هذا التشغيل، نلغي
                if ($asset->last_depreciation_date && Carbon::parse($asset->last_depreciation_date)->eq(Carbon::parse($run->period))) {
                    $asset->last_depreciation_date = null;
                }

                $asset->save();
            }

            $run->status    = 'void';
            $run->posted_by = null;
            $run->posted_at = null;
            $run->save();
        });

        return back()->with('success', trans('assets.depreciation.unposted_ok'));
    }

    /**
     * تصدير CSV بسيط
     */
    public function exportCsv($id)
    {
        $run = Run::with('lines.asset')->findOrFail($id);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="depreciation_'.$run->period->format('Y_m').'.csv"',
        ];

        $callback = function() use ($run) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Asset ID','Code','Name','Method','Life(M)','Rate','Residual','Cost','Qty','UOP Est','UOP Used','UOP Remain','UOP Units','Amount','Accum Before','Accum After','NBV Before','NBV After','Location','Branch']);
            foreach ($run->lines as $ln) {
                $a = $ln->asset;
                fputcsv($out, [
                    $ln->asset_id,
                    $a?->code, $a?->name_ar ?: $a?->name_en,
                    $ln->dep_method, $ln->useful_life_months, $ln->dep_rate,
                    $ln->residual_value, $ln->cost_basis, $ln->qty,
                    $ln->uop_estimated_total, // est
                    null,                     // used (غير محفوظ تاريخيًا في السطر)
                    null,                     // remain
                    $ln->uop_units_period,
                    $ln->amount, $ln->accum_before, $ln->accum_after, $ln->nbv_before, $ln->nbv_after,
                    $ln->location_id, $ln->branch_id
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * صفحة للطباعة (PDF لاحقاً)
     */
    public function exportPrint($id)
    {
        $run = Run::with('lines.asset')->findOrFail($id);
        return view('assets.programs.depreciation.print', compact('run'));
    }
}
