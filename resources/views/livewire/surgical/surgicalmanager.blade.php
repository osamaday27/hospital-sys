<div class="container-fluid" dir="rtl">

    {{-- 🏷️ العنوان والخبز --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18 text-dark">{{ trans('surgical_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="surgical_programs" class="text-muted">
                                {{ trans('main_trans.programs') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-dark">{{ trans('surgical_trans.surgical_manager') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ رسائل السيشن --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 🔎 فلاتر البحث --}}
    <div class="card mb-3 border-primary">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="search-box">
                        <input type="search" class="form-control" placeholder="ابحث عن مريض بالاسم أو الرقم"
                            wire:model.debounce.500ms="search">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="room_id">
                        <option value="">جميع الغرف</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">
                                {{ $room->name_ar ?? ($room->name_en ?? 'Room #' . $room->id) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="surgical_id">
                        <option value="">جميع العمليات</option>
                        @foreach ($surgicals as $surgical)
                            <option value="{{ $surgical->id }}">{{ $surgical->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" wire:model="booking_date">
                </div>
            </div>
        </div>
    </div>

    {{-- 📋 جدول الحجوزات --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive" style="min-height: 500px; overflow-y: auto;">
                <table class="table table-hover align-middle table-nowrap mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>{{ trans('surgical_trans.action') }}</th>
                            <th>{{ trans('surgical_trans.surgical') }}</th>
                            <th class="text-center">{{ trans('surgical_trans.status') }}</th>
                            <th>{{ trans('surgical_trans.name') }}</th>
                            <th>{{ trans('inpatient_trans.medical_number') }}</th>
                            <th class="text-center">{{ trans('inpatient_trans.age') }}</th>
                            <th>{{ trans('inpatient_trans.phone') }}</th>
                            <th>{{ trans('inpatient_trans.contract') }}</th>
                            <th class="text-center">دفعة مقدّمة</th>
                            <th>{{ trans('inpatient_trans.nursing_station') }}</th>
                            <th>{{ trans('inpatient_trans.booking_date') }}</th>
                            <th>{{ trans('surgical_trans.diagnosis') }}</th>
                            <th>{{ trans('surgical_trans.surgeon') }}</th>
                            <th>{{ trans('surgical_trans.anesthesiologist') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            @php
                                $hasAdvance = \App\Models\Finance\advancepayment::where('booking_id', $booking->id)
                                    ->where('status', 'confirmed')
                                    ->exists();
                                $hasHistory = \App\models\administrative\surgerystatushistory::where(
                                    'surgery_booking_id',
                                    $booking->id,
                                )->exists();
                                $isScheduled =
                                    !empty($booking->room_id) &&
                                    !empty($booking->scheduled_from) &&
                                    !empty($booking->scheduled_to);
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">

                                            {{-- ✅ تخصيص أو إلغاء الجدولة حسب الحالة --}}
                                            @if ($isScheduled)
                                                @if (
                                                    $booking->status != 'cancelled' &&
                                                        !in_array($booking->status, [
                                                            'preparing_patient',
                                                            'preparing_room',
                                                            'ready_for_surgery',
                                                            'anesthesia_started',
                                                            'anesthesia_completed',
                                                            'incision_started',
                                                            'in_progress',
                                                            'on_hold',
                                                            'completed',
                                                            'closing_wound',
                                                            'wound_closed',
                                                            'anesthesia_reversed',
                                                            'recovery_room',
                                                            'under_observation',
                                                            'discharged',
                                                            'failed',
                                                            'postponed',
                                                            'transferred',
                                                            'room_canceled',
                                                            'arrived',
                                                            'admitted',
                                                            'in_surgery',
                                                            'anesthesia_approved',
                                                        ]))
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            wire:click.prevent="cancelSchedule({{ $booking->id }})">
                                                            <i class="ri-close-circle-line text-danger me-2"></i>
                                                            إلغاء الجدولة
                                                        </a>
                                                    </li>
                                                @endif
                                            @else
                                                @if ($booking->status != 'cancelled')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            wire:click.prevent="openAssignModal({{ $booking->id }})">
                                                            <i class="ri-time-line text-primary me-2"></i>
                                                            تخصيص غرفة ووقت
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>

                                            {{-- ✅ تفاصيل العملية (يتطلب دفعة) --}}
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    wire:click.prevent="openManagerDetails({{ $booking->id }})">
                                                    <i class="ri-eye-line text-secondary me-2"></i> عرض تفاصيل العملية
                                                </a>
                                            </li>

                                            {{-- ✅ حالات ما قبل العملية (بعد الدفع) + تأكيد الوصول مسموح دائماً --}}
                                            @if ($booking->status == 'booked')
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'arrived')">
                                                        <i class="ri-user-follow-line text-primary me-2"></i> تأكيد
                                                        الوصول
                                                    </a>
                                                </li>
                                            @endif

                                            @if ($hasAdvance)
                                                @if ($booking->status == 'arrived')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'preparing_patient')">
                                                            <i class="ri-stethoscope-line text-info me-2"></i>
                                                            تحضير المريض
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($booking->status == 'preparing_patient')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'preparing_room')">
                                                            <i class="ri-hotel-bed-line text-warning me-2"></i>
                                                            تجهيز الغرفة
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($booking->status == 'preparing_room')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'ready_for_surgery')">
                                                            <i class="ri-check-line text-success me-2"></i>
                                                            جاهز للعملية
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                            @if ($booking->status != 'cancelled')
                                                {{-- ✅ إلغاء الحجز --}}
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        wire:click.prevent="confirmCancel({{ $booking->id }})">
                                                        <i class="ri-close-circle-line text-danger me-2"></i> إلغاء
                                                        الحجز
                                                    </a>
                                                </li>
                                            @endif
                                            {{-- ✅ الرجوع + الملاحظات --}}
                                            @if ($hasHistory)
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        wire:click.prevent="revertPrompt({{ $booking->id }})">
                                                        <i class="ri-arrow-go-back-line me-2"></i> الرجوع للحالة
                                                        السابقة
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    wire:click.prevent="openNotes({{ $booking->id }})">
                                                    <i class="ri-file-list-line me-2"></i> ملاحظات الحالة
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                                {{-- العمليات كبادجات + Popover للباقي (نفس التمريض) --}}
                                <td style="min-width:260px;">
                                    @php
                                        $services = $booking->services ?? collect();
                                        $count = $services->count();
                                        $limit = 1;

                                        $sideTitle = function ($side) {
                                            if (!$side) {
                                                return __('surgical_trans.not_specified');
                                            }
                                            $key = match ($side) {
                                                'right' => 'right',
                                                'left' => 'left',
                                                'both' => 'both',
                                                default => 'not_specified',
                                            };
                                            return __('surgical_trans.' . $key);
                                        };
                                    @endphp

                                    @if ($count > 0)
                                        @foreach ($services->take($limit) as $srv)
                                            <span
                                                class="badge bg-light text-dark border me-1 mb-1 d-inline-flex align-items-center">
                                                <i class="ri-scissors-cut-line me-1"></i>
                                                {{ $srv->name }}
                                                @if (isset($srv->pivot) && $srv->pivot->side)
                                                    <span class="ms-2 badge bg-soft-primary text-primary">
                                                        {{ $sideTitle($srv->pivot->side) }}
                                                    </span>
                                                @endif
                                            </span>
                                        @endforeach

                                        @if ($count > $limit)
                                            @php
                                                $moreHtml = $services
                                                    ->slice($limit)
                                                    ->map(function ($srv) use ($sideTitle) {
                                                        $label = e($srv->name);
                                                        $side =
                                                            isset($srv->pivot) && $srv->pivot->side
                                                                ? $sideTitle($srv->pivot->side)
                                                                : __('surgical_trans.not_specified');
                                                        return "<div class='mb-1'><i class=\"ri-scissors-cut-line me-1\"></i> {$label} <span class=\"badge bg-soft-primary text-primary ms-2\">{$side}</span></div>";
                                                    })
                                                    ->implode('');
                                                $moreHtmlAttr = str_replace(['"', "'"], ['&quot;', '&#39;'], $moreHtml);
                                            @endphp

                                            <button type="button" class="btn btn-link p-0 text-secondary"
                                                tabindex="0" role="button" data-bs-toggle="popover"
                                                data-bs-trigger="focus" data-bs-html="true" data-bs-container="body"
                                                title="{{ __('surgical_trans.all_surgicals') }} ({{ $count }})"
                                                data-bs-content="{!! $moreHtmlAttr !!}">
                                                {{ __('surgical_trans.show_more') }} (+{{ $count - $limit }})
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- الحالة --}}
                                <td class="text-center">
                                    @php
                                        $statusColors = [
                                            'booked' => 'warning',
                                            'arrived' => 'info',
                                            'admitted' => 'info',
                                            'preparing_patient' => 'primary',
                                            'preparing_room' => 'primary',
                                            'ready_for_surgery' => 'success',
                                            'anesthesia_started' => 'info',
                                            'anesthesia_completed' => 'info',
                                            'incision_started' => 'primary',
                                            'in_progress' => 'primary',
                                            'on_hold' => 'warning',
                                            'closing_wound' => 'warning',
                                            'wound_closed' => 'primary',
                                            'anesthesia_reversed' => 'info',
                                            'recovery_room' => 'info',
                                            'under_observation' => 'info',
                                            'discharged' => 'success',
                                            'completed' => 'success',
                                            'in_surgery' => 'warning',
                                            'cancelled' => 'danger',
                                            'failed' => 'danger',
                                            'postponed' => 'warning',
                                            'transferred' => 'dark',
                                            'room_canceled' => 'dark',
                                            'confirmed' => 'success',
                                        ];
                                        $color = $statusColors[$booking->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-soft-{{ $color }} text-{{ $color }}">
                                        <i class="me-1 ri-check-line"></i>
                                        {{ __('surgical_trans.status_' . $booking->status) }}
                                    </span>
                                </td>

                                {{-- بيانات المريض --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                    {{ mb_substr($booking->patient->full_name ?? 'N', 0, 1) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">{{ $booking->patient->full_name ?? '-' }}</div>
                                    </div>
                                </td>
                                <td>{{ $booking->patient->medical_number ?? '-' }}</td>
                                <td class="text-center">{{ $booking->patient->age ?? '-' }}</td>
                                <td>{{ $booking->patient->phone_number ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ $booking->contract_full_name ?? '-' }}</span></td>

                                {{-- حالة الدفعة --}}
                                <td class="text-center">
                                    @if ($hasAdvance)
                                        <span class="badge bg-success">مدفوع</span>
                                    @else
                                        <span class="badge bg-danger">غير مدفوع</span>
                                    @endif
                                </td>

                                {{-- الغرفة والتاريخ --}}
                                <td><span class="badge bg-secondary">{{ $booking->room->name_ar ?? '-' }}</span></td>
                                <td>
                                    @if ($booking->booking_date)
                                        <span
                                            class="fw-medium">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                            @if ($booking->scheduled_from && $booking->scheduled_to)
                                                <small class="text-muted d-block">{{ $booking->scheduled_from }} -
                                                    {{ $booking->scheduled_to }}</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- التشخيصات --}}
                                <td>
                                    @php
                                        $diagnosisCount = $booking->diagnoses ? count($booking->diagnoses) : 0;
                                        $displayLimit = 1;
                                    @endphp
                                    @if ($diagnosisCount > 0)
                                        @foreach ($booking->diagnoses->take($displayLimit) as $diagnosis)
                                            <span
                                                class="badge bg-light text-dark mb-1 d-inline-block">{{ $diagnosis->name }}</span>
                                        @endforeach
                                        @if ($diagnosisCount > $displayLimit)
                                            <button type="button" class="btn btn-link p-0 text-primary"
                                                tabindex="0" role="button" data-bs-toggle="popover"
                                                data-bs-trigger="focus"
                                                title="التشخيصات الكاملة ({{ $diagnosisCount }})" data-bs-html="true"
                                                data-bs-content="
                                                    @foreach ($booking->diagnoses->slice($displayLimit) as $diagnosis)
                                                        <div>{{ $diagnosis->name }}</div> @endforeach
                                                ">
                                                عرض المزيد (+{{ $diagnosisCount - $displayLimit }})
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- الفريق الطبي --}}
                                <td>
                                    @php $surgeon = $booking->medicalTeam->firstWhere('role', 'surgeon'); @endphp
                                    {{ $surgeon ? $surgeon->medical_team->full_name : '-' }}
                                </td>
                                <td>
                                    @php $anesthesiologist = $booking->medicalTeam->firstWhere('role', 'anesthesiologist'); @endphp
                                    {{ $anesthesiologist ? $anesthesiologist->medical_team->full_name : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center py-4">
                                    <img src="{{ asset('assets/images/no-data.svg') }}" alt="No data"
                                        height="100">
                                    <h5 class="mt-3 text-muted">{{ trans('surgical_trans.no_bookings_found') }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>

    {{-- ✅ مودال تغيير الحالة --}}
    <div wire:ignore.self class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="statusModalLabel">
                        <i class="ri-time-line me-1"></i> تغيير الحالة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الوقت</label>
                        <input type="datetime-local" class="form-control" wire:model.defer="statusChangeTime">
                        @error('statusChangeTime')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">ملاحظات (اختياري)</label>
                        <textarea rows="4" class="form-control" wire:model.defer="statusNote" placeholder="اكتب ملاحظة..."></textarea>
                        @error('statusNote')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                    <button class="btn btn-primary" wire:click="saveStatusChange">
                        <i class="ri-save-3-line me-1"></i> حفظ التغيير
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ مودال الرجوع للحالة السابقة --}}
    <div wire:ignore.self class="modal fade" id="revertModal" tabindex="-1" aria-labelledby="revertModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="revertModalLabel">
                        <i class="ri-arrow-go-back-line me-1"></i> الرجوع للحالة السابقة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <div>الحالة الحالية:
                            <span
                                class="badge bg-info">{{ __('surgical_trans.status_' . ($currentStatusLabel ?? '')) }}</span>
                        </div>
                        <div class="mt-2">سيتم الرجوع إلى:
                            <span
                                class="badge bg-secondary">{{ __('surgical_trans.status_' . ($previousStatusLabel ?? '')) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سبب/ملاحظة الرجوع</label>
                        <textarea rows="4" class="form-control" wire:model.defer="revertNote" placeholder="اكتب ملاحظة..."></textarea>
                        @error('revertNote')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">وقت الرجوع</label>
                        <input type="datetime-local" class="form-control" wire:model.defer="revertTime">
                        @error('revertTime')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                    <button class="btn btn-warning" wire:click="confirmRevert">
                        <i class="ri-check-line me-1"></i> تأكيد الرجوع
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ مودال ملاحظات الحالة --}}
    <div wire:ignore.self class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="notesModalLabel">
                        <i class="ri-file-list-line me-1"></i> سجل ملاحظات الحالة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($historyItems && count($historyItems))
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>الوقت</th>
                                        <th>الحالة</th>
                                        <th>المستخدم</th>
                                        <th>الملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historyItems as $h)
                                        <tr>
                                            <td>
                                                @if ($h['status_changed_at'])
                                                    {{ \Carbon\Carbon::parse($h['status_changed_at'])->format('d/m/Y H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td><span
                                                    class="badge bg-soft-primary text-primary">{{ __('surgical_trans.status_' . $h['status']) }}</span>
                                            </td>
                                            <td>{{ $h['user_name'] ?? '-' }}</td>
                                            <td>{{ $h['note'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">لا توجد ملاحظات مسجّلة.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ مودال إلغاء الحجز --}}
    <div wire:ignore.self class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelModalLabel">
                        <i class="ri-close-circle-line me-1"></i> تأكيد إلغاء الحجز
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">سبب الإلغاء</label>
                    <textarea class="form-control" rows="4" wire:model.defer="cancelReason" placeholder="اكتب سبب الإلغاء..."></textarea>
                    @error('cancelReason')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                    <button class="btn btn-danger" wire:click="cancelBookingConfirmed">
                        <i class="ri-check-line me-1"></i> تأكيد الإلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ مودال تخصيص الغرفة والوقت (Time Slots) --}}
    <div wire:ignore.self class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assignModalLabel">
                        <i class="ri-time-line me-1"></i> تخصيص غرفة ووقت العملية
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">الغرفة</label>
                            <select class="form-select" wire:model="assignRoomId" id="assignRoomId">
                                <option value="">اختر الغرفة</option>
                                @foreach ($rooms as $r)
                                    <option value="{{ $r->id }}">
                                        {{ $r->name_ar ?? ($r->name_en ?? 'Room #' . $r->id) }}</option>
                                @endforeach
                            </select>
                            @error('assignRoomId')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">التاريخ</label>
                            <input type="date" class="form-control" wire:model="assignDate" id="assignDate">
                            @error('assignDate')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary w-100" id="btnLoadSlots">
                                <i class="ri-refresh-line me-1"></i> تحميل الفتحات المتاحة
                            </button>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- عرض الفتحات --}}
                    <div id="slotsArea">
                        <div class="d-flex flex-wrap gap-2">
                            @if (count($timeSlots))
                                @foreach ($timeSlots as $s)
                                    @php $reserved = $s['reserved'] ?? false; @endphp
                                    <button type="button"
                                        class="btn btn-sm {{ $reserved ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                        @if (!$reserved) wire:click="setPickedSlot('{{ $s['start'] }}','{{ $s['end'] }}')" @endif
                                        @if ($reserved) disabled @endif>
                                        {{ $s['start'] }} - {{ $s['end'] }} {!! $reserved ? '<span class="ms-1 badge bg-secondary">محجوز</span>' : '' !!}
                                    </button>
                                @endforeach
                            @else
                                <div class="text-muted">اضغط "تحميل الفتحات" لعرض المتاح.</div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">وقت البداية</label>
                            <input type="time" class="form-control" wire:model="assignStart" id="assignStart">
                            @error('assignStart')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">وقت النهاية</label>
                            <input type="time" class="form-control" wire:model="assignEnd" id="assignEnd">
                            @error('assignEnd')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                    <button class="btn btn-primary" wire:click="saveAssignRoomAndTime">
                        <i class="ri-save-3-line me-1"></i> حفظ التخصيص
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ سكربتات المودالات والـ Popover و تحميل الفتحات --}}
    <div>
        <script>
            document.addEventListener('livewire:load', function() {
                const showModal = (id) => bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
                const hideModal = (id) => {
                    const m = bootstrap.Modal.getInstance(document.getElementById(id));
                    if (m) m.hide();
                };

                Livewire.on('showCancelModal', () => showModal('cancelModal'));
                Livewire.on('hideCancelModal', () => hideModal('cancelModal'));

                Livewire.on('showStatusModal', () => showModal('statusModal'));
                Livewire.on('hideStatusModal', () => hideModal('statusModal'));

                Livewire.on('showRevertModal', () => showModal('revertModal'));
                Livewire.on('hideRevertModal', () => hideModal('revertModal'));

                Livewire.on('showNotesModal', () => showModal('notesModal'));

                Livewire.on('showAssignModal', () => showModal('assignModal'));
                Livewire.on('hideAssignModal', () => hideModal('assignModal'));

                const initPopovers = () => {
                    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                        const inst = bootstrap.Popover.getInstance(el);
                        if (inst) inst.dispose();
                    });
                    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                        new bootstrap.Popover(el, {
                            container: 'body',
                            html: true,
                            trigger: el.getAttribute('data-bs-trigger') || 'focus',
                            sanitize: false
                        });
                    });
                };

                initPopovers();
                Livewire.hook('message.processed', () => {
                    initPopovers();
                });

                /* ========= تحميل الفتحات (AJAX) ========= */
                const btnLoadSlots = document.getElementById('btnLoadSlots');
                const roomInput = document.getElementById('assignRoomId');
                const dateInput = document.getElementById('assignDate');

                async function loadSlots() {
                    if (!roomInput || !dateInput) return;
                    const id_room = roomInput.value;
                    const booking_date = dateInput.value;

                    if (!id_room || !booking_date) {
                        alert('اختر الغرفة والتاريخ أولاً.');
                        return;
                    }

                    try {
                        const url = "{{ route('surgical.timeslots.get') }}";
                        const resp = await fetch(url + '?' + new URLSearchParams({
                            id_room,
                            booking_date
                        }), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await resp.json();

                        if (Array.isArray(data?.time_slots)) {
                            @this.set('timeSlots', data.time_slots);
                        } else {
                            @this.set('timeSlots', []);
                        }
                    } catch (e) {
                        console.error(e);
                        alert('تعذر تحميل الفتحات. حاول مجددًا.');
                    }
                }

                if (btnLoadSlots) btnLoadSlots.addEventListener('click', loadSlots);
            });
        </script>
    </div>
</div>
