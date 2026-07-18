<?php
namespace App\Http\Controllers\finance\lockers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\finance\reasonspartialpayment;
use App\models\general\currencies;
use App\models\User;

class reasonspartialpaymentcontroller extends Controller
{
   public function index()
    {
        $reasons = reasonspartialpayment::orderBy('id', 'desc')->get();
         return view('finance.lockers.settings.reasonspartialpayment.index', compact('reasons'));
    }

    public function create()
    {
        return view('reasonspartialpayments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255|unique:fin_s_reasonspartialpayment,name_ar',
            'name_en' => 'required|string|max:255|unique:fin_s_reasonspartialpayment,name_en',
            'status' => 'required|in:active,inactive',
        ]);

        reasonspartialpayment::create($request->all());

        return redirect()->route('reasonspartialpayment.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit(reasonspartialpayment $reasonspartialpayment)
    {
        return view('reasonspartialpayment.edit', compact('reasonspartialpayment'));
    }

    public function update(Request $request, reasonspartialpayment $reasonspartialpayment)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255|unique:fin_s_reasonspartialpayment,name_ar,' . $reasonspartialpayment->id,
            'name_en' => 'required|string|max:255|unique:fin_s_reasonspartialpayment,name_en,' . $reasonspartialpayment->id,
            'status' => 'required|in:active,inactive',
        ]);

        $reasonspartialpayment->update($request->all());

        return redirect()->route('reasonspartialpayment.index')->with('updated', 'تم التحديث بنجاح');
    }

    public function destroy(reasonspartialpayment $reasonspartialpayment)
    {
        $reasonspartialpayment->delete();

        return redirect()->route('reasonspartialpayment.index')->with('deleted', 'تم الحذف بنجاح');
    }

}