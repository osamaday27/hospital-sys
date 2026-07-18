@extends('layouts.master')

@section('title')
    {{ trans('costing_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('costing_trans.title') }}</a>
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

            <!-- Costing Settings Section -->
            <div class="col-lg-12">
                @include('partials.icon', [
                    'title' => __('costing_trans.costing_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-source-branch',
                            'text' => __('costing_trans.cost_centers'),
                            'link' => 'cost_centers',
                        ],
                        [
                            'icon' => 'mdi mdi-cash-multiple',
                            'text' => __('costing_trans.overhead_pools'),
                            'link' => 'overhead_pools',
                        ],
                        [
                            'icon' => 'mdi mdi-chart-areaspline',
                            'text' => __('costing_trans.overhead_allocation'),
                            'link' => 'overhead_allocation',
                        ],
                        [
                            'icon' => 'mdi mdi-account-clock',
                            'text' => __('costing_trans.resource_rates'),
                            'link' => 'resource_rates',
                        ],
                        [
                            'icon' => 'mdi mdi-file-tree',
                            'text' => __('costing_trans.service_boms'),
                            'link' => 'service_boms',
                        ],
                          [
                            'icon' => 'mdi mdi-file-tree',
                            'text' => __('costing_trans.cost_center_linking'),
                            'link' => 'cost_center_linking',
                        ],
                    ],
                ])
            </div>
        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->
@endsection
