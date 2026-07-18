@extends('layouts.master')

@section('title')
    {{ trans('inpatient_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('inpatient_trans.title') }}</a>
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
                @include('partials.submain', [
                    'title' => __('inpatient_trans.inpatient_settings'),
                    'cards' => [
                        [
                            'icon' => 'kddybgok.json',
                            'text' => __('inpatient_trans.Nursing station settings'),
                            'link' => 'nursing_station_settings',
                        ],
                        [
                            'icon' => 'cnpvyndp.json',
                            'text' => __('inpatient_trans.room_settings'),
                            'link' => 'room_settings',
                        ],
                        [
                            'icon' => 'eouimtlu.json',
                            'text' => __('inpatient_trans.bed_settings'),
                            'link' => 'bed_settings',
                        ],
                        [
                            'icon' => 'swcqkzdc.json',
                            'text' => __('inpatient_trans.Reasons_entry_settings'),
                            'link' => 'reasons_entry_settings',
                        ],
                         [
                            'icon' => 'swcqkzdc.json',
                            'text' => __('inpatient_trans.Reasons_exit_settings'),
                            'link' => 'reasons_exit_settings',
                        ],
                         [
                            'icon' => 'nizfqlnk.json',
                            'text' => __('inpatient_trans.accommodation_grade_settings'),
                            'link' => 'residencies_settings',
                        ],
                          [
                            'icon' => 'jkgunhbs.json',
                            'text' => __('inpatient_trans.administration_responsible_patient_discharge'),
                            'link' => 'adm_res_pa_discharge',
                        ],
                        [
                            'icon' => 'ercyvufy.json',
                            'text' => __('inpatient_trans.access_source_settings'),
                            'link' => 'entry_sources_settings',
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
