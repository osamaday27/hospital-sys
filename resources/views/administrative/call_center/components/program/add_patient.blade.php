   <!--  Extra Large modal example -->
   <div class="modal fade bs-example-modal-xl" name="myModalpatient" id="myModalpatient" tabindex="-1" role="dialog"
       aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-xl">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="myExtraLargeModalLabel">{{ trans('outpatient_trans.create_patient') }}
                   </h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <form id="patientForm" action="{{ route('medical_record_ajax.store') }}" method="post"
                       enctype="multipart/form-data">
                       {{ csrf_field() }}
                       <div id="responseMessage"></div>
                       <div class="row">
                           <!-- Accordions Bordered -->
                           <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary "
                               id="accordionBordered">
                               <div class="accordion-item shadow bordersys">
                                   <h2 class="accordion-header " id="accordionborderedExample1">
                                       <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                           data-bs-target="#accor_borderedExamplecollapse1" aria-expanded="true"
                                           aria-controls="accor_borderedExamplecollapse1">
                                           {{ trans('patient_trans.name') }}
                                       </button>
                                   </h2>
                                   <div id="accor_borderedExamplecollapse1" class="accordion-collapse collapse show "
                                       aria-labelledby="accordionborderedExample1" data-bs-parent="#accordionBordered">
                                       <div class="accordion-body">
                                           @include('administrative.medical_record.master_data_info_call_center.secssion_name')
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <!-- Accordions Bordered -->
                           <div style="top: 10px;"
                               class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary"
                               id="gender_age">
                               <div class="accordion-item shadow bordersys">
                                   <h2 class="accordion-header" id="gender_ageExample1">
                                       <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                           data-bs-target="#gender_ageExamplecollapse1" aria-expanded="true"
                                           aria-controls="gender_ageExamplecollapse1">
                                           {{ trans('patient_trans.gender_age') }}
                                       </button>
                                   </h2>
                                   <div id="gender_ageExamplecollapse1" class="accordion-collapse collapse show"
                                       aria-labelledby="gender_ageExample1" data-bs-parent="#gender_age">
                                       <div class="accordion-body">
                                           <div class="row">
                                               @include('administrative.medical_record.master_data_info_call_center.gender_age')

                                           </div>
                                       </div>
                                   </div>
                               </div>

                           </div>

                           <div style="top:23px;"
                               class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary"
                               id="contact_information">
                               <div class="accordion-item shadow bordersys">
                                   <h2 class="accordion-header" id="contact_informationExample1">
                                       <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                           data-bs-target="#contact_informationExamplecollapse1" aria-expanded="true"
                                           aria-controls="contact_informationExamplecollapse1">
                                           {{ trans('patient_trans.contact_information') }}
                                       </button>
                                   </h2>
                                   <div id="contact_informationExamplecollapse1"
                                       class="accordion-collapse collapse show"
                                       aria-labelledby="contact_informationExample1"
                                       data-bs-parent="#contact_information">
                                       <div class="accordion-body">
                                           <div class="row">
                                               @include('administrative.medical_record.master_data_info_call_center.contact_information')
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div style="top: 33px;"
                               class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary"
                               id="address_information">
                               <div class="accordion-item shadow bordersys">
                                   <h2 class="accordion-header" id="address _informationExample1">
                                       <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                           data-bs-target="#address_informationExamplecollapse1" aria-expanded="true"
                                           aria-controls="address_informationExamplecollapse1">
                                           {{ trans('patient_trans.address_information') }}
                                       </button>
                                   </h2>
                                   <div id="address_informationExamplecollapse1"
                                       class="accordion-collapse collapse show"
                                       aria-labelledby="address_informationExample1"
                                       data-bs-parent="#address_information">
                                       <div class="accordion-body">
                                           <div class="row">
                                               @include('administrative.medical_record.master_data_info_call_center.address_information')
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="col-lg-12" style="margin-top: 44px;">
                           <div class="card">
                               <div class="card-body">
                                   <div class="d-flex align-items-center mb-5">
                                       <div class="flex-grow-1">
                                           <h5 class="card-title mb-0"> {{ trans('patient_trans.complete_profile') }}
                                           </h5>
                                       </div>
                                   </div>
                                   <div class="progress animated-progress custom-progress progress-label">
                                       <div id="wi" class="progress-bar bg-danger" role="progressbar"
                                           style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                           aria-valuemax="100">
                                           <div class="label" id="pr">0%</div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div class="text-start">
                               <button type="submit"
                                   class="btn btn-primary">{{ trans('patient_trans.add') }}</button>
                           </div>
                       </div>
                   </form>
               </div>
               <div class="modal-footer">
                   <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium"
                       data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>
                       {{ trans('buttons.close') }}</a>
               </div>
           </div><!-- /.modal-content -->
       </div><!-- /.modal-dialog -->
   </div><!-- /.modal -->
  <!-- سكربت الجافاسكربت الكامل -->
<script>
    $(document).ready(function() {
        // 1. تهيئة DataTable مع تفعيل destroy لمنع الخطأ
        var patientTable = $('#patient-table').DataTable({
            destroy: true,            // يسمح بإعادة التهيئة بدون خطأ
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('get-patient') }}",
                data: function(d) {
                    d.search_term = $('.search-box input[name="search_for_patient"]').val();
                }
            },
            columns: [
                { data: 'patient_info', name: 'patient_info' }
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

        // 2. دالة البحث وإعادة تحميل الجدول
        window.SearchData = function() {
            patientTable.ajax.reload();
        };

        // 3. البحث عند الضغط على Enter
        $('.search-box input[name="search_for_patient"]').on('keypress', function(e) {
            if (e.which === 13) {
                SearchData();
            }
        });

        // 4. معالجة إرسال نموذج إضافة المريض عبر AJAX
        $("#patientForm").on("submit", function(e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(form);

            $.ajax({
                url: $(form).attr("action"),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',  // تأكد من استقبال JSON
                beforeSend: function() {
                    $("#responseMessage").html('<div class="alert alert-info">جارٍ الحفظ...</div>');
                },
                success: function(response) {
                    console.log("AJAX response:", response);
                    if (!response.success) {
                        $("#responseMessage").html(
                            '<div class="alert alert-warning">' + response.message + '</div>'
                        );
                        return;
                    }

                    // عرض رسالة النجاح
                    Swal.fire({
                        title: response.message,
                        icon: "success",
                        confirmButtonText: 'حسناً',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });

                    // ضع اسم المريض الجديد في حقل البحث
                    $('.search-box input[name="search_for_patient"]')
                        .val(response.data.full_name);

                    // أعد تحميل الجدول لإظهار السجل الجديد
                    SearchData();

                    // أعد تعيين النموذج وأغلق المودال
                    form.reset();
                    $("#myModalpatient").modal("hide");
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // عرض رسائل التحقق من الصحة
                        var errors = xhr.responseJSON.errors,
                            html = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function(_, msgs) {
                            $.each(msgs, function(__, msg) {
                                html += '<li>' + msg + '</li>';
                            });
                        });
                        html += "</ul></div>";
                        $("#responseMessage").html(html);
                    } else {
                        $("#responseMessage").html(
                            '<div class="alert alert-danger">حدث خطأ، الرجاء المحاولة لاحقاً.</div>'
                        );
                    }
                }
            });
        });
    });
</script>

<script>
    // دوال حساب نسبة اكتمال النموذج
    function calculateCompletionPercentage(formId) {
        const form = document.getElementById(formId);
        const totalFields = form.querySelectorAll('select, input').length;
        const inputFields = form.querySelectorAll('select, input');
        let count = 0;

        inputFields.forEach((input) => {
            if (input.value.trim() !== '') {
                count++;
            }
        });
        return (count / totalFields) * 100;
    }

    function updateProgress() {
        const formId = 'patientForm';
        const completionPercentage = calculateCompletionPercentage(formId);
        const roundedNumber = Math.round(completionPercentage);
        const pr = document.getElementById('pr');
        const bar = document.getElementById('wi');
        bar.setAttribute('aria-valuenow', roundedNumber);
        bar.style.width = `${roundedNumber}%`;
        pr.textContent = `${roundedNumber}%`;
    }

    // استدعاء تحديث الشريط في كل تغيير في الحقول
    document.getElementById('patientForm').addEventListener('input', updateProgress);
</script>
