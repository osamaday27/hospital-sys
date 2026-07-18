@extends('layouts.master_table')

@section('title')
    {{ trans('hr.tax_conf_title') }}
@stop

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h2>{{ trans('hr.tax_conf_title') }}</h2>
                    <br>
                    <a href="{{ route('hr_tax_conf.create') }}" class="btn btn-primary float-right">{{ trans('hr.add_tax_conf') }}</a>
                </div>

                

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                            <th>{{ trans('hr.conf_name') }}</th>
                            <th>{{ trans('hr.toggle_status') }}</th>
                                <th>{{ trans('hr.exemption_margin') }}</th>
                                <th>{{ trans('hr.segment_1') }}</th>
                                <th>{{ trans('hr.segment_2') }}</th>
                                <th>{{ trans('hr.segment_3') }}</th>
                                <th>{{ trans('hr.segment_4') }}</th>
                                <th>{{ trans('hr.segment_5') }}</th>
                                <th>{{ trans('hr.segment_6') }}</th>
                                <th>{{ trans('hr.segment_7') }}</th>
                                <th>{{ trans('hr.segment_8') }}</th>
                                <th>{{ trans('hr.segment_9') }}</th>
                                <th>{{ trans('hr.segment_10') }}</th>
                                <th>{{ trans('hr.action') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($taxConfs as $taxConf)
                                <tr>
                                    <td>{{ $taxConf->name }}</td>
                                    <td>
                                            <form action="{{ route('hr-tax-confs.toggle-status', $taxConf->id) }}" method="POST">
                                                @csrf
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="status-{{ $taxConf->id }}" 
                                                        name="status" value="1" {{ $taxConf->status ? 'checked' : '' }}
                                                        onchange="this.form.submit()">
                                                
                                                </div>
                                            </form>
                                        </td>
                                    <td>{{ number_format($taxConf->exemption_margin, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_1, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_2, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_3, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_4, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_5, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_6, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_7, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_8, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_9, 2) }}</td>
                                    <td>{{ number_format($taxConf->segment_10, 2) }}</td>
                                    <td>
                                        <a href="{{ route('hr_tax_conf.show', $taxConf->id) }}" class="btn btn-info btn-sm">{{ trans('hr.show') }}</a>
                                        <!-- <a href="{{ route('hr_tax_conf.edit', $taxConf->id) }}" class="btn btn-primary btn-sm">{{ trans('hr.edit') }}</a> -->
                                         
                                        <form action="{{ route('hr_tax_conf.destroy', $taxConf->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <br>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">{{ trans('hr.delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection