<div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18 ">{{ trans('surgical_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="surgical_programs" class="text-muted">
                                {{ trans('main_trans.programs') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-dark">{{ trans('surgical_trans.surgical_anesthesia') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ trans('buttons.close') }}"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i> {{ session('alerts.error') }}</i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ trans('buttons.close') }}"></button>
        </div>
    @endif

    <div class="card mb-3 border-primary">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="search-box">
                        <input type="search"
                               class="form-control"
                               placeholder="{{ trans('surgical_trans.search_patient') }}"
                               wire:model.debounce.500ms="search">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="room_id">
                        <option value="">{{ trans('surgical_trans.all_rooms') }}</option>
                             @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">
                                {{ app()->getLocale() === 'ar' ? $room->name_ar : $room->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="surgical_id">
                        <option value="">{{ trans('surgical_trans.all_surgicals') }}</option>
                        @foreach ($surgicals as $surgical)
                            <option value="{{ $surgical->id }}">{{ $surgical->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" wire:model="booking_date" aria-label="{{ trans('inpatient_trans.booking_date') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive" style="min-height: 500px;">
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
                            <th class="text-center">{{ trans('finance_trans.advance_payment') }}</th>
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
                                $hasHistory = \App\models\administrative\surgerystatushistory::where('surgery_booking_id', $booking->id)->exists();
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ trans('surgical_trans.more_actions') }}">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                {{-- التفاصيل لا تُفتح إلا بعد دفعة مقدّمة مؤكّدة (منطقك محفوظ) --}}
                                                <a class="dropdown-item" href="#" wire:click.prevent="openAnesthesiaDetails({{ $booking->id }})">
                                                    <i class="ri-eye-line text-secondary me-2"></i> {{ trans('surgical_trans.show_details') }}
                                                </a>
                                            </li>

                                            @if($hasAdvance)
                                                @if ($booking->status == 'ready_for_surgery')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'anesthesia_started')">
                                                            <i class="ri-contrast-drop-2-line text-info me-2"></i>
                                                            {{ trans('surgical_trans.action_anesthesia_started') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'anesthesia_started')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'anesthesia_completed')">
                                                            <i class="ri-checkbox-circle-line text-success me-2"></i>
                                                            {{ trans('surgical_trans.action_anesthesia_completed') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'closing_wound')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'anesthesia_reversed')">
                                                            <i class="ri-arrow-go-back-line text-info me-2"></i>
                                                            {{ trans('surgical_trans.action_anesthesia_reversed') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'anesthesia_reversed')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'recovery_room')">
                                                            <i class="ri-hotel-bed-line text-success me-2"></i>
                                                            {{ trans('surgical_trans.action_recovery_room') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'recovery_room')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'under_observation')">
                                                            <i class="ri-eye-line text-warning me-2"></i>
                                                            {{ trans('surgical_trans.action_under_observation') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($booking->status == 'under_observation')
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           wire:click.prevent="changeStatusPrompt({{ $booking->id }}, 'discharged')">
                                                            <i class="ri-logout-box-line text-success me-2"></i>
                                                            {{ trans('surgical_trans.action_discharged') }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif

                                            {{-- زر الإلغاء مُعطّل حسب تعليقك السابق، أسيبه ككومنت --}}
                                            {{-- <li>
                                                <a class="dropdown-item" href="#"
                                                   wire:click.prevent="confirmCancel({{ $booking->id }})">
                                                    <i class="ri-close-circle-line text-danger me-2"></i>
                                                    {{ trans('surgical_trans.cancel_booking') }}
                                                </a>
                                            </li> --}}

                                            @if($hasHistory)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                       wire:click.prevent="revertPrompt({{ $booking->id }})">
                                                        <i class="ri-arrow-go-back-line me-2"></i>
                                                        {{ trans('surgical_trans.revert_to_previous') }}
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                   wire:click.prevent="openNotes({{ $booking->id }})">
                                                    <i class="ri-file-list-line me-2"></i>
                                                    {{ trans('surgical_trans.case_notes') }}
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </td>

                                {{-- العمليات كبادجات + Popover للباقي --}}
                                <td style="min-width:260px;">
                                    @php
                                        $services = $booking->services ?? collect();
                                        $count = $services->count();
                                        $limit = 1;

                                        $sideTitle = function ($side) {
                                            if (!$side) return __('surgical_trans.not_specified');
                                            $key = match ($side) {
                                                'right' => 'right',
                                                'left'  => 'left',
                                                'both'  => 'both',
                                                default => 'not_specified',
                                            };
                                            return __('surgical_trans.' . $key);
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

                                            <button type="button"
                                                    class="btn btn-link p-0 text-secondary"
                                                    tabindex="0" role="button"
                                                    data-bs-toggle="popover"
                                                    data-bs-trigger="focus"
                                                    data-bs-html="true"
                                                    data-bs-container="body"
                                                    title="{{ __('surgical_trans.all_surgicals') }} ({{ $count }})"
                                                    data-bs-content="{!! $moreHtmlAttr !!}">
                                                {{ __('surgical_trans.show_more') }} (+{{ $count - $limit }})
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ trans('surgical_trans.no_data_short') }}</span>
                                    @endif
                                </td>

                              

                                <td class="text-center">
                                    @php
                                        $statusColors = [
                                            'booked' => 'warning',
                                            'arrived' => 'info',
                                            'preparing_patient' => 'primary',
                                            'preparing_room' => 'primary',
                                            'ready_for_surgery' => 'success',
                                            'anesthesia_started' => 'info',
                                            'anesthesia_completed' => 'info',
                                            'incision_started' => 'primary',
                                            'in_progress' => 'primary',
                                            'closing_wound' => 'warning',
                                            'wound_closed' => 'primary',
                                            'anesthesia_reversed' => 'info',
                                            'recovery_room' => 'info',
                                            'under_observation' => 'info',
                                            'discharged' => 'success',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                        ];
                                        $color = $statusColors[$booking->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-soft-{{ $color }} text-{{ $color }}">
                                        <i class="me-1 ri-check-line"></i>
                                        {{ __('surgical_trans.status_' . $booking->status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                    {{ mb_substr($booking->patient->full_name ?? trans('surgical_trans.na_short'), 0, 1) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">{{ $booking->patient->full_name ?? trans('surgical_trans.na') }}</div>
                                    </div>
                                </td>

                                <td>{{ $booking->patient->medical_number ?? trans('surgical_trans.na') }}</td>
                                <td class="text-center">{{ $booking->patient->age ?? trans('surgical_trans.na') }}</td>
                                <td>{{ $booking->patient->phone_number ?? trans('surgical_trans.na') }}</td>
                                <td><span class="badge bg-info">{{ $booking->contract_full_name ?? trans('surgical_trans.na') }}</span></td>

                                <td class="text-center">
                                    @if($hasAdvance)
                                        <span class="badge bg-success">{{ trans('finance_trans.paid') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ trans('finance_trans.unpaid') }}</span>
                                    @endif
                                </td>

                                <td><span class="badge bg-secondary">{{ $booking->room->name_en ?? trans('surgical_trans.na') }}</span></td>
                                <td><span class="fw-medium">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y H:i') }}</span></td>

                                <td>
                                    @php
                                        $diagnosisCount = $booking->diagnoses ? count($booking->diagnoses) : 0;
                                        $displayLimit = 1;
                                    @endphp
                                    @if($diagnosisCount > 0)
                                        @foreach($booking->diagnoses->take($displayLimit) as $diagnosis)
                                            <span class="badge bg-light text-dark mb-1 d-inline-block">{{ $diagnosis->name }}</span>
                                        @endforeach
                                        @if($diagnosisCount > $displayLimit)
                                            <button type="button" class="btn btn-link p-0 text-primary" tabindex="0" role="button"
                                                data-bs-toggle="popover" data-bs-trigger="focus"
                                                title="{{ trans('surgical_trans.full_diagnoses') }} ({{ $diagnosisCount }})"
                                                data-bs-html="true"
                                                data-bs-content="
                                                    @foreach($booking->diagnoses->slice($displayLimit) as $diagnosis)
                                                        <div>{{ $diagnosis->name }}</div>
                                                    @endforeach
                                                ">
                                                {{ trans('surgical_trans.show_more') }} (+{{ $diagnosisCount - $displayLimit }})
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ trans('surgical_trans.no_data_short') }}</span>
                                    @endif
                                </td>

                                <td>
                                    @php $surgeon = $booking->medicalTeam->firstWhere('role', 'surgeon'); @endphp
                                    {{ $surgeon ? $surgeon->medical_team->full_name : trans('surgical_trans.na') }}
                                </td>
                                <td>
                                    @php $anesthesiologist = $booking->medicalTeam->firstWhere('role', 'anesthesiologist'); @endphp
                                    {{ $anesthesiologist ? $anesthesiologist->medical_team->full_name : trans('surgical_trans.na') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center py-4">
                                    <img src="{{ asset('assets/images/no-data.svg') }}" alt="{{ trans('surgical_trans.no_data_alt') }}" height="100">
                                    <h5 class="mt-3 text-muted">{{ trans('surgical_trans.no_bookings_found') }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- مودال تغيير الحالة --}}
    <div wire:ignore.self class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="statusModalLabel"><i class="ri-time-line me-1"></i> {{ trans('surgical_trans.change_status') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ trans('buttons.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ trans('surgical_trans.time') }}</label>
                        <input type="datetime-local" class="form-control" wire:model.defer="statusChangeTime" aria-label="{{ trans('surgical_trans.time') }}">
                        @error('statusChangeTime') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ trans('surgical_trans.note_optional') }}</label>
                        <textarea rows="4" class="form-control" wire:model.defer="statusNote" placeholder="{{ trans('surgical_trans.write_note') }}"></textarea>
                        @error('statusNote') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ trans('buttons.close') }}</button>
                    <button class="btn btn-primary" wire:click="saveStatusChange">
                        <i class="ri-save-3-line me-1"></i> {{ trans('buttons.save_change') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال الرجوع للحالة السابقة --}}
    <div wire:ignore.self class="modal fade" id="revertModal" tabindex="-1" aria-labelledby="revertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="revertModalLabel"><i class="ri-arrow-go-back-line me-1"></i> {{ trans('surgical_trans.revert_to_previous') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('buttons.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <div>{{ trans('surgical_trans.current_status') }}:
                            <span class="badge bg-info">{{ __('surgical_trans.status_' . ($currentStatusLabel ?? '')) }}</span>
                        </div>
                        <div class="mt-2">{{ trans('surgical_trans.will_revert_to') }}:
                            <span class="badge bg-secondary">{{ __('surgical_trans.status_' . ($previousStatusLabel ?? '')) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('surgical_trans.revert_reason') }}</label>
                        <textarea rows="4" class="form-control" wire:model.defer="revertNote" placeholder="{{ trans('surgical_trans.write_note') }}"></textarea>
                        @error('revertNote') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ trans('surgical_trans.revert_time') }}</label>
                        <input type="datetime-local" class="form-control" wire:model.defer="revertTime" aria-label="{{ trans('surgical_trans.revert_time') }}">
                        @error('revertTime') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ trans('buttons.close') }}</button>
                    <button class="btn btn-warning" wire:click="confirmRevert">
                        <i class="ri-check-line me-1"></i> {{ trans('buttons.confirm_revert') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال ملاحظات الحالة --}}
    <div wire:ignore.self class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="notesModalLabel"><i class="ri-file-list-line me-1"></i> {{ trans('surgical_trans.case_notes_log') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ trans('buttons.close') }}"></button>
                </div>
                <div class="modal-body">
                    @if($historyItems && count($historyItems))
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ trans('surgical_trans.time') }}</th>
                                        <th>{{ trans('surgical_trans.status') }}</th>
                                        <th>{{ trans('surgical_trans.user') }}</th>
                                        <th>{{ trans('surgical_trans.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historyItems as $h)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($h['status_changed_at'])->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-soft-primary text-primary">{{ __('surgical_trans.status_' . $h['status']) }}</span></td>
                                            <td>{{ $h['user_name'] ?? trans('surgical_trans.na') }}</td>
                                            <td>{{ $h['note'] ?? trans('surgical_trans.na') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">{{ trans('surgical_trans.no_notes_found') }}</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ trans('buttons.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إلغاء الحجز --}}
    <div wire:ignore.self class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelModalLabel"><i class="ri-close-circle-line me-1"></i> {{ trans('surgical_trans.cancel_booking_confirm') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ trans('buttons.close') }}"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">{{ trans('surgical_trans.cancel_reason') }}</label>
                    <textarea class="form-control" rows="4" wire:model.defer="cancelReason" placeholder="{{ trans('surgical_trans.write_cancel_reason') }}"></textarea>
                    @error('cancelReason') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ trans('buttons.close') }}</button>
                    <button class="btn btn-danger" wire:click="cancelBookingConfirmed">
                        <i class="ri-check-line me-1"></i> {{ trans('buttons.confirm_cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- سكربتات المودالات والـ Popover --}}
    <div>
        <script>
            document.addEventListener('livewire:load', function() {
                Livewire.on('showCancelModal', () => { new bootstrap.Modal(document.getElementById('cancelModal')).show(); });
                Livewire.on('hideCancelModal', () => { const m = bootstrap.Modal.getInstance(document.getElementById('cancelModal')); m && m.hide(); });

                Livewire.on('showStatusModal', () => { new bootstrap.Modal(document.getElementById('statusModal')).show(); });
                Livewire.on('hideStatusModal', () => { const m = bootstrap.Modal.getInstance(document.getElementById('statusModal')); m && m.hide(); });

                Livewire.on('showRevertModal', () => { new bootstrap.Modal(document.getElementById('revertModal')).show(); });
                Livewire.on('hideRevertModal', () => { const m = bootstrap.Modal.getInstance(document.getElementById('revertModal')); m && m.hide(); });

                Livewire.on('showNotesModal', () => { new bootstrap.Modal(document.getElementById('notesModal')).show(); });

                const initPopovers = () => {
                    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                        const inst = bootstrap.Popover.getInstance(el);
                        if (inst) inst.dispose();
                    });
                    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                        new bootstrap.Popover(el, { container: 'body', html: true, trigger: el.getAttribute('data-bs-trigger') || 'focus', sanitize: false });
                    });
                };
                initPopovers();
                Livewire.hook('message.processed', () => { initPopovers(); });
            });
        </script>
    </div>
</div>
