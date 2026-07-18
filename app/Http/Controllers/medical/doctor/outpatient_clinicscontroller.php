<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\area;
use App\models\general\adm_s_floors;
use App\models\general\adm_s_buildings;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\reservations;
use App\Traits\web\get_contract;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\medical\vitalsign;
use App\models\finance\service;

class outpatient_clinicscontroller extends Controller
{
    use file_storage, get_contract;

    public function index()
    {
        return view('medical.doctor.program.outpatient_clinics');
    }
    public function start($id)
    {
        try {
            $idi = Crypt::decryptString($id); // استخدم decryptString بدلاً من decrypt
            $reservations = reservations::find($idi);
            $patient = medical_record::find($reservations->id_patient);
            $patient_visits = patient_visits::find($reservations->visit_id);
            $latestVitalSign = vitalsign::where('id_patient', $reservations->id_patient)->latest()->first();
            if (!$reservations) {
                return redirect()->back()->with('error', 'الحجز غير موجود.');
            }

            if ($reservations) {
                $reservations->status = 'examination_start'; // استبدل 'new_status' بالحالة الجديدة المطلوبة
                $reservations->save();
            } else {
                return response()->json(['message' => 'الحجز غير موجود'], 404);
            }
            $services = service::whereHas('sublevel.level1', function ($query) {
                $query->where('level_type', 'operations'); // تصفية الخدمات حسب نوع المستوى
            })->get();
            return view('medical.doctor.program.start_session_out', compact('patient', 'patient_visits', 'latestVitalSign', 'reservations','services'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'خطأ في فك التشفير: ' . $e->getMessage());
        }
    }

    public function current_reservations(Request $request)
    {
        $todayDate = now()->toDateString();
        $reservations = reservations::with('doctor', 'patient', 'clinic', 'patient_visits');
        $reservations->where('date_reservations', $todayDate);

        // فلترة النتائج بناءً على الفلاتر المدخلة
        if ($request->has('clinc_id') && $request->clinc_id != '') {
            $reservations->where('clinic_id', $request->clinc_id);
        }
        if ($request->has('doctor_id') && $request->doctor_id != '') {
            $reservations->where('id_doctor', $request->doctor_id);
        }

        $reservations = $reservations->get(); // استعلام للحصول على البيانات

        return DataTables::of($reservations)
            ->addColumn('sr_no', function ($reservation) {
                return $reservation->id;
            })
            ->addColumn('q_number', function ($reservation) {
                return $reservation->q_number;
            })
            ->addColumn('status_and_visit', function ($reservation) {
                // حالة الحجز
                $statusClass = [
                    'reserved' => 'primary',
                    'pending' => 'warning',
                    'cancelled' => 'danger',
                    'completed' => 'success',
                    'confirmed' => 'info',
                    'waiting_for_payment' => 'warning',
                    'examination_start' => 'success',
                ];
                $status = $reservation->status;
                $statusClassBadge = $statusClass[$status] ?? 'light';
                $statusBadge = '<span class="badge bg-' . $statusClassBadge . '" style="display: block;margin-bottom: 10px;">' . trans('outpatient_trans.' . $status) . '</span>';

                // نوع الزيارة
                $visitTypeBadge = '-';
                if ($reservation->patient_visits->type == 'examination') {
                    $visitTypeBadge = '<span class="badge bg-primary" style="display: block;background-color: #49a131 !important;"><i class="fas fa-stethoscope"></i> ' . trans('outpatient_trans.examination') . '</span>';
                } elseif ($reservation->patient_visits->type == 'follow_up') {
                    $visitTypeBadge = '<span class="badge bg-info" style="display: block;"><i class="fas fa-clipboard-check"></i> ' . trans('outpatient_trans.follow_up') . '</span>';
                } elseif ($reservation->patient_visits->type == 'follow_up_opr') {
                    $visitTypeBadge = '<span class="badge bg-warning" style="display: block;"><i class="fas fa-procedures"></i> ' . trans('outpatient_trans.follow_up_opr') . '</span>';
                }

                // دمج الحالة ونوع الزيارة معًا
                return $statusBadge . ' ' . $visitTypeBadge;
            })
            ->addColumn('doctor_clinic', function ($reservation) {
                return $reservation->doctor ? $reservation->clinic->name_ar . '/' . $reservation->doctor->full_name : '-';
            })
            ->addColumn('patient_id', function ($reservation) {
                return $reservation->patient ? $reservation->patient->medical_number : '-';
            })
            ->addColumn('patient_name', function ($reservation) {
                return $reservation->patient ? $reservation->patient->full_name : '-';
            })
            ->addColumn('patient_phone', function ($reservation) {
                $phone = $reservation->patient->phone_number;
                $phone2 = $reservation->patient->phone_number2;
                $whatsapp = $reservation->patient->whatsapp;

                $phoneHtml = '<i class="bx bx-phone-outgoing text-primary"></i>' . $phone;

                if ($phone2) {
                    $phoneHtml .=
                        '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bx-phone-outgoing text-info" style="color: #140cfd;"></i>' .
                        $phone2 .
                        '</span>';
                }

                if ($whatsapp) {
                    $phoneHtml .=
                        '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bxl-whatsapp text-success"></i>' .
                        $whatsapp .
                        '</span>';
                }

                return $phoneHtml;
            })
            ->addColumn('age', function ($reservation) {
                return $reservation->patient ? Carbon::parse($reservation->patient->date_of_birth)->age : '-';
            })
            ->addColumn('patient_contract', function ($reservation) {
                return $reservation->patient && $this->getcontractname($reservation->patient->insurance_contracting) ? $this->getcontractname($reservation->patient->insurance_contracting) : '-';
            })
            ->addColumn('reservations_details', function ($reservation) {
                $reservationDate = Carbon::parse($reservation->date_reservations)->format('d/m/Y');
                $reservationTime = $reservation->time_slot; // إذا كان لديك time_slot في الحجز
                $qNumber = $reservation->q_number;
                $reservationId = $reservation->id;

                return '<div style="margin-bottom: 10px;text-align: start;">
                      <span class="badge badge-soft-dark badge-border" style="margin: 5px;text-align: start;display: initial;">
                        ' .
                    trans('outpatient_trans.created_at') .
                    ' ' .
                    Carbon::parse($reservation->created_at)->format('d/m/Y h:i A') .
                    '
                    </span>
                    <span class="badge badge-soft-dark badge-border" style="margin: 5px;text-align: start;display: initial;">
                        ' .
                    trans('outpatient_trans.q_number') .
                    ' ' .
                    $qNumber .
                    '
                    </span>
                </div>
                <div style="text-align: start;">
                  <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                        ' .
                    trans('outpatient_trans.date_reservations') .
                    ' ' .
                    $reservationDate .
                    ' ' .
                    $reservationTime .
                    '
                    </span>
                    <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                        ' .
                    trans('outpatient_trans.reservation_number') .
                    ' ' .
                    $reservationId .
                    '
                    </span>
                </div>';
            })
            ->addColumn('action', function ($reservation) {
                $encryptedPatientId = Crypt::encryptString($reservation->id ?? '');
                $encryptedId = Crypt::encryptString($reservation->id);
                // تعريف المتغير actions
                $actions = '';
                // زر مميز بتصميم أنيق
                // زر مميز بتصميم أنيق
                if ($reservation->status === 'examination_start') {
                    $transbuttonsn = trans('buttons.open'); // اسم المسار الجديد
                } else {
                    $transbuttonsn = trans('buttons.start_session'); // المسار الافتراضي
                }
                $actions .=
                    ' <a target="_blank" class="btn btn-primary waves-effect waves-light shadow-none" href="' .
                    route('start_session_out.start', ['id' => $encryptedPatientId]) .
                    '">
            <i class="bx bxs-right-arrow-circle me-2"></i> ' .
                    $transbuttonsn .
                    '
        </a>';

                return $actions;
            })

            ->rawColumns(['status_and_visit', 'action', 'visit_type', 'reservations_details', 'patient_phone']) // معاملة الأعمدة الخاصة بالـ status و action كـ raw
            ->make(true);
    }
}