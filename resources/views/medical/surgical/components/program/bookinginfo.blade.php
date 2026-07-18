<div class="row">

    <!-- Section 1: Booking and Discharge Dates -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header text-white" style=" background-color: #1373c3 !important;">
                <i class="bx bx-calendar"></i> {{ trans('surgical_trans.dates_section') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="expected_discharge_date" class="form-label">
                                <i class="bx bx-calendar-check"></i>
                                {{ trans('surgical_trans.surgical') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="surgical_id" name="surgical_id"
                                required>
                                @foreach ($services as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('surgical_id') == $data->id ? 'selected' : '' }}>
                                        {{ $data->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ trans('surgical_trans.surgical_help') }}</small>
                        </div>
                    </div>
                    <!-- Booking Date -->

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="booking_date" class="form-label">
                                <i class="bx bx-calendar-alt"></i> {{ trans('surgical_trans.booking_date') }}
                            </label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control"
                                onchange="getTimeSlots();" min="{{ \Carbon\Carbon::today()->toDateString() }}"
                                value="{{ old('booking_date') ?? \Carbon\Carbon::today()->toDateString() }}"
                                placeholder="{{ trans('surgical_trans.select_booking_date') }}">
                            <small class="form-text text-muted">{{ trans('surgical_trans.booking_date_help') }}</small>
                        </div>
                    </div>

                    <!-- Expected Discharge Date -->


                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="patient_status" class="form-label">
                                <i class="bx bx-heart"></i> {{ trans('surgical_trans.room') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="id_room" name="id_room"
                                onchange="getTimeSlots();" required>
                                <option value="" selected disabled>{{ trans('surgical_trans.room') }}</option>
                                @foreach ($room as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('surgical_id') == $data->id ? 'selected' : '' }}>
                                        {{ App::getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ trans('surgical_trans.room_help') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card ribbon-box border mb-lg-0 right">
                            <div class="card-body">
                                <div class="ribbon ribbon-info ribbon-shape">
                                    {{ trans('outpatient_trans.select_time') }}
                                </div>
                                <label for="lastNameinput" class="form-label">
                                    {{ trans('outpatient_trans.choose_time') }}
                                </label>
                                <div id="timeSlotsContainer" class="timeSlotsContainer"
                                    style="height: 200px !important;overflow: auto;">

                                </div>

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <!-- Section: Companion Information -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bx bx-user"></i> {{ trans('surgical_trans.surgery_medical_team') }}
            </div>
            <div class="card-body">
                <!-- Section: surgical Team -->
                <div class="row mt-4">
                    <!-- جراح -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-user"></i> {{ trans('surgical_trans.surgeon') }}
                            </label>
                            <select name="surgeon[]" id="surgeon"
                                class="form-control js-example-basic-single"multiple>

                                <!-- إضافة المزيد من الخيارات حسب الحاجة -->
                            </select>
                        </div>
                    </div>

                    <!-- مساعد جراح -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-user"></i> {{ trans('surgical_trans.assistant_surgeon') }}
                            </label>
                            <select name="assistant_surgeon[]" id="assistant_surgeon"
                                class="form-control js-example-basic-single" multiple>

                                <!-- إضافة المزيد من الخيارات حسب الحاجة -->
                            </select>
                        </div>
                    </div>

                    <!-- تخدير -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-user"></i> {{ trans('surgical_trans.anesthesiologist') }}
                            </label>
                            <select name="anesthesiologist[]" id="anesthesiologist"
                                class="form-control js-example-basic-single" multiple>

                                <!-- إضافة المزيد من الخيارات حسب الحاجة -->
                            </select>
                        </div>
                    </div>

                    <!-- ممرضة -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-user"></i> {{ trans('surgical_trans.nurse') }}
                            </label>
                            <select name="nurse[]" id="nurse" class="form-control js-example-basic-single" multiple>

                                <!-- إضافة المزيد من الخيارات حسب الحاجة -->
                            </select>
                        </div>
                    </div>

                    <!-- فني
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-user"></i> {{ trans('surgical_trans.technician') }}
                            </label>
                            <select name="technician[]" id="technician" class="form-control js-example-basic-single"
                                multiple required>

                                إضافة المزيد من الخيارات حسب الحاجة
                            </select>
                        </div>
                    </div>-->

                    <!-- آخر -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-user"></i> {{ trans('surgical_trans.other') }}
                            </label>
                            <select name="other[]" id="other" class="form-control js-example-basic-single"
                                multiple>
                                <!-- إضافة المزيد من الخيارات حسب الحاجة -->
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Section: Diagnoses -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <i class="bx bx-notepad"></i> {{ trans('surgical_trans.diagnoses') }}
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">
                        <i class="bx bx-search-alt-2"></i> {{ trans('surgical_trans.select_diagnosis') }}
                    </label>
                    <select name="diagnoses[]" id="diagnoses" class="form-control js-example-basic-single" multiple>
                        @foreach ($diagnoses as $diagnosis)
                            <option value="{{ $diagnosis->id }}">{{ $diagnosis->name }}</option>
                        @endforeach
                    </select>
                    <small
                        class="form-text text-muted">{{ trans('surgical_trans.select_existing_diagnosis_help') }}</small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="bx bx-pencil"></i> {{ trans('surgical_trans.add_custom_diagnosis') }}
                    </label>
                    <textarea name="custom_diagnosis" id="custom_diagnosis" rows="3" class="form-control"
                        placeholder="{{ trans('surgical_trans.write_custom_diagnosis') }}"></textarea>
                    <small class="form-text text-muted">{{ trans('surgical_trans.write_if_not_exist') }}</small>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    function getTimeSlots() {
        var booking_date = $('#booking_date').val();
        var id_room = $('#id_room').val();

        $.ajax({
            url: "{{ route('get-time-slots-surgical') }}",
            method: 'POST',
            data: {
                booking_date: booking_date,
                id_room: id_room,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response == 'no data') {
                    $('#timeSlotsContainer').empty();
                    Swal.fire({
                        title: "{{ trans('outpatient_trans.no_appointments') }}!",
                        icon: "info",
                        showConfirmButton: false,
                        timer: 3500
                    });
                } else {
                    var timeSlots = response.time_slots;
                    $('#timeSlotsContainer').empty();

                    timeSlots.forEach(function(slot) {
                        var isReserved = slot.reserved;
                        var disabledAttr = isReserved ? 'disabled' : '';
                        var style = isReserved ? 'style="background-color: red;color: #fff;"' : '';

                        $('#timeSlotsContainer').append(
                            '<div class="app-check">' +
                            '<input ' + disabledAttr +
                            ' onclick="handleSlotSelection(this)" type="checkbox" id="time_slots" class="option-input checkbox" name="time_slots[]" value="' +
                            slot.start + '" />' +
                            '<div ' + style + ' class="app-border"><label class="app-label">' +
                            slot.start + ' - ' + slot.end +
                            '</label></div></div>'
                        );

                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX request error:', error);
            }
        });
    }

    let selectedSlots = []; // مصفوفة لحفظ البداية والنهاية

    function handleSlotSelection(inputElement) {
        let value = inputElement.value;

        if ($(inputElement).is(':disabled')) {
            return; // لا نسمح بتغيير الفتحات المحجوزة
        }

        // إذا كانت المصفوفة فارغة أو تحتوي على عنصر واحد فقط، أضف الوقت الجديد
        if (selectedSlots.length < 2) {
            selectedSlots.push(value);
        } else {
            // إذا كانت المصفوفة تحتوي على عنصرين بالفعل، أعد تعيين المصفوفة
            selectedSlots = [value];
        }

        updateSelectedSlots(); // تحديث الاختيارات بناءً على القيم الجديدة
    }


    function updateSelectedSlots() {
        if (selectedSlots.length !== 2) return; // نحتاج بالضبط وقتين لاختيار النطاق

        let times = $(".option-input:checkbox:not(:disabled)").map(function() {
            return this.value;
        }).get(); // جلب جميع الأوقات المتاحة عدا المحجوزة

        let start = times.indexOf(selectedSlots[0]);
        let end = times.indexOf(selectedSlots[1]);

        if (start > end) {
            [start, end] = [end, start]; // تبديل الأوقات إذا كان المستخدم اختار النهاية قبل البداية
            selectedSlots = [times[start], times[end]];
        }

        $(".option-input:checkbox:not(:disabled)").each(function() {
            let index = times.indexOf(this.value);
            $(this).prop("checked", index >= start && index <= end);
            highlightSelected(this);
        });
    }

    function highlightSelected(inputElement) {
        if ($(inputElement).is(':checked')) {
            $(inputElement).next('.app-border').css({
                'background-color': 'green',
                'color': '#fff'
            });
        } else {
            $(inputElement).next('.app-border').css({
                'background-color': '',
                'color': ''
            });
        }
    }



    $(document).ready(function() {
        // تحميل بيانات مصدر الدخول بعد تحميل الصفحة
        loadphysicianAfterLoad();
        loadnurseAfterLoad();
    });


    function loadphysicianAfterLoad() {
        // إظهار رسالة تحميل مؤقتة في الـ select
        $('#surgeon').html('<option value="">{{ trans('surgical_trans.loading') }}</option>');
        $('#assistant_surgeon').html('<option value="">{{ trans('surgical_trans.loading') }}</option>');
        $('#anesthesiologist').html('<option value="">{{ trans('surgical_trans.loading') }}</option>');
        $('#other').html('<option value="">{{ trans('surgical_trans.loading') }}</option>');

        // إرسال طلب AJAX لتحميل بيانات مصدر الدخول
        $.ajax({
            url: "{{ route('getphysician') }}", // تأكد من أن هذا المسار صحيح
            type: "GET",
            success: function(data) {
                // إعادة تعيين الخيارات
                $('#surgeon').html(
                    '<option value="" disabled>{{ trans('surgical_trans.select_surgeon') }}</option>'
                );
                $('#assistant_surgeon').html(
                    '<option value="" disabled>{{ trans('surgical_trans.select_assistant_surgeon') }}</option>'
                );
                $('#anesthesiologist').html(
                    '<option value="" disabled>{{ trans('surgical_trans.select_anesthesiologist') }}</option>'
                );
                $('#other').html(
                    '<option value="" disabled>{{ trans('surgical_trans.select_other') }}</option>'
                );
                // إضافة الخيارات الجديدة
                $.each(data, function(key, physician) {
                    $('#surgeon').append('<option value="' + physician.id +
                        '">' + physician.name + '</option>');
                    $('#assistant_surgeon').append('<option value="' + physician.id +
                        '">' + physician.name + '</option>');
                    $('#anesthesiologist').append('<option value="' + physician.id +
                        '">' + physician.name + '</option>');
                    $('#other').append('<option value="' + physician.id +
                        '">' + physician.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                // معالجة الأخطاء في حال فشل الطلب
                console.error("Error loading physician:", error);
                $('#surgeon').html(
                    '<option value="">{{ trans('surgical_trans.error_loading_data') }}</option>');
                $('#assistant_surgeon').html(
                    '<option value="">{{ trans('surgical_trans.error_loading_data') }}</option>');
                $('#anesthesiologist').html(
                    '<option value="">{{ trans('surgical_trans.error_loading_data') }}</option>');
                $('#other').html(
                    '<option value="">{{ trans('surgical_trans.error_loading_data') }}</option>');
            }
        });
    }

    function loadnurseAfterLoad() {
        // إظهار رسالة تحميل مؤقتة في الـ select
        $('#nurse').html('<option value="">{{ trans('surgical_trans.loading') }}</option>');


        // إرسال طلب AJAX لتحميل بيانات مصدر الدخول
        $.ajax({
            url: "{{ route('getnurse') }}", // تأكد من أن هذا المسار صحيح
            type: "GET",
            success: function(data) {
                $('#nurse').html(
                    '<option value="" disabled>{{ trans('surgical_trans.select_treating_nurse') }}</option>'
                );
                // إضافة الخيارات الجديدة
                $.each(data, function(key, nurse) {
                    $('#nurse').append('<option value="' + nurse.id +
                        '">' + nurse.name + '</option>');

                });
            },
            error: function(xhr, status, error) {
                // معالجة الأخطاء في حال فشل الطلب
                console.error("Error loading nurse:", error);
                $('#nurse').html(
                    '<option value="">{{ trans('surgical_trans.error_loading_data') }}</option>');
            }
        });
    }
</script>
