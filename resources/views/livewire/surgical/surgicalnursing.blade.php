<div class="container-fluid" dir="rtl">

    {{-- 🏷️ Title & Breadcrumb --}}
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
                        <li class="breadcrumb-item active text-dark">{{ trans('surgical_trans.surgical_nursing') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔘 General Buttons --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between">
            <a href="{{ route('surgical_booking.export_excel') }}" class="btn btn-success">
                <i class="ri-file-excel-2-line align-bottom me-1"></i> {{ __('general.export_excel') }}
            </a>
        </div>
    </div>

    {{-- ✅ Session Messages --}}
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

    {{-- 🔎 Search Filters --}}
    <div class="card mb-3 border-primary">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="search-box">
                        <input type="search" class="form-control" placeholder="{{ __('general.type_here_to_search') }}"
                               wire:model.debounce.500ms="search">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="room_id">
                        <option value="">{{ __('general.all_rooms') }}</option>
                       @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">
                                {{ app()->getLocale() === 'ar' ? $room->name_ar : $room->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                  <select id="surgicalSelect" class="form-select select2">
                        <option value="">{{ trans('general.all_surgicals') }}</option>
                        @foreach ($surgicals as $surgical)
                            <option value="{{ $surgical->id }}">{{ $surgical->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" wire:model="booking_date" aria-label="{{ __('inpatient_trans.booking_date') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- 📋 Booking Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive" style="height: 500px;">
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
                            <th class="text-center">{{ __('general.advance_payment') }}</th>
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
                                $hasAdvance = \App\Models\Finance\AdvancePayment::where('booking_id', $booking->id)
                                    ->where('status', 'confirmed')
                                    ->exists();
                                $hasHistory = \App\Models\Administrative\SurgeryStatusHistory::where('surgery_booking_id', $booking->id)->exists();

                                $isScheduled = !empty($booking->room_id) && !empty($booking->scheduled_from) && !empty($booking->scheduled_to);
                            @endphp
                            <tr>
                                <td class="text-center">
                                    {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">

                                            {{-- ✅ Assign or Cancel Scheduling depending on status --}}
                                            @if ($isScheduled)
                                                @if (
                                                    $booking->status != 'cancelled' &&
                                                    !in_array($booking->status, [
                                                        'preparing_patient','preparing_room','ready_for_surgery',
                                                        'anesthesia_started','anesthesia_completed','incision_started','in_progress',
                                                        'on_hold','closing_wound','wound_closed','anesthesia_reversed','recovery_room',
                                                        'under_observation','discharged','completed','in_surgery','failed','postponed','transferred','room_canceled','arrived','admitted','anesthesia_approved',
                                                    ])
                                                )
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           wire:click.prevent="cancelSchedule({{ $booking->id }})">
                                                            <i class="ri-close-circle-line text-danger me-2"></i>
                                                            {{ __('surgical_trans.cancel_schedule') }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                       wire:click.prevent="openAssignModal({{ $booking->id }})">
                                                        <i class="ri-time-line text-info me-2"></i>
                                                        {{ __('surgical_trans.assign_room_and_time') }}
                                                    </a>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>

                                            {{-- ✅ Details (requires advance) --}}
                                            <li>
                                                <a class="dropdown-item" href="#" wire:click.prevent="openNursingDetails({{ $booking->id }})">
                                                    <i class="ri-eye-line text-secondary me-2"></i> {{ __('surgical_trans.view_details') }}
                                                </a>
                                            </li>

                                            {{-- ✅ Status transitions (based on advance) --}}
                                            @if ($hasAdvance)
                                                @if ($booking->status == 'arrived')
                                                    <li>
                                                        <a class="dropdown-item" href="#" wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'preparing_patient')">
                                                            <i class="ri-stethoscope-line text-info me-2"></i> {{ __('surgical_trans.preparing_patient') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'preparing_patient')
                                                    <li>
                                                        <a class="dropdown-item" href="#" wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'preparing_room')">
                                                            <i class="ri-hotel-bed-line text-warning me-2"></i> {{ __('surgical_trans.preparing_room') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'preparing_room')
                                                    <li>
                                                        <a class="dropdown-item" href="#" wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'ready_for_surgery')">
                                                            <i class="ri-check-line text-success me-2"></i> {{ __('surgical_trans.ready_for_surgery') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'anesthesia_reversed')
                                                    <li>
                                                        <a class="dropdown-item" href="#" wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'recovery_room')">
                                                            <i class="ri-hotel-bed-line text-success me-2"></i> {{ __('surgical_trans.transfer_to_recovery') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'recovery_room')
                                                    <li>
                                                        <a class="dropdown-item" href="#" wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'under_observation')">
                                                            <i class="ri-eye-line text-warning me-2"></i> {{ __('surgical_trans.under_observation') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'under_observation')
                                                    <li>
                                                        <a class="dropdown-item" href="#" wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'discharged')">
                                                            <i class="ri-logout-box-line text-success me-2"></i> {{ __('surgical_trans.discharge_patient') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'discharged')
                                                    <li>
                                                        <a class="dropdown-item" href="#" wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'completed')">
                                                            <i class="ri-logout-box-line text-success me-2"></i> {{ __('surgical_trans.complete_surgery') }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif

                                            {{-- Revert & Notes --}}
                                            @if ($hasHistory)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="#" wire:click.prevent="revertPrompt({{ $booking->id }})">
                                                        <i class="ri-arrow-go-back-line me-2"></i> {{ __('surgical_trans.revert_previous_status') }}
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item" href="#" wire:click.prevent="openNotes({{ $booking->id }})">
                                                    <i class="ri-file-list-line me-2"></i> {{ __('surgical_trans.case_notes') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                                {{-- Surgeries badges + Popover --}}
                                <td style="min-width:260px;">
                                    @php
                                        $services = $booking->services ?? collect();
                                        $count = $services->count();
                                        $limit = 1;

                                        $sideTitle = function ($side) {
                                            if (!$side) return __('surgical_trans.not_specified');
                                            return __('surgical_trans.' . match ($side) {
                                                'right' => 'right',
                                                'left'  => 'left',
                                                'both'  => 'both',
                                                default => 'not_specified',
                                            });
                                        };
                                    @endphp

                                    @if ($count > 0)
                                        @foreach ($services->take($limit) as $srv)
                                            <span class="badge bg-light text-dark border me-1 mb-1 d-inline-flex align-items-center">
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
                                                $moreHtml = $services->slice($limit)->map(function ($srv) use ($sideTitle) {
                                                    $label = e($srv->name);
                                                    $side  = isset($srv->pivot) && $srv->pivot->side
                                                        ? $sideTitle($srv->pivot->side)
                                                        : __('surgical_trans.not_specified');
                                                    return "<div class='mb-1'><i class=\"ri-scissors-cut-line me-1\"></i> {$label} <span class=\"badge bg-soft-primary text-primary ms-2\">{$side}</span></div>";
                                                })->implode('');
                                                $moreHtmlAttr = str_replace(['"', "'"], ['&quot;', '&#39;'], $moreHtml);
                                            @endphp

                                            <button type="button" class="btn btn-link p-0 text-secondary"
                                                    tabindex="0" role="button" data-bs-toggle="popover"
                                                    data-bs-trigger="focus" data-bs-container="body"
                                                    title="{{ __('surgical_trans.all_surgicals') }} ({{ $count }})"
                                                    data-bs-content="{!! $moreHtmlAttr !!}">
                                                {{ __('surgical_trans.show_more') }} (+{{ $count - $limit }})
                                            </button>
                                        @endif
                                    @else
                                        <span class="text--muted">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="text-center">
                                    @php
                                        $statusColors = [
                                            'booked'=>'warning','arrived'=>'info','admitted'=>'info',
                                            'preparing_patient'=>'primary','preparing_room'=>'primary','ready_for_surgery'=>'success',
                                            'anesthesia_started'=>'info','anesthesia_completed'=>'info','incision_started'=>'primary',
                                            'in_progress'=>'primary','on_hold'=>'warning','closing_wound'=>'warning',
                                            'wound_closed'=>'primary','anesthesia_reversed'=>'info','recovery_room'=>'info',
                                            'under_observation'=>'info','discharged'=>'success','completed'=>'success',
                                            'in_surgery'=>'warning','cancelled'=>'danger','failed'=>'danger',
                                            'postponed'=>'warning','transferred'=>'dark','room_canceled'=>'dark','confirmed'=>'success',
                                        ];
                                        $color = $statusColors[$booking->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-soft-{{ $color }} text-{{ $color }}">
                                        <i class="me-1 ri-check-line"></i>
                                        {{ __('surgical_trans.status_' . $booking->status) }}
                                    </span>
                                </td>

                                {{-- Patient Info --}}
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

                                {{-- Advance Payment Status --}}
                                <td class="text-center">
                                    @if($hasAdvance)
                                        <span class="badge bg-success">{{ __('general.paid') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('general.unpaid') }}</span>
                                    @endif
                                </td>

                                {{-- Room & Date --}}
                                <td><span class="badge bg-secondary">{{ $booking->room->name_ar ?? '-' }}</span></td>
                                <td>
                                    @if($booking->booking_date)
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                            @if($booking->scheduled_from && $booking->scheduled_to)
                                                <small class="text-muted d-block">{{ $booking->scheduled_from }} – {{ $booking->scheduled_to }}</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Diagnoses --}}
                                <td>
                                    @php
                                        $diagnosisCount = $booking->diagnoses ? count($booking->diagnoses) : 0;
                                        $displayLimit  = 1;
                                    @endphp
                                    @if ($diagnosisCount > 0)
                                        @foreach ($booking->diagnoses->take($displayLimit) as $diagnosis)
                                            <span class="badge bg-light text-dark mb-1 d-inline-block">{{ $diagnosis->name }}</span>
                                        @endforeach
                                        @if ($diagnosisCount > $displayLimit)
                                            <button type="button" class="btn btn-link p-0 text-primary"
                                                    tabindex="0" role="button" data-bs-toggle="popover"
                                                    data-bs-trigger="focus" data-bs-html="true"
                                                    title="{{ __('surgical_trans.all_diagnoses') }} ({{ $diagnosisCount }})"
                                                    data-bs-content="
                                                        @foreach ($booking->diagnoses->slice($displayLimit) as $diagnosis)
                                                            <div>{{ $diagnosis->name }}</div>
                                                        @endforeach
                                                    ">
                                                {{ __('surgical_trans.show_more') }} (+{{ $diagnosisCount - $displayLimit }})
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Medical Team Surgeon & Anesthesiologist --}}
                                <td>
                                    @php $surgeon = $booking->medicalTeam->firstWhere('role','surgeon'); @endphp
                                    {{ $surgeon ? $surgeon->medical_team->full_name : '-' }}
                                </td>
                                <td>
                                    @php $anesthesiologist = $booking->medicalTeam->firstWhere('role','anesthesiologist'); @endphp
                                    {{ $anesthesiologist ? $anesthesiologist->medical_team->full_name : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center py-4">
                                    <img src="{{ asset('assets/images/no-data.svg') }}" alt="{{ __('general.no_data') }}" height="100">
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

    {{-- ✅ Status Modal --}}
    <div wire:ignore.self class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="statusModalLabel">
                        <i class="ri-time-line me-1"></i> {{ __('surgical_trans.change_status') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('general.time') }}</label>
                        <input type="datetime-local" class="form-control" wire:model.defer="statusChangeTime">
                        @error('statusChangeTime') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ __('general.note_optional') }}</label>
                        <textarea rows="4" class="form-control" wire:model.defer="statusNote" placeholder="{{ __('general.write_note') }}"></textarea>
                        @error('statusNote') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('general.close') }}</button>
                    <button class="btn btn-primary" wire:click="saveStatusChange">
                        <i class="ri-save-3-line me-1"></i> {{ __('general.save_change') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Revert Modal --}}
    <div wire:ignore.self class="modal fade" id="revertModal" tabindex="-1" aria-labelledby="revertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="revertModalLabel">
                        <i class="ri-arrow-go-back-line me-1"></i> {{ __('surgical_trans.revert_previous_status') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <div>{{ __('surgical_trans.current_status') }}:
                            <span class="badge bg-info">{{ __('surgical_trans.status_' . ($currentStatusLabel ?? '')) }}</span>
                        </div>
                        <div class="mt-2">{{ __('surgical_trans.will_revert_to') }}:
                            <span class="badge bg-secondary">{{ __('surgical_trans.status_' . ($previousStatusLabel ?? '')) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('surgical_trans.revert_note') }}</label>
                        <textarea rows="4" class="form-control" wire:model.defer="revertNote" placeholder="{{ __('general.write_note') }}"></textarea>
                        @error('revertNote') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ __('surgical_trans.revert_time') }}</label>
                        <input type="datetime-local" class="form-control" wire:model.defer="revertTime">
                        @error('revertTime') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('general.close') }}</button>
                    <button class="btn btn-warning" wire:click="confirmRevert">
                        <i class="ri-check-line me-1"></i> {{ __('surgical_trans.confirm_revert') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Notes Modal --}}
    <div wire:ignore.self class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="notesModalLabel">
                        <i class="ri-file-list-line me-1"></i> {{ __('surgical_trans.case_notes_log') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
                </div>
                <div class="modal-body">
                    @if($historyItems && count($historyItems))
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.time') }}</th>
                                        <th>{{ __('surgical_trans.status') }}</th>
                                        <th>{{ __('general.user') }}</th>
                                        <th>{{ __('general.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historyItems as $h)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($h['status_changed_at'])->format('d/m/Y H:i') ?? '-' }}</td>
                                            <td><span class="badge bg-soft-primary text-primary">{{ __('surgical_trans.status_' . $h['status']) }}</span></td>
                                            <td>{{ $h['user_name'] ?? '-' }}</td>
                                            <td>{{ $h['note'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">{{ __('general.no_notes') }}</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('general.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Cancel Booking Modal --}}
    <div wire:ignore.self class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelModalLabel">
                        <i class="ri-close-circle-line me-1"></i> {{ __('surgical_trans.confirm_cancel_title') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">{{ __('surgical_trans.cancel_reason') }}</label>
                    <textarea class="form-control" rows="4" wire:model.defer="cancelReason" placeholder="{{ __('surgical_trans.cancel_reason_placeholder') }}"></textarea>
                    @error('cancelReason') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('general.close') }}</button>
                    <button class="btn btn-danger" wire:click="cancelBookingConfirmed">
                        <i class="ri-check-line me-1"></i> {{ __('surgical_trans.confirm_cancel_button') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Assign Room & Time Modal --}}
    <div wire:ignore.self class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="assignModalLabel">
              <i class="ri-time-line me-1"></i> {{ __('surgical_trans.assign_room_and_time') }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">{{ __('surgical_trans.room') }}</label>
                <select class="form-select" wire:model="assignRoomId" id="assignRoomId">
                  <option value="">{{ __('surgical_trans.select_room') }}</option>
                  @foreach($rooms as $r)
                    <option value="{{ $r->id }}">{{ $r->name_ar ?? $r->name_en ?? ('Room #'.$r->id) }}</option>
                  @endforeach
                </select>
                @error('assignRoomId') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="col-md-4">
                <label class="form-label">{{ __('surgical_trans.date') }}</label>
                <input type="date" class="form-control" wire:model="assignDate" id="assignDate">
                @error('assignDate') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100" id="btnLoadSlots">
                  <i class="ri-refresh-line me-1"></i> {{ __('surgical_trans.load_slots') }}
                </button>
              </div>
            </div>

            <hr class="my-3">

            {{-- Display Slots --}}
            <div id="slotsArea">
              <div class="d-flex flex-wrap gap-2">
                @if(count($timeSlots))
                  @foreach($timeSlots as $s)
                    @php $reserved = $s['reserved'] ?? false; @endphp
                    <button type="button"
                            class="btn btn-sm {{ $reserved ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                            @if(!$reserved)
                              wire:click="setPickedSlot('{{ $s['start'] }}','{{ $s['end'] }}')"
                            @endif
                            @if($reserved) disabled @endif>
                      {{ $s['start'] }} – {{ $s['end'] }}
@if($reserved)
    <span class="ms-1 badge bg-secondary">{{ __('surgical_trans.booked') }}</span>
@endif
                    </button>
                  @endforeach
                @else
                  <div class="text-muted">{{ __('surgical_trans.press_load_slots') }}</div>
                @endif
              </div>
            </div>

            <hr class="my-3">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">{{ __('surgical_trans.start_time') }}</label>
                <input type="time" class="form-control" wire:model="assignStart" id="assignStart">
                @error('assignStart') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('surgical_trans.end_time') }}</label>
                <input type="time" class="form-control" wire:model="assignEnd" id="assignEnd">
                @error('assignEnd') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-light" data-bs-dismiss="modal">{{ __('general.close') }}</button>
            <button class="btn btn-primary" wire:click="saveAssignRoomAndTime">
              <i class="ri-save-3-line me-1"></i> {{ __('surgical_trans.save_schedule') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- ✅ Scripts for Modals & Popovers & Slots Loading --}}
    <div>
       <script>
        document.addEventListener('livewire:load', function () {
            /* ========= أدوات مساعدة للمودالات ========= */
            const showModal = (id) => bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
            const hideModal = (id) => {
                const m = bootstrap.Modal.getInstance(document.getElementById(id));
                if (m) m.hide();
            };

            /* ========= Events من Livewire ========= */
            Livewire.on('showCancelModal', () => showModal('cancelModal'));
            Livewire.on('hideCancelModal', () => hideModal('cancelModal'));

            Livewire.on('showStatusModal', () => showModal('statusModal'));
            Livewire.on('hideStatusModal', () => hideModal('statusModal'));

            Livewire.on('showRevertModal', () => showModal('revertModal'));
            Livewire.on('hideRevertModal', () => hideModal('revertModal'));

            Livewire.on('showNotesModal', () => showModal('notesModal'));

            Livewire.on('showAssignModal', () => showModal('assignModal'));
            Livewire.on('hideAssignModal', () => hideModal('assignModal'));

            /* ========= تهيئة Popovers ========= */
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

            // تشغيل أولي
            initPopovers();

            // إعادة التهيئة بعد كل تحديث من Livewire
            Livewire.hook('message.processed', () => {
                initPopovers();
            });

            /* ========= تحميل الفتحات (AJAX) ========= */
            const btnLoadSlots = document.getElementById('btnLoadSlots');
            const roomInput    = document.getElementById('assignRoomId');
            const dateInput    = document.getElementById('assignDate');

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
                    const resp = await fetch(url + '?' + new URLSearchParams({ id_room, booking_date }), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
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

            // (اختياري) تحميل تلقائي عند تغيير الغرفة/التاريخ
            // if (roomInput) roomInput.addEventListener('change', loadSlots);
            // if (dateInput) dateInput.addEventListener('change', loadSlots);
        });
        </script>
  <script>
            document.addEventListener('livewire:load', function() {
                // تهيئة select2 عند التحميل
                $('#surgicalSelect').select2({
                    placeholder: "{{ trans('general.all_surgicals') }}",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "{{ trans('general.no_results_found') }}";
                        }
                    }
                });

                // عند تغيير الاختيار في Select2، يتم تحديث الخاصية في Livewire
                $('#surgicalSelect').on('change', function(e) {
                    let value = $(this).val();
                    @this.set('surgical_id', value);
                });

                // إعادة تهيئة Select2 بعد كل تحديث من Livewire
                Livewire.hook('message.processed', (message, component) => {
                    $('#surgicalSelect').select2({
                        placeholder: "{{ trans('general.all_surgicals') }}",
                        allowClear: true,
                        width: '100%',
                    });
                });
            });
        </script>
    </div>
</div>
<style>
    /* Custom styles if needed */   
    h5{
        color: #fff !important;
    }
</style>