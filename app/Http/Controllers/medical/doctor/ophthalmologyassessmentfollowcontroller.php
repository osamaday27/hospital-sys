<?php
namespace App\Http\Controllers\medical\doctor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\medical\followophthalmologyassessment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Traits\web\file_storage;
use App\models\administrative\medical_record;

class ophthalmologyassessmentfollowcontroller extends Controller
{
    use file_storage;
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'uava_bcva_left' => 'nullable|string',
            'uava_bcva_right' => 'nullable|string|max:255',
            'iop_left' => 'nullable|string|max:255',
            'iop_right' => 'nullable|string|max:255',
            'ant_segment_left' => 'nullable|string',
            'ant_segment_right' => 'nullable|string',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        // Store the uploaded image
        $filePath = $this->file_storage($request->file('image_path'), 'patient_attachments'); // This function should handle file saving

        // Extract other necessary data from the request
        $id_reservation = $request->input('id_reservation');
        $id_visit = $request->input('id_visit');
        $type_visit = $request->input('type_visit');
        $patientId = $request->input('patient_id');

        // Save the assessment in the database
        $assessment = followophthalmologyassessment::create([
            'uava_bcva_left' => $validatedData['uava_bcva_left'],
            'uava_bcva_right' => $validatedData['uava_bcva_right'],
            'ant_segment_left' => $validatedData['ant_segment_left'],
            'ant_segment_right' => $validatedData['ant_segment_right'],
            'iop_left' => $validatedData['iop_left'],
            'iop_right' => $validatedData['iop_right'],
            'fundus' => $filePath, // Store the image file path
            'account_id' => Auth::id(), // Current user ID
            'user_id' => Auth::id(), // Current user ID
            'id_patient' => $patientId, // Patient ID
            'id_reservation' => $id_reservation,
            'id_visit' => $id_visit, // Corrected to use $id_visit
            'type_visit' => $type_visit, // Corrected to use $type_visit
        ]);

        // Return success response
        return response()->json(
            [
                'success' => true,
                'data' => $assessment,
            ],
            201,
        );
    }

    public function get_sheet_history($id)
    {
        // جلب تاريخ الفحص للمريض بناءً على معرف المريض مع معلومات المستخدم
        $assessments = ophthalmologyassessment::where('id_patient', $id)
            ->with('user') // جلب معلومات المستخدم الذي قام بالإنشاء
            ->get();

        // تعديل المسار الخاص بالصورة ليكون مسارًا كاملاً
        foreach ($assessments as $assessment) {
            // تحويل المسار النسبي إلى مسار كامل باستخدام asset()
            $assessment->fundus = asset($assessment->fundus); // تأكد من المسار الصحيح حسب مكان تخزين الصورة
        }

        // إعادة البيانات كـ JSON
        return response()->json($assessments);
    }
    public function delete_sheet_history($id)
    {
        // تحقق من وجود السجل
        $assessment = ophthalmologyassessment::find($id);

        if ($assessment) {
            $assessment->delete(); // احذف السجل
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Record not found'], 404);
    }
}