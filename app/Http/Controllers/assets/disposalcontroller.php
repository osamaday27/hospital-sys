<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\models\assets\assetitem;
use App\models\assets\DisposalRequest;
use App\models\assets\DisposalRequestItem;
use App\models\assets\DisposalApproval;
use App\models\assets\Disposal;
use App\models\assets\DisposalItem;
use App\models\assets\AssetDisposalAttachment;
use App\models\assets\assetaudit;

use App\Events\Assets\DisposalRequestSubmitted;
use App\Events\Assets\DisposalRequestApproved;
use App\Events\Assets\DisposalExecuted;
use App\models\assets\assetassignment;
use App\Traits\web\file_storage as Uploads;

class disposalcontroller extends Controller
{
    use Uploads;

    /** لجان افتراضية (يمكن لاحقاً نقلها للإعدادات) */
    protected array $workflowByMethod = [
        'sale'     => ['لجنة فنية', 'لجنة مالية/قانونية', 'مدير إدارة'],
        'donation' => ['لجنة فنية', 'الإدارة العليا/قانوني'],
        'scrap'    => ['لجنة فنية', 'لجنة إعدام/مخازن', 'مدير مالي'],
    ];

    /** index */
    public function index(Request $r)
    {
        if ($r->wantsJson()) return $this->ajaxIndex($r);
        return view('assets.programs.disposal.index');
    }

    public function ajaxIndex(Request $r)
    {
        $q = DisposalRequest::withCount('items');

        if ($m = $r->get('method')) $q->where('method', $m);
        if ($s = $r->get('status')) $q->where('status', $s);
        if ($search = ($r->get('search')['value'] ?? $r->get('q'))) {
            $q->where(function ($w) use ($search) {
                $w->where('request_no', 'like', "%$search%")
                  ->orWhere('reason', 'like', "%$search%")
                  ->orWhere('notes', 'like', "%$search%");
            });
        }

        $total = $q->count();
        $start = (int)$r->get('start', 0);
        $len   = (int)$r->get('length', 10);
        $rows  = $q->orderByDesc('id')->skip($start)->take($len)->get();

        $data = [];
        $i = $start + 1;
        foreach ($rows as $row) {
            $data[] = [
                'DT_RowIndex'  => $i++,
                'request_no'   => e($row->request_no),
                'method'       => e(trans('assets.disposal.method_' . $row->method, [], 'ar')),
                'status'       => e(trans('assets.disposal.status_' . $row->status, [], 'ar')),
                'items'        => $row->items_count,
                'requested_at' => $row->requested_at?->toDateTimeString(),
                'action'       => view('assets.programs.disposal._actions', compact('row'))->render(),
            ];
        }

        return response()->json(['data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $total]);
    }

    /** create */
    public function create()
    {
        return view('assets.programs.disposal.create');
    }

    /** AJAX: بحث الأصول */
    public function ajaxAssets(Request $r)
    {
        $q = assetitem::query()->select('id', 'code', 'name_ar', 'name_en', 'qty', 'status', 'location_id', 'store_id', 'net_book_value');
        if ($s = $r->get('q')) {
            $q->where(function ($w) use ($s) {
                $w->where('code', 'like', "%$s%")
                  ->orWhere('name_ar', 'like', "%$s%")
                  ->orWhere('name_en', 'like', "%$s%");
            });
        }
        $rows = $q->orderBy('code')->limit(50)->get();
        return response()->json($rows->map(fn($a) => [
            'id' => $a->id,
            'text' => $a->code . ' — ' . ($a->name_ar ?? $a->name_en ?? ''),
            'qty' => $a->qty,
            'status' => $a->status,
            'nbv' => $a->net_book_value,
        ]));
    }

    /** AJAX: بحث المستخدمين لاختيار المعتمدين (Select2) */
    public function ajaxUsers(Request $r)
    {
        $q = \App\models\User::query()->select('id', 'name', 'email');
        if ($s = $r->get('q')) {
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%");
            });
        }
        $rows = $q->orderBy('name')->limit(50)->get();
        return response()->json($rows->map(fn($u) => [
            'id' => $u->id,
            'text' => $u->name . ' — ' . $u->email,
        ]));
    }

    /** ترقيم الطلب */
    protected function nextRequestNo(): string
    {
        $prefix = 'DR-' . now()->format('Ym') . '-';
        $last = DisposalRequest::where('request_no', 'like', $prefix . '%')->orderByDesc('id')->value('request_no');
        $seq  = $last ? ((int)substr($last, -4)) + 1 : 1;
        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    /** فحص الجرد النشط (يستثني الملغى/المغلق) */
    protected function hasActiveAuditForAsset(assetitem $asset): bool
    {
        return assetaudit::whereNotIn('status', ['cancelled', 'canceled', 'closed', 'finished', 'void'])
            ->whereNotNull('started_at')
            ->whereNull('finished_at')
            ->where(function ($w) use ($asset) {
                $w->whereNull('scope_location_id')->orWhere('scope_location_id', $asset->location_id);
            })
            ->where(function ($w) use ($asset) {
                $w->whereNull('scope_store_id')->orWhere('scope_store_id', $asset->store_id);
            })
            ->exists();
    }

    /** إنشاء طلب مسودة */
    public function store(Request $r)
    {
        $data = $r->validate([
            'method' => 'required|in:sale,donation,scrap',
            'reason' => 'nullable|string|max:500',
            'notes'  => 'nullable|string',
            'items'  => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:asset_items,id',
            'items.*.qty'      => 'required|integer|min:1',
            'attachments.*'    => 'nullable|file|max:8192|mimes:pdf,jpg,jpeg,png',
        ]);

        // منع الإنشاء وقت الجرد + حالة الأصل
        foreach ($data['items'] as $it) {
            $asset = assetitem::find($it['asset_id']);
            if ($this->hasActiveAuditForAsset($asset)) {
                return back()->with('error', __('assets.disposal.audit_active_block'))->withInput();
            }
            if (in_array($asset->status, ['retired', 'scrapped'])) {
                return back()->with('error', __('assets.disposal.asset_already_retired'))->withInput();
            }
            // تحقق كمية موجبة ومتاحة
            $qty = (int)$it['qty'];
            if ($qty < 1 || $qty > (int)$asset->qty) {
                return back()->with('error', __('assets.disposal.qty_not_available'))->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $req = DisposalRequest::create([
                'request_no'   => $this->nextRequestNo(),
                'method'       => $data['method'],
                'status'       => 'draft',
                'reason'       => $data['reason'] ?? null,
                'notes'        => $data['notes'] ?? null,
                'requested_at' => now(),
                'requested_by' => Auth::id(),
                'created_by'   => Auth::id(),
            ]);

            foreach ($data['items'] as $it) {
                $asset = assetitem::find($it['asset_id']);
                $perUnitNBV   = max(0, (float)($asset->net_book_value ?? 0)) / max(1, (int)($asset->qty ?? 1));
                $perUnitCost  = max(0, (float)($asset->cost ?? 0)) / max(1, (int)($asset->qty ?? 1));
                $perUnitResid = max(0, (float)($asset->residual_value ?? 0)) / max(1, (int)($asset->qty ?? 1));

                DisposalRequestItem::create([
                    'request_id'         => $req->id,
                    'asset_id'           => $asset->id,
                    'qty_requested'      => (int)$it['qty'],
                    'snapshot_cost'      => $perUnitCost * (int)$it['qty'],
                    'snapshot_accum_dep' => null,
                    'snapshot_nbv'       => $perUnitNBV * (int)$it['qty'],
                    'snapshot_residual'  => $perUnitResid * (int)$it['qty'],
                    'remarks'            => null,
                ]);
            }

            // مرفقات
            if ($r->hasFile('attachments')) {
                foreach ($r->file('attachments') as $file) {
                    $path = $this->file_storage($file, 'assets/disposals');
                    AssetDisposalAttachment::create([
                        'request_id'     => $req->id,
                        'type'           => 'request',
                        'file_path'      => $path,
                        'original_name'  => $file->getClientOriginalName(),
                        'uploaded_by'    => Auth::id(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('assets.programs.disposal.show', $req->id)->with('success', __('assets.saved'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /** عرض الطلب — تم تمرير $levels لإظهار اختيار اللجنة */
public function show($id)
{
    $row = DisposalRequest::with([
        'items.asset',
        'approvals.assigneeUser',
        'approvals.approverUser',
        'attachments',
        'disposal.items'
    ])->findOrFail($id);

    // مستويات اللجان حسب الطريقة
    $levels = $this->workflowByMethod[$row->method] ?? ['لجنة'];

    return view('assets.programs.disposal.show', compact('row','levels'));
}


    /** إرسال للاعتماد + تعيين المخوّلين (أو اعتماد مباشر إذا لم يُحدَّد أحد) */
public function submit($id)
{
    $row = DisposalRequest::with('items')->findOrFail($id);
    if ($row->status !== 'draft') {
        return back()->with('error', __('assets.disposal.only_draft_can_submit'));
    }

    // منع الإرسال وقت الجرد
    foreach ($row->items as $it) {
        if ($this->hasActiveAuditForAsset($it->asset)) {
            return back()->with('error', __('assets.disposal.audit_active_block'));
        }
    }

    // المسؤولون المختارون من الفورم: assignees[level] = user_id
    $assigneesRaw = (array) request()->get('assignees', []);
    // احتفظ فقط بالمستويات التي تم تعيين user_id لها
    $assignees = array_filter($assigneesRaw, fn($v) => !empty($v));

    DB::transaction(function () use ($row, $assignees) {
        $levels = $this->workflowByMethod[$row->method] ?? ['لجنة'];

        if (count($assignees) === 0) {
            // لا يوجد مسؤولون محدّدون → اعتماد فوري
            $row->update([
                'status'        => 'approved',
                'submitted_at'  => now(),
                'submitted_by'  => Auth::id(),
                'approved_at'   => now(),
            ]);
        } else {
            // أنشئ approvals فقط للمستويات التي تم اختيار مستخدم لها
            $i = 1;
            foreach ($levels as $committeeName) {
                $uid = $assignees[$i] ?? null;
                if ($uid) {
                    DisposalApproval::create([
                        'context'            => 'disposal',
                        'request_id'         => $row->id,
                        'level'              => $i,
                        'role_or_committee'  => $committeeName,
                        'assignee_user_id'   => $uid,
                        'decision'           => 'pending',
                        'created_by'         => Auth::id(),
                    ]);
                }
                $i++;
            }

            $row->update([
                'status'        => 'submitted',
                'submitted_at'  => now(),
                'submitted_by'  => Auth::id(),
            ]);
        }
    });

    // Events حسب الحالة النهائية بعد الترانزاكشن
    $row->refresh();
    if ($row->status === 'approved') {
        event(new DisposalRequestApproved($row));
    } else {
        event(new DisposalRequestSubmitted($row));
    }

    return redirect()->route('assets.programs.disposal.show', $row->id)
        ->with('success', $row->status === 'approved'
            ? __('assets.disposal.status_approved')  // أو رسالة "تم الاعتماد"
            : __('assets.disposal.submitted'));
}


    /** تسجيل قرار لجنة لمستوى معين */
public function approve(Request $r, $id)
{
    $data = $r->validate([
        'level'    => 'required|integer|min:1',
        'decision' => 'required|in:approved,rejected,returned',
        'notes'    => 'nullable|string',
    ]);

    $row = DisposalRequest::with('approvals')->findOrFail($id);
    if (!in_array($row->status, ['submitted', 'under_review'])) {
        return back()->with('error', __('assets.disposal.approval_wrong_status'));
    }

    DB::transaction(function () use ($row, $data) {
        $appr = $row->approvals()->where('level', $data['level'])->firstOrFail();

        if ($appr->decision !== 'pending') return;

        // enforce: نفس المعيّن فقط يعتمد
        if ($appr->assignee_user_id && $appr->assignee_user_id != Auth::id()) {
            abort(403, 'غير مخوّل لاعتماد هذا المستوى.');
        }

        $appr->update([
            'decision'          => $data['decision'],
            'decision_notes'    => $data['notes'] ?? null,
            'decided_at'        => now(),
            'approver_user_id'  => Auth::id(),
        ]);

        if ($data['decision'] === 'rejected') {
            $row->update(['status' => 'rejected']);
            return;
        }
        if ($data['decision'] === 'returned') {
            $row->update(['status' => 'draft']);
            return;
        }

        // approved: هل جميع الموافقات الموجودة Approved؟
        $totalApprovals   = $row->approvals()->count();
        $approvedCount    = $row->approvals()->where('decision', 'approved')->count();
        $allApproved      = ($totalApprovals > 0) && ($approvedCount === $totalApprovals);

        $row->update(['status' => $allApproved ? 'approved' : 'under_review']);

        if ($allApproved) {
            $row->update(['approved_at' => now()]);
            event(new \App\Events\Assets\DisposalRequestApproved($row));
        }
    });

    return back()->with('success', __('assets.updated'));
}

    /** نموذج التنفيذ */
    public function executeForm($id)
    {
        $row = DisposalRequest::with(['items.asset', 'disposal'])->findOrFail($id);
        if ($row->status !== 'approved') return back()->with('error', __('assets.disposal.only_approved_can_execute'));

        foreach ($row->items as $it) {
            if ($it->asset && $this->hasOpenAssignment($it->asset)) {
                return back()->with('error', __('assets.disposal.asset_has_open_assignment'));
            }
        }

        return view('assets.programs.disposal.execute', compact('row'));
    }

    /** تنفيذ التخلّص */
    public function execute(Request $r, $id)
    {
        $row = DisposalRequest::with(['items.asset'])->findOrFail($id);

        foreach ($row->items as $it) {
            if ($it->asset && $this->hasOpenAssignment($it->asset)) {
                return back()->with('error', __('assets.disposal.asset_has_open_assignment'));
            }
        }
        if ($row->status !== 'approved') return back()->with('error', __('assets.disposal.only_approved_can_execute'));

        foreach ($row->items as $it) {
            if ($this->hasActiveAuditForAsset($it->asset)) {
                return back()->with('error', __('assets.disposal.audit_active_block'));
            }
            // تحقق كمية التنفيذ لا تتجاوز المتوفّر
            $maxAllowed = min((int)$it->qty_requested, (int)$it->asset->qty);
            $inp = $r->input("items.*.qty_executed"); // فحص عام سيتم فحصه تفصيلياً أسفل
            // الفحص التفصيلي تحت في foreach الإنشاء
        }

        $rules = [
            'items' => 'required|array|min:1',
            'items.*.request_item_id' => 'required|exists:disposal_request_items,id',
            'items.*.qty_executed'    => 'required|integer|min:0',
        ];

        if ($row->method === 'sale') {
            $rules += [
                'buyer_name'      => 'required|string|max:190',
                'sale_invoice_no' => 'required|string|max:190',
                'sale_amount'     => 'required|numeric|min:0',
                'vat_amount'      => 'nullable|numeric|min:0',
                'fees_amount'     => 'nullable|numeric|min:0',
            ];
        } elseif ($row->method === 'donation') {
            $rules += ['donee_name' => 'required|string|max:190', 'donation_doc_no' => 'nullable|string|max:190'];
        } else {
            $rules += ['destruction_method' => 'required|string|max:190', 'destruction_doc_no' => 'nullable|string|max:190'];
        }

        $data = $r->validate($rules);

        DB::beginTransaction();
        try {
            $disposal = Disposal::create([
                'request_id' => $row->id,
                'method'     => $row->method,
                'buyer_name' => $data['buyer_name'] ?? null,
                'buyer_id_number' => $data['buyer_id_number'] ?? null,
                'sale_invoice_no'  => $data['sale_invoice_no'] ?? null,
                'sale_amount'      => $data['sale_amount'] ?? null,
                'vat_amount'       => $data['vat_amount'] ?? null,
                'fees_amount'      => $data['fees_amount'] ?? null,
                'net_amount'       => isset($data['sale_amount']) ? (($data['sale_amount']) - (float)($data['fees_amount'] ?? 0) + (float)($data['vat_amount'] ?? 0)) : null,
                'donee_name'       => $data['donee_name'] ?? null,
                'donation_doc_no'  => $data['donation_doc_no'] ?? null,
                'destruction_method' => $data['destruction_method'] ?? null,
                'destruction_doc_no' => $data['destruction_doc_no'] ?? null,
                'witnessed_by'      => $data['witnessed_by'] ?? null,
                'executed_by'       => Auth::id(),
                'executed_at'       => now(),
                'created_by'        => Auth::id(),
            ]);

            $sumCost = $sumAcc = $sumNBV = $sumRes = 0;

            foreach ($data['items'] as $it) {
                $ri = DisposalRequestItem::with('asset')->findOrFail($it['request_item_id']);
                $qtyExec = (int)$it['qty_executed'];

                // لا نسمح بأكثر من المتاح (من الكمية الحالية للأصل) ولا أكثر من المطلوب
                $maxAllowed = min((int)$ri->qty_requested - (int)($ri->qty_executed ?? 0), (int)$ri->asset->qty);
                if ($qtyExec < 0 || $qtyExec > $maxAllowed) {
                    throw new \RuntimeException("Invalid qty for request item #{$ri->id}");
                }
                if ($qtyExec === 0) continue;

                $asset = $ri->asset;

                $perUnitNBV   = max(0, (float)($asset->net_book_value ?? 0)) / max(1, (int)($asset->qty ?? 1));
                $perUnitCost  = max(0, (float)($asset->cost ?? 0)) / max(1, (int)($asset->qty ?? 1));
                $perUnitResid = max(0, (float)($asset->residual_value ?? 0)) / max(1, (int)($asset->qty ?? 1));

                DisposalItem::create([
                    'disposal_id'             => $disposal->id,
                    'request_item_id'         => $ri->id,
                    'asset_id'                => $asset->id,
                    'qty_executed'            => $qtyExec,
                    'exec_snapshot_cost'      => $perUnitCost * $qtyExec,
                    'exec_snapshot_accum_dep' => null,
                    'exec_snapshot_nbv'       => $perUnitNBV * $qtyExec,
                    'exec_snapshot_residual'  => $perUnitResid * $qtyExec,
                ]);

                $sumCost += $perUnitCost * $qtyExec;
                $sumNBV  += $perUnitNBV  * $qtyExec;
                $sumRes  += $perUnitResid * $qtyExec;

                $ri->qty_executed = (int)($ri->qty_executed ?? 0) + $qtyExec;
                $ri->save();

                $newQty = max(0, (int)$asset->qty - $qtyExec);
                $asset->qty = $newQty;

                if ($newQty === 0) {
                    $asset->status = in_array($row->method, ['sale', 'donation']) ? 'retired' : 'scrapped';
                    $asset->disposal_method     = $row->method;
                    $asset->disposal_date       = now()->toDateString();
                    $asset->disposal_request_id = $row->id;
                }
                $asset->save();
            }

            $disposal->update([
                'exec_snapshot_total_cost'      => $sumCost,
                'exec_snapshot_total_nbv'       => $sumNBV,
                'exec_snapshot_total_residual'  => $sumRes,
            ]);

            $row->update([
                'status'      => 'executed',
                'executed_at' => $disposal->executed_at,
            ]);

            $bp = [
                'date' => $disposal->executed_at?->toDateString(),
                'method' => $row->method,
                'request_no' => $row->request_no,
                'total_nbv'  => $disposal->exec_snapshot_total_nbv,
                'sale_amount' => $disposal->sale_amount,
                'fees'       => $disposal->fees_amount,
                'vat'        => $disposal->vat_amount,
                'gain_loss'  => isset($disposal->sale_amount) ? ((float)$disposal->sale_amount - (float)$disposal->fees_amount - (float)$disposal->exec_snapshot_total_nbv) : null,
                'lines'      => [],
            ];

            DB::commit();

            event(new DisposalExecuted($disposal, $bp));
            return redirect()->route('assets.programs.disposal.show', $row->id)->with('success', __('assets.disposal.executed'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /** إلغاء قبل التنفيذ */
    public function cancel($id)
    {
        $row = DisposalRequest::findOrFail($id);
        if (!in_array($row->status, ['draft', 'submitted', 'under_review', 'approved'])) {
            return back()->with('error', __('assets.disposal.cannot_cancel_now'));
        }
        $row->update(['status' => 'cancelled']);
        return back()->with('success', __('assets.updated'));
    }

    /** طباعة */
    public function printRequest($id)
    {
        $row = DisposalRequest::with(['items.asset', 'attachments'])->findOrFail($id);
        return view('assets.programs.disposal.print_request', compact('row'));
    }
    public function printCommittee($id)
    {
        $row = DisposalRequest::with(['approvals.assigneeUser','approvals.approverUser', 'items.asset'])->findOrFail($id);
        return view('assets.programs.disposal.print_committee', compact('row'));
    }
    public function printExecution($id)
    {
        $row = DisposalRequest::with(['disposal.items.asset'])->findOrFail($id);
        return view('assets.programs.disposal.print_execution', compact('row'));
    }

    protected function hasOpenAssignment(\App\models\assets\assetitem $asset): bool
    {
        return assetassignment::open()->where('asset_id', $asset->id)->exists();
    }
}
