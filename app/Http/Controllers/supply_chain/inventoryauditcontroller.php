<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\supply_chain\InventoryAudit;
use App\Models\supply_chain\InventoryAuditDetail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\web\file_storage;
use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\productstorebalance;
use App\models\supply_chain\productdata;
use App\Models\supply_chain\productunit;
use App\models\hr\Employee;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\models\supply_chain\storemovement;         // موديل الحركات


class inventoryauditcontroller extends Controller
{
    use file_storage;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $q = InventoryAudit::query()
                ->with(['store', 'creator'])
                ->select('inventory_audits.*');

            return DataTables::eloquent($q)
                ->addIndexColumn()
                ->editColumn('audit_date', fn($row) => optional($row->audit_date)->format('Y-m-d'))
                ->addColumn(
                    'store_name',
                    fn($row) =>
                    $row->store->name_ar
                        ?? $row->store->name_en
                        ?? $row->store->name
                        ?? ('#' . $row->store_id)
                )
                ->addColumn('creator_name', fn($row) => $row->creator->name ?? ('User#' . $row->user_id))
                ->addColumn('status_label', function ($row) {
                    $status = $row->status ?: 'default';
                    $color  = __('supply_chain_trans.status_colors.' . $status) ?: 'secondary';
                    $label  = __('supply_chain_trans.' . $status, [], app()->getLocale()) ?: $status;

                    // دعم Bootstrap 4/5
                    return '<span class="badge badge-' . e($color) . ' bg-' . e($color) . ' text-bg-' . e($color) . '">' . e($label) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id;

                    // الحالات المنتهية: settled / reconciled / adjusted / done
                    $isSettled = in_array((string)$row->status, ['settled', 'reconciled', 'adjusted', 'done'], true);

                    // عرض + طباعة متاحين دائمًا
                    $viewBtn  = '<button class="btn btn-sm btn-outline-info view-btn" data-id="' . $id . '" title="' . e(__('supply_chain_trans.view')) . '">
                                <i class="ri-eye-line"></i>
                             </button>';

                    $printBtn = '<a href="' . route('inventory_audits.print', $id) . '" target="_blank" class="btn btn-sm btn-outline-secondary" title="' . e(__('supply_chain_trans.print')) . '">
                                <i class="ri-printer-line"></i>
                             </a>';

                    if ($isSettled) {
                        return '<div class="btn-group" role="group">' . $viewBtn . $printBtn . '</div>';
                    }

                    // في حالة غير مُسوّى: تعديل (يفتح مودال التأكيد)، ضبط/تسوية، حذف
                    $editBtn   = '<a href="#" class="btn btn-sm btn-outline-warning edit-btn" data-id="' . $id . '" title="' . e(__('supply_chain_trans.edit')) . '">
                                <i class="ri-edit-2-line"></i>
                              </a>';


                    // زر التسوية الجديد (يفتح مودال تأكيد التسوية من الواجهة)
                    $settleBtn = '<button class="btn btn-sm btn-outline-success settle-btn" data-id="' . $id . '" title="' . e(__('supply_chain_trans.settle') ?? 'تسوية') . '">
                                <i class="ri-check-double-line"></i>
                              </button>';

                    $delBtn    = '<button class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $id . '" title="' . e(__('supply_chain_trans.delete')) . '">
                                <i class="ri-delete-bin-6-line"></i>
                              </button>';

                    return '<div class="btn-group" role="group">'
                        . $viewBtn . $printBtn . $editBtn . $settleBtn . $delBtn .
                        '</div>';
                })
                ->rawColumns(['status_label', 'action'])
                ->toJson();
        }

        return view('supply_chain.program.inventory_audits.index');
    }


    /** ✅ صفحة الطباعة */
    public function print($id)
    {
        $audit = InventoryAudit::with([
            'store',
            'creator',
            'details' => function ($q) {
                $q->with(['product', 'productUnit.smallUnit', 'productUnit.largeUnit']);
            }
        ])->findOrFail($id);

        // المرفقات المطلقة للعرض/الطباعة
        $relAttachments = array_values((array) ($audit->attachments ?? []));
        $absAttachments = collect($relAttachments)
            ->filter()
            ->map(function ($p) {
                $p = ltrim((string)$p, '/');
                return URL::to('/') . '/' . $p;
            })
            ->values()
            ->all();

        // أسماء أعضاء اللجنة (لو محفوظة IDs في الحقل committee_user_ids)
        $committeeNames = [];
        $committeeIds = array_values(array_filter((array) ($audit->committee_user_ids ?? []), 'is_numeric'));
        if (!empty($committeeIds)) {
            $emps = Employee::whereIn('id', $committeeIds)->get();
            foreach ($emps as $e) {
                $committeeNames[] = $e->full_name
                    ?? $e->first_name_ar
                    ?? $e->first_name_en
                    ?? ('#' . $e->id);
            }
        }

        return view('supply_chain.program.inventory_audits.print', [
            'audit'            => $audit,
            'store'            => $audit->store,
            'details'          => $audit->details,
            'creator'          => $audit->creator,
            'absAttachments'   => $absAttachments,
            'committeeNames'   => $committeeNames,
            'title'            => __('supply_chain_trans.inventory_audit') . ' #' . $audit->code,
        ]);
    }

    /**
     * تجهيز التعديل: تعطيل المخزن الخاص بالمحضر ثم إعادة التوجيه إلى صفحة التعديل.
     */
    public function prepareEdit(Request $request, $id)
    {
        $audit = InventoryAudit::with('store')->findOrFail($id);

        // تعطيل المخزن (إن لم يكن معطلًا)
        if ($audit->store_id) {
            storemaindata::where('id', $audit->store_id)->update(['status' => 'inactive']);
        }

        return response()->json([
            'redirect' => route('inventory_audits.edit', $audit->id),
        ]);
    }


    public function show($id)
    {
        $audit = InventoryAudit::with([
            'store',
            'creator',
            'details' => function ($q) {
                $q->with(['product', 'productUnit.smallUnit', 'productUnit.largeUnit']);
            }
        ])->findOrFail($id);

        // Badge الحالة في الهيدر
        $status = $audit->status ?: 'default';
        $statusColor = __('supply_chain_trans.status_colors.' . $status);
        $statusLabel = __('supply_chain_trans.' . $status, [], app()->getLocale());
        $statusBadge = '<span class="badge badge-' . e($statusColor) . ' bg-' . e($statusColor) . ' text-bg-' . e($statusColor) . '">' . e($statusLabel) . '</span>';

        // أسماء أعضاء اللجنة
        $committeeIds = (array) ($audit->committee_user_ids ?? []);
        $committeeNames = [];
        if (!empty($committeeIds)) {
            $emps = Employee::query()->whereIn('id', $committeeIds)->get();
            foreach ($emps as $emp) {
                $committeeNames[] = $emp->full_name
                    ?? $emp->first_name_ar
                    ?? $emp->first_name_en
                    ?? ('#' . $emp->id);
            }
        }
        $committeeText = !empty($committeeNames) ? implode('، ', $committeeNames) : '-';

        // جلب أسماء الوحدات حسب units_id (بدون عمود name)
        $unitsIds = $audit->details->pluck('units_id')->filter()->unique()->values();
        $unitsMap = collect();
        if ($unitsIds->isNotEmpty()) {
            $rows = DB::table('units')
                ->whereIn('id', $unitsIds)
                ->get(['id', 'name_ar', 'name_en']); // ← أزلنا 'name'
            $unitsMap = $rows->keyBy('id');
        }

        $header = [
            __('supply_chain_trans.code')               => e($audit->code),
            __('supply_chain_trans.store')              => e($audit->store->name_ar ?? $audit->store->name_en ?? $audit->store->name ?? '#' . $audit->store_id),
            __('supply_chain_trans.date')               => optional($audit->audit_date)->format('Y-m-d'),
            __('supply_chain_trans.start_date')         => $audit->start_date ?: '-',
            __('supply_chain_trans.end_date')           => $audit->end_date ?: '-',
            __('supply_chain_trans.status')             => $statusBadge, // badge ملوّن
            __('supply_chain_trans.created_by')         => e($audit->creator->name ?? '-'),
            __('supply_chain_trans.committee_members')  => e($committeeText),
            __('supply_chain_trans.notes')              => e($audit->notes ?: '-'),
        ];

        $items = $audit->details->map(function (InventoryAuditDetail $d) use ($unitsMap) {
            $code   = $d->product->product_code ?? $d->product->system_code ?? ('#' . $d->product_id);
            $pname  = $d->product->name_ar ?? $d->product->name_en ?? null;

            // اسم الوحدة: أولاً من جدول units عبر units_id، وإلا نحاول استنتاجه من علاقات productUnit
            $unitName = '';
            if ($d->units_id && ($u = $unitsMap->get($d->units_id))) {
                $unitName = $u->name_ar ?? $u->name_en ?? '';
            } elseif ($d->productUnit) {
                if ($d->productUnit->smallUnit && $d->productUnit->unit_small_id == $d->units_id) {
                    $unitName = $d->productUnit->smallUnit->name_ar ?? $d->productUnit->smallUnit->name_en ?? '';
                } elseif ($d->productUnit->largeUnit && $d->productUnit->unit_large_id == $d->units_id) {
                    $unitName = $d->productUnit->largeUnit->name_ar ?? $d->productUnit->largeUnit->name_en ?? '';
                } else {
                    // fallback لأصغر وحدة متاحة
                    $unitName = $d->productUnit->smallUnit->name_ar
                        ?? $d->productUnit->smallUnit->name_en
                        ?? ($d->productUnit->largeUnit->name_ar ?? $d->productUnit->largeUnit->name_en ?? '');
                }
            }

            $st = $d->status ?: 'default';
            $color  = __('supply_chain_trans.status_colors.' . $st);
            $label  = __('supply_chain_trans.' . $st);
            $badge  = '<span class="badge badge-' . e($color) . ' bg-' . e($color) . ' text-bg-' . e($color) . '">' . e($label) . '</span>';

            return [
                'product'             => e($pname ?: $code),
                'product_code'        => e($code),
                'unit'                => e($unitName ?: ''), // ← بدون #
                'batch_no'            => e($d->batch_no ?? ''),
                'expiry_date'         => $d->expiry_date ? $d->expiry_date->format('Y-m-d') : '',
                'system_quantity'     => (string) $d->system_quantity,
                'actual_quantity'     => (string) $d->actual_quantity,
                'difference_quantity' => (string) $d->difference_quantity,
                'status_label'        => $badge,
            ];
        })->values();

        // روابط المرفقات مطلقة (بدون /ar/)
        $attachments = collect((array) ($audit->attachments ?? []))
            ->filter()
            ->map(function ($p) {
                $p = ltrim((string)$p, '/');
                return URL::to('/') . '/' . $p;
            })
            ->values()
            ->all();

        return response()->json([
            'header'      => $header,
            'items'       => $items,
            'attachments' => $attachments,
        ]);
    }



    /* ====== بدأ الجرد: تعطيل المخزن مؤقتًا ====== */
    public function start(Request $request)
    {
        $request->validate(['store_id' => 'required|integer|exists:store_main_date,id']);
        storemaindata::where('id', $request->store_id)->update(['status' => 'inactive']);

        return response()->json([
            'redirect' => route('inventory_audits.create', ['store_id' => $request->store_id])
        ]);
    }

    /* ====== زر الرجوع وتفعيل المخزن فورًا ====== */
    public function reactivateStore(Request $request)
    {
        $request->validate(['store_id' => 'required|integer|exists:store_main_date,id']);
        storemaindata::where('id', $request->store_id)->update(['status' => 'active']);
        return response()->json([
            'success'  => true,
            'redirect' => route('inventory_audits.index'),
        ]);
    }

    /* ====== صفحة create ====== */
    public function create(Request $request)
    {
        $storeId = $request->get('store_id');
        abort_if(!$storeId, 404);

        $store = storemaindata::findOrFail($storeId);
        abort_if($store->status !== 'inactive', 403, __('supply_chain_trans.store_must_be_inactive'));

        // توليد كود المحضر
        do {
            $code = 'IA-' . now()->format('Ym') . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (InventoryAudit::where('code', $code)->exists());

        return view('supply_chain.program.inventory_audits.create', compact('store', 'code'));
    }

    /* ====== حفظ ====== */
    public function store(Request $request)
    {
        $rules = [
            'code'       => 'required|string|max:40|unique:inventory_audits,code',
            'store_id'   => 'required|exists:store_main_date,id',
            'audit_date' => 'required|date',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string',
            'committee_user_ids'   => 'array',
            'committee_user_ids.*' => 'integer',
            'attachments.*' => 'file|max:20480',
            'items.*.units_id' => 'required|integer|exists:units,id',
        ];
        Validator::make($request->all(), $rules)->validate();

        // تأكد أن المخزن لا يزال inactive
        $store = storemaindata::findOrFail($request->store_id);
        if ($store->status !== 'inactive') {
            return response()->json(['message' => __('supply_chain_trans.store_must_be_inactive')], 422);
        }

        DB::transaction(function () use ($request) {
            $audit = InventoryAudit::create([
                'code'               => $request->code,
                'store_id'           => $request->store_id,
                'audit_date'         => $request->audit_date,
                'start_date'         => $request->start_date ?: null, // ✅ حوّل الفراغ إلى null
                'end_date'           => $request->end_date   ?: null, // ✅
                'user_id'            => auth()->id(),
                'status'             => 'draft',
                'committee_user_ids' => array_values($request->input('committee_user_ids', [])),
                'attachments'        => [],
                'notes'              => $request->notes,
            ]);

            if ($request->hasFile('attachments')) {
                $paths = $this->file_storage($request->file('attachments'), 'inventory_audits');
                $audit->attachments = $paths;
                $audit->save();
            }

            $items = $request->input('items', []);
            $seen  = [];
            foreach ($items as $it) {
                $pid   = (int)($it['product_id'] ?? 0);
                $batch = trim($it['batch_no'] ?? '');
                $k = $pid . '__' . mb_strtolower($batch);
                if (isset($seen[$k])) continue;
                $seen[$k] = true;

                $status   = in_array(($it['status'] ?? ''), ['surplus', 'shortage', 'match']) ? $it['status'] : 'match';
                $system   = (float)($it['system_quantity'] ?? 0);
                $actual   = (float)($it['actual_quantity'] ?? 0);
                if ($status === 'match') {
                    $actual = $system;
                }
                $difference = $actual - $system;

                $detail = new InventoryAuditDetail([
                    'product_id'          => $pid,
                    'product_unit_id'     => $it['product_unit_id'] ?? null,
                    'units_id'            => $it['units_id'] ?? null,
                    'batch_no'            => $batch,
                    'expiry_date'         => ($it['expiry_date'] ?? null) ?: null,
                    'system_quantity'     => $system,
                    'actual_quantity'     => $actual,
                    'difference_quantity' => $difference,
                    'status'              => $status,
                ]);
                $audit->details()->save($detail);
            }

            if ((int)$request->input('activate_store') === 1) {
                storemaindata::where('id', $request->store_id)->update(['status' => 'active']);
            }
        });

        return response()->json(['success' => true]);
    }


    /* ====== AJAX: مخازن ====== */
    public function ajaxStores(Request $request)
    {
        $q = trim($request->get('q'));
        $rows = storemaindata::query()
            ->when($q, fn($qq) => $qq->where(function ($s) use ($q) {
                $s->where('name_ar', 'like', "%$q%")
                    ->orWhere('name_en', 'like', "%$q%");
            }))
            ->where('status', 'active')
            ->get();

        return $rows->map(fn($r) => ['id' => $r->id, 'text' => $r->name_ar ?? $r->name_en ?? ('#' . $r->id)])->all();
    }

    /* ====== AJAX: موظفون (اللجنة) ====== */
    public function ajaxEmployees(Request $request)
    {
        $q = trim($request->get('q'));
        $rows = Employee::query()
            ->when($q, fn($qq) => $qq->where(function ($s) use ($q) {
                $s->where('full_name', 'like', "%$q%")
                    ->orWhere('first_name_ar', 'like', "%$q%")
                    ->orWhere('first_name_en', 'like', "%$q%");
            }))
            ->get();

        return $rows->map(fn($r) => ['id' => $r->id, 'text' => $r->full_name ?? $r->first_name_ar ?? $r->first_name_en ?? ('#' . $r->id)])->all();
    }

    /* ====== AJAX: منتجات ====== */
    public function ajaxProducts(Request $request)
    {
        $q = trim($request->get('q'));
        $rows = productdata::query()
            ->when($q, fn($qq) => $qq->where(function ($s) use ($q) {
                $s->where('name_ar', 'like', "%$q%")
                    ->orWhere('name_en', 'like', "%$q%")
                    ->orWhere('product_code', 'like', "%$q%")
                    ->orWhere('system_code', 'like', "%$q%");
            }))->get();

        return $rows->map(fn($r) => [
            'id'   => $r->id,
            'text' => trim(($r->product_code ? $r->product_code . ' - ' : '') . ($r->name_ar ?? $r->name_en ?? '')),
        ])->all();
    }

    /* ====== AJAX: مجموعات ====== */
    public function ajaxCategories(Request $request)
    {
        $q = trim($request->get('q'));
        $rows = \App\Models\supply_chain\productcategory::query()
            ->when($q, fn($qq) => $qq->where(function ($s) use ($q) {
                $s->where('name_ar', 'like', "%$q%")
                    ->orWhere('name_en', 'like', "%$q%");
            }))->get();

        return $rows->map(fn($r) => ['id' => $r->id, 'text' => $r->name])->all();
    }

    /* ====== AJAX: منتجات مجموعة ====== */
    public function ajaxProductsByCategory($id)
    {
        $rows = productdata::where('product_category_id', $id)->get();

        return $rows->map(function ($r) {
            return [
                'id'   => $r->id,
                'text' => trim(($r->product_code ? $r->product_code . ' - ' : '') . ($r->name_ar ?? $r->name_en ?? '')),
                'code' => $r->product_code,
            ];
        })->all();
    }

    /* ====== AJAX: وحدات منتج (عامل تحويل من أصغر وحدة) ====== */
    public function ajaxProductUnits(productdata $product)
    {
        $pairs = productunit::with(['smallUnit', 'largeUnit'])
            ->where('product_id', $product->id)
            ->get();

        $edges = [];
        $smallIds = collect();
        $largeIds = collect();
        foreach ($pairs as $p) {
            if ($p->unit_small_id && $p->unit_large_id && $p->conversion_rate > 0) {
                $edges[] = ['from' => (int)$p->unit_small_id, 'to' => (int)$p->unit_large_id, 'rate' => (float)$p->conversion_rate];
                $smallIds->push((int)$p->unit_small_id);
                $largeIds->push((int)$p->unit_large_id);
            }
        }
        $smallIds = $smallIds->unique();
        $largeIds = $largeIds->unique();

        $bases = $smallIds->diff($largeIds);
        $baseId = $bases->first() ?? $smallIds->first() ?? $largeIds->first();

        $factor = [];
        if ($baseId) $factor[$baseId] = 1.0;

        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($edges as $e) {
                if (isset($factor[$e['from']]) && !isset($factor[$e['to']])) {
                    $factor[$e['to']] = $factor[$e['from']] * $e['rate'];
                    $changed = true;
                }
            }
        }

        $units = [];
        foreach ($pairs as $p) {
            if ($p->smallUnit) {
                $uid = (int)$p->unit_small_id;
                if (!isset($units[$uid])) {
                    $units[$uid] = [
                        'product_unit_id' => $p->id,
                        'unit_id'    => $uid,
                        'unit_name'  => $p->smallUnit->name_ar ?? $p->smallUnit->name_en,
                        'factor'     => isset($factor[$uid]) ? (float)$factor[$uid] : 1.0,
                    ];
                }
            }
            if ($p->largeUnit) {
                $uid = (int)$p->unit_large_id;
                if (!isset($units[$uid])) {
                    $units[$uid] = [
                        'product_unit_id' => $p->id,
                        'unit_id'    => $uid,
                        'unit_name'  => $p->largeUnit->name_ar ?? $p->largeUnit->name_en,
                        'factor'     => isset($factor[$uid]) ? (float)$factor[$uid] : 1.0,
                    ];
                }
            }
        }

        $units = collect($units)->values()->sortBy('factor')->values()->all();

        return $units;
    }

    /* ====== AJAX: أرصدة المخزن (كاملة) ====== */
    public function ajaxBalancesByStore($storeId)
    {
        $rows = productstorebalance::with(['product', 'unit'])
            ->where('store_id', $storeId)
            ->get();

        return $rows->map(function ($r) {
            return [
                'product_id'            => $r->product_id,
                'product_name'          => $r->product->name_ar ?? $r->product->name_en ?? '',
                'product_code'          => $r->product->product_code ?? '',
                'quantity_in_base_unit' => (float)($r->quantity_in_base_unit ?? 0),
                'batch_no'              => $r->batch_no,
                'expiry_date'           => optional($r->expiry_date)->format('Y-m-d'),
            ];
        })->all();
    }

    // أرصدة المخزن مقسّمة صفحات (متوافقة مع DataTables serverSide)
    public function ajaxBalancesPaged(Request $request, $storeId)
    {
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 500) : 10;

        $search = trim($request->input('search.value', ''));

        $base = DB::table('product_store_balances as psb')
            ->join('products_data as p', 'p.id', '=', 'psb.product_id')
            ->where('psb.store_id', $storeId)
            ->whereNull('psb.deleted_at');

        $recordsTotal = (clone $base)->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('p.name_ar', 'like', "%{$search}%")
                    ->orWhere('p.name_en', 'like', "%{$search}%")
                    ->orWhere('p.product_code', 'like', "%{$search}%")
                    ->orWhere('psb.batch_no', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $base)->count();

        $rows = $base->orderBy('p.product_code')
            ->offset($start)->limit($length)
            ->get([
                'psb.product_id',
                'psb.batch_no',
                'psb.expiry_date',
                'psb.quantity_in_base_unit',
                'p.name_ar',
                'p.name_en',
                'p.product_code',
            ]);

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'product_id'            => (int)$r->product_id,
                'product_name'          => $r->name_ar ?? $r->name_en ?? '',
                'product_code'          => $r->product_code ?? '',
                'batch_no'              => $r->batch_no,
                'expiry_date'           => $r->expiry_date ? date('Y-m-d', strtotime($r->expiry_date)) : null,
                'quantity_in_base_unit' => (float)$r->quantity_in_base_unit,
            ];
        }

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    // AJAX: وحدات عدة أصناف دفعة واحدة: ids[]=1&ids[]=5...
    public function ajaxProductUnitsBulk(Request $request)
    {
        $ids = array_values(array_filter(array_map('intval', (array) $request->input('ids', []))));
        if (empty($ids)) return response()->json([]);

        $pairs = productunit::with(['smallUnit', 'largeUnit'])
            ->whereIn('product_id', $ids)
            ->get()
            ->groupBy('product_id');

        $out = [];

        foreach ($pairs as $pid => $group) {
            $edges = [];
            $smallIds = collect();
            $largeIds = collect();

            foreach ($group as $p) {
                if ($p->unit_small_id && $p->unit_large_id && $p->conversion_rate > 0) {
                    $edges[] = ['from' => (int)$p->unit_small_id, 'to' => (int)$p->unit_large_id, 'rate' => (float)$p->conversion_rate];
                    $smallIds->push((int)$p->unit_small_id);
                    $largeIds->push((int)$p->unit_large_id);
                }
            }

            $smallIds = $smallIds->unique();
            $largeIds = $largeIds->unique();

            $bases  = $smallIds->diff($largeIds);
            $baseId = $bases->first() ?? $smallIds->first() ?? $largeIds->first();

            $factor = [];
            if ($baseId) $factor[$baseId] = 1.0;

            $changed = true;
            while ($changed) {
                $changed = false;
                foreach ($edges as $e) {
                    if (isset($factor[$e['from']]) && !isset($factor[$e['to']])) {
                        $factor[$e['to']] = $factor[$e['from']] * $e['rate'];
                        $changed = true;
                    }
                }
            }

            $units = [];
            foreach ($group as $p) {
                if ($p->smallUnit) {
                    $uid = (int)$p->unit_small_id;
                    if (!isset($units[$uid])) {
                        $units[$uid] = [
                            'product_unit_id' => $p->id,
                            'unit_id'   => $uid,
                            'unit_name' => $p->smallUnit->name_ar ?? $p->smallUnit->name_en,
                            'factor'    => isset($factor[$uid]) ? (float)$factor[$uid] : 1.0,
                        ];
                    }
                }
                if ($p->largeUnit) {
                    $uid = (int)$p->unit_large_id;
                    if (!isset($units[$uid])) {
                        $units[$uid] = [
                            'product_unit_id' => $p->id,
                            'unit_id'   => $uid,
                            'unit_name' => $p->largeUnit->name_ar ?? $p->largeUnit->name_en,
                            'factor'    => isset($factor[$uid]) ? (float)$factor[$uid] : 1.0,
                        ];
                    }
                }
            }

            $out[$pid] = collect($units)->values()->sortBy('factor')->values()->all();
        }

        return response()->json($out);
    }

    // AJAX: أرصدة المخزن لمنتجات محددة ids[]=1&ids[]=5...
    public function ajaxBalancesByProducts(Request $request, $storeId)
    {
        $ids = array_values(array_filter(array_map('intval', (array)$request->input('ids', []))));
        if (empty($ids)) return response()->json([]);

        $rows = DB::table('product_store_balances as psb')
            ->join('products_data as p', 'p.id', '=', 'psb.product_id')
            ->where('psb.store_id', $storeId)
            ->whereNull('psb.deleted_at')
            ->whereIn('psb.product_id', $ids)
            ->orderBy('p.product_code')
            ->get([
                'psb.product_id',
                'psb.batch_no',
                'psb.expiry_date',
                'psb.quantity_in_base_unit',
                'p.name_ar',
                'p.name_en',
                'p.product_code',
            ]);

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'product_id'            => (int)$r->product_id,
                'product_name'          => $r->name_ar ?? $r->name_en ?? '',
                'product_code'          => $r->product_code ?? '',
                'batch_no'              => $r->batch_no,
                'expiry_date'           => $r->expiry_date ? date('Y-m-d', strtotime($r->expiry_date)) : null,
                'quantity_in_base_unit' => (float)$r->quantity_in_base_unit,
            ];
        }
        return response()->json($data);
    }

    /* ====== صفحة التعديل ====== */
    public function edit($id)
    {
        $audit = InventoryAudit::with([
            'store',
            'creator',
            'details' => function ($q) {
                $q->with(['product', 'productUnit.smallUnit', 'productUnit.largeUnit']);
            }
        ])->findOrFail($id);

        // --- منع الوصول إن لم يكن المخزن "متوقف/مغلق" أثناء الجرد ---
        $store = $audit->store;

        // نحاول استنتاج حالة الإغلاق حسب أكثر الحقول شيوعًا
        $isLocked =
            (isset($store->is_locked)  ? (bool)$store->is_locked  : null);
        if ($isLocked === null && isset($store->is_active)) {
            $isLocked = !(bool)$store->is_active; // إذا غير مفعّل نعتبره مقفول
        }
        if ($isLocked === null && isset($store->status)) {
            $isLocked = in_array((string)$store->status, ['locked', 'inactive', 'disabled', 'stopped'], true);
        }
        $isLocked = (bool)($isLocked ?? false);

        if (!$isLocked) {
            return redirect()
                ->route('inventory_audits.index')
                ->with('error', __('supply_chain_trans.cannot_edit_when_store_active'));
        }
        // --- نهاية التحقق ---

        // المرفقات: مسارات نسبية للحفظ + مطلقة للعرض/المعاينة
        $relAttachments = array_values((array) ($audit->attachments ?? []));
        $absAttachments = collect($relAttachments)
            ->filter()
            ->map(function ($p) {
                $p = ltrim((string)$p, '/');
                return URL::to('/') . '/' . $p;
            })
            ->values()
            ->all();

        // تجهيز أعضاء اللجنة المختارين مسبقًا للـ Select2
        $committeeIds = array_values(array_filter(
            (array) ($audit->committee_user_ids ?? []),
            fn($v) => is_numeric($v)
        ));

        $committeePreset = [];
        if (!empty($committeeIds)) {
            $empsById = \App\models\hr\Employee::query()
                ->whereIn('id', $committeeIds)
                ->get()
                ->keyBy('id');

            foreach ($committeeIds as $cid) {
                if (!$empsById->has($cid)) continue;
                $emp = $empsById->get($cid);

                $committeePreset[] = [
                    'id'   => (int) $emp->id,
                    'text' => $emp->full_name
                        ?? $emp->first_name_ar
                        ?? $emp->first_name_en
                        ?? ('#' . $emp->id),
                ];
            }
        }

        return view('supply_chain.program.inventory_audits.edit', [
            'audit'            => $audit,
            'store'            => $audit->store,   // عرض فقط
            'relAttachments'   => $relAttachments, // للحفظ (keep_attachments[])
            'absAttachments'   => $absAttachments, // للعرض
            'committeePreset'  => $committeePreset // أعضاء اللجنة المحدّدون مسبقًا
        ]);
    }



    /* ====== تحديث ====== */
    public function update(Request $request, $id)
    {
        $audit = InventoryAudit::findOrFail($id);

        $rules = [
            'code'       => 'required|string|max:40|unique:inventory_audits,code,' . $audit->id,
            'audit_date' => 'required|date',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string',
            'committee_user_ids'   => 'array',
            'committee_user_ids.*' => 'integer',
            'attachments.*'        => 'file|max:20480',
            'keep_attachments'     => 'array',
            'keep_attachments.*'   => 'string',
            'items.*.units_id'     => 'required|integer|exists:units,id',
        ];
        Validator::make($request->all(), $rules)->validate();

        DB::transaction(function () use ($request, $audit) {

            // 1) رأس المحضر
            $audit->code       = $request->code;
            $audit->audit_date = $request->audit_date;
            $audit->start_date = $request->start_date ?: null; // ✅
            $audit->end_date   = $request->end_date   ?: null; // ✅
            $audit->notes      = $request->notes;
            $audit->committee_user_ids = array_values($request->input('committee_user_ids', []));
            $audit->save();

            // 2) المرفقات
            $old = array_values((array) ($audit->attachments ?? []));

            if ($request->has('keep_attachments')) {
                // جاي من الواجهة (بعد حذف/إبقاء)
                $keep = array_values(array_filter(
                    (array) $request->input('keep_attachments', []),
                    fn($v) => trim((string)$v) !== ''
                ));
            } else {
                // لم تُرسل keep_attachments:
                // - لو لم تُرفع مرفقات جديدة => لا تغيّر شيئًا (أبقِ القديمة)
                // - لو رُفعت ملفات جديدة فقط => اعتبر keep = []
                $keep = $request->hasFile('attachments') ? [] : $old;
            }

            // احذف من التخزين التي لم تعد ضمن keep
            $toDelete = array_values(array_diff($old, $keep));
            foreach ($toDelete as $relPath) {
                $full = public_path($relPath);
                if (is_file($full)) @unlink($full);
            }

            // أضف مرفقات جديدة
            $final = $keep;
            if ($request->hasFile('attachments')) {
                $uploaded = $this->file_storage($request->file('attachments'), 'inventory_audits');
                $final = array_merge($final, $uploaded);
            }
            $audit->attachments = array_values($final);
            $audit->save();

            // 3) البنود
            $audit->details()->delete();

            $items = $request->input('items', []);
            $seen  = [];
            foreach ($items as $it) {
                $pid   = (int)($it['product_id'] ?? 0);
                $batch = trim($it['batch_no'] ?? '');
                $k = $pid . '__' . mb_strtolower($batch);
                if (isset($seen[$k])) continue;
                $seen[$k] = true;

                $status   = in_array(($it['status'] ?? ''), ['surplus', 'shortage', 'match']) ? $it['status'] : 'match';
                $system   = (float)($it['system_quantity'] ?? 0);
                $actual   = (float)($it['actual_quantity'] ?? 0);
                if ($status === 'match') $actual = $system;
                $difference = $actual - $system;

                $detail = new InventoryAuditDetail([
                    'product_id'          => $pid,
                    'product_unit_id'     => $it['product_unit_id'] ?? null,
                    'units_id'            => $it['units_id'] ?? null,
                    'batch_no'            => $batch,
                    'expiry_date'         => ($it['expiry_date'] ?? null) ?: null,
                    'system_quantity'     => $system,
                    'actual_quantity'     => $actual,
                    'difference_quantity' => $difference,
                    'status'              => $status,
                ]);
                $audit->details()->save($detail);
            }
        });

        return response()->json(['success' => true, 'message' => __('supply_chain_trans.updated_success')]);
    }
    //---------التسويه-------
    // ====== تسوية محضر الجرد ======

    /** كاش للجرافات لكل صنف لتقليل الاستعلامات أثناء التسوية */
    protected array $unitGraphCache = [];

    /**
     * ابني جراف التحويلات لصنف معيّن.
     * يعيد مصفوفتين:
     *  - graph[u] => [['to'=>v, 'mul'=>factor], ...]
     *  - base_unit_id => أصغر وحدة (لا تظهر كـ unit_large_id)
     */
    protected function buildUnitGraphForProduct(int $productId): array
    {
        if (isset($this->unitGraphCache[$productId])) {
            return $this->unitGraphCache[$productId];
        }

        $edges = [];
        $largeSet = [];
        $smallSet = [];

        $rows = \App\Models\supply_chain\productunit::query()
            ->where('product_id', $productId)
            ->get([
                'unit_large_id',
                'unit_small_id',
                'conversion_rate'
            ]);

        foreach ($rows as $r) {
            $L = (int) $r->unit_large_id;
            $S = (int) $r->unit_small_id;
            $c = (float) max(0, (float) $r->conversion_rate);

            if ($L && $S && $c > 0) {
                $edges[$L][] = ['to' => $S, 'mul' => $c];       // 1 كبير = c × صغير
                $edges[$S][] = ['to' => $L, 'mul' => 1.0 / $c]; // 1 صغير = (1/c) × كبير

                $largeSet[$L] = true;
                $smallSet[$S] = true;
            }
        }

        // أصغر وحدة = أي وحدة ظهرت كـ small ولم تظهر كـ large
        $baseUnitId = null;
        foreach (array_keys($smallSet) as $sid) {
            if (!isset($largeSet[$sid])) {
                $baseUnitId = (int) $sid;
                break;
            }
        }
        // fallback: لو لم نجد "صغيرة خالصة"، خذ أي small (أو null)
        if (!$baseUnitId && count($smallSet)) {
            $baseUnitId = (int) array_key_first($smallSet);
        }

        return $this->unitGraphCache[$productId] = [
            'graph'        => $edges,
            'base_unit_id' => $baseUnitId,
        ];
    }

    /**
     * احسب معامل التحويل من وحدة معيّنة إلى أصغر وحدة للصنف.
     * يرجع [factor_to_base, base_unit_id]
     * - factor_to_base: اضرب به الكمية الحالية لتحويلها إلى أصغر وحدة.
     */
    protected function factorToBaseUnit(int $productId, ?int $currentUnitId): array
    {
        $currentUnitId = $currentUnitId ? (int) $currentUnitId : 0;

        $data = $this->buildUnitGraphForProduct($productId);
        $graph = $data['graph'];
        $base  = (int) ($data['base_unit_id'] ?? 0);

        // لو لا يوجد تحويلات مسجلة، اعتبر نفس الوحدة هي الأساس
        if ($base === 0 || !$currentUnitId) {
            return [1.0, $currentUnitId ?: $base];
        }

        // BFS لحساب المعامل من current -> base
        $q = new \SplQueue();
        $q->enqueue($currentUnitId);

        $visited = [];
        $visited[$currentUnitId] = 1.0; // factor للوصول إلى نفس الوحدة

        while (!$q->isEmpty()) {
            $u = (int) $q->dequeue();
            $f = (float) $visited[$u];

            if ($u === $base) {
                return [$f, $base];
            }

            foreach (($graph[$u] ?? []) as $edge) {
                $v  = (int) $edge['to'];
                $mf = (float) $edge['mul'];
                if (!isset($visited[$v])) {
                    $visited[$v] = $f * $mf; // نضرب العوامل على الطريق
                    $q->enqueue($v);
                }
            }
        }

        // لم نجد طريقًا: fallback
        return [1.0, $currentUnitId];
    }

    /**
     * نسخة ملائمة لصف تفاصيل الجرد.
     * تقرأ product_id + units_id من السطر وتحسب التحويل إلى الأصغر.
     */
    protected function factorAndBaseUnitFromDetail($detail): array
    {
        $productId = (int) $detail->product_id;

        // أفضل وحدة حالية للاعتماد: units_id إن وُجدت، وإلا جرّب smallUnit ثم largeUnit
        $currentUnitId = null;
        if (!empty($detail->units_id)) {
            $currentUnitId = (int) $detail->units_id;
        } elseif ($detail->productUnit && $detail->productUnit->smallUnit) {
            $currentUnitId = (int) $detail->productUnit->smallUnit->id;
        } elseif ($detail->productUnit && $detail->productUnit->largeUnit) {
            $currentUnitId = (int) $detail->productUnit->largeUnit->id;
        }

        return $this->factorToBaseUnit($productId, $currentUnitId);
    }

    /** ===================== التسوية ===================== */
    public function settle($id)
    {
        $audit = InventoryAudit::with([
            'store',
            'details.product',
            'details.productUnit.smallUnit',
            'details.productUnit.largeUnit',
        ])->findOrFail($id);

        // امنع تكرار التسوية
        if (in_array((string) $audit->status, ['settled', 'reconciled', 'done', 'adjusted'], true)) {
            return response()->json([
                'success' => false,
                'message' => __('supply_chain_trans.already_settled') ?? 'تمت تسوية هذا المحضر مسبقًا.'
            ], 422);
        }

        $storeId  = (int) $audit->store_id;
        $userId   = Auth::id();
        $moveDate = $audit->audit_date ?: now();

        DB::transaction(function () use ($audit, $storeId, $userId, $moveDate) {

            foreach ($audit->details as $detail) {
                $diff = (float) ($detail->difference_quantity ?? 0);
                if (abs($diff) < 1e-9) {
                    continue; // لا فرق
                }

                /** 1) تحويل الفرق إلى «أصغر وحدة» للصنف عبر الجراف */
                [$factorToBase, $baseUnitId] = $this->factorAndBaseUnitFromDetail($detail);
                // مثال: 1 دستة (الوحدة الحالية) -> 12 عدد (أصغر وحدة)
                $diffInBase = round($diff * (float) $factorToBase, 6); // موجب = زيادة / سالب = خصم

                /** 2) تحديد الباتش والصلاحية كما في السطر */
                $batch = trim((string) ($detail->batch_no ?? '')) ?: null;
                $exp   = $detail->expiry_date
                    ? Carbon::parse($detail->expiry_date)->format('Y-m-d')
                    : null;

                /** 3) تحديث/إنشاء رصيد المخزن لنفس (product/batch/expiry) */
                $balanceQuery = \App\Models\supply_chain\productstorebalance::where('product_id', (int) $detail->product_id)
                    ->where('store_id', $storeId);

                is_null($batch) ? $balanceQuery->whereNull('batch_no') : $balanceQuery->where('batch_no', $batch);
                is_null($exp)   ? $balanceQuery->whereNull('expiry_date') : $balanceQuery->whereDate('expiry_date', $exp);

                /** @var \App\Models\supply_chain\productstorebalance|null $balance */
                $balance = $balanceQuery->lockForUpdate()->first();

                if ($balance) {
                    $newBaseQty = (float) ($balance->quantity_in_base_unit ?? 0) + (float) $diffInBase;
                    if ($newBaseQty < 0) {
                        $newBaseQty = 0;
                    } // لا تسمح بالسالب (اختياري)
                    $balance->quantity_in_base_unit = $newBaseQty;
                    $balance->units_id              = $baseUnitId; // نخزّن الكمية بالوحدة الصغرى
                    $balance->quantity              = $newBaseQty; // لأن units_id = الصغرى
                    $balance->save();
                } else {
                    if ($diffInBase > 0) {
                        \App\Models\supply_chain\productstorebalance::create([
                            'product_id'            => (int) $detail->product_id,
                            'store_id'              => $storeId,
                            'batch_no'              => $batch,
                            'expiry_date'           => $exp,
                            'units_id'              => $baseUnitId,
                            'quantity_in_base_unit' => $diffInBase,
                            'quantity'              => $diffInBase,
                        ]);
                    }
                    // في حالة السالب دون رصيد: نكتفي بتسجيل الحركة أدناه.
                }

                /** 4) تسجيل حركة مخزنية بنوع معتمد من الـ enum */
                $movementType = $diffInBase >= 0 ? 'adjustment_plus' : 'adjustment_minus';
                \App\models\supply_chain\storemovement::create([
                    'product_id'    => (int) $detail->product_id,
                    'store_id'      => $storeId,
                    'units_id'      => $baseUnitId,                       // نسجّل بالوحدة الصغرى
                    'quantity'      => abs($diffInBase),                  // دائمًا موجبة
                    'batch_no'      => $batch,
                    'expiry_date'   => $exp,
                    'movement_type' => $movementType,                     // adjustment_plus / adjustment_minus
                    'reference_no'  => $audit->code,                      // مرجع المحضر
                    'movement_date' => ($moveDate instanceof Carbon)
                        ? $moveDate->format('Y-m-d')
                        : Carbon::parse($moveDate)->format('Y-m-d'),
                    'notes'         => 'Inventory audit settlement (auto)',
                    'user_id'       => $userId,
                ]);
            }

            /** 5) تحديث حالة المحضر */
            $audit->status   = 'settled';
            $audit->end_date = $audit->end_date ?: now()->format('Y-m-d');
            $audit->save();
        });

        return response()->json([
            'success' => true,
            'message' => __('supply_chain_trans.settled_successfully') ?? 'تمت التسوية وتحديث الأرصدة بنجاح.',
        ]);
    }



    /**
     * يحسب عامل التحويل للوحدة الصغرى + يُعيد ID الوحدة الصغرى
     * يعتمد على productUnit.smallUnit / largeUnit وعمود عامل التحويل (يحاول أكثر من اسم شائع)
     */
    private function factorAndBaseUnit($detail): array
    {
        $factor = 1.0;
        $baseUnitId = (int)($detail->units_id ?? 0);

        $pu = $detail->productUnit; // علاقة السطر مع productUnit
        if ($pu) {
            $smallId = optional($pu->smallUnit)->id;
            $largeId = optional($pu->largeUnit)->id;

            if ($smallId) {
                $baseUnitId = (int)$smallId; // الصغرى
            }

            // جرّب أسماء حقول شائعة لعامل التحويل من الكبيرة إلى الصغرى
            $possibleFactor = null;
            foreach (['factor', 'conversion_factor', 'large_unit_factor', 'transfer_value', 'small_unit_quantity'] as $col) {
                if (!is_null($pu->{$col})) {
                    $possibleFactor = (float)$pu->{$col};
                    break;
                }
            }
            if (!$possibleFactor || $possibleFactor <= 0) {
                $possibleFactor = 1.0;
            }

            $detailUnitId = (int)($detail->units_id ?? 0);
            if ($detailUnitId === (int)$smallId) {
                $factor = 1.0; // أصلاً بالصغرى
            } elseif ($largeId && $detailUnitId === (int)$largeId) {
                $factor = (float)$possibleFactor; // حوّل من الكبيرة للصغرى
            } else {
                // لو وحدة أخرى، اعتبر العامل المرسل = 1 كحل آمن
                $factor = 1.0;
            }
        }

        return [$factor, (int)$baseUnitId];
    }

    public function destroy($id)
    {
        $audit = InventoryAudit::findOrFail($id);

        // اجمع المسارات قبل الحذف من قاعدة البيانات
        $attachments = array_values((array) ($audit->attachments ?? []));

        // احذف التفاصيل ثم رأس المحضر
        DB::transaction(function () use ($audit) {
            $audit->details()->delete();
            // لو عندك SoftDeletes وتريد حذفًا نهائيًا: استخدم forceDelete()
            $audit->delete();
        });

        // احذف الملفات فعليًا (ببساطة)
        foreach ($attachments as $path) {
            if (!$path) continue;

            $rel = ltrim((string) $path, '/');

            // 1) لو الملف موجود مباشرة تحت public/
            $abs = public_path($rel);
            if (is_file($abs)) {
                @unlink($abs);
                continue;
            }

            // 2) لو المسار يبدأ بـ public/storage/ (= storage disk 'public')
            if (Str::startsWith($rel, 'storage/')) {
                $onDisk = Str::after($rel, 'storage/'); // يصبح نسبةً لقرص public
                if (Storage::disk('public')->exists($onDisk)) {
                    Storage::disk('public')->delete($onDisk);
                    continue;
                }
            }

            // 3) محاولة أخيرة على قرص public بنفس المسار
            if (Storage::disk('public')->exists($rel)) {
                Storage::disk('public')->delete($rel);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('supply_chain_trans.deleted_successfully')
        ]);
    }
}
