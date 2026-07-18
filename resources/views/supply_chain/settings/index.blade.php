@extends('layouts.master')

@section('title')
{{ trans('supply_chain_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('supply_chain_trans.title') }}</a>
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
                    'title' => __('supply_chain_trans.supply_chain_settings'),
                    'cards' => [
                        [
                            'icon' => 'sbrvirwc.json',
                            'text' => __('supply_chain_trans.product_categories'),
                            'link' => 'product_categories',
                        ],
                        [
                            'icon' => 'fwkrbvja.json',
                            'text' => __('supply_chain_trans.store_settings'),
                            'link' => 'store_settings',
                        ]
                        ,
                        [
                            'icon' => 'exymduqj.json',
                            'text' => __('supply_chain_trans.products_main_data'),
                            'link' => 'products_main_data',
                        ],
                        [
                            'icon' => 'tjjwskjx.json',
                            'text' => __('supply_chain_trans.store_main_data'),
                            'link' => 'store_main_data',
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
