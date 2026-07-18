<div>
    <h4>تعديل حجز العملية</h4>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Section 1: Booking and Discharge Dates -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header text-white" style="background-color: #1373c3 !important;">
                        <i class="bx bx-calendar"></i> {{ trans('surgical_trans.dates_section') }}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Surgical Service Select -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="surgical_id" class="form-label">
                                        <i class="bx bx-calendar-check"></i>
                                        {{ trans('surgical_trans.surgical') }}
                                    </label>
                                    <select class="form-control js-example-basic-single" id="surgical_id" name="surgical_id" required>
                                        @foreach ($services as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == $surgical_id ? 'selected' : '' }}>
                                                {{ $data->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">{{ trans('surgical_trans.surgical_help') }}</small>
                                </div>
                            </div>

                            <!-- Procedure Side -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="procedure_side" class="form-label">
                                        <i class="bx bx-transfer"></i> {{ trans('surgical_trans.procedure_side') }}
                                    </label>
                                    <select class="form-control js-example-basic-single" id="procedure_side" name="procedure_side" required>
                                        <option value="right" {{ $procedure_side == 'right' ? 'selected' : '' }}>{{ trans('surgical_trans.right') }}</option>
                                        <option value="left" {{ $procedure_side == 'left' ? 'selected' : '' }}>{{ trans('surgical_trans.left') }}</option>
                                        <option value="both" {{ $procedure_side == 'both' ? 'selected' : '' }}>{{ trans('surgical_trans.both') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Booking Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="booking_date" class="form-label">
                                        <i class="bx bx-calendar-alt"></i> {{ trans('surgical_trans.booking_date') }}
                                    </label>
                                    <input type="date" name="booking_date" id="booking_date" class="form-control"
                                        onchange="getTimeSlots();"
                                        value="{{ old('booking_date', $booking_date ?? \Carbon\Carbon::today()->toDateString()) }}"
                                        placeholder="{{ trans('surgical_trans.select_booking_date') }}">
                                    <small class="form-text text-muted">{{ trans('surgical_trans.booking_date_help') }}</small>
                                </div>
                            </div>

                            <!-- Room Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_room" class="form-label">
                                        <i class="bx bx-heart"></i> {{ trans('surgical_trans.room') }}
                                    </label>
                                    <select class="form-control js-example-basic-single" id="id_room" name="id_room" required>
                                        <option value="" disabled selected>{{ trans('surgical_trans.room') }}</option>
                                        @foreach ($rooms as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == $id_room ? 'selected' : '' }}>
                                                {{ App::getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">{{ trans('surgical_trans.room_help') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Slots Section -->
            <div class="col-md-6">
                <div class="card ribbon-box border mb-lg-0 right">
                    <div class="card-body">
                        <div class="ribbon ribbon-info ribbon-shape">
                            {{ trans('outpatient_trans.select_time') }}
                        </div>
                        <label for="lastNameinput" class="form-label">
                            {{ trans('outpatient_trans.choose_time') }}
                        </label>
                        <div id="timeSlotsContainer" class="timeSlotsContainer"
                            style="height: 200px !important;overflow: auto;">
                        </div>
                    </div>
                </div>
            </div>
        

            <!-- Section: Diagnoses -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <i class="bx bx-notepad"></i> {{ trans('surgical_trans.diagnoses') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">
                                <i class="bx bx-search-alt-2"></i> {{ trans('surgical_trans.select_diagnosis') }}
                            </label>
                            <select name="diagnoses[]" id="diagnoses" class="form-control js-example-basic-single" multiple>
                                @foreach ($diagnoses as $diagnosis)
                                    <option value="{{ $diagnosis->id }}" 
                                        {{ in_array($diagnosis->id, $diagnoses_ids) ? 'selected' : '' }}>
                                        {{ $diagnosis->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ trans('surgical_trans.select_existing_diagnosis_help') }}</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bx bx-pencil"></i> {{ trans('surgical_trans.add_custom_diagnosis') }}
                            </label>
                            <textarea name="custom_diagnosis" id="custom_diagnosis" rows="3" class="form-control"
                                placeholder="{{ trans('surgical_trans.write_custom_diagnosis') }}">{{ old('custom_diagnosis', $custom_diagnosis) }}</textarea>
                            <small class="form-text text-muted">{{ trans('surgical_trans.write_if_not_exist') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('surgical_trans.save_changes') }}</button>
    </form>
</div>
