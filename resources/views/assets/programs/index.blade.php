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
                    'title' => __('assets.assets_programs'),
                    'cards' => [
                        [
                            'icon' => 'sbrvirwc.json',
                            'text' => __('assets.items'),
                            'link' => 'assets/programs/items',
                        ]
                        ,
                        [
                            'icon' => 'exymduqj.json',
                            'text' => __('assets.documents'),
                            'link' => 'assets/programs/documents',
                        ]
                        ,
                        [
                            'icon' => 'cfoaotmk.json',
                            'text' => __('assets.assignments'),
                            'link' => 'assets/programs/assignments',
                        ]
                        ,
                        [
                            'icon' => 'fwkrbvja.json',
                            'text' => __('assets.maintenance_title'),
                            'link' => 'assets/programs/maintenance',
                        ]
                        ,
                        [
                            'icon' => 'zruuduya.json',
                            'text' => __('assets.depreciation_title'),
                            'link' => 'assets/programs/depreciation',
                        ],
                        [
                            'icon' => 'wjyqkiew.json',
                            'text' => __('assets.audits_title'),
                            'link' => 'assets/programs/audits',
                        ],
                        [
                            'icon' => 'oqeixref.json',
                            'text' => __('assets.disposal_and_retired'),
                            'link' => 'assets/programs/disposal',
                        ],
                                                [
                            'icon' => 'olfvnikl.json',
                            'text' => __('assets.convert_to_assets_title'),
                            'link' => 'assets/programs/convert_to_assets',
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
