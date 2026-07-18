@extends('layouts.master')

@section('title')
{{ trans('supply_chain_trans.return_items') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('supply_chain_trans.return_items') }}</a>
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
                    'title' => __('supply_chain_trans.supply_chain_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-account-arrow-left',
                            'text' => __('supply_chain_trans.return_to_supplier'),
                            'link' => 'return_to_supplier',
                        ],
                        // [
                        //     'icon' => 'mdi mdi-table-arrow-left',
                        //     'text' => __('supply_chain_trans.return_to_store'),
                        //     'link' => 'return_to_store',
                        // ],
                        [
                            'icon' => 'mdi mdi-database-import',
                            'text' => __('supply_chain_trans.return_direct_to_store'),
                            'link' => 'return_direct_to_store',
                        ],


                        [
                            'icon' => 'mdi mdi-transfer-right',
                            'text' => __('supply_chain_trans.return_from_section'),
                            'link' => 'return_from_section',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-arrow-right-outline',
                            'text' => __('supply_chain_trans.return_from_employee'),
                            'link' => 'return_from_employee',
                        ],
                        [
                            'icon' => 'mdi mdi-cart-arrow-up',
                            'text' => __('supply_chain_trans.return_from_client'),
                            'link' => 'return_from_client',
                        ]

                    ],
                ])
                </div>

            </div>
            <!-- End Content Row -->
        </div>
        <!-- End col -->
@endsection
