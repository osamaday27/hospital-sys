<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\medical\mballergieshispat;
use App\models\medical\mbcomplaintshispat;
use App\models\medical\mbdiseaseshispat;
use App\models\medical\mbexaminationhispat;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class generalexaminationcontroller extends Controller
{
    public function saveGeneralExamination(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'id_patient' => 'required|string',
            'id_visit' => 'required|string',
            'allergies' => 'nullable|array',
            'medical_history' => 'nullable|array',
            'complaints' => 'nullable|array',
            'examination' => 'nullable|array',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        // Extract validated data
        $validatedData = $validator->validated();

        // Save data for each category
        try {
            DB::beginTransaction();

            // Save Allergies
            if (!empty($validatedData['allergies'])) {
                foreach ($validatedData['allergies'] as $allergy) {
                    mballergieshispat::create([
                        'id_patient' => $validatedData['id_patient'],
                        'id_visit' => $validatedData['id_visit'],
                        'allergies' => $allergy,
                        'status' => 'active', // Default status
                        'user_id' => auth()->id(), // Assuming the user is authenticated
                    ]);
                }
            }

            // Save medical History (Diseases)
            if (!empty($validatedData['medical_history'])) {
                foreach ($validatedData['medical_history'] as $disease) {
                    mbdiseaseshispat::create([
                        'id_patient' => $validatedData['id_patient'],
                        'id_visit' => $validatedData['id_visit'],
                        'diseases' => $disease,
                        'status' => 'active', // Default status
                        'user_id' => auth()->id(), // Assuming the user is authenticated
                    ]);
                }
            }

            // Save Complaints
            if (!empty($validatedData['complaints'])) {
                foreach ($validatedData['complaints'] as $complaint) {
                    mbcomplaintshispat::create([
                        'id_patient' => $validatedData['id_patient'],
                        'id_visit' => $validatedData['id_visit'],
                        'complaints' => $complaint,
                        'status' => 'active', // Default status
                        'user_id' => auth()->id(), // Assuming the user is authenticated
                    ]);
                }
            }

            // Save Examination
            if (!empty($validatedData['examination'])) {
                foreach ($validatedData['examination'] as $examination) {
                    mbexaminationhispat::create([
                        'id_patient' => $validatedData['id_patient'],
                        'id_visit' => $validatedData['id_visit'],
                        'examination' => $examination,
                        'status' => 'active', // Default status
                        'user_id' => auth()->id(), // Assuming the user is authenticated
                    ]);
                }
            }

            DB::commit();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Data saved successfully!',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Failed to save data',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}