@extends('layouts.master')

@section('title')
    {{ trans('radiology.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('radiology.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
        <!-- Content Row -->
        <div class="row justify-content-evenly mt-3">

            <!-- Service Management Section -->
            <div class="col-lg-12">

                @include('partials.icon', [
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-hospital-building',
                            'text' => __('radiology_trans.radiology_rooms_settings'),
                            'link' => 'radiology_rooms_settings',
                        ],
                        [
                            'icon' => 'mdi mdi-cogs',
                            'text' => __('radiology_trans.radiology_devices_types_settings'),
                            'link' => 'radiology/devicestypes/settings',
                        ],
                        [
                            'icon' => 'mdi mdi-monitor',
                            'text' => __('radiology_trans.devices_settings'),
                            'link' => 'radiology/devices/settings',
                        ],
                        [
                            'icon' => 'mdi mdi-stethoscope',
                            'text' => __('radiology_trans.medical_services_settings'),
                            'link' => 'radiology/medical_services/settings',
                        ],
                        [
                            'icon' => 'mdi mdi-earth',
                            'text' => __('radiology_trans.devices_rooms_country_settings'),
                            'link' => 'radiology/scheduling/settings',
                        ],
                        [
                            'icon' => 'mdi mdi-file-document',
                            'text' => __('radiology_trans.medical_reports_settings'),
                            'link' => 'radiology/medicalreports/settings',
                        ],
                        [
                            'icon' => 'mdi mdi-cash-multiple',
                            'text' => __('radiology_trans.financial_settings'),
                            'link' => 'radiology/financial/settings',
                        ],
                        [
                            'icon' => 'mdi mdi-comment-question-outline',
                            'text' => __('radiology_trans.questions_settings'),
                            'link' => 'radiology/questions/settings',
                        ],
                        [
                            'icon' => 'mdi mdi-package-variant-closed',
                            'text' => __('radiology_trans.packages_settings'),
                            'link' => 'radiology/settings/packages',
                        ],
                    ],
                ])

            </div>

        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->

    </div>
@endsection
