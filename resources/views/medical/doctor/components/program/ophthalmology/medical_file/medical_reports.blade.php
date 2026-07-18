    
  <div class="tab-pane fade" id="medical_reports" role="tabpanel">
      <div class="card">
          <div class="card-body">
              <div class="d-flex align-items-center mb-4">
                  <h5 class="card-title flex-grow-1 mb-0">{{ trans('doctor_trans.report') }}</h5>
                  <div class="flex-shrink-0">
                      <input type="text" id="patient_id" name="patient_id" hidden value="">
                      <input type="text" id="id_reservation" name="id_reservation" hidden
                          value="">
                      <button class="btn btn-danger" id="saveButton"><i class="ri-pencil-fill me-1 align-bottom"></i>
                          {{ trans('doctor_trans.create_report') }}</button>
                     <button class="btn btn-info" onclick="printContent()">{{ trans('doctor_trans.print_report') }}</button>
                  </div>
              </div>
              <div class="row mt-2">
                  <div class="col-lg-3">
                      <div class="list-group list-group-fill-success">
                          <a href="#" class="list-group-item list-group-item-action active"><i
                                  class="ri-download-2-fill align-middle me-2"></i>{{ trans('doctor_trans.report') }}</a>
                          <div id="report_save" name="report_save" class="report_save">

                          </div>
                      </div>
                  </div>
                  <div class="col-lg-9">
                      <div class="card">
                          <div class="card-body">
                              <div class="snow-editor editor" id="editor" style="height: 300px;">
                           
                              </div> <!-- end Snow-editor-->
                          </div><!-- end card-body -->
                      </div><!-- end card -->
                  </div>
                  <!-- end col -->
              </div>
              <!-- end row -->
          </div>
      </div>
  </div>
