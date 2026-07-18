<?php
namespace App\Http\Controllers\medical\doctor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\medical\ophthalmologyassessment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Traits\web\file_storage;
use App\models\administrative\medical_record;

class ophthalmologyassessmentcontroller extends Controller
{
    use file_storage;
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'glasses_left' => 'nullable|string|max:255',
            'glasses_right' => 'nullable|string|max:255',
            'uava_bcva_left' => 'nullable|string',
            'uava_bcva_right' => 'nullable|string|max:255',
            'cycloplegic_refraction_left' => 'nullable|string|max:255',
            'cycloplegic_refraction_right' => 'nullable|string|max:255',
            'iop_left' => 'nullable|string|max:255',
            'iop_right' => 'nullable|string|max:255',
            'pupil_right' => 'nullable|string|max:255',
            'pupil_left' => 'nullable|string|max:255',
            'ocular_motility_left' => 'nullable|string|max:255',
            'ocular_motility_right' => 'nullable|string|max:255',
            'eyelid_and_lacrimal_left' => 'nullable|string|max:255',
            'eyelid_and_lacrimal_right' => 'nullable|string|max:255',
            'conjunctive_and_sclera_left' => 'nullable|string',
            'conjunctive_and_sclera_right' => 'nullable|string|max:255',
            'cornea_left' => 'nullable|string|max:255',
            'cornea_right' => 'nullable|string|max:255',
            'ac_and_iris_left' => 'nullable|string|max:255',
            'ac_and_iris_right' => 'nullable|string|max:255',
            'lens_left' => 'nullable|string|max:255',
            'lens_right' => 'nullable|string|max:255',
            'anterior_vitreous_left' => 'nullable|string|max:255',
            'anterior_vitreous_right' => 'nullable|string|max:255',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // تحقق من الصورة
        ]);

        $filePath = $this->file_storage($request->file('image_path'), 'patient_attachments');
        $id_reservation = $request->input('id_reservation');
        $id_visit = $request->input('id_visit');
        $type_visit = $request->input('type_visit');

        $patientId = $request->input('patient_id');
        // حفظ البيانات في قاعدة البيانات
        $assessment = ophthalmologyassessment::create([
            'glasses_left' => $validatedData['glasses_left'],
            'glasses_right' => $validatedData['glasses_right'],

            'uava_bcva_left' => $validatedData['uava_bcva_left'],
            'uava_bcva_right' => $validatedData['uava_bcva_right'],
            
            'cycloplegic_refraction_left' => $validatedData['cycloplegic_refraction_left'],
            'cycloplegic_refraction_right' => $validatedData['cycloplegic_refraction_right'],
            
            'iop_left' => $validatedData['iop_left'],
            'iop_right' => $validatedData['iop_right'],
            
            'pupil_left' => $validatedData['pupil_left'],
            'pupil_right' => $validatedData['pupil_right'],
            
            'ocular_motility_left' => $validatedData['ocular_motility_left'],
            'ocular_motility_right' => $validatedData['ocular_motility_right'],
            
            'eyelid_and_lacrimal_left' => $validatedData['eyelid_and_lacrimal_left'],
            'eyelid_and_lacrimal_right' => $validatedData['eyelid_and_lacrimal_right'],
            
            'conjunctive_and_sclera_left' => $validatedData['conjunctive_and_sclera_left'],
            'conjunctive_and_sclera_right' => $validatedData['conjunctive_and_sclera_right'],
            
            'cornea_left' => $validatedData['cornea_left'],
            'cornea_right' => $validatedData['cornea_right'],
            
            'ac_and_iris_left' => $validatedData['ac_and_iris_left'],
            'ac_and_iris_right' => $validatedData['ac_and_iris_right'],
            
            'lens_left' => $validatedData['lens_left'],
            'lens_right' => $validatedData['lens_right'],
            
            'anterior_vitreous_left' => $validatedData['anterior_vitreous_left'],
            'anterior_vitreous_right' => $validatedData['anterior_vitreous_right'],
            'fundus' => $filePath,
            'account_id' => Auth::id(), // استخدم معرف المستخدم الحالي
            'user_id' => Auth::id(), // استخدم معرف المستخدم الحالي
            'id_patient' => $patientId, // قم بتحديث هذا المعرف بناءً على المريض
            'id_reservation' => $id_reservation,
            // قم بتحديث هذا المعرف بناءً على الحجز
            'id_visit' => $id_reservation, // قم بتحديث هذا المعرف بناءً على الحجز
            'type_visit' => $id_reservation, // قم بتحديث هذا المعرف بناءً على الحجز
        ]);

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