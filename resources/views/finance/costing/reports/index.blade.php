@extends('layouts.master')

@section('title')
    {{ trans('outpatient_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('outpatient_trans.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
       <!-- Reports Section -->
        <div class="row justify-content-evenly mt-4">
            <div class="col-lg-12">
                <h5 class="mb-3 text-info">{{ __('costing_trans.section_reports') }}</h5>
                @include('partials.icon', [
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-account-cash-outline',
                            'text' => __('costing_trans.report_case_profitability'),
                            'link' => 'costing.reports.case-profitability',
                        ],
                        [
                            'icon' => 'mdi mdi-stethoscope',
                            'text' => __('costing_trans.report_service_profitability'),
                            'link' => 'costing.reports.service-profitability',
                        ],
                        [
                            'icon' => 'mdi mdi-domain',
                            'text' => __('costing_trans.report_center_pnl'),
                            'link' => 'costing.reports.center-pnl',
                        ],
                        [
                            'icon' => 'mdi mdi-vector-intersection',
                            'text' => __('costing_trans.report_overhead_distribution'),
                            'link' => 'costing.reports.overhead-distribution',
                        ],
                        [
                            'icon' => 'mdi mdi-chart-line-variant',
                            'text' => __('costing_trans.report_trends'),
                            'link' => 'costing.reports.trends',
                        ],
                        [
                            'icon' => 'mdi mdi-flask-outline',
                            'text' => __('costing_trans.report_supplies_usage'),
                            'link' => 'costing.reports.supplies-usage',
                        ],
                    ],
                ])
            </div>
        </div>
    </div>


@endsection
