@extends('layouts.master')

@section('title')
    {{ trans('inpatient_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('inpatient_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('inpatient_booking.index') }}">{{ trans('inpatient_trans.inpatient_booking') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('inpatient_trans.map') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">&nbsp;</span>
                                <span>{{ trans('inpatient_trans.available') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">&nbsp;</span>
                                <span>{{ trans('inpatient_trans.occupied') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">&nbsp;</span>
                                <span>{{ trans('inpatient_trans.maintenance') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs mb-3" id="buildingTabs" role="tablist">
                @foreach ($buildings as $index => $building)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="building-{{ $building->id }}-tab"
                            data-bs-toggle="tab" data-bs-target="#building-{{ $building->id }}" type="button"
                            role="tab" aria-controls="building-{{ $building->id }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            <i class='bx bx-building-house me-2'></i>{{ $building->{'name_' . app()->getLocale()} }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="buildingTabsContent">
                @foreach ($buildings as $index => $building)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="building-{{ $building->id }}"
                        role="tabpanel" aria-labelledby="building-{{ $building->id }}-tab">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"
                                style="background-color: #133E87  !important;color: #fff !important;">
                                <h5 class="mb-0 text-white">
                                    <i
                                        class='bx bx-building-house me-2'></i>{{ $building->{'name_' . app()->getLocale()} }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">{{ $building->address }}</p>
                                <!-- عرض الطوابق الخاصة بالمبنى -->
                                @foreach ($building->floors as $floor)
                                    <div class="floor card mb-2 shadow-sm">
                                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center"   style="background-color: #1373c3 !important;color: #fff !important;"
                                            data-bs-toggle="collapse" href="#floor-{{ $floor->id }}" role="button"
                                            aria-expanded="true" aria-controls="floor-{{ $floor->id }}">
                                            <h6 class="mb-0 text-white">
                                                <i class='bx bx-layer me-2'></i>{{ trans('inpatient_trans.floor') }}
                                                {{ $floor->{'name_' . app()->getLocale()} }}
                                            </h6>
                                            <span class="badge bg-light text-dark">{{ $floor->nursingStations->count() }}
                                                {{ trans('inpatient_trans.stations') }}</span>
                                        </div>
                                        <div class="collapse show" id="floor-{{ $floor->id }}">
                                            <div class="card-body bg-light">
                                                <!-- عرض محطات التمريض داخل كل طابق -->
                                                <div class="row">
                                                    @foreach ($floor->nursingStations as $nursingStation)
                                                        <div class="col-md-12 mb-3">
                                                            <div class="nursing-station card shadow-sm">
                                                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center" style="background-color: #608BC1 !important;color: #fff !important;"
                                                                    data-bs-toggle="collapse"
                                                                    href="#nursingStation-{{ $nursingStation->id }}"
                                                                    role="button" aria-expanded="true"
                                                                    aria-controls="nursingStation-{{ $nursingStation->id }}">
                                                                    <h6 class="mb-0 text-white">
                                                                        <i
                                                                            class='bx bx-clinic me-2'></i>{{ trans('inpatient_trans.nursing_station') }}:
                                                                        {{ $nursingStation->{'name_' . app()->getLocale()} }}
                                                                    </h6>
                                                                    <span class="badge bg-light text-dark">
                                                                        {{ $nursingStation->rooms->where('status', 'available')->count() }}
                                                                        {{ trans('inpatient_trans.available_rooms') }}
                                                                    </span>
                                                                </div>
                                                                <div class="collapse show"
                                                                    id="nursingStation-{{ $nursingStation->id }}">
                                                                    <div class="card-body bg-light">
                                                                        <!-- عرض نسبة اكتمال الأسرة في المحطة -->
                                                                        @php
                                                                            $totalBeds = $nursingStation->rooms->sum(
                                                                                function ($room) {
                                                                                    return $room->beds->count();
                                                                                },
                                                                            );
                                                                            $occupiedBeds = $nursingStation->rooms->sum(
                                                                                function ($room) {
                                                                                    return $room->beds
                                                                                        ->where(
                                                                                            'status',
                                                                                            '!=',
                                                                                            'available',
                                                                                        )
                                                                                        ->count();
                                                                                },
                                                                            );
                                                                            $emptyBeds = $totalBeds - $occupiedBeds;
                                                                            $completionPercentage =
                                                                                $totalBeds > 0
                                                                                    ? round(
                                                                                        ($occupiedBeds / $totalBeds) *
                                                                                            100,
                                                                                    )
                                                                                    : 0;
                                                                        @endphp
                                                                        <div class="progress mb-3">
                                                                            <div class="progress-bar bg-{{ $completionPercentage >= 80 ? 'danger' : ($completionPercentage >= 50 ? 'warning' : 'success') }}"
                                                                                role="progressbar"
                                                                                style="width: {{ $completionPercentage }}%;"
                                                                                aria-valuenow="{{ $completionPercentage }}"
                                                                                aria-valuemin="0" aria-valuemax="100">
                                                                                {{ $completionPercentage }}%
                                                                                {{ trans('inpatient_trans.occupied') }}
                                                                            </div>
                                                                        </div>
                                                                        <!-- عرض الغرف داخل كل محطة تمريض -->
                                                                        <div class="row">
                                                                            @foreach ($nursingStation->rooms as $room)
                                                                                <div class="col-md-4 mb-3">
                                                                                    <div class="room card shadow-sm">
                                                                                        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center" style="background-color: #CBDCEB !important;color: #fff !important;"
                                                                                            data-bs-toggle="collapse"
                                                                                            href="#room-{{ $room->id }}"
                                                                                            role="button"
                                                                                            aria-expanded="true"
                                                                                            aria-controls="room-{{ $room->id }}">
                                                                                            <h6 class="mb-0">
                                                                                                <i
                                                                                                    class='bx bx-door-open me-2'></i>{{ trans('inpatient_trans.room') }}:
                                                                                                {{ $room->{'name_' . app()->getLocale()} }}
                                                                                            </h6>
                                                                                            <span
                                                                                                class="badge bg-light text-dark">
                                                                                                {{ $room->beds->where('status', '!=', 'available')->count() }}
                                                                                                {{ trans('inpatient_trans.occupied_beds') }}
                                                                                                /
                                                                                                {{ $room->beds->where('status', 'available')->count() }}
                                                                                                {{ trans('inpatient_trans.available_beds') }}
                                                                                            </span>
                                                                                        </div>
                                                                                        <div class="collapse show"
                                                                                            id="room-{{ $room->id }}">
                                                                                            <div class="card-body">
                                                                                                <!-- عرض الأسرّة داخل كل غرفة -->
                                                                                                <div class="row">
                                                                                                    @foreach ($room->beds as $bed)
                                                                                                        <div
                                                                                                            class="col-md-4 mb-2">
                                                                                                            <div class="bed d-flex justify-content-between align-items-center p-2 rounded shadow-sm"
                                                                                                                style="background-color: {{ $bed->status === 'available'
                                                                                                                    ? '#01ae60'
                                                                                                                    : ($bed->status === 'occupied'
                                                                                                                        ? '#ff4d4d' // لون جديد للحالة occupied
                                                                                                                        : ($bed->status === 'maintenance'
                                                                                                                            ? '#f0ad4e' // لون جديد للحالة maintenance
                                                                                                                            : ($bed->status === 'reserved'
                                                                                                                                ? '#5bc0de' // لون جديد للحالة reserved
                                                                                                                                : ($bed->status === 'out_of_service'
                                                                                                                                    ? '#bdc3c7' // لون جديد للحالة out_of_service
                                                                                                                                    : ($bed->status === 'sanitized'
                                                                                                                                        ? '#2ecc71' // لون جديد للحالة sanitized
                                                                                                                                        : ($bed->status === 'pending_cleaning'
                                                                                                                                            ? '#f39c12' // لون جديد للحالة pending_cleaning
                                                                                                                                            : ($bed->status === 'allocated'
                                                                                                                                                ? '#f39c12' // لون جديد للحالة allocated
                                                                                                                                                : ($bed->status === 'isolation'
                                                                                                                                                    ? '#e74c3c'
                                                                                                                                                    : '#ffffff')))))))) }}">
                                                                                                                <div>
                                                                                                                    <p class="mb-0"
                                                                                                                        style="color: #fff;">
                                                                                                                        <i
                                                                                                                            class='bx bx-bed me-2'></i>{{ trans('inpatient_trans.bed') }}:
                                                                                                                        {{ $bed->name }}
                                                                                                                    </p>
                                                                                                                    <span
                                                                                                                        class="badge {{ $bed->status === 'available'
                                                                                                                            ? 'bg-success'
                                                                                                                            : ($bed->status === 'occupied'
                                                                                                                                ? 'bg-danger'
                                                                                                                                : ($bed->status === 'maintenance'
                                                                                                                                    ? 'bg-warning'
                                                                                                                                    : ($bed->status === 'reserved'
                                                                                                                                        ? 'bg-info'
                                                                                                                                        : ($bed->status === 'out_of_service'
                                                                                                                                            ? 'bg-secondary'
                                                                                                                                            : ($bed->status === 'sanitized'
                                                                                                                                                ? 'bg-success'
                                                                                                                                                : ($bed->status === 'pending_cleaning'
                                                                                                                                                    ? 'bg-warning'
                                                                                                                                                    : ($bed->status === 'allocated'
                                                                                                                                                        ? 'bg-primary'
                                                                                                                                                        : ($bed->status === 'isolation'
                                                                                                                                                            ? 'bg-danger'
                                                                                                                                                            : 'bg-light')))))))) }}">
                                                                                                                        {{ trans('inpatient_trans.' . $bed->status) }}
                                                                                                                    </span>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    @endforeach

                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .map-container {
            margin-top: 20px;
        }

        .building,
        .floor,
        .nursing-station,
        .room {
            margin-bottom: 10px;
        }

        .card-header {
            cursor: pointer;
        }

        .badge {
            font-size: 0.9em;
        }
    </style>
@endsection
