<div id="update_booking" class="modal modal-xl fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> {{ trans('outpatient_trans.update_booking') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="clinc_id" class="form-label" style="margin-bottom: 12px;">
                                {{ trans('outpatient_trans.clinc') }}
                            </label>
                            <input type="text" class="form-control" name="clinc_name" id="clinc_name" readonly />
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="visit_opening_service" class="form-label" style="margin-bottom: 12px;">
                                {{ trans('outpatient_trans.visit_opening_service') }}
                            </label>
                            <input type="text" class="form-control" name="visit_opening_service"
                                id="visit_opening_service" readonly />
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="clinc_id" class="form-label" style="margin-bottom: 12px;">
                                {{ trans('outpatient_trans.doctor_name') }}
                            </label>
                            <input type="text" class="form-control" name="doctor_name" id="doctor_name" readonly />
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">

                            <input type="hidden" class="form-control" name="update_booking_patient_id"
                                id="update_booking_patient_id" />
                            <input type="hidden" class="form-control" name="id_booking" id="id_booking" />
                            <label for="clinc_id" class="form-label" style="margin-bottom: 12px;">
                                {{ trans('outpatient_trans.choose_clinc') }}
                            </label>
                            <select class="js-example-basic-single" name="clinc_id_update" id="clinc_id_update"
                                style="margin-top: 5px;" onchange="getDoctors_update();getClinicServices_update();">
                                <option selected disabled> {{ trans('outpatient_trans.choose_clinc') }}</option>
                                @foreach ($clinic as $data)
                                    <option value="{{ $data->id }}"> {{ $data->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="id_doctor" class="form-label" style="margin-bottom: 12px;">
                                {{ trans('outpatient_trans.choose_visit_opening_service') }}
                            </label>
                            <select class="js-example-basic-single" name="id_opening_service_update"
                                id="id_opening_service_update">
                                <option selected disabled>
                                    {{ trans('outpatient_trans.choose_visit_opening_service') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="id_doctor" class="form-label" style="margin-bottom: 12px;">
                                {{ trans('outpatient_trans.choose_doctor') }}
                            </label>
                            <select onchange="getTimeSlots_update()" class="js-example-basic-single"
                                name="id_doctor_update" id="id_doctor_update" style="margin-top: 5px;">
                                <option selected disabled> {{ trans('outpatient_trans.select_doctor') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="lastNameinput" class="form-label" style="margin-bottom: 12px;">
                                {{ trans('outpatient_trans.choose_date') }}</label>
                            <input type="date" class="form-control" id="booking_date_update"
                                name="booking_date_update" data-date-format="d M, Y"
                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" onchange="getTimeSlots_update();"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="card ribbon-box border mb-lg-0 right">
                            <div class="card-body">
                                <div class="ribbon ribbon-info ribbon-shape">
                                    {{ trans('outpatient_trans.select_time') }}
                                </div>
                                <label for="lastNameinput" class="form-label">
                                    {{ trans('outpatient_trans.choose_time') }}
                                </label>
                                <div id="timeSlotsContainer_update" class="timeSlotsContainer_update"
                                    style="height: 200px !important;overflow: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light"
                    data-bs-dismiss="modal">{{ trans('buttons.close') }}</button>
                <button type="button" class="btn btn-primary " onclick="update_reservation()">{{ trans('buttons.save') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    function getdataresrvupdate(updatebookingPatientId, id_booking, clinc_name, doctor_name, visit_opening_service) {
        $('#clinc_name').val(clinc_name);
        $('#doctor_name').val(doctor_name);
        $('#visit_opening_service').val(visit_opening_service);
        $('#update_booking_patient_id').val(updatebookingPatientId);
        $('#id_booking').val(id_booking);
    }

    function getDoctors_update() {
        var clinicId = $('#clinc_id_update').val(); // الحصول على الـ clinic_id من العنصر
        document.getElementById("id_doctor_update").disabled = true; // تعطيل حقل الأطباء حتى يتم تحميل البيانات
        // التأكد من وجود clinic_id
        if (clinicId) {
            $.ajax({
                url: '{{ route('get.doctors.by.clinic') }}', // رابط الطلب إلى المسار في Laravel
                type: 'GET',
                data: {
                    clinic_id: clinicId // إرسال الـ clinic_id إلى السيرفر
                },
                success: function(response) {
                    // التحقق من وجود مفتاح doctors في الاستجابة وتحويله إلى مصفوفة
                    if (response.doctors && typeof response.doctors === 'object') {
                        var doctorsArray = Object.values(response.doctors); // تحويل الكائن إلى مصفوفة

                        // مسح محتويات الـ dropdown للأطباء
                        $('#id_doctor_update').empty();

                        // إضافة خيار "اختر الطبيب" في القائمة المنسدلة
                        $('#id_doctor_update').append(
                            '<option selected disabled> {{ trans('outpatient_trans.select_doctor') }} </option>'
                        );

                        // إضافة الأطباء إلى الـ dropdown
                        doctorsArray.forEach(function(doctor) {
                            $('#id_doctor_update').append('<option value="' + doctor.id + '">' +
                                doctor.full_name + '</option>');
                        });
                    } else {
                        console.error("الاستجابة غير صحيحة: response.doctors ليس كائنًا.");
                    }

                    // تمكين حقل اختيار الطبيب بعد التحميل
                    document.getElementById("id_doctor_update").disabled = false;
                },
                error: function(xhr) {
                    console.log(xhr.responseText); // عرض الخطأ في حال حدوث مشكلة في الـ AJAX
                }
            });
        }
    }

    function getTimeSlots_update() {
        var booking_date = $('#booking_date_update').val();
        var id_doctor = $('#id_doctor_update').val();
        var id_clinic = $('#clinc_id_update').val();

        $.ajax({
            url: "{{ route('get-time-slots') }}",
            method: 'POST',
            data: {
                booking_date: booking_date,
                id_doctor: id_doctor,
                id_clinic: id_clinic,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response == 'no data') {
                    $('#timeSlotsContainer_update').empty();
                    Swal.fire({
                        title: "{{ trans('outpatient_trans.no_appointments') }}!",
                        icon: "info",
                        showConfirmButton: false,
                        timer: 3500
                    });
                } else {
                    var timeSlots = response.time_slots;
                    var time_slot_reservations = response.time_slot_reservations;
                    $('#timeSlotsContainer_update').empty();

                    var firstAvailableSlot = null;
                    var lastSlot = timeSlots[timeSlots.length - 1]; // تحديد آخر وقت في الأوقات المتاحة
                    var allSlotsReserved = true; // متغير للتأكد إذا كانت جميع الأوقات محجوزة

                    for (var i = 0; i < timeSlots.length; i++) {
                        var slot = timeSlots[i];
                        var display = '';
                        var style = '';
                        var isReserved = time_slot_reservations.some(function(reservation) {
                            return reservation.time_slot == slot;
                        });

                        if (isReserved) {
                            display = 'disabled';
                            style = 'style="background-color: red;color: #fff;"';
                        } else {
                            allSlotsReserved = false; // إذا كان هناك وقت غير محجوز
                        }

                        // Track the first available slot (not reserved)
                        if (!isReserved && firstAvailableSlot === null) {
                            firstAvailableSlot = slot;
                        }

                        $('#timeSlotsContainer_update').append(
                            '<div class="app-check">' +
                            '<input ' + display +
                            ' onclick="highlightSelected(this)" type="radio" class="option-input radio" name="time_slot" id="time_slot" value="' +
                            slot + '" ' + (firstAvailableSlot === slot ? 'checked' : '') + ' />' +
                            '<div ' + style + ' class="app-border"><label class="app-label">' + slot +
                            '</label></div></div>'
                        );
                    }

                    // إذا كانت جميع الأوقات محجوزة، يتم تحديد آخر وقت متاح بشكل افتراضي
                    if (allSlotsReserved) {
                        firstAvailableSlot = lastSlot; // تحديد آخر وقت
                    }

                    // تأكد من اختيار أول وقت متاح أو آخر وقت إذا كانت جميع الأوقات محجوزة
                    if (firstAvailableSlot !== null) {
                        $('input[name="time_slot"][value="' + firstAvailableSlot + '"]').prop('checked',
                            true);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX request error:', error);
            }
        });
    }

    function highlightSelected(inputElement) {
        $('.app-border').css({
            'background-color': '',
            'color': ''
        }); // Reset all item colors
        $(inputElement).next('.app-border').css({
            'background-color': 'green',
            'color': '#fff'
        }); // Highlight selected item
    }


    function getClinicServices_update() {
        var clinicId = $('#clinc_id_update').val();

        if (clinicId) {
            $.ajax({
                url: "{{ route('get-clinic-services') }}",
                method: 'GET',
                data: {
                    clinic_id: clinicId
                },
                success: function(response) {
                    $('#id_opening_service_update').empty().append(
                        '<option selected disabled>{{ trans('outpatient_trans.choose_visit_opening_service') }}</option>'
                    );

                    if (response.services.length > 0) {
                        response.services.forEach(function(service) {
                            $('#id_opening_service_update').append('<option value="' + service.id +
                                '">' +
                                service.name + '</option>');
                        });
                    } else {
                        Swal.fire({
                            title: "{{ trans('outpatient_trans.no_services_available') }}",
                            icon: "info",
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching clinic services:', xhr.responseText);
                }
            });
        }
    }

    function update_reservation() {
        var clinicId = $('#clinc_id_update').val();
        var id_doctor = $('#id_doctor_update').val();
        var booking_date = $('#booking_date_update').val();
        var id_opening_service = $('#id_opening_service_update').val();
        var patient_id = $('#update_booking_patient_id').val();
        var id_booking = $('#id_booking').val();
        var time_slot = $('input[name="time_slot"]:checked').val();
        // التحقق من ود القيم المطلوبة
        if (!clinicId || !patient_id || !id_doctor || !booking_date || !id_opening_service) {
            Swal.fire({
                title: "{{ trans('outpatient_trans.missing_fields') }}",
                icon: "warning",
                showConfirmButton: true
            });
            return;
        }

        // إظهار رسالة تحميل للمستخدم
        Swal.fire({
            title: "{{ trans('outpatient_trans.saving_reservation') }}",
            text: "{{ trans('outpatient_trans.please_wait') }}",
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('update.reservation') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                clinic_id: clinicId,
                id_doctor: id_doctor,
                booking_date: booking_date,
                id_opening_service: id_opening_service,
                patient_id: patient_id,
                id_booking: id_booking,
                time_slot: time_slot
            },
            success: function(response) {
                Swal.close();

                if (!response.success) {
                    Swal.fire({
                        title: response.message,
                        icon: "warning",
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                } else {
                   // تحديث الأوقات المتاحة بعد التحديث
                    Swal.fire({
                        title: response.message,
                        icon: "success",
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    title: "{{ trans('outpatient_trans.error_saving_reservation') }}",
                    icon: "error",
                    text: error || "{{ trans('outpatient_trans.please_try_again') }}",
                    showConfirmButton: true
                });
            }
        });

    }
</script>
