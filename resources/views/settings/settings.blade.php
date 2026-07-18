@extends('layouts.master')

@section('text')
    {{ trans('main_trans.text') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-text-box d-sm-flex align-items-center justify-content-between">
                <div class="page-text-right">
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
                            'text' => trans('settings_trans.nationalities_settings'),
                            'link' => url('/nationalities_settings'),
                        ],
                        [
                            'icon' => 'oaflahpk.json',
                            'text' => trans('settings_trans.Place_settings'),
                            'link' => url('/places_settings'),
                        ],
                        [
                            'icon' => 'hpnrikyx.json',
                            'text' => trans('settings_trans.currency_settings'),
                            'link' => url('/currencies'),
                        ],
                        [
                            'icon' => 'nnbhwnej.json',
                            'text' => trans('settings_trans.general_settings'),
                            'link' => 'javascript:void(0);',
                        ],
                        [
                            'icon' => 'nwfpiryp.json',
                            'text' => trans('settings_trans.namedictionaryl_settings'),
                            'link' => url('/namedictionary'),
                        ],
                        [
                            'icon' => 'nwfpiryp.json',
                            'text' => trans('settings_trans.patientnamereview'),
                            'link' => url('/patientnamereview'),
                        ],
                        [
                            'icon' => 'dyfvgeqj.json',
                          
                            'text' => trans('settings_trans.outpatient_settings'),
                            'link' => url('/outpatient_settings'),
                        ],
                        [
                            'icon' => 'hyvuvsxh.json',
                            'text' => trans('settings_trans.building_settings'),
                            'link' => url('/building_settings'),
                        ],
                         [
                            'icon' => 'zhiiqoue.json',
                            'text' => trans('settings_trans.jop'),
                            'link' => url('/jop_settings'),
                        ],
                         [
                            'icon' => 'jdgfsfzr.json',
                            'text' => trans('settings_trans.how_to_identify_place'),
                            'link' => url('/how_to_identify_place_settings'),
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
