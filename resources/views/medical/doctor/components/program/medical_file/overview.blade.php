<div class="tab-pane " id="overview-tab" role="tabpanel">
    <div class="row">
        <div class="col-xxl-3">
            <!-- Profile Completion Card -->
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title mb-4">{{ trans('doctor_trans.Complete_Profile') }}</h5>
                    <div class="progress animated-progress custom-progress progress-label">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 30%" aria-valuenow="30"
                            aria-valuemin="0" aria-valuemax="100">
                            <div class="label">30%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accordion -->
            <div class="accordion accordion-flush mt-3" id="doctorInfoAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingComplaining">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseComplaining" aria-expanded="false">
                            <i class="bx bx-comment-detail me-2"></i>{{ trans('doctor_trans.complaining') }}
                        </button>
                    </h2>
                    <div id="collapseComplaining" class="accordion-collapse collapse"
                        data-bs-parent="#doctorInfoAccordion">
                        <div class="accordion-body">
                            <!-- Complaining Details Here -->
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFinding">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFinding" aria-expanded="false">
                            <i class="bx bx-search-alt me-2"></i>{{ trans('doctor_trans.finding') }}
                        </button>
                    </h2>
                    <div id="collapseFinding" class="accordion-collapse collapse" data-bs-parent="#doctorInfoAccordion">
                        <div class="accordion-body">
                            <!-- Finding Details Here -->
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingHistory">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseHistory" aria-expanded="false">
                            <i class="bx bx-history me-2"></i>{{ trans('doctor_trans.medical_history') }}
                        </button>
                    </h2>
                    <div id="collapseHistory" class="accordion-collapse collapse" data-bs-parent="#doctorInfoAccordion">
                        <div class="accordion-body">
                            <!-- medical History Details Here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-9">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0">{{ trans('doctor_trans.doctor_order_overview') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="bx bxs-capsule align-middle me-2"></i>
                                {{ trans('doctor_trans.prescription') }}
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="ri-flask-fill align-middle me-2"></i> {{ trans('doctor_trans.lab') }}
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="ri-registered-fill align-middle me-2"></i> {{ trans('doctor_trans.rad') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
