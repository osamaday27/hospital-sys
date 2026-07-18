<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\models\assets\assetitem;
use App\models\assets\assetaudit as Audit;
use App\models\assets\assetauditline as Line;
use App\models\assets\assetlocation;
use App\models\assets\assetbrand;
use App\models\assets\assetmodel;
use App\Models\supply_chain\storemaindata as StoreMainData;

class auditscontroller extends Controller
{
    /** صفحة الجرد */
    public function index()
    {
        // مواقع
        $locations = assetlocation::select('id', 'name_ar', 'name_en')
            ->where('is_active', 1)->orderBy('name_ar')->get()
            ->map(fn($l) => ['id' => $l->id, 'name' => $l->name_ar ?? $l->name_en ?? (string)$l->id])
            ->values()->all();

        // مخازن
        $stores = StoreMainData::select('id', 'name_ar', 'name_en', 'manual_code')
            ->orderBy('name_ar')->get()
            ->map(function ($s) {
                $name = $s->name_ar ?? $s->name_en ?? '';
                return ['id' => $s->id, 'name' => ($s->manual_code ? $s->manual_code . ' — ' : '') . $name ?: (string)$s->id];
            })->values()->all();

        // حالات
        $statusOptions = [
            'in_service'  => trans('assets.status_in_service'),
            'maintenance' => trans('assets.status_maintenance'),
            'retired'     => trans('assets.status_retired'),
            'scrapped'    => trans('assets.status_scrapped'),
        ];

        // ماركات/موديلات (للفلاتر)
        $brands = assetbrand::select('id', 'name')
            ->where('is_active', 1)->orderBy('name')
            ->get()->map(fn($b) => ['id' => $b->id, 'name' => $b->name ?: (string)$b->id])->values()->all();

        $models = assetmodel::select('id', 'name')
            ->where('is_active', 1)->orderBy('name')
            ->get()->map(fn($m) => ['id' => $m->id, 'name' => $m->name ?: (string)$m->id])->values()->all();

        return view('assets.programs.audits.index', compact('locations', 'stores', 'statusOptions', 'brands', 'models'))
            ->with(['perPageLines' => 50]);
    }

    /** قائمة الجردات */
    public function ajaxAudits(Request $r)
    {
        $q = Audit::query()->orderByDesc('id');

        if ($status = $r->get('status')) $q->where('status', $status);
        if ($from   = $r->get('from'))   $q->whereDate('created_at', '>=', $from);
        if ($to     = $r->get('to'))     $q->whereDate('created_at', '<=', $to);

        $perPage = (int)$r->get('per_page', 15);
        $rows = $q->paginate($perPage);

        $data = $rows->getCollection()->map(function (Audit $a) {
            return [
                'id'           => $a->id,
                'code'         => $a->code,
                'title'        => $a->title,
                'status'       => $a->status,
                'status_label' => $a->status_label,
                'created_at'   => $a->created_at?->toDateTimeString(),
                'expected'     => $a->expected_count,
                'scanned'      => $a->scanned_count,
                'matched'      => $a->matched_count,
                'mismatch'     => $a->mismatch_count,
                'missing'      => $a->missing_count,
                'extra'        => $a->extra_count,
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page'    => $rows->lastPage(),
                'total'        => $rows->total(),
                'per_page'     => $rows->perPage(),
            ]
        ]);
    }

    /** بدء جرد جديد + توليد البنود المتوقعة */
    public function start(Request $r)
    {
        $data = $r->validate([
            'title'             => 'nullable|string|max:190',
            'scope_location_id' => 'nullable|integer',
            'scope_store_id'    => 'nullable|integer',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $audit = null;

        DB::transaction(function () use ($data, &$audit) {
            $code = $this->nextCodeSafe(); // كود آمن

            $audit = Audit::create([
                'code'   => $code,
                'title'  => $data['title'] ?? null,
                'status' => 'in_progress',
                'notes'  => $data['notes'] ?? null,
                'scope_location_id' => $data['scope_location_id'] ?? null,
                'scope_store_id'    => $data['scope_store_id'] ?? null,
                'created_by' => Auth::id(),
                'started_by' => Auth::id(),
                'started_at' => now(),
            ]);

            $assets = assetitem::query()
                ->when($audit->scope_location_id, fn($q, $v) => $q->where('location_id', $v))
                ->when($audit->scope_store_id,    fn($q, $v) => $q->where('store_id', $v))
                ->where('is_active', 1)
                ->get(['id', 'code', 'location_id', 'store_id', 'status', 'qty']);

            $audit->expected_count = $assets->count();
            $audit->save();

            foreach ($assets as $a) {
                Line::create([
                    'audit_id' => $audit->id,
                    'asset_id' => $a->id,
                    'asset_code' => $a->code,
                    'expected_location_id' => $a->location_id,
                    'expected_store_id'    => $a->store_id,
                    'expected_status'      => $a->status,
                    'result' => 'missing',
                ]);
            }

            $audit->recomputeTotals();
        });

        return back()->with('success', trans('assets.audit.started_ok'));
    }

    /** مسح أصل (يدعم سياق المسح + كمية) */
    public function scan(Request $r)
    {
        $r->validate([
            'audit_id'         => 'required|integer|exists:asset_audits,id',
            'code'             => 'required|string|max:190',
            'ctx_location_id'  => 'nullable|integer',
            'ctx_store_id'     => 'nullable|integer',
            'ctx_status'       => 'nullable|in:in_service,maintenance,retired,scrapped',
            'qty'              => 'nullable|integer|min:1',
        ]);

        $audit = Audit::findOrFail($r->audit_id);
        if ($audit->status !== 'in_progress') {
            return response()->json(['ok' => false, 'message' => trans('assets.audit.not_in_progress')], 422);
        }

        $code  = trim($r->code);
        $asset = assetitem::where('code', $code)->first();
        if (!$asset && ctype_digit($code)) $asset = assetitem::find((int)$code);

        $qtyAdd = (int)($r->qty ?? 1);

        if (!$asset) {
            $line = Line::create([
                'audit_id'        => $audit->id,
                'asset_id'        => null,
                'asset_code'      => $code,
                'scan_count'      => $qtyAdd,
                'last_scanned_at' => now(),
                'result'          => 'extra',
                'scanned_by'      => Auth::id(),
            ]);
            $audit->recomputeTotals();
            return response()->json(['ok' => true, 'line_id' => $line->id, 'extra' => true]);
        }

        $line = Line::where('audit_id', $audit->id)->where('asset_id', $asset->id)->first();
        if (!$line) {
            $line = Line::create([
                'audit_id'   => $audit->id,
                'asset_id'   => $asset->id,
                'asset_code' => $asset->code,
                'expected_location_id' => null,
                'expected_store_id'    => null,
                'expected_status'      => null,
                'result' => 'extra',
            ]);
        }

        $line->found_location_id = $r->get('ctx_location_id') ?: $asset->location_id;
        $line->found_store_id    = $r->get('ctx_store_id')    ?: $asset->store_id;
        $line->found_status      = $r->get('ctx_status')      ?: $asset->status;
        $line->scan_count        = (int)($line->scan_count ?? 0) + $qtyAdd;
        $line->last_scanned_at   = now();
        $line->scanned_by        = Auth::id();
        $line->save();

        $line->recomputeResult();
        $audit->recomputeTotals();

        return response()->json(['ok' => true, 'line_id' => $line->id, 'result' => $line->result]);
    }

    /** حفظ بيانات أصل مفقود كمسودة على سطر الجرد (JSON) */
    public function saveDraftAsset(Request $r, $auditId, $lineId)
    {
        $audit = Audit::findOrFail($auditId);
        if ($audit->status !== 'in_progress') {
            return response()->json(['ok' => false, 'message' => trans('assets.audit.not_in_progress')], 422);
        }

        $data = $r->validate([
            'code'        => 'required|string|max:190',
            'name_ar'     => 'nullable|string|max:190',
            'name_en'     => 'nullable|string|max:190',
            'brand_id'    => 'nullable|integer|exists:asset_brands,id',
            'model_id'    => 'nullable|integer|exists:asset_models,id',
            'location_id' => 'nullable|integer',
            'store_id'    => 'nullable|integer',
            'status'      => 'required|in:in_service,maintenance,retired,scrapped',
            'qty'         => 'nullable|integer|min:1',
        ]);

        $ln = Line::where('audit_id', $audit->id)->where('id', $lineId)->firstOrFail();

        // خزّن JSON في الحقل draft_asset_data (أضفته بالمايجريشن)
        $ln->draft_asset_data = $data; // لو عامل cast بالـ Model راح يخزن Array->JSON تلقائيًا
        $ln->save();

        return response()->json(['ok' => true]);
    }

    /** بنود الجرد مع فلاتر + Pagination */
    public function ajaxLines(Request $r, $auditId)
    {
        $audit = Audit::findOrFail($auditId);

        $q = Line::with('asset')->where('audit_id', $audit->id);

        // نتيجة
        if ($res = $r->get('result')) $q->where('result', $res);

        // فلاتر المخزن/الموقع/الحالة (expected/found/asset)
        if ($store = $r->get('store_id')) {
            $q->where(function ($x) use ($store) {
                $x->where('expected_store_id', $store)
                    ->orWhere('found_store_id', $store)
                    ->orWhereHas('asset', fn($a) => $a->where('store_id', $store));
            });
        }
        if ($loc = $r->get('location_id')) {
            $q->where(function ($x) use ($loc) {
                $x->where('expected_location_id', $loc)
                    ->orWhere('found_location_id', $loc)
                    ->orWhereHas('asset', fn($a) => $a->where('location_id', $loc));
            });
        }
        if ($status = $r->get('status')) {
            $q->where(function ($x) use ($status) {
                $x->where('expected_status', $status)
                    ->orWhere('found_status', $status)
                    ->orWhereHas('asset', fn($a) => $a->where('status', $status));
            });
        }

        // فلاتر الماركة/الموديل + البحث
        if ($brand = $r->get('brand_id')) $q->whereHas('asset', fn($a) => $a->where('brand_id', $brand));
        if ($model = $r->get('model_id')) $q->whereHas('asset', fn($a) => $a->where('model_id', $model));

        if ($term = trim((string)$r->get('q', ''))) {
            $q->where(function ($x) use ($term) {
                $x->where('asset_code', 'like', "%{$term}%")
                    ->orWhereHas('asset', function ($a) use ($term) {
                        $a->where('name_ar', 'like', "%{$term}%")
                            ->orWhere('name_en', 'like', "%{$term}%")
                            ->orWhere('code', 'like', "%{$term}%");
                    });
            });
        }

        $perPage = (int)$r->get('per_page', 50);
        $rows = $q->orderBy('id')->paginate($perPage);
        $coll = $rows->getCollection();

        $locIds = $coll->pluck('expected_location_id')->merge($coll->pluck('found_location_id'))->filter()->unique()->values();
        $storeIds = $coll->pluck('expected_store_id')->merge($coll->pluck('found_store_id'))->filter()->unique()->values();

        $locMap = assetlocation::whereIn('id', $locIds)->get()->keyBy('id')
            ->map(fn($l) => $l->name_ar ?? $l->name_en ?? (string)$l->id)->all();

        $storeMap = StoreMainData::whereIn('id', $storeIds)->get()->keyBy('id')
            ->map(function ($s) {
                $name = $s->name_ar ?? $s->name_en ?? '';
                return ($s->manual_code ? $s->manual_code . ' — ' : '') . $name ?: (string)$s->id;
            })->all();

        $statusLabel = fn($s) => $s ? trans('assets.status_' . $s) : null;

        $data = $coll->map(function (Line $ln) use ($locMap, $storeMap, $statusLabel) {
            $expQty   = $ln->asset?->qty ?? 1;
            $foundQty = (int)($ln->scan_count ?? 0);
            return [
                'id'              => $ln->id,
                'asset_id'        => $ln->asset_id,
                'asset_code'      => $ln->asset_code,
                'asset_name'      => $ln->asset?->name_ar ?: $ln->asset?->name_en,

                'exp_loc_id'      => $ln->expected_location_id,
                'found_loc_id'    => $ln->found_location_id,
                'exp_loc'         => $ln->expected_location_id ? ($locMap[$ln->expected_location_id] ?? (string)$ln->expected_location_id) : '-',
                'found_loc'       => $ln->found_location_id    ? ($locMap[$ln->found_location_id] ?? (string)$ln->found_location_id)       : '-',

                'exp_store_id'    => $ln->expected_store_id,
                'found_store_id'  => $ln->found_store_id,
                'exp_store'       => $ln->expected_store_id ? ($storeMap[$ln->expected_store_id] ?? (string)$ln->expected_store_id) : '-',
                'found_store'     => $ln->found_store_id    ? ($storeMap[$ln->found_store_id] ?? (string)$ln->found_store_id)       : '-',

                'exp_status_key'  => $ln->expected_status,
                'found_status_key' => $ln->found_status,
                'exp_status'      => $statusLabel($ln->expected_status) ?? '-',
                'found_status'    => $statusLabel($ln->found_status)   ?? '-',

                'exp_qty'         => $expQty,
                'found_qty'       => $foundQty,

                'scan_count'      => (int)$ln->scan_count,
                'result'          => $ln->result,
                'result_label'    => trans('assets.audit.result_' . $ln->result),
                'apply_change'    => (bool)$ln->apply_change,
                'last_scanned_at' => $ln->last_scanned_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'audit' => [
                'id'           => $audit->id,
                'code'         => $audit->code,
                'status'       => $audit->status,
                'status_label' => $audit->status_label,
                'expected'     => $audit->expected_count,
                'scanned'      => $audit->scanned_count,
                'matched'      => $audit->matched_count,
                'mismatch'     => $audit->mismatch_count,
                'missing'      => $audit->missing_count,
                'extra'        => $audit->extra_count,
            ],
            'data' => $data,
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page'    => $rows->lastPage(),
                'total'        => $rows->total(),
                'per_page'     => $rows->perPage(),
            ]
        ]);
    }

    /** تحديث قيم سطر واحد */
    public function updateLine(Request $r, $auditId, $lineId)
    {
        $audit = Audit::findOrFail($auditId);
        if ($audit->status !== 'in_progress') {
            return response()->json(['ok' => false, 'message' => trans('assets.audit.not_in_progress')], 422);
        }

        $data = $r->validate([
            'found_location_id' => 'nullable|integer',
            'found_store_id'    => 'nullable|integer',
            'found_status'      => 'nullable|in:in_service,maintenance,retired,scrapped',
            'found_qty'         => 'nullable|integer|min:0',
        ]);

        $ln = Line::where('audit_id', $audit->id)->where('id', $lineId)->firstOrFail();

        if ($r->has('found_location_id')) $ln->found_location_id = $data['found_location_id'];
        if ($r->has('found_store_id'))    $ln->found_store_id    = $data['found_store_id'];
        if ($r->has('found_status'))      $ln->found_status      = $data['found_status'];
        if ($r->has('found_qty')) {
            $ln->scan_count = (int)$data['found_qty'];
            $ln->last_scanned_at = now();
            $ln->scanned_by = Auth::id();
        }

        $ln->save();
        $ln->recomputeResult();
        $audit->recomputeTotals();

        return response()->json(['ok' => true]);
    }

    /** تحديد/إلغاء "تطبيق التغيير" */
    public function applySelection(Request $r, $auditId)
    {
        $r->validate([
            'line_ids' => 'required|array|min:1',
            'apply'    => 'required|boolean',
        ]);

        $audit = Audit::findOrFail($auditId);
        Line::where('audit_id', $audit->id)->whereIn('id', $r->line_ids)->update(['apply_change' => $r->boolean('apply')]);

        return response()->json(['ok' => true]);
    }

    /** تسوية/إقفال (تشمل إنشاء الأصول المضافة كمسودة) */
    public function reconcile(Request $r, $auditId)
    {
        $audit = Audit::with('lines')->findOrFail($auditId);
        if ($audit->status !== 'in_progress') {
            return back()->with('warning', trans('assets.audit.not_in_progress'));
        }

        DB::transaction(function () use ($audit) {
            foreach ($audit->lines()->where('apply_change', true)->get() as $ln) {

                // لو Extra ولا يوجد asset_id ولكن يوجد مسودة → أنشئ الأصل
                if (!$ln->asset_id && !empty($ln->draft_asset_data)) {
                    $draft = is_array($ln->draft_asset_data)
                        ? $ln->draft_asset_data
                        : (json_decode($ln->draft_asset_data, true) ?: []);

                    // لو الأصل موجود بنفس الكود، استخدمه
                    $asset = null;
                    if (!empty($draft['code'])) {
                        $asset = assetitem::where('code', $draft['code'])->first();
                    }

                    if (!$asset) {
                        $asset = new assetitem();
                        $asset->code        = $draft['code']        ?? $ln->asset_code;
                        $asset->name_ar     = $draft['name_ar']     ?? null;
                        $asset->name_en     = $draft['name_en']     ?? null;
                        $asset->brand_id    = $draft['brand_id']    ?? null;
                        $asset->model_id    = $draft['model_id']    ?? null;
                        $asset->location_id = $draft['location_id'] ?? $ln->found_location_id;
                        $asset->store_id    = $draft['store_id']    ?? $ln->found_store_id;
                        $asset->status      = $draft['status']      ?? $ln->found_status ?? 'in_service';
                        $asset->qty         = $draft['qty']         ?? ($ln->scan_count ?? 1);
                        $asset->is_active   = 1;
                        $asset->save();
                    }

                    $ln->asset_id   = $asset->id;
                    $ln->asset_code = $asset->code;
                    $ln->save();
                }

                // لو لا يزال بلا أصل، تجاهله (لا تنشئ تلقائيًا بدون مسودة)
                if (!$ln->asset_id) continue;

                $asset = assetitem::lockForUpdate()->find($ln->asset_id);
                if (!$asset) continue;

                // تطبيق الموقع/المخزن/الحالة إن وُجدت
                if (!is_null($ln->found_location_id)) $asset->location_id = $ln->found_location_id;
                if (!is_null($ln->found_store_id))    $asset->store_id    = $ln->found_store_id;
                if (!is_null($ln->found_status))      $asset->status      = $ln->found_status;

                // تطبيق الكمية = المسح
                if (!is_null($ln->scan_count)) $asset->qty = (int)$ln->scan_count;

                $asset->save();

                $ln->resolved_by = Auth::id();
                $ln->resolved_at = now();
                $ln->save();
            }

            $audit->status      = 'reconciled';
            $audit->finished_at = now();
            $audit->finished_by = Auth::id();
            $audit->save();
        });

        return back()->with('success', trans('assets.audit.reconciled_ok'));
    }

    /** إلغاء الجرد */
    public function void($auditId)
    {
        $audit = Audit::findOrFail($auditId);
        if ($audit->status === 'reconciled') {
            return back()->with('warning', trans('assets.audit.cannot_void_closed'));
        }
        $audit->status = 'void';
        $audit->save();
        return back()->with('success', trans('assets.audit.void_ok'));
    }

    /** تصدير CSV */
    public function exportCsv($auditId)
    {
        $audit = Audit::with('lines.asset')->findOrFail($auditId);

        $lines = $audit->lines;
        $locIds = $lines->pluck('expected_location_id')->merge($lines->pluck('found_location_id'))->filter()->unique()->values();
        $storeIds = $lines->pluck('expected_store_id')->merge($lines->pluck('found_store_id'))->filter()->unique()->values();

        $locMap = assetlocation::whereIn('id', $locIds)->get()->keyBy('id')
            ->map(fn($l) => $l->name_ar ?? $l->name_en ?? (string)$l->id)->all();

        $storeMap = StoreMainData::whereIn('id', $storeIds)->get()->keyBy('id')
            ->map(function ($s) {
                $name = $s->name_ar ?? $s->name_en ?? '';
                return ($s->manual_code ? $s->manual_code . ' — ' : '') . $name ?: (string)$s->id;
            })->all();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_' . $audit->code . '.csv"',
        ];

        $callback = function () use ($audit, $locMap, $storeMap) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Audit Code',
                'Line ID',
                'Asset ID',
                'Asset Code',
                'Asset Name',
                'Result',
                'Expected Loc',
                'Found Loc',
                'Expected Store',
                'Found Store',
                'Expected Status',
                'Found Status',
                'Expected Qty',
                'Found Qty',
                'Scan Count',
                'Last Scan'
            ]);
            foreach ($audit->lines as $ln) {
                $expQty = $ln->asset?->qty ?? 1;
                $foundQty = (int)($ln->scan_count ?? 0);
                fputcsv($out, [
                    $audit->code,
                    $ln->id,
                    $ln->asset_id,
                    $ln->asset_code,
                    $ln->asset?->name_ar ?: $ln->asset?->name_en,
                    $ln->result,
                    $ln->expected_location_id ? ($locMap[$ln->expected_location_id] ?? $ln->expected_location_id) : '',
                    $ln->found_location_id    ? ($locMap[$ln->found_location_id] ?? $ln->found_location_id)       : '',
                    $ln->expected_store_id    ? ($storeMap[$ln->expected_store_id] ?? $ln->expected_store_id)     : '',
                    $ln->found_store_id       ? ($storeMap[$ln->found_store_id] ?? $ln->found_store_id)           : '',
                    $ln->expected_status ? trans('assets.status_' . $ln->expected_status) : '',
                    $ln->found_status    ? trans('assets.status_' . $ln->found_status)    : '',
                    $expQty,
                    $foundQty,
                    (int)$ln->scan_count,
                    optional($ln->last_scanned_at)->toDateTimeString()
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * يولّد كود جرد:
     * AUD-YYYYMM-0001 (مع تجنّب التصادم)
     */
    protected function nextCode(): string
    {
        $prefix = 'AUD-' . now()->format('Ym') . '-';
        $last = Audit::where('code', 'like', $prefix . '%')->orderByDesc('id')->value('code');
        $num = 1;
        if ($last && preg_match('/-(\d+)\s*$/', $last, $m)) {
            $num = (int)$m[1] + 1;
        }
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    protected function nextCodeSafe(int $tries = 3): string
    {
        for ($i = 0; $i < $tries; $i++) {
            $code = $this->nextCode();
            if (!Audit::where('code', $code)->exists()) {
                return $code;
            }
            $prefix = 'AUD-' . now()->format('Ym') . '-';
            $max = Audit::where('code', 'like', $prefix . '%')
                ->get(['code'])
                ->map(function ($a) {
                    return preg_match('/-(\d+)$/', $a->code, $m) ? (int)$m[1] : 0;
                })->max();
            $code = $prefix . str_pad((string)($max + 1), 4, '0', STR_PAD_LEFT);
            if (!Audit::where('code', $code)->exists()) {
                return $code;
            }
            usleep(50000);
        }
        $prefix = 'AUD-' . now()->format('Ym') . '-';
        return $prefix . str_pad((string)(Audit::max('id') + 1), 4, '0', STR_PAD_LEFT);
    }
    public function print($auditId)
    {
        $audit = Audit::with(['lines.asset'])->findOrFail($auditId);

        $lines = $audit->lines;
        $locIds = $lines->pluck('expected_location_id')->merge($lines->pluck('found_location_id'))->filter()->unique()->values();
        $storeIds = $lines->pluck('expected_store_id')->merge($lines->pluck('found_store_id'))->filter()->unique()->values();

        $locMap = assetlocation::whereIn('id', $locIds)->get()->keyBy('id')
            ->map(fn($l) => $l->name_ar ?? $l->name_en ?? (string)$l->id)->all();

        $storeMap = StoreMainData::whereIn('id', $storeIds)->get()->keyBy('id')
            ->map(function ($s) {
                $name = $s->name_ar ?? $s->name_en ?? '';
                return ($s->manual_code ? $s->manual_code . ' — ' : '') . $name ?: (string)$s->id;
            })->all();

        return view('assets.programs.audits.print', compact('audit', 'locMap', 'storeMap'));
    }
}
