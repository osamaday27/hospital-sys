<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\assets\assetmodel;
use App\models\assets\assetbrand;

class AssetModelsController extends Controller
{
    public function index(Request $r)
    {
        if ($r->wantsJson()) return $this->ajaxIndex($r);
        return view('assets.settings.models.index');
    }
 public function ajaxIndex(Request $r)
    {
        $q = assetmodel::query()->with('brand');

        if ($s = ($r->get('search')['value'] ?? $r->get('q'))) {
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%$s%")
                  ->orWhere('code', 'like', "%$s%");
            });
        }

        $total = $q->count();
        $start = (int)$r->get('start', 0);
        $len   = (int)$r->get('length', 10);

        $rows = $q->orderByDesc('id')->skip($start)->take($len)->get();
        $data = [];
        $i = $start + 1;
        foreach ($rows as $x) {
            $data[] = [
                'DT_RowIndex' => $i++,
                'code'        => e($x->code),
                'name'        => e($x->name),
                'brand'       => $x->brand ? e($x->brand->name) : '-',
                'is_active'   => $x->is_active
                    ? '<span class="badge bg-success">نشط</span>'
                    : '<span class="badge bg-secondary">متوقف</span>',
                'action'      => view('assets.settings.models._actions', compact('x'))->render(),
            ];
        }

        return response()->json([
            'data'            => $data,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total
        ]);
    }
public function ajaxSelect2(Request $r)
    {
        $s = $r->get('q');

        $rows = assetmodel::when($s, function ($q) use ($s) {
                $q->where(function ($w) use ($s) {
                    $w->where('name', 'like', "%$s%")
                      ->orWhere('code', 'like', "%$s%");
                });
            })
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $rows->map(fn($x) => ['id' => $x->id, 'text' => $x->name])
        ]);
    }

 public function create()
    {
        $brands = assetbrand::select('id', 'name')->get();
        return view('assets.settings.models.create', compact('brands'));
    }

    public function store(Request $r)
    {
        $brandsTable = (new assetbrand)->getTable();

        $d = $r->validate([
            'brand_id'  => "required|integer|exists:{$brandsTable},id",
            'name'      => 'required|max:190',
            'code'      => 'required|max:190|unique:asset_models,code',
            'is_active' => 'nullable|boolean',
        ]);

        $d['is_active'] = $r->boolean('is_active');

        $row = assetmodel::create($d);

        $action = $r->input('submit_action', 'back');
        return $action === 'stay'
            ? redirect()->route('assets.settings.models.create')->with('success','تم الحفظ، أضف ماركة أخرى.')
            : redirect()->route('assets.settings.models.index')->with('success','تم الحفظ.');
    }

    public function edit($id)
    {
        $x = assetmodel::findOrFail($id);
        $brands = assetbrand::select('id','name')->get(); // مهم للـ partial
        return view('assets.settings.models.edit', compact('x','brands'));
    }

    public function update(Request $r, $id)
    {
        $x = assetmodel::findOrFail($id);
        $brandsTable = (new assetbrand)->getTable();

        $d = $r->validate([
            'brand_id'  => "required|integer|exists:{$brandsTable},id",
            'name'      => 'required|max:190',
            'code'      => 'required|max:190|unique:asset_models,code,'.$x->id,
            'is_active' => 'nullable|boolean',
        ]);

        $d['is_active'] = $r->boolean('is_active');

        $x->update($d);

        $action = $r->input('submit_action', 'back');
        return $action === 'stay'
            ? redirect()->route('assets.settings.models.edit', $x->id)->with('success','تم التحديث وبقيت هنا.')
            : redirect()->route('assets.settings.models.index')->with('success','تم التحديث.');
    }


    public function show(Request $r, $id){
        $x = assetmodel::findOrFail($id);
        if ($r->ajax() || $r->wantsJson() || $r->query('modal')) {
            $html = view('assets.settings.models._show', compact('x'))->render();
            return response()->json(['html'=>$html]);
        }
        return view('assets.settings.models.show', compact('x'));
    }



    public function destroy($id){
        assetmodel::findOrFail($id)->delete();
        if(request()->wantsJson()) return response()->json(['ok'=>true,'message'=>'تم الحذف.']);
        return back()->with('success','تم الحذف');
    }
}

