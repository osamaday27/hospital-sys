<div class="tab-pane fade" id="complaints_diagnosis" role="tabpanel">
    <div class="card shadow-sm border-0 ">
        <div class="card-body">
            <div class="hstack flex-wrap gap-2">
                @include('medical.doctor.components.program.history_data.btn_tab')
                <div class="text-end">
                    <button type="button" class="btn btn-success px-4" id="save-medical-data">{{ trans('doctor_trans.save') }}</button>
                </div>
            </div>
            @include('medical.doctor.components.program.history_data.history_complain')
            @include('medical.doctor.components.program.history_data.history_finding')

            <form id="form-medical-file" class="mt-4">
                @csrf
                <div class="row">
                    <!-- Allergies Table -->
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ $patient->id }}">
                    <input type="hidden" name="patient_visits_id" id="patient_visits_id" value="{{ $patient_visits->id }}">
                    <!-- Examination Table -->
                    <div class="col-md-6 mb-3">
                        <div class="card card-examination">
                            <div class="card-body">
                                <h6 class="fw-semibold text-primary">{{ trans('doctor_trans.examination') }}</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" id="search-examination" class="form-control mb-2" placeholder="بحث...">
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" id="enter_examination" name="enter_examination" class="form-control mb-2" placeholder="{{ trans('doctor_trans.enter_examination') }}">
                                    </div>
                                </div>
                                <table id="examination-table" class="table table-striped table-bordered dt-responsive nowrap examination-table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="max-width: 20px;"></th>
                                            <th>{{ trans('doctor_trans.examination') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Complaints Table -->
                    <div class="col-md-6 mb-3">
                        <div class="card card-complaints">
                            <div class="card-body">
                                <h6 class="fw-semibold text-primary">{{ trans('doctor_trans.complaining') }}</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" id="search-medicalcomplaints" class="form-control mb-2" placeholder="بحث...">
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" id="enter_complaints" name="enter_complaints" class="form-control mb-2" placeholder="{{ trans('doctor_trans.enter_complaints') }}">
                                    </div>
                                </div>
                                <table id="medicalcomplaints-table" class="table table-striped table-bordered dt-responsive nowrap complaints-table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="max-width: 20px;"></th>
                                            <th>{{ trans('doctor_trans.complaining') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-success px-4" id="save-medical-data2">{{ trans('doctor_trans.save') }}</button>
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
                            return '<input type="checkbox" class="select-row" value="' + data + '">';
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
          let medicalcomplaintsTable = initializeDataTable('#medicalcomplaints-table', "{{ url('setting_complaining') }}", '#search-medicalcomplaints');
        let examinationTable = initializeDataTable('#examination-table', "{{ url('setting_examination') }}", '#search-examination');

        $('#save-medical-data, #save-medical-data2').click(function() {
            // Collect selected data
      
            let selectedComplaints = [];
            let selectedExaminations = [];

         

            $('#medicalcomplaints-table .select-row:checked').each(function() {
                selectedComplaints.push($(this).val());
            });

            $('#examination-table .select-row:checked').each(function() {
                selectedExaminations.push($(this).val());
            });

            // Disable the button and show a loading message (optional)
            $('#save-medical-data').prop('disabled', true).text('Saving...');

            // AJAX request
            $.ajax({
                url: "{{ url('save_general_examination') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id_patient: $('#patient_id').val(),
                    id_visit: $('#patient_visits_id').val(),
                    complaints: selectedComplaints,
                    examination: selectedExaminations
                },
                success: function(response) {
                    $('#save-medical-data').prop('disabled', false).text('Save');
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت الإضافة بنجاح!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                },
                error: function() {
                    $('#save-medical-data').prop('disabled', false).text('Save');
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
    .card-allergies {
        background-color: #f8d7da;
        /* Light Red */
    }

    .card-medical-history {
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