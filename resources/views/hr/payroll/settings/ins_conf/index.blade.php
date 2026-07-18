@extends('layouts.master_table')

@section('title')
    {{ trans('hr.ins_conf_title') }}
@stop

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h2>{{ trans('hr.ins_conf_title') }}</h2>
                    <br>
                    <a href="{{ route('hr_ins_conf.create') }}" class="btn btn-primary float-right">{{ trans('hr.add_ins_conf') }}</a>
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
                                <th>{{ trans('hr.employee_share') }}</th>
                                <th>{{ trans('hr.company_share') }}</th>
                                <th>{{ trans('hr.social_insurance_margin') }}</th>
                                <th>{{ trans('hr.action') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($insConfs as $insConf)
                                <tr>
                                    <td>{{ $insConf->name }}</td>
                                    <td>
                                            <form action="{{ route('hr-ins-confs.toggle-status', $insConf->id) }}" method="POST">
                                                @csrf
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="status-{{ $insConf->id }}" 
                                                        name="status" value="1" {{ $insConf->status ? 'checked' : '' }}
                                                        onchange="this.form.submit()">
                                                
                                                </div>
                                            </form>
                                        </td>
                                    <td>{{ number_format($insConf->employee_share, 2) }}</td>
                                    <td>{{ number_format($insConf->company_share, 2) }}</td>
                                    <td>{{ number_format($insConf->social_insurance_margin, 2) }}</td>
                                    <td>
                                        <a href="{{ route('hr_ins_conf.show', $insConf->id) }}" class="btn btn-info btn-sm">{{ trans('hr.show') }}</a>
                                         
                                        <form action="{{ route('hr_ins_conf.destroy', $insConf->id) }}" method="POST" style="display: inline-block;">
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