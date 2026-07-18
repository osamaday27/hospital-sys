@extends('layouts.master')

@section('title')
    {{ trans('inpatient_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('inpatient_trans.title') }}</a>
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
                    'title' => __('inpatient_trans.inpatient_program'),
                    'cards' => [
                        [
                            'icon' => 'abfverha.json',
                            'text' => __('inpatient_trans.inpatient_booking'),
                            'link' => 'inpatient_booking',
                        ],
                        [
                            'icon' => 'eouimtlu.json',
                            'text' => __('inpatient_trans.beds_map'),
                            'link' => 'beds_map',
                        ],
                        [
                            'icon' => 'vspbqszr.json',
                            'text' => __('inpatient_trans.Confirming patients discharge'),
                            'link' => 'conf_pat_dis',
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
