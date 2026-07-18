<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-xl-12">
                    <div class="search-box">

                        <input type="text" class="form-control search" name="search_for_patient"
                            placeholder="{{ trans('outpatient_trans.search_for_patient') }}">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>
                <!--end col-->
                <div class="col-xl-12">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div>
                                <button type="button" class="btn btn-primary w-100" onclick="SearchData();">
                                    <i
                                        class="ri-equalizer-fill me-2 align-bottom"></i>{{ trans('outpatient_trans.search_patient') }}
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div>
                                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                                    data-bs-target=".bs-example-modal-xl">
                                    <i class="ri-user-add-line  me-2 align-bottom"></i>
                                    {{ trans('outpatient_trans.create_patient') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive table-card">
                <table class="table table-borderless table-nowrap align-middle mb-0" id="patient-table">
                    <thead class="table-light text-muted">
                        <tr>
                            <th scope="col">{{ trans('outpatient_trans.patient_data') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <!-- end tbody -->
                </table><!-- end table -->
            </div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-xl-12">
                    <div class="search-box">
                        <input type="text" class="form-control search" name="search_for_patient_visit"
                            placeholder="{{ trans('inpatient_trans.search_for_patient_visit') }}">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>
                <!--end col-->
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive table-card">
                <table class="table table-borderless table-nowrap align-middle mb-0" id="patient_visit-table">
                    <thead class="table-light text-muted">
                        <tr>
                            <th scope="col">{{ trans('inpatient_trans.visit_date') }}</th>
                            <th scope="col">{{ trans('inpatient_trans.visit_type') }}</th>
                            <th scope="col">{{ trans('inpatient_trans.visit_status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <!-- end tbody -->
                </table><!-- end table -->
            </div>
        </div>
    </div>
</div>


<script>
    // تهيئة DataTable للمرضى
    $('#patient-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('get-patient') }}",
            data: function(d) {
                d.search_term = $('.search-box input[name="search_for_patient"]').val(); // تمرير قيمة البحث
            }
        },
        columns: [{
            data: 'patient_info',
            name: 'patient_info'
        }],
        dom: 'Bfrtip',
        pageLength: 4,
        language: {
            emptyTable: `
            <div class="py-4 text-center">
                <lord-icon src="{{ URL::asset('assets/images/icon/msoeawqm.json') }}" 
                    trigger="loop" colors="primary:#405189,secondary:#0ab39c" 
                    style="width:72px;height:72px">
                </lord-icon>
                <h5 class="mt-4">{{ trans('outpatient_trans.sorry_no_result_found') }}</h5>
            </div>`,
            lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
            info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
            processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
            paginate: {
                previous: "{{ __('datatables.previous') }}",
                next: "{{ __('datatables.next') }}"
            }
        }
    });


    // وظيفة البحث
    function SearchData() {
        $('#patient-table').DataTable().ajax.reload();
    }

    // البحث عند الضغط على Enter
    $('.search-box input[name="search_for_patient"]').on('keypress', function(event) {
        if (event.which === 13) { // إذا تم الضغط على مفتاح Enter
            SearchData();
        }
    });

    // عرض تفاصيل المريض
    function showpatient(name, contract_name, gender, image, medical_number, nationality, date_of_birth, age,
        phone_number, phone_number2, whatsapp, contract_id, patient_id) {
        $.ajax({
            url: '../administrative.inpatient.components.program.header_data_patient',
            method: 'GET',
            success: function(response) {
                document.getElementById("patient-details").innerHTML = response;
                document.getElementById("patient-name").innerText = name;
                document.getElementById("contract_name").innerText = contract_name;
                document.getElementById("medical_number").innerText = medical_number;
                document.getElementById("nationality").innerText = nationality;
                document.getElementById("date_of_birth").innerText = date_of_birth;
                document.getElementById("age").innerText = age;
                document.getElementById("phone_number").innerText = phone_number;
                document.getElementById("phone_number2").innerText = phone_number2;
                document.getElementById("whatsapp").innerText = whatsapp;
                document.getElementById("patient-image").src = image;
                document.getElementById("contract_id").value = contract_id;
                document.getElementById("patient_id").value = patient_id;
                showvisit(patient_id);

                let maleIcon = document.getElementById("gender_male");
                let femaleIcon = document.getElementById("gender_female");
                if (gender.toLowerCase() === '1') {
                    maleIcon.style.display = "inline-block";
                    femaleIcon.style.display = "none";
                } else {
                    femaleIcon.style.display = "inline-block";
                    maleIcon.style.display = "none";
                }

                // Display the patient details section
                document.getElementById("patient-details").style.display = "block";
                document.getElementById("header_view").style.display = "none";
            },
            error: function() {
                alert('Failed to load patient details.');
            }
        });
    }
    var patientVisitTable;

    function showvisit(id) {
        // تدمير الجدول الحالي إذا كان موجودًا
        if ($.fn.dataTable.isDataTable('#patient_visit-table')) {
            patientVisitTable.destroy();
        }

        // تهيئة الجدول مع بيانات المريض الجديد
        patientVisitTable = $('#patient_visit-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('get_patient_visit') }}",
                data: function(d) {
                    d.patient_id = id || null; // إرسال ID المريض الحالي
                    d.search_term = $('.search-box input[name="search_for_patient_visit"]')
                        .val(); // إرسال مصطلح البحث إذا كان موجودًا
                }
            },
            columns: [{
                    data: 'visit_date',
                    name: 'visit_date'
                },
                {
                    data: 'visit_type',
                    name: 'visit_type'
                },
                {
                    data: 'visit_status',
                    name: 'visit_status'
                }
            ],
            dom: 'Bfrtip',
            pageLength: 4,
            language: {
                emptyTable: `
                <div class="py-4 text-center">
                    <lord-icon src="{{ URL::asset('assets/images/icon/msoeawqm.json') }}" 
                        trigger="loop" colors="primary:#405189,secondary:#0ab39c" 
                        style="width:72px;height:72px">
                    </lord-icon>
                    <h5 class="mt-4">{{ trans('outpatient_trans.sorry_no_result_found') }}</h5>
                </div>`,
                lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
                paginate: {
                    previous: "{{ __('datatables.previous') }}",
                    next: "{{ __('datatables.next') }}"
                }
            }
        });

        // Apply search filter
        $('.search-box input[name="search_for_patient_visit"]').on('input', function() {
            patientVisitTable.search(this.value).draw();
        });
    }
</script>
