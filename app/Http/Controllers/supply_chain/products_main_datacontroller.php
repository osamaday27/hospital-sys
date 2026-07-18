<?php

namespace App\Http\Controllers\supply_chain;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\supply_chain\productdata;


class products_main_datacontroller extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = productdata::with('category')->select('products_data.*');
            return datatables()->of($products)
                ->addColumn('action', function ($row) {
                    $editUrl = route('products_main_data.edit', $row->id);
                    $btn  = '<a href="'.$editUrl.'" class="btn btn-sm btn-warning">'. __('general.edit') .'</a> ';
                    $btn .= '<button data-id="'.$row->id.'" class="btn btn-sm btn-danger delete-btn">'. __('general.delete') .'</button>';
                    return $btn;
                })
                ->editColumn('status', function ($row) {
                    return $row->status == 'active' ? __('supply_chain_trans.active') : __('supply_chain_trans.inactive');
                })
                ->editColumn('product_category', function ($row) {
                    return $row->category ? $row->category->name_ar : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('supply_chain.settings.products_main_data.index');
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {

        return view('supply_chain.settings.products_main_data.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
        ]);

        productdata::create($request->all());

        return redirect()->route('products_main_data.index')->with('success', __('supply_chain_trans.created_successfully'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = productdata::findOrFail($id);
        return view('supply_chain.settings.products_main_data.create', compact('product'));
    }


    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
        ]);

        $product = productdata::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products_main_data.index')->with('success', __('supply_chain_trans.updated_successfully'));
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = productdata::findOrFail($id);
        $product->delete();

        return response()->json(['success' => true, 'message' => __('general.deleted_successfully')]);
    }
     /**
     * مسار AJAX للبحث عن الأصناف (Select2)
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');

        $products = productdata::query()
            ->where('name_ar', 'like', "%{$term}%")
            ->orWhere('name_en', 'like', "%{$term}%")
            ->orWhere('system_code', 'like', "%{$term}%")
            ->limit(50)
            ->get();

        $results = $products->map(function($p) {
            $unit = $p->unit;

            return [
                'id'                     => $p->id,
                'code'                   => $p->system_code,                   // كود الصنف
                'name_ar'                => $p->name_ar,                       // الاسم
                'pending_qty'            => $p->pending_qty ?? 0,             // الكميات تحت الشراء (إن كنت تحتفظ بها)
                'current_balance'        => $p->current_balance  ?? 0,         // الرصيد الحالي للمخزن
                'other_stores_balance'   => $p->other_stores_balance ?? 0,     // الرصيد في مخازن أخرى
                'last_price'             => $p->last_price ?? 0,               // آخر سعر
                'unit_id'                => $p->unit_id,                       // معرّف الوحدة
                'unit_name'              => $unit?->name_ar  ?? '',            // اسم الوحدة للعربي
                'notes'                  => '',                                 // يمكنك إرفاق ملاحظات افتراضية هنا
            ];
        });

        return response()->json($results);
    }
}
