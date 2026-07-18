<div class="tab-pane fade" id="doctor_order" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <!-- Base Examples -->
            <div class="hstack flex-wrap gap-2">
                @include('medical.doctor.components.program.doctor_order.btn_tab')
            </div>
            @include('medical.doctor.components.program.doctor_order.lab')
            @include('medical.doctor.components.program.doctor_order.rad')

            <form id="form-medical-file" class="mt-4">
                @csrf
                <div class="row">
                    <!-- Allergies Table -->
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ $patient->id }}">
                    <input type="hidden" name="patient_visits_id" id="patient_visits_id"
                        value="{{ $patient_visits->id }}">
                    <div class="col-md-6 mb-3">
                        <div class="card card-lab">
                            <div class="card-body">
                                <h6 class="fw-semibold text-primary">{{ trans('doctor_trans.lab') }}</h6>
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="text" id="search-lab" class="form-control mb-2"
                                                placeholder="بحث...">
                                        </div>

                                    </div>
                                    <table id="lab-table"
                                        class="table table-striped table-bordered dt-responsive nowrap lab-table"
                                        style="width: 100%; height: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="max-width: 20px;"></th>
                                                <th>{{ trans('doctor_trans.lab') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- medical History Table -->
                    <div class="col-md-6 mb-3">
                        <div class="card card-rad">
                            <div class="card-body">
                                <h6 class="fw-semibold text-primary">{{ trans('doctor_trans.rad') }}</h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" id="search-rad" class="form-control mb-2"
                                            placeholder="بحث...">
                                    </div>

                                </div>
                                <table id="rad-table"
                                    class="table table-striped table-bordered dt-responsive nowrap rad-table"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="max-width: 20px;"></th>
                                            <th>{{ trans('doctor_trans.rad') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-success btn-lg px-4"
                        id="save-doctor_order">{{ trans('doctor_trans.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        function initializeDataTable(tableId, url, searchInputId) {
            let table = $(tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: url,
                lengthChange: false,
                paging: true,
                columnDefs: [{
                    targets: [0], // For the checkbox column
                    orderable: false,
                    className: 'text-center'
                }],
                columns: [{
                        data: 'id',
                        render: function(data) {
                            return '<input type="checkbox" class="select-row" value="' + data +
                                '">';
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    }
                ]
            });

            $(searchInputId).on('keyup', function() {
                table.search(this.value).draw();
            });

            $(tableId + ' tbody').on('click', 'tr', function() {
                let checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked'));
            });

            return table;
        }

        // Initialize DataTables for each category
        let allergiesTable = initializeDataTable('#lab-table', "{{ url('get_lab') }}", '#search-lab');
        let medicalHistoryTable = initializeDataTable('#rad-table', "{{ url('get_rad') }}", '#search-rad');

        $('#save-doctor_order').click(function() {
            // Collect selected data
            let selectedlab = [];
            let selectedrad = [];
            $('#lab-table .select-row:checked').each(function() {
                selectedlab.push($(this).val());
            });
            $('#rad-table .select-row:checked').each(function() {
                selectedrad.push($(this).val());
            });

            // Disable the button and show a loading message (optional)
            $('#save-doctor_order').prop('disabled', true).text('Saving...');

            // AJAX request
            $.ajax({
                url: "{{ url('save_doctor_order') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id_patient: $('#patient_id').val(),
                    id_visit: $('#patient_visits_id').val(),
                    lab: selectedlab,
                    rad: selectedrad,
                },
                success: function(response) {
                    $('#save-doctor_order').prop('disabled', false).text('Save');
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت الإضافة بنجاح!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                },
                error: function() {
                    $('#save-doctor_order').prop('disabled', false).text('Save');
                    Swal.fire({
                        icon: 'error',
                        title: 'حدث خطأ!',
                        text: 'يرجى التحقق من البيانات والمحاولة مجدداً.'
                    });
                }
            });
        });
    });
</script>
<style>
    /* Custom Colors for Cards */
    .card-lab {
        background-color: #d4edda;
        /* Light Red */
    }

    .card-rad {
        background-color: #d1ecf1;
        /* Light Blue */
    }

    .card-examination {
        background-color: #d4edda;
        /* Light Green */
    }

    .card-complaints {
        background-color: #fff3cd;
        /* Light Yellow */
    }

    /* Custom styles for card */
    .custom-card {
        background-color: #f0f8ff;
        /* Light Blue */
        min-height: 600px;
        /* Set the height to your desired value */
    }
</style>
