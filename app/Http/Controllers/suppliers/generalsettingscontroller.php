<?php

namespace App\Http\Controllers\Suppliers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\suppliers\suppliersnotification;
use App\Models\suppliers\suppliergeneralsetting;


class GeneralSettingsController extends Controller
{
    public function index()
    {
        $settings = suppliergeneralsetting::first();
        return view('suppliers.settings.general_settings.index', compact('settings'));
    }

    public function saveGeneral(Request $r)
    {
        $data = $r->validate([
            'notify_return'            => 'required|boolean',
            'notify_service_invoice'   => 'required|boolean',
            'notify_supply_invoice'    => 'required|boolean',
            'rounding_value'           => 'nullable|numeric',
        ]);

        $settings = suppliergeneralsetting::first() ?: new suppliergeneralsetting();
        $settings->notify_return_from_warehouse       = $data['notify_return'];
        $settings->notify_discount_on_service_invoice = $data['notify_service_invoice'];
        $settings->notify_discount_on_supply_invoice  = $data['notify_supply_invoice'];
        // القيمة الافتراضية 0 إذا كان الحقل فارغ
        $settings->rounding_allowed_value             = $data['rounding_value'] ?? 0;
        $settings->save();

        return response()->json([
            'message' => trans('general.saved_successfully')
        ]);
    }

    public function listNotifications()
    {
        $settings = suppliergeneralsetting::first();
        $notifs = $settings
            ? $settings->notifications()->orderBy('id','desc')->get()
            : collect();
        return response()->json(['data' => $notifs]);
    }

  public function saveNotification(Request $r)
    {
        $data = $r->validate([
            'label'       => 'required|string|max:255',
            'value'       => 'required|numeric',
            'value_type'  => 'required|in:fixed,percentage',
            'action_type' => 'required|in:discount,addition',
            'category'    => 'required|in:tax,additional,collection_tax,invoice_expense,freelance_services,stamp_duty,income_tax',
        ]);

        $settings = suppliergeneralsetting::firstOrFail();

        $notif = $r->isMethod('put')
            ? suppliersnotification::findOrFail($r->route('notification'))
            : new suppliersnotification;

        $notif->general_setting_id = $settings->id;
        $notif->fill($data);
        $notif->save();

        return response()->json([
            'message' => trans('general.saved_successfully')
        ]);
    }

    public function showNotification($id)
    {
        $notif = suppliersnotification::findOrFail($id);
        return response()->json($notif);
    }

    public function deleteNotification($id)
    {
        suppliersnotification::findOrFail($id)->delete();
        return response()->json([
            'message' => trans('general.deleted_successfully')
        ]);
    }
}