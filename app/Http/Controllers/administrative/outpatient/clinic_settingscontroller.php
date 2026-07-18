<?php
namespace App\Http\Controllers\administrative\outpatient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\hr\Employee;
use App\models\administrative\admsclinic;
use App\models\finance\service;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\scheduling_settings;
use Illuminate\Http\JsonResponse;
class clinic_settingscontroller extends Controller
{
    use file_storage;

    public function index($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $admsclinic = admsclinic::findOrFail($id);
        $services = service::whereHas('sublevel.level1', function ($query) {
            $query->where('level_type', 'outpatient'); // تصفية الخدمات حسب نوع المستوى
        })->get();
        return view('administrative.outpatient.settings.clinic_settings', compact('admsclinic', 'services'));
    }
   public function update(Request $request)
{
    try
    {
        // التحقق من البيانات المدخلة
        $request->validate([
            'id_examination_service' => 'required|exists:fin_services,id',
            'id_follow_up_service' => 'required|exists:fin_services,id',
            'id_follow_up_opr_service' => 'required|exists:fin_services,id',
            'id_spe_examination_service' => 'required|exists:fin_services,id',
            'id_spe_follow_up_service' => 'required|exists:fin_services,id',
            'id_spe_follow_up_opr_service' => 'required|exists:fin_services,id',
        ]);

        // جلب السجل المطلوب تعديله
        $setting = admsclinic::findOrFail($request->id);

        // تحديث البيانات
        $setting->update([
            'id_examination_service' => $request->id_examination_service,
            'id_follow_up_service' => $request->id_follow_up_service,
            'id_follow_up_opr_service' => $request->id_follow_up_opr_service,
            'id_spe_examination_service' => $request->id_spe_examination_service,
            'id_spe_follow_up_service' => $request->id_spe_follow_up_service,
            'id_spe_follow_up_opr_service' => $request->id_spe_follow_up_opr_service,
        ]);

        // رسالة النجاح
        session()->flash('add', trans('general.updated_successfully'));
        return redirect()->route('outpatient_settings.index');

    } catch (\Exception $e) {
        // رسالة الخطأ
        return redirect()
            ->back()
            ->withErrors(['error' => $e->getMessage()]);
    }
}

}