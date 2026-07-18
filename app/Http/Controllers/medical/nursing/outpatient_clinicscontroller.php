<?php
namespace App\Http\Controllers\medical\nursing;
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

use App\Traits\patienttrackingtrait;
class outpatient_clinicscontroller extends Controller
{
    use file_storage, get_contract, patienttrackingtrait;

    public function index()
    {
        return view('medical.nursing.program.outpatient_clinics');
    }
    public function getNextPatient($reservationId)
    {
        $current = reservations::findOrFail($reservationId);

        // تحديث حالة المريض الحالي
        $current->update([
            'status' => 'nursing_session_ended',
        ]);

        // البحث عن المريض التالي
        $next = reservations::with('patient')
            ->where('clinic_id', $current->clinic_id) // نفس العيادة
            ->where('date_reservations', $current->date_reservations) // نفس اليوم
            ->whereIn('status', ['confirmed_paid', 'nursing_session_started'])
            ->where('id', '>', $current->id) // اللي بعد المريض الحالي
            ->orderBy('id', 'asc')
            ->first();

        // تسجيل تتبع انتهاء جلسة الحالي
        $this->logPatientStep($current->id_patient, $current->visit_id, 'nursing_session', 'completed', auth()->id(), 'تم إنهاء جلسة التمريض');

        if (!$next) {
            return redirect()->route('nursing_outpatient_clinics.index')->with('warning', '📌 لا يوجد مريض تالي لنفس العيادة.');
        }

        // تشفير ID المريض التالي
        $encryptedId = \Crypt::encryptString($next->id);

        return redirect()->route('n_start_session_out.start', ['id' => $encryptedId]);
    }

    public function start($id)
    {
        try {
            $idi = Crypt::decryptString($id); // استخدم decryptString بدلاً من decrypt
            $reservations = reservations::with('patient')->find($idi);
            $patient = medical_record::find($reservations->id_patient);
            $patient_visits = patient_visits::find($reservations->visit_id);
            $latestVitalSign = vitalsign::where('id_patient', $reservations->id_patient)->latest()->first();
            // حساب عمر المريض
            if ($patient->birth_date) {
                $age = \Carbon\Carbon::parse($patient->birth_date)->age;
            } else {
                $age = 20;
            }

            if (!$reservations) {
                return redirect()->back()->with('error', 'الحجز غير موجود.');
            }

            // تحديث حالة الجلسة
            $reservations->update([
                'status' => 'nursing_session_started',
            ]);

            // تسجيل التتبع
            $this->logPatientStep($reservations->id_patient, $reservations->visit_id, 'nursing_session', 'in_progress', auth()->id(), 'تم بداية جلسة التمريض');
            return view('medical.nursing.program.start_session_out', compact('patient', 'patient_visits', 'latestVitalSign', 'reservations', 'age'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'خطأ في فك التشفير: ' . $e->getMessage());
        }
    }
    public function endSession($id)
    {
        $reservation = reservations::with('patient')->findOrFail($id);

        // تحديث حالة الجلسة
        $reservation->update([
            'status' => 'nursing_session_ended',
        ]);

        // تسجيل التتبع
        $this->logPatientStep($reservation->id_patient, $reservation->visit_id, 'nursing_session', 'completed', auth()->id(), 'تم إنهاء جلسة التمريض');

        // إعادة التوجيه لواجهة التمريض
        return redirect()->route('nursing_outpatient_clinics.index')->with('success', __('doctor_trans.nursing_session_ended_successfully'));
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
                $actions .=
                    '
        <a target="_blank" class="btn btn-primary waves-effect waves-infoshadow-none" href="' .
                    route('n_start_session_out.start', ['id' => $encryptedPatientId]) .
                    '">
            <i class="bx bxs-right-arrow-circle me-2"></i> ' .
                    trans('buttons.open') .
                    '
        </a>';

                return $actions;
            })

            ->rawColumns(['status_and_visit', 'action', 'visit_type', 'reservations_details', 'patient_phone']) // معاملة الأعمدة الخاصة بالـ status و action كـ raw
            ->make(true);
    }
}