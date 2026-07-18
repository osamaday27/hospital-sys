<?php
namespace App\Http\Controllers\administrative\surgical;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\adm_s_rooms_surgical;
use App\models\administrative\adm_b_surgery_bookings;
use App\models\administrative\surgery_medical_team;
use App\models\hr\employee;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\medical\vitalsign;
class surgical_doctorcontroller extends Controller
{
  

    public function index()
    {
        return view('medical.surgical.program.doctor.index');
    }

    public function details($id)
    {
        $booking = adm_b_surgery_bookings::with(['patient', 'room', 'services'])->findOrFail($id);
        $teamMembers = surgery_medical_team::where('surgery_bookings_id', $id)->with('medical_team')->get();
        $availableEmployees = employee::all(); 
        $patient = medical_record::find($booking->patient_id);
        $patient_visits = patient_visits::find($booking->visit_id);
        $latestVitalSign = vitalsign::where('id_patient', $booking->patient_id)->latest()->first();
        return view('medical.surgical.program.doctor.details', compact('booking', 'teamMembers', 'availableEmployees', 'patient', 'patient_visits', 'latestVitalSign'));
    }
}

    