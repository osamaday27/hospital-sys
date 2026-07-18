<?php
namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\assets\assetcategory;
use Illuminate\Support\Facades\DB;

class AssetCategoriesController extends Controller
{
    public function index(Request $req){
        if ($req->wantsJson()) return $this->ajaxIndex($req);
        return view('assets.settings.categories.index');
    }

    public function ajaxIndex(Request $req){
        $q = assetcategory::query()->with('parent');
        $search = $req->get('search')['value'] ?? $req->get('q');
        if ($search) {
            $q->where(function($x) use ($search){
                $x->where('name_ar','like',"%$search%")
                  ->orWhere('name_en','like',"%$search%")
                  ->orWhere('code','like',"%$search%");
            });
        }
        $total = $q->count();
        $start = (int)($req->get('start',0));
        $len   = (int)($req->get('length',10));
        $rows  = $q->orderByDesc('id')->skip($start)->take($len)->get();

        $data = [];
        $idx  = $start+1;
        foreach($rows as $r){
            $data[] = [
                'DT_RowIndex' => $idx++,
                'code' => e($r->code),
                'name' => e($r->name_ar ?? $r->name_en),
                'parent'=> $r->parent? e($r->parent->name_ar ?? $r->parent->name_en) : '-',
                'is_active' => $r->is_active ? '<span class="badge bg-success">نشط</span>' : '<span class="badge bg-secondary">متوقف</span>',
                'action' => view('assets.settings.categories._actions', compact('r'))->render(),
            ];
        }
        return response()->json([
            'data'=>$data,
            'recordsTotal'=>$total,
            'recordsFiltered'=>$total,
        ]);
    }

    public function ajaxSelect2(Request $req){
        $search = $req->get('q');
        $rows = assetcategory::where('is_active',1)
            ->when($search, fn($x)=>$x->where('name_ar','like',"%$search%")
                                      ->orWhere('name_en','like',"%$search%")
                                      ->orWhere('code','like',"%$search%"))
            ->limit(20)->get();
        return response()->json(['results'=>$rows->map(fn($r)=>['id'=>$r->id,'text'=>$r->name_ar ?: $r->name_en ?: $r->code])]);
    }

    public function create(){
        $parents = assetcategory::select('id','name_ar','name_en')->get();
        return view('assets.settings.categories.create', compact('parents'));
    }

    public function store(Request $req){
        $data = $req->validate([
            'name_ar'=>'required|string|max:190',
            'name_en'=>'nullable|string|max:190',
            'code'=>'required|string|max:190|unique:asset_categories,code',
            'parent_id'=>'nullable|integer|exists:asset_categories,id',
            'useful_life_months'=>'nullable|integer|min:0',
            'default_dep_method'=>'nullable|in:SL,DDB,UOP',
            'default_dep_rate'=>'nullable|numeric|min:0',
            'is_active'=>'nullable|boolean',
        ]);
        $data['is_active']=(bool)($req->is_active??0);
        $row = assetcategory::create($data);

        $action = $req->input('submit_action','back'); // stay | back
        if ($action === 'stay') {
            return redirect()->route('assets.settings.categories.create')->with('success','تم الحفظ، يمكنك إدخال سجل جديد.');
        }
        return redirect()->route('assets.settings.categories.index')->with('success','تم الحفظ بنجاح.');
    }

    public function show(Request $req, $id){
        $r = assetcategory::with('parent')->findOrFail($id);
        // للعرض داخل مودال عبر AJAX
        if ($req->ajax() || $req->wantsJson() || $req->query('modal')) {
            $html = view('assets.settings.categories._show', compact('r'))->render();
            return response()->json(['html'=>$html]);
        }
        return view('assets.settings.categories.show', compact('r'));
    }

    public function edit($id){
        $r = assetcategory::findOrFail($id);
        $parents = assetcategory::where('id','<>',$id)->get(['id','name_ar','name_en']);
        return view('assets.settings.categories.edit', compact('r','parents'));
    }

    public function update(Request $req, $id){
        $r = assetcategory::findOrFail($id);
        $data = $req->validate([
            'name_ar'=>'required|string|max:190',
            'name_en'=>'nullable|string|max:190',
            'code'=>'required|string|max:190|unique:asset_categories,code,'.$r->id,
            'parent_id'=>'nullable|integer|exists:asset_categories,id',
            'useful_life_months'=>'nullable|integer|min:0',
            'default_dep_method'=>'nullable|in:SL,DDB,UOP',
            'default_dep_rate'=>'nullable|numeric|min:0',
            'is_active'=>'nullable|boolean',
        ]);
        $data['is_active']=(bool)($req->is_active??0);
        $r->update($data);

        $action = $req->input('submit_action','back');
        if ($action === 'stay') {
            return redirect()->route('assets.settings.categories.edit', $r->id)->with('success','تم التحديث وبقيت في نفس الصفحة.');
        }
        return redirect()->route('assets.settings.categories.index')->with('success','تم التحديث.');
    }

    public function destroy($id){
        $r = assetcategory::findOrFail($id);
        $r->delete();
        if (request()->wantsJson()) return response()->json(['ok'=>true,'message'=>'تم الحذف.']);
        return redirect()->back()->with('success','تم الحذف.');
    }
}
