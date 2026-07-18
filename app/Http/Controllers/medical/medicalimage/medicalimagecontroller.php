<?php
namespace App\Http\Controllers\medical\medicalimage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\medical\m_d_medicalimage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class medicalimagecontroller extends Controller
{
    use file_storage;

public function saveFundusAjax(Request $request)
{
    
    $request->validate([
        'image_data' => 'required|string',
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'nullable|exists:patient_visits,id',
        'reservation_id' => 'nullable|exists:reservations,id',
        'notes' => 'nullable|string',
    ]);

    try {
        $imageData = $request->image_data;

        // استخراج البيانات من base64
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type))
         {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg', 'jpeg', 'png'])) 
            {
                return response()->json(['success' => false, 'message' => 'صيغة غير مدعومة']);
            }
            $imageData = base64_decode($imageData);
            if ($imageData === false) 
            {
                return response()->json(['success' => false, 'message' => 'فشل في تحويل البيانات']);
            }
        } 
        else 
        {
            return response()->json(['success' => false, 'message' => 'البيانات غير صالحة']);
        }
        // تحديد اسم ومسار الصورة
        $fileName = 'fundus_' . time() . '.' . $type;
        $path = 'medical_images/fundus/' . $fileName;

        // حفظ الصورة في storage/app/public/medical_images/fundus
        Storage::disk('public')->put($path, $imageData);
        // حفظ في قاعدة البيانات
        m_d_medicalimage::create([
            'path' => $path,
            'type' => 'fundus',
            'notes' => $request->notes,
            'patient_id' => $request->patient_id,
            'visit_id' => $request->visit_id,
            'reservation_id' => $request->reservation_id,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'تم حفظ صورة Fundus بنجاح']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
    }
}

}