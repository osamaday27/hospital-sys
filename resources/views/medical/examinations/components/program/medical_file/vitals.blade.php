<div class="tab-pane" id="vital_signs" role="tabpanel" aria-labelledby="vital-signs-tab">
    <div class="row g-3">
        <!-- فورم الإضافة -->
        <div class="col-lg-3">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="mdi mdi-heart-pulse me-2"></i>
                    <h5 class="mb-0" style="color: #fff;">{{ __('doctor_trans.add_vital_sign') }}</h5>
                </div>
                <div class="card-body">
                    <form id="vitalSignsForm" class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                        <input type="hidden" name="patient_visits_id" value="{{ $patient_visits->id }}">
                        <input type="hidden" id="patientAge" value="{{ $age }}">

                        <!-- الوزن والطول مع مؤشر كتلة الجسم -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.weight_height') }}</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" name="weight" id="weight" class="form-control"
                                            placeholder="{{ __('doctor_trans.weight') }}" min="0" max="300"
                                            step="0.1" value="{{ $latestVitalSign->weight ?? '' }}">
                                        <span class="input-group-text">kg</span>
                                        <div class="invalid-feedback">
                                            {{ __('doctor_trans.weight_validation') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" name="height" id="height" class="form-control"
                                            placeholder="{{ __('doctor_trans.height') }}" min="0" max="250"
                                            step="0.1" value="{{ $latestVitalSign->height ?? '' }}">
                                        <span class="input-group-text">cm</span>
                                        <div class="invalid-feedback">
                                            {{ __('doctor_trans.height_validation') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- مؤشر كتلة الجسم مع تصنيف -->
                            <div class="mt-2">
                                <label class="form-label fw-bold">{{ __('doctor_trans.bmi') }}</label>
                                <div class="input-group">
                                    <input type="text" id="bmi" name="bmi" class="form-control" readonly>
                                    <span class="input-group-text" id="bmiCategory"></span>
                                </div>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div id="bmiProgress" class="progress-bar" role="progressbar" style="width: 0%">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- درجة الحرارة -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.temperature') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="temperature" id="temperature"
                                    class="form-control" min="25" max="45">
                                <span class="input-group-text">°C</span>
                                <div class="invalid-feedback">
                                    {{ __('doctor_trans.temp_validation') }}
                                </div>
                                <div class="form-text text-danger" id="tempAlert"></div>
                            </div>
                        </div>

                        <!-- معدل ضربات القلب -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.heart_rate') }}</label>
                            <div class="input-group">
                                <input type="number" name="heart_rate" id="heart_rate" class="form-control"
                                    min="30" max="250">
                                <span class="input-group-text">bpm</span>
                                <div class="invalid-feedback">
                                    {{ __('doctor_trans.heart_rate_validation') }}
                                </div>
                                <div class="form-text text-danger" id="heartAlert"></div>
                            </div>
                        </div>

                        <!-- معدل التنفس -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.respiratory_rate') }}</label>
                            <div class="input-group">
                                <input type="number" name="respiratory_rate" id="respiratory_rate"
                                    class="form-control" min="5" max="60">
                                <span class="input-group-text">bpm</span>
                                <div class="invalid-feedback">
                                    {{ __('doctor_trans.resp_rate_validation') }}
                                </div>
                                <div class="form-text text-danger" id="respAlert"></div>
                            </div>
                        </div>

                        <!-- ضغط الدم -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.blood_pressure') }}</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" name="systolic_bp" id="systolic_bp"
                                            class="form-control" placeholder="{{ __('doctor_trans.systolic') }}"
                                            min="50" max="250">
                                        <span class="input-group-text">SYS</span>
                                        <div class="invalid-feedback">
                                            {{ __('doctor_trans.systolic_validation') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" name="diastolic_bp" id="diastolic_bp"
                                            class="form-control" placeholder="{{ __('doctor_trans.diastolic') }}"
                                            min="30" max="150">
                                        <span class="input-group-text">DIA</span>
                                        <div class="invalid-feedback">
                                            {{ __('doctor_trans.diastolic_validation') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="bpWarning" class="text-danger small mt-1 d-none"></div>
                        </div>

                        <!-- تشبع الأكسجين -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.oxygen_saturation') }}</label>
                            <div class="input-group">
                                <input type="number" name="oxygen_saturation" id="oxygen_saturation"
                                    class="form-control" min="50" max="100">
                                <span class="input-group-text">%</span>
                                <div class="invalid-feedback">
                                    {{ __('doctor_trans.oxygen_validation') }}
                                </div>
                                <div class="form-text text-danger" id="oxyAlert"></div>
                            </div>
                        </div>

                        <!-- سكر الدم -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.blood_sugar') }}</label>
                            <div class="input-group">
                                <input type="number" name="blood_sugar" id="blood_sugar" class="form-control"
                                    min="0" max="500">
                                <span class="input-group-text">mg/dL</span>
                                <div class="invalid-feedback">
                                    {{ __('doctor_trans.blood_sugar_validation') }}
                                </div>
                                <div class="form-text text-danger" id="sugarAlert"></div>
                            </div>
                        </div>

                        <!-- مقياس الألم -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.pain_scale') }}</label>
                            <div class="d-flex align-items-center">
                                <input type="range" name="pain_scale" class="form-range" min="0"
                                    max="10" step="0.5" id="painScale" value="0">
                                <span class="ms-3 fw-bold" id="painScaleValue">0</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <!-- زر الإضافة -->
                        <button type="submit" id="addVitalSignBtn" class="btn btn-primary w-100 py-2">
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            {{ __('doctor_trans.add') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- جدول العرض -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-secondary text-white">{{ __('doctor_trans.previous_vitals') }}</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap examination-table"
                            id="vitalSignsTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{ __('doctor_trans.date') }}</th>
                                    <th>{{ __('doctor_trans.temperature') }} (°C)</th>
                                    <th>{{ __('doctor_trans.heart_rate') }} (bpm)</th>
                                    <th>{{ __('doctor_trans.respiratory_rate') }} (bpm)</th>
                                    <th>{{ __('doctor_trans.blood_pressure') }} (mmHg)</th>
                                    <th>{{ __('doctor_trans.oxygen_saturation') }} (%)</th>
                                    <th>{{ __('doctor_trans.blood_sugar') }} (mg/dL)</th>
                                    <th>{{ __('doctor_trans.pain_scale') }}</th>
                                    <th>{{ __('doctor_trans.notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // بيانات المريض
        const patientAge = {{ $age }};
        const isChild = patientAge < 12;
        const isInfant = patientAge < 2;
        const isElderly = patientAge > 65;

        // DataTable
        const table = $('#vitalSignsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
            },
            ajax: {
                url: "{{ route('vital-signs.index', $patient->id) }}",
                type: "GET",
                data: {
                    visit_id: "{{ $patient_visits->id }}"
                }
            },
            columns: [{
                    data: 'created_at'
                },
                {
                    data: 'temperature'
                },
                {
                    data: 'heart_rate'
                },
                {
                    data: 'respiratory_rate'
                },
                {
                    data: null,
                    render: function(data) {
                        return data.systolic_bp && data.diastolic_bp ?
                            `${data.systolic_bp}/${data.diastolic_bp}` :
                            '-';
                    }
                },
                {
                    data: 'oxygen_saturation'
                },
                {
                    data: 'blood_sugar'
                },
                {
                    data: 'pain_scale'
                },
                {
                    data: 'notes'
                },
            ],
            createdRow: function(row, data) {
                // تحديد القيم الطبيعية بناء على العمر
                const normalTemp = isInfant ? [36.5, 37.5] : [36, 37.2];
                const normalHR = isInfant ? [120, 160] : isChild ? [80, 120] : [60, 100];
                const normalRR = isInfant ? [30, 60] : isChild ? [20, 30] : [12, 20];
                const normalBP = isChild ?
                    [90 + (patientAge * 2), 60 + patientAge] :
                    isElderly ?
                    [90, 140] :
                    [90, 120];
                const normalOxygen = isElderly ? [92, 100] : [95, 100];
                const normalSugar = [70, 140];

                if (data.temperature > normalTemp[1] || data.temperature < normalTemp[0]) {
                    $(row).find('td').eq(1).css('background-color', '#f8d7da').attr('title',
                        'درجة الحرارة غير طبيعية');
                }
                if (data.heart_rate > normalHR[1] || data.heart_rate < normalHR[0]) {
                    $(row).find('td').eq(2).css('background-color', '#f8d7da').attr('title',
                        'معدل النبض غير طبيعي');
                }
                if (data.respiratory_rate > normalRR[1] || data.respiratory_rate < normalRR[0]) {
                    $(row).find('td').eq(3).css('background-color', '#f8d7da').attr('title',
                        'معدل التنفس غير طبيعي');
                }
                if ((data.systolic_bp && data.systolic_bp > normalBP[1]) || (data.diastolic_bp &&
                        data.diastolic_bp > normalBP[1] - 30)) {
                    $(row).find('td').eq(4).css('background-color', '#f8d7da').attr('title',
                        'ضغط الدم مرتفع');
                }
                if ((data.systolic_bp && data.systolic_bp < normalBP[0]) || (data.diastolic_bp &&
                        data.diastolic_bp < normalBP[0] - 30)) {
                    $(row).find('td').eq(4).css('background-color', '#f8d7da').attr('title',
                        'ضغط الدم منخفض');
                }
                if (data.oxygen_saturation && data.oxygen_saturation < normalOxygen[0]) {
                    $(row).find('td').eq(5).css('background-color', '#f8d7da').attr('title',
                        'تشبع الأكسجين منخفض');
                }
                if (data.blood_sugar && (data.blood_sugar > normalSugar[1] || data.blood_sugar <
                        normalSugar[0])) {
                    $(row).find('td').eq(6).css('background-color', '#f8d7da').attr('title',
                        'سكر الدم غير طبيعي');
                }
            }
        });

        // حساب BMI
        function calculateBMI() {
            const weight = parseFloat($('#weight').val());
            const height = parseFloat($('#height').val()) / 100;
            if (weight > 0 && height > 0) {
                const bmi = (weight / (height * height)).toFixed(1);
                $('#bmi').val(bmi);
                let category = '';
                let color = '';
                if (bmi < 18.5) {
                    category = 'نقص وزن';
                    color = 'bg-info';
                } else if (bmi < 25) {
                    category = 'طبيعي';
                    color = 'bg-success';
                } else if (bmi < 30) {
                    category = 'زيادة وزن';
                    color = 'bg-warning';
                } else {
                    category = 'سمنة';
                    color = 'bg-danger';
                }
                $('#bmiCategory').text(category).removeClass().addClass('input-group-text ' + color);
                const bmiPercent = Math.min(Math.max((bmi - 15) / 20 * 100, 0), 100);
                $('#bmiProgress').css('width', bmiPercent + '%').removeClass().addClass('progress-bar ' +
                color);
            } else {
                $('#bmi').val('');
                $('#bmiCategory').text('').removeClass().addClass('input-group-text');
                $('#bmiProgress').css('width', '0%').removeClass().addClass('progress-bar');
            }
        }

        // تحديث قيمة مقياس الألم
        $('#painScale').on('input', function() {
            $('#painScaleValue').text($(this).val());
        });

        // حساب BMI عند إدخال الطول والوزن
        $('#weight, #height').on('input', calculateBMI);

        // إضافة تنبيهات لكل حقل
        function setError(selector, message) {
            $(selector).removeClass('d-none').text(message);
        }

        function clearErrors() {
            $('.text-danger').text('');
            $('.text-danger').addClass('d-none');
        }
        // تنبيه درجة الحرارة
        function checkTemperature() {
            const temp = parseFloat($('#temperature').val());
            if (!isNaN(temp)) {
                const normal = isInfant ? [36.5, 37.5] : [36, 37.2];
                if (temp < normal[0] || temp > normal[1]) {
                    $('#tempAlert').text('⚠️ درجة الحرارة غير طبيعية').removeClass('d-none');
                } else {
                    $('#tempAlert').text('').addClass('d-none');
                }
            } else {
                $('#tempAlert').text('').addClass('d-none');
            }
        }

        // تنبيه معدل النبض
        function checkHeartRate() {
            const hr = parseInt($('#heart_rate').val());
            if (!isNaN(hr)) {
                const normal = isInfant ? [120, 160] : isChild ? [80, 120] : [60, 100];
                if (hr < normal[0] || hr > normal[1]) {
                    $('#heartAlert').text('⚠️ معدل النبض غير طبيعي').removeClass('d-none');
                } else {
                    $('#heartAlert').text('').addClass('d-none');
                }
            } else {
                $('#heartAlert').text('').addClass('d-none');
            }
        }

        // تنبيه معدل التنفس
        function checkRespiratoryRate() {
            const rr = parseInt($('#respiratory_rate').val());
            if (!isNaN(rr)) {
                const normal = isInfant ? [30, 60] : isChild ? [20, 30] : [12, 20];
                if (rr < normal[0] || rr > normal[1]) {
                    $('#respAlert').text('⚠️ معدل التنفس غير طبيعي').removeClass('d-none');
                } else {
                    $('#respAlert').text('').addClass('d-none');
                }
            } else {
                $('#respAlert').text('').addClass('d-none');
            }
        }

        // تنبيه ضغط الدم
        function checkBloodPressure() {
            const sys = parseInt($('#systolic_bp').val());
            const dia = parseInt($('#diastolic_bp').val());
            let warn = '';
            if (!isNaN(sys) && !isNaN(dia)) {
                const normal = isChild ?
                    [90 + (patientAge * 2), 60 + patientAge] :
                    isElderly ?
                    [90, 140] :
                    [90, 120];
                if (sys < normal[0] || dia < normal[0] - 30) {
                    warn = '⚠️ ضغط الدم منخفض';
                } else if (sys > normal[1] || dia > normal[1] - 30) {
                    warn = '⚠️ ضغط الدم مرتفع';
                }
            }
            if (warn !== '') {
                $('#bpWarning').text(warn).removeClass('d-none');
            } else {
                $('#bpWarning').text('').addClass('d-none');
            }
        }

        // تنبيه تشبع الأكسجين
        function checkOxygen() {
            const oxy = parseInt($('#oxygen_saturation').val());
            if (!isNaN(oxy)) {
                const normal = isElderly ? [92, 100] : [95, 100];
                if (oxy < normal[0]) {
                    $('#oxyAlert').text('⚠️ تشبع الأكسجين منخفض').removeClass('d-none');
                } else {
                    $('#oxyAlert').text('').addClass('d-none');
                }
            } else {
                $('#oxyAlert').text('').addClass('d-none');
            }
        }

        // تنبيه سكر الدم
        function checkBloodSugar() {
            const sugar = parseInt($('#blood_sugar').val());
            if (!isNaN(sugar)) {
                const normal = [70, 140];
                if (sugar < normal[0] || sugar > normal[1]) {
                    $('#sugarAlert').text('⚠️ سكر الدم غير طبيعي').removeClass('d-none');
                } else {
                    $('#sugarAlert').text('').addClass('d-none');
                }
            } else {
                $('#sugarAlert').text('').addClass('d-none');
            }
        }

        // ربط الأحداث
        $('#temperature').on('input', checkTemperature);
        $('#heart_rate').on('input', checkHeartRate);
        $('#respiratory_rate').on('input', checkRespiratoryRate);
        $('#systolic_bp, #diastolic_bp').on('input', checkBloodPressure);
        $('#oxygen_saturation').on('input', checkOxygen);
        $('#blood_sugar').on('input', checkBloodSugar);

        // إرسال النموذج
        $('#vitalSignsForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors();
            const formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: "{{ route('vital-signs.store') }}",
                data: formData,
                beforeSend: function() {
                    $('button[type="submit"]').prop('disabled', true)
                        .html(
                            '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...'
                            );
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت الإضافة بنجاح',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#vitalSignsForm')[0].reset();
                    $('#bmi').val('');
                    $('#bmiCategory').text('').removeClass().addClass('input-group-text');
                    $('#painScaleValue').text('0');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            setError('#' + key + 'Error', value[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء الحفظ'
                        });
                    }
                },
                complete: function() {
                    $('button[type="submit"]').prop('disabled', false)
                        .html('<i class="mdi mdi-plus-circle-outline me-1"></i> إضافة');
                }
            });
        });
    });
</script>
