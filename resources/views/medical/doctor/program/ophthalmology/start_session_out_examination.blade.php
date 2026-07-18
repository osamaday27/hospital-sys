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
            <div class="d-flex profile-wrapper">
                @include('medical.doctor.components.program.ophthalmology.medical_file.tab_option_examination')
            </div>
            <div class="tab-content pt-4 text-muted">
                @include('medical.doctor.components.program.ophthalmology.medical_file.overview')
                @include('medical.doctor.components.program.ophthalmology.medical_file.complaint')
                @include('medical.doctor.components.program.ophthalmology.medical_file.medical_history')
                @include('medical.doctor.components.program.ophthalmology.medical_file.glasses')
                @include('medical.doctor.components.program.ophthalmology.medical_file.eye-examination')
                @include('medical.doctor.components.program.medical_file.general_examination')
                @include('medical.doctor.components.program.ophthalmology.medical_file.diagnoseseye')
                @include('medical.doctor.components.program.ophthalmology.medical_file.surgery_request')
                @include('medical.doctor.components.program.ophthalmology.medical_file.prescription')
                @include('medical.doctor.components.program.ophthalmology.medical_file.doctor_order')
                @include('medical.doctor.components.program.medical_sheet.view')
                @include('medical.doctor.components.program.medical_file.medical_reports')
                @include('medical.doctor.components.program.medical_file.notes')
                @include('medical.doctor.components.program.ophthalmology.medical_file.instruction')
                @include('medical.doctor.components.program.ophthalmology.medical_file.attachments')
                @include('medical.doctor.components.program.ophthalmology.medical_file.vitals')
                @include('medical.doctor.components.program.ophthalmology.medical_file.follow_up')

            </div>
        </div>
    </div>

@endsection
