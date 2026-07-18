@extends('layouts.master_table')

@section('title')
    {{ trans('hr.ins_conf_title') }}
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ trans('hr.view_ins_conf_title') }}</h2>
                </div>

                <div class="card-body">
            
                    <div class="form-group">
                        <label>{{ trans('hr.employee_share') }}</label>
                        <p class="form-control-static">{{ number_format($insConfs->employee_share, 2) }}</p>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('hr.company_share') }}</label>
                        <p class="form-control-static">{{ number_format($insConfs->company_share, 2) }}</p>
                    </div>


                    <div class="form-group">
                        <label>{{ trans('hr.social_insurance_margin') }}</label>
                        <p class="form-control-static">{{ number_format($insConfs->social_insurance_margin, 2) }}</p>
                    </div>


                    <a href="{{ route('hr_ins_conf.index') }}" class="btn btn-secondary">{{ trans('hr.back') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection