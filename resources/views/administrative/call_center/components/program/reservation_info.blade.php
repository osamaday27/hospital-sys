<div class="card">
    <!-- ننقل هذا الجزء للأعلى ونضيف له فاصل -->
    <div id="serviceMessage" class="alert alert-danger" style="display:none; margin:15px; border-left:4px solid #f44336;">
        <div class="d-flex align-items-center">
            <i class="ri-alarm-warning-fill me-2" style="font-size:1.5rem;"></i>
            <div id="warningContent"></div>
        </div>
    </div>
    <div class="card-header">
        <div class="row">
            <div class="col-4">
                <div class="mb-3">
                    <label for="clinc_id" class="form-label" style="margin-bottom: 12px;">
                        {{ trans('outpatient_trans.choose_clinc') }}
                    </label>
                    <select class="js-example-basic-single" name="clinc_id" id="clinc_id" style="margin-top: 5px;"
                        onchange="getDoctors();getClinicServices();">
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
                        {{ trans('outpatient_trans.choose_doctor') }}
                    </label>
                    <select onchange="getTimeSlots()" class="js-example-basic-single" name="id_doctor" id="id_doctor"
                        style="margin-top: 5px;">
                        <option selected disabled> {{ trans('outpatient_trans.select_doctor') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-2">
                <div class="mb-3">
                    <label for="lastNameinput" class="form-label" style="margin-bottom: 12px;">
                        {{ trans('outpatient_trans.choose_date') }}</label>
                    <input type="date" class="form-control" id="booking_date" name="booking_date"
                        data-date-format="d M, Y" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                        onchange="getTimeSlots();" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
            </div>
            <div class="col-2">
                <div class="mb-3">
                    <label for="reservation_type" class="form-label" style="margin-bottom: 12px;">
                        {{ trans('outpatient_trans.reservation_type') }}
                    </label>
                    <select class="form-control" name="reservation_type" id="reservation_type">
                        <option value="hospital" selected>{{ trans('outpatient_trans.hospital_case') }}</option>
                        <option value="doctor">{{ trans('outpatient_trans.doctor_case') }}</option>
                    </select>
                </div>
            </div>
              <div class="col-12">
                <div class="mb-3">
                   <input type="text" class="form-control" id="reservation_note" name="reservation_note" placeholder="{{ trans('outpatient_trans.reservation_note') }}" />
                       
                </div>
            </div>
            <p id="show_fist_time"></p>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-5">
                <div class="card ribbon-box border mb-lg-0 right">
                    <div class="card-body">
                        <div class="ribbon ribbon-success ribbon-shape">
                            {{ trans('outpatient_trans.booking_details') }}
                        </div>
                        <label for="lastNameinput" class="form-label">
                            {{ trans('outpatient_trans.payment_details') }}
                        </label>
                        <div class="row">

                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <label for="id_contract" class="form-label" style="margin-top: 8px;color: #000;">
                                        {{ trans('outpatient_trans.choose_visit_opening_service') }}</label>
                                </div>
                                <div class="col-lg-12">
                                    <select class="js-example-basic-single" name="id_opening_service" disabled
                                        id="id_opening_service">
                                        <option selected disabled>
                                            {{ trans('outpatient_trans.choose_visit_opening_service') }}
                                        </option>

                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <label for="id_contract" class="form-label" style="margin-top: 8px;color: #000;">
                                        {{ trans('outpatient_trans.choose_payment_method') }}</label>
                                </div>
                                <div class="col-lg-12">
                                    <select class="js-example-basic-single" name="id_payment_method"
                                        id="id_payment_method" onchange="getTimeSlots();">
                                        <option selected disabled>{{ trans('outpatient_trans.choose_payment_method') }}
                                        </option>
                                        <option value="cash" selected>{{ trans('outpatient_trans.cash') }}</option>
                                        <option value="visa">{{ trans('outpatient_trans.visa') }}</option>
                                        <option value="mastercard">{{ trans('outpatient_trans.mastercard') }}</option>
                                        <option value="bank_transfers">{{ trans('outpatient_trans.bank_transfers') }}
                                        </option>
                                        <option value="wallets">{{ trans('outpatient_trans.wallets') }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-7">
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
    <div class="card-footer ">
        <div class="row">

            <div class="col-4">
                <div class="mb-3">
                    <label for="lastNameinput" class="form-label" style="margin-bottom: 12px;">
                        {{ trans('outpatient_trans.total') }}
                    </label>
                    <input type="text" class="form-control" id="total" name="total" value="0"
                        readonly>
                </div>
            </div>

            <div class="col-4">
                <div class="mb-3">
                    <label for="lastNameinput" class="form-label" style="margin-bottom: 12px;">
                        {{ trans('outpatient_trans.contracting_value') }}
                    </label>
                    <input type="text" class="form-control" id="contracting_value" name="contracting_value"
                        value="0" readonly>
                </div>
            </div>

            <div class="col-4">
                <div class="mb-3">
                    <label for="lastNameinput" class="form-label" style="margin-bottom: 12px;">
                        {{ trans('outpatient_trans.patient_value') }}
                    </label>
                    <input type="text" class="form-control" id="patient_value" name="patient_value"
                        value="0" readonly>
                </div>
            </div>
            <div class="col-sm-12">

                <div>
                    <button type="button" class="btn btn-info w-100" onclick="save_reservation()"> <i
                            class="ri-newspaper-line  me-2 align-bottom"></i>
                        {{ trans('outpatient_trans.create_resvtion') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // تحميل النصوص المترجمة من Laravel
    var nearestAppointmentText = @json(__('outpatient_trans.nearest_appointment'));
    var dayText = @json(__('outpatient_trans.day'));
    var dateText = @json(__('outpatient_trans.date'));
    var firstShiftText = @json(__('outpatient_trans.first_shift'));
    var from = @json(__('outpatient_trans.from'));
    var to = @json(__('outpatient_trans.to'));

    var daysOfWeek = {
        'saturday': @json(__('outpatient_trans.saturday')),
        'sunday': @json(__('outpatient_trans.sunday')),
        'monday': @json(__('outpatient_trans.monday')),
        'tuesday': @json(__('outpatient_trans.tuesday')),
        'wednesday': @json(__('outpatient_trans.wednesday')),
        'thursday': @json(__('outpatient_trans.thursday')),
        'friday': @json(__('outpatient_trans.friday'))
    };
</script>

<script>
    // عند اختيار الطبيب بعد تحميل القائمة
    $('#id_doctor').on('change', function() {
        checkPatientWarnings();
    });

    function checkPatientWarnings() {
        var patientId = $('#patient_id').val();
        var clinicId = $('#clinc_id').val();
        var doctorId = $('#id_doctor').val();

        if (patientId && clinicId && doctorId) {
            $.ajax({
                url: "{{ route('check.patient.no_shows') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    patient_id: patientId,
                    clinic_id: clinicId,
                    doctor_id: doctorId
                },
                success: function(response) {
                    if (response.warning) {
                        $('#warningContent').html(response.warning.replace(/\n/g, "<br>"));
                        $('#serviceMessage').fadeIn();
                    } else {
                        $('#serviceMessage').fadeOut();
                        $('#warningContent').html('');
                    }
                },
                error: function(xhr) {
                    console.error('Error checking warnings:', xhr.responseText);
                    $('#serviceMessage').fadeOut();
                    $('#warningContent').html('');
                }
            });
        } else {
            $('#serviceMessage').fadeOut();
            $('#warningContent').html('');
        }
    }

    $(document).ready(function() {
        // عند اختيار الطبيب من القائمة
        $('#id_doctor').change(function() {
            let doctorId = $(this).val(); // الحصول على معرف الطبيب
            var clinicId = $('#clinc_id').val(); // الحصول على معرف العيادة

            if (doctorId) {
                // عرض رسالة انتظار أثناء تحميل البيانات
                $('#show_fist_time').html('<strong>جاري تحميل الموعد...</strong>');

                // تنفيذ طلب AJAX لجلب الموعد الأقرب للطبيب
                $.ajax({
                    url: '/doctor/nearest-appointment/' + doctorId + '/' +
                        clinicId, // استدعاء الوظيفة عبر المسار
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            let data = response.data;

                            function convertTo12HourFormat(time) {
                                if (!time) return 'غير متاح'; // التحقق من حالة الوقت الفارغ
                                let [hours, minutes] = time.split(':').map(Number);
                                let suffix = hours >= 12 ? 'PM' : 'AM';
                                hours = hours % 12 || 12; // تحويل الساعة 0 إلى 12
                                return `${hours}:${minutes < 10 ? '0' + minutes : minutes}${suffix}`; // إزالة المسافة قبل AM/PM
                            }


                            // تحويل الوقت
                            let firstShiftFrom = convertTo12HourFormat(data
                                .first_shift_from);
                            let firstShiftTo = convertTo12HourFormat(data.first_shift_to);

                            // ترجمة اليوم باستخدام daysOfWeek
                            let translatedDay = daysOfWeek[data.day.toLowerCase()] || data
                                .day;
                            let date = data.nearest_date;

                            // عرض الموعد في العنصر p مع الترجمة
                            $('#show_fist_time').html(`
                                <strong>${nearestAppointmentText}:</strong>
                                <span class="day">${dayText}: ${translatedDay}</span>
                                <span class="date">${dateText}: ${date}</span>
                                <span class="shift">${firstShiftText}:${from} ${firstShiftFrom} ${to} ${firstShiftTo}</span>
                            `);

                        } else {
                            // عرض رسالة إذا لم يكن هناك موعد
                            $('#show_fist_time').html(
                                `<strong>${response.message}</strong>`);
                        }
                    },
                    error: function(xhr) {
                        // عرض رسالة في حالة حدوث خطأ
                        $('#show_fist_time').html(
                            '<strong>فشل في جلب الموعد الأقرب. حاول مرة أخرى.</strong>');
                        console.error(xhr.responseText);
                    }
                });
            } else {
                // إذا لم يتم اختيار طبيب
                $('#show_fist_time').html('<strong>الرجاء اختيار طبيب.</strong>');
            }
        });
    });


    function getDoctors() {
        var clinicId = $('#clinc_id').val(); // الحصول على الـ clinic_id من العنصر
        document.getElementById("id_doctor").disabled = true; // تعطيل حقل الأطباء حتى يتم تحميل البيانات

        // التأكد من وجود clinic_id
        if (clinicId) {
            $.ajax({
                url: '{{ route('get.doctors.by.clinic') }}', // رابط الطلب إلى المسار في Laravel
                type: 'GET',
                data: {
                    clinic_id: clinicId // إرسال الـ clinic_id إلى السيرفر
                },
                success: function(response) {
                    // التحقق من أن response.doctors هو كائن، وتحويله إلى مصفوفة
                    if (response.doctors && typeof response.doctors === 'object') {
                        var doctorsArray = Object.values(response.doctors); // تحويل الكائن إلى مصفوفة

                        // مسح محتويات الـ dropdown للأطباء
                        $('#id_doctor').empty();

                        // إضافة خيار "اختر الطبيب" في القائمة المنسدلة
                        $('#id_doctor').append(
                            '<option selected disabled> {{ trans('outpatient_trans.select_doctor') }} </option>'
                        );

                        // إضافة الأطباء إلى الـ dropdown
                        doctorsArray.forEach(function(doctor) {
                            $('#id_doctor').append('<option value="' + doctor.id + '">' + doctor
                                .full_name + '</option>');
                        });
                    } else {
                        console.error("الاستجابة غير صحيحة: response.doctors ليس كائنًا.");
                    }

                    // تمكين حقل اختيار الطبيب بعد التحميل
                    document.getElementById("id_doctor").disabled = false;
                },
                error: function(xhr) {
                    console.log(xhr.responseText); // عرض الخطأ في حال حدوث مشكلة في الـ AJAX
                }
            });
        }
    }

    function getTimeSlots() {
        var booking_date = $('#booking_date').val();
        var id_doctor = $('#id_doctor').val();
        var id_clinic = $('#clinc_id').val();

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
                    $('#timeSlotsContainer').empty();
                    Swal.fire({
                        title: "{{ trans('outpatient_trans.no_appointments') }}!",
                        icon: "info",
                        showConfirmButton: false,
                        timer: 3500
                    });
                } else {
                    var timeSlots = response.time_slots;
                    var time_slot_reservations = response.time_slot_reservations;
                    $('#timeSlotsContainer').empty();

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

                        $('#timeSlotsContainer').append(
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


    function getClinicServices() {
        var clinicId = $('#clinc_id').val();

        if (clinicId) {
            $.ajax({
                url: "{{ route('get-clinic-services') }}",
                method: 'GET',
                data: {
                    clinic_id: clinicId
                },
                success: function(response) {
                    $('#id_opening_service').empty().append(
                        '<option selected disabled>{{ trans('outpatient_trans.choose_visit_opening_service') }}</option>'
                    );

                    if (response.services.length > 0) {
                        response.services.forEach(function(service) {
                            $('#id_opening_service').append('<option value="' + service.id + '">' +
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
    $(document).ready(function() {
        $('#id_opening_service').change(function() {
            var serviceId = $(this).val(); // الحصول على معرف الخدمة
            var contract_id = $('#contract_id').val();

            if (contract_id) {
                // إرسال طلب إلى الخادم لجلب البيانات
                $.ajax({
                    url: "{{ route('get_price_service') }}",
                    type: "GET",
                    data: {
                        contract_id: contract_id, // يمكن أن تكون القيمة من قاعدة البيانات
                        service_id: serviceId
                    },
                    success: function(response) {
                        if (response.original_price && response.amount_to_pay !==
                            undefined) {
                            // تحويل القيم إلى أرقام صحيحة
                            var total = parseFloat(response.original_price);
                            var patientValue = parseFloat(response.amount_to_pay);
                            var contractingValue = total - patientValue;

                            // التأكد أن المتغيرات هي أرقام صالحة قبل استخدام toFixed
                            if (!isNaN(total) && !isNaN(patientValue) && !isNaN(
                                    contractingValue)) {
                                // تحديث الحقول بالقيم الجديدة
                                $('#total').val(total.toFixed(2));
                                $('#patient_value').val(patientValue.toFixed(2));
                                $('#contracting_value').val(contractingValue.toFixed(2));
                                $('#unpaid').val(patientValue.toFixed(2));
                                $('#discount').val(0);
                                $('#paid').val(0);
                            } else {
                                Swal.fire({
                                    title: "{{ trans('outpatient_trans.Pricing data is incorrect') }}",
                                    icon: "danger",
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        } else {
                            Swal.fire({
                                title: "{{ trans('outpatient_trans.An error occurred while fetching data') }}",
                                icon: "danger",
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: "{{ trans('outpatient_trans.An error occurred connecting to the server') }}",
                            icon: "danger",
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: "{{ trans('outpatient_trans.select_patient') }}",
                    icon: "danger",
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    });
    $(document).ready(function() {
        // تحديد الحقول
        var discountInput = $('#discount');
        var paidInput = $('#paid');
        var unpaidInput = $('#unpaid');
        var totalInput = $('#patient_value'); // تأكد أن لديك هذا الحقل يمثل إجمالي السعر.

        // وظيفة لحساب المبلغ المتبقي
        function calculateRemaining() {
            var total = parseFloat(totalInput.val()); // إجمالي الخدمة
            var discount = parseFloat(discountInput.val()); // الخصم
            var paid = parseFloat(paidInput.val()); // المبلغ المدفوع

            // التأكد من أن القيم هي أرقام صالحة
            if (isNaN(total)) total = 0;
            if (isNaN(discount)) discount = 0;
            if (isNaN(paid)) paid = 0;

            // حساب المبلغ المتبقي
            var remaining = total - discount - paid;

            // التأكد أن المبلغ المتبقي لا يكون سالبًا
            if (remaining < 0) {
                remaining = 0;
            }

            // تحديث حقل المبلغ المتبقي
            unpaidInput.val(remaining.toFixed(2)); // عرض النتيجة بعدد عشري 2
        }

        // استدعاء دالة الحساب عند تغيير القيم في الحقول
        discountInput.on('input', calculateRemaining); // عند تغيير قيمة الخصم
        paidInput.on('input', calculateRemaining); // عند تغيير المبلغ المدفوع


    });


    function save_reservation() {
        var clinicId = $('#clinc_id').val();
        var discount = 0;
        var amount_paid = 0;
        var remaining_amount = $('#patient_value').val();
        var patient_contribution = $('#patient_value').val();
        var contract_contribution = $('#contracting_value').val();
        var total = $('#total').val();
        var id_doctor = $('#id_doctor').val();
        var booking_date = $('#booking_date').val();
        var id_payment_method = $('#id_payment_method').val();
        var id_opening_service = $('#id_opening_service').val();
        var case_type = $('#reservation_type').val();
        var patient_id = $('#patient_id').val();
        var id_contract = $('#contract_id').val();
        var reservation_note = $('#reservation_note').val();
        var time_slot = $('input[name="time_slot"]:checked').val();
        // التحقق من وجود القيم المطلوبة
        if (!clinicId || !patient_id || !id_doctor || !booking_date) {
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

        // إرسال البيانات باستخدام AJAX
        $.ajax({
            url: "{{ route('save.reservation_call_center') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                clinic_id: clinicId,
                discount: discount,
                amount_paid: amount_paid,
                remaining_amount: remaining_amount,
                patient_contribution: patient_contribution,
                contract_contribution: contract_contribution,
                total: total,
                id_doctor: id_doctor,
                booking_date: booking_date,
                id_payment_method: id_payment_method,
                id_opening_service: id_opening_service,
                patient_id: patient_id,
                id_contract: id_contract,
                case_type: case_type,
                reservation_note: reservation_note,
                time_slot: time_slot
            },
            success: function(response) {
                // إخفاء رسالة التحميل
                Swal.close();
                if (response.status === 'error') {
                    Swal.fire({
                        title: response.message,
                        icon: "warning",
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary' // تخصيص اللون باستخدام كلاس CSS
                        }
                    });
                } else {
                    getTimeSlots(); // تحديث الأوقات المتاحة
                    Swal.fire({
                        title: response.message,
                        icon: "success",
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary' // تخصيص اللون باستخدام كلاس CSS
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                // إخفاء رسالة التحميل في حالة حدوث خطأ
                Swal.close();

                // عرض رسالة خطأ للمستخدم
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
<style>
    #serviceMessage {
        transition: all 0.3s ease;
        background-color: #fff8f8;
        border-radius: 4px;
        padding: 12px 15px;
    }

    #serviceMessage .alert-icon {
        color: #f44336;
        margin-right: 10px;
    }

    #serviceMessage .alert-content {
        flex: 1;
    }

    #serviceMessage strong {
        color: #d32f2f;
    }
</style>
