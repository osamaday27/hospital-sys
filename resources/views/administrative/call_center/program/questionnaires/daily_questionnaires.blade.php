{{-- resources/views/administrative/call_center/program/questionnaires/daily_questionnaires.blade.php --}}
@extends('layouts.master_table')

@section('title')
    {{ trans('call_center_trans.daily_questionnaires') }}
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font">{{ trans('call_center_trans.daily_questionnaires') }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            {{-- Header with search, reload and export --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <input type="search" id="search-daily" class="form-control"
                       placeholder="{{ __('call_center_trans.search_daily_reservations') }}"
                       style="width: 250px;">

                <div class="hstack gap-2">
                    <button id="reload-table" class="btn btn-info">
                        <i class="ri-repeat-2-line"></i>
                    </button>
                    <button id="export-csv" class="btn btn-primary">
                        <i class="fa fa-file-csv"></i> CSV
                    </button>
                    <button id="export-xlsx" class="btn btn-success">
                        <i class="fa fa-file-excel"></i> XLSX
                    </button>
                </div>
            </div>

            <div id="alert-container"></div>

            <div class="card-body">
                <table id="todays_visit-table"
                       class="table dt-responsive nowrap align-middle"
                       style="width:100%; border: aliceblue;">
                    <thead>
                        <tr>
                            <th>{{ trans('call_center_trans.patient_name') }}</th>
                            <th>{{ trans('call_center_trans.clinic_name') }}</th>
                            <th>{{ trans('call_center_trans.doctor_name') }}</th>
                            <th>{{ trans('call_center_trans.visit_type') }}</th>
                            <th>{{ trans('call_center_trans.created_by') }}</th>
                            <th>{{ trans('call_center_trans.status') }}</th>
                            <th>{{ trans('call_center_trans.create_questionaire') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    var table = $('#todays_visit-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('get.todays.visits') }}",
        columns: [
            { data: 'patient_name', name: 'patient.full_name' },
            { data: 'clinic_name',  name: 'clinic.clinic_name' },
            { data: 'doctor_name',  name: 'doctor.name' },
            { data: 'visit_type',   name: 'patient_visits.type' },
            { data: 'created_by',   name: 'user.name' },
            { data: 'status',       name: 'status' },
            { data: 'action',       name: 'action', orderable: false, searchable: false },
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csv',
                title: '{{ trans("call_center_trans.daily_questionnaires") }} Data',
                className: 'buttons-csv',
                text: '<i class="fa fa-file-csv"></i> CSV'
            },
            {
                extend: 'excel',
                title: '{{ trans("call_center_trans.daily_questionnaires") }} Data',
                className: 'buttons-excel',
                text: '<i class="fa fa-file-excel"></i> XLSX'
            }
        ],
        language: {
            lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
            info:       "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
            processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
            paginate: {
                previous: "{{ __('datatables.previous') }}",
                next:     "{{ __('datatables.next') }}"
            },
            emptyTable: `<div class="py-4 text-center">
                <lord-icon src="{{ URL::asset('assets/images/icon/msoeawqm.json') }}"
                    trigger="loop" colors="primary:#405189,secondary:#0ab39c"
                    style="width:72px;height:72px"></lord-icon>
                <h5 class="mt-4">{{ trans('outpatient_trans.sorry_no_result_found') }}</h5>
            </div>`
        }
    });

    $('#reload-table').on('click', function () {
        table.ajax.reload();
    });
    $('#search-daily').on('input', function () {
        table.search(this.value).draw();
    });
    $('#export-csv').on('click', function () {
        table.button('.buttons-csv').trigger();
    });
    $('#export-xlsx').on('click', function () {
        table.button('.buttons-excel').trigger();
    });
});
</script>
@endsection
