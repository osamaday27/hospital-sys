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
use App\models\finance\cancellationreason;
use App\models\general\currencies;
use App\models\User;

class cancellationreasoncontroller extends Controller
{
   public function index()
    {
        $reasons = cancellationreason::orderBy('id', 'desc')->get();
         return view('finance.lockers.settings.cancellationreason.index', compact('reasons'));
    }

    public function create()
    {
        return view('cancellation_reasons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255|unique:fin_s_cancellation_reasons,name_ar',
            'name_en' => 'required|string|max:255|unique:fin_s_cancellation_reasons,name_en',
            'status' => 'required|in:active,inactive',
        ]);

        CancellationReason::create($request->all());

        return redirect()->route('cancellationreason.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit(CancellationReason $cancellation_reason)
    {
        return view('cancellationreason.edit', compact('cancellation_reason'));
    }

    public function update(Request $request, cancellationreason $cancellation_reason)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255|unique:fin_s_cancellation_reasons,name_ar,' . $cancellation_reason->id,
            'name_en' => 'required|string|max:255|unique:fin_s_cancellation_reasons,name_en,' . $cancellation_reason->id,
            'status' => 'required|in:active,inactive',
        ]);

        $CancellationReason->update($request->all());

        return redirect()->route('cancellationreason.index')->with('update', 'تم التحديث بنجاح');
    }

    public function destroy(cancellationreason $cancellation_reason)
    {
        $cancellation_reason->delete();

        return redirect()->route('cancellationreason.index')->with('delete', 'تم الحذف بنجاح');
    }

}