<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\models\assets\assetitem;
use App\models\assets\assetdocument;
use App\models\assets\assetcategory;
use App\models\assets\assetbrand;
use App\models\assets\assetmodel;
use App\models\assets\assetlocation;
use App\models\assets\assetsetting;
use App\models\assets\assetdepreciationline as DepLine;
use App\models\assets\assetdepreciationrun as DepRun;
// جداول خارجية (المخازن/الموردين)
use App\Models\supply_chain\storemaindata as StoreMainData;
use App\Models\Suppliers\suppliersdata as SuppliersData;

class AssetItemsController extends Controller
{
    public function index(Request $req){
        if ($req->wantsJson()) return $this->ajaxIndex($req);
        return view('assets.programs.items.index');
    }

    public function ajaxIndex(Request $req){
        $base = assetitem::query();                                // قبل الفلترة
        $q    = assetitem::with(['category','brand','model','location','store','supplier']);

        $search = $req->get('search')['value'] ?? $req->get('q');
        if ($search) {
            $q->where(function($x) use ($search){
                $x->where('code','like',"%$search%")
                  ->orWhere('name_ar','like',"%$search%")
                  ->orWhere('name_en','like',"%$search%")
                  ->orWhere('serial_no','like',"%$search%");
            });
        }

        $recordsTotal    = (clone $base)->count();                 // إجمالي قبل الفلترة
        $recordsFiltered = (clone $q)->count();                    // إجمالي بعد الفلترة

        $start = (int)($req->get('start',0));
        $len   = (int)($req->get('length',10));
        $rows  = $q->orderByDesc('id')->skip($start)->take($len)->get();

        $statusClasses = [
            'in_service'  => 'badge bg-success',
            'maintenance' => 'badge bg-warning text-dark',
            'retired'     => 'badge bg-secondary',
            'scrapped'    => 'badge bg-danger',
        ];

        $data = [];
        $idx  = $start+1;
        foreach($rows as $row){
            $storeText = $row->store
                ? (($row->store->manual_code ? ($row->store->manual_code.' — ') : '')
                   .($row->store->name_ar ?? $row->store->name_en ?? ''))
                : '-';

            $status = $row->status ?? 'in_service';
            $badgeClass = $statusClasses[$status] ?? 'badge bg-light text-dark';
            $statusHtml = '<span class="'.$badgeClass.'">'.e(__('assets.status_'.$status)).'</span>';

            $data[] = [
                'DT_RowIndex' => $idx++,
                'code'     => e($row->code),
                'name'     => e($row->name_ar ?? $row->name_en ?? ''),
                'category' => e($row->category? ($row->category->name_ar ?? $row->category->name_en ?? '') : '-'),
                'brand'    => e($row->brand? ($row->brand->name ?? $row->brand->code ?? '') : '-'),
                'model'    => e($row->model? ($row->model->name ?? $row->model->code ?? '') : '-'),
                'store'    => e($storeText),
                'status'   => $statusHtml,
                'action'   => view('assets.programs.items._actions', compact('row'))->render(),
            ];
        }

        return response()->json([
            'data'            => $data,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
        ]);
    }

    public function create(){
        $cats    = assetcategory::select('id','name_ar','name_en','code')->where('is_active',1)->get();
        $brands  = assetbrand::select('id','name','code')->where('is_active',1)->get();
        $models  = assetmodel::select('id','name','code')->where('is_active',1)->get();
        $locs    = assetlocation::select('id','name_ar','name_en')->where('is_active',1)->get();
        $stores  = StoreMainData::select('id','name_ar','name_en','manual_code','status')->orderBy('name_ar')->get();
        $suppliers = SuppliersData::select('id','name_ar','name_en','code','status')->orderBy('name_ar')->get();

        $today = now()->toDateString();
        $code  = $this->nextCode();

        return view('assets.programs.items.create', compact('cats','brands','models','locs','suppliers','stores','today','code'));
    }

/**
 * إنشاء/تحديث QR وحفظ المسار في qr_code_path بشكل موثوق.
 */
protected function tryMakeQr(\App\models\assets\assetitem $row, bool $force = false): void
{
    try {
        if (!$force && $row->qr_code_path) return;

        if (!class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            return; // الحزمة غير محملة
        }

        // الإفتراضيات
        $disk = 'public';
        $basePath = 'qr/assets'; // مهم: بدون "attachments/" هنا
        $size = 256;
        $margin = 1;

        // تحميل الإعدادات إن وُجدت
        if (class_exists(\App\models\assets\assetsetting::class)) {
            if ($gs = \App\models\assets\assetsetting::firstRow()) {
                $disk     = trim($gs->qr_disk ?: $disk);
                $basePath = trim($gs->qr_path ?: $basePath);
                $size     = (int)($gs->qr_size_px ?? $size);
                $margin   = (int)($gs->qr_margin ?? $margin);
            }
        }

        // تطبيع المسار (مهم جدًا لتفادي تكرار attachments/)
        $basePath = str_replace('\\','/',$basePath);
        $basePath = trim(preg_replace('/^(public|attachments)\//i', '', $basePath), '/');

        \Illuminate\Support\Facades\Storage::disk($disk)->makeDirectory($basePath);

        // اختَر الصيغة: PNG إن كان Imagick متاحًا، وإلا SVG
        $usePng = extension_loaded('imagick');
        $ext = $usePng ? 'png' : 'svg';

        $binary = \SimpleSoftwareIO\QrCode\Facades\QrCode::format($usePng ? 'png' : 'svg')
                    ->size($size)->margin($margin)
                    ->generate($row->code);

$file = $basePath.'/'.\Illuminate\Support\Str::slug($row->code).'.'.$ext;
Storage::disk($disk)->put($file, $binary);

// خزّن المسار مسبوقًا بـ attachments/
$row->qr_code_path = 'attachments/'.$file;
\Illuminate\Database\Eloquent\Model::withoutEvents(fn() => $row->save());


    } catch (\Throwable $e) {
        // \Log::warning('QR failed: '.$e->getMessage());
    }
}





public function store(Request $req){
    // فاليوديشن أساسي
    $data = $req->validate([
        'code'               => 'nullable|string|max:190|unique:asset_items,code',
        'name_ar'            => 'nullable|string|max:190',
        'name_en'            => 'nullable|string|max:190',
        'category_id'        => 'nullable|exists:asset_categories,id',
        'brand_id'           => 'nullable|exists:asset_brands,id',
        'model_id'           => 'nullable|exists:asset_models,id',
        'location_id'        => 'nullable|exists:asset_locations,id',
        'store_id'           => 'nullable|exists:store_main_date,id',
        'supplier_id'        => 'nullable|exists:suppliers_data,id',
        'serial_no'          => 'nullable|string|max:190',
        'purchase_date'      => 'nullable|date',
        'in_service_date'    => 'nullable|date|after_or_equal:purchase_date',
        'cost'               => 'nullable|numeric|min:0',
        'residual_value'     => 'nullable|numeric|min:0',
        'useful_life_months' => 'nullable|integer|min:1',
        'dep_method'         => 'nullable|in:SL,DDB,UOP',
        'dep_rate'           => 'nullable|numeric|min:0|max:100',
        'uop_total_estimated_units' => 'nullable|integer|min:1',
        'status'             => 'required|in:in_service,maintenance,retired,scrapped',
        'rfid_tag'           => 'nullable|string|max:190|unique:asset_items,rfid_tag',
        'is_active'          => 'nullable|boolean',
        'qty'                => 'nullable|integer|min:1',
    ]);

    if (empty($data['code'])) $data['code'] = $this->nextCode();
    if (empty($data['qty']))  $data['qty']  = 1;

    // افتراضيات من التصنيف
    if (!empty($data['category_id']) && (empty($data['useful_life_months']) || empty($data['dep_method']) || !isset($data['dep_rate']))) {
        $cat = assetcategory::find($data['category_id']);
        if ($cat) {
            $data['useful_life_months'] = $data['useful_life_months'] ?? $cat->useful_life_months;
            $data['dep_method']         = $data['dep_method'] ?? $cat->default_dep_method;
            $data['dep_rate']           = isset($data['dep_rate']) ? $data['dep_rate'] : $cat->default_dep_rate;
        }
    }

    // قواعد منطقية إضافية:
    $m = $data['dep_method'] ?? null;
    if (in_array($m, ['SL','DDB'])) {
        if (empty($data['useful_life_months']) && (!isset($data['dep_rate']) || $data['dep_rate']==='')) {
            return back()->withErrors(['useful_life_months' => __('assets.validation.life_or_rate')])->withInput();
        }
    }
    if ($m === 'UOP') {
        if (empty($data['uop_total_estimated_units'])) {
            return back()->withErrors(['uop_total_estimated_units' => __('assets.validation.uop_total_required')])->withInput();
        }
        // في UOP لا نلزم rate/life
        $data['useful_life_months'] = $data['useful_life_months'] ?? null;
        $data['dep_rate']           = $data['dep_rate'] ?? null;
    }

    // in_service_date = purchase_date لو فاضي
    if (empty($data['in_service_date']) && !empty($data['purchase_date'])) {
        $data['in_service_date'] = $data['purchase_date'];
    }

    // default residual
    if (!isset($data['residual_value'])) $data['residual_value'] = 0;

    $data['is_active'] = (bool)($req->is_active ?? 1);

    $row = assetitem::create($data);

    // NBV ابتدائي
    $row->net_book_value = round((float)($row->cost ?? 0) - (float)($row->accumulated_depreciation ?? 0), 2);
    $row->save();

    // توليد QR
    $this->tryMakeQr($row, true);

    $action = $req->input('submit_action','back');
    return $action==='stay'
        ? redirect()->route('assets.programs.items.create')->with('success', __('assets.saved_stay'))
        : redirect()->route('assets.programs.items.index')->with('success', __('assets.saved'));
}


public function show(Request $req, $id){
    $row = assetitem::with(['category','brand','model','location','store','supplier','documents.supplier'])->findOrFail($id);

    // إجمالي وحدات UOP المستخدمة للأصل من تشغيلات مُرحّلة
    $uopUsed = 0;
    if ($row->dep_method === 'UOP' && (int)($row->uop_total_estimated_units ?? 0) > 0) {
        $uopUsed = DepLine::where('asset_id', $row->id)
            ->where('dep_method','UOP')
            ->whereHas('run', function($q){
                $q->where('status','posted');
            })
            ->sum('uop_units_period');
        $uopUsed = (int)$uopUsed;
    }

    if ($req->ajax() || $req->wantsJson() || $req->query('modal')) {
        $html = view('assets.programs.items._show', ['row'=>$row, 'uopUsed'=>$uopUsed])->render();
        return response()->json(['html'=>$html]);
    }
    return view('assets.programs.items.show', ['row'=>$row, 'uopUsed'=>$uopUsed]);
}


    public function edit($id){
        $row = assetitem::with(['store','supplier'])->findOrFail($id);

        $cats     = assetcategory::select('id','name_ar','name_en','code')->where('is_active',1)->get();
        $brands   = assetbrand::select('id','name','code')->where('is_active',1)->get();
        $models   = assetmodel::select('id','name','code')->where('is_active',1)->get();
        $locs     = assetlocation::select('id','name_ar','name_en')->where('is_active',1)->get();
        $stores   = StoreMainData::select('id','name_ar','name_en','manual_code','status')->orderBy('name_ar')->get();
        $suppliers= SuppliersData::select('id','name_ar','name_en','code','status')->orderBy('name_ar')->get();

        // ضمّ المخزن/المورد الحاليين إن لم يكونا ضمن القائمة
        if ($row->store && !$stores->contains('id', $row->store->id))           $stores->push($row->store);
        if ($row->supplier && !$suppliers->contains('id', $row->supplier->id))  $suppliers->push($row->supplier);

        return view('assets.programs.items.edit', compact('row','cats','brands','models','locs','suppliers','stores'));
    }

public function update(Request $req, $id){
    $row = assetitem::findOrFail($id);

    $data = $req->validate([
        'code'               => 'required|string|max:190|unique:asset_items,code,'.$row->id,
        'name_ar'            => 'nullable|string|max:190',
        'name_en'            => 'nullable|string|max:190',
        'category_id'        => 'nullable|exists:asset_categories,id',
        'brand_id'           => 'nullable|exists:asset_brands,id',
        'model_id'           => 'nullable|exists:asset_models,id',
        'location_id'        => 'nullable|exists:asset_locations,id',
        'store_id'           => 'nullable|exists:store_main_date,id',
        'supplier_id'        => 'nullable|exists:suppliers_data,id',
        'serial_no'          => 'nullable|string|max:190',
        'purchase_date'      => 'nullable|date',
        'in_service_date'    => 'nullable|date|after_or_equal:purchase_date',
        'cost'               => 'nullable|numeric|min:0',
        'residual_value'     => 'nullable|numeric|min:0',
        'useful_life_months' => 'nullable|integer|min:1',
        'dep_method'         => 'nullable|in:SL,DDB,UOP',
        'dep_rate'           => 'nullable|numeric|min:0|max:100',
        'uop_total_estimated_units' => 'nullable|integer|min:1',
        'status'             => 'required|in:in_service,maintenance,retired,scrapped',
        'rfid_tag'           => 'nullable|string|max:190|unique:asset_items,rfid_tag,'.$row->id,
        'is_active'          => 'nullable|boolean',
        'qty'                => 'nullable|integer|min:1',
    ]);

    if (empty($data['qty'])) $data['qty'] = 1;

    // افتراضيات من التصنيف عند الحاجة
    if (!empty($data['category_id']) && (empty($data['useful_life_months']) || empty($data['dep_method']) || !isset($data['dep_rate']))) {
        $cat = assetcategory::find($data['category_id']);
        if ($cat) {
            $data['useful_life_months'] = $data['useful_life_months'] ?? $cat->useful_life_months;
            $data['dep_method']         = $data['dep_method'] ?? $cat->default_dep_method;
            $data['dep_rate']           = isset($data['dep_rate']) ? $data['dep_rate'] : $cat->default_dep_rate;
        }
    }

    $m = $data['dep_method'] ?? $row->dep_method;
    if (in_array($m, ['SL','DDB'])) {
        $life = $data['useful_life_months'] ?? $row->useful_life_months;
        $rate = array_key_exists('dep_rate', $data) ? $data['dep_rate'] : $row->dep_rate;
        if (empty($life) && ($rate === null || $rate === '')) {
            return back()->withErrors(['useful_life_months' => __('assets.validation.life_or_rate')])->withInput();
        }
    }
    if ($m === 'UOP') {
        $uopTotal = $data['uop_total_estimated_units'] ?? $row->uop_total_estimated_units;
        if (empty($uopTotal)) {
            return back()->withErrors(['uop_total_estimated_units' => __('assets.validation.uop_total_required')])->withInput();
        }
        // تنظيف حقول لا حاجة لها
        if (!array_key_exists('useful_life_months', $data)) $data['useful_life_months'] = $row->useful_life_months;
        if (!array_key_exists('dep_rate', $data))           $data['dep_rate']           = $row->dep_rate;
        $data['useful_life_months'] = null;
        $data['dep_rate']           = null;
    }

    if ((empty($data['in_service_date']) || $data['in_service_date']===null) && !empty($data['purchase_date'])) {
        $data['in_service_date'] = $data['purchase_date'];
    }

    if (!isset($data['residual_value'])) $data['residual_value'] = $row->residual_value ?? 0;

    $data['is_active'] = (bool)($req->is_active ?? 0);
    $row->update($data);

    // NBV إعادة حساب مبدئي (بدون إهلاك مُرحّل)
    $row->net_book_value = round((float)($row->cost ?? 0) - (float)($row->accumulated_depreciation ?? 0), 2);
    $row->save();

    // إعادة توليد QR عند الحاجة
    $this->tryMakeQr($row, false);

    $action = $req->input('submit_action','back');
    return $action==='stay'
        ? redirect()->route('assets.programs.items.edit',$row->id)->with('success', __('assets.updated_stay'))
        : redirect()->route('assets.programs.items.index')->with('success', __('assets.updated'));
}

    public function destroy($id){
        $row = assetitem::with('documents')->findOrFail($id);

        DB::transaction(function() use ($row) {
            // حذف QR
            $this->deleteFileAnywhere($row->qr_code_path);

            // حذف المرفقات + السجلات
            foreach ($row->documents as $doc) {
                $this->deleteFileAnywhere($doc->file_path);
                $doc->delete();
            }

            $row->delete();
        });

        return request()->wantsJson()
            ? response()->json(['ok'=>true,'message'=>__('assets.deleted_success')])
            : back()->with('success', __('assets.deleted_success'));
    }

    public function printCard($id){
        $row = assetitem::findOrFail($id);
        if (!$row->qr_code_path) {
            $this->tryMakeQr($row, true);
            $row->refresh();
        }
        return view('assets.programs.items.print', compact('row'));
    }

    // ===== Helpers =====
    protected function nextCode(): string {
        $gs = class_exists(\App\models\assets\assetsetting::class) ? assetsetting::firstRow() : null;
        $prefix = $gs? ($gs->code_prefix ?: 'AST-') : 'AST-';
        $last   = assetitem::where('code','like',$prefix.'%')->orderByDesc('id')->value('code');
        $num = 1;
        if ($last && Str::startsWith($last, $prefix)) {
            $tail = (int)preg_replace('/\D/', '', $last);
            $num  = $tail + 1;
        }
        return $prefix . str_pad((string)$num, 6, '0', STR_PAD_LEFT);
    }

    /**
     * حذف ملف أينما كان:
     * - على قرص public
     * - على القرص المعرّف في الإعدادات (إن وُجد)
     * - تحت public_path() و public/storage/...
     */
    protected function deleteFileAnywhere(?string $path): void
    {
        if (!$path) return;

        try {
            $p = ltrim(str_replace(['\\','//'], '/', $path), '/');

            // جرب على أقراص التخزين المعروفة
            $disks = ['public'];
            if (class_exists(\App\models\assets\assetsetting::class)) {
                if ($gs = \App\models\assets\assetsetting::firstRow()) {
                    $d = trim($gs->qr_disk ?: '');
                    if ($d && !in_array($d, $disks, true)) $disks[] = $d;
                }
            }
            foreach ($disks as $disk) {
                if (Storage::disk($disk)->exists($p)) {
                    Storage::disk($disk)->delete($p);
                    return;
                }
                // احتمال أن تُحذف بادئة attachments/
                $alt = preg_replace('/^attachments\//', '', $p);
                if ($alt !== $p && Storage::disk($disk)->exists($alt)) {
                    Storage::disk($disk)->delete($alt);
                    return;
                }
            }

            // محاولات على نظام الملفات مباشرة
            $abs = public_path($p);
            if (@is_file($abs)) { @unlink($abs); return; }

            $abs2 = public_path('storage/'.$p);
            if (@is_file($abs2)) { @unlink($abs2); return; }

            if (@is_file($path)) { @unlink($path); }
        } catch (\Throwable $e) {
            // تجاهل
        }
    }
}
