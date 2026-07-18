@extends('layouts.master')

@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ trans('hr.settings') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.settings') }}</li>
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
                        'title' => __('hr.settings'),
                        'cards' => [
                            
                            ['icon' => 'fwkrbvja.json', 'text' => __('hr.tax_conf'), 'link' => 'hr_tax_conf'],
                            ['icon' => 'fqbvgezn.json', 'text' => __('hr.ins_conf'), 'link' => 'hr_ins_conf'],
                            ['icon' => 'bktacmnd.json', 'text' => __('hr.salary_rules'), 'link' => 'hr_salary_rules'],
                            ['icon' => 'bktacmnd.json', 'text' => __('hr.social_insurance_title'), 'link' => 'hr_social_insurance'],
                            ['icon' => 'bktacmnd.json', 'text' => __('hr.allowances_types'), 'link' => 'hr_allowances_types'],
                        ]
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection
