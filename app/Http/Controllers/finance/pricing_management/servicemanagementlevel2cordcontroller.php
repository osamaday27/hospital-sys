<?php
namespace App\Http\Controllers\finance\pricing_management;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\finance\servicesublevel;
use App\models\finance\servicelevel;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
class servicemanagementlevel2cordcontroller extends Controller
{
    public function index()
    {
        return view('finance.pricing_contracts_management.pricing_management.services.indexlevel2');
    }
    public function create()
    {
        // استرجاع المستويات الأولى التي حالتها نشطة
        $level1s = servicelevel::where('status', 'active')->get();

        // تمرير البيانات إلى الفيو
        return view('finance.pricing_contracts_management.pricing_management.services.createlevel2', compact('level1s'));
    }

    public function store(Request $request)
    {
        // التحقق من وجود القيم مسبقًا
        if (
            servicesublevel::where('name->ar', $request->name_ar)
                ->orWhere('name->en', $request->name_en)
                ->exists()
        ) {
            session()->flash('warning_system', trans('service_settings.exists'));
            return redirect()
                ->back()
                ->withErrors([trans('service_settings.exists')]);
        }

        try {
        // التحقق من صحة المدخلات
        $validated = $request->validate([
            'level1_id' => 'required',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'hospital_code' => 'required|string|max:50',
        ]);

        $servicesublevel = new servicesublevel();
        $servicesublevel->level_id = $request->level1_id;
        $servicesublevel->name = ['en' => $request->name_en, 'ar' => $request->name_ar]; // Translation data
        $servicesublevel->hospital_code = $request->hospital_code;
        $servicesublevel->user_id = Auth::user()->id;
        $servicesublevel->default_branch = 1;
        $servicesublevel->status = 'active';
        $servicesublevel->save();

        // رسالة النجاح
        session()->flash('add', trans('service_settings.level_created_success'));
        return redirect()->back();
        } catch (\Exception $e) {
            // رسالة الخطأ
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function edit($encryptedId)
    {
        // فك تشفير الـ ID
        $id = Crypt::decryptString($encryptedId);

        // الحصول على بيانات المستوى باستخدام الـ ID
        $level = servicesublevel::findOrFail($id);
        $level1 = servicelevel::where('status', 'active')->get(); // المستويات الأولى
        // تمرير البيانات إلى الواجهة (view)
        return view('finance.pricing_contracts_management.pricing_management.services.editlevel2', compact('level1', 'level'));
    }

    public function update(Request $request)
    {
      
        // التحقق من البيانات المدخلة
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'hospital_code' => 'required|string|max:50',
            'level1_id' => 'required|exists:fin_service_levels,id',
        ]);

        // الحصول على بيانات المستوى الثاني
        $level2 = servicesublevel::findOrFail($request->id);
        // تحديث البيانات
        $level2->update([
            'name' => ['en' => $request->name_en, 'ar' => $request->name_ar],
            'hospital_code' => $request->input('hospital_code'),
            'level1_id' => $request->input('level1_id'),
            'status' => $request->input('status'),
        ]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('service_management_level2.index')->with('success', trans('messages.update_success'));
    }

    public function destroy($id)
    {
        $servicelevel = servicesublevel::find($id);

        if ($servicelevel) {
            $servicelevel->delete();
            return response()->json(['success' => 'medical record deleted successfully']);
        }

        return response()->json(['error' => 'medical record not found'], 404);
    }
    public function getservicelevel2()
    {
        // Fetch service sublevels and eager load the 'level1' relationship (parent level)
        $levels = servicesublevel::select(['id', 'name', 'hospital_code', 'created_at', 'status', 'level_id'])->with('level1'); // Eager load 'level1' relationship

        return DataTables::of($levels)
            ->addColumn('status', function ($level) {
                // Display status with classes
                $status = $level->status;
                switch ($status) {
                    case 'active':
                        return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive':
                        return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('name', function ($level) {
                // Translate the 'name' field based on the current locale
                return $level->getTranslation('name', app()->getLocale());
            })
            ->editColumn('created_at', function ($level) {
                // Format the creation date for display
                return Carbon::parse($level->created_at)->format('d/m/Y h:i A');
            })
            
            ->addColumn('level1', function ($level) {
                // Check if level1 is related and exists
                if ($level->level_id) {
                    return $level->level1->name; // Return the level1 name if it exists
                } else {
                    return trans('service_settings.no_level1_assigned'); // Fallback text if no relation is found
                }
            })

            ->addColumn('action', function ($level) {
                $encryptedId = Crypt::encryptString($level->id); // Encrypt the ID for secure routing
                $deleteFunction = 'deleteFunction(' . $level->id . ')'; // Function for deletion

                // Render action buttons with edit and delete options
                return '
                <a href="' .
                    route('service_management_level2.edit', ['service_management_level2' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
                <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'action', 'created_at', 'name', 'level1']) // Ensure these columns can render HTML
            ->make(true);
    }
}