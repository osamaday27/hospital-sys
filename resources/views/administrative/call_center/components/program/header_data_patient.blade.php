<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-soft-warning py-3 border-bottom">
                <div class="row align-items-center">
                    <!-- Patient Avatar and Name -->
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-lg me-3">
                                <div class="avatar-title bg-white rounded-circle p-2 shadow-sm">
                                    <img id="patient-image" alt="Patient" class="avatar-md rounded-circle" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236c757d'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z'/%3E%3C/svg%3E">
                                </div>
                            </div>
                            <div>
                                <input name="patient_id" id="patient_id" type="hidden" value="">
                                <input name="contract_id" id="contract_id" type="hidden" value="">
                                <h4 class="fw-semibold mb-1" id="patient-name">Patient Name</h4>
                                
                            </div>
                        </div>
                    </div>
                    
                    <!-- Patient Details -->
                    <div class="col-md-9">
                        <div class="row g-3">
                            <!-- Row 1 -->
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-info rounded p-2 me-3 text-white">
                                        <i class="bx bx-id-card fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">{{ trans('outpatient_trans.patient_medical_number') }}</small>
                                        <span id="medical_number" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-info rounded p-2 me-3 text-white">
                                        <i class="bx bx-file fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">{{ trans('outpatient_trans.contract_name') }}</small>
                                        <span id="contract_name" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-info rounded p-2 me-3 text-white">
                                        <i class="bx bx-male fs-5 d-none" id="gender_male_icon"></i>
                                        <i class="bx bx-female fs-5 d-none" id="gender_female_icon"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">{{ trans('patient_trans.gender') }}</small>
                                        <span id="gender_text" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-info rounded p-2 me-3 text-white">
                                        <i class="bx bx-flag fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">{{ trans('patient_trans.nationality') }}</small>
                                        <span id="nationality" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Row 2 -->
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-info rounded p-2 me-3 text-white">
                                        <i class="bx bx-cake fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">{{ trans('patient_trans.date_birth') }}</small>
                                        <span id="date_of_birth" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-info rounded p-2 me-3 text-white">
                                        <i class="bx bx-time-five fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">{{ trans('patient_trans.age') }}</small>
                                        <span id="age" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-info rounded p-2 me-3 text-white">
                                        <i class="ri-phone-line fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">{{ trans('patient_trans.phone') }}</small>
                                        <span id="phone_number" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-danger rounded p-2 me-3 text-white">
                                        <i class="ri-phone-line fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">{{ trans('patient_trans.phone') }}</small>
                                        <span id="phone_number2" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Row 3 -->
                            <div class="col-sm-6 col-md-2">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="bg-success rounded p-2 me-3 text-white">
                                        <i class="ri-whatsapp-line fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">WhatsApp</small>
                                        <span id="whatsapp" class="fw-medium">--</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>