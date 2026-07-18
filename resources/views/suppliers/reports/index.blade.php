@extends('layouts.master')

@section('title')
{{ trans('suppliers.reports_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('suppliers.reports_title') }}</a>
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
                    'title' => __('suppliers.reports_title'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-clipboard-text-search-outline',
                            'text' => __('suppliers.supplier_payment_report_title'),
                            'link' => 'supplier_payment_report',
                        ],
                        // [
                        //     'icon' => 'mdi mdi-truck-delivery',
                        //     'text' => __('suppliers.late_delivery_items_title'),
                        //     'link' => 'late_delivery_items',
                        // ],
                        // [
                        //     'icon' => 'mdi mdi-star-circle-outline',
                        //     'text' => __('suppliers.most_purchased_items_title'),
                        //     'link' => 'most_purchased_items',
                        // ],
                        // [
                        //     'icon' => 'mdi mdi-clipboard-check',
                        //     'text' => __('suppliers.supply_orders_report_title'),
                        //     'link' => 'supply_orders_report',
                        // ],
                    ],
                ])
                            </div>

                        </div>
                        <!-- End Content Row -->
                    </div>
                    <!--end col-->

                    </div>


@endsection
