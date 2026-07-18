<?php

namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medical\EyeFundusImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FundusController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'image_data' => 'required|string',
            ]);

            // فك base64
            $imageData = $request->input('image_data');
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageBinary = base64_decode($imageData);

            // اسم فريد للصورة
            $fileName = 'fundus_' . time() . '_' . uniqid() . '.png';
            $path = 'fundus/' . $fileName;

            Storage::disk('public')->put($path, $imageBinary);

            // سجل في قاعدة البيانات
            $fundus = EyeFundusImage::create([
                'patient_id'     => $request->patient_id,
                'visit_id'       => $request->visit_id,
                'reservation_id' => $request->reservation_id,
                'image_path'     => $path,
                'notes'          => $request->notes,
                'created_by'     => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ رسم Fundus بنجاح',
                'path'    => asset('storage/' . $path),
                'id'      => $fundus->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ أثناء الحفظ: ' . $e->getMessage(),
            ], 500);
        }
    }
}