<?php

namespace App\Http\Controllers\purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\purchases\purchasesetting;
use App\Models\purchases\evaluationitem;
use App\Models\purchases\deliveryplace;
use App\Models\purchases\closurereason;

class purchases_system_settingscontroller extends Controller
{
    public function index()
    {
        $settings = purchasesetting::first();
        return view('purchases.settings.system_settings.index', compact('settings'));
    }

    public function saveSettings(Request $r)
    {
        $data = $r->validate([
            'has_max_limit'           => 'required|boolean',
            'max_limit_value'         => 'nullable|numeric',
            'use_last_supplier_price' => 'required|boolean',
            'use_sale_price'          => 'required|boolean',
        ]);
        $s = purchasesetting::first() ?: new purchasesetting();
        $s->fill($data)->save();
        return response()->json(['message'=>trans('general.saved_successfully')]);
    }

    // --- EvaluationItems CRUD ---
    public function evaluationItemsList()
    {
        return response()->json(['data'=>evaluationitem::orderBy('id','desc')->get()]);
    }
    public function evaluationItemsStore(Request $r)
    {
        $data = $r->validate([
            'name_ar'=>'required|string',
            'name_en'=>'required|string',
            'status'=>'required|in:active,inactive',
        ]);
        EvaluationItem::create($data);
        return response()->json(['message'=>trans('general.saved_successfully')]);
    }
    public function evaluationItemsShow($id)
    {
        return response()->json(EvaluationItem::findOrFail($id));
    }
    public function evaluationItemsUpdate(Request $r, $id)
    {
        $data = $r->validate([
            'name_ar'=>'required|string',
            'name_en'=>'required|string',
            'status'=>'required|in:active,inactive',
        ]);
        $item = EvaluationItem::findOrFail($id);
        $item->update($data);
        return response()->json(['message'=>trans('general.saved_successfully')]);
    }
    public function evaluationItemsDestroy($id)
    {
        EvaluationItem::findOrFail($id)->delete();
        return response()->json(['message'=>trans('general.deleted_successfully')]);
    }

    // --- DeliveryPlaces CRUD ---
    public function deliveryPlacesList()
    {
        return response()->json(['data'=>deliveryplace::orderBy('id','desc')->get()]);
    }
    public function deliveryPlacesStore(Request $r)
    {
        $data = $r->validate([
            'name_ar'=>'required|string',
            'name_en'=>'required|string',
            'status'=>'required|in:active,inactive',
        ]);
        deliveryplace::create($data);
        return response()->json(['message'=>trans('general.saved_successfully')]);
    }
    public function deliveryPlacesShow($id)
    {
        return response()->json(deliveryplace::findOrFail($id));
    }
    public function deliveryPlacesUpdate(Request $r, $id)
    {
        $data = $r->validate([
            'name_ar'=>'required|string',
            'name_en'=>'required|string',
            'status'=>'required|in:active,inactive',
        ]);
        $place = deliveryplace::findOrFail($id);
        $place->update($data);
        return response()->json(['message'=>trans('general.saved_successfully')]);
    }
    public function deliveryPlacesDestroy($id)
    {
        deliveryplace::findOrFail($id)->delete();
        return response()->json(['message'=>trans('general.deleted_successfully')]);
    }

    // --- ClosureReasons CRUD ---
    public function closureReasonsList()
    {
        return response()->json(['data'=>closurereason::orderBy('id','desc')->get()]);
    }
    public function closureReasonsStore(Request $r)
    {
        $data = $r->validate([
            'name_ar'=>'required|string',
            'name_en'=>'required|string',
            'status'=>'required|in:active,inactive',
        ]);
        closurereason::create($data);
        return response()->json(['message'=>trans('general.saved_successfully')]);
    }
    public function closureReasonsShow($id)
    {
        return response()->json(closurereason::findOrFail($id));
    }
    public function closureReasonsUpdate(Request $r, $id)
    {
        $data = $r->validate([
            'name_ar'=>'required|string',
            'name_en'=>'required|string',
            'status'=>'required|in:active,inactive',
        ]);
        $reason = closurereason::findOrFail($id);
        $reason->update($data);
        return response()->json(['message'=>trans('general.saved_successfully')]);
    }
    public function closureReasonsDestroy($id)
    {
        closurereason::findOrFail($id)->delete();
        return response()->json(['message'=>trans('general.deleted_successfully')]);
    }
}
