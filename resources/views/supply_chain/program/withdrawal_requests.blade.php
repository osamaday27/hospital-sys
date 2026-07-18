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
                      
                        // ✅ الروابط الجديدة
                        [
                            'icon' => 'mdi mdi-account-arrow-right-outline',
                            'text' => __('supply_chain_trans.issue_items_to_employee'),
                            'link' => 'issue_items_to_employee',
                        ],
                        [
                            'icon' => 'mdi mdi-office-building',
                            'text' => __('supply_chain_trans.issue_items_to_department'),
                            'link' => 'issue_items_to_department',
                        ],
                        [
                            'icon' => 'mdi mdi-account-box-multiple',
                            'text' => __('supply_chain_trans.request_issue_to_client'),
                            'link' => 'request_issue_to_client',
                        ],
                        [
                            'icon' => 'mdi mdi-archive-arrow-down-outline',
                            'text' => __('supply_chain_trans.request_transfer_from_backup'),
                            'link' => 'request_transfer_from_backup',
                        ],
                        [
                            'icon' => 'mdi mdi-warehouse',
                            'text' => __('supply_chain_trans.issue_items_to_store'),
                            'link' => 'issue_items_to_store',
                        ],
                    ],
                ])
                
            </div>

        </div>
        <!-- End Content Row -->
    </div>
    <!-- End col -->
@endsection
