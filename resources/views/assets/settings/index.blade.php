@extends('layouts.master')

@section('title')
{{ trans('assets.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('assets.title') }}</a>
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
                    'title' => __('assets.assets_settings'),
                    'cards' => [
                        [
                            'icon' => 'sbrvirwc.json',
                            'text' => __('assets.brands'),
                            'link' => 'assets/settings/brands',
                        ]
                        ,
                        [
                            'icon' => 'exymduqj.json',
                            'text' => __('assets.models'),
                            'link' => 'assets/settings/models',
                        ],
                        [
                            'icon' => 'fwkrbvja.json',
                            'text' => __('assets.categories'),
                            'link' => 'assets/settings/categories',
                        ],


                        [
                            'icon' => 'onmwuuox.json',
                            'text' => __('assets.locations'),
                            'link' => 'assets/settings/locations',
                        ],
                        [
                            'icon' => 'cfoaotmk.json',
                            'text' => __('assets.general'),
                            'link' => 'assets/settings/general',
                        ]
                    ],
                ])
                    </div>

                </div>
                <!-- End Content Row -->
            </div>
            <!--end col-->

            </div>


@endsection
