@extends('layouts.master_doctor')

@section('title')
    {{ trans('doctor_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('doctor_trans.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @include('medical.doctor.components.program.ophthalmology.medical_file.header_patient_file')
    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex profile-wrapper">
                    @include('medical.doctor.components.program.dentistry.medical_file.tab_option')
                </div>
                <div class="tab-content pt-4 text-muted">
                    @include('medical.doctor.components.program.dentistry.medical_file.complaint')
                    @include('medical.doctor.components.program.dentistry.medical_file.medicalhistory')
                    @include('medical.doctor.components.program.dentistry.medical_file.dentalhistory')
                    @include('medical.doctor.components.program.dentistry.medical_file.extraoralintraoralexams')
                    @include('medical.doctor.components.program.dentistry.medical_file.treatmentplans')
                    @include('medical.doctor.components.program.dentistry.medical_file.cariesriskassessment')
                    @include('medical.doctor.components.program.medical_file.surgery_request')
                    @include('medical.doctor.components.program.medical_file.prescription')
                    @include('medical.doctor.components.program.medical_file.doctor_order')
                    @include('medical.doctor.components.program.medical_sheet.view')
                    @include('medical.doctor.components.program.medical_file.medical_reports')
                    @include('medical.doctor.components.program.medical_file.notes')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.vitals')
                </div>
            </div>
        </div>
    </div>

@endsection
