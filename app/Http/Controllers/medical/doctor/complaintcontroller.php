<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\specialties;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\models\medical\mbcomplaintshispat;

class complaintcontroller extends Controller
{
    public function index($patient_id)
    {
        if ($patient_id) {
            $complaints = mbcomplaintshispat::where('id_patient', $patient_id)->get();

            return DataTables::of($complaints)
                ->addColumn('user_name', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->addColumn('formatted_created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function ($complaint) {
                    return '
                    <button class="btn btn-sm btn-primary" onclick="editComplaint(' .
                        $complaint->id .
                        ')">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteComplaint(' .
                        $complaint->id .
                        ')">Delete</button>
                ';
                })
                ->rawColumns(['action']) // تأكد من أن الأعمدة التي تحتوي على HTML يتم معالجتها بشكل صحيح
                ->make(true);
        }
    }

    public function saveComplaint(Request $request)
    {
        $complaintData = $request->input('complaint');

        $complaint = new mbcomplaintshispat();
        $complaint->complaints = $complaintData['category'];
        $complaint->eye = $complaintData['eye'];
        $complaint->onset = $complaintData['onset'];
        $complaint->duration = $complaintData['duration'];
        $complaint->course = $complaintData['course'];
        $complaint->remarks = $complaintData['remarks'];
        $complaint->id_patient = $complaintData['id_patient'];
        $complaint->id_reservation = $complaintData['id_reservation'];
        $complaint->type_visit = $complaintData['type_visit'];
        $complaint->id_visit = $complaintData['id_visit'];
        $complaint->user_id = Auth::user()->id;
        $complaint->status = 'active';
        $complaint->save();

        return response()->json(['success' => true]);
    }
    public function show($id)
    {
        $complaint = mbcomplaintshispat::findOrFail($id);
        return response()->json(['success' => true, 'complaint' => $complaint]);
    }

    public function update(Request $request, $id)
    {
        $complaint = mbcomplaintshispat::findOrFail($id);
        $complaint->update($request->complaint);
        return response()->json(['success' => true, 'message' => 'Complaint updated successfully!']);
    }

    public function destroy($id)
    {
        $complaint = mbcomplaintshispat::findOrFail($id);
        $complaint->delete();

        return response()->json(['success' => true, 'message' => 'Complaint deleted successfully!']);
    }
}