@extends('layouts.master')

@section('title')
{{ trans('suppliers.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('suppliers.title') }}</a>
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
                    'title' => __('suppliers.suppliers_settings'),
                    'cards' => [
                        [
                            'icon' => 'fwkrbvja.json',
                            'text' => __('suppliers.suppliers_system'),
                            'link' => 'suppliers_general_settings',
                        ],
                        [
                            'icon' => 'sbrvirwc.json',
                            'text' => __('suppliers.suppliers_data'),
                            'link' => 'suppliers_data',
                        ]
                        ,
                        [
                            'icon' => 'exymduqj.json',
                            'text' => __('suppliers.suppliers_services'),
                            'link' => 'suppliers_services',
                        ],
                        [
                            'icon' => 'tjjwskjx.json',
                            'text' => __('suppliers.suppliers_company'),
                            'link' => 'suppliers_company',
                        ],
                        [
                            'icon' => 'cfoaotmk.json',
                            'text' => __('suppliers.suppliers_groups'),
                            'link' => 'suppliers_groups',
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
