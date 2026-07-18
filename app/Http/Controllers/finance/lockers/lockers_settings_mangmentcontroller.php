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
use App\models\finance\slocker;
use App\models\finance\glaccount;
use App\models\general\currencies;
use App\models\User;

class lockers_settings_mangmentcontroller extends Controller
{
    public function index()
    {
        $lockers = slocker::with('users', 'currencies')->get();
        $Users = User::all();
        $currencies = currencies::all();
        $glaccounts = glaccount::where('status','1')->get();
        return view('finance.lockers.settings.locker_create.index', compact('lockers', 'currencies', 'Users', 'glaccounts'));
    }

    public function bank_settings()
    {
       
        return view('finance.lockers.settings.bank_settings');
    }
  public function bank_machines()
    {
       
        return view('finance.lockers.settings.bank_machines');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => 'required|in:main,sub',
            'currency' => 'required|exists:currencies_settings,id',
            'glaccounts' => 'required|exists:fin_s_gl_accounts,id',
            'user' => 'nullable|array',
            'user.*' => 'exists:users,id',
            'has_daily_limit' => 'nullable|in:0,1',
        ]);
    
        $locker = slocker::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'type' => $request->type,
            'currency' => $request->currency,
            'gl_account_id' => $request->glaccounts,
            'has_daily_limit' => $request->has_daily_limit ?? false,
            'stauts' => true,
        ]);
    
        if ($request->has('user')) {
            $locker->users()->sync($request->user);
        }
    
        return redirect()->back()->with('success', 'تمت العملية بنجاح');
    }
    

    public function show(FinSLocker $finSLocker)
    {
        return response()->json($finSLocker->load('users'));
    }

    public function edit(FinSLocker $finSLocker)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => 'required|in:main,sub',
            'currency' => 'required|string|max:10',
            'has_daily_limit' => 'string',
        ]);

        $locker = slocker::findOrFail($id);

        $locker->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'type' => $request->type,
            'currency' => $request->currency,
            'gl_account_id' => $request->glaccounts,

            'has_daily_limit' => $request->has_daily_limit,
        ]);

        // تحديث المستخدمين المرتبطين بالخزانة
        if ($request->has('user')) {
            $locker->users()->sync($request->user);
        } else {
            $locker->users()->detach(); // إزالة جميع المستخدمين إذا لم يتم إرسال أي مستخدم
        }

        return redirect()->back()->with('success', 'تم تحديث البيانات بنجاح');
    }
    public function destroy($id)
    {
        $locker = slocker::findOrFail($id);

        // حذف العلاقات مع المستخدمين قبل الحذف
        $locker->users()->detach();

        // حذف الخزنة
        $locker->delete();

        return redirect()->back()->with('success', 'تم الحذف بنجاح');
    }
}