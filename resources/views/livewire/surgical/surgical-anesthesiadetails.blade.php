<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="text-primary mb-0">
            <i class="mdi mdi-information-outline me-1"></i>
            {{ trans('surgical_trans.operation_details') }}
        </h5>
        <button wire:click="$emit('printOperationDetails')" class="btn btn-sm btn-outline-primary">
            <i class="mdi mdi-printer-outline me-1"></i> {{ trans('main_trans.print') }}
        </button>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-6">
            <div class="detail-card mb-3">
                <div class="detail-header">
                    <i class="mdi mdi-heart-pulse text-danger me-2"></i>
                    <strong>{{ trans('surgical_trans.operation_info') }}</strong>
                </div>
                <div class="detail-body">
                    <div class="detail-item">
                        <span>{{ trans('surgical_trans.operation_type') }}:</span>
                        <strong>{{ $booking->surgicalsetting->name_en ?? '-' }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>{{ trans('surgical_trans.anesthesia') }}:</span>
                        <strong>{{ $booking->surgicalsetting->anesthesia_type ?? '-' }}</strong>
                    </div>

                    @php
                        $statusColors = [
                            'booked' => 'primary',
                            'confirmed' => 'success',
                            'preparing_patient' => 'info',
                            'preparing_room' => 'info',
                            'ready_for_surgery' => 'info',
                            'anesthesia_started' => 'secondary',
                            'anesthesia_completed' => 'secondary',
                            'incision_started' => 'warning',
                            'in_progress' => 'warning',
                            'on_hold' => 'dark',
                            'completed' => 'success',
                            'closing_wound' => 'warning',
                            'wound_closed' => 'success',
                            'anesthesia_reversed' => 'secondary',
                            'recovery_room' => 'primary',
                            'under_observation' => 'primary',
                            'discharged' => 'success',
                            'cancelled' => 'danger',
                            'failed' => 'danger',
                            'postponed' => 'warning',
                            'transferred' => 'warning',
                            'room_canceled' => 'danger',
                            'arrived' => 'primary',
                            'admitted' => 'primary',
                            'in_surgery' => 'info',
                        ];
                    @endphp

                    <div class="detail-item">
                        <span>{{ trans('surgical_trans.booking_status') }}:</span>
                        <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }}">
                            {{ trans('surgical_trans.status_' . $booking->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <div class="detail-card mb-3">
                <div class="detail-header">
                    <i class="mdi mdi-calendar-clock text-primary me-2"></i>
                    <strong>{{ trans('surgical_trans.scheduling') }}</strong>
                </div>
                <div class="detail-body">
                    <div class="detail-item">
                        <span>{{ trans('surgical_trans.operation_date') }}:</span>
                        <strong>{{ $booking->booking_date }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>{{ trans('surgical_trans.operation_time_start') }}:</span>
                        <strong>{{ $booking->scheduled_from }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>{{ trans('surgical_trans.operation_time_end') }}:</span>
                        <strong>{{ $booking->scheduled_to }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

  

    <!-- Status History -->
    <div class="detail-card mt-4">
        <div class="detail-header">
            <i class="mdi mdi-history text-info me-2"></i>
            <strong>{{ trans('surgical_trans.status_history') }}</strong>
        </div>
        <div class="detail-body">
            <div class="timeline">
                @forelse($statusHistory as $history)
                    <div class="timeline-item">
                        <div class="timeline-icon bg-{{ $statusColors[$history->status] ?? 'secondary' }}">
                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">
                                {{ trans('surgical_trans.status_' . $history->status) }}
                                <small
                                    class="text-muted">({{ $history->status_changed_at->format('Y-m-d H:i') }})</small>
                            </h6>
                            <p class="mb-0">{{ $history->note }}</p>
                            <small class="text-muted">
                                {{ trans('surgical_trans.changed_by') }}: {{ $history->changedByUser->name ?? '-' }}
                            </small>
                        </div>
                    </div>
                @empty
                    <p>{{ trans('surgical_trans.no_status_history') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- CSS Styling -->
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .timeline-content {
            margin-left: 35px;
            background: #fff;
            padding: 12px;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
        }

        .detail-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .detail-header {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .detail-body {
            padding: 15px;
        }

        .detail-item {
            margin-bottom: 10px;
        }
    </style>
</div>
