@extends('layouts.master_table')

@section('title')
    {{ trans('hr.add_tax_conf_title') }}
@stop


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ trans('hr.add_tax_conf_title') }}</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('hr_tax_conf.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">{{ trans('hr.conf_name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="exemption_margin">{{ trans('hr.exemption_margin') }}</label>
                            <input type="number" step="0.01" name="exemption_margin" id="exemption_margin" class="form-control" value="20000.00" required>
                        </div>
                        <br>
                        <h4>{{ trans('hr.tax_segments') }}</h4>
                        <br>
                        <div class="row">
                            @for ($i = 1; $i <= 10; $i++)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="segment_{{ $i }}">شريحة {{ $i }}</label>
                                        <input type="number" step="0.01" name="segment_{{ $i }}" id="segment_{{ $i }}" class="form-control" 
                                            value="{{ [
                                                1 => 40000.00,
                                                2 => 55000.00,
                                                3 => 70000.00,
                                                4 => 200000.00,
                                                5 => 400000.00,
                                                6 => 600000.00,
                                                7 => 700000.00,
                                                8 => 800000.00,
                                                9 => 900000.00,
                                                10 => 1200000.00,
                                            ][$i] }}" required>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <br>

                        <button type="submit" class="btn btn-primary">{{ trans('hr.save') }}</button>
                        <a href="{{ route('hr_tax_conf.index') }}" class="btn btn-secondary">{{ trans('hr.cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection