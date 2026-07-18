@extends('layouts.master_table')

@section('title')
    {{ trans('doctor_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('doctor_trans.title') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('inpatient_settings.index') }}">{{ trans('doctor_trans.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('inpatient_trans.room') }}</li>
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
                    <div class="d-flex align-items-center">
                        <input type="search" class="form-control" id="search-nursing" placeholder="ابحث عن مريض "
                            style="width: 250px;">
                    </div>
                    <div class="hstack gap-2">
                        <a href="{{ route('inpatient_booking.create') }}"
                            class="btn btn-outline-secondary waves-effect waves-light shadow-none">
                            <i class="ri-add-line"></i> {{ trans('inpatient_trans.create_booking') }}
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
                <div id="alert-container"></div>

                <!-- Table Section -->
                <div class="card-body">
                    <table id="bed_settings-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('inpatient_trans.sr_no') }}</th>
                                <th>{{ trans('inpatient_trans.action') }}</th>
                                <th>{{ trans('inpatient_trans.days_stayed') }}</th>
                                <th>{{ trans('inpatient_trans.status') }}</th>
                                <th>{{ trans('inpatient_trans.name') }}</th>
                                <th>{{ trans('inpatient_trans.medical_number') }}</th>
                                <th>{{ trans('inpatient_trans.age') }}</th>
                                <th>{{ trans('inpatient_trans.phone') }}</th>
                                <th>{{ trans('inpatient_trans.contract') }}</th>
                                <th>{{ trans('inpatient_trans.nursing_station') }}</th>
                                <th>{{ trans('inpatient_trans.booking_date') }}</th>
                              
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
                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                    colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                                <div class="mt-4">
                                    <h4>{{ __('inpatient_trans.h4_delete') }}</h4>
                                    <p>{{ __('inpatient_trans.p_delete') }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <form action="" method="post" id="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" id="id" name="id">
                                    <button type="button" class="btn btn-light"
                                        data-bs-dismiss="modal">{{ __('inpatient_trans.close') }}</button>
                                    <button type="button" class="btn btn-danger"
                                        id="delete-record">{{ __('inpatient_trans.yes_delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            var table = $('#bed_settings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('getbookingsdoctor') }}",
                    data: function(d) {}
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'days_stayed',
                        name: 'days_stayed'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'patient_name',
                        name: 'patient_name'
                    },
                    {
                        data: 'medical_number',
                        name: 'medical_number'
                    },
                    {
                        data: 'age',
                        name: 'age'
                    },
                    {
                        data: 'patient_phone',
                        name: 'patient_phone'
                    },
                    {
                        data: 'contract',
                        name: 'contract'
                    },
                  
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'dates',
                        name: 'dates'
                    },

                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        title: 'rooms Data',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-outline-primary'
                    },
                    {
                        extend: 'excel',
                        title: 'ROOM Data',
                        text: '<i class="fas fa-file-excel"></i> XLSX',
                        className: 'btn btn-outline-success'
                    }
                ],
                language: {
                    lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                    info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                    processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
                    paginate: {
                        "previous": "{{ __('datatables.previous') }}",
                        "next": "{{ __('datatables.next') }}"
                    }
                }
            });

            $('#reload-table').click(function() {
                table.ajax.reload();
            });

            $('#search-nursing').on('input', function() {
                table.search(this.value).draw();
            });

            $('#export-csv').click(function() {
                table.button('.buttons-csv').trigger();
            });
            $('#export-xlsx').click(function() {
                table.button('.buttons-excel').trigger();
            });
        });

        function deleteFunction(id) {
            $('#id').val(id);
            $('#deleteModal').modal('show');
        }

        $('#delete-record').click(function() {
            var id = $('#id').val();
            var url = '{{ route('bed_settings.destroy', ':id') }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    $('#bed_settings-table').DataTable().ajax.reload();
                    if (response.status == 'success') {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert("There was an error deleting the record.");
                }
            });
        });

        function showAlert(type, message) {
            var alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';
            var alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show shadow" role="alert">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2">
                        <i class="ri-${type === 'success' ? 'check-line' : 'alert-line'} align-middle"></i>
                    </div>
                    <div class="flex-grow-1">
                        ${message}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>`;
            $('#alert-container').append(alertHtml);
            setTimeout(function() {
                $('#alert-container .alert').first().alert('close');
            }, 15000);
        }
    </script>
@endsection
