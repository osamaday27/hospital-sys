    <div class="tab-pane fade" id="attachments_tab" role="tabpanel" aria-labelledby="attachments-tab" tabindex="0">
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">{{ trans('doctor_trans.attachments') }}</h6>
                    </div>
                    <div class="flex-shrink-0">
                        <ul class="list-inline card-toolbar-menu d-flex align-items-center mb-0">
                            <li class="list-inline-item">
                                <button class="align-middle minimize-card btn btn-link p-0"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#attachmentsCollapse"
                                        aria-expanded="true"
                                        aria-controls="attachmentsCollapse">
                                    <i class="mdi mdi-minus align-middle"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="attachmentsCollapse" class="card-body collapse show">
                @livewire('patient.files-upload', [
                    'patient_id' => $patient->id,
                    'visit_id'   => $patient_visits->id ?? null
                ])
            </div>
        </div>
    </div>