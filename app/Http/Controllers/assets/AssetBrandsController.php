<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\assets\assetbrand;

class AssetBrandsController extends Controller
{
    public function index(Request $r)
    {
        if ($r->wantsJson()) return $this->ajaxIndex($r);
        return view('assets.settings.brands.index');
    }
    public function ajaxIndex(Request $r)
    {
        $q = assetbrand::query();
        $s = $r->get('search')['value'] ?? $r->get('q');
        if ($s) {
            $q->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%");
        }
        $total = $q->count();
        $start = (int)$r->get('start', 0);
        $len = (int)$r->get('length', 10);
        $rows = $q->orderByDesc('id')->skip($start)->take($len)->get();
        $data = [];
        $i = $start + 1;
        foreach ($rows as $x) {
            $data[] = [
                'DT_RowIndex' => $i++,
                'code' => e($x->code),
                'name' => e($x->name),
                'is_active' => $x->is_active ? '<span class="badge bg-success">نشط</span>' : '<span class="badge bg-secondary">متوقف</span>',
                'action' => view('assets.settings.brands._actions', compact('x'))->render(),
            ];
        }
        return response()->json(['data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $total]);
    }
    public function ajaxSelect2(Request $r)
    {
        $s = $r->get('q');
        $rows = assetbrand::when($s, fn($q) => $q->where('name', 'like', "%$s%") || $q->where('code', 'like', "%$s%"))->limit(20)->get();
        return response()->json(['results' => $rows->map(fn($x) => ['id' => $x->id, 'text' => $x->name])]);
    }
    public function create()
    {
        return view('assets.settings.brands.create');
    }
    public function store(Request $r){
        $d=$r->validate(['name'=>'required|max:190','code'=>'required|max:190|unique:asset_brands,code','is_active'=>'nullable|boolean']);
        $d['is_active']=(bool)($r->is_active??0);
        $row = assetbrand::create($d);

        $action = $r->input('submit_action','back');
        return $action==='stay'
            ? redirect()->route('assets.settings.brands.create')->with('success','تم الحفظ، أضف ماركة أخرى.')
            : redirect()->route('assets.settings.brands.index')->with('success','تم الحفظ.');
    }

    public function show(Request $r, $id){
        $x = assetbrand::findOrFail($id);
        if ($r->ajax() || $r->wantsJson() || $r->query('modal')) {
            $html = view('assets.settings.brands._show', compact('x'))->render();
            return response()->json(['html'=>$html]);
        }
        return view('assets.settings.brands.show', compact('x'));
    }

    public function edit($id){ $x=assetbrand::findOrFail($id); return view('assets.settings.brands.edit', compact('x')); }

    public function update(Request $r,$id){
        $x=assetbrand::findOrFail($id);
        $d=$r->validate(['name'=>'required|max:190','code'=>'required|max:190|unique:asset_brands,code,'.$x->id,'is_active'=>'nullable|boolean']);
        $d['is_active']=(bool)($r->is_active??0);
        $x->update($d);

        $action = $r->input('submit_action','back');
        return $action==='stay'
            ? redirect()->route('assets.settings.brands.edit',$x->id)->with('success','تم التحديث وبقيت هنا.')
            : redirect()->route('assets.settings.brands.index')->with('success','تم التحديث.');
    }

    public function destroy($id){
        assetbrand::findOrFail($id)->delete();
        if(request()->wantsJson()) return response()->json(['ok'=>true,'message'=>'تم الحذف.']);
        return back()->with('success','تم الحذف');
    }
}

