@extends('layouts.master')
@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h3 class="highlight-text">{{ $admsclinic->name_ar }}</h3>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a
                                        href="{{ route('outpatient_settings.index') }}">{{ trans('outpatient_trans.settings') }}</a>
                                </li>
                                <li class="breadcrumb-item active">{{ trans('outpatient_trans.schedule_settings') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <!-- عرض رسائل النجاح -->
                @if (session('success'))
                    <!-- Success Alert -->
                    <div class="alert alert-success alert-dismissible shadow fade show" role="alert">
                        <strong>{{ session('success') }}</strong></b>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- عرض رسائل الخطأ -->
                @if (session('error'))
                    <!-- Danger Alert -->
                    <div class="alert alert-danger alert-dismissible shadow fade show" role="alert">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('outpatient_trans.scheduling_settings') }}</h5>

                </div>

                <div class="card-body">

                    <form action="{{ route('schedule_settings.store') }}" method="post">
                        {{ csrf_field() }}
                        @csrf
                        <div class="row">
                            <input type="hidden" id="id_clinic" name="id_clinic" value="{{ $admsclinic->id }}">
                            <!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-companyel">{{ trans('outpatient_trans.doctor') }}</label>
                                    <select name="id_doctor" id="id_doctor" class="js-example-basic-single" required>
                                        <option value="">{{ trans('outpatient_trans.select_doctor') }}</option>
                                        @foreach ($doctor as $data)
                                            <option value="{{ $data->id }}">{{ $data->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-companyel">{{ trans('outpatient_trans.examination_time') }}</label>
                                    <input type="text" class="form-control" id="examination_time" name="examination_time"
                                        placeholder="مثال: 30 دقيقة">
                                </div>
                            </div><!--end col-->

                            <!-- Days and Shifts -->
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
                                <div class="row">
                                    <div class="col-2">
                                        <div class="mb-3" style="margin: 29px;">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="SwitchCheck{{ $dayKey }}" name="day[]"
                                                    value="{{ $dayKey }}"
                                                    onclick="checkbox_disabled('SwitchCheck{{ $dayKey }}', 'shift_{{ $dayKey }}')">
                                                <label class="form-check-label"
                                                    for="SwitchCheck{{ $dayKey }}">{{ $dayName }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <div class="row">
                                            @for ($shift = 1; $shift <= 3; $shift++)
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check form-switch">
                                                        <label
                                                            class="form-check-label">{{ trans(str_replace('%s', $shift, 'outpatient_trans.shift_%s')) }}</label>
                                                        <div class="row">
                                                            <div class="col">
                                                                <input type="time"
                                                                    class="shift_{{ $dayKey }} form-control"
                                                                    id="shift_{{ $dayKey }}"
                                                                    name="{{ $dayKey }}_shift{{ $shift }}_from"
                                                                    disabled>
                                                            </div>
                                                            <div class="col">
                                                                <input type="time"
                                                                    class="shift_{{ $dayKey }} form-control"
                                                                    id="shift_{{ $dayKey }}"
                                                                    name="{{ $dayKey }}_shift{{ $shift }}_to"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>

                                    </div>
                                </div>
                            @endforeach

                            <div class="col-lg-12">
                                <div class="text-start">
                                    <button type="submit" class="btn btn-primary">{{ trans('buttons.save') }}</button>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function checkbox_disabled(checkbox1, inputClass) {
            var checkbox = document.getElementById(checkbox1);
            var inputs = document.getElementsByClassName(inputClass);
            for (var i = 0; i < inputs.length; i++) {
                if (checkbox.checked) {
                    inputs[i].removeAttribute("disabled");
                } else {
                    inputs[i].setAttribute("disabled", "disabled");
                }
            }
        }
        $('#id_doctor').change(function() {
            var doctorId = $(this).val();
            var clinicId = $('#id_clinic').val(); // إضافة قيمة العيادة
            if (doctorId && clinicId) {
                $.ajax({
                    url: '{{ route('get.doctor.schedule') }}',
                    type: 'GET',
                    data: {
                        doctor_id: doctorId,
                        clinic_id: clinicId
                    },
                    success: function(response) {
                        if (response.schedule) {
                            updateScheduleForm(response.schedule);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });

        function updateScheduleForm(schedule) {
            Object.keys(schedule).forEach(function(day) {
                var daySchedule = schedule[day];
                if (daySchedule) {
                    $('#SwitchCheck' + day).prop('checked', true);
                    checkbox_disabled('SwitchCheck' + day, 'shift_' + day);
                    for (var shift = 1; shift <= 3; shift++) {
                        if (daySchedule['shift' + shift]) {
                            var shiftData = daySchedule['shift' + shift];
                            $('[name="' + day + '_shift' + shift + '_from"]').val(shiftData.from);
                            $('[name="' + day + '_shift' + shift + '_to"]').val(shiftData.to);
                        }
                    }
                }
            });
        }
    </script>
@endsection
