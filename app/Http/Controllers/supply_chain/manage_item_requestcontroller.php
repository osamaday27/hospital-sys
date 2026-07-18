<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\supply_chain\ItemRequest;
use App\Models\supply_chain\ItemRequestDetail;
use App\Models\supply_chain\storeproductmodel;
use Illuminate\Support\Facades\Auth;

class manage_item_requestcontroller extends Controller
{
    // 1. عرض القائمة (نشطة أو سابقة حسب الفلتر)
    public function index(Request $request)
    {
        $previous = $request->get('previous') == 1;

        if ($previous) {
            // الطلبات التي لم يعد فيها بنود قيد انتظار
            $requests = ItemRequest::with('user')
                ->whereDoesntHave('details', fn($q) => $q->where('status','pending'))
                ->latest()
                ->get();
        } else {
            // الطلبات التي لا تزال فيها بنود قيد انتظار
            $requests = ItemRequest::with('user')
                ->whereHas('details', fn($q) => $q->where('status','pending'))
                ->latest()
                ->get();
        }

        return view('supply_chain.program.manage_item_request.index', compact('requests','previous'));
    }

    // 2. جلب بنود الطلب المفتوح (pending فقط)
    public function details(Request $request, ItemRequest $manage_item_request)
    {
        $query = $manage_item_request
            ->details()
            ->with('item');

        if (! $request->boolean('all')) {
            $query->where('status','pending');
        }

        $details = $query->get();

        return response()->json(
            $details->map(fn($d) => [
                'id'      => $d->id,
                'name_ar' => $d->item->name_ar,
                'name_en' => $d->item->name_en,
                'status'  => $d->status,
            ])
        );
    }


    // 3. تعديل حالة بند واحد
   public function updateDetailStatus(Request $request, ItemRequestDetail $detail)
{
    $data = $request->validate([
        'status' => 'required|in:approved,rejected',
        'notes'  => 'nullable|string',
    ]);

    // نحدّث حالة البند
    $detail->update([
        'status' => $data['status'],
        'notes'  => $data['notes'] ?? null,
    ]);

    // عند الموافقة: نضيف الصنف إلى المخزن
    if ($data['status'] === 'approved') {
        storeproductmodel::firstOrCreate([
            'store_id'   => $detail->request->store_id,
            'product_id' => $detail->item_id,
        ],[
            'status' => 'active',
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => __('supply_chain_trans.detail_status_updated'),
    ]);
}
}
