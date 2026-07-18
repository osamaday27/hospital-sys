<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\area;
use App\models\medical\vitalsign;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\reservations;
use App\models\administrative\patient_visits;

class vitalsigncontroller extends Controller
{
    use file_storage;

    public function index($patient_id)
    {
        $vitalSigns = vitalsign::where('id_patient', $patient_id)
            ->with('user') // تأكد أنك عامل علاقة user()
            ->latest();
    
        return DataTables::of($vitalSigns)
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('blood_pressure', function ($row) {
                return $row->systolic_bp && $row->diastolic_bp
                    ? "{$row->systolic_bp}/{$row->diastolic_bp}"
                    : '-';
            })
            ->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '-';
            })
            ->make(true);
    }
    
    public function store(Request $request)
    {
        // التحقق الأساسي
        $request->validate([
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|numeric',
            'systolic_bp' => 'nullable|numeric',
            'diastolic_bp' => 'nullable|numeric',
            'oxygen_saturation' => 'nullable|numeric',
            'blood_sugar' => 'nullable|numeric',
            'pain_scale' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
    
        // قائمة القيم المطلوب التأكد من وجود واحدة منها على الأقل
        $vitalFields = [
            'temperature',
            'heart_rate',
            'respiratory_rate',
            'systolic_bp',
            'diastolic_bp',
            'oxygen_saturation',
            'blood_sugar',
            'notes',
            'weight',
            'height',
            'bmi'
        ];
    
        $hasAny = false;
        foreach ($vitalFields as $field) {
            $value = $request->input($field);
            if (!is_null($value) && $value !== '') {
                $hasAny = true;
                break;
            }
        }
    
        if (!$hasAny) {
            return response()->json([
                'success' => false,
                'message' => 'يجب إدخال قيمة واحدة على الأقل.'
            ], 422);
        }
    
        // الحفظ
        $vitalSign = new VitalSign();
        $vitalSign->id_patient = $request->patient_id;
        $vitalSign->weight = $request->weight ?? null;
        $vitalSign->height = $request->height ?? null;
        $vitalSign->bmi = $request->bmi ?? null;
        $vitalSign->temperature = $request->temperature ?? null;
        $vitalSign->heart_rate = $request->heart_rate ?? null;
        $vitalSign->respiratory_rate = $request->respiratory_rate ?? null;
        $vitalSign->systolic_bp = $request->systolic_bp ?? null;
        $vitalSign->diastolic_bp = $request->diastolic_bp ?? null;
        $vitalSign->oxygen_saturation = $request->oxygen_saturation ?? null;
        $vitalSign->blood_sugar = $request->blood_sugar ?? null;
        $vitalSign->pain_scale = $request->pain_scale ?? null;
        $vitalSign->notes = $request->notes ?? null;
        $vitalSign->save();
    
        return response()->json(['success' => true, 'message' => 'تم الحفظ بنجاح']);
    }
    
    
}