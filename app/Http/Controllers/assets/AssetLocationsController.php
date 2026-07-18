<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\assets\assetlocation;

class AssetLocationsController extends Controller
{
    public function index(Request $r)
    {
        if ($r->wantsJson()) return $this->ajaxIndex($r);
        return view('assets.settings.locations.index');
    }

    public function ajaxIndex(Request $r)
    {
        $q = assetlocation::query()->with('parent');
        $s = $r->get('search')['value'] ?? $r->get('q');
        if ($s) {
            $q->where('name_ar', 'like', "%$s%")->orWhere('name_en', 'like', "%$s%")->orWhere('code', 'like', "%$s%");
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
                'name' => e($x->name_ar ?? $x->name_en),
                'type' => e($x->type),
                'parent' => $x->parent ? e($x->parent->name_ar ?? $x->parent->name_en) : '-',
                'is_active' => $x->is_active ? '<span class="badge bg-success">نشط</span>' : '<span class="badge bg-secondary">متوقف</span>',
                'action' => view('assets.settings.locations._actions', compact('x'))->render(),
            ];
        }
        return response()->json(['data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $total]);
    }

    public function ajaxTree(Request $r)
    {
        $rows = assetlocation::orderBy('parent_id')->orderBy('type')->orderBy('id')->get();
        return response()->json(['results' => $rows->map(fn($x) => [
            'id' => $x->id,
            'text' => ($x->name_ar ?: $x->name_en) . " (" . $x->type . ")",
            'parent' => $x->parent_id ?: '#'
        ])]);
    }

    public function ajaxSelect2(Request $r)
    {
        $s = $r->get('q');
        $rows = assetlocation::when($s, fn($q) => $q->where('name_ar', 'like', "%$s%")
            ->orWhere('name_en', 'like', "%$s%")
            ->orWhere('code', 'like', "%$s%"))
            ->limit(20)->get();
        return response()->json(['results' => $rows->map(fn($x) => ['id' => $x->id, 'text' => $x->name_ar ?: $x->name_en])]);
    }

    public function create()
    {
        $parents = assetlocation::select('id', 'name_ar', 'name_en')->get();
        return view('assets.settings.locations.create', compact('parents'));
    }
    public function store(Request $r){
        $d=$r->validate([
            'parent_id'=>'nullable|integer|exists:asset_locations,id',
            'type'=>'required|in:site,building,floor,room',
            'name_ar'=>'required|max:190','name_en'=>'nullable|max:190',
            'code'=>'required|max:190|unique:asset_locations,code',
            'is_active'=>'nullable|boolean'
        ]);
        $d['is_active']=(bool)($r->is_active??0);
        $row = assetlocation::create($d);

        $action = $r->input('submit_action','back');
        return $action==='stay'
            ? redirect()->route('assets.settings.locations.create')->with('success','تم الحفظ، أضف موقعًا آخر.')
            : redirect()->route('assets.settings.locations.index')->with('success','تم الحفظ.');
    }

    public function show(Request $r, $id){
        $x = assetlocation::with('parent')->findOrFail($id);
        if ($r->ajax() || $r->wantsJson() || $r->query('modal')) {
            $html = view('assets.settings.locations._show', compact('x'))->render();
            return response()->json(['html'=>$html]);
        }
        return view('assets.settings.locations.show', compact('x'));
    }

    public function edit($id){ $x=assetlocation::findOrFail($id); $parents=assetlocation::where('id','<>',$id)->get(); return view('assets.settings.locations.edit', compact('x','parents')); }

    public function update(Request $r,$id){
        $x=assetlocation::findOrFail($id);
        $d=$r->validate([
            'parent_id'=>'nullable|integer|exists:asset_locations,id',
            'type'=>'required|in:site,building,floor,room',
            'name_ar'=>'required|max:190','name_en'=>'nullable|max:190',
            'code'=>'required|max:190|unique:asset_locations,code,'.$x->id,
            'is_active'=>'nullable|boolean'
        ]);
        $d['is_active']=(bool)($r->is_active??0);
        $x->update($d);

        $action = $r->input('submit_action','back');
        return $action==='stay'
            ? redirect()->route('assets.settings.locations.edit',$x->id)->with('success','تم التحديث وبقيت هنا.')
            : redirect()->route('assets.settings.locations.index')->with('success','تم التحديث.');
    }

    public function destroy($id){
        assetlocation::findOrFail($id)->delete();
        if(request()->wantsJson()) return response()->json(['ok'=>true,'message'=>'تم الحذف.']);
        return back()->with('success','تم الحذف');
    }
}
