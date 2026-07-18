<!-- Section: Attachments -->
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <i class="bx bx-paperclip"></i> {{ trans('inpatient_trans.attachments_section') }}
        </div>
        <div class="card-body">
            <div class="row">
                <!-- بطاقة المريض - الوجه -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-id-card"></i> {{ trans('inpatient_trans.patient_id_front') }}
                        </label>
                        <input type="file" name="patient_id_front" class="form-control" accept="image/*">
                        <small class="form-text text-muted">{{ trans('inpatient_trans.patient_id_front_help') }}</small>
                    </div>
                </div>

                <!-- بطاقة المريض - الظهر -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-id-card"></i> {{ trans('inpatient_trans.patient_id_back') }}
                        </label>
                        <input type="file" name="patient_id_back" class="form-control" accept="image/*">
                        <small class="form-text text-muted">{{ trans('inpatient_trans.patient_id_back_help') }}</small>
                    </div>
                </div>

                <!-- بطاقة المرافق - الوجه -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-id-card"></i> {{ trans('inpatient_trans.companion_id_front') }}
                        </label>
                        <input type="file" name="companion_id_front" class="form-control" accept="image/*">
                        <small class="form-text text-muted">{{ trans('inpatient_trans.companion_id_front_help') }}</small>
                    </div>
                </div>

                <!-- بطاقة المرافق - الظهر -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-id-card"></i> {{ trans('inpatient_trans.companion_id_back') }}
                        </label>
                        <input type="file" name="companion_id_back" class="form-control" accept="image/*">
                        <small class="form-text text-muted">{{ trans('inpatient_trans.companion_id_back_help') }}</small>
                    </div>
                </div>

                <!-- أمر الدخول -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-file"></i> {{ trans('inpatient_trans.admission_order') }}
                        </label>
                        <input type="file" name="admission_order" class="form-control" accept="image/*,application/pdf">
                        <small class="form-text text-muted">{{ trans('inpatient_trans.admission_order_help') }}</small>
                    </div>
                </div>

                <!-- التقرير الطبي -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-file-medical"></i> {{ trans('inpatient_trans.medical_report') }}
                        </label>
                        <input type="file" name="medical_report" class="form-control" accept="image/*,application/pdf">
                        <small class="form-text text-muted">{{ trans('inpatient_trans.medical_report_help') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
