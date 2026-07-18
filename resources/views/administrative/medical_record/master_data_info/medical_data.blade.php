<div class="row g-3">
    <div class="col-md-6">
        <label>{{ __('patient_trans.blood_type') }}</label>
        <select name="blood_type" class="form-select">
            <option value="">{{ __('patient_trans.select_option') }}</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
        </select>
    </div>
    <div class="col-md-6">
        <label>{{ __('patient_trans.chronic_diseases') }}</label>
        <textarea name="chronic_diseases" class="form-control" rows="2"></textarea>
    </div>
    <div class="col-md-6">
        <label>{{ __('patient_trans.allergies') }}</label>
        <textarea name="allergies" class="form-control" rows="2"></textarea>
    </div>
    <div class="col-md-6">
        <label>{{ __('patient_trans.medications') }}</label>
        <textarea name="medications" class="form-control" rows="2"></textarea>
    </div>
</div>
