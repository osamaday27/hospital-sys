@extends('layouts.master_table')

@section('title')
    {{ trans('surgical_trans.title') }}
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font">{{ trans('surgical_trans.title') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('surgical_settings.index') }}">{{ trans('surgical_trans.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('surgical_trans.operation_room') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <input type="search" class="form-control" id="search-name" placeholder="ابحث بالاسم"
                        style="width: 200px;">
                    <input type="search" class="form-control" id="search-floor" placeholder="ابحث بالدور"
                        style="width: 150px;">
                    <input type="search" class="form-control" id="search-type" placeholder="ابحث بالنوع"
                        style="width: 150px;">
                </div>
                <div class="hstack gap-2">
                    <a href="{{ route('roomsurgicalsettings.create') }}"
                        class="btn btn-outline-secondary waves-effect waves-light shadow-none">
                        <i class="ri-add-line"></i> {{ trans('surgical_trans.create_room') }}
                    </a>
                    <button id="reload-table" class="btn btn-info">
                        <i class="ri-repeat-2-line"></i>
                    </button>
                </div>
            </div>

            <div id="alert-container"></div>

            <div class="card-body">
                <table id="roomsurgicalsettings-table" class="table dt-responsive nowrap align-middle"
                    style="width: 100%; border: aliceblue;">
                    <thead>
                        <tr>
                            <th>{{ trans('surgical_trans.sr_no') }}</th>
                            <th>{{ trans('surgical_trans.action') }}</th>
                            <th>{{ trans('surgical_trans.name') }}</th>
                            <th>{{ trans('surgical_trans.floor') }}</th>
                            <th>{{ trans('surgical_trans.type') }}</th>
                            <th>{{ trans('surgical_trans.status') }}</th>
                            <th>{{ trans('surgical_trans.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    var table = $('#roomsurgicalsettings-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('roomsurgicalsettings.index') }}",
            data: function (d) {
                d.name = $('#search-name').val();
                d.floor = $('#search-floor').val();
                d.type = $('#search-type').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'floor', name: 'floor' },
            { data: 'type', name: 'type' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' }
        ],
        language: {
            lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
            info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
            processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
            paginate: { "previous": "{{ __('datatables.previous') }}", "next": "{{ __('datatables.next') }}" }
        }
    });

    $('#search-name, #search-floor, #search-type').on('keyup change', function() {
        table.ajax.reload();
    });

    $('#reload-table').click(function() {
        $('#search-name, #search-floor, #search-type').val('');
        table.ajax.reload();
    });
});
</script>
@endsection
