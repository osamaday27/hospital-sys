<?php

namespace App\Http\Controllers\finance\pricing_management;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\finance\price_lists;
use App\models\general\currencies;
use App\models\general\branch;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class pricelistcontroller extends Controller
{
    public function index()
    {
        return view('finance.pricing_contracts_management.pricing_management.price_lists.index');
    }

    public function create()
    {
        $currencys = currencies::all();
        $branches = branch::all();
        return view('finance.pricing_contracts_management.pricing_management.price_lists.create', compact('currencys', 'branches'));
    }

    public function store(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $request->validate([
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'fiscal_year' => 'required|digits:4',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'list_type' => 'nullable|string|max:50',
                'default_branch' => 'nullable|exists:branches,id',
            ]);

            // إنشاء سجل جديد
            $priceList = new price_lists();
            $priceList->name = ['ar' => $request->name_ar, 'en' => $request->name_en];
            $priceList->fiscal_year = $request->fiscal_year;
            $priceList->start_date = $request->start_date;
            $priceList->end_date = $request->end_date;
            $priceList->list_type = $request->list_type;
            $priceList->status = $request->status ?? 'active';
            $priceList->branches = $request->branches;
            $priceList->default_branch = $request->default_branch ?? '1';
            $priceList->currency = $request->currency ?? 'NULL';

            $priceList->user_id = Auth::id();
            $priceList->save();

            // رسالة النجاح
            session()->flash('add', trans('pricing_list.created_success'));
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $priceList = price_lists::findOrFail($id);
        $currencys = currencies::all();
        $branches = branch::all();
        return view('finance.pricing_contracts_management.pricing_management.price_lists.edit', compact('priceList', 'branches', 'currencys'));
    }

    public function update(Request $request, $id)
    {
       try {
        $priceList = price_lists::findOrFail($id);
        // التحقق من صحة البيانات
           $request->validate([
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'fiscal_year' => 'required|digits:4',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'list_type' => 'nullable|string|max:50',
                'default_branch' => 'nullable|exists:branches,id',
            ]);
        // تحديث السجل
        $priceList->update([
            'name' => ['ar' => $request->name_ar, 'en' => $request->name_en],
            'fiscal_year' => $request->fiscal_year,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'list_type' => $request->list_type,
            'status' => $request->status,
            'branches' => $request->branches,
            'user_id' => Auth::id(),
        ]);

        session()->flash('add', trans('price_list.updated_success'));
        return redirect()->back();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
{
    $price_lists = price_lists::find($id);

    if ($price_lists) {
        // التحقق إذا كانت مرتبطة بجدول orders
        if ($price_lists->parentList()->exists()) {  
            return response()->json([
                'status' => 'success',
                'message' => 'This price lists is related to orders and cannot be deleted.'
            ], 302);
        }

        // إذا لم تكن هناك سجلات مرتبطة
        $price_lists->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'price lists deleted successfully'
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'price lists not found'
    ], 404);
}

   public function getPriceLists()
{
    $priceLists = price_lists::with(['branch', 'getcurrency', 'createdBy']) // جلب العلاقات المطلوبة
        ->select(['id', 'name', 'fiscal_year', 'start_date', 'end_date', 'status', 'branches', 'currency', 'user_id', 'list_type', 'created_at'])
        ->get();

    return DataTables::of($priceLists)
        ->editColumn('name', function ($priceList) {
            return $priceList->getTranslation('name', app()->getLocale());
        })
        ->editColumn('status', function ($priceList) {
            return $priceList->status === 'active' 
                ? '<span class="badge bg-success">' . trans('status.active') . '</span>' 
                : '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
        })
        ->editColumn('created_at', function ($priceList) {
            return Carbon::parse($priceList->created_at)->format('d/m/Y h:i A');
        })
        ->addColumn('branch', function ($priceList) {
            return optional($priceList->branch)->getTranslation('name', app()->getLocale()) ?? trans('pricing_list.not_available');
        })
        ->addColumn('currency', function ($priceList) {
            return optional($priceList->getcurrency)->getTranslation('name', app()->getLocale()) ?? trans('pricing_list.not_available');
        })
        ->addColumn('list_type', function ($priceList) {
            $listTypes = [
                'cash' => trans('pricing_list.cash'),
                'contracts' => trans('pricing_list.Contracts'),
                'all' => trans('pricing_list.all'),
            ];

            return $listTypes[$priceList->list_type] ?? trans('messages.not_available');
        })
        ->addColumn('created_by', function ($priceList) {
            return optional($priceList->createdBy)->name ?? trans('messages.unknown_user');
        })
        ->addColumn('action', function ($priceList) {
            $encryptedId = Crypt::encryptString($priceList->id);
            $deleteFunction = 'deleteFunction(' . $priceList->id . ')';

            return '
            <a href="' .
                route('price_lists.edit', ['price_list' => $encryptedId]) .
                '" class="btn btn-warning btn-sm">' .
                trans('buttons.edit') .
                 '<a style="margin: 3px;" href="' .
                    route('pricing_of_services.create', ['id' => $encryptedId]) .
                    '" class="btn btn-info btn-sm">' .
                    trans('buttons.createpriceset') .
                    '</a>
            <a href="javascript:void(0)" onclick="' .
                $deleteFunction .
                '" class="btn btn-danger btn-sm delete-btn">' .
                trans('buttons.delete') .
                '</a>';
        })
        ->rawColumns(['status', 'action'])
        ->make(true);
}

}