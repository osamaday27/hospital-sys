@extends('layouts.master')
@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h3 class="highlight-text">{{ $adm_s_rooms_surgical->name_ar }}</h3>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a
                                        href="{{ route('surgical_settings.index') }}">{{ trans('surgical_trans.settings') }}</a>
                                </li>
                                <li class="breadcrumb-item active">{{ trans('surgical_trans.schedule_settings') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <!-- عرض رسائل النجاح والخطأ -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible shadow fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible shadow fade show" role="alert">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('outpatient_trans.scheduling_settings') }}</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('scheduling_operation.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="id_room" value="{{ $adm_s_rooms_surgical->id }}">

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="duration" class="form-label">{{ trans('surgical_trans.duration') }}</label>
                                <input type="text" class="form-control" id="duration" name="duration" value="{{$operation_schedules->duration??''}}"
                                    placeholder="مثال: 30 دقيقة" required>
                            </div>
                        </div>

                        <!-- أيام الأسبوع -->
                        @php
                            $days = [
                                'saturday' => trans('outpatient_trans.saturday'),
                                'sunday' => trans('outpatient_trans.sunday'),
                                'monday' => trans('outpatient_trans.monday'),
                                'tuesday' => trans('outpatient_trans.tuesday'),
                                'wednesday' => trans('outpatient_trans.wednesday'),
                                'thursday' => trans('outpatient_trans.thursday'),
                                'friday' => trans('outpatient_trans.friday'),
                            ];
                        @endphp

                        @foreach ($days as $dayKey => $dayName)
                            @php
                                $daySchedule = $schedule[$dayKey] ?? null;
                            @endphp
                            <div class="row mb-3">
                                <div class="col-2 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="SwitchCheck{{ $dayKey }}"
                                            name="day[]" value="{{ $dayKey }}"
                                            onclick="toggleInputs('{{ $dayKey }}')"
                                            {{ $daySchedule ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="SwitchCheck{{ $dayKey }}">{{ $dayName }}</label>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>{{ trans('surgical_trans.start_time') }}</label>
                                            <input type="time" class="form-control shift_{{ $dayKey }}"
                                                name="{{ $dayKey }}_start_time"
                                                value="{{ $daySchedule ? $daySchedule->first()->start_time : '' }}"
                                                {{ !$daySchedule ? 'disabled' : '' }}>
                                        </div>
                                        <div class="col-md-6">
                                            <label>{{ trans('surgical_trans.end_time') }}</label>
                                            <input type="time" class="form-control shift_{{ $dayKey }}"
                                                name="{{ $dayKey }}_end_time"
                                                value="{{ $daySchedule ? $daySchedule->first()->end_time : '' }}"
                                                {{ !$daySchedule ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach


                        <div class="text-start">
                            <button type="submit" class="btn btn-primary">{{ trans('buttons.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleInputs(dayKey) {
            let checkbox = document.getElementById('SwitchCheck' + dayKey);
            let inputs = document.querySelectorAll('.shift_' + dayKey);

            inputs.forEach(input => {
                input.disabled = !checkbox.checked;
            });
        }

        function updateScheduleForm(schedule) {
            Object.keys(schedule).forEach(day => {
                let checkbox = document.getElementById('SwitchCheck' + day);
                let inputs = document.querySelectorAll('.shift_' + day);

                if (schedule[day]) {
                    checkbox.checked = true;
                    toggleInputs(day);

                    let startTime = document.querySelector(`[name="${day}_start_time"]`);
                    let endTime = document.querySelector(`[name="${day}_end_time"]`);

                    if (schedule[day]['start_time']) {
                        startTime.value = schedule[day]['start_time'];
                    }

                    if (schedule[day]['end_time']) {
                        endTime.value = schedule[day]['end_time'];
                    }
                }
            });
        }
    </script>
@endsection
