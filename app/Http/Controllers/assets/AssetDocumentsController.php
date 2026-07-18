<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\models\assets\assetdocument;
use App\models\assets\assetitem;

// موديل المورد
use App\Models\Suppliers\suppliersdata as SuppliersData;

// ✅ Trait الرفع الموحّد
use App\Traits\web\file_storage as Uploads;

class AssetDocumentsController extends Controller
{
    use Uploads;

    public function index(Request $req)
    {
        if ($req->wantsJson()) return $this->ajaxIndex($req);

        $assets = assetitem::select('id', 'code', 'name_ar', 'name_en')->orderByDesc('id')->limit(500)->get();
        $suppliers = SuppliersData::select('id', 'name_ar', 'name_en', 'code', 'status')->orderBy('name_ar')->get();

        return view('assets.programs.documents.index', compact('assets', 'suppliers'));
    }

    public function ajaxIndex(Request $req)
    {
        $q = assetdocument::with(['asset','supplier']);

        if ($aid = $req->get('asset_id'))    $q->where('asset_id', $aid);
        if ($sid = $req->get('supplier_id')) $q->where('supplier_id', $sid);

        $search = $req->get('search')['value'] ?? $req->get('q');
        if ($search) {
            $q->where(function ($x) use ($search) {
                $x->where('doc_no','like',"%$search%")
                  ->orWhere('doc_type','like',"%$search%")
                  ->orWhere('notes','like',"%$search%");
            })->orWhereHas('asset', function ($y) use ($search) {
                $y->where('code','like',"%$search%")
                  ->orWhere('name_ar','like',"%$search%")
                  ->orWhere('name_en','like',"%$search%");
            });
        }

        $total = $q->count();
        $start = (int)($req->get('start', 0));
        $len   = (int)($req->get('length', 10));
        $rows  = $q->orderByDesc('id')->skip($start)->take($len)->get();

        $data = [];
        $idx  = $start + 1;
        foreach ($rows as $row) {
            // رابط التحميل (يخدم سواء حفظت تحت public/attachments أو public/storage/attachments)
            $fileUrl = $row->file_path ? asset(ltrim($row->file_path, '/')) : null;

            $data[] = [
                'DT_RowIndex' => $idx++,
                'asset' => e($row->asset ? ($row->asset->code . ' — ' . ($row->asset->name_ar ?? $row->asset->name_en ?? '')) : '-'),
                'doc_type' => e(__('assets.doc_' . $row->doc_type)),
                'doc_no' => e($row->doc_no ?: '-'),
                'doc_date' => e($row->doc_date ? $row->doc_date->toDateString() : '-'),
                'cost' => e($row->cost ?? '-'),
                'file' => $fileUrl
                    ? '<a href="' . e($fileUrl) . '" target="_blank">' . __('assets.download') . '</a>'
                    : '-',
                'action'   => view('assets.programs.documents._actions', compact('row'))->render(),
            ];
        }
        return response()->json(['data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $total]);
    }

    public function create()
    {
        $assets = assetitem::select('id', 'code', 'name_ar', 'name_en')->get();
        $suppliers = SuppliersData::select('id', 'name_ar', 'name_en', 'code', 'status')->orderBy('name_ar')->get();
        return view('assets.programs.documents.create', compact('assets', 'suppliers'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'asset_id' => 'required|exists:asset_items,id',
            'doc_type' => 'required|in:invoice,warranty,other',
            'doc_no' => 'nullable|string|max:190',
            'doc_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers_data,id',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:8192|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($req->hasFile('file')) {
            // ✅ استخدام Trait
            $data['file_path'] = $this->file_storage($req->file('file'), 'assets/docs');
        }

        assetdocument::create($data);

        $action = $req->input('submit_action', 'back');
        return $action === 'stay'
            ? redirect()->route('assets.programs.documents.create')->with('success', __('assets.saved_stay'))
            : redirect()->route('assets.programs.documents.index')->with('success', __('assets.saved'));
    }

    public function show(Request $req, $id)
    {
        $row = assetdocument::with(['asset', 'supplier'])->findOrFail($id);
        if ($req->ajax() || $req->wantsJson() || $req->query('modal')) {
            $html = view('assets.programs.documents._show', compact('row'))->render();
            return response()->json(['html' => $html]);
        }
        return view('assets.programs.documents.show', compact('row'));
    }

    public function edit($id)
    {
        $row = assetdocument::with(['asset', 'supplier'])->findOrFail($id);
        $assets = assetitem::select('id', 'code', 'name_ar', 'name_en')->get();
        $suppliers = SuppliersData::select('id', 'name_ar', 'name_en', 'code', 'status')->orderBy('name_ar')->get();

        // لو المورد الحالي غير موجود في القائمة لأي سبب، ضمّه
        if ($row->supplier && !$suppliers->contains('id', $row->supplier->id)) $suppliers->push($row->supplier);

        return view('assets.programs.documents.edit', compact('row', 'assets', 'suppliers'));
    }

    public function update(Request $req, $id)
    {
        $row = assetdocument::findOrFail($id);
        $data = $req->validate([
            'asset_id' => 'required|exists:asset_items,id',
            'doc_type' => 'required|in:invoice,warranty,other',
            'doc_no' => 'nullable|string|max:190',
            'doc_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers_data,id',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:8192|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($req->hasFile('file')) {
            // حذف القديم من أي مكان
            $this->deleteFileAnywhere($row->file_path);
            // وارفع الجديد
            $data['file_path'] = $this->file_storage($req->file('file'), 'assets/docs');
        }

        $row->update($data);

        $action = $req->input('submit_action', 'back');
        return $action === 'stay'
            ? redirect()->route('assets.programs.documents.edit', $row->id)->with('success', __('assets.updated_stay'))
            : redirect()->route('assets.programs.documents.index')->with('success', __('assets.updated'));
    }

    public function destroy($id)
    {
        $row = assetdocument::findOrFail($id);

        // احذف الملف فعليًا مهما كان مكانه
        $this->deleteFileAnywhere($row->file_path);

        $row->delete();

        return request()->wantsJson()
            ? response()->json(['ok' => true, 'message' => __('assets.deleted_success')])
            : back()->with('success', __('assets.deleted_success'));
    }

    /**
     * يحذف ملفًا أينما كان:
     * - storage/app/public/{path} عبر Storage::disk('public')
     * - أو public/{path} عبر unlink
     * يقبل مسارات مثل: "attachments/assets/docs/2025/09/file.png"
     */
    protected function deleteFileAnywhere(?string $path): void
    {
        if (!$path) return;

        try {
            // ط normalization
            $p = ltrim(str_replace(['\\','//'], '/', $path), '/');

            // 1) جرّب على disk('public') بالمسار كما هو
            if (Storage::disk('public')->exists($p)) {
                Storage::disk('public')->delete($p);
                return;
            }

            // 2) أحيانًا كانت القيم بدون "attachments/"
            $alt = preg_replace('/^attachments\//', '', $p);
            if ($alt !== $p && Storage::disk('public')->exists($alt)) {
                Storage::disk('public')->delete($alt);
                return;
            }

            // 3) جرّب الحذف من public مباشرةً
            $abs = public_path($p);            // public/attachments/... أو أي مسار نسبي تحت public
            if (@is_file($abs)) { @unlink($abs); return; }

            // 4) أحيانًا الملفات موجودة تحت public/storage/{p}
            $abs2 = public_path('storage/'.$p);
            if (@is_file($abs2)) { @unlink($abs2); return; }

            // 5) كحل أخير لو المسار المُخزن أصلاً مطلق
            if (@is_file($path)) { @unlink($path); }
        } catch (\Throwable $e) {
            // نتجاهل الأخطاء حتى لا نكسر التدفق
        }
    }
}
