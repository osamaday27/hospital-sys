@extends('layouts.master_table')

@section('title')
    {{ trans('hr.offers_management') }}
@stop
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ trans('hr.offers_management') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('hr.hr') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('hr.recruitment') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.offers_management') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4 class="card-title">{{ trans('hr.offers_management') }}</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('hr_offers.create') }}" class="btn btn-primary">
                                <i class="ri-add-line"></i> {{ trans('hr.create_offer') }}
                            </a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status_filter" class="form-label">{{ trans('hr.filter_by_status') }}</label>
                            <select id="status_filter" class="form-select">
                                <option value="">{{ trans('hr.all_statuses') }}</option>
                                <option value="new">{{ trans('hr.new') }}</option>
                                <option value="approved">{{ trans('hr.approved') }}</option>
                                <option value="rejected">{{ trans('hr.rejected') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="offers-table" class="table dt-responsive nowrap align-middle" style="width: 100%; border: aliceblue;">
                            <thead>
                                <tr>
                                    <th>{{ trans('hr.sr_no') }}</th>
                                    <th>{{ trans('hr.action') }}</th>
                                    <th>{{ trans('hr.candidate_name') }}</th>
                                    <th>{{ trans('hr.job_title') }}</th>
                                    <th>{{ trans('hr.salary') }}</th>
                                    <th>{{ trans('hr.status') }}</th>
                                    <th>{{ trans('hr.created_by') }}</th>
                                    <th>{{ trans('hr.create_date') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center">
                                    <i class="ri-alert-line" style="font-size: 64px; color: red;"></i>
                                    <h4 class="mt-3">{{ trans('hr.confirm_delete') }}</h4>
                                    <input type="hidden" id="id">
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('hr.close') }}</button>
                                <button id="delete-record" type="button" class="btn btn-danger">{{ trans('hr.delete') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function () {
        var table = $('#offers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('hr_offers.data') }}",
                data: function (d) {
                    d.status = $('#status_filter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'candidate_name', name: 'candidate_name' },
                { data: 'job_title', name: 'job_title' },
                { data: 'salary', name: 'salary' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { data: 'creator_name', name: 'creator_name' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="ri-file-text-line"></i> CSV',
                    className: 'btn btn-success waves-effect waves-light',
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'excel',
                    text: '<i class="ri-file-excel-line"></i> Excel',
                    className: 'btn btn-success waves-effect waves-light',
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="ri-file-pdf-line"></i> PDF',
                    className: 'btn btn-danger waves-effect waves-light',
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="ri-printer-line"></i> Print',
                    className: 'btn btn-info waves-effect waves-light',
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7]
                    }
                }
            ],
            language: {
                search: "{{ trans('hr.search') }}:",
                lengthMenu: "{{ trans('hr.show') }} _MENU_ {{ trans('hr.entries') }}",
                info: "{{ trans('hr.showing') }} _START_ {{ trans('hr.to') }} _END_ {{ trans('hr.of') }} _TOTAL_ {{ trans('hr.entries') }}",
                paginate: {
                    first: "{{ trans('hr.first') }}",
                    last: "{{ trans('hr.last') }}",
                    next: "{{ trans('hr.next') }}",
                    previous: "{{ trans('hr.previous') }}"
                }
            }
        });

        $('#status_filter').change(function() {
            table.ajax.reload();
        });
    });

    function deleteFunction(id) {
        $('#id').val(id);
        $('#deleteModal').modal('show');
    }

    $('#delete-record').click(function() {
        var id = $('#id').val();
        var url = '{{ route('hr_offers.destroy', ':id') }}';
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    $('#offers-table').DataTable().ajax.reload();
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Record deleted successfully');
                    } else {
                        alert(response.message || 'Record deleted successfully');
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Error deleting record');
                    } else {
                        alert(response.message || 'Error deleting record');
                    }
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = 'There was an error deleting the record.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
    });

    function approveOffer(id) {
        if (confirm('{{ trans("hr.confirm_approve_offer") }}')) {
            $.ajax({
                url: '/hr_offers/' + id + '/approve',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        $('#offers-table').DataTable().ajax.reload();
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                },
                error: function(xhr) {
                    alert('Error approving offer');
                }
            });
        }
    }

    function rejectOffer(id) {
        if (confirm('{{ trans("hr.confirm_reject_offer") }}')) {
            $.ajax({
                url: '/hr_offers/' + id + '/reject',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        $('#offers-table').DataTable().ajax.reload();
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                },
                error: function(xhr) {
                    alert('Error rejecting offer');
                }
            });
        }
    }
</script>
@endsection
