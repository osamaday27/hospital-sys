<?php

namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Traits\web\file_storage;
use App\Traits\web\get_contract;
use App\models\administrative\reservations;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\medical\vitalsign;
use App\models\administrative\admsclinic;
use App\models\finance\service;
use App\Traits\patienttrackingtrait;

class ophthalmology_examinationcontroller extends Controller
{
    use file_storage, get_contract, patienttrackingtrait;

    /**
     * عرض الصفحة الرئيسية لعيادة العيون
     */
    public function index()
    {
        return view('medical.doctor.program.ophthalmology.outpatient_clinics_examination');
    }

    /**
     * بدء الجلسة لطبيب عيون بناءً على معرف الحجز المشفّر
     */
    public function start($id)
    {
        $idi = $id;
        // فك تشفير المعرف
        $idi = Crypt::decryptString($id);
        $reservations = reservations::with(['patient', 'patient_visits'])->find($idi);

        $patient = $reservations->patient;
        $patient_visits = $reservations->patient_visits;

        $latestVital = vitalsign::where('id_patient', $reservations->id_patient)->latest()->first();

        $latestVitalSign = VitalSign::where('id_patient', $patient->id)->latest()->first();

        // حساب عمر المريض
        if ($patient->birth_date) {
            $age = \Carbon\Carbon::parse($patient->birth_date)->age;
        } else {
            $age = 20;
        }

        // حدّث الحالة إلى بدء الفحص
        $reservations->status = 'examination_start';
        $reservations->save();

        // جلب خدمات العمليات فقط
        $services = service::whereHas('sublevel.level1', function ($q) {
            $q->where('level_type', 'operations');
        })->get();
        // تسجيل التتبع
        $this->logPatientStep($reservations->id_patient, $reservations->visit_id, 'ophthalmology_examination', 'in_progress', auth()->id(), 'تم بداية جلسة فحص العيون');

        return view('medical.doctor.program.ophthalmology.start_session_out_examination', compact('patient', 'patient_visits', 'latestVitalSign', 'reservations', 'services', 'age'));
    }
    public function endSession($id)
    {
        $reservation = reservations::with('patient')->findOrFail($id);

        // تحديث حالة الجلسة
        $reservation->update([
            'status' => 'specialist_examination_completed',
        ]);

        // تسجيل التتبع
        $this->logPatientStep($reservation->id_patient, $reservation->visit_id, 'ophthalmology_examination', 'completed', auth()->id(), 'تم إنهاء جلسة فحص العيون بواسطة الاخصائي');

        // إعادة التوجيه لواجهة التمريض
        return redirect()->route('ophthalmology_examination.index')->with('success', __('doctor_trans.end_ophthalmology_examination_session'));
    }

    /**
     * جلب الحجوزات الحالية لعرضها في جدول DataTables
     * - فقط للحجوزات المؤكدة
     * - فقط التي لا يوجد عليها مبلغ متبقٍ
     * - فقط عيادة العيون (غيّر المعرف حسب الحاجة)
     */
    public function current_reservations(Request $request)
    {
        $todayDate = now()->toDateString();
        $ophClinicIds = admsclinic::where('specialty_id', '14')->pluck('id');

        $query = reservations::with(['doctor', 'patient', 'clinic', 'patient_visits'])
            ->whereDate('date_reservations', $todayDate)
            ->where('status', '!=', 'unconfirmed')
            ->whereIn('clinic_id', $ophClinicIds);

        if ($request->filled('clinc_id')) {
            $query->where('clinic_id', $request->clinc_id);
        }
        if ($request->filled('doctor_id')) {
            $query->where('id_doctor', $request->doctor_id);
        }

        return DataTables::of($query->get())
            ->addColumn('sr_no', fn($r) => $r->id)
            ->addColumn('q_number', fn($r) => $r->q_number)
            ->addColumn('status_and_visit', function ($r) {
                $classes = [
                    'reserved' => 'primary',
                    'pending' => 'warning',
                    'cancelled' => 'danger',
                    'completed' => 'success',
                    'confirmed' => 'info',
                    'waiting_for_payment' => 'warning',
                    'examination_start' => 'success',
                    'nursing_session_ended' => 'success',
                    'nursing_session_started' => 'info',
                    'specialist_examination_completed' => 'success',
                    'consultation_examination_completed' => 'success',
                ];
                $badge = $classes[$r->status] ?? 'info';
                $visitTypeBadge = match ($r->patient_visits->type) {
                    'examination' => '<span class="badge bg-primary"><i class="fas fa-stethoscope"></i> ' . trans('outpatient_trans.examination') . '</span>',
                    'follow_up' => '<span class="badge bg-info"><i class="fas fa-clipboard-check"></i> ' . trans('outpatient_trans.follow_up') . '</span>',
                    'follow_up_opr' => '<span class="badge bg-warning"><i class="fas fa-procedures"></i> ' . trans('outpatient_trans.follow_up_opr') . '</span>',
                    default => '-',
                };
                return '<span class="badge bg-' . $badge . '">' . trans('outpatient_trans.' . $r->status) . '</span> ' . $visitTypeBadge;
            })
            ->addColumn('doctor_clinic', fn($r) => $r->doctor ? $r->clinic->name_ar . '/' . $r->doctor->full_name : '-')
            ->addColumn('patient_id', fn($r) => $r->patient->medical_number ?? '-')
            ->addColumn('patient_name', fn($r) => $r->patient->full_name ?? '-')
            ->addColumn('patient_phone', function ($r) {
                $html = '<i class="bx bx-phone-outgoing text-primary"></i>' . $r->patient->phone_number;
                if ($r->patient->phone_number2) {
                    $html .= '<br><i class="bx bx-phone-outgoing text-info"></i>' . $r->patient->phone_number2;
                }
                if ($r->patient->whatsapp) {
                    $html .= '<br><i class="bx bxl-whatsapp text-success"></i>' . $r->patient->whatsapp;
                }
                return $html;
            })
            ->addColumn('age', fn($r) => $r->patient ? Carbon::parse($r->patient->date_of_birth)->age : '-')
            ->addColumn('patient_contract', fn($r) => $r->patient ? ($this->getcontractname($r->patient->insurance_contracting) ?: '-') : '-')
            ->addColumn('reservations_details', function ($r) {
                $d = Carbon::parse($r->date_reservations)->format('d/m/Y');
                $t = $r->time_slot;
                return "<span class=\"badge badge-soft-info\">{$d} {$t}</span>";
            })
            ->addColumn('action', function ($r) {
                if ($r->status === 'examination_test_start' || $r->status === 'nursing_session_ended' || $r->status === 'examination_test_completed' || $r->status === 'examination_start') {
                    $encrypted = Crypt::encryptString($r->id);
                    return '
            <a href="' .
                        route('start_session_out_ophthalmology_examination.start', ['id' => $encrypted]) .
                        '"
               class="btn btn-primary">
               <i class="bx bxs-right-arrow-circle me-2"></i>' .
                        trans('buttons.start_session') .
                        '
            </a>
        ';
                }

                return '<span class="badge bg-secondary">' . trans('outpatient_trans.cannot_start_yet') . '</span>';
            })

            ->rawColumns(['status_and_visit', 'patient_phone', 'reservations_details', 'action'])
            ->make(true);
    }
}