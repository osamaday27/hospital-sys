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
use App\models\medical\mbdiseases_his;

class diseases_hiscontroller extends Controller
{
    public function loadcomplaint(Request $request)
    {
        $idPatient = $request->input('id_patient');
        $idReservation = $request->input('id_reservation');
        $typeVisit = $request->input('type_visit');
        $idVisit = $request->input('id_visit');

        $complaints = mbcomplaintshispat::where('id_patient', $idPatient)
         
            ->get();

        return response()->json(['success' => true, 'complaints' => $complaints]);
    }

    public function saveComplaint(Request $request)
    {
        $complaintData = $request->input('complaint');

        $complaint = new mbcomplaintshispat();
        $complaint->complaints = $complaintData['complaints'];
        $complaint->eye = $complaintData['eye'];
        $complaint->onset = $complaintData['onset'];
        $complaint->duration = $complaintData['duration'];
        $complaint->course = $complaintData['course'];
        $complaint->remarks = $complaintData['remarks'];
        $complaint->id_patient = $complaintData['id_patient'];
        $complaint->id_reservation = $complaintData['id_reservation'];
        $complaint->type_visit = $complaintData['type_visit'];
        $complaint->id_visit = $complaintData['id_visit'];
        $complaint->save();

        return response()->json(['success' => true]);
    }

    public function deleteComplaint(Request $request)
    {
        $id = $request->input('id');
        mbcomplaintshispat::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    
}