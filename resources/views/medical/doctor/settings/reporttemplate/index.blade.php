@extends('layouts.master_doctor')

@section('title')
    {{ trans('doctor_trans.title') }}
@stop
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('doctor_trans.title') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('doctor_settings.index') }}">{{ trans('doctor_trans.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('doctor_trans.setting_repport') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <!-- Table Section -->
                <div class="card-body">
                    @livewire('medical-report-template-form')
                </div>
            </div>


        </div>
    </div>

@endsection
