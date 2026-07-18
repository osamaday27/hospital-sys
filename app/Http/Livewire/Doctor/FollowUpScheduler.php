<?php

namespace App\Http\Livewire\Doctor;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\models\administrative\medical_record as Patient;
use App\models\administrative\admsclinic as Clinic;
use App\models\hr\Employee as Doctor;
use App\models\administrative\reservations as Reservation;
use App\models\administrative\patient_visits as Visit;
use App\models\administrative\scheduling_settings as Schedule;
use App\Models\administrative\follow_up_request;

use App\models\finance\service as Service;
use App\Models\Notification; // موديل الإشعارات

class FollowUpScheduler extends Component
{
    public $patient_id;
    public $visit_id;
    public $notes;

    public $clinic_id;
    public $doctor_id;
    public $base_date;
    public $days_after = 14;
    public $followup_type = 'follow_up';
    public $period = 'any';

    public $target_date;
    public $proposals = [];
    public $selected_proposal = null;

    public $clinics = [];
    public $doctors = [];

    public $saving = false;
    public $message = null;
    public $error = null;

    protected $rules = [
        'patient_id' => 'required|integer|exists:patient,id',
        'visit_id' => 'required|integer|exists:patient_visits,id',
        'clinic_id' => 'required|integer|exists:adm_s_clinics,id',
        'doctor_id' => 'required|integer|exists:employees,id',
        'base_date' => 'required|date',
        'days_after' => 'required|integer|min:0|max:365',
        'followup_type' => 'required|in:follow_up,follow_up_opr',
        'period' => 'required|in:any,morning,afternoon,evening',
        'notes' => 'nullable|string|max:500',
    ];

    public function mount($patient, $visit)
    {
        $this->patient_id = (int) $patient;
        $this->visit_id = (int) $visit;

        $visitRow = Visit::find($this->visit_id);
        $this->base_date = $visitRow ? Carbon::parse($visitRow->date_visit)->toDateString() : now()->toDateString();

        $this->clinics = Clinic::query()
            ->orderBy('name_ar')
            ->get(['id', 'name_ar'])
            ->toArray();

        $lastRes = Reservation::query()->where('id_patient', $this->patient_id)->orderByDesc('date_reservations')->first();

        if ($lastRes) {
            $this->clinic_id = $lastRes->clinic_id;
            $this->doctor_id = $lastRes->id_doctor;
        } else {
            $this->clinic_id = $this->clinics[0]['id'] ?? null;
        }

        $this->refreshDoctors();
        $this->computeTargetAndProposals();
    }

    public function updatedClinicId()
    {
        $this->doctor_id = null;
        $this->refreshDoctors();
        $this->computeTargetAndProposals();
    }

    public function updatedDoctorId()
    {
        $this->computeTargetAndProposals();
    }
    public function updatedBaseDate()
    {
        $this->computeTargetAndProposals();
    }
    public function updatedDaysAfter()
    {
        $this->computeTargetAndProposals();
    }
    public function updatedFollowupType()
    {
        $this->computeTargetAndProposals();
    }
    public function updatedPeriod()
    {
        $this->computeTargetAndProposals();
    }

    protected function refreshDoctors()
    {
        if (!$this->clinic_id) {
            $this->doctors = [];
            return;
        }

        $doctorIds = Schedule::query()->where('id_clinic', $this->clinic_id)->where('status', 'active')->pluck('id_doctor')->unique()->values()->all();

        $this->doctors = Doctor::query()
            ->whereIn('id', $doctorIds ?: [-1])
            ->where('employee_type', 'physician')
            ->where('employee_status', 'in_service')
            ->orderBy('full_name')
            ->get(['id', 'full_name'])
            ->toArray();

        if (!$this->doctor_id && !empty($this->doctors)) {
            $this->doctor_id = $this->doctors[0]['id'];
        }
    }

    protected function computeTargetAndProposals()
    {
        $this->error = $this->message = null;
        if (!$this->clinic_id || !$this->doctor_id || !$this->base_date) {
            $this->proposals = [];
            $this->selected_proposal = null;
            return;
        }

        $this->target_date = Carbon::parse($this->base_date)->addDays((int) $this->days_after)->startOfDay();
        $this->proposals = $this->findNextSlots($this->target_date, $this->clinic_id, $this->doctor_id, 3, $this->period);
        $this->selected_proposal = $this->proposals[0]['key'] ?? null;
    }

    protected function findNextSlots(Carbon $fromDate, int $clinicId, int $doctorId, int $limit = 3, string $period = 'any'): array
    {
        $out = [];
        $cursor = $fromDate->copy();
        $maxDaysScan = 90;

        while (count($out) < $limit && $maxDaysScan-- > 0) {
            $doweek = $cursor->dayOfWeek;

            $shift = Schedule::query()->where('id_clinic', $clinicId)->where('id_doctor', $doctorId)->where('status', 'active')->where('day', $doweek)->first();

            if ($shift) {
                foreach (['first', 'second', 'third'] as $prefix) {
                    $from = $shift->{$prefix . '_shift_from'};
                    $to = $shift->{$prefix . '_shift_to'};

                    if (!$from || !$to) {
                        continue;
                    }

                    $start = Carbon::parse($cursor->toDateString() . ' ' . $from);
                    $end = Carbon::parse($cursor->toDateString() . ' ' . $to);

                    [$pStart, $pEnd] = $this->periodBounds($cursor, $period);
                    $start = $start->max($pStart);
                    $end = $end->min($pEnd);

                    if ($start->gte($end)) {
                        continue;
                    }

                    $slotMinutes = 20;

                    for ($t = $start->copy(); $t->lt($end); $t->addMinutes($slotMinutes)) {
                        $timeStr = $t->format('h:i A'); // صيغة AM/PM

                        $taken = Reservation::query()
                            ->where('clinic_id', $clinicId)
                            ->where('id_doctor', $doctorId)
                            ->whereDate('date_reservations', $cursor->toDateString())
                            ->where('time_slot', $timeStr)
                            ->whereNotIn('status', ['cancelled'])
                            ->exists();

                        if (!$taken) {
                            $key = $cursor->toDateString() . ' ' . $timeStr;
                            $out[] = [
                                'key' => $key,
                                'date' => $cursor->toDateString(),
                                'time' => $timeStr,
                                'doctor_id' => $doctorId,
                                'clinic_id' => $clinicId,
                                'label' => $cursor->isoFormat('ddd D/M/Y') . ' - ' . $timeStr,
                            ];
                            if (count($out) >= $limit) {
                                break 2;
                            }
                        }
                    }
                }
            }

            $cursor->addDay()->startOfDay();
        }

        return $out;
    }

    protected function periodBounds(Carbon $date, string $period): array
    {
        $start = $date->copy()->setTime(0, 0, 0);
        $end = $date->copy()->setTime(23, 59, 59);
        if ($period === 'morning') {
            $start->setTime(8, 0);
            $end->setTime(12, 0);
        }
        if ($period === 'afternoon') {
            $start->setTime(12, 0);
            $end->setTime(16, 0);
        }
        if ($period === 'evening') {
            $start->setTime(16, 0);
            $end->setTime(21, 0);
        }
        return [$start, $end];
    }

    public function bookSelected()
    {
        $this->validate();

        if (!$this->selected_proposal) {
            $this->error = 'من فضلك اختر موعدًا للمتابعة.';
            return;
        }

        // ⬇️ تقسيم التاريخ والوقت بشكل صحيح
        $parts = explode(' ', $this->selected_proposal, 3);
        $date = $parts[0];
        $time = isset($parts[2]) ? $parts[1] . ' ' . $parts[2] : $parts[1];
        $time = Carbon::parse($time)->format('h:i A'); // صيغة AM/PM

        $clinic = Clinic::find($this->clinic_id);
        if (!$clinic) {
            $this->error = 'العيادة غير موجودة.';
            return;
        }

        // ✅ منع تكرار المتابعة لنفس المريض في نفس اليوم
        $alreadyFollowUpToday = Reservation::query()
            ->where('id_patient', $this->patient_id)
            ->whereDate('date_reservations', $date)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($alreadyFollowUpToday) {
            $this->error = 'المريض لديه متابعة مسجلة بالفعل في هذا اليوم.';
            return;
        }

        $serviceId = null;
        if ($this->followup_type === 'follow_up_opr' && !empty($clinic->id_follow_up_opr_service)) {
            $serviceId = $clinic->id_follow_up_opr_service;
        } elseif (!empty($clinic->id_follow_up_service)) {
            $serviceId = $clinic->id_follow_up_service;
        }

        if (!$serviceId) {
            $this->error = 'خدمة المتابعة غير معرفة للعيادة.';
            return;
        }

        // ✅ التأكد من عدم وجود حجز على نفس الساعة AM/PM
        $already = Reservation::query()
            ->where('clinic_id', $this->clinic_id)
            ->where('id_doctor', $this->doctor_id)
            ->whereDate('date_reservations', $date)
            ->where('time_slot', $time)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($already) {
            $this->computeTargetAndProposals();
            $this->error = 'الميعاد المختار لم يعد متاحًا.';
            return;
        }

        DB::beginTransaction();
        try {
            $visit = Visit::create([
                'patient_id' => $this->patient_id,
                'date_visit' => $date,
                'type' => $this->followup_type,
                'status' => 'open',
                'account_id' => Auth::user()->id_account,
                'user_id' => Auth::id(),
            ]);

            $queue = $this->calculateQueue($this->doctor_id, $date);

            Reservation::create([
                'id_patient' => $this->patient_id,
                'visit_id' => $visit->id,
                'id_doctor' => $this->doctor_id,
                'clinic_id' => $this->clinic_id,
                'date_visit' => $date,
                'date_reservations' => $date,
                'time_slot' => $time,
                'reservation_type' => $serviceId,
                'q_number' => $queue,
                'status' => 'unconfirmed',
                'contract' => null,
                'contract_id' => null,
                'total' => 0,
                'patient_contribution' => 0,
                'contract_contribution' => 0,
                'discount' => 0,
                'payment_method' => 'cash',
                'paid_amount' => 0,
                'remaining_amount' => 0,
                'account_id' => Auth::user()->id_account,
                'user_id' => Auth::id(),
            ]);
            follow_up_request::create([
                'patient_id' => $this->patient_id,
                'visit_id' => $visit->id,
                'doctor_id' => $this->doctor_id,
                'clinic_id' => $this->clinic_id,
                'request_date' => now()->toDateString(),
                'followup_date' => $date,
                'notes' => $this->notes,
                'user_id' => Auth::id(),
            ]);

            // ✅ إشعار للطبيب
            Notification::create([
                'type' => 'follow_up',
                'notifiable_id' => $this->doctor_id,
                'notifiable_type' => Doctor::class,
                'data' => [
                    'message' => 'تم تسجيل متابعة جديدة للمريض رقم: ' . $this->patient_id,
                    'date' => $date,
                    'time' => $time,
                ],
            ]);

            DB::commit();

            $this->message = 'تم الحجز بنجاح للمتابعة بتاريخ ' . Carbon::parse($date)->format('d/m/Y') . ' الساعة ' . $time . '، رقم الدور: ' . $queue;

            $this->computeTargetAndProposals();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error = 'فشل حجز الموعد: ' . $e->getMessage();
        }
    }

    protected function calculateQueue(int $doctorId, string $date): int
    {
        $last = Reservation::query()->where('id_doctor', $doctorId)->where('date_reservations', $date)->latest('q_number')->first();

        return $last ? $last->q_number + 1 : 1;
    }

    public function render()
    {
        $clinicName = optional(Clinic::find($this->clinic_id))->name_ar;
        $doctorName = optional(Doctor::find($this->doctor_id))->full_name;

        return view('livewire.doctor.follow-up-scheduler', [
            'clinicName' => $clinicName,
            'doctorName' => $doctorName,
        ]);
    }
}