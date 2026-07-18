<?php
namespace App\Http\Controllers\medical\doctor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class viewfilecontroller extends Controller
{
    public function loadOverview()
    {
        return view('medical.doctor.components.program.ophthalmology.medical_file.overview');
    }

    public function loadComplaint()
    {
        return view('medical.doctor.components.program.ophthalmology.medical_file.complaint');
    }

    public function loadmedicalHistory()
    {
        return view('medical.doctor.components.program.ophthalmology.medical_file.medical_history');
    }
}