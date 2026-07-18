@extends('layouts.master')

@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ trans('hr.settings') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.settings') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
       
            <div class="row justify-content-evenly mt-3">
                <div class="col-lg-12">
                    @include('partials.main', [
                        'title' => __('hr.settings'),
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
                        ['icon' => 'yanwuwms.json', 'text' => __('hr.jobs_settings'), 'link' => 'hr_jobs'],


                        ]
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection
