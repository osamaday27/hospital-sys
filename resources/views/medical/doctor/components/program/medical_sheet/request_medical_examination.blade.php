<div class="row">
    <!-- النصف الأول: نموذج إضافة فحص -->
    <div class="col-md-4">
        <h2>@lang('doctor_trans.add_new_test')</h2>
        <form id="testForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.test_required')</label>
                <select class="js-example-basic-single form-control" name="service_id" id="serviceDropdown">
                    <option value="">اختر الخدمة</option>
                </select>

            </div>

            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.eye')</label>
                <input type="text" name="eye" required class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.initial_diagnosis')</label>
                <input type="text" name="initial_diagnosis" required class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.clinical_info')</label>
                <input type="text" name="clinical_info" required class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.test_type')</label>
                <select name="test_type" class="form-control">
                    <option value="routine">@lang('doctor_trans.routine')</option>
                    <option value="urgent">@lang('doctor_trans.urgent')</option>
                </select>
            </div>
            <input id="id_visit" name="id_visit" value="{{ $patient_visits->id }}" type="hidden" />

            <input id="id_patient" name="id_patient" value="{{ $patient->id }}" type="hidden" />
            <button type="submit" class="btn btn-success">@lang('buttons.save')</button>
        </form>
    </div>

    <!-- النصف الثاني: جدول عرض الفحوصات -->
    <div class="col-md-8">
        <h2>@lang('doctor_trans.test_list')</h2>
        <table id="testsTable" class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>@lang('doctor_trans.request_date')</th>
                    <th>@lang('doctor_trans.requested_by')</th>
                    <th>@lang('doctor_trans.test_required')</th>
                    <th>@lang('doctor_trans.eye')</th>
                    <th>@lang('doctor_trans.test_type')</th>
                </tr>
            </thead>
            <tbody>
                <!-- البيانات يتم تحميلها عبر Ajax -->
            </tbody>
        </table>
    </div>
</div>


<script>
   $(document).ready(function() {
    $("#serviceDropdown").select2({
        placeholder: "اختر الخدمة",
        allowClear: true,
        ajax: {
            url: "{{ route('get.services') }}",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
                // إضافة الخيار الافتراضي "اختر الخدمة" في أول القائمة
                var results = [{
                    id: '',
                    text: 'اختر الخدمة', // العنصر المبدئي
                    disabled: true // منع اختياره بعد اختيارات أخرى
                }];

                // إضافة باقي الخدمات من قاعدة البيانات
                results = results.concat($.map(data, function(item) {
                    return {
                        id: item.id,
                        text: item.name // يتم جلب الاسم حسب اللغة المختارة
                    };
                }));

                return {
                    results: results
                };
            },
            cache: true
        }
    });
});

    $(document).ready(function() {
        // تهيئة DataTable لعرض الحجوزات السابقة
        // DataTables
        let table = $('#testsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('request_medical_examination.index', $patient->id) }}",
            columns: [{
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'user.name',
                    name: 'user.name'
                },
                {
                    data: 'service_id',
                    name: 'service_id'
                },
                {
                    data: 'eye',
                    name: 'eye'
                },
                {
                    data: 'test_type',
                    name: 'test_type'
                }
            ]
        });



        // معالجة الطلب عند إرسال النموذج
        $('#testForm').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('request_medical_examination.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحجز بنجاح',
                        text: response.message,
                    });
                    $('#testForm')[0].reset();
                    table.ajax.reload();
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في الإدخال',
                        text: errorMessage,
                    });
                }
            });
        });
    });
</script>
