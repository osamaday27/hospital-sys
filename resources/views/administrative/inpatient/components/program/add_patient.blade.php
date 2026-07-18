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
                                           @include('administrative.medical_record.master_data_info.secssion_name')
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
                                               @include('administrative.medical_record.master_data_info.gender_age')

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
                                               @include('administrative.medical_record.master_data_info.contact_information')
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
                                               @include('administrative.medical_record.master_data_info.address_information')
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
   <script>
       $(document).ready(function() {
           $("#patientForm").on("submit", function(e) {
               e.preventDefault(); // منع إعادة تحميل الصفحة

               var formData = new FormData(this); // جمع بيانات النموذج

               $.ajax({
                   url: $(this).attr("action"),
                   type: "POST",
                   data: formData,
                   processData: false,
                   contentType: false,
                   beforeSend: function() {
                       $("#responseMessage").html(
                           '<div class="alert alert-info">جارٍ الحفظ...</div>');
                   },
                   success: function(response) {
                       $("#responseMessage").html(
                           '<div class="alert alert-success">تمت إضافة المريض بنجاح</div>');
                       $("#patientForm")[0].reset(); // إعادة تعيين الحقول
                       $("#myModalpatient").modal("hide"); // إغلاق المودال إذا كان موجودًا
                       Swal.fire({
                           title: response.message,
                           icon: "success",
                           showConfirmButton: true,
                           confirmButtonText: 'OK',
                           customClass: {
                               confirmButton: 'btn btn-primary' // تخصيص اللون باستخدام كلاس CSS
                           }
                       });
                       // تحديث حقل البحث باسم المريض الجديد
                       $("input[name='search_for_patient']").val(response.data);
                       return  SearchData();
                   },
                   error: function(xhr) {
                       var errors = xhr.responseJSON.errors;
                       var errorMessage = '<div class="alert alert-danger"><ul>';
                       $.each(errors, function(key, value) {
                           errorMessage += '<li>' + value + '</li>';
                       });
                       errorMessage += "</ul></div>";
                       $("#responseMessage").html(errorMessage);
                   },
               });
           });
       });
   </script>
   <script>
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

        function get() {
            const formId = 'patientForm';
            const completionPercentage = calculateCompletionPercentage(formId);
            console.log(`Completion Percentage: ${completionPercentage}%`);
            const roundedNumber = Math.round(completionPercentage);
            const pr = document.getElementById('pr');
            const element = document.getElementById('wi');
            element.setAttribute('aria-valuenow', roundedNumber);
            element.style.width = `${roundedNumber}%`;
            pr.textContent = `${roundedNumber}%`;
        }

      

    
    </script>
