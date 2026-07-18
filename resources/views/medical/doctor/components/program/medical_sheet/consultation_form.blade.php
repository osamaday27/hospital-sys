<div class="row">
    <!-- النصف الأول: نموذج إضافة فحص -->
    <div class="col-md-4">
        <h2>@lang('doctor_trans.consultation_form')</h2>
        <form id="testForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.type_consultation')</label>
                <select class="js-example-basic-single form-control" name="type_consultation" id="type_consultation">
                    <option value="consultation">Consultation</option>
                    <option value="comanagement">Co-management</option>
                    <option value="transfer_your_service">Transfer to your service</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.urgency_consultation')</label>
                <select name="test_type" class="form-control">
                    <option value="routine">@lang('doctor_trans.routine')</option>
                    <option value="urgent">@lang('doctor_trans.urgent')</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.specialty')</label>
                <select class="js-example-basic-single form-control" name="specialty" id="specialtyDropdown">
                    <option value="">اختر التخصص</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.reason_consultation')</label>
                <input type="text" name="reason_consultation" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">@lang('doctor_trans.clinical_details')</label>
                <input type="text" name="clinical_details" required class="form-control">
            </div>

            <input id="id_visit" name="id_visit" value="{{ $patient_visits->id }}" type="hidden" />

            <input id="id_patient" name="id_patient" value="{{ $patient->id }}" type="hidden" />
            <button type="submit" class="btn btn-success">@lang('buttons.save')</button>
        </form>
    </div>

    <!-- النصف الثاني: جدول عرض الفحوصات -->
    <div class="col-md-8">
        <h2>@lang('doctor_trans.test_list')</h2>
        <table id="consultationTable" class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>@lang('doctor_trans.request_date')</th>
                    <th>@lang('doctor_trans.requested_by')</th>
                    <th>@lang('doctor_trans.clinical_details_t')</th>
                    <th>@lang('doctor_trans.type_consultation')</th>
                    <th>@lang('doctor_trans.urgency_consultation')</th>
                    <th>@lang('doctor_trans.specialty')</th>
                    <th>@lang('doctor_trans.reason_consultation')</th>
                    <th>@lang('doctor_trans.clinical_details_t')</th>
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
        $("#specialtyDropdown").select2({
            placeholder: "اختر التخصص",
            allowClear: true,
            ajax: {
                url: "{{ route('get.specialty') }}",
                dataType: "json",
                delay: 250,
                processResults: function(data) {
                    // إضافة الخيار الافتراضي "اختر الخدمة" في أول القائمة
                    var results = [{
                        id: '',
                        text: 'اختر التخصص', // العنصر المبدئي
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
</script>
