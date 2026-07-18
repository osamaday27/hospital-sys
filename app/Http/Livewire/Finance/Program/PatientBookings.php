<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Administrative\medical_record;
use App\Models\Finance\operation_contract_pricing;
use App\Models\Finance\advancepayment;
use Carbon\Carbon;
use App\Traits\Web\get_contract;

class PatientBookings extends Component
{
    use get_contract, WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterDateFrom;
    public $filterDateTo;
    public $filterSurgery = '';
    public $filterSurgeon = '';
    public $filterAdvance = ''; // ✅ فلتر بالدفعات المقدمة

    public function mount()
    {
        $today = Carbon::today()->toDateString();
        $this->filterDateFrom = $today;
        $this->filterDateTo   = $today;
    }

    public function render()
    {
        $query = adm_b_surgery_bookings::with([
            'patient',
            'room',
            'user',
            // ملاحظة: services علاقة many-to-many
            'services', // لازم تكون مع pivot لو محتاج side في العرض
            'medicalTeam.employee'
        ]);

        // عدم عرض الملغي افتراضيًا
        if (!$this->filterStatus) {
            $query->where('status', '!=', 'cancelled');
        }

        if ($this->search) {
            $query->whereHas('patient', fn($q) =>
                $q->where('full_name', 'like', '%' . $this->search . '%')
            );
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterDateFrom) {
            $query->whereDate('booking_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('booking_date', '<=', $this->filterDateTo);
        }

        if ($this->filterSurgery) {
            $query->whereHas('services', fn($q) =>
                $q->where('name', 'like', '%' . $this->filterSurgery . '%')
            );
        }

        if ($this->filterSurgeon) {
            $query->whereHas('medicalTeam.employee', fn($q) =>
                $q->where('full_name', 'like', '%' . $this->filterSurgeon . '%')
            );
        }

        // ✅ فلتر الحجوزات اللي عليها دفعات مقدمة
        if ($this->filterAdvance == 'with') {
            $query->whereHas('advancePayments');
        } elseif ($this->filterAdvance == 'without') {
            $query->whereDoesntHave('advancePayments');
        }

        $bookings = $query->orderBy('booking_date', 'desc')->paginate(10);

        // ✅ attach contract + per-surgery package_price + advance payments (يدعم تعدد العمليات)
        $bookings->getCollection()->transform(function ($booking) {
            // عقد المريض
            $contractId = medical_record::where('id', $booking->patient_id)->value('insurance_contracting');
            $booking->contract_name = $contractId ? ($this->getcontractname($contractId) ?: '-') : '-';

            // بناء قائمة العمليات مع السعر لكل عملية من جدول التسعير بالعقد
            $surgeries = [];
            $packageTotal = 0.0;

            if ($booking->relationLoaded('services') && $booking->services->count()) {
                foreach ($booking->services as $srv) {
                    $srvId   = $srv->id;
                    $srvName = $srv->name;
                    $side    = $srv->pivot->side ?? null;

                    $pkg = null;
                    if ($contractId) {
                        $pkg = operation_contract_pricing::where('contract_id', $contractId)
                            ->where('surgery_id', $srvId)
                            ->first();
                    }
                    $srvPrice = $pkg ? (float) $pkg->total_price : 0.0;
                    $packageTotal += $srvPrice;

                    $surgeries[] = [
                        'id'    => $srvId,
                        'name'  => $srvName,
                        'side'  => $side,
                        'price' => $srvPrice,
                    ];
                }
            }

            // قائمة العمليات (للعرض كبادجات أو popover)
            $booking->surgeries_list       = $surgeries;
            // إجمالي سعر الباكيج لكل العمليات في الحجز
            $booking->package_price_total  = $packageTotal;

            // ✅ اجمالي الدفعات المقدمة
            $booking->advance_paid = (float) advancepayment::where('booking_id', $booking->id)->sum('amount');

            return $booking;
        });

        return view('livewire.finance.program.patient-bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function setStatus($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }
}