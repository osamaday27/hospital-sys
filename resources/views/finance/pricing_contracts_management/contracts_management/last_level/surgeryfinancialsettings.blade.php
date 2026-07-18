@extends('layouts.masterliveware')
@section('title')
    {{ trans('contract_settings.edit_last_level_contract') }}
@stop
@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('contract_settings.edit_last_level_contract') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('management_contract_last_level.index') }}">
                                {{ trans('contract_settings.contracts') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('contract_settings.edit_last_level_contract') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title -->
    <div id="alert-container"></div>
    <!-- مكان الرسائل -->
    <div class="row">
        <div class="col-12">
            @livewire('finance.contract-operation-pricing', ['contract_id' => $contract_id, 'surgery_id' => $service_id])
        </div>
    </div>
    </div>
     <!-- مكان الرسائل -->
    <div class="row">
        <div class="col-lg-12">
                @livewire('surgical.surgery-services-items', ['surgery_id' => $service_id])

            </div>
    </div>
    </div>
@endsection
