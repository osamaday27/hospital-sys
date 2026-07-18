@extends('layouts.master_table')

@section('title')
    {{ trans('hr.schedules_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.schedules_title') }}</a>
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
                        <input type="search" class="form-control" id="search-schedules-table"
                            placeholder="{{ trans('hr.schedules_search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Reload Button -->
                         <a  href="{{ url('/' . ($page = 'hr_schedules/create')) }}"  class="btn btn-warning">
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
                <!-- jobs Table -->
                <div class="card-body">
                    <table id="schedules-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.schedule_name') }}</th>
                                <th>{{ trans('hr.description') }}</th>
                                <th>{{ trans('hr.is_active') }}</th>
                                <th>{{ trans('hr.total_hours') }}</th>
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
        var table = $('#schedules-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ url('get-schedules') }}",
                data: function(d) {}
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'is_active', name: 'is_active' },
                { data: 'total_hours', name: 'total_hours' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Schedules Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Schedules Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
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

        $('#search-schedules-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#export-pdf').click(function() { table.button('.buttons-pdf').trigger(); });
        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
        $('#export-print').click(function() { table.button('.buttons-print').trigger(); });
    });

    function deleteFunction(id) {
        $('#id').val(id); // وضع المعرف في حقل الإدخال المخفي
        $('#deleteModal').modal('show'); // فتح النافذة المنبثقة
    }

    $('#delete-record').click(function() {
        var id = $('#id').val(); // الحصول على ID من الحقل المخفي
        var url = '{{ route('hr_schedules.destroy', ':id') }}';
        url = url.replace(':id', id); // استبدال الـ :id بالـ ID الفعلي

        // إرسال طلب DELETE باستخدام AJAX
        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                "_token": "{{ csrf_token() }}", // توكن CSRF للأمان
            },
            success: function(response) {
                $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة
                $('#schedules-table').DataTable().ajax.reload(); // إعادة تحميل الـ DataTable
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert("There was an error deleting the record.");
            }
        });
    });
</script>
@endsection
