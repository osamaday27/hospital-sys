<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\models\general\namedictionary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
class namedictionarycontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('general.namedictionary.index');
    }
    public function patientnamereview()
    {
        return view('general.namedictionary.patientnamereview');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('general.namedictionary.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if (
            namedictionary::where('name_ar', $request->name_ar)
                ->orwhere('name_en', $request->name_en)
                ->exists()
        ) {
            return redirect()
                ->back()
                ->withErrors([trans('namedictionary_trans.existes')]);
        }
        try {
            $namedictionary = new namedictionary();
            $namedictionary->name_en = $request->name_en;
            $namedictionary->name_ar = $request->name_ar;
            $namedictionary->gender = $request->gender;
            $namedictionary->user_id = Auth::user()->id;
            $namedictionary->save();
            session()->flash('add', trans('general.created_success'));
            return redirect()->route('namedictionary.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validated();
            $namedictionary = namedictionary::findorFail($request->id);
            $namedictionary->update([($namedictionary->name_ar =$request->name_ar),($namedictionary->name_en =$request->name_en), ($namedictionary->gender = $request->gender), ($namedictionary->status = $request->status), ($namedictionary->user_id = Auth::user()->id)]);
            return redirect()->route('namedictionary.index')->with('success', 'User Deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        try {
            $namedictionary = namedictionary::findorFail($request->id)->delete();
            return redirect()->route('namedictionary.index')->with('success', 'User Deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function getnamedictionary()
    {
        $namedictionary = namedictionary::get();

        return DataTables::of($namedictionary)
            ->addColumn('status', function ($namedictionary) {
                // تحديد الحالة باللغة العربية
                $status = $namedictionary->status;
                switch ($status) {
                    case 'active':
                        return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive':
                        return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    case 'temporarily_suspended':
                        return '<span class="badge bg-danger">' . trans('status.temporarily_suspended') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('created_at', function ($namedictionary) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($namedictionary->created_at)->format('d/m/Y h:i A');
            })

            ->editColumn('gender', function ($namedictionary) {
                // تحديد الجنس وعرض أيقونة أو صورة بناءً على الجنس
                if ($namedictionary->gender == 1) {
                    return '<i class="fas fa-male"></i> ' . trans('namedictionary_trans.male'); // ذكر
                } elseif ($namedictionary->gender == 1) {
                    return '<i class="fas fa-female"></i> ' . trans('namedictionary_trans.female'); // أنثى
                } else {
                    return '<i class="fas fa-genderless"></i> ' . trans('namedictionary_trans.undefined'); // غير معروف
                }
            })
            ->addColumn('action', function ($namedictionary) {
                $encryptedId = Crypt::encryptString($namedictionary->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $namedictionary->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
            <a href="' .
                    route('namedictionary.edit', ['namedictionary' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'gender', 'action'])
            ->make(true);
    }
    public function translateNames(Request $request)
    {
        // الحصول على الأسماء من الطلب
        $names = $request->input('names');

        // استرجاع الترجمات من قاعدة البيانات
        // إذا كانت الأسماء عبارة عن مصفوفة، نستخدم whereJsonContains مع whereIn
        $translations = Namedictionary::whereIn('name_ar', $names)->pluck('name_en', 'name_ar')->toArray();

        // العودة بالترجمات على شكل JSON
        return response()->json($translations);
    }
}

?>