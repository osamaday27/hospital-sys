@extends('layouts.master')

@section('title')
{{ trans('supply_chain_trans.reports_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('supply_chain_trans.reports_title') }}</a>
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
                    'title' => __('supply_chain_trans.reports_title'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-swap-horizontal-bold',
                            'text' => __('supply_chain_trans.store_movements_report'),
                            'link' => 'store_movements_report',
                        ],
                        [
                            'icon' => 'mdi mdi-warehouse',
                            'text' => __('supply_chain_trans.warehouse_balance_report_title'),
                            'link' => 'warehouse_balance_report',
                        ],
                        [
                            'icon' => 'mdi mdi-account-hard-hat',
                            'text' => __('supply_chain_trans.warehouse_keeper_report_title'),
                            'link' => 'warehouse_keeper_report',
                        ],
                        [
                            'icon' => 'mdi mdi-chart-line',
                            'text' => __('supply_chain_trans.item_consumption_rate_report_title'),
                            'link' => 'item_consumption_rate_report',
                        ],
                        [
                            'icon' => 'mdi mdi-card-text-outline',
                            'text' => __('supply_chain_trans.item_card_report_title'),
                            'link' => 'item_card_report',
                        ],
                        [
                            'icon' => 'mdi mdi-shape-outline',
                            'text' => __('supply_chain_trans.item_groups_report_title'),
                            'link' => 'item_groups_report',
                        ],
                        [
                            'icon' => 'mdi mdi-database',
                            'text' => __('supply_chain_trans.item_data_report_title'),
                            'link' => 'item_data_report',
                        ],
                        [
                            'icon' => 'mdi mdi-calendar-alert',
                            'text' => __('supply_chain_trans.expired_item_report_title'),
                            'link' => 'expired_item_report',
                        ],
                        [
                            'icon' => 'mdi mdi-swap-horizontal',
                            'text' => __('supply_chain_trans.transfer_item_report_title'),
                            'link' => 'transfer_item_report',
                        ],
                        [
                            'icon' => 'mdi mdi-timer-sand',
                            'text' => __('supply_chain_trans.near_expiry_item_report_title'),
                            'link' => 'near_expiry_item_report',
                        ],
                        [
                            'icon' => 'mdi mdi-file-document-multiple',
                            'text' => __('supply_chain_trans.damaged_items_report_title'),
                            'link' => 'damaged_items_report',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-list-outline',
                            'text' => __('supply_chain_trans.current_stock_report_title'),
                            'link' => 'current_stock_report',
                        ],
                        [
                            'icon' => 'mdi mdi-tray-arrow-down',
                            'text' => __('supply_chain_trans.item_additions_report_title'),
                            'link' => 'item_additions_report',
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
