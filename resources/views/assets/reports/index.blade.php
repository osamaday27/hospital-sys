@extends('layouts.master')

@section('title')
{{ trans('assets.reports_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('assets.reports_title') }}</a>
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
                    'title' => __('assets.reports_title'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-clipboard-text-search-outline',
                            'text' => __('assets.master_movement_report_title'),
                            'link' => 'master_movement_report',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-text-search-outline',
                            'text' => __('assets.depreciation_report_title'),
                            'link' => 'depreciation_report',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-text-search-outline',
                            'text' => __('assets.maintenance_report_title'),
                            'link' => 'maintenance_report',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-text-search-outline',
                            'text' => __('assets.audit_report_title'),
                            'link' => 'audit_report',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-text-search-outline',
                            'text' => __('assets.documents_report_title'),
                            'link' => 'documents_report',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-text-search-outline',
                            'text' => __('assets.disposal_lifecycle_report_title'),
                            'link' => 'disposal_lifecycle_report',
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
