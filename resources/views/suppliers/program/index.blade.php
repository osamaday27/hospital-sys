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
                @include('partials.icon', [
                    'title' => __('suppliers.supply_chain_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-cash-register',
                            'text' => __('suppliers.opening_balance'),
                            'link' => 'supplier_opening_balances',
                        ],
                        [
                            'icon' => 'mdi mdi-file-document-outline',
                            'text' => __('suppliers.service_invoices'),
                            'link' => 'serviceinvoicesindex',
                        ],
                        [
                            'icon' => 'mdi mdi-warehouse',
                            'text' => __('suppliers.warehouse_invoices'),
                            'link' => 'warehouse_invoices',
                        ],
                        [
                            'icon' => 'mdi mdi-wallet',
                            'text' => __('suppliers.supplier_accounts'),
                            'link' => 'suppliers_accounts',
                        ],
                
                        [
                            'icon' => 'mdi mdi-bookmark-box-multiple-outline',
                            'text' => __('suppliers.supplier_notifications'),
                            'link' => 'supplier_alerts',
                        ],
                        [
                            'icon' => 'mdi mdi-account-multiple-plus',
                            'text' => __('suppliers.merge_suppliers'),
                            'link' => 'suppliers_merge',
                        ],
                        [
                            'icon' => 'mdi mdi-bell-outline',
                            'text' => __('suppliers.supplier_notification'),
                            'link' => 'supplier_notifications',
                        ],
                    ],
                ])
            </div>

        </div>
        <!-- End Content Row -->
    </div>
    <!-- End col -->
@endsection
