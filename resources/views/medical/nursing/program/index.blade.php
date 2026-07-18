@extends('layouts.master')

@section('title')
    {{ trans('nursing_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('nursing_trans.title') }}</a>
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
                    'title' => __('nursing_trans.nursing_program'),
                    'cards' => [
                        [
                            'icon' => 'trgtjfff.json', // أيقونة مناسبة للعيادات الخارجية
                            'text' => __('nursing_trans.outpatient_clinics'),
                            'link' => 'nursing_outpatient_clinics',
                        ],
                        [
                            'icon' => 'eouimtlu.json', // أيقونة مناسبة للمرضى الداخليين
                            'text' => __('nursing_trans.inpatient'),
                            'link' => 'nursing_inpatient',
                        ],
                        [
                            'icon' => 'dyfvgeqj.json', // أيقونة مناسبة للعمليات
                            'text' => __('nursing_trans.surgeries'),
                            'link' => 'surgeries',
                        ],
                      /*  [
                            'icon' => 'thvvrdbi.json', // أيقونة مناسبة للطوارئ
                            'text' => __('nursing_trans.emergency'),
                            'link' => 'emergency',
                        ],*/
                    ],
                ])
            </div>

        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->

@endsection
