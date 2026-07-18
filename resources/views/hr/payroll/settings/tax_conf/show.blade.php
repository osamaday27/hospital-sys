@extends('layouts.master_table')

@section('title')
    {{ trans('hr.tax_conf_title') }}
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ trans('hr.view_tax_conf_title') }}</h2>
                </div>

                <div class="card-body">
            
                    <div class="form-group">
                        <label>{{ trans('hr.exemption_margin') }}</label>
                        <p class="form-control-static">{{ number_format($hrTaxConf->exemption_margin, 2) }}</p>
                    </div>

                    <h4>{{ trans('hr.tax_segments') }}</h4>
                    <div class="row">
                        @for ($i = 1; $i <= 10; $i++)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>شريحة {{ $i }}</label>
                                    <p class="form-control-static">{{ number_format($hrTaxConf->{'segment_'.$i}, 2) }}</p>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <a href="{{ route('hr_tax_conf.index') }}" class="btn btn-secondary">{{ trans('hr.back') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection