<div class="row mt-3">
    <!-- Phone -->
    <div class="col-3">
        <div class="mb-3">
            <label for="phone" class="form-label">{{ trans('hr.phone') }}</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="{{ trans('hr.phone') }}" />
        </div>
    </div>

    <!-- Email -->
    <div class="col-3">
        <div class="mb-3">
            <label for="email" class="form-label">{{ trans('hr.email') }}</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="{{ trans('hr.email') }}" />
        </div>
    </div>

    <!-- Landline -->
    <div class="col-3">
        <div class="mb-3">
            <label for="landline" class="form-label">{{ trans('hr.landline') }}</label>
            <input type="text" class="form-control" name="landline" id="landline" placeholder="{{ trans('hr.landline') }}" />
        </div>
    </div>

    <!-- Fax -->
    <div class="col-3">
        <div class="mb-3">
            <label for="fax" class="form-label">{{ trans('hr.fax') }}</label>
            <input type="text" class="form-control" name="fax" id="fax" placeholder="{{ trans('hr.fax') }}" />
        </div>
    </div>

    <!-- Emergency Contact Name -->
    <div class="col-4">
        <div class="mb-3">
            <label for="emergency_name" class="form-label">{{ trans('hr.emergency_name') }}</label>
            <input type="text" class="form-control" name="emergency_name" id="emergency_name" placeholder="{{ trans('hr.emergency_name') }}" />
        </div>
    </div>

    <!-- Emergency Contact Number -->
    <div class="col-4">
        <div class="mb-3">
            <label for="emergency_number" class="form-label">{{ trans('hr.emergency_number') }}</label>
            <input type="text" class="form-control" name="emergency_number" id="emergency_number" placeholder="{{ trans('hr.emergency_number') }}" />
        </div>
    </div>

    <!-- Relationship -->
    <div class="col-4">
        <div class="mb-3">
            <label for="relationship" class="form-label">{{ trans('hr.relationship') }}</label>
            <input type="text" class="form-control" name="relationship" id="relationship" placeholder="{{ trans('hr.relationship') }}" />
        </div>
    </div>
</div>
