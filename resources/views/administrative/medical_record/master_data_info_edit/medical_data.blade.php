<div class="row g-3">
    <!-- فصيلة الدم -->
    <div class="col-md-3">
        <div class="form-floating">
            <select name="blood_type" id="bloodType" class="form-select">
                <option value="">{{ __('patient_trans.select_option') }}</option>
                <option value="A+" {{ old('blood_type', $patient->blood_type ?? '') == 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ old('blood_type', $patient->blood_type ?? '') == 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ old('blood_type', $patient->blood_type ?? '') == 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ old('blood_type', $patient->blood_type ?? '') == 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ old('blood_type', $patient->blood_type ?? '') == 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ old('blood_type', $patient->blood_type ?? '') == 'AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ old('blood_type', $patient->blood_type ?? '') == 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ old('blood_type', $patient->blood_type ?? '') == 'O-' ? 'selected' : '' }}>O-</option>
            </select>
            <label for="bloodType" class="text-muted">
                <i class="fas fa-tint me-2"></i>{{ __('patient_trans.blood_type') }}
            </label>
        </div>
    </div>

    <!-- الحساسية -->
    <div class="col-md-4">
        <div class="form-floating">
            <input type="text" name="allergies" id="allergies" class="form-control"
                   placeholder="{{ __('patient_trans.enter_allergies') }}"
                   value="{{ old('allergies', $patient->allergies ?? '') }}">
            <label for="allergies" class="text-muted">
                <i class="fas fa-allergies me-2"></i>{{ __('patient_trans.allergies') }}
            </label>
        </div>
    </div>

    <!-- الأمراض المزمنة -->
    <div class="col-md-5">
        <div class="form-floating">
            <input type="text" name="chronic_diseases" id="chronicDiseases" class="form-control"
                   placeholder="{{ __('patient_trans.enter_chronic_diseases') }}"
                   value="{{ old('chronic_diseases', $patient->chronic_diseases ?? '') }}">
            <label for="chronicDiseases" class="text-muted">
                <i class="fas fa-notes-medical me-2"></i>{{ __('patient_trans.chronic_diseases') }}
            </label>
        </div>
    </div>
</div>
