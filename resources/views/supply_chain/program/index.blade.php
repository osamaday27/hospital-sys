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
                @include('partials.icon', [
                    'title' => __('supply_chain_trans.supply_chain_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-plus-box',
                            'text' => __('supply_chain_trans.add_items'),
                            'link' => 'storemovement',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-check-outline',
                            'text' => __('supply_chain_trans.receiving_inspecting_items'),
                            'link' => 'receiving_inspecting_page',
                        ],
                        [
                            'icon' => 'mdi mdi-database-import',
                            'text' => __('supply_chain_trans.initial_balances'),
                            'link' => 'view_opening_balance',
                        ],


                        [
                            'icon' => 'mdi mdi-transfer-right',
                            'text' => __('supply_chain_trans.transfer_to_branch_store'),
                            'link' => 'transfer_substore',
                        ],
                        [
                            'icon' => 'mdi mdi-truck-delivery',
                            'text' => __('supply_chain_trans.receive_withdraw_requests'),
                            'link' => 'receive_withdraw_requests',
                        ],
                        [
                            'icon' => 'mdi mdi-cart-arrow-down',
                            'text' => __('supply_chain_trans.withdrawal_requests'),
                            'link' => 'withdrawal_requests',
                        ],
                        [
                            'icon' => 'mdi mdi-shape-plus',
                            'text' => __('supply_chain_trans.new_item_request'),
                            'link' => 'new_item_request',
                        ],
                        [
                            'icon' => 'mdi mdi-delete-alert',
                            'text' => __('supply_chain_trans.write_off_items'),
                            'link' => 'write_off_items',
                        ],
                        [
                            'icon' => 'mdi mdi-file-document-multiple',
                            'text' => __('supply_chain_trans.inventory'),
                            'link' => 'inventory_audits',
                        ],
                        [
                            'icon' => 'mdi mdi-calendar-edit',
                            'text' => __('supply_chain_trans.edit_item_expiry'),
                            'link' => 'edit_item_expiry',
                        ],
                        [
                            'icon' => 'mdi mdi-scale-balance',
                            'text' => __('supply_chain_trans.adjust_balance'),
                            'link' => 'adjust_balance',
                        ],
                        [
                            'icon' => 'mdi mdi-backup-restore',
                            'text' => __('supply_chain_trans.return_items'),
                            'link' => 'return_items',
                        ],
                        [
                            'icon' => 'mdi mdi-barcode',
                            'text' => __('supply_chain_trans.print_barcode'),
                            'link' => 'print_barcode',
                        ]
                    ],
                ])
                </div>

            </div>
            <!-- End Content Row -->
        </div>
        <!-- End col -->
@endsection
