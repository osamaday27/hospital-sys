@extends('layouts.master_table')

@section('title')
    {{ trans('hr.attendance_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.attendance_title') }}</a>
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
                        <input type="search" class="form-control" id="search-attendance-table"
                            placeholder="{{ trans('hr.attendance_search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Reload Button -->
                         <a  href="{{ url('/' . ($page = 'hr_attendance/create')) }}"  class="btn btn-warning">
                            <i class="ri-user-add-line"></i>
                        </a>
                        <!-- Upload Button -->
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="ri-upload-2-line"></i> {{ trans('hr.upload') }}
                        </button>
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

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('upload_errors'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h6>{{ trans('hr.upload_errors') }}:</h6>
                        <ul class="mb-0">
                            @foreach(session('upload_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- jobs Table -->
                <div class="card-body">
                    <table id="attendance-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.employee') }}</th>
                                <th>{{ trans('hr.employee_code') }}</th>
                                <th>{{ trans('hr.section') }}</th>
                                <th>{{ trans('hr.schedule') }}</th>
                                <th>{{ trans('hr.schedule_start') }}</th>
                                <th>{{ trans('hr.schedule_end') }}</th>
                                <th>{{ trans('hr.check_in') }}</th>
                                <th>{{ trans('hr.check_out') }}</th>
                                <th>{{ trans('hr.worked_hours') }}</th>
                                <th>{{ trans('hr.shortage') }}</th>
                                <th>{{ trans('hr.penalty') }}</th>
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

                <!-- Upload Modal -->
                <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ trans('hr.upload_attendance') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('hr_attendance.upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="excel_file" class="form-label">{{ trans('hr.select_excel_file') }}</label>
                                        <input type="file" class="form-control" id="excel_file" name="excel_file" 
                                               accept=".xlsx,.xls,.csv" required>
                                        <div class="form-text">{{ trans('hr.excel_file_help') }}</div>
                                    </div>
                                    <div class="alert alert-info">
                                        <h6>{{ trans('hr.required_columns') }}:</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>{{ trans('hr.employee_code') }}:</strong><br>
                                                <small>employee_code, emp_code, code, employee</small>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>{{ trans('hr.check_in') }}:</strong><br>
                                                <small>check_in, time_in, start_time, arrival</small>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>{{ trans('hr.check_out') }}:</strong><br>
                                                <small>check_out, time_out, end_time, departure</small>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <h6>{{ trans('hr.supported_date_formats') }}:</h6>
                                        <small>
                                            <strong>{{ trans('hr.date_examples') }}:</strong><br>
                                            • 2024-01-15 08:30:00 &nbsp;• 15/01/2024 08:30 &nbsp;• 01/15/2024 8:30 AM<br>
                                            • 15-01-2024 08:30 &nbsp;• Excel date numbers &nbsp;• And many more formats
                                        </small>
                                    </div>
                                    <div class="alert alert-warning">
                                        <small>{{ trans('hr.upload_warning') }}</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('hr.cancel') }}</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-upload-2-line"></i> {{ trans('hr.upload') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
    $(function() {
        var table = $('#attendance-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ url('get-attendance') }}",
                data: function(d) {}
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'employee', name: 'employee' },
                { data: 'employee_code', name: 'employee_code' },
                { data: 'section', name: 'section' },
                { data: 'schedule_id', name: 'schedule_id' },
                { data: 'schedule_start', name: 'schedule_start' },
                { data: 'schedule_end', name: 'schedule_end' },
                { data: 'check_in', name: 'check_in' },
                { data: 'check_out', name: 'check_out' },
                { data: 'worked_hours', name: 'worked_hours' },
                { data: 'shortage', name: 'shortage' },
                { data: 'penalty', name: 'penalty' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Attendance Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Attendance Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
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

        $('#search-attendance-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#export-pdf').click(function() { table.button('.buttons-pdf').trigger(); });
        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
        $('#export-print').click(function() { table.button('.buttons-print').trigger(); });

        // Inline editing for penalty
        $('#attendance-table').on('blur', '.penalty-badge', function() {
            const id = $(this).data('id');
            const newPenalty = parseFloat($(this).text().replace(' days', ''));
            
            if (isNaN(newPenalty) || newPenalty < 0 || newPenalty > 10) {
                alert('Please enter a valid penalty value between 0 and 10');
                table.ajax.reload();
                return;
            }

            $.ajax({
                url: '/hr_attendance/' + id + '/update-penalty',
                type: 'POST',
                data: {
                    penalty: newPenalty,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Update the badge color based on penalty value
                        const badge = $('.penalty-badge[data-id="' + id + '"]');
                        if (newPenalty > 0) {
                            badge.removeClass('bg-success').addClass('bg-danger');
                        } else {
                            badge.removeClass('bg-danger').addClass('bg-success');
                        }
                    }
                },
                error: function() {
                    alert('Error updating penalty');
                    table.ajax.reload();
                }
            });
        });

        // Handle Enter key for penalty editing
        $('#attendance-table').on('keydown', '.penalty-badge', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $(this).blur();
            }
        });
    });

    function deleteFunction(id) {
        $('#id').val(id); // وضع المعرف في حقل الإدخال المخفي
        $('#deleteModal').modal('show'); // فتح النافذة المنبثقة
    }

    $('#delete-record').click(function() {
        var id = $('#id').val(); // الحصول على ID من الحقل المخفي
        var url = '{{ route('hr_attendance.destroy', ':id') }}';
        url = url.replace(':id', id); // استبدال الـ :id بالـ ID الفعلي

        // إرسال طلب DELETE باستخدام AJAX
        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "_token": "{{ csrf_token() }}", // توكن CSRF للأمان
            },
            success: function(response) {
                $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة
                $('#attendance-table').DataTable().ajax.reload(); // إعادة تحميل الـ DataTable
                // Show success message
                if (response && response.message) {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                if (xhr.status === 419) {
                    alert("CSRF token mismatch. Please refresh the page and try again.");
                } else {
                    alert("There was an error deleting the record.");
                }
            }
        });
    });
</script>
@endsection
