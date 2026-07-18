<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\medical\mdpainassessment;
use App\models\medical\mdpainassessment_doc;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class mdpainassessmentcontroller extends Controller
{
    // دالة لحفظ تقييم الألم باستخدام Ajax
    public function store(Request $request)
    {
        // التحقق من البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'id_patient' => 'nullable|string|max:255',
            'id_visit' => 'nullable|string|max:255',
            'id_reservation' => 'nullable|string|max:255',
            'type_visit' => 'nullable|string|max:255',
            'digital_assessment' => 'nullable|numeric|between:0,10',
            'face_assessment' => 'nullable|numeric|between:0,10',
            'child_assessment' => 'nullable|numeric|between:0,10',
            'pain_type' => 'nullable|in:Dull,Burning,Cramping,Crushing,Heavy,Tingling,Pricking,Stabbing,Pulsating',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:pending,completed,canceled',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // إذا فشل التحقق من البيانات، إعادة الخطأ
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // إنشاء سجل تقييم الألم الرئيسي
        $painAssessment = mdpainassessment::create($request->only(['id_patient', 'id_visit', 'id_reservation', 'type_visit', 'digital_assessment', 'face_assessment', 'child_assessment', 'pain_type', 'notes', 'status', 'user_id']));

        // التحقق من البيانات الخاصة بالتقييمات التفصيلية
        $validator = Validator::make($request->all(), [
            'assessment' => 'required|array',
            'degree' => 'required|array',
            'pain_location' => 'required|array',
            'continuous_intermittent' => 'required|array',
            'pain_type_t' => 'required|array',
            'increases_during' => 'required|array',
            'decreases_during' => 'required|array',
            'treatment' => 'required|array',
            'reassessment' => 'required|array',
        ]);

        // إذا فشل التحقق من البيانات التفصيلية، إعادة الخطأ
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // حفظ التقييمات التفصيلية
        foreach ($request->assessment as $index => $assessment) {
            mdpainassessment_doc::create([
                'assessment' => $assessment,
                'id_mdpainassessment' => $painAssessment->id,
                'id_patient' => $painAssessment->id_patient,
                'id_visit' => $painAssessment->id_visit,
                'id_reservation' => $painAssessment->id_reservation,
                'degree' => $request->degree[$index],
                'pain_location' => $request->pain_location[$index],
                'continuous_intermittent' => $request->continuous_intermittent[$index],
                'pain_type' => $request->pain_type_t[$index],
                'increases_during' => $request->increases_during[$index],
                'decreases_during' => $request->decreases_during[$index],
                'treatment' => $request->treatment[$index],
                'reassessment' => $request->reassessment[$index],
            ]);
        }

        // إعادة الرد مع رسالة النجاح
        return response()->json(['message' => 'تم حفظ التقييم بنجاح!', 'data' => $painAssessment], 200);
    }
}