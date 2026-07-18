@extends('layouts.master')

@section('title', __('jobs.title'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ __('jobs.mainTitle') }}</h4>
                <a href="{{ route('jop_settings.create') }}" class="btn btn-primary">{{ __('jobs.create') }}</a>
            </div>
            <div class="card-body">

                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('jobs.name_en') }}</th>
                            <th>{{ __('jobs.name_ar') }}</th>
                            <th>{{ __('jobs.status') }}</th>
                            <th>{{ __('jobs.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobs as $index => $job)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $job->name_en ?? 'N/A' }} </td>
                            <td>{{ $job->name_ar ?? 'N/A' }} </td>
                            <td>
                                @if($job->status)
                                    <span class="badge bg-success">{{ __('jobs.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('jobs.inactive') }}</span>
                                @endif
                            </td>
                          
                            <td>
                                <a href="{{ route('jop_settings.edit', $job->id) }}" class="btn btn-warning">{{ __('jobs.edit') }}</a>
                                <form action="{{ route('jop_settings.destroy', $job->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('jobs.confirm_delete') }}');">
                                        {{ __('jobs.delete') }}
                                    </button>
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

<script>

new DataTable('#example');

</script>

@endsection
