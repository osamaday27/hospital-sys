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
                        <li class="breadcrumb-item active">{{ trans('settings_trans.pricing') }}</li>
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
                    <h5 class="card-title mb-0">{{ trans('settings_trans.pricing_settings') }}</h5>
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
                    <form action="{{ route('contract_settings.update', 'test') }}" method="post">
                        {{ method_field('patch') }}
                        @csrf
                        <div class="row">
                            <!--end col-->
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-contractel">{{ trans('settings_trans.name_ar') }}</label>
                                    <input id="id" name="id" value="{{ $contract->id }}" hidden>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        placeholder="{{ trans('settings_trans.name_ar_enter') }}"
                                        value="{{ $contract->getTranslation('name', 'ar') }}">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-contractel">{{ trans('settings_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        placeholder="{{ trans('settings_trans.name_en_enter') }}"
                                        value="{{ $contract->getTranslation('name', 'en') }}">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default" class="form-label">{{ trans('settings_trans.company') }}</label>
                                    <select name="id_company" id="id_company" class="form-control" required>
                                        @foreach ($company as $companyes)
                                            <option @if ($contract->id_company == $companyes->id) selected @endif
                                                value="{{ $companyes->id }}"> {{ $companyes->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default"
                                        class="form-label">{{ trans('settings_trans.price_list') }}</label>
                                    <select name="id_pricing" id="id_pricing" class="form-control" required>
                                        @foreach ($pricing as $pricinges)
                                            <option @if ($contract->id_pricing == $pricinges->id) selected @endif
                                                value="{{ $pricinges->id }}"> {{ $pricinges->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default"
                                        class="form-label">{{ trans('settings_trans.discount_type') }}</label>
                                    <select name="discount_type" id="discount_type" class="form-control" required>
                                        <option @if ($contract->discount_type == 'percent') selected @endif value="percent">
                                            {{ trans('settings_trans.percent') }}</option>
                                        <option @if ($contract->discount_type == 'value') selected @endif value="value">
                                            {{ trans('settings_trans.value') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('settings_trans.discount') }}</label>
                                    <input type="text" class="form-control" id="discount" name="discount"
                                        placeholder="{{ trans('settings_trans.discount_enter') }}"
                                        value="{{ $contract->discount }}">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default"
                                        class="form-label">{{ trans('settings_trans.patient_contribution_type') }}</label>
                                    <select name="patient_contribution_type" id="patient_contribution_type"
                                        class="form-control" required>
                                        <option @if ($contract->discount_type == 'percent') selected @endif value="percent">
                                            {{ trans('settings_trans.percent') }}</option>
                                        <option @if ($contract->discount_type == 'value') selected @endif value="value">
                                            {{ trans('settings_trans.value') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.patient_contribution') }}</label>
                                    <input type="text" class="form-control" id="patient_contribution"
                                        name="patient_contribution"
                                        placeholder="{{ trans('settings_trans.patient_contribution_enter') }}"
                                        value="{{ $contract->patient_contribution }}">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default" class="form-label">{{ trans('settings_trans.status') }}</label>
                                    <select name="status" id="10" class="form-control" required>
                                        <option @if ($contract->status == '1') selected @endif value="1">
                                            {{ trans('settings_trans.default_true') }}</option>
                                        <option @if ($contract->status == '0') selected @endif value="0">
                                            {{ trans('settings_trans.default_false') }}</option>
                                    </select>
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
