<div class="row mt-3">

    <div class="mb-3">
        <label for="employee_type" class="form-label">{{ trans('hr.employee_type') }}</label>
        <select id="employee_type" class="js-example-basic-single" name="employee_type" onchange="get();">
            <option value="">--Select--</option>
            <option value="physician">{{ trans('hr.physician') }}</option>
            <option value="nursing">{{ trans('hr.nursing') }}</option>
            <option value="admin">{{ trans('hr.admin') }}</option>
        </select>
    </div>
    <!-- Department -->
    <div class="col-md-6 mb-3">
        <label for="department" class="form-label">{{ trans('hr.department') }}</label>
        <select name="department" id="department" class="js-example-basic-single">
            <option value=""></option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name_ar }}</option>
            @endforeach
        </select>
    </div>

    <!-- Section -->
    <div class="col-md-6 mb-3">
        <label for="section" class="form-label">{{ trans('hr.section') }}</label>
        <select name="section" id="section" class="js-example-basic-single">
            <option value=""></option>
            @foreach ($sections as $section)
                <option value="{{ $section->id }}">{{ $section->name_ar }}</option>
            @endforeach
        </select>
    </div>
    <!-- Job Position -->
    <div class="col-md-6 mb-3">
        <label for="job_position" class="form-label">{{ trans('hr.job_position') }}</label>
        <select name="job_position" id="job_position" class="js-example-basic-single">
            <option value=""></option>
            @foreach ($jobs as $job)
                <option value="{{ $job->id }}">{{ $job->name_ar }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-3 mb-3">
    <label for="schedule_id" class="form-label">{{ trans('hr.schedule') }}</label>
    <select id="schedule_id" class="js-example-basic-single" name="schedule_id" onchange="get();">
        <option value="">{{ trans('hr.select_option') }}</option>
        @if(isset($schedules))
            @foreach($schedules as $schedule)
                <option value="{{ $schedule->id }}" {{ old('schedule_id', $employee->schedule_id ?? '') == $schedule->id ? 'selected' : '' }}>
                    {{ $schedule->name }} ({{ number_format($schedule->total_hours ?? 0, 2) }} hrs)
                </option>
            @endforeach
        @endif
    </select>
    </div>

    <!-- Contract Open Date -->
    <div class="col-md-6 mt-3">
        <label for="contract_open_date" class="form-label">{{ trans('hr.contract_open_date') }}</label>
        <input type="date" class="form-control" name="contract_open_date" id="contract_open_date">
    </div>

    <!-- Hiring Date -->
    <div class="col-md-6 mt-3">
        <label for="hiring_date" class="form-label">{{ trans('hr.hiring_date') }}</label>
        <input type="date" class="form-control" name="hiring_date" id="hiring_date">
    </div>

    <!-- Contract End Date -->
    <div class="col-md-6 mt-3">
        <label for="contract_end_date" class="form-label">{{ trans('hr.contract_end_date') }}</label>
        <input type="date" class="form-control" name="contract_end_date" id="contract_end_date">
    </div>
</div>
