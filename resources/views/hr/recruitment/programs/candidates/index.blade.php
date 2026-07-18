@extends('layouts.master_table')

@section('title')
    {{ trans('hr.recruitment_candidates') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.recruitment_candidates') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('hr.filter_candidates') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="requisition_filter" class="form-label">{{ trans('hr.filter_by_requisition') }}</label>
                                <select id="requisition_filter" class="form-select js-example-basic-single">
                                    <option value="">{{ trans('hr.all_requisitions') }}</option>
                                    @foreach($requisitions as $requisition)
                                        <option value="{{ $requisition->id }}">
                                            {{ $requisition->requisition_name }} - {{ $requisition->job->name_ar ?? $requisition->job->job_title }} 
                                            ({{ $requisition->candidates->count() }}/{{ $requisition->number_of_candidates }} {{ trans('hr.candidates') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <!-- DataTable Search Input moved to the right -->
                    <div class="d-flex align-items-center">
                        <input type="search" class="form-control" id="search-candidates-table"
                            placeholder="{{ trans('hr.search_candidates') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Add Button -->
                         <a href="{{ route('hr_candidates.create') }}" class="btn btn-warning">
                            <i class="ri-user-add-line"></i>
                        </a>
                        <!-- Reload Button -->
                        <button id="reload-table" class="btn btn-info">
                            <i class="ri-repeat-2-line"></i>
                        </button>
                        <button class="btn btn-primary" id="export-csv">
                            <i class="fa fa-file-csv"></i> CSV
                        </button>
                        <button class="btn btn-success" id="export-xlsx">
                            <i class="fas fa-file-excel"></i> XLSX
                        </button>
                    </div>
                </div>
                <!-- Candidates Table -->
                <div class="card-body">
                    <table id="candidates-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.full_name') }}</th>
                                <th>{{ trans('hr.email') }}</th>
                                <th>{{ trans('hr.gender') }}</th>
                                <th>{{ trans('hr.age') }}</th>
                                <th>{{ trans('hr.city') }}</th>
                                <th>{{ trans('hr.nationality') }}</th>
                                <th>{{ trans('hr.status') }}</th>
                                <th>{{ trans('hr.cv_file') }}</th>
                                <th>{{ trans('hr.requisition') }}</th>
                                <th>{{ trans('hr.create_date') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                        colors="primary:#f7b84b,secondary:#f06548"
                                        style="width:100px;height:100px"></lord-icon>
                                    <div class="mt-4">
                                        <h4>{{ __('hr.h4_delete') }}</h4>
                                        <p>{{ __('hr.p_delete') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mt-4">
                                    <form action="" method="post" id="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="id" name="id">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">{{ __('hr.close') }}</button>
                                        <button type="button" class="btn btn-danger"
                                            id="delete-record">{{ __('hr.yes_delete') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
    $(function() {
        var table = $('#candidates-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ route('hr_candidates.data') }}",
                data: function(d) {
                    d.requisition_id = $('#requisition_filter').val();
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTable Error:', error);
                    console.log('XHR:', xhr);
                    console.log('Thrown:', thrown);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'full_name', name: 'full_name' },
                { data: 'email', name: 'email' },
                { data: 'gender_text', name: 'gender' },
                { data: 'age', name: 'age', orderable: false },
                { data: 'city_name', name: 'city_name' },
                { data: 'nationality_name', name: 'nationality_name' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { data: 'cv_file', name: 'cv_file', orderable: false, searchable: false },
                { data: 'requisition_name', name: 'requisition_name' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Candidates Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Candidates Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
            ],
            language: {
                lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }} ",
                paginate: { "previous": "{{ __('datatables.previous') }}", "next": "{{ __('datatables.next') }}" }
            }
        });

        $('#reload-table').click(function() {
            table.ajax.reload();
        });

        $('#search-candidates-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#requisition_filter').on('change', function() {
            table.ajax.reload();
        });

        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });

        // Initialize Select2
        $('.js-example-basic-single').select2();
    });

    function deleteFunction(id) {
        $('#id').val(id);
        $('#deleteModal').modal('show');
    }

    $('#delete-record').click(function() {
        var id = $('#id').val();
        var url = '{{ route('hr_candidates.destroy', ':id') }}';
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
                    $('#candidates-table').DataTable().ajax.reload();
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

    function updateStatus(candidateId, newStatus) {
        if (confirm('{{ trans("hr.confirm_status_update") }}')) {
            $.ajax({
                url: '/hr_candidates/' + candidateId + '/update-status',
                type: 'POST',
                data: {
                    "status": newStatus,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        $('#candidates-table').DataTable().ajax.reload();
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
                    var errorMessage = 'Error updating status';
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
        }
    }
</script>
@endsection
