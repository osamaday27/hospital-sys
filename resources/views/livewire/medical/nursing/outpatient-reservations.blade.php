<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        @php
            $statCards = [
                'total' => ['icon' => 'mdi mdi-account-multiple', 'color' => 'secondary', 'bg' => 'bg-soft-secondary'],
                'reserved' => ['icon' => 'mdi mdi-calendar-clock', 'color' => 'primary', 'bg' => 'bg-soft-primary'],
                'pending' => ['icon' => 'mdi mdi-clock-alert', 'color' => 'warning', 'bg' => 'bg-soft-warning'],
                'nursing_session_ended' => [
                    'icon' => 'mdi mdi-check-circle',
                    'color' => 'success',
                    'bg' => 'bg-soft-success',
                ],
                'cancelled' => ['icon' => 'mdi mdi-cancel', 'color' => 'danger', 'bg' => 'bg-soft-danger'],
            ];
        @endphp

        @foreach ($statCards as $key => $card)
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card card-animate border-0 shadow-none {{ $card['bg'] }}">
                    <div class="card-body text-center p-3">
                        <div class="avatar-sm mx-auto mb-3">
                            <span class="avatar-title rounded fs-2 {{ $card['bg'] }} text-{{ $card['color'] }}">
                                <i class="{{ $card['icon'] }}"></i>
                            </span>
                        </div>
                        <h5 class="text-muted mb-1">{{ __('outpatient_trans.' . $key) }}</h5>
                        <h3 class="mt-2 mb-0 text-{{ $card['color'] }} fw-bold">{{ $stats[$key] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-none bg-soft-info">
                <div class="card-body text-center p-3">
                    <button wire:click="$emit('refreshTable')" class="btn btn-soft-dark px-3 py-2 w-100">
                        <i class="mdi mdi-refresh align-middle me-1"></i>
                        {{ __('outpatient_trans.refresh') }}
                    </button>
                    <small class="d-block text-muted mt-2">
                        <i class="mdi mdi-clock-outline me-1"></i>
                        {{ __('outpatient_trans.last_update') }}
                        <span class="fw-semibold">{{ $lastRefresh }}</span>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <!-- Card Header with Filters -->
        <div class="card-header bg-primary bg-opacity-10 border-bottom p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-hospital-building fs-3 text-primary me-2" style="color: #fff !important;"></i>
                    <h5 class="mb-0 text-primary fw-semibold" style="color: #fff !important;">
                        {{ __('outpatient_trans.reservation_management') }}
                    </h5>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <!-- Clinic Filter -->
                    <div class="position-relative" style="width: 200px;">
                        <i
                            class="mdi mdi-domain position-absolute start-0 top-50 translate-middle-y ms-2 text-muted"></i>
                        <select class="form-select form-select-sm ps-4 select2" wire:model="clinic_id">
                            <option value="">{{ __('outpatient_trans.select_clinic') }}</option>
                            @foreach ($clinics as $clinic)
                                <option value="{{ $clinic->id }}">{{ $clinic->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Doctor Filter -->
                    <div class="position-relative" style="width: 200px;">
                        <i
                            class="mdi mdi-doctor position-absolute start-0 top-50 translate-middle-y ms-2 text-muted"></i>
                        <select class="form-select form-select-sm ps-4 select2" wire:model="doctor_id">
                            <option value="">{{ __('outpatient_trans.select_doctor') }}</option>
                            @foreach ($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="position-relative" style="width: 200px;">
                        <i
                            class="mdi mdi-filter-menu position-absolute start-0 top-50 translate-middle-y ms-2 text-muted"></i>
                        <select class="form-select form-select-sm ps-4 select2" wire:model="status">
                            <option value="">{{ __('outpatient_trans.select_status') }}</option>
                            <option value="reserved">{{ __('outpatient_trans.reserved') }}</option>
                            <option value="unconfirmed">{{ __('outpatient_trans.unconfirmed') }}</option>
                            <option value="nursing_session_ended">{{ __('outpatient_trans.completed') }}</option>
                            <option value="cancelled">{{ __('outpatient_trans.cancelled') }}</option>
                            <option value="confirmed">{{ __('outpatient_trans.confirmed') }}</option>
                            <option value="confirm_waiting_for_payment">
                                {{ __('outpatient_trans.confirm_waiting_for_payment') }}
                            </option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="position-relative" style="width: 220px;">
                        <i
                            class="mdi mdi-magnify position-absolute start-0 top-50 translate-middle-y ms-2 text-muted"></i>
                        <input type="text" wire:model.debounce.500ms="search"
                            class="form-control form-control-sm ps-4"
                            placeholder="{{ trans('outpatient_trans.search') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            <div class="table-responsive" style="height: 100% !important;min-height: 500px;">
                <table class="table table-borderless table-hover align-middle mb-0">
                    <thead class="bg-primary bg-opacity-10">
                        <tr>
                            <th width="50" class="ps-4">#</th>
                            <th width="120">{{ trans('outpatient_trans.action') }}</th>
                            <th width="150">{{ trans('outpatient_trans.status') }}</th>
                            <th>{{ trans('outpatient_trans.clinc_doctor') }}</th>
                            <th width="100">{{ trans('outpatient_trans.q_number') }}</th>
                            <th width="120">{{ trans('outpatient_trans.patient_id') }}</th>
                            <th>{{ trans('outpatient_trans.patient_name') }}</th>
                            <th width="120">{{ trans('outpatient_trans.patient_phone') }}</th>
                            <th width="80">{{ trans('outpatient_trans.age') }}</th>
                            <th width="150">{{ trans('outpatient_trans.patient_contract') }}</th>
                            <th width="150">{{ trans('outpatient_trans.reservations_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $index => $reservation)
                            <tr class="border-bottom border-light">
                                <td class="ps-4 fw-semibold">{{ $index + $reservations->firstItem() }}</td>
                                <td>
                                    @if (
                                        $reservation->status !== 'unconfirmed' &&
                                            $reservation->status !== 'confirm_waiting_for_payment' &&
                                            $reservation->status !== 'nursing_session_ended')
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <!-- الإجراءات المعتادة -->
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center py-2"
                                                        target="_blank"
                                                        href="{{ route('n_start_session_out.start', ['id' => Crypt::encryptString($reservation->id)]) }}">
                                                        <i class="mdi mdi-play-circle-outline text-primary me-2"></i>
                                                        {{ trans('buttons.open_session') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center py-2"
                                                        href="{{ route('nursing.add_services', ['reservation_id' => $reservation->id]) }}">
                                                        <i class="mdi mdi-needle text-info me-2"></i>
                                                        {{ trans('buttons.add_services') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center py-2"
                                                        href="{{ route('nursing.add_supplies', ['reservation_id' => $reservation->id]) }}">
                                                        <i class="mdi mdi-medical-bag text-warning me-2"></i>
                                                        {{ trans('buttons.add_supplies') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center py-2"
                                                        href="#">
                                                        <i class="mdi mdi-pill text-success me-2"></i>
                                                        {{ trans('buttons.add_drugs') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider my-1">
                                                </li>
                                                <li>
                                                    <button
                                                        class="dropdown-item d-flex align-items-center py-2 text-danger"
                                                        wire:click="cancelModal({{ $reservation->id }})">
                                                        <i class="mdi mdi-cancel me-2"></i>
                                                        {{ trans('outpatient_trans.cancel') }}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    @elseif($reservation->status === 'unconfirmed' || $reservation->status === 'confirm_waiting_for_payment')
                                        <span class="text-muted">
                                            <i class="mdi mdi-lock-outline me-1"></i>
                                            {{ __('outpatient_trans.actions_locked_due_to_payment') }}
                                        </span>
                                    @elseif($reservation->status === 'nursing_session_ended')
                                        <span class="text-success">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $statusConfig = [
                                            'reserved' => ['icon' => 'mdi mdi-calendar-clock', 'color' => 'primary'],
                                            'pending' => ['icon' => 'mdi mdi-clock', 'color' => 'warning'],
                                            'cancelled' => ['icon' => 'mdi mdi-cancel', 'color' => 'danger'],
                                            'nursing_session_ended' => [
                                                'icon' => 'mdi mdi-check-circle',
                                                'color' => 'success',
                                            ],
                                            'nursing_session_started' => [
                                                'icon' => 'mdi mdi-play-circle-outline',
                                                'color' => 'info',
                                            ],
                                            'confirmed_paid' => ['icon' => 'mdi mdi-shield-check', 'color' => 'info'],
                                            'unconfirmed' => ['icon' => 'mdi mdi-cash-clock', 'color' => 'warning'],
                                            'confirm_waiting_for_payment' => [
                                                'icon' => 'mdi mdi-cash-clock',
                                                'color' => 'warning',
                                            ],
                                        ];
                                        $statusInfo = $statusConfig[$reservation->status] ?? [
                                            'icon' => '',
                                            'color' => 'info',
                                        ];
                                    @endphp

                                    <span
                                        class="badge bg-soft-{{ $statusInfo['color'] }} text-{{ $statusInfo['color'] }} rounded-pill mb-1">
                                        <i class="{{ $statusInfo['icon'] }} me-1"></i>
                                        {{ __('outpatient_trans.' . $reservation->status) }}
                                    </span>

                                    @if ($reservation->patient_visits)
                                        @php
                                            $visitConfig = [
                                                'examination' => [
                                                    'icon' => 'mdi mdi-stethoscope',
                                                    'color' => 'success',
                                                ],
                                                'follow_up' => ['icon' => 'mdi mdi-clipboard-check', 'color' => 'info'],
                                                'follow_up_opr' => [
                                                    'icon' => 'mdi mdi-hospital-box',
                                                    'color' => 'warning',
                                                ],
                                            ];
                                            $visitInfo = $visitConfig[$reservation->patient_visits->type] ?? null;
                                        @endphp

                                        @if ($visitInfo)
                                            <span
                                                class="badge bg-soft-{{ $visitInfo['color'] }} text-{{ $visitInfo['color'] }} rounded-pill d-block mt-1">
                                                <i class="{{ $visitInfo['icon'] }} me-1"></i>
                                                {{ __('outpatient_trans.' . $reservation->patient_visits->type) }}
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $reservation->clinic->name_ar ?? '-' }}</span>
                                        <small class="text-muted">
                                            <i class="mdi mdi-doctor me-1"></i>
                                            {{ $reservation->doctor->full_name ?? '-' }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary fw-bold fs-12">
                                        {{ $reservation->q_number }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-soft-dark text-dark">
                                        <i class="mdi mdi-identifier me-1"></i>
                                        {{ $reservation->patient->medical_number ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="{{ asset('assets/images/users/avatar-1.jpg') }}"
                                                class="rounded-circle avatar-xs"
                                                alt="{{ $reservation->patient->full_name ?? '-' }}">
                                        </div>
                                        <div class="flex-grow-1">
                                            <span
                                                class="fw-semibold">{{ $reservation->patient->full_name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="tel:{{ $reservation->patient->phone_number }}"
                                        class="text-decoration-none">
                                        <i class="mdi mdi-phone-outline me-1 text-muted"></i>
                                        {{ $reservation->patient->phone_number }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-dark text-dark">
                                        {{ \Carbon\Carbon::parse($reservation->patient->date_of_birth)->age ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-soft-info text-info">
                                        <i class="mdi mdi-file-document-outline me-1"></i>
                                        {{ $this->getcontractname($reservation->patient->insurance_contracting) ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">
                                            <i class="mdi mdi-calendar-outline me-1 text-muted"></i>
                                            {{ \Carbon\Carbon::parse($reservation->date_reservations)->format('d/m/Y') }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="mdi mdi-clock-outline me-1"></i>
                                            {{ \Carbon\Carbon::parse($reservation->date_reservations)->format('H:i') }}
                                        </small>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <img src="{{ asset('assets/images/no-data.svg') }}" alt="No data"
                                            style="height: 120px;">
                                        <h5 class="mt-3 text-muted">{{ __('outpatient_trans.no_reservations_found') }}
                                        </h5>
                                        <button wire:click="resetFilters" class="btn btn-sm btn-soft-primary mt-2">
                                            <i class="mdi mdi-filter-remove me-1"></i>
                                            {{ __('outpatient_trans.clear_filters') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($reservations->hasPages())
                <div class="card-footer bg-transparent border-top py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="mdi mdi-information-outline me-1"></i>
                            {{ __('Showing') }}
                            <span class="fw-semibold">{{ $reservations->firstItem() }}</span>
                            {{ __('to') }}
                            <span class="fw-semibold">{{ $reservations->lastItem() }}</span>
                            {{ __('of') }}
                            <span class="fw-semibold">{{ $reservations->total() }}</span>
                            {{ __('entries') }}
                        </div>
                        <div class="d-flex align-items-center">
                            {{ $reservations->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Cancel Reservation Modal -->
    <div wire:ignore.self class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="confirmCancel">
                <div class="modal-content border-0 shadow-sm">
                    <div class="modal-header bg-danger bg-opacity-10 border-bottom p-3">
                        <h5 class="modal-title text-danger">
                            <i class="mdi mdi-cancel me-2"></i>
                            {{ trans('outpatient_trans.cancel_reservation_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="mdi mdi-comment-question-outline me-1"></i>
                                {{ trans('outpatient_trans.reason_for_cancellation') }}
                            </label>
                            <textarea wire:model.defer="cancelReason" class="form-control" rows="4"
                                placeholder="{{ trans('outpatient_trans.enter_cancel_reason') }}"></textarea>
                            @error('cancelReason')
                                <span class="text-danger small mt-1">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3">
                        <button type="button" class="btn btn-soft-info" data-bs-dismiss="modal">
                            <i class="mdi mdi-close me-1"></i>
                            {{ trans('outpatient_trans.close') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            {{ trans('outpatient_trans.yes_cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize Select2 with MDI icons
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('body'),
            templateResult: formatOption,
            templateSelection: formatOption
        });

        function formatOption(option) {
            if (!option.id) return option.text;

            // Add icons to specific options
            if ($(option.element).parent().attr('wire:model') === 'clinic_id') {
                return $('<span><i class="mdi mdi-domain me-2"></i>' + option.text + '</span>');
            }
            if ($(option.element).parent().attr('wire:model') === 'doctor_id') {
                return $('<span><i class="mdi mdi-doctor me-2"></i>' + option.text + '</span>');
            }
            if ($(option.element).parent().attr('wire:model') === 'status') {
                const icons = {
                    'reserved': 'mdi-calendar-clock',
                    'pending': 'mdi-clock',
                    'completed': 'mdi-check-circle',
                    'cancelled': 'mdi-cancel',
                    'confirmed': 'mdi-shield-check',
                    'waiting_for_payment': 'mdi-cash-clock'
                };
                const icon = icons[option.id] || 'mdi-help-circle';
                return $('<span><i class="mdi ' + icon + ' me-2"></i>' + option.text + '</span>');
            }
            return option.text;
        }

        // Livewire integration
        $('.select2').on('change', function() {
            @this.set($(this).attr('wire:model'), $(this).val());
        });

        // Modal events
        window.addEventListener('show-cancel-modal', () => {
            $('#cancelModal').modal('show');
        });

        window.addEventListener('hide-cancel-modal', () => {
            $('#cancelModal').modal('hide');
        });
    });
</script>
