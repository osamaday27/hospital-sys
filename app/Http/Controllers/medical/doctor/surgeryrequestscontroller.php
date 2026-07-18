<?php

namespace App\Http\Controllers\medical\doctor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\medical_record;
use App\models\finance\service;
use App\models\general\adm_s_rooms_surgical;
use App\models\administrative\adm_b_surgery_bookings;

class surgeryrequestscontroller extends Controller
{
    // عرض طلبات العمليات السابقة
    public function index(Request $request, $patient_id)
    {
        if ($request->ajax()) {
            $data = adm_b_surgery_bookings::where('patient_id', $patient_id)->latest()->get();
            return DataTables::of($data)
                ->addColumn('formatted_created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('user_name', function ($row) {
                    return $row->user->name ?? 'غير معروف';
                })
                  ->addColumn('services', function ($row) {
                    return $row->services->name ?? 'غير معروف';
                })
                ->make(true);
        }

        return view('surgery_requests.index', compact('patient_id'));
    }

    // حفظ طلب العملية الجراحية
    public function store(Request $request)
    {
        $request->validate([
            'id_patient' => 'required|exists:patient,id',
            'surgical_id' => 'required|string|max:255',
            'booking_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        adm_b_surgery_bookings::create([
            'patient_id' => $request->id_patient,
            'surgical_id' => $request->surgical_id,
            'booking_date' => $request->booking_date,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'تم حفظ طلب العملية بنجاح']);
    }
}