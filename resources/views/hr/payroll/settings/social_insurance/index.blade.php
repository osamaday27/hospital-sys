@extends('layouts.master_table')

@section('title')
    {{ trans('hr.social_insurance_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.social_insurance_title') }}</a>
                        </li>
                    </ol>
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
                        <input type="search" class="form-control" id="search-social-insurance-table"
                            placeholder="{{ trans('hr.social_insurance_search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Add Button -->
                        <a href="{{ route('hr_social_insurance.create') }}" class="btn btn-warning">
                            <i class="ri-user-add-line"></i>
                        </a>
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
                <!-- Social Insurance Table -->
                <div class="card-body">
                    <table id="social-insurance-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.employee_code') }}</th>
                                <th>{{ trans('hr.job_position') }}</th>
                                <th>{{ trans('hr.department') }}</th>
                                <th>{{ trans('hr.hiring_date') }}</th>
                                <th>{{ trans('hr.has_valid_contract') }}</th>
                                <th>{{ trans('hr.marital_status') }}</th>
                                <th>{{ trans('hr.gender') }}</th>
                                <th>{{ trans('hr.phone') }}</th>
                                <th>{{ trans('hr.full_address') }}</th>
                                <th>{{ trans('hr.identification_number') }}</th>
                                <th>{{ trans('hr.insurance_status') }}</th>
                                <th>{{ trans('hr.subscription_date') }}</th>
                                <th>{{ trans('hr.insurance_office') }}</th>
                                <th>{{ trans('hr.insurance_number') }}</th>
                                <th>{{ trans('hr.health_status') }}</th>
                                <th>{{ trans('hr.insurance_company') }}</th>
                                <th>{{ trans('hr.main_salary') }}</th>
                                <th>{{ trans('hr.shared_salary') }}</th>
                                <th>{{ trans('hr.action') }}</th>
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
    $(document).ready(function() {
        var table = $('#social-insurance-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
                url: "{{ route('get-social-insurance') }}",
            },
            columns: [
                { data: 'employee_code', name: 'employee_code' },
                { data: 'job_position', name: 'job_position' },
                { data: 'department', name: 'department' },
                { data: 'hiring_date', name: 'hiring_date' },
                { data: 'has_valid_contract', name: 'has_valid_contract' },
                { data: 'marital_status', name: 'marital_status' },
                { data: 'gender', name: 'gender' },
                { data: 'phone', name: 'phone' },
                { data: 'full_address', name: 'full_address' },
                { data: 'identification_number', name: 'identification_number' },
                { data: 'insurance_status', name: 'insurance_status' },
                { data: 'subscription_date', name: 'subscription_date' },
                { data: 'insurance_office', name: 'insurance_office' },
                { data: 'insurance_number', name: 'insurance_number' },
                { data: 'health_status', name: 'health_status' },
                { data: 'insurance_company', name: 'insurance_company' },
                { data: 'main_salary', name: 'main_salary' },
                { data: 'shared_salary', name: 'shared_salary' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Social Insurance Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Social Insurance Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
            ],
            language: {
                lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }} ",
                paginate: { "previous": "{{ __('datatables.previous') }}", "next": "{{ __('datatables.next') }}" }
            }
        });

        // Delete function
        window.deleteFunction = function(id) {
            Swal.fire({
                title: "{{ __('hr.are_you_sure') }}",
                text: "{{ __('hr.you_wont_be_able_to_revert_this') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn btn-primary w-xs me-2 mt-2",
                cancelButtonClass: "btn btn-danger w-xs mt-2",
                confirmButtonText: "{{ __('hr.yes_delete_it') }}",
                cancelButtonText: "{{ __('hr.cancel') }}",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(result) {
                if (result.value) {
                    var url = '{{ route('hr_social_insurance.destroy', ':id') }}';
                    url = url.replace(':id', id);

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: "{{ __('hr.deleted') }}!",
                                    text: "{{ __('hr.social_insurance_deleted_success') }}",
                                    icon: "success",
                                    confirmButtonClass: "btn btn-primary w-xs mt-2",
                                    buttonsStyling: false
                                });
                                table.ajax.reload();
                            } else {
                                Swal.fire({
                                    title: "{{ __('hr.error') }}!",
                                    text: response.message || "{{ __('hr.something_went_wrong') }}",
                                    icon: "error",
                                    confirmButtonClass: "btn btn-primary w-xs mt-2",
                                    buttonsStyling: false
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "{{ __('hr.error') }}!",
                                text: xhr.responseJSON?.message || "{{ __('hr.social_insurance_deleted_error') }}",
                                icon: "error",
                                confirmButtonClass: "btn btn-primary w-xs mt-2",
                                buttonsStyling: false
                            });
                        }
                    });
                }
            });
        };

        // Search functionality
        $('#search-social-insurance-table').on('input', function() {
            table.search(this.value).draw();
        });

        // Reload table
        $('#reload-table').on('click', function() {
            table.ajax.reload();
        });

        // Export buttons
        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
    });
</script>
@endsection 