<?php

namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\models\medical\m_d_eyeexamination;

class FundusImageController extends Controller
{
    public function store(Request $request)
    {
        // 1) تحقق البيانات
        $validated = $request->validate([
            'image_data'     => ['required','string'],
            'patient_id'     => ['required'],
            'visit_id'       => ['nullable'],
            'reservation_id' => ['nullable'],
            'notes'          => ['nullable','string'],
        ]);

        // 2) فك Base64
        $image64 = $validated['image_data'];
        $matches = [];
        if (!preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $image64, $matches)) {
            return response()->json(['success' => false, 'message' => 'Invalid image data'], 422);
        }
        $type = strtolower($matches[1]);
        $image64 = preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $image64);
        $image64 = str_replace(' ', '+', $image64);
        $binary = base64_decode($image64, true);
        if ($binary === false) {
            return response()->json(['success' => false, 'message' => 'Image decode failed'], 422);
        }
        $ext = $type === 'jpeg' ? 'jpg' : $type;

        // 3) حفظ الملف على قرص public
        $relativePath = 'medical_sheet/fundus/' . date('Y/m/') . Str::uuid() . '.' . $ext;
        Storage::disk('public')->put($relativePath, $binary);
        $publicUrl = Storage::disk('public')->url($relativePath); // لعرض الصورة

        // 4) إيجاد أحدث Batch لنفس المريض (ولو موجود للزيارة الحالية أفضل)
        $batchId = null;
        $type_visit = null;
        $id_visit = $validated['visit_id'] ?? null;
        $id_reservation = $validated['reservation_id'] ?? null;

        $latest = m_d_eyeexamination::query()
            ->where('id_patient', $validated['patient_id'])
            ->when($id_visit, fn($q) => $q->where('id_visit', $id_visit))
            ->orderByDesc('created_at')
            ->first();

        if ($latest) {
            $batchId = $latest->batch_id;
            $type_visit = $latest->type_visit;
            $id_visit = $id_visit ?: $latest->id_visit;
            $id_reservation = $id_reservation ?: $latest->id_reservation;
        } else {
            $batchId = time() . (Auth::id() ?? '0');
            // لو عندك قيمة type_visit حابب تبعتها من الفرونت ابعتها وأقراها هنا
            $type_visit = $request->input('type_visit'); // ممكن تبقى null لو الكولمن يقبل null
        }

        // 5) إنشاء صف في جدول الفحص وربط الصورة به
        $record = m_d_eyeexamination::create([
            'id_patient'         => $validated['patient_id'],
            'id_reservation'     => $id_reservation,
            'id_visit'           => $id_visit,
            'type_visit'         => $type_visit,
            'section'            => 'Fundus',
            'right_eye'          => null,
            'left_eye'           => null,
            'comment'            => $validated['notes'] ?? null,
            'fundus_image_path'  => $relativePath,
            'fundus_comment'     => $validated['notes'] ?? null,
            'batch_id'           => $batchId,
            'created_by'         => Auth::id(),
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'تم حفظ رسم الـ Fundus بنجاح',
            'path'      => $publicUrl,
            'batch_id'  => $batchId,
            'record_id' => $record->id ?? null,
        ]);
    }
}