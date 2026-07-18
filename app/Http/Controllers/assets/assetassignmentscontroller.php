<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\models\assets\assetassignment;
use App\models\assets\assetitem;

// جهات الإسناد
use App\Models\supply_chain\storemaindata as StoreMainData;
use App\Models\Suppliers\suppliersdata as SuppliersData;
use App\models\hr\Employee as HrEmployee;
use App\models\hr\section  as HrSection;

// Trait الرفع الموحّد
use App\Traits\web\file_storage as Uploads;

class AssetAssignmentsController extends Controller
{
    use Uploads;

    /** يحوّل أي مسار محفوظ إلى مسار نسبي لقرص public */
    private function relPath(?string $path): ?string
    {
        if (!$path) return null;
        $p = str_replace('\\', '/', $path);
        $p = ltrim($p, '/');
        return preg_replace('/^(attachments|public)\//i', '', $p);
    }

    /** رابط عام لملف محفوظ على disk('public') */
    private function publicUrl(?string $path): ?string
    {
        if (!$path) return null;
        $rel = $this->relPath($path);
        return $rel ? Storage::disk('public')->url($rel) : null;
    }

    /** حذف ملف من قرص public بأمان */
    private function deletePublicFile(?string $path): void
    {
        if (!$path) return;
        try {
            $rel = $this->relPath($path);
            if ($rel) Storage::disk('public')->delete($rel);
        } catch (\Throwable $e) { /* ignore */ }
    }

    /**
     * المتاح من أصل = qty في جدول الأصول - (إجمالي المُسند - إجمالي المُرتجع)
     */
    private function availableQtyForAsset(int $assetId): int
    {
        $row = assetitem::select('id','qty','status')->find($assetId);
        if (!$row) return 0;

        // أصل قيد الخدمة فقط
        if (($row->status ?? 'in_service') !== 'in_service') {
            return 0;
        }

        $assigned = (int) assetassignment::whereNull('deleted_at')
            ->where('asset_id', $assetId)
            ->where('status', 'assigned')
            ->sum('qty');

        $returned = (int) assetassignment::whereNull('deleted_at')
            ->where('asset_id', $assetId)
            ->where('status', 'returned')
            ->sum('qty');

        $outstanding = max(0, $assigned - $returned);
        $available   = max(0, (int)$row->qty - $outstanding);
        return $available;
    }

    /** إجمالي الكمية المُعادة لهذا الإسناد (الأب) */
    private function returnedQtyForAssignment(assetassignment $assign): int
    {
        return (int) assetassignment::whereNull('deleted_at')
            ->where('parent_id', $assign->id)
            ->where('status', 'returned')
            ->sum('qty');
    }

    /** المتبقي لإرجاعه من نفس سجل الإسناد */
    private function remainingToReturnForAssignment(assetassignment $assign): int
    {
        if ($assign->status !== 'assigned') return 0;
        $alreadyReturned = $this->returnedQtyForAssignment($assign);
        return max(0, (int)$assign->qty - $alreadyReturned);
    }

    /**
     * الأصول المتاحة للإسناد: الحالة in_service والمتاح > 0
     */
    private function availableAssetsQuery()
    {
        return assetitem::query()
            ->where('status', 'in_service')
            ->where(function($q){
                $q->whereRaw("
                    (COALESCE(qty,0) -
                     (
                       COALESCE((SELECT SUM(a1.qty) FROM asset_assignments a1
                                 WHERE a1.asset_id = asset_items.id AND a1.status='assigned' AND a1.deleted_at IS NULL),0)
                       -
                       COALESCE((SELECT SUM(a2.qty) FROM asset_assignments a2
                                 WHERE a2.asset_id = asset_items.id AND a2.status='returned' AND a2.deleted_at IS NULL),0)
                     )
                    ) > 0
                ");
            })
            ->orderByDesc('id');
    }

    /** يبني اسم الجهة الرسمي لعرضه بجانب الاسم اليدوي */
    private function buildOfficialAssigneeText(assetassignment $row): string
    {
        static $emp = [], $sec = [], $store = [], $supp = [];

        $id   = (int)($row->assignable_id ?? 0);
        $type = (string)($row->assignable_type ?? '');

        if (!$id) return '';

        switch ($type) {
            case 'employee':
                if (!isset($emp[$id])) {
                    $emp[$id] = HrEmployee::select('id','full_name','first_name_ar','second_name_ar','third_name_ar','fourth_name_ar')->find($id);
                }
                if ($emp[$id]) {
                    $ar = trim(implode(' ', array_filter([
                        $emp[$id]->first_name_ar,
                        $emp[$id]->second_name_ar,
                        $emp[$id]->third_name_ar,
                        $emp[$id]->fourth_name_ar,
                    ])));
                    return $ar ?: ($emp[$id]->full_name ?? '');
                }
                break;

            case 'department':
                if (!isset($sec[$id])) {
                    $sec[$id] = HrSection::select('id','name_ar','name_ne')->find($id);
                }
                if ($sec[$id]) return $sec[$id]->name_ar ?? $sec[$id]->name_ne ?? '';
                break;

            case 'store':
                if (!isset($store[$id])) {
                    $store[$id] = StoreMainData::select('id','manual_code','name_ar','name_en')->find($id);
                }
                if ($store[$id]) {
                    return trim(($store[$id]->manual_code ? $store[$id]->manual_code.' — ' : '')
                             . ($store[$id]->name_ar ?? $store[$id]->name_en ?? ''));
                }
                break;

            case 'supplier':
                if (!isset($supp[$id])) {
                    $supp[$id] = SuppliersData::select('id','code','name_ar','name_en')->find($id);
                }
                if ($supp[$id]) {
                    return trim(($supp[$id]->code ? $supp[$id]->code.' — ' : '')
                             . ($supp[$id]->name_ar ?? $supp[$id]->name_en ?? ''));
                }
                break;
        }
        return '';
    }

    /** هل مازال هناك كمية متبقية للإرجاع لهذا السجل؟ */
    private function canReturn(assetassignment $row): bool
    {
        return $row->status === 'assigned' && $this->remainingToReturnForAssignment($row) > 0;
    }

    // ================== CRUD ==================

    public function index(Request $req)
    {
        if ($req->wantsJson()) return $this->ajaxIndex($req);
        $assets  = assetitem::select('id','code','name_ar','name_en')->orderByDesc('id')->limit(500)->get();
        return view('assets.programs.assignments.index', compact('assets'));
    }

    public function ajaxIndex(Request $req)
    {
        $q = assetassignment::with(['asset'])->orderByDesc('id');

        // بحث عام
        $search = $req->get('search')['value'] ?? $req->get('q');
        if ($search) {
            $q->where(function($x) use ($search){
                $x->where('assignable_name','like',"%$search%")
                  ->orWhere('notes','like',"%$search%");
            })->orWhereHas('asset', function($y) use ($search){
                $y->where('code','like',"%$search%")
                  ->orWhere('name_ar','like',"%$search%")
                  ->orWhere('name_en','like',"%$search%");
            });
        }

        // فلاتر اختيارية
        if ($aid = $req->get('asset_id')) $q->where('asset_id', $aid);
        if ($st  = $req->get('status'))   $q->where('status', $st);

        $total = (clone $q)->count();
        $start = (int)($req->get('start',0));
        $len   = (int)($req->get('length',10));
        $rows  = $q->skip($start)->take($len)->get();

        $data=[]; $idx=$start+1;
        foreach($rows as $row){
            $assetTxt = $row->asset ? ($row->asset->code.' — '.($row->asset->name_ar ?? $row->asset->name_en ?? '')) : '-';

            $statusBadge = $row->status === 'assigned'
                ? '<span class="badge bg-info">'.e(__('assets.status_assigned')).'</span>'
                : '<span class="badge bg-success">'.e(__('assets.status_returned')).'</span>';

            $fileUrl = $this->publicUrl($row->handover_path);

            $official = $this->buildOfficialAssigneeText($row);
            $assigneeShown = trim(($row->assignable_name ?: '-') . ($official ? (' — '.$official) : ''));

            $canReturn = $this->canReturn($row);
            $remaining = $this->remainingToReturnForAssignment($row);

            $data[] = [
                'DT_RowIndex' => $idx++,
                'asset'       => e($assetTxt),
                'assignee'    => e(ucfirst($row->assignable_type)).' — '.e($assigneeShown),
                'assigned_at' => e(optional($row->assigned_at)->toDateString() ?: '-'),
                'due_back_at' => e(optional($row->due_back_at)->toDateString() ?: '-'),
                'returned_at' => e(optional($row->returned_at)->toDateString() ?: '-'),
                'qty'         => e((int)$row->qty), // عرض الكمية
                'status'      => $statusBadge,
                'notes'       => e($row->notes ?? '-'),
                'file'        => $fileUrl ? '<a href="'.e($fileUrl).'" target="_blank">'.__('assets.download').'</a>' : '-',
                'action'      => view('assets.programs.assignments._actions', [
                                    'row' => $row,
                                    'canReturn' => $canReturn,
                                    'remainingToReturn' => $remaining,
                                ])->render(),
            ];
        }

        return response()->json([
            'data'            => $data,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total
        ]);
    }

    public function create()
    {
$assets = $this->availableAssetsQuery()
    ->selectRaw("id, code, name_ar, name_en, qty,
        (COALESCE(qty,0) - (
            COALESCE((SELECT SUM(a1.qty) FROM asset_assignments a1
                     WHERE a1.asset_id = asset_items.id AND a1.status='assigned' AND a1.deleted_at IS NULL),0)
            -
            COALESCE((SELECT SUM(a2.qty) FROM asset_assignments a2
                     WHERE a2.asset_id = asset_items.id AND a2.status='returned' AND a2.deleted_at IS NULL),0)
        )) AS available_qty")
    ->limit(800)->get();

        $today = now()->toDateString();
        return view('assets.programs.assignments.create', compact('assets','today'));
    }

    public function store(Request $req)
    {
        $rules = [
            'asset_id'        => ['required','exists:asset_items,id'],
            'assignable_type' => ['required', Rule::in(['employee','department','store','supplier','other'])],
            'assignable_id'   => ['nullable','integer'],
            'assignable_name' => ['nullable','string','max:190'],
            'assigned_at'     => ['required','date'],
            'due_back_at'     => ['nullable','date','after_or_equal:assigned_at'],
            'qty'             => ['required','integer','min:1'], // الكمية المسندة
            'notes'           => ['nullable','string'],
            'handover'        => ['nullable','file','max:8192','mimes:pdf,jpg,jpeg,png'],
        ];

        // إلزام الجهة حسب النوع
        $type = $req->input('assignable_type');
        if (in_array($type, ['employee','department','store','supplier'], true)) {
            $rules['assignable_id'][] = 'required';
            switch ($type) {
                case 'employee':  $rules['assignable_id'][] = 'exists:employees,id'; break;
                case 'department':$rules['assignable_id'][] = 'exists:hr_sections,id'; break;
                case 'store':     $rules['assignable_id'][] = 'exists:store_main_date,id'; break;
                case 'supplier':  $rules['assignable_id'][] = 'exists:suppliers_data,id'; break;
            }
        }

        $data = $req->validate($rules);

        // الأصل يجب أن يكون قيد الخدمة
        $asset = assetitem::find($data['asset_id']);
        if (!$asset || $asset->status !== 'in_service') {
            return back()->withErrors(['asset_id' => __('assets.err_asset_not_in_service')])->withInput();
        }

        $assetId   = (int)$data['asset_id'];
        $assignQty = (int)$data['qty'];

        // تحقّق الكمية المتاحة (معاملة لمنع السباق)
        return DB::transaction(function() use ($req, $data, $assetId, $assignQty) {
            $available = $this->availableQtyForAsset($assetId);
            if ($assignQty > $available) {
                return back()->withErrors([
                    'qty' => __('assets.err_qty_exceeds_available', ['available' => $available])
                ])->withInput();
            }

            // الاسم الظاهر تلقائيًا إن غاب
            if (empty($data['assignable_name']) && !empty($data['assignable_id'])) {
                $fake = new assetassignment([
                    'assignable_id'   => $data['assignable_id'],
                    'assignable_type' => $data['assignable_type'],
                ]);
                $data['assignable_name'] = $this->buildOfficialAssigneeText($fake);
            }

            $data['status'] = 'assigned';

            if ($req->hasFile('handover')) {
                $data['handover_path'] = $this->file_storage($req->file('handover'), 'assignments/handover');
            }

            assetassignment::create($data);

            return $req->input('submit_action')==='stay'
                ? redirect()->route('assets.programs.assignments.create')->with('success', __('assets.saved_stay'))
                : redirect()->route('assets.programs.assignments.index')->with('success', __('assets.saved'));
        });
    }

    public function edit($id)
    {
        $row = assetassignment::findOrFail($id);

$assets = $this->availableAssetsQuery()
    ->orWhere('id', $row->asset_id)
    ->selectRaw("id, code, name_ar, name_en, qty,
        (COALESCE(qty,0) - (
            COALESCE((SELECT SUM(a1.qty) FROM asset_assignments a1
                     WHERE a1.asset_id = asset_items.id AND a1.status='assigned' AND a1.deleted_at IS NULL),0)
            -
            COALESCE((SELECT SUM(a2.qty) FROM asset_assignments a2
                     WHERE a2.asset_id = asset_items.id AND a2.status='returned' AND a2.deleted_at IS NULL),0)
        )) AS available_qty")
    ->limit(800)->get();

        $currentText = $this->buildOfficialAssigneeText($row);

        // متبقي لهذا الإسناد
        $remainingToReturn = $this->remainingToReturnForAssignment($row);

        return view('assets.programs.assignments.edit', compact('row','assets','currentText','remainingToReturn'));
    }

    public function update(Request $req, $id)
    {
        $row = assetassignment::findOrFail($id);

        $rules = [
            'asset_id'        => ['required','exists:asset_items,id'],
            'assignable_type' => ['required', Rule::in(['employee','department','store','supplier','other'])],
            'assignable_id'   => ['nullable','integer'],
            'assignable_name' => ['nullable','string','max:190'],
            'assigned_at'     => ['required','date'],
            'due_back_at'     => ['nullable','date','after_or_equal:assigned_at'],
            'qty'             => ['required','integer','min:1'], // تعديل الكمية
            'notes'           => ['nullable','string'],
            'handover'        => ['nullable','file','max:8192','mimes:pdf,jpg,jpeg,png'],
        ];

        $type = $req->input('assignable_type');
        if (in_array($type, ['employee','department','store','supplier'], true)) {
            $rules['assignable_id'][] = 'required';
            switch ($type) {
                case 'employee':  $rules['assignable_id'][] = 'exists:employees,id'; break;
                case 'department':$rules['assignable_id'][] = 'exists:hr_sections,id'; break;
                case 'store':     $rules['assignable_id'][] = 'exists:store_main_date,id'; break;
                case 'supplier':  $rules['assignable_id'][] = 'exists:suppliers_data,id'; break;
            }
        }

        $data = $req->validate($rules);

        // الأصل يجب أن يكون قيد الخدمة
        $asset = assetitem::find($data['asset_id']);
        if (!$asset || $asset->status !== 'in_service') {
            return back()->withErrors(['asset_id' => __('assets.err_asset_not_in_service')])->withInput();
        }

        // لا نعدّل الحالة هنا
        unset($data['status'], $data['returned_at']);

        return DB::transaction(function() use ($req, $row, $data) {

            $newAssetId = (int)$data['asset_id'];
            $newQty     = (int)$data['qty'];
            $oldAssetId = (int)$row->asset_id;
            $oldQty     = (int)$row->qty;

            // لا يمكن تخفيض الكمية أقل من مجموع ما تم إرجاعه بالفعل لهذا الإسناد
            $alreadyReturned = $this->returnedQtyForAssignment($row);
            if ($newQty < $alreadyReturned) {
                return back()->withErrors([
                    'qty' => __('assets.err_qty_below_returned', ['returned' => $alreadyReturned])
                ])->withInput();
            }

            // تحقق المتاح
            if ($newAssetId !== $oldAssetId) {
                $available = $this->availableQtyForAsset($newAssetId);
                if ($newQty > $available) {
                    return back()->withErrors([
                        'qty' => __('assets.err_qty_exceeds_available', ['available' => $available])
                    ])->withInput();
                }
            } else {
                $delta = $newQty - $oldQty;
                if ($delta > 0) {
                    $available = $this->availableQtyForAsset($newAssetId);
                    if ($delta > $available) {
                        return back()->withErrors([
                            'qty' => __('assets.err_qty_exceeds_available', ['available' => $available])
                        ])->withInput();
                    }
                }
            }

            if (empty($data['assignable_name']) && !empty($data['assignable_id'])) {
                $fake = new assetassignment([
                    'assignable_id'   => $data['assignable_id'],
                    'assignable_type' => $data['assignable_type'],
                ]);
                $data['assignable_name'] = $this->buildOfficialAssigneeText($fake);
            }

            if ($req->hasFile('handover')) {
                $this->deletePublicFile($row->handover_path);
                $data['handover_path'] = $this->file_storage($req->file('handover'), 'assignments/handover');
            }

            $row->update($data);

            return $req->input('submit_action')==='stay'
                ? redirect()->route('assets.programs.assignments.edit', $row->id)->with('success', __('assets.updated_stay'))
                : redirect()->route('assets.programs.assignments.index')->with('success', __('assets.updated'));
        });
    }

    /**
     * إرجاع جزئي/كامل: ينشئ سجل جديد status=returned بكمية qty (<= المتبقي).
     * لا يغيّر السجل الأصلي.
     */
    public function return($id, Request $req)
    {
        $orig = assetassignment::findOrFail($id);

        if ($orig->status !== 'assigned') {
            return response()->json(['ok'=>false,'message'=>__('assets.err_not_assigned')], 422);
        }

        // يستقبل الحقل 'qty' من صفحة الـ index (المودال)
        $data = $req->validate([
            'qty'     => ['required','integer','min:1'],
            'notes'   => ['nullable','string'],
            'handover'=> ['nullable','file','max:8192','mimes:pdf,jpg,jpeg,png'],
        ]);

        $remaining = $this->remainingToReturnForAssignment($orig);
        if ($remaining <= 0) {
            return response()->json(['ok'=>false,'message'=>__('assets.err_nothing_to_return')], 422);
        }
        if ((int)$data['qty'] > $remaining) {
            return response()->json(['ok'=>false,'message'=>__('assets.err_return_exceeds_outstanding', ['remaining'=>$remaining])], 422);
        }

        $handoverPath = null;
        if ($req->hasFile('handover')) {
            $handoverPath = $this->file_storage($req->file('handover'), 'assignments/return');
        }

        assetassignment::create([
            'parent_id'       => $orig->id,
            'asset_id'        => $orig->asset_id,
            'assignable_type' => $orig->assignable_type,
            'assignable_id'   => $orig->assignable_id,
            'assignable_name' => $orig->assignable_name,
            'assigned_at'     => $orig->assigned_at,
            'due_back_at'     => $orig->due_back_at,
            'returned_at'     => now(),
            'status'          => 'returned',
            'qty'             => (int)$data['qty'],
            'handover_path'   => $handoverPath,
            'notes'           => $data['notes'] ?? null,
        ]);

        return response()->json(['ok'=>true,'message'=>__('assets.returned_success')]);
    }

    public function destroy($id)
    {
        $row = assetassignment::findOrFail($id);

        // حذف المرفق فعليًا
        $this->deletePublicFile($row->handover_path);

        // soft delete للسجل
        $row->delete();

        return request()->wantsJson()
            ? response()->json(['ok'=>true,'message'=>__('assets.deleted_success')])
            : back()->with('success', __('assets.deleted_success'));
    }

    // =========== Select2 AJAX ===========

    public function ajaxEmployeesSelect2(Request $req)
    {
        $q = trim($req->get('q',''));
        $rows = HrEmployee::query()
            ->when($q, function($x) use ($q){
                $x->where('full_name','like',"%$q%")
                  ->orWhere('first_name_ar','like',"%$q%")
                  ->orWhere('second_name_ar','like',"%$q%")
                  ->orWhere('third_name_ar','like',"%$q%")
                  ->orWhere('fourth_name_ar','like',"%$q%");
            })
            ->orderBy('first_name_ar')
            ->limit(50)->get(['id','full_name','first_name_ar','second_name_ar','third_name_ar','fourth_name_ar']);

        $out = [];
        foreach($rows as $r){
            $ar = trim(implode(' ', array_filter([$r->first_name_ar,$r->second_name_ar,$r->third_name_ar,$r->fourth_name_ar])));
            $out[] = ['id'=>$r->id, 'text'=>$ar ?: ($r->full_name ?? ('#'.$r->id))];
        }
        return ['results'=>$out];
    }

    public function ajaxSectionsSelect2(Request $req)
    {
        $q = trim($req->get('q',''));
        $rows = HrSection::query()
            ->when($q, function($x) use ($q){
                $x->where('name_ar','like',"%$q%")
                  ->orWhere('name_ne','like',"%$q%");
            })
            ->orderBy('name_ar')
            ->limit(50)->get(['id','name_ar','name_ne']);

        $out=[];
        foreach($rows as $r){
            $out[] = ['id'=>$r->id, 'text'=>($r->name_ar ?? $r->name_ne ?? ('#'.$r->id))];
        }
        return ['results'=>$out];
    }

    public function ajaxStoresSelect2(Request $req)
    {
        $q = trim($req->get('q',''));
        $rows = StoreMainData::query()
            ->when($q, function($x) use ($q){
                $x->where('name_ar','like',"%$q%")
                  ->orWhere('name_en','like',"%$q%")
                  ->orWhere('manual_code','like',"%$q%");
            })
            ->orderBy('name_ar')
            ->limit(50)->get(['id','manual_code','name_ar','name_en']);

        $out=[];
        foreach($rows as $r){
            $text = trim(($r->manual_code ? $r->manual_code.' — ' : '')
                      . ($r->name_ar ?? $r->name_en ?? ''));
            $out[] = ['id'=>$r->id, 'text'=>$text ?: ('#'.$r->id)];
        }
        return ['results'=>$out];
    }

    public function ajaxSuppliersSelect2(Request $req)
    {
        $q = trim($req->get('q',''));
        $rows = SuppliersData::query()
            ->when($q, function($x) use ($q){
                $x->where('name_ar','like',"%$q%")
                  ->orWhere('name_en','like',"%$q%")
                  ->orWhere('code','like',"%$q%");
            })
            ->orderBy('name_ar')
            ->limit(50)->get(['id','code','name_ar','name_en']);

        $out=[];
        foreach($rows as $r){
            $text = trim(($r->code ? $r->code.' — ' : '')
                      . ($r->name_ar ?? $r->name_en ?? ''));
            $out[] = ['id'=>$r->id, 'text'=>$text ?: ('#'.$r->id)];
        }
        return ['results'=>$out];
    }
}
