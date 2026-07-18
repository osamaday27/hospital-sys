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

    @include('medical.examinations.components.program.ophthalmology.medical_file.header_patient_file')
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex profile-wrapper">
                @include('medical.examinations.components.program.ophthalmology.medical_file.tab_option_examination')
            </div>
            <div class="tab-content pt-4 text-muted">
                @include('medical.examinations.components.program.ophthalmology.medical_file.glasses')
                @include('medical.examinations.components.program.medical_file.notes')
                @include('medical.examinations.components.program.ophthalmology.medical_file.attachments')
            </div>
        </div>
    </div>

@endsection
