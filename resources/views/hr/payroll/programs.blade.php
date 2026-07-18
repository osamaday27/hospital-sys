@extends('layouts.master')

@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.programs') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ trans('hr.programs') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.programs') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
       
            <div class="row justify-content-evenly mt-3">
                <div class="col-lg-12">
                    @include('partials.main', [
                        'title' => __('hr.programs'),
                        'cards' => [

                            ['icon' => 'fjvfsqea.json', 'text' => __('hr.payslips'), 'link' => 'hr_payslips']],
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection
