@extends('layouts.master')

@section('title')
    {{ trans('call_center_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('call_center_trans.title') }}</a>
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
                    'title' => __('call_center_trans.inpatient_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-format-list-checks', // ✅ إعدادات الإجابات
                            'text' => __('call_center_trans.answers_settings'),
                            'link' => 'answer_types',
                        ],
                        [
                            'icon' => 'mdi mdi-help-circle-outline', // ✅ إعدادات الأسئلة
                            'text' => __('call_center_trans.questions_settings'),
                            'link' => 'questionnaires',
                        ],
                        [
                            'icon' => 'mdi mdi-alert-octagon-outline', // ✅ إعدادات الشكاوى
                            'text' => __('call_center_trans.complaints_settings'),
                            'link' => 'complaints-category',
                        ],
                        // [
                        //     'icon' => 'mdi mdi-comment-question-outline', // ✅ الاستفسارات
                        //     'text' => __('call_center_trans.enquiry'),
                        //     'link' => 'enquiry',
                        // ],
                        [
                            'icon' => 'mdi mdi-calendar-remove', // ✅ اسباب عدم الحضور
                            'text' => __('call_center_trans.absence_reasons'),
                            'link' => 'absence_reasons',
                        ],
                    ],
                ])
            </div>

        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->
@endsection
