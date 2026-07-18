@extends('layouts.master')

@section('title')
    {{ trans('outpatient_trans.settings') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('outpatient_trans.settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('management_contract_frist_level.index') }}">{{ trans('outpatient_trans.settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('outpatient_trans.edit_clinic') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title -->

    <!-- Start Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- عرض رسائل النجاح أو الخطأ -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('clinic_settings.update', 'test') }}" method="post">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="row">
                            <input type="hidden" name="id" value="{{ $admsclinic->id }}" />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_examination_service" class="form-label">
                                            {{ trans('outpatient_trans.id_examination_service') }}
                                        </label>
                                        <select name="id_examination_service" id="id_examination_service"
                                           class="js-example-basic-single">
                                            <option value="">{{ trans('outpatient_trans.select_service') }}</option>
                                            @foreach ($services as $data)
                                                <option value="{{ $data->id }}"
                                                    {{ $admsclinic->id_examination_service == $data->id ? 'selected' : '' }}>
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_examination_service')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_follow_up_service" class="form-label">
                                            {{ trans('outpatient_trans.id_follow_up_service') }}
                                        </label>
                                        <select name="id_follow_up_service" id="id_follow_up_service"class="js-example-basic-single">
                                            <option value="">{{ trans('outpatient_trans.select_service') }}</option>
                                            @foreach ($services as $data)
                                                <option value="{{ $data->id }}"
                                                    {{ $admsclinic->id_follow_up_service == $data->id ? 'selected' : '' }}>
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_follow_up_service')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_follow_up_opr_service" class="form-label">
                                            {{ trans('outpatient_trans.id_follow_up_opr_service') }}
                                        </label>
                                        <select name="id_follow_up_opr_service" id="id_follow_up_opr_service"
                                           class="js-example-basic-single">
                                            <option value="">{{ trans('outpatient_trans.select_service') }}</option>
                                            @foreach ($services as $data)
                                                <option value="{{ $data->id }}"
                                                    {{ $admsclinic->id_follow_up_opr_service == $data->id ? 'selected' : '' }}>
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_follow_up_opr_service')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_spe_examination_service" class="form-label">
                                            {{ trans('outpatient_trans.id_spe_examination_service') }}
                                        </label>
                                        <select name="id_spe_examination_service" id="id_spe_examination_service"
                                           class="js-example-basic-single">
                                            <option value="">{{ trans('outpatient_trans.select_service') }}</option>
                                            @foreach ($services as $data)
                                                <option value="{{ $data->id }}"
                                                    {{ $admsclinic->id_spe_examination_service == $data->id ? 'selected' : '' }}>
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_spe_examination_service')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_spe_follow_up_service" class="form-label">
                                            {{ trans('outpatient_trans.id_spe_follow_up_service') }}
                                        </label>
                                        <select name="id_spe_follow_up_service" id="id_spe_follow_up_service"
                                           class="js-example-basic-single">
                                            <option value="">{{ trans('outpatient_trans.select_service') }}</option>
                                            @foreach ($services as $data)
                                                <option value="{{ $data->id }}"
                                                    {{ $admsclinic->id_spe_follow_up_service == $data->id ? 'selected' : '' }}>
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_spe_follow_up_service')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_spe_follow_up_opr_service" class="form-label">
                                            {{ trans('outpatient_trans.id_spe_follow_up_opr_service') }}
                                        </label>
                                        <select name="id_spe_follow_up_opr_service" id="id_spe_follow_up_opr_service"
                                           class="js-example-basic-single">
                                            <option value="">{{ trans('outpatient_trans.select_service') }}</option>
                                            @foreach ($services as $data)
                                                <option value="{{ $data->id }}"
                                                    {{ $admsclinic->id_spe_follow_up_opr_service == $data->id ? 'selected' : '' }}>
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_spe_follow_up_opr_service')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="d-flex justify-content-between">
                                    <button type="submit"
                                        class="btn btn-success">{{ trans('outpatient_trans.save') }}</button>
                                    <a href="{{ route('outpatient_settings.index') }}"
                                        class="btn btn-secondary">{{ trans('outpatient_trans.cancel') }}</a>
                                </div>

                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
