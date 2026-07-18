  <div class="tab-pane fade" id="notes_tab" role="tabpanel">
      <div class="card">
          <div class="card-body">
              <div class="card card-height-100">
                  <div class="card-header align-items-center d-flex">
                      <h4 class="card-title mb-0 flex-grow-1">{{ trans('doctor_trans.notes') }}</h4>
                  </div><!-- end card header -->

                  <div class="card-body p-0">
                      <div id="users-chat">
                          <div class="chat-conversation p-3" id="chat-conversation" data-simplebar
                              style="height: 400px;">
                              <ul class="list-unstyled chat-conversation-list chat-sm" id="get_medical_notes_patient">
                                  
                                     
                                                      
                                              
                              </ul>
                          </div>
                      </div>
                          <div class="border-top border-top-dashed">
                              <div class="row g-2 mx-3 mt-2 mb-3">
                                  <div class="col">
                                      <div class="position-relative">
                                          <input type="text" id="notes_add" name="notes_add"
                                              class="form-control border-light bg-light" placeholder="Enter Message...">
                                      </div>
                                  </div><!-- end col -->
                                  <div class="col-auto">
                                      <button id="saveButton_notes" class="btn btn-info"><span
                                              class="d-none d-sm-inline-block me-2">{{ trans('doctor_trans.save') }}</span> <i
                                              class="mdi mdi-send float-end"></i>
                                        </button>
                                  </div><!-- end col -->
                              </div><!-- end row -->
                          </div>
                  </div><!-- end cardbody -->
              </div><!-- end card -->
          </div>
      </div>
  </div>
