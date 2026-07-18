<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\models\assets\assetitem;
use App\models\assets\assetmaintenance;
use App\models\assets\assetmaintenancetask;
use App\Models\Suppliers\suppliersdata as SuppliersData;

class MaintenanceController extends Controller
{
    public function index(Request $r)
    {
        // إذا كان طلب صفحة HTML
        if (!($r->wantsJson() || $r->ajax())) {
            return view('assets.programs.maintenance.index');
        }

        // JSON لـ DataTables مع فلاتر
        $q = assetmaintenance::query()->with(['asset','vendor']);

        // فلاتر متقدمة
        if ($assetId = $r->get('asset_id')) {
            $q->where('asset_id', (int)$assetId);
        }
        if ($status = $r->get('status')) {
            $q->where('status', $status);
        }
        if ($from = $r->get('date_from')) {
            $q->whereDate('reported_at', '>=', $from);
        }
        if ($to = $r->get('date_to')) {
            $q->whereDate('reported_at', '<=', $to);
        }

        // بحث عام
        $search = $r->input('search.value') ?? $r->input('q');
        if ($search) {
            $q->where(function($qq) use ($search) {
                $qq->where('code','like',"%$search%")
                   ->orWhere('summary','like',"%$search%")
                   ->orWhereHas('asset', fn($a)=>$a->where('code','like',"%$search%")
                                                  ->orWhere('name_ar','like',"%$search%")
                                                  ->orWhere('name_en','like',"%$search%"));
            });
        }

        $total = $q->count();
        $start = (int)$r->get('start', 0);
        $len   = (int)$r->get('length', 10);

        $rows  = $q->latest('id')->skip($start)->take($len)->get();

        $data = $rows->map(function($row){
            return [
                'code'        => $row->code,
                'asset'       => $row->asset?->code.' - '.($row->asset?->name_ar ?? $row->asset?->name_en),
                'type'        => $this->renderBadge('types',      $row->maintenance_type, self::TYPE_COLORS),
                'status'      => $this->renderBadge('statuses',   $row->status,           self::STATUS_COLORS),
                'priority'    => $this->renderBadge('priorities', $row->priority,         self::PRIORITY_COLORS),
                'reported_at' => optional($row->reported_at)->format('Y-m-d'),
                'cost_total'  => number_format((float)$row->cost_total, 2),
                'actions'     => view('assets.programs.maintenance._actions', compact('row'))->render(),
            ];
        });

        return response()->json([
            'draw'            => (int)$r->get('draw', 1),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ]);
    }

    public function create()
    {
        $assets  = assetitem::latest('id')->limit(50)->get();
        $vendors = SuppliersData::latest('id')->limit(50)->get();
        return view('assets.programs.maintenance.create', compact('assets','vendors'));
    }

    public function store(Request $r)
    {
        $validated = $r->validate([
            'asset_id'         => ['required','integer','exists:asset_items,id'],
            'maintenance_type' => ['required','in:preventive,corrective,inspection'],
            'priority'         => ['required','in:low,medium,high'],
            'reported_at'      => ['required','date'],
            'started_at'       => ['nullable','date'],
            'finished_at'      => ['nullable','date','after_or_equal:started_at'],
            'summary'          => ['required','string','max:255'],
            'description'      => ['nullable','string'],
            'vendor_id'        => ['nullable','integer','exists:suppliers_data,id'],
            'next_due_date'    => ['nullable','date'],

            'attachment'                => ['nullable','file','max:10240'],

            'tasks'                     => ['nullable','array'],
            'tasks.*.description'       => ['required_with:tasks','string','max:255'],
            'tasks.*.performed_at'      => ['nullable','date'],
            'tasks.*.hours'             => ['nullable','numeric','min:0'],
            'tasks.*.cost'              => ['nullable','numeric','min:0'],
            'tasks.*.technician_name'   => ['nullable','string','max:255'],
            'tasks.*.notes'             => ['nullable','string'],
        ]);

        return DB::transaction(function() use ($validated, $r) {
            $code  = $this->nextCode();
            $tasks = collect($validated['tasks'] ?? []);
            $total = (float) $tasks->sum(fn($t)=> (float)($t['cost'] ?? 0));

            $attachmentPath = null;
            if ($r->hasFile('attachment')) {
                $attachmentPath = $r->file('attachment')->store('attachments/maintenance', 'public');
            }

            $m = assetmaintenance::create([
                'code'              => $code,
                'asset_id'          => $validated['asset_id'],
                'maintenance_type'  => $validated['maintenance_type'],
                'status'            => 'open',
                'priority'          => $validated['priority'],
                'reported_at'       => $validated['reported_at'],
                'started_at'        => $validated['started_at']   ?? null,
                'finished_at'       => $validated['finished_at']  ?? null,
                'summary'           => $validated['summary'],
                'description'       => $validated['description']  ?? null,
                'vendor_id'         => $validated['vendor_id']    ?? null,
                'cost_total'        => $total,
                'next_due_date'     => $validated['next_due_date']?? null,
                'user_id'           => auth()->id(),
                'attachment_path'   => $attachmentPath,
            ]);

            foreach ($tasks as $t) {
                $m->tasks()->create([
                    'description'     => $t['description'],
                    'performed_at'    => $t['performed_at']  ?? null,
                    'hours'           => $t['hours']         ?? 0,
                    'cost'            => $t['cost']          ?? 0,
                    'technician_name' => $t['technician_name'] ?? null,
                    'notes'           => $t['notes']         ?? null,
                ]);
            }

            // مزامنة حالة الأصل: open => maintenance
            $this->syncAssetStatus($m->asset, 'open');

            return redirect()
                ->route('assets.programs.maintenance.index')
                ->with('success', __('assets.maintenance.flash.created'));
        });
    }

    public function show($id)
    {
        $row = assetmaintenance::with(['asset','vendor','tasks'])->findOrFail($id);
        return view('assets.programs.maintenance.show', compact('row'));
    }

    public function edit($id)
    {
        $row = assetmaintenance::with(['asset','vendor','tasks'])->findOrFail($id);
        $assets = assetitem::latest('id')->limit(50)->get();
        $vendors = SuppliersData::latest('id')->limit(50)->get();
        return view('assets.programs.maintenance.edit', compact('row','assets','vendors'));
    }

    public function update(Request $r, $id)
    {
        $row = assetmaintenance::with('asset')->findOrFail($id);

        $validated = $r->validate([
            'asset_id'         => ['required','integer','exists:asset_items,id'],
            'maintenance_type' => ['required','in:preventive,corrective,inspection'],
            'status'           => ['required','in:open,in_progress,done,cancelled'],
            'priority'         => ['required','in:low,medium,high'],
            'reported_at'      => ['required','date'],
            'started_at'       => ['nullable','date'],
            'finished_at'      => ['nullable','date','after_or_equal:started_at'],
            'summary'          => ['required','string','max:255'],
            'description'      => ['nullable','string'],
            'vendor_id'        => ['nullable','integer','exists:suppliers_data,id'],
            'next_due_date'    => ['nullable','date'],

            'attachment'                => ['nullable','file','max:10240'],

            'tasks'                     => ['nullable','array'],
            'tasks.*.id'                => ['nullable','integer','exists:asset_maintenance_tasks,id'],
            'tasks.*.description'       => ['required_with:tasks','string','max:255'],
            'tasks.*.performed_at'      => ['nullable','date'],
            'tasks.*.hours'             => ['nullable','numeric','min:0'],
            'tasks.*.cost'              => ['nullable','numeric','min:0'],
            'tasks.*.technician_name'   => ['nullable','string','max:255'],
            'tasks.*.notes'             => ['nullable','string'],
        ]);

        return DB::transaction(function() use ($row, $validated, $r) {

            // قيد الإغلاق: لا تُغلق "done" إلا بوجود بنود وتكلفة > 0
            $inputTasks = collect($validated['tasks'] ?? []);
            $sumCost    = (float) $inputTasks->sum(fn($t)=> (float)($t['cost'] ?? 0));
            $willBeDone = $validated['status'] === 'done';

            if ($willBeDone) {
                // إن لم تُرسل بنود في الطلب، نعتمد بنود DB
                if ($inputTasks->isEmpty()) {
                    $sumCost = (float) $row->tasks()->sum('cost');
                    $hasAny  = $row->tasks()->exists();
                } else {
                    $hasAny  = $inputTasks->count() > 0;
                }

                if (!$hasAny || $sumCost <= 0) {
                    return back()
                        ->withErrors(['status' => __('assets.maintenance.validation.cannot_close_done_without_cost')])
                        ->withInput();
                }
            }

            // مزامنة البنود: حذف ثم إنشاء (بسيطة)
            $row->tasks()->delete();
            foreach ($inputTasks as $t) {
                $row->tasks()->create([
                    'description'     => $t['description'],
                    'performed_at'    => $t['performed_at']  ?? null,
                    'hours'           => $t['hours']         ?? 0,
                    'cost'            => $t['cost']          ?? 0,
                    'technician_name' => $t['technician_name'] ?? null,
                    'notes'           => $t['notes']         ?? null,
                ]);
            }

            // مرفق
            $attachmentPath = $row->attachment_path;
            if ($r->hasFile('attachment')) {
                if ($attachmentPath) Storage::disk('public')->delete($attachmentPath);
                $attachmentPath = $r->file('attachment')->store('attachments/maintenance', 'public');
            }

            // تحديث الرأس
            $row->update([
                'asset_id'          => $validated['asset_id'],
                'maintenance_type'  => $validated['maintenance_type'],
                'status'            => $validated['status'],
                'priority'          => $validated['priority'],
                'reported_at'       => $validated['reported_at'],
                'started_at'        => $validated['started_at']   ?? null,
                'finished_at'       => $validated['finished_at']  ?? null,
                'summary'           => $validated['summary'],
                'description'       => $validated['description']  ?? null,
                'vendor_id'         => $validated['vendor_id']    ?? null,
                'cost_total'        => $sumCost,
                'next_due_date'     => $validated['next_due_date']?? null,
                'attachment_path'   => $attachmentPath,
            ]);

            // مزامنة حالة الأصل مع حالة الصيانة
            $this->syncAssetStatus($row->asset, $row->status);

            return redirect()
                ->route('assets.programs.maintenance.index')
                ->with('success', __('assets.maintenance.flash.updated'));
        });
    }

    public function destroy($id)
    {
        $row = assetmaintenance::findOrFail($id);
        $assetId = $row->asset_id;
        $row->delete();

        // إعادة حساب حالة الأصل
        $this->recalcAssetStatus($assetId);

        return back()->with('success', __('assets.maintenance.flash.deleted'));
    }

    // == ألوان Bootstrap للمؤشرات ==
    private const TYPE_COLORS = [
        'corrective' => 'danger',   // تصحيحية
        'preventive' => 'success',  // وقائية
        'inspection' => 'info',     // تفقد/فحص
    ];

    private const STATUS_COLORS = [
        'open'        => 'secondary', // مفتوحة
        'in_progress' => 'warning',   // جارية
        'done'        => 'success',   // منتهية
        'cancelled'   => 'dark',      // ملغاة
    ];

    private const PRIORITY_COLORS = [
        'low'    => 'secondary', // منخفضة
        'medium' => 'info',      // متوسطة
        'high'   => 'danger',    // مرتفعة
    ];

    private function renderBadge(string $group, string $value, array $colors): string
    {
        $color = $colors[$value] ?? 'secondary';
        // مثال: assets.maintenance.types.corrective
        $label = __("assets.maintenance.{$group}.{$value}");
        return '<span class="badge bg-' . $color . '">' . e($label) . '</span>';
    }

    // ====== AJAX helpers for Select2 ======
    public function ajaxAssets(Request $r)
    {
        $term = $r->get('term');
        $q = assetitem::query();
        if ($term) {
            $q->where('code','like',"%$term%")
              ->orWhere('name_ar','like',"%$term%")
              ->orWhere('name_en','like',"%$term%");
        }
        return [
            'results' => $q->limit(50)->get()->map(function($a){
                return ['id'=>$a->id,'text'=>$a->code.' - '.($a->name_ar ?? $a->name_en)];
            })
        ];
    }

public function ajaxVendors(Request $r)
{
    $term = trim((string) $r->get('term'));

    $q = SuppliersData::query();

    if ($term !== '') {
        $q->where(function ($qq) use ($term) {
            $qq->where('code', 'like', "%{$term}%")
               ->orWhere('name_ar', 'like', "%{$term}%")
               ->orWhere('name_en', 'like', "%{$term}%");
        });
    }

    $vendors = $q->orderByDesc('id')->limit(50)->get();

    return [
        'results' => $vendors->map(function ($v) {
            // اسم معرّب حسب لغة الواجهة
            $name = app()->getLocale() === 'ar'
                ? ($v->name_ar ?: $v->name_en)
                : ($v->name_en ?: $v->name_ar);

            // النص المعروض في Select2
            $text = trim(($v->code ? "{$v->code} - " : '') . ($name ?: "#{$v->id}"));

            return ['id' => $v->id, 'text' => $text];
        }),
    ];
}


    // ====== Helpers ======
protected function nextCode(): string
{
    $year   = date('Y');
    $prefix = "MT-{$year}-";

    // أعلى رقم تسلسلي للسنة الحالية
    $maxSeq = DB::table('asset_maintenances')
        ->where('code', 'like', $prefix.'%')
        ->selectRaw("COALESCE(MAX(CAST(SUBSTRING_INDEX(code, '-', -1) AS UNSIGNED)), 0) as max_seq")
        ->value('max_seq');

    $seq = (int)$maxSeq + 1;

    // تأكيد عدم الاصطدام حتى لو فيه فجوة/سجل قديم بنفس الكود
    do {
        $code = $prefix . str_pad((string)$seq, 5, '0', STR_PAD_LEFT);
        $exists = DB::table('asset_maintenances')->where('code', $code)->exists();
        if (!$exists) break;
        $seq++;
    } while (true);

    return $code;
}


    protected function syncAssetStatus(?assetitem $asset, string $maintenanceStatus): void
    {
        if (!$asset) return;
        // لا نغيّر retired/scrapped
        if (in_array($asset->status, ['retired','scrapped'])) return;

        if (in_array($maintenanceStatus, ['open','in_progress'])) {
            $asset->status = 'maintenance';
        } else { // done, cancelled
            // إن لم توجد صيانة أخرى مفتوحة/جارية يَرجع للخدمة
            $existsOpen = assetmaintenance::where('asset_id', $asset->id)
                ->whereIn('status', ['open','in_progress'])
                ->exists();
            $asset->status = $existsOpen ? 'maintenance' : 'in_service';
        }
        $asset->save();
    }

    protected function recalcAssetStatus(int $assetId): void
    {
        $asset = assetitem::find($assetId);
        if (!$asset) return;
        if (in_array($asset->status, ['retired','scrapped'])) return;

        $existsOpen = assetmaintenance::where('asset_id', $assetId)
            ->whereIn('status', ['open','in_progress'])
            ->exists();

        $asset->status = $existsOpen ? 'maintenance' : 'in_service';
        $asset->save();
    }
}
