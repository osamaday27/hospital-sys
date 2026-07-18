<div class="row g-3 mt-3">
    <!-- اسم شخص الطوارئ -->
    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" name="emergency_contact_name" class="form-control"
                   value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}"
                   placeholder="{{ __('patient_trans.enter_name') }}">
            <label class="text-muted">
                <i class="fas fa-user-friends me-2"></i>{{ __('patient_trans.emergency_name') }}
            </label>
        </div>
    </div>

    <!-- صلة القرابة -->
    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" name="emergency_contact_relation" class="form-control"
                   value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation ?? '') }}"
                   placeholder="{{ __('patient_trans.enter_relation') }}">
            <label class="text-muted">
                <i class="fas fa-users me-2"></i>{{ __('patient_trans.emergency_relation') }}
            </label>
        </div>
    </div>

    <!-- رقم هاتف الطوارئ -->
    <div class="col-md-6">
        <div class="form-floating">
            <input type="tel" name="emergency_contact_phone" class="form-control"
                   value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}"
                   placeholder="{{ __('patient_trans.enter_phone') }}">
            <label class="text-muted">
                <i class="fas fa-phone-alt me-2"></i>{{ __('patient_trans.emergency_phone') }}
            </label>
        </div>
    </div>

    <!-- عنوان الطوارئ -->
    <div class="col-md-6">
        <div class="form-floating">
            <textarea name="emergency_contact_address" class="form-control" rows="2"
                      placeholder="{{ __('patient_trans.enter_address') }}">{{ old('emergency_contact_address', $patient->emergency_contact_address ?? '') }}</textarea>
            <label class="text-muted">
                <i class="fas fa-map-marker-alt me-2"></i>{{ __('patient_trans.emergency_address') }}
            </label>
        </div>
    </div>
</div>
