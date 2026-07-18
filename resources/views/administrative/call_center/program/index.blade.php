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
                    'title' => __('call_center_trans.call_center_program'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-calendar-check-outline',   // ✅ أيقونة الحجز
                            'text' => __('call_center_trans.booking'),
                            'link' => 'call_center_program',
                        ],
                        [
                            'icon' => 'mdi mdi-file-document-outline',    // ✅ أيقونة الاستبيانات
                            'text' => __('call_center_trans.patient_questionnaires'),
                            'link' => 'all_questionnaires',
                        ],
                        [
                            'icon' => 'mdi mdi-alert-circle-outline',     // ✅ أيقونة الشكاوى
                            'text' => __('call_center_trans.complaints'),
                            'link' => 'complaints',
                        ],
                        [
                            'icon' => 'mdi mdi-calendar-text-outline',    // ✅ أيقونة الاستبيان اليومي
                            'text' => __('call_center_trans.daily_questionnaires'),
                            'link' => route('daily_questionnaires'),
                        ],
                        [
                            'icon' => 'mdi mdi-account-question-outline', // ✅ أيقونة استفسارات المرضى
                            'text' => __('call_center_trans.patient_inquiries'),
                            'link' => 'patient_inquiries',
                        ],
                        [
                            'icon' => 'mdi mdi-account-off-outline',      // ✅ أيقونة النوشو
                            'text' => __('call_center_trans.no_show_reservations'),
                            'link' => route('no_show_reservations'),     // 🔗 هنا لازم تعمل Route
                        ],
                    ],
                ])
            </div>

        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->
@endsection
