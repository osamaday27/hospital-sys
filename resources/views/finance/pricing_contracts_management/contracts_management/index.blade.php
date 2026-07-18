@extends('layouts.master')

@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('settings_trans.settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ trans('settings_trans.settings') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('settings_trans.system_settings') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
       
            <!-- Content Row -->
            <div class="row justify-content-evenly mt-3">
                <!-- Service Management Section -->
                <div class="col-lg-12">
                    @include('partials.main', [
                        'title' => __('contract_settings.contract_management'),
                        'cards' => [
                            ['icon' => 'fjvfsqea.json', 'text' => __('contract_settings.management_contract_frist_level'), 'link' => 'management_contract_frist_level'],
                            ['icon' => 'fjvfsqea.json', 'text' => __('contract_settings.management_contract_second_level'), 'link' => 'management_contract_second_level'],
                            ['icon' => 'fjvfsqea.json', 'text' => __('contract_settings.management_contract_last_level'), 'link' => 'management_contract_last_level']
                        ]
                    ])
                </div>
              
            </div>
            <!-- End Content Row -->
        </div>
    </div>

@endsection
