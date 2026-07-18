@extends('layouts.master')

@section('title')
{{ trans('purchases.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('purchases.title') }}</a>
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
                    'title' => __('purchases.purchases_programs'),
                    'cards' => [
                        [
                            'icon' => 'jprtoagx.json',
                            'text' => __('purchases.item_purchase_request'),
                            'link' => 'purchase_requisitions',
            ],        [
                            'icon' => 'fqbvgezn.json',
                            'text' => __('purchases.management_of_demand_and_supply_requests'),
                            'link' => 'purchase_order',
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
