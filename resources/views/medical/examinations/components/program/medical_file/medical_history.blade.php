<div class="tab-pane fade" id="medical_history" role="tabpanel">
    <div class="card shadow-sm border-0 ">
        <div class="card-body">
            <div class="hstack flex-wrap gap-2">
                @include('medical.doctor.components.program.history_data.btn_tab')
                <div class="text-end">
                    <button type="button" class="btn btn-success px-4"
                        id="save-medical-data">{{ trans('doctor_trans.save') }}</button>
                </div>
            </div>
            @include('medical.doctor.components.program.history_data.history_allergies')
            @include('medical.doctor.components.program.history_data.history_complain')
            @include('medical.doctor.components.program.history_data.history_surgeries')
            @include('medical.doctor.components.program.history_data.history_finding')
            @include('medical.doctor.components.program.history_data.history_medical_history')

            <form id="form-medical-file" class="mt-4">
                @csrf
                <div class="row">
                    <!-- Allergies Table -->
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ $patient->id }}">
                    <input type="hidden" name="patient_visits_id" id="patient_visits_id"
                        value="{{ $patient_visits->id }}">
                    <div class="col-md-6 mb-3">
                        <div class="card card-allergies">
                            <div class="card-body">
                                <h6 class="fw-semibold text-primary">{{ trans('doctor_trans.allergies') }}</h6>
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" id="search-allergies" class="form-control mb-2"
                                                placeholder="بحث...">
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="enter_allergies" name="enter_allergies"
                                                class="form-control mb-2"
                                                placeholder="{{ trans('doctor_trans.enter_allergies') }}">
                                        </div>
                                    </div>
                                    <table id="allergies-table"
                                        class="table table-striped table-bordered dt-responsive nowrap allergies-table"
                                        style="width: 100%; height: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="max-width: 20px;"></th>
                                                <th>{{ trans('doctor_trans.allergies') }}</th>
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
                        <div class="card card-medical-history">
                            <div class="card-body">
                                <h6 class="fw-semibold text-primary">{{ trans('doctor_trans.medical_history') }}</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" id="search-medical-history" class="form-control mb-2"
                                            placeholder="بحث...">
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" id="enter_medical_history" name="enter_medical_history"
                                            class="form-control mb-2"
                                            placeholder="{{ trans('doctor_trans.enter_medical_history') }}">
                                    </div>
                                </div>
                                <table id="medical-history-table"
                                    class="table table-striped table-bordered dt-responsive nowrap medical-history-table"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="max-width: 20px;"></th>
                                            <th>{{ trans('doctor_trans.medical_history') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- medical History Table -->
                    <div class="col-md-6 mb-3">
                        <div class="card card-family_history">
                            <div class="card-body">
                                <h6 class="fw-semibold text-primary">{{ trans('doctor_trans.family_history') }}</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" id="search-family_history" class="form-control mb-2"
                                            placeholder="بحث...">
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" id="enter_family_history" name="enter_family_history"
                                            class="form-control mb-2"
                                            placeholder="{{ trans('doctor_trans.enter_family_history') }}">
                                    </div>
                                </div>
                                <table id="family_history-table"
                                    class="table table-striped table-bordered dt-responsive nowrap family_history-table"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="max-width: 20px;"></th>
                                            <th>{{ trans('doctor_trans.family_history') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-success px-4"
                        id="save-medical-data2">{{ trans('doctor_trans.save') }}</button>
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
        let allergiesTable = initializeDataTable('#allergies-table', "{{ url('setting_allergies') }}",
            '#search-allergies');
        let medicalHistoryTable = initializeDataTable('#medical-history-table',
            "{{ url('setting_medical_history') }}", '#search-medical-history');
        let family_historyTable = initializeDataTable('#family_history-table',
            "{{ url('setting_medical_history') }}", '#search-setting_medical_history');

        $('#save-medical-data, #save-medical-data2').click(function() {
            // Collect selected data
            let selectedAllergies = [];
            let selectedmedicalHistory = [];

            $('#allergies-table .select-row:checked').each(function() {
                selectedAllergies.push($(this).val());
            });

            $('#medical-history-table .select-row:checked').each(function() {
                selectedmedicalHistory.push($(this).val());
            });
            $('#family_history-table .select-row:checked').each(function() {
                selectedfamily_history.push($(this).val());
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
                    allergies: selectedAllergies,
                    medical_history: selectedmedicalHistory,
                    family_history: selectedfamily_history,

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
