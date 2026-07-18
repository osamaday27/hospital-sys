@extends('layouts.master_table')

@section('title')
{{ trans('hr.employees_management') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.employees_management') }}</a>
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
                        <input type="search" class="form-control" id="search-employees-table"
                            placeholder="{{ trans('hr.employee_search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Reload Button -->
                         <a  href="{{ url('/' . ($page = 'employees/create')) }}"  class="btn btn-warning">
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
                        <button class="btn btn-info" id="generate-acknowledgments">
                            <i class="fas fa-file-word"></i> {{ trans('hr.generate_acknowledgments') }}
                        </button>
                    </div>
                </div>
                <!-- jobs Table -->
                <div class="card-body">
                    <table id="employees-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.employee_code') }}</th>
                                <th>{{ trans('hr.name') }}</th>
                                <th>{{ trans('hr.phone') }}</th>
                                <th>{{ trans('hr.email') }}</th>
                                <th>{{ trans('hr.department') }}</th>
                                <th>{{ trans('hr.job_position') }}</th>
                                <th>{{ trans('hr.schedule') }}</th>
                                <th>{{ trans('hr.hiring_date') }}</th>
                                <th>{{ trans('hr.has_valid_contract') }}</th>
                                <th>{{ trans('hr.employee_status') }}</th>
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

                <!-- Acknowledgment Generation Modal -->
                <div class="modal fade" id="acknowledgmentModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ trans('hr.generate_acknowledgments') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="acknowledgment-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="employee_id" class="form-label">{{ trans('hr.select_employee') }}</label>
                                                <select name="employee_id" id="employee_id" class="form-control select2" required>
                                                    <option value="">{{ trans('hr.choose_employee') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="acknowledgment_type" class="form-label">{{ trans('hr.acknowledgment_type') }}</label>
                                                <select name="acknowledgment_type" id="acknowledgment_type" class="form-control" required>
                                                    <option value="policy_acknowledgment">{{ trans('hr.policy_acknowledgment') }}</option>
                                                    <option value="ethics_confidentiality_acknowledgment">{{ trans('hr.ethics_confidentiality_acknowledgment') }}</option>
                                                    <option value="qualifications_verification_request">{{ trans('hr.qualifications_verification_request') }}</option>
                                                    <option value="departmental_job_orientation">{{ trans('hr.departmental_job_orientation') }}</option>
                                                    <option value="new_employee_general_orientation">{{ trans('hr.new_employee_general_orientation') }}</option>
                                                    <option value="outsourced_employee_orientation">{{ trans('hr.outsourced_employee_orientation') }}</option>
                                                    <option value="training_needs_assessment">{{ trans('hr.training_needs_assessment') }}</option>
                                                    <option value="annual_training_plan">{{ trans('hr.annual_training_plan') }}</option>
                                                    <option value="training_course_effectiveness">{{ trans('hr.training_course_effectiveness') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="format" class="form-label">{{ trans('hr.file_format') }}</label>
                                                <select name="format" id="format" class="form-control">
                                                    <option value="pdf">PDF</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="language" class="form-label">{{ trans('hr.language') }}</label>
                                                <select name="language" id="language" class="form-control">
                                                    <option value="ar">العربية</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Employee Preview -->
                                    <div id="employee-preview" class="mt-3" style="display: none;">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>{{ trans('hr.employee_preview') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>{{ trans('hr.name') }}:</strong>
                                                        <span id="preview-name">-</span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>{{ trans('hr.employee_code') }}:</strong>
                                                        <span id="preview-code">-</span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>{{ trans('hr.job_position') }}:</strong>
                                                        <span id="preview-position">-</span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>{{ trans('hr.department') }}:</strong>
                                                        <span id="preview-department">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('hr.close') }}</button>
                                <button type="button" class="btn btn-primary" id="generate-acknowledgment-btn">
                                    <i class="fas fa-download"></i> {{ trans('hr.generate_and_download') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
    $(function() {
        var table = $('#employees-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ url('get-employees') }}",
                data: function(d) {}
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'employee_code', name: 'employee_code' },
                { data: 'full_name', name: 'full_name' },
                { data: 'phone', name: 'phone' },
                { data: 'email', name: 'email' },
                { data: 'emp_dep', name: 'emp_dep' },
                { data: 'emp_pos', name: 'emp_pos' },
                { data: 'schedule', name: 'schedule' },
                { data: 'hiring_date', name: 'hiring_date' },
                { data: 'has_valid_contract', name: 'has_valid_contract' },
                { data: 'employee_status', name: 'employee_status' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Employees Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Employees Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
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

        $('#search-employees-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#export-pdf').click(function() { table.button('.buttons-pdf').trigger(); });
        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
        $('#export-print').click(function() { table.button('.buttons-print').trigger(); });

        // Acknowledgment Generation Modal
        $('#generate-acknowledgments').click(function() {
            loadEmployees();
            $('#acknowledgmentModal').modal('show');
        });

        // Load employees for the dropdown
        function loadEmployees() {
            $.ajax({
                url: "{{ url('get-employees') }}",
                type: 'GET',
                data: { 
                    draw: 1,
                    start: 0,
                    length: -1 // Get all employees
                },
                success: function(response) {
                    var employees = response.data;
                    var select = $('#employee_id');
                    select.empty();
                    select.append('<option value="">{{ trans("hr.choose_employee") }}</option>');
                    
                    employees.forEach(function(employee) {
                        select.append('<option value="' + employee.id + '" ' +
                            'data-name="' + employee.full_name + '" ' +
                            'data-code="' + employee.employee_code + '" ' +
                            'data-position="' + (employee.emp_pos || '') + '" ' +
                            'data-department="' + (employee.emp_dep || '') + '">' +
                            employee.full_name + ' - ' + employee.employee_code +
                            '</option>');
                    });
                    
                    // Initialize Select2
                    select.select2({
                        placeholder: '{{ trans("hr.choose_employee") }}',
                        allowClear: true,
                        width: '100%'
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error loading employees:', error);
                    alert('{{ trans("hr.error_loading_employees") }}');
                }
            });
        }

        // Update employee preview when employee is selected
        $('#employee_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                $('#preview-name').text(selectedOption.data('name'));
                $('#preview-code').text(selectedOption.data('code'));
                $('#preview-position').text(selectedOption.data('position'));
                $('#preview-department').text(selectedOption.data('department'));
                $('#employee-preview').show();
            } else {
                $('#employee-preview').hide();
            }
        });

        // Generate acknowledgment
        $('#generate-acknowledgment-btn').click(function() {
            const form = $('#acknowledgment-form');
            const formData = form.serialize();
            
            // Validate form
            if (!$('#employee_id').val()) {
                alert('{{ trans("hr.please_select_employee") }}');
                return;
            }
            if (!$('#acknowledgment_type').val()) {
                alert('{{ trans("hr.please_select_acknowledgment_type") }}');
                return;
            }

            // Show loading
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ trans("hr.generating") }}...');

            // Generate the document
            $.ajax({
                url: "{{ route('acknowledgments.generate') }}",
                type: 'POST',
                data: formData + '&_token=' + '{{ csrf_token() }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data, status, xhr) {
                    // Open the HTML in a new window for printing
                    const newWindow = window.open('', '_blank');
                    newWindow.document.write(data);
                    newWindow.document.close();
                    
                    // Auto-print after a short delay
                    setTimeout(function() {
                        newWindow.print();
                    }, 500);
                    
                    // Close modal and reset form
                    $('#acknowledgmentModal').modal('hide');
                    form[0].reset();
                    $('#employee-preview').hide();
                },
                error: function(xhr, status, error) {
                    alert('{{ trans("hr.error_generating_document") }}: ' + error);
                },
                complete: function() {
                    // Reset button
                    $('#generate-acknowledgment-btn').prop('disabled', false)
                        .html('<i class="fas fa-download"></i> {{ trans("hr.generate_and_download") }}');
                }
            });
        });
    });

    const currentLocale = '{{ app()->getLocale() }}';

    function deleteFunction(id) {
        $('#id').val(id); // وضع المعرف في حقل الإدخال المخفي
    var action = '{{ route('employees.destroy', ['employee' => '__ID__']) }}'.replace('__ID__', id);
    $('#delete-form').attr('action', action);
        $('#deleteModal').modal('show'); // فتح النافذة المنبثقة
    }

    $('#delete-record').click(function() {
        var id = $('#id').val(); // الحصول على ID من الحقل المخفي
        var url = $('#delete-form').attr('action');
        if(!url || /\/employees\/?$/.test(url)) {
            url = `${window.location.origin}/${currentLocale}/employees/${id}`;
        }

        // إرسال طلب DELETE باستخدام AJAX
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}", // توكن CSRF للأمان
                "_method": "DELETE"
            },
            success: function(response) {
                $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة
                $('#employees-table').DataTable().ajax.reload(); // إعادة تحميل الـ DataTable
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert("There was an error deleting the record.");
            }
        });
    });
</script>
@endsection
