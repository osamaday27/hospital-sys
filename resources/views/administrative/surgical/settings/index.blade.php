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
                    'title' => __('surgical_trans.surgical_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-hospital-building', // 🏥 إعدادات غرف العمليات
                            'text' => __('surgical_trans.room_settings'),
                            'link' => 'roomsurgicalsettings',
                        ],
                        [
                            'icon' => 'mdi mdi-knife-military', // 🩺 إعدادات العمليات الجراحية
                            'text' => __('surgical_trans.surgical_settings'),
                            'link' => 'surgicaldatasettings',
                        ],
                        [
                            'icon' => 'mdi mdi-calendar-clock', // 📅 جدولة غرف العمليات
                            'text' => __('surgical_trans.scheduling_operation_room'),
                            'link' => 'scheduling_operation_room',
                        ],
                      /* [
                            'icon' => 'mdi mdi-cash-multiple', // 💰 الإعدادات المالية للعمليات
                            'text' => __('surgical_trans.surgical_financial_settings'),
                            'link' => 'surgical_financial_settings',
                        ],*/
                        [
                            'icon' => 'mdi mdi-content-copy', // 🧩 نسخ الإعدادات الجراحية
                            'text' => __('surgical_trans.copy_surgical_settings'),
                            'link' => 'copy_surgical_tools',
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
