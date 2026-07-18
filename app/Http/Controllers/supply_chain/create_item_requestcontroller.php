<?php

namespace App\Http\Controllers\supply_chain;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\supply_chain\ItemRequest;
use App\Models\supply_chain\ProductData;
use App\Models\supply_chain\StoreMainData;
use App\Models\supply_chain\ProductCategory;
use App\Models\supply_chain\StoreKeeperModel;
use App\Models\supply_chain\ItemRequestDetail;
use Illuminate\Support\Facades\Schema;


class create_item_requestcontroller extends Controller
{
    // عرض قائمة الطلبات
    public function index()
    {
        $requests = ItemRequest::with('user')->latest()->get();
        return view('supply_chain.program.create_item_request.index', compact('requests'));
    }

    // صفحة إنشاء طلب جديد
    public function create()
    {
        // رقم الطلب التالي تلقائيًا
        $count = ItemRequest::withTrashed()->count() + 1;
        $requestNumber = 'REQ-' . now()->format('Ymd') . '-' . sprintf('%03d', $count);

        // مخازن المستخدم
        $storeIds = StoreKeeperModel::where('user_id', Auth::id())
                       ->pluck('store_id');
        $stores = StoreMainData::whereIn('id', $storeIds)->get();

        // مجموعات الأصناف
        $groups = ProductCategory::select('id', 'name_ar', 'name_en')->get();

        return view('supply_chain.program.create_item_request.create', compact(
            'requestNumber', 'stores', 'groups'
        ));
    }

    // حفظ الطلب الجديد
    public function store(Request $request)
    {
        $data = $request->validate([
            'request_number' => 'required|unique:item_requests,request_number',
            'store_id'       => 'required|exists:store_main_date,id',
            'items'          => 'required|array|min:1',
            'items.*'        => 'exists:products_data,id',
        ]);

        // إنشاء الطلب
        $itemReq = ItemRequest::create([
            'request_number' => $data['request_number'],
            'request_date'   => now(),
            'user_id'        => Auth::id(),
            'store_id'       => $data['store_id'],
        ]);

        // تفاصيل الطلب
        foreach ($data['items'] as $itemId) {
            ItemRequestDetail::create([
                'item_request_id' => $itemReq->id,
                'item_id'         => $itemId,
                'status'          => 'pending',
            ]);
        }

        return redirect()->route('create_item_request.index')
                         ->with('success', 'تم إنشاء طلب الإضافة بنجاح');
    }

//     // API: جلب الأصناف حسب المجموعة
public function getItems(Request $request)
{
    $categoryId = $request->input('category_id');
    $storeId    = $request->input('store_id');

    // جلب IDs الأصناف الموجودة مسبقاً
    $existing = [];
    if (Schema::hasTable('store_products')) {
        $existing = DB::table('store_products')
                      ->where('store_id', $storeId)
                      ->pluck('product_id')
                      ->toArray();
    }

    // جلب كل الأصناف في الفئة مع علامة exists_in_store
    $items = ProductData::where('product_category_id', $categoryId)
                ->select('id','name_ar','name_en')
                ->get()
                ->map(function($it) use($existing) {
                    $it->exists_in_store = in_array($it->id, $existing) ? 1 : 0;
                    return $it;
                });

    return response()->json($items);
}


    // صفحة التعديل (عرض التفاصيل)
    public function edit(ItemRequest $item_request)
    {
        $storeIds = StoreKeeperModel::where('user_id', Auth::id())->pluck('store_id');
        $stores = StoreMainData::whereIn('id', $storeIds)->get();
        $groups = ProductCategory::select('id', 'name_ar', 'name_en')->get();
        $details = $item_request->details()->with('item')->get();

        return view('supply_chain.program.create_item_request.edit', compact(
            'item_request','stores','groups','details'
        ));
    }

    // تحديث الطلب
    public function update(Request $request, ItemRequest $item_request)
    {
        $data = $request->validate([
            'store_id'       => 'required|exists:store_main_date,id',
            'items'          => 'required|array|min:1',
            'items.*'        => 'exists:products_data,id',
        ]);

        // تحديث البيانات الأساسية
        $item_request->update([
            'store_id' => $data['store_id'],
        ]);

        // إعادة إنشاء التفاصيل
        $item_request->details()->delete();
        foreach ($data['items'] as $itemId) {
            ItemRequestDetail::create([
                'item_request_id' => $item_request->id,
                'item_id'         => $itemId,
                'status'          => 'pending',
            ]);
        }

        return redirect()->route('create_item_request.index')
                         ->with('success', 'تم تعديل الطلب بنجاح');
    }

    // حذف الطلب
public function destroy(ItemRequest $item_request)
{
    $item_request->delete();

    // إذا كان الطلب عبر AJAX، نرجع JSON
    if (request()->ajax()) {
        return response()->json([
            'success' => true,
            'message' => __('تم حذف الطلب بنجاح')
        ]);
    }

    // خلاف ذلك، إعادة توجيه مع رسالة
    return redirect()
        ->route('create_item_request.index')
        ->with('success', __('تم حذف الطلب بنجاح'));
}
}
