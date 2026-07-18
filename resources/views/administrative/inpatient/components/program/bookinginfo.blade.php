<div class="row">
    <!-- Section 1: Booking and Discharge Dates -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header text-white" style=" background-color: #1373c3 !important;">
                <i class="bx bx-calendar"></i> {{ trans('inpatient_trans.dates_section') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Booking Date -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="booking_date" class="form-label">
                                <i class="bx bx-calendar-alt"></i> {{ trans('inpatient_trans.booking_date') }}
                            </label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control"
                                min="{{ \Carbon\Carbon::today()->toDateString() }}" value="{{ old('booking_date') }}"
                                placeholder="{{ trans('inpatient_trans.select_booking_date') }}">
                            <small class="form-text text-muted">{{ trans('inpatient_trans.booking_date_help') }}</small>
                        </div>
                    </div>

                    <!-- Expected Discharge Date -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="expected_discharge_date" class="form-label">
                                <i class="bx bx-calendar-check"></i>
                                {{ trans('inpatient_trans.expected_discharge_date') }}
                            </label>
                            <input type="date" name="expected_discharge_date" id="expected_discharge_date"
                                class="form-control" min="{{ \Carbon\Carbon::today()->toDateString() }}"
                                value="{{ old('expected_discharge_date') }}"
                                placeholder="{{ trans('inpatient_trans.select_discharge_date') }}">
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.discharge_date_help') }}</small>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="patient_status" class="form-label">
                                <i class="bx bx-heart"></i> {{ trans('inpatient_trans.patient_status') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="patient_status"
                                name="patient_status" required>
                                <option value="">{{ trans('inpatient_trans.select_patient_status') }}</option>
                                <option value="stable" {{ old('patient_status') == 'stable' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.stable') }}
                                </option>
                                <option value="critical" {{ old('patient_status') == 'critical' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.critical') }}
                                </option>
                                <option value="recovering"
                                    {{ old('patient_status') == 'recovering' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.recovering') }}
                                </option>
                            </select>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.patient_status_help') }}</small>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="case_type" class="form-label">
                                <i class="bx bx-clipboard"></i> {{ trans('inpatient_trans.case_type') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="case_type" name="case_type"
                                required>
                                <option value="">{{ trans('inpatient_trans.select_case_type') }}</option>
                                <option value="emergency" {{ old('case_type') == 'emergency' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.emergency') }}
                                </option>
                                <option value="scheduled" {{ old('case_type') == 'scheduled' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.scheduled') }}
                                </option>
                                <option value="follow_up" {{ old('case_type') == 'follow_up' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.follow_up') }}
                                </option>
                            </select>
                            <small class="form-text text-muted">{{ trans('inpatient_trans.case_type_help') }}</small>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="case_severity" class="form-label">
                                <i class="bx bx-alarm-exclamation"></i> {{ trans('inpatient_trans.case_severity') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="case_severity" name="case_severity"
                                required>
                                <option value="">{{ trans('inpatient_trans.select_case_severity') }}</option>
                                <option value="low" {{ old('case_severity') == 'low' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.low') }}
                                </option>
                                <option value="medium" {{ old('case_severity') == 'medium' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.medium') }}
                                </option>
                                <option value="high" {{ old('case_severity') == 'high' ? 'selected' : '' }}>
                                    {{ trans('inpatient_trans.high') }}
                                </option>
                            </select>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.case_severity_help') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Section 3: Nursing Station, Room, and Bed -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bx bx-building-house"></i> {{ trans('inpatient_trans.accommodation_section') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Nursing Station -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nursing_station_id" class="form-label">
                                <i class="bx bxs-school"></i> {{ trans('inpatient_trans.nursing_station') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="nursing_stations_id"
                                name="nursing_station_id" required>
                                <option value="">{{ trans('inpatient_trans.select_nursing_stations') }}</option>
                                @foreach ($nursingstation as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('nursing_stations_id') == $data->id ? 'selected' : '' }}>
                                        {{ trans('inpatient_trans.nursing_station') . ' :- ' . (App::getLocale() == 'ar' ? $data->name_ar : $data->name_en) }}
                                        - {{ trans('inpatient_trans.floor') }}:
                                        {{ $data->floor ? (App::getLocale() == 'ar' ? $data->floor->name_ar : $data->floor->name_en) : trans('inpatient_trans.no_floor') }}
                                    </option>
                                @endforeach
                            </select>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.nursing_station_help') }}</small>
                        </div>
                    </div>

                    <!-- Room -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="room_id" class="form-label">
                                <i class="bx bx-door-open"></i> {{ trans('inpatient_trans.room') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="room_id" name="room_id" required>
                                <option value="">{{ trans('inpatient_trans.select_room') }}</option>
                            </select>
                            <small class="form-text text-muted">{{ trans('inpatient_trans.room_help') }}</small>
                        </div>
                    </div>

                    <!-- Bed -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="bed_id" class="form-label">
                                <i class="bx bx-bed"></i> {{ trans('inpatient_trans.bed') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="bed_id" name="bed_id"
                                required>
                                <option value="">{{ trans('inpatient_trans.select_bed') }}</option>
                            </select>
                            <small class="form-text text-muted">{{ trans('inpatient_trans.bed_help') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="full_room_booking" class="form-label">
                                <i class="bx bx-bed"></i> {{ trans('inpatient_trans.full_room_booking') }}
                            </label>
                            <div class="form-check">
                                <input type="checkbox" name="full_room_booking" id="full_room_booking"
                                    class="form-check-input" value="1"
                                    {{ old('full_room_booking')}}>
                                <label class="form-check-label" for="full_room_booking">
                                    {{ trans('inpatient_trans.enable_full_room_booking') }}
                                </label>
                            </div>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.full_room_booking_help') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Entry Sources and Admission Reason -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="bx bx-list-check"></i> {{ trans('inpatient_trans.admission_section') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="manual_booking_number" class="form-label">
                                <i class="bx bx-edit-alt"></i>
                                {{ trans('inpatient_trans.manual_booking_number') }}
                            </label>
                            <input type="text" name="manual_booking_number" id="manual_booking_number"
                                class="form-control" value="{{ old('manual_booking_number') }}"
                                placeholder="{{ trans('inpatient_trans.enter_manual_booking_number') }}">
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.help_manual_booking_number') }}</small>
                        </div>
                    </div>
                    <!-- Entry Sources -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="admission_source_id" class="form-label">
                                <i class="bx bx-log-in"></i> {{ trans('inpatient_trans.entry_sources') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="admission_source_id"
                                name="admission_source_id" required onfocus="loadEntrySources('entry_sources')">
                                <option value="">{{ trans('inpatient_trans.select_entry_sources') }}</option>
                            </select>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.entry_sources_help') }}</small>
                        </div>
                    </div>


                    <!-- Admission Reason -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="admission_reason" class="form-label">
                                <i class="bx bx-paste"></i> {{ trans('inpatient_trans.admission_reason') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="admission_reason"
                                name="admission_reason" required>
                                <option value="">{{ trans('inpatient_trans.select_admission_reason') }}</option>
                            </select>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.admission_reason_help') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="treating_physician_id" class="form-label">
                                <i class="bx bx-male"></i> {{ trans('inpatient_trans.treating_physician') }}
                            </label>
                            <select class="form-control js-example-basic-single" id="treating_physician_id"
                                name="treating_physician_id" required>
                                <option value="">{{ trans('inpatient_trans.select_treating_physician') }}
                                </option>
                            </select>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.help_treating_physician') }}</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3" style="margin-top: 10px;">
                            <div class="form-group">
                                <label for="referring_physician_id" class="form-label">
                                    <i class="bx bx-male"></i> {{ trans('inpatient_trans.referring_physician') }}
                                </label>
                                <select class="form-control js-example-basic-single" id="referring_physician_id"
                                    name="referring_physician_id" required>
                                    <option value="">{{ trans('inpatient_trans.select_referring_physician') }}
                                    </option>
                                </select>
                                <small
                                    class="form-text text-muted">{{ trans('inpatient_trans.help_referring_physician') }}</small>
                            </div>
                        </div>
                        <!-- Referred Hospital -->
                        <div class="col-md-3" style="margin-top: 10px;">
                            <div class="form-group">
                                <label for="referred_hospital" class="form-label">
                                    <i class="bx bx-building-house"></i>
                                    {{ trans('inpatient_trans.referred_hospital') }}
                                </label>
                                <input type="text" name="referred_hospital" id="referred_hospital"
                                    class="form-control" value="{{ old('referred_hospital') }}"
                                    placeholder="{{ trans('inpatient_trans.enter_referred_hospital') }}">
                                <small
                                    class="form-text text-muted">{{ trans('inpatient_trans.help_referred_hospital') }}</small>
                            </div>
                        </div>

                        <!-- Referring Physician Notes -->
                        <div class="col-md-3" style="margin-top: 10px;">
                            <div class="form-group">
                                <label for="referring_physician_notes" class="form-label">
                                    <i class="bx bx-note"></i>
                                    {{ trans('inpatient_trans.referring_physician_notes') }}
                                </label>
                                <input type="text" name="referring_physician_notes" id="referring_physician_notes"
                                    class="form-control"
                                    placeholder="{{ trans('inpatient_trans.enter_referring_physician_notes') }}"
                                    value="{{ old('referring_physician_notes') }}">
                                <small
                                    class="form-text text-muted">{{ trans('inpatient_trans.help_referring_physician_notes') }}</small>
                            </div>
                        </div>

                        <!-- Specialty Notes -->
                        <div class="col-md-3" style="margin-top: 10px;">
                            <div class="form-group">
                                <label for="specialty_notes" class="form-label">
                                    <i class="bx bx-clipboard"></i> {{ trans('inpatient_trans.specialty_notes') }}
                                </label>
                                <select class="form-control js-example-basic-single" id="specialty_notes"
                                    name="specialty_notes" required>
                                    <option value="">{{ trans('inpatient_trans.select_specialty_notes') }}
                                    </option>
                                </select>
                                <small
                                    class="form-text text-muted">{{ trans('inpatient_trans.help_specialty_notes') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Section 4: Booking Notes, Initial Diagnosis, and Physician Request Notes -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <i class="bx bx-notepad"></i> {{ trans('inpatient_trans.additional_information') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Booking Notes -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="booking_notes" class="form-label">
                                <i class="bx bx-message-square-detail"></i>
                                {{ trans('inpatient_trans.booking_notes') }}
                            </label>
                            <textarea class="form-control" id="booking_notes" name="booking_notes" rows="3">{{ old('booking_notes') }}</textarea>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.booking_notes_help') }}</small>
                        </div>
                    </div>

                    <!-- Initial Diagnosis -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="initial_diagnosis" class="form-label">
                                <i class="bx bx-heart"></i> {{ trans('inpatient_trans.initial_diagnosis') }}
                            </label>
                            <textarea class="form-control" id="initial_diagnosis" name="initial_diagnosis" rows="3">{{ old('initial_diagnosis') }}</textarea>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.initial_diagnosis_help') }}</small>
                        </div>
                    </div>

                    <!-- Physician Request Notes -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="physician_request_notes" class="form-label">
                                <i class="bx bx-file"></i> {{ trans('inpatient_trans.physician_request_notes') }}
                            </label>
                            <textarea class="form-control" id="physician_request_notes" name="physician_request_notes" rows="3">{{ old('physician_request_notes') }}</textarea>
                            <small
                                class="form-text text-muted">{{ trans('inpatient_trans.physician_request_notes_help') }}</small>
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
                <i class="bx bx-user"></i> {{ trans('inpatient_trans.companion_section') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- اسم المرافق -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-user"></i> {{ trans('inpatient_trans.companion_name') }}
                            </label>
                            <input type="text" name="companion_name" class="form-control" required>
                        </div>
                    </div>

                    <!-- رقم هاتف المرافق -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-phone"></i> {{ trans('inpatient_trans.companion_phone') }}
                            </label>
                            <input type="tel" name="companion_phone" class="form-control" required>
                        </div>
                    </div>

                    <!-- الرقم القومي للمرافق -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-id-card"></i> {{ trans('inpatient_trans.companion_national_id') }}
                            </label>
                            <input type="text" name="companion_national_id" class="form-control" required>
                        </div>
                    </div>

                    <!-- صلة القرابة -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-link"></i> {{ trans('inpatient_trans.companion_relation') }}
                            </label>
                            <select name="companion_relation" class="form-control" required>
                                <option value="father">{{ trans('inpatient_trans.father') }}</option>
                                <option value="mother">{{ trans('inpatient_trans.mother') }}</option>
                                <option value="brother">{{ trans('inpatient_trans.brother') }}</option>
                                <option value="sister">{{ trans('inpatient_trans.sister') }}</option>
                                <option value="spouse">{{ trans('inpatient_trans.spouse') }}</option>
                                <option value="other">{{ trans('inpatient_trans.other') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function() {
        $('#nursing_stations_id').change(function() {
            let nursingStationId = $(this).val();
            $('#room_id').html('<option value="">{{ trans('inpatient_trans.loading') }}</option>');
            $('#bed_id').html('<option value="">{{ trans('inpatient_trans.select_bed') }}</option>');

            if (nursingStationId) {
                $.ajax({
                    url: "{{ route('getRoomsByNursingStation') }}",
                    type: "GET",
                    data: {
                        nursing_station_id: nursingStationId
                    },
                    success: function(data) {
                        $('#room_id').html(
                            '<option value="">{{ trans('inpatient_trans.select_room') }}</option>'
                        );
                        $.each(data, function(key, room) {
                            $('#room_id').append('<option value="' + room.id +
                                '">' + room.name + '</option>');
                        });
                    }
                });
            }
        });

        $('#room_id').change(function() {
            let roomId = $(this).val();
            $('#bed_id').html('<option value="">{{ trans('inpatient_trans.loading') }}</option>');

            if (roomId) {
                $.ajax({
                    url: "{{ route('getBedsByRoom') }}",
                    type: "GET",
                    data: {
                        room_id: roomId
                    },
                    success: function(data) {
                        $('#bed_id').html(
                            '<option value="">{{ trans('inpatient_trans.select_bed') }}</option>'
                        );
                        $.each(data, function(key, bed) {
                            $('#bed_id').append('<option value="' + bed.id + '">' +
                                bed.name + '</option>');
                        });
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        // تحميل بيانات مصدر الدخول بعد تحميل الصفحة
        loadEntrySourcesAfterLoad();
        loadadmissionreasonAfterLoad();
        loadphysicianAfterLoad();
        loadspecialtyAfterLoad();
    });

    function loadEntrySourcesAfterLoad() {
        // إظهار رسالة تحميل مؤقتة في الـ select
        $('#admission_source_id').html('<option value="">{{ trans('inpatient_trans.loading') }}</option>');

        // إرسال طلب AJAX لتحميل بيانات مصدر الدخول
        $.ajax({
            url: "{{ route('getentry_sources') }}", // تأكد من أن هذا المسار صحيح
            type: "GET",
            data: {
                type: 'entry_sources'
            },
            success: function(data) {
                // إعادة تعيين الخيارات
                $('#admission_source_id').html(
                    '<option value="">{{ trans('inpatient_trans.select_entry_sources') }}</option>');

                // إضافة الخيارات الجديدة
                $.each(data, function(key, entry_source) {
                    $('#admission_source_id').append('<option value="' + entry_source.id + '">' +
                        entry_source.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                // معالجة الأخطاء في حال فشل الطلب
                console.error("Error loading entry sources:", error);
                $('#admission_source_id').html(
                    '<option value="">{{ trans('inpatient_trans.error_loading_data') }}</option>');
            }
        });
    }

    function loadadmissionreasonAfterLoad() {
        // إظهار رسالة تحميل مؤقتة في الـ select
        $('#admission_reason').html('<option value="">{{ trans('inpatient_trans.loading') }}</option>');

        // إرسال طلب AJAX لتحميل بيانات مصدر الدخول
        $.ajax({
            url: "{{ route('getentry_sources') }}", // تأكد من أن هذا المسار صحيح
            type: "GET",
            data: {
                type: 'entry'
            },
            success: function(data) {
                // إعادة تعيين الخيارات
                $('#admission_reason').html(
                    '<option value="">{{ trans('inpatient_trans.select_admission_reason') }}</option>'
                );

                // إضافة الخيارات الجديدة
                $.each(data, function(key, entry_source) {
                    $('#admission_reason').append('<option value="' + entry_source.id +
                        '">' + entry_source.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                // معالجة الأخطاء في حال فشل الطلب
                console.error("Error loading entry sources:", error);
                $('#admission_reason').html(
                    '<option value="">{{ trans('inpatient_trans.error_loading_data') }}</option>');
            }
        });
    }

    function loadphysicianAfterLoad() {
        // إظهار رسالة تحميل مؤقتة في الـ select
        $('#treating_physician_id').html('<option value="">{{ trans('inpatient_trans.loading') }}</option>');
        $('#treating_physician_id').html('<option value="">{{ trans('inpatient_trans.loading') }}</option>');

        // إرسال طلب AJAX لتحميل بيانات مصدر الدخول
        $.ajax({
            url: "{{ route('getphysician') }}", // تأكد من أن هذا المسار صحيح
            type: "GET",
            success: function(data) {
                // إعادة تعيين الخيارات
                $('#treating_physician_id').html(
                    '<option value="">{{ trans('inpatient_trans.select_treating_physician') }}</option>'
                );
                $('#referring_physician_id').html(
                    '<option value="">{{ trans('inpatient_trans.select_referring_physician') }}</option>'
                );
                // إضافة الخيارات الجديدة
                $.each(data, function(key, physician) {
                    $('#treating_physician_id').append('<option value="' + physician.id +
                        '">' + physician.name + '</option>');
                    $('#referring_physician_id').append('<option value="' + physician.id +
                        '">' + physician.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                // معالجة الأخطاء في حال فشل الطلب
                console.error("Error loading physician:", error);
                $('#treating_physician_id').html(
                    '<option value="">{{ trans('inpatient_trans.error_loading_data') }}</option>');
                $('#referring_physician_id').html(
                    '<option value="">{{ trans('inpatient_trans.error_loading_data') }}</option>');
            }
        });
    }

    function loadspecialtyAfterLoad() {
        // إظهار رسالة تحميل مؤقتة في الـ select
        $('#specialty_notes').html('<option value="">{{ trans('inpatient_trans.loading') }}</option>');

        // إرسال طلب AJAX لتحميل بيانات مصدر الدخول
        $.ajax({
            url: "{{ route('getspecialty') }}", // تأكد من أن هذا المسار صحيح
            type: "GET",
            data: {
                type: 'entry'
            },
            success: function(data) {
                // إعادة تعيين الخيارات
                $('#specialty_notes').html(
                    '<option value="">{{ trans('inpatient_trans.select_specialty_notes') }}</option>'
                );

                // إضافة الخيارات الجديدة
                $.each(data, function(key, specialty) {
                    $('#specialty_notes').append('<option value="' + specialty.id +
                        '">' + specialty.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                // معالجة الأخطاء في حال فشل الطلب
                console.error("Error loading specialty:", error);
                $('#specialty_notes').html(
                    '<option value="">{{ trans('inpatient_trans.error_loading_data') }}</option>');
            }
        });
    }
</script>
