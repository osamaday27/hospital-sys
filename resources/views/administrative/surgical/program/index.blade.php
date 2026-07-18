@extends('layouts.master')

@section('title')
    {{ trans('surgical_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('surgical_trans.title') }}</a>
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
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-calendar-check',
                            'text' => __('surgical_trans.surgical_reception'),
                            'link' => 'surgical_booking',
                        ],
                        [
                            'icon' => 'mdi mdi-hospital-box-outline',
                            'text' => __('surgical_trans.surgical_manager'),
                            'link' => 'surgical_manager',
                        ],
                        [
                            'icon' => 'mdi mdi-doctor',
                            'text' => __('surgical_trans.nursing'),
                            'link' => 'surgical_nursing',
                        ],
                        [
                            'icon' => 'mdi mdi-stethoscope',
                            'text' => __('surgical_trans.doctor'),
                            'link' => 'surgical_doctor ',
                        ],
                        [
                            'icon' => 'mdi mdi-head-snowflake', // تمثل التخدير بشكل رمزي
                            'text' => __('surgical_trans.surgical_anesthesia'),
                            'link' => 'surgical_anesthesia',
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
