@extends('layouts.masterliveware')

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


    <!-- Content Section -->
    <div class="row justify-content-evenly mt-3">
        <div class="col-lg-12">
            @livewire('outpatient.settings.clinic-service-link-manager')
        </div>
    </div>
@endsection

