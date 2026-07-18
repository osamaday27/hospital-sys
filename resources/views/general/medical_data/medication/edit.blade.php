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
                        <li class="breadcrumb-item active">{{ trans('settings_trans.medical_settings') }}</li>
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
                    <h5 class="card-title mb-0">{{ trans('settings_trans.medical_settings') }}</h5>
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
                    <form action="{{ route('drug_settings.update', 'test') }}" method="post">
                        {{ method_field('patch') }}
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <input id="id" name="id" value="{{ $medication->id }}" hidden>
                                    <label for="name_ar" class="form-label">{{ trans('settings_trans.name_ar') }}</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        placeholder="{{ trans('settings_trans.name_ar_enter') }}"
                                        value="{{ $medication->getTranslation('name', 'ar') }}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('settings_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        placeholder="{{ trans('settings_trans.name_en_enter') }}"
                                        value="{{ $medication->getTranslation('name', 'en') }}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.preparations') }}</label>
                                    <input type="text" class="form-control" id="preparations" name="preparations"
                                        placeholder="{{ trans('settings_trans.preparations_enter') }}"
                                        value="{{ $medication->preparations }}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.description') }}</label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        placeholder="{{ trans('settings_trans.description_enter') }}"
                                        value="{{ $medication->description }}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.effective_material') }}</label>
                                    <input type="text" class="form-control" id="effective_material"
                                        name="effective_material"
                                        placeholder="{{ trans('settings_trans.effective_material_enter') }}"
                                        value="{{ $medication->effective_material }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.default_dose_unit') }}</label>
                                    <select class="js-example-basic-single" name="default_dose_unit">
                                        @foreach ($units_settings as $units_settinges)
                                            <option value="{{ $units_settinges->id }}"
                                                @if ($units_settinges->id == $medication->default_dose_unit) selected @endif>
                                                {{ $units_settinges->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.default_dose') }}</label>
                                    <input type="text" class="form-control" id="default_dose" name="default_dose"
                                        placeholder="{{ trans('settings_trans.default_dose_enter') }}"
                                        value="{{ $medication->default_dose }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.method_taking_medications') }}</label>
                                    <input type="text" class="form-control" id="method_taking_medications"
                                        name="method_taking_medications"
                                        placeholder="{{ trans('settings_trans.method_taking_medications_enter') }}"
                                        value="{{ $medication->method_taking_medications }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.start_dose') }}</label>
                                    <input type="date" class="form-control" id="start_dose" name="start_dose"
                                        placeholder="{{ trans('settings_trans.start_dose') }}"
                                        value="{{ $medication->start_dose }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.end_dose') }}</label>
                                    <input type="date" class="form-control" id="end_dose" name="end_dose"
                                        placeholder="{{ trans('settings_trans.end_dose') }}"
                                        value="{{ $medication->end_dose }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.duration') }}</label>
                                    <input type="text" class="form-control" id="duration" name="duration"
                                        placeholder="{{ trans('settings_trans.duration') }}"
                                        value="{{ $medication->duration }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('settings_trans.repeat') }}</label>
                                    <input type="text" class="form-control" id="repeat" name="repeat"
                                        placeholder="{{ trans('settings_trans.repeat') }}"
                                        value="{{ $medication->repeat }}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit"
                                        class="btn btn-primary">{{ trans('settings_trans.update') }}</button>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </form>


                </div>
            </div>
        </div>
    </div>

@endsection
