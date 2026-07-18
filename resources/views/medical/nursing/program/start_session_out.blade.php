@extends('layouts.master_doctor')

@section('title')
    {{ trans('nursing_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('nursing_trans.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @include('medical.nursing.components.program.medical_file.header_patient_file')
    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex profile-wrapper">
                    @include('medical.nursing.components.program.medical_file.tab_option')
                </div>
                <div class="tab-content pt-4 text-muted">
                    @include('medical.nursing.components.program.medical_file.overview')
                    @include('medical.nursing.components.program.medical_file.vitals')
                    @include('medical.nursing.components.program.medical_file.service')
                    @include('medical.nursing.components.program.medical_file.assessmentform')
                    @include('medical.nursing.components.program.medical_file.reassessmentform')
                    @include('medical.nursing.components.program.medical_file.giving_medications')
                    @include('medical.nursing.components.program.medical_sheet.view')
                    @include('medical.nursing.components.program.medical_file.notes')
                </div>
            </div>
        </div>
    </div>

@endsection
