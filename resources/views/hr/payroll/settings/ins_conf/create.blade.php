@extends('layouts.master_table')

@section('title')
    {{ trans('hr.add_ins_conf_title') }}
@stop


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ trans('hr.add_ins_conf_title') }}</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('hr_ins_conf.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">{{ trans('hr.conf_name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="employee_share">{{ trans('hr.employee_share') }}</label>
                            <input type="number" step="0.01" name="employee_share" id="employee_share" class="form-control" value="11.0" required>
                        </div>

                        <div class="form-group">
                            <label for="company_share">{{ trans('hr.company_share') }}</label>
                            <input type="number" step="0.01" name="company_share" id="company_share" class="form-control" value="18.75" required>
                        </div>

                        <div class="form-group">
                            <label for="social_insurance_margin">{{ trans('hr.social_insurance_margin') }}</label>
                            <input type="number" step="0.01" name="social_insurance_margin" id="social_insurance_margin" class="form-control" value="12600.0" required>
                        </div>
                       
                        <br>

                        <button type="submit" class="btn btn-primary">{{ trans('hr.save') }}</button>
                        <a href="{{ route('hr_ins_conf.index') }}" class="btn btn-secondary">{{ trans('hr.cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection