<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\medical\doctor_order;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class doctor_ordercontroller extends Controller
{
    public function save_doctor_order(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'id_patient' => 'required|string',
            'id_visit' => 'required|string',
            'lab' => 'nullable|array',
            'rad' => 'nullable|array',
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
            if (!empty($validatedData['lab'])) {
                foreach ($validatedData['lab'] as $lab) {
                    doctor_order::create([
                        'id_patient' => $validatedData['id_patient'],
                        'id_visit' => $validatedData['id_visit'],
                        'service_id' => $lab,
                        'request_type' => 'lab', // Default status
                        'request_date' => now(), // أو التاريخ الذي تريده
                        'status' => 'pending', // Default status
                        'user_id' => auth()->id(), // Assuming the user is authenticated
                    ]);
                }
            }
            // Save Allergies
            if (!empty($validatedData['rad'])) {
                foreach ($validatedData['rad'] as $rad) {
                    doctor_order::create([
                        'id_patient' => $validatedData['id_patient'],
                        'id_visit' => $validatedData['id_visit'],
                        'service_id' => $rad,
                        'request_type' => 'lab', // Default status
                        'request_date' => now(), // أو التاريخ الذي تريده
                        'status' => 'pending', // Default status
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