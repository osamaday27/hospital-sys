<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\supply_chain\storemaindata;


class store_main_datacontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stores = storemaindata::select('id', 'manual_code', 'name_ar', 'store_type', 'status');

            return datatables()->of($stores)
                ->addColumn('action', function ($row) {
                    $editUrl = route('store_main_data.create', ['id' => $row->id, 'tab' => 'tab1']);
                    return '<a href="'.$editUrl.'" class="btn btn-sm btn-warning">'. __('general.edit') .'</a>
                            <button data-id="'.$row->id.'" class="btn btn-sm btn-danger delete-btn">'. __('general.delete') .'</button>';
                })
                ->editColumn('status', function ($row) {
                    return $row->status === 'active' ? __('supply_chain_trans.active') : __('supply_chain_trans.inactive');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('supply_chain.settings.store_main_data.index');
    }
    public function create()
    {

        return view('supply_chain.settings.store_main_data.create');
    }
    public function destroy($id)
{
    try {
        $store = storemaindata::findOrFail($id);

        $store->delete();

        return response()->json([
            'success' => true,
            'message' => __('supply_chain_trans.store_deleted_successfully')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => __('supply_chain_trans.store_delete_failed')
        ]);
    }

}
}
