  <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="bg-soft-warning">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md">
                              <div class="row align-items-center">
                                  <div class="col-md-auto">
                                      <div class="avatar-md mb-md-0 mb-4">
                                          <div class="avatar-title bg-white rounded-circle">
                                              <img id="patient-image" alt="" class="avatar-sm rounded-circle" />
                                          </div>
                                      </div>
                                  </div>
                                  <!--end col-->
                                  <div class="col-md">
                                      <input name="contract_id" id="contract_id" value="" type="hidden">

                                      <h4 class="fw-semibold" id="patient-name"></h4>
                                      <div class="hstack gap-3 flex-wrap">
                                          <div class="text-muted">{{ trans('outpatient_trans.patient_medical_number') }}
                                              : <span id="medical_number"></span>
                                          </div>
                                          <div class="vr"></div>
                                          <div class="text-muted">{{ trans('outpatient_trans.contract_name') }} :
                                              <span id="contract_name" class="fw-medium "></span>
                                          </div>
                                          <div class="vr"></div>
                                          <div class="badge rounded-pill fs-20 mb-1 bg-info" id="gender_male">
                                              <i class="bx bx-male"></i>
                                          </div>
                                          <div class="badge rounded-pill fs-20 mb-1 bg-danger" id="gender_female">
                                              <i class="bx bx-female"></i>
                                          </div>

                                          <div class="vr"></div>
                                          <div class="text-muted">{{ trans('patient_trans.nationality') }} :
                                              <span id="nationality" class="fw-medium"></span>
                                          </div>

                                          <div class="vr"></div>
                                          <div class="text-muted">{{ trans('patient_trans.date_birth') }} :
                                              <span id="date_of_birth" class="fw-medium"></span>
                                          </div>
                                          <div class="vr"></div>
                                          <div class="text-muted">{{ trans('patient_trans.age') }} :
                                              <span id="age" class="fw-medium"></span>
                                          </div>
                                          <div class="vr"></div>
                                          <div class="text-muted"><i class="ri-phone-line"
                                                  style="font-size: larger;color: black;"></i>
                                              <p id="phone_number" class="fw-medium"></p>

                                          </div>
                                          <div class="text-muted">
                                              <i class="ri-phone-line" style="font-size: larger;color: #fa0000;"></i>
                                              <p id="phone_number2" class="fw-medium"></p>
                                          </div>
                                          <div class="text-muted">
                                              <i class="ri-whatsapp-line" style="font-size: larger;color: #0f7900;"></i>
                                              <p id="whatsapp" class="fw-medium"></p>
                                          </div>

                                      </div>
                                  </div>
                              </div>
                          </div>
                          <!-- end card -->
                      </div><!-- end col -->
                  </div><!-- end row -->
