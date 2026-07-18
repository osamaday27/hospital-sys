@extends('layouts.master')

@section('title', __('how_to_identify_place.title'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ __('how_to_identify_place.mainTitle') }}</h4>
                <a href="{{ route('how_to_identify_place_settings.create') }}" class="btn btn-primary">{{ __('how_to_identify_place.create') }}</a>
            </div>
            <div class="card-body">

                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('how_to_identify_place.name_en') }}</th>
                            <th>{{ __('how_to_identify_place.name_ar') }}</th>
                            <th>{{ __('how_to_identify_place.status') }}</th>
                            <th>{{ __('how_to_identify_place.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($how_to_identify_place as $index => $how_to_identify)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $how_to_identify->name_en ?? 'N/A' }} </td>
                            <td>{{ $how_to_identify->name_ar ?? 'N/A' }} </td>
                            <td>
                                @if($how_to_identify->status)
                                    <span class="badge bg-success">{{ __('how_to_identify_place.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('how_to_identify_place.inactive') }}</span>
                                @endif
                            </td>
                          
                            <td>
                                <a href="{{ route('how_to_identify_place_settings.edit', $how_to_identify->id) }}" class="btn btn-warning">{{ __('how_to_identify_place.edit') }}</a>
                                <form action="{{ route('how_to_identify_place_settings.destroy', $how_to_identify->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('how_to_identify_place.confirm_delete') }}');">
                                        {{ __('how_to_identify_place.delete') }}
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
