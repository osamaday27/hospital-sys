@extends('layouts.master')
@section('title')
    {{ trans('main_trans.title') }}



@stop


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ trans('settings_trans.settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('settings_trans.settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('settings_trans.scheduling') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('settings_trans.scheduling_settings') }}</h5>
                    {{-- Message --}}
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                                data-dismiss="alert">
                                <i class="fa fa-times"></i>
                            </button>
                            <strong>Success !</strong> {{ session('success') }}
                        </div>
                    @endif

                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                                data-dismiss="alert">
                                <i class="fa fa-times"></i>
                            </button>
                            <strong>Error !</strong> {{ session('error') }}
                        </div>
                    @endif

                </div>

                <div class="card-body">

                    <form action="{{ route('scheduling_settings.store') }}" method="post">
                        {{ csrf_field() }}
                        @csrf
                        <div class="row">
                            <!--end col-->
                            <div class="col-4">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-companyel">{{ trans('settings_trans.doctor') }}</label>
                                    <select name="id_doctor" id="id_doctor" class="form-control" required>
                                        @foreach ($doctor as $data)
                                            <option value="{{ $data->id }}"> {{ $data->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-companyel">{{ trans('settings_trans.Clinic_name') }}</label>
                                    <select name="id_clinic" id="id_clinic" class="form-control" required>
                                        @foreach ($clinic as $data)
                                            <option value="{{ $data->id }}"> {{ $data->clinic_name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label
                                        for="name_en"class="form-companyel">{{ trans('settings_trans.examination_time') }}</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div><!--end col-->
                            <!--end col-->
                            <div class="row">
                                <div class="col-2">
                                    <div class="mb-3" style="margin: 29px;">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1"
                                                onclick="checkbox_disabled('SwitchCheck1','shift_saturday')" name="day[]"
                                                value="saturday">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.saturday') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <label
                                                class="form-check-label">{{ trans('settings_trans.first_shift') }}</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="time" class="shift_saturday form-control"
                                                        id="shift_saturday" name="first_shift_from_saturday" disabled>
                                                </div>
                                                <div class="col">
                                                    <input type="time" class="shift_saturday form-control"
                                                        id="shift_saturday" name="first_shift_to_saturday" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="mb-3" style="margin: 29px;">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"id="SwitchCheck2"
                                                onclick="checkbox_disabled('SwitchCheck2','shift_sunday')" name="day[]"
                                                value="sunday">
                                            <label class="form-check-label"
                                                for="SwitchCheck2">{{ trans('settings_trans.sunday') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.first_shift') }}</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="time" class="shift_sunday form-control"
                                                        id="shift_sunday" name="first_shift_from_sunday" disabled>
                                                </div>
                                                <div class="col">
                                                    <input type="time" class="shift_sunday form-control"
                                                        id="shift_sunday" name="first_shift_to_sunday" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="mb-3" style="margin: 29px;">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="SwitchCheck3" name="day[]" value="monday"
                                                onclick="checkbox_disabled('SwitchCheck3','shift_monday')">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.monday') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.first_shift') }}</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="time" class="shift_monday form-control"
                                                        id="shift_monday" name="first_shift_from_monday" disabled>
                                                </div>
                                                <div class="col">
                                                    <input type="time" class="shift_monday form-control"
                                                        id="shift_monday" name="first_shift_to_monday" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="mb-3" style="margin: 29px;">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="SwitchCheck4" name="day[]" value="tuesday"
                                                onclick="checkbox_disabled('SwitchCheck4','shift_tuesday')">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.tuesday') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.first_shift') }}</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="time" class="shift_tuesday form-control"
                                                        id="shift_tuesday" name="first_shift_from_tuesday" disabled>
                                                </div>
                                                <div class="col">
                                                    <input type="time" class="shift_tuesday form-control"
                                                        id="shift_tuesday" name="first_shift_to_tuesday" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="mb-3" style="margin: 29px;">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="SwitchCheck5" name="day[]" value="wednesday"
                                                onclick="checkbox_disabled('SwitchCheck5','shift_wednesday')">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.wednesday') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.first_shift') }}</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="time" class="shift_wednesday form-control"
                                                        id="shift_wednesday" name="first_shift_from_wednesday" disabled>
                                                </div>
                                                <div class="col">
                                                    <input type="time" class="shift_wednesday form-control"
                                                        id="shift_wednesday" name="first_shift_to_wednesday" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="mb-3" style="margin: 29px;">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="SwitchCheck6" name="day[]" value="thursday"
                                                onclick="checkbox_disabled('SwitchCheck6','shift_thursday')">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.thursday') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.first_shift') }}</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="time" class="shift_thursday form-control"
                                                        id="shift_thursday" name="first_shift_from_thursday" disabled>
                                                </div>
                                                <div class="col">
                                                    <input type="time" class="shift_thursday form-control"
                                                        id="shift_thursday" name="first_shift_to_thursday" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="mb-3" style="margin: 29px;">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="SwitchCheck7" name="day[]" value="friday"
                                                onclick="checkbox_disabled('SwitchCheck7','shift_friday')">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.friday') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label"
                                                for="SwitchCheck1">{{ trans('settings_trans.first_shift') }}</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="time" class="shift_friday form-control"
                                                        id="shift_friday" name="first_shift_from_friday" disabled>
                                                </div>
                                                <div class="col">
                                                    <input type="time" class="shift_friday form-control"
                                                        id="shift_friday" name="first_shift_to_friday" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit"
                                        class="btn btn-primary">{{ trans('settings_trans.add') }}</button>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </form>

                </div>

            </div>
        </div>
    </div>
    <script>
        function checkbox_disabled(checkbox1, input1) {
            var checkbox = document.getElementById(checkbox1);
            var inputs = document.getElementsByClassName(input1);
            for (var i = 0; i < inputs.length; i++) {
                if (checkbox.checked) {
                    inputs[i].removeAttribute("disabled");
                } else {
                    inputs[i].setAttribute("disabled", "disabled");
                }
            }

        }
    </script>
@endsection
