@extends('layouts.master')

@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('main_trans.title') }}</a>
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
                    'title' => __('settings_trans.settings'),
                    'cards' => [
                        [
                            'icon' => 'oqhlhtfq.json',
                            'text' => __('settings_trans.nationalities_settings'),
                            'link' => 'nationalities_settings',
                        ],
                        [
                            'icon' => 'oaflahpk.json',
                            'text' => __('settings_trans.Place_settings'),
                            'link' => 'places_settings',
                        ],
                        [
                            'icon' => 'hpnrikyx.json',
                            'text' => __('settings_trans.currency_settings'),
                            'link' => 'currencies',
                        ],
                        [
                            'icon' => 'iwaotjbp.json',
                            'text' => __('settings_trans.specialization_settings'),
                            'link' => 'specialization',
                        ],
                          [
                            'icon' => 'dxoycpzg.json',
                            'text' => __('settings_trans.medical_settings'),
                            'link' => 'medical_settings',
                        ],
                          [
                            'icon' => 'ckatldkn.json',
                            'text' => __('settings_trans.accounts_settings'),
                            'link' => 'accounts_settings',
                        ],
                         [
                            'icon' => 'etqbfrgp.json',
                            'text' => __('settings_trans.clinic_settings'),
                            'link' => 'clinic_settings',
                        ],
                         [
                            'icon' => 'mgmiqlge.json',
                            'text' => __('settings_trans.scheduling_settings'),
                            'link' => 'scheduling_settings',
                        ],
                         [
                            'icon' => 'zfmcashd.json',
                            'text' => __('settings_trans.employees_settings'),
                            'link' => 'employees_settings',
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
