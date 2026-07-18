<div class="row g-3">
    <div class="col-md-6">
        <label>{{ __('patient_trans.emergency_name') }}</label>
        <input type="text" name="emergency_contact_name" class="form-control"
               value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}"
               placeholder="{{ __('patient_trans.enter_name') }}">
    </div>
    <div class="col-md-6">
        <label>{{ __('patient_trans.emergency_relation') }}</label>
        <input type="text" name="emergency_contact_relation" class="form-control"
               value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation ?? '') }}"
               placeholder="{{ __('patient_trans.enter_relation') }}">
    </div>
    <div class="col-md-6">
        <label>{{ __('patient_trans.emergency_phone') }}</label>
        <input type="tel" name="emergency_contact_phone" class="form-control"
               value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}"
               placeholder="{{ __('patient_trans.enter_phone') }}">
    </div>
    <div class="col-md-6">
        <label>{{ __('patient_trans.emergency_address') }}</label>
        <textarea name="emergency_contact_address" class="form-control" rows="2"
                  placeholder="{{ __('patient_trans.enter_address') }}">{{ old('emergency_contact_address', $patient->emergency_contact_address ?? '') }}</textarea>
    </div>
</div>
