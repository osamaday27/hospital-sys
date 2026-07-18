<div class="tab-pane fade" id="service" role="tabpanel">
    <div class="card shadow-sm border-0 ">
        <div class="card-body">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#nav-border-top-home"
                                        role="tab" aria-selected="false">
                                         <h5 class="modal-title" id="myModalLabel">
                                                    {{ trans('outpatient_trans.add_service') }}</h5>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="reload-tab" data-bs-toggle="tab" href="#nav-border-top-profile"
                                        role="tab" aria-selected="false">
                                         <h5 class="modal-title" id="myModalLabel">{{ trans('outpatient_trans.service_list') }}</h5>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="nav-border-top-home" role="tabpanel">
                                    <div class="d-flex">
                                        <div class="flex-grow-1 ms-2">
                                           
                                            <div class="modal-body">
                                                <div class="row">
                                                    <input type="hidden" class="form-control"
                                                        name="contract_id" id="contract_id" value="{{ $patient->insurance_contracting}}"/>
                                                    <input type="hidden" class="form-control" name="service_patient_id"
                                                        id="service_patient_id" />
                                                    <input type="hidden" class="form-control"
                                                        name="service_reservations_id" id="service_reservations_id" />
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label for="second_id" class="form-label"
                                                                style="margin-bottom: 12px;">
                                                                {{ trans('outpatient_trans.sacend_level') }}
                                                            </label>
                                                            <select class="js-example-basic-single" name="second_id"
                                                                id="second_id" style="margin-top: 5px;"
                                                                onchange="getlast_level_service();">
                                                                <option selected disabled>
                                                                    {{ trans('outpatient_trans.sacend_level') }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="service_add" class="form-label"
                                                            style="margin-bottom: 12px;">
                                                            {{ trans('outpatient_trans.choose_service') }}
                                                        </label>
                                                        <select class="js-example-basic-single" name="service_add"
                                                            disabled id="service_add">
                                                            <option selected disabled>
                                                                {{ trans('outpatient_trans.choose_service') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="quantity" class="form-label"
                                                            style="margin-bottom: 12px;">
                                                            {{ trans('outpatient_trans.quantity') }}
                                                        </label>
                                                        <input type="number" class="form-control" name="quantity"
                                                            id="quantity" />
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" style="margin-top: 33px;"
                                                            id="add_service_button"
                                                            class="btn btn-success btn-icon waves-effect waves-light">
                                                            <i class="ri-add-box-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="table-responsive table-card">
                                                        <table class="table table-nowrap table-striped-columns mb-0"
                                                            style="margin-top: 23px;" id="table_service">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th scope="col">
                                                                        {{ trans('outpatient_trans.sacend_level') }}
                                                                    </th>
                                                                    <th scope="col">
                                                                        {{ trans('outpatient_trans.last_level') }}</th>
                                                                    <th scope="col">
                                                                        {{ trans('outpatient_trans.total') }}</th>
                                                                    <th scope="col">
                                                                        {{ trans('outpatient_trans.contracting_value') }}
                                                                    </th>
                                                                    <th scope="col">
                                                                        {{ trans('outpatient_trans.patient_value') }}
                                                                    </th>
                                                                    <th scope="col">
                                                                        {{ trans('outpatient_trans.discount') }}</th>
                                                                    <th scope="col">
                                                                        {{ trans('outpatient_trans.quantity') }}</th>
                                                                    <th scope="col"><i
                                                                            class="ri-delete-bin-line"></i></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <div class="mb-3">
                                                            <label for="total" class="form-label"
                                                                style="margin-bottom: 12px;">
                                                                {{ trans('outpatient_trans.total') }}
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="total_service" name="total_service"
                                                                value="0" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-2">
                                                        <div class="mb-3">
                                                            <label for="contracting_value" class="form-label"
                                                                style="margin-bottom: 12px;">
                                                                {{ trans('outpatient_trans.contracting_value') }}
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="contracting_value_service"
                                                                name="contracting_value_service" value="0"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-2">
                                                        <div class="mb-3">
                                                            <label for="patient_value" class="form-label"
                                                                style="margin-bottom: 12px;">
                                                                {{ trans('outpatient_trans.patient_value') }}
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="patient_value_service"
                                                                name="patient_value_service" value="0" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-2">
                                                        <div class="mb-3">
                                                            <label for="discount" class="form-label"
                                                                style="margin-bottom: 12px;">
                                                                {{ trans('outpatient_trans.discount') }}
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="discount_service" name="discount_service"
                                                                value="0">
                                                        </div>
                                                    </div>

                                                    <div class="col-2">
                                                        <div class="mb-3">
                                                            <label for="paid" class="form-label"
                                                                style="margin-bottom: 12px;">
                                                                {{ trans('outpatient_trans.paid') }}
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="paid_service" name="paid_service" value="0">
                                                        </div>
                                                    </div>

                                                    <div class="col-2">
                                                        <div class="mb-3">
                                                            <label for="unpaid" class="form-label"
                                                                style="margin-bottom: 12px;">
                                                                {{ trans('outpatient_trans.unpaid') }}
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="unpaid_service" name="unpaid_service"
                                                                value="0" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                    {{ trans('buttons.close') }}
                                                </button>
                                                <button type="button" class="btn btn-primary" id="save_button"
                                                    name="save_button">
                                                    {{ trans('buttons.save') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="nav-border-top-profile" role="tabpanel">
                                    <div class="d-flex">
                                         @include('medical.nursing.components.program.medical_file.datatable_sarvice')
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card-body -->
                    </div>
                </div>
                <!--end col-->
            </div>
        </div>
    </div>
</div>

<script>
    function getdataresrv(ContractId, servicePatientId, serviceReservationsId) {
        $('#contract_id').val(ContractId);
        $('#service_patient_id').val(servicePatientId);
        $('#service_reservations_id').val(serviceReservationsId);
    }

    function getsecond_level_service() {
        var frist_level_service = 'outpatient';

        if (frist_level_service) {
            $.ajax({
                url: "{{ route('n_get-second_level_service') }}",
                method: 'GET',
                data: {
                    frist_level_service: frist_level_service
                },
                success: function(response) {
                    $('#second_id').empty().append(
                        '<option selected disabled>{{ trans('outpatient_trans.choose_service') }}</option>'
                    );

                    if (response.services.length > 0) {
                        response.services.forEach(function(service) {
                            $('#second_id').append('<option value="' + service.id + '">' + service
                                .name + '</option>');
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
                    console.error('Error fetching services:', xhr.responseText);
                }
            });
        }
    }

    function getlast_level_service() {
        var second_level_id = $('#second_id').val();

        if (second_level_id) {
            $.ajax({
                url: "{{ route('n_get-last_level_service') }}",
                method: 'GET',
                data: {
                    second_level_id: second_level_id
                },
                success: function(response) {
                    $('#service_add').empty().append(
                        '<option selected disabled>{{ trans('outpatient_trans.choose_service') }}</option>'
                    );

                    if (response.services.length > 0) {
                        response.services.forEach(function(service) {
                            $('#service_add').append('<option value="' + service.id +
                                '" data-price="' + service.price + '">' + service.name +
                                '</option>');
                        });
                        document.getElementById("service_add").disabled = false;
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
                    console.error('Error fetching services:', xhr.responseText);
                }
            });
        }
    }
    $(document).ready(function() {
        // عند الضغط على الزر لإضافة الخدمة
        $('#add_service_button').click(function() {
            var serviceId = $('#service_add').val(); // الحصول على معرف الخدمة
            var contract_id = $('#contract_id').val();; // تحديد معرف العقد

            if ( serviceId) {
                // إرسال طلب إلى الخادم لجلب البيانات
                $.ajax({
                    url: "{{ route('n_get_price_service') }}",
                    type: "GET",
                    data: {
                        contract_id: contract_id, // معرف العقد
                        service_id: serviceId // معرف الخدمة
                    },
                    success: function(response) {
                        if (response.original_price && response.amount_to_pay !==
                            undefined) {
                            // تحويل القيم إلى أرقام صحيحة
                            var total = parseFloat(response.original_price);
                            var patientValue = parseFloat(response.amount_to_pay);
                            var contractingValue = total - patientValue;

                            // التأكد من أن المتغيرات هي أرقام صالحة قبل استخدام toFixed
                            if (!isNaN(total) && !isNaN(patientValue) && !isNaN(
                                    contractingValue)) {
                                // الحصول على الكمية
                                var quantity = parseFloat($('#quantity').val()) || 1;
                                // إضافة الصف إلى الجدول
                                addRowToTable(serviceId, total, patientValue,
                                    contractingValue, 0, quantity);
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
                    title: "{{ trans('outpatient_trans.select_service_requ') }}",
                    icon: "danger",
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    });
    // إضافة صف جديد إلى الجدول
    function addRowToTable(serviceId, total, patientValue, contractingValue, discount, quantity) {
        var newRow = `<tr>
        <td>${$('#second_id option:selected').text()}</td>
        <td><input class="form-control" name="service_id" type="hidden" value="${serviceId}"/>${$('#service_add option:selected').text()}</td>
        <td><input class="form-control" name="total_service_table" type="text" value="${total.toFixed(2)}" readonly/></td>
        <td><input class="form-control" name="contract_service_table" type="text" value="${contractingValue.toFixed(2)}" readonly/></td>
        <td><input class="form-control" name="patien_service_table" type="text" value="${patientValue.toFixed(2)}" readonly/></td>
        <td><input class="form-control" name="discount_service_table" type="text" value="${discount.toFixed(2)}" /></td>
        <td><input class="form-control" name="quantity_service_table" type="number" value="${quantity}" oninput="calculateTotal()"/></td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)"><i class="ri-delete-bin-line"></i></button></td>
    </tr>`;

        $('#table_service tbody').append(newRow);
        calculateTotal(); // إعادة حساب الإجمالي بعد إضافة الصف
    }

    // إزالة صف من الجدول
    function removeRow(button) {
        $(button).closest('tr').remove();
        calculateTotal(); // إعادة حساب الإجمالي بعد إزالة الصف
    }
    // حساب الإجمالي لكل القيم
    function calculateTotal() {
        var total = 0;
        var contractingValue = 0;
        var patientValue = 0;
        var discount = 0;
        var quantity = 0;

        // حساب القيم عبر الصفوف
        $('#table_service tbody tr').each(function() {
            var rowTotal = parseFloat($(this).find('input[name="total_service_table"]').val()) || 0;
            var rowContractingValue = parseFloat($(this).find('input[name="contract_service_table"]').val()) ||
                0;
            var rowPatientValue = parseFloat($(this).find('input[name="patien_service_table"]').val()) || 0;
            var rowDiscount = parseFloat($(this).find('input[name="discount_service_table"]').val()) || 0;
            var rowQuantity = parseFloat($(this).find('input[name="quantity_service_table"]').val()) || 0;

            // تحديث القيم بناءً على الكمية
            var rowTotalWithQuantity = rowTotal * rowQuantity;
            var rowContractingValueWithQuantity = rowContractingValue * rowQuantity;
            var rowPatientValueWithQuantity = rowPatientValue * rowQuantity;
            var rowDiscountWithQuantity = rowDiscount; // مضاعفة الخصم مع الكمية

            // إضافة القيم المحدثة
            total += rowTotalWithQuantity;
            contractingValue += rowContractingValueWithQuantity;
            patientValue += rowPatientValueWithQuantity;
            discount += rowDiscountWithQuantity; // إضافة الخصم المحدث
            quantity += rowQuantity;
        });

        // تحديث الحقول الخاصة بالإجمالي
        $('#total_service').val(total.toFixed(2)); // إجمالي
        $('#contracting_value_service').val(contractingValue.toFixed(2)); // قيمة العقد
        $('#patient_value_service').val(patientValue.toFixed(2)); // قيمة المريض
        $('#discount_service').val(discount.toFixed(2)); // الخصم
        $('#paid_service').val((patientValue - discount).toFixed(2)); // المدفوعات (قيمة المريض بعد الخصم)
        $('#unpaid_service').val((patientValue - discount - $('#paid_service').val()).toFixed(2)); // المبلغ غير المدفوع
    }

    // تحديث الإجمالي عند تغيير الكمية أو الخصم
    $(document).on('input', 'input[name="quantity_service_table"], input[name="discount_service_table"]', function() {
        calculateTotal(); // إعادة حساب الإجمالي عند تعديل الكمية أو الخصم
    });

    $(document).on('input',
        'input[name="quantity_service_table"], input[name="discount_service_table"], input[name="discount_service"], input[name="paid_service"]',
        function() {
            var quantityValue = parseFloat($(this).val()) || 0;
            var discountValue = parseFloat($('input[name="discount_service"]').val()) || 0;
            var totalValue = parseFloat($('#patient_value_service').val()) || 0;
            var paidValue = parseFloat($('#paid_service').val()) || 0;

            // تحقق من عدم إدخال قيم سالبة
            if (quantityValue < 0) {
                $(this).val(0); // إعادة تعيين الكمية إلى 0 إذا كانت سالبة
            }

            // حساب الإجمالي بعد الخصم
            var totalAfterDiscount = totalValue - discountValue;

            // تحقق من أن المدفوع لا يتجاوز الإجمالي بعد الخصم
            if (paidValue > totalAfterDiscount) {
                alert('المبلغ المدفوع لا يمكن أن يتجاوز الإجمالي بعد الخصم');
                // عدم مسح المدفوع بالكامل، وإنما يمكن تعيينه لأقرب قيمة من الإجمالي بعد الخصم
                $('#paid_service').val(totalAfterDiscount.toFixed(
                    2)); // إعادة تعيين المدفوع ليكون قيمة الإجمالي بعد الخصم
                paidValue = totalAfterDiscount; // تعيين قيمة المدفوع لتكون نفس قيمة الإجمالي بعد الخصم
            }

            // حساب المبلغ المتبقي
            var unpaid = totalAfterDiscount - paidValue;

            // تحديث القيم
            $('#unpaid_service').val(unpaid.toFixed(2)); // تحديث المبلغ المتبقي
            $('#paid_service').val(paidValue); // تحديث المدفوع
        });

    $(document).ready(function() {
        // عند الضغط على الزر لحفظ البيانات
        $('#save_button').click(function() {
            // اجمع البيانات من الجدول
            var servicesData = [];

            $('#table_service tbody tr').each(function() {
                var serviceId = $(this).find('input[name="service_id"]').val();
                var total = parseFloat($(this).find('input[name="total_service_table"]')
                    .val()) || 0;
                var contractingValue = parseFloat($(this).find(
                    'input[name="contract_service_table"]').val()) || 0;
                var patientValue = parseFloat($(this).find('input[name="patien_service_table"]')
                    .val()) || 0;
                var discount = parseFloat($(this).find('input[name="discount_service_table"]')
                    .val()) || 0;
                var quantity = parseFloat($(this).find('input[name="quantity_service_table"]')
                    .val()) || 0;

                // جمع البيانات في مصفوفة
                servicesData.push({
                    service_id: serviceId,
                    total: total,
                    contracting_value: contractingValue,
                    patient_value: patientValue,
                    discount: discount,
                    quantity: quantity
                });
            });

            // جمع بيانات إضافية مثل ID الزيارة والعلاج
            var reservation_id = $('#service_reservations_id')
                .val(); // تأكد من وجود الحقل أو استخدم ID الحجز المتاح
            var patient_id = $('#service_patient_id')
                .val(); // تأكد من وجود الحقل أو استخدم ID المريض المتاح
            var total_service = $('#total_service')
                .val(); // تأكد من وجود الحقل أو استخدم ID المريض المتاح
            var contracting_value_service = $('#contracting_value_service')
                .val(); // تأكد من وجود الحقل أو استخدم ID المريض المتاح
            var patient_value_service = $('#patient_value_service')
                .val(); // تأكد من وجود الحقل أو استخدم ID المريض المتاح
            var paid_service = $('#paid_service')
                .val(); // تأكد من وجود الحقل أو استخدم ID المريض المتاح
            var discount_service = $('#discount_service').val();
            var unpaid_service = $('#unpaid_service').val();

            // تأكد من أن هناك بيانات في المصفوفة
            if (servicesData.length > 0 && reservation_id && patient_id) {
                $.ajax({
                    url: "{{ route('n_save_service_data') }}", // تأكد من أن هذه هي المسار الصحيح
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        services: servicesData, // البيانات الخاصة بالخدمات
                        reservation_id: reservation_id, // ID الحجز
                        patient_id: patient_id, // ID المريض
                        total_service: total_service,
                        contracting_value_service: contracting_value_service,
                        patient_value_service: patient_value_service,
                        paid_service: paid_service,
                        unpaid_service: unpaid_service,
                        discount_service: discount_service,

                    },
                    success: function(response) {
                        // هنا يمكنك التعامل مع الاستجابة من الخادم
                        if (response.success) {
                            Swal.fire({
                                title: "{{ trans('outpatient_trans.data_saved_successfully') }}",
                                icon: "success",
                                showConfirmButton: true
                            });
                        } else {
                            Swal.fire({
                                title: "{{ trans('outpatient_trans.error_occurred') }}",
                                icon: "error",
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving data:', error);
                        Swal.fire({
                            title: "{{ trans('outpatient_trans.an_error_occurred') }}",
                            icon: "error",
                            showConfirmButton: true
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: "{{ trans('outpatient_trans.please_fill_all_fields') }}",
                    icon: "warning",
                    showConfirmButton: true
                });
            }
        });
    });
</script>
