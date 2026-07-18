
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.country') }}</label>
        <input disabled type="text" class="form-control"  value="{{ old('full_address', $patient->countries->name??'') }}" readonly/>
    </div>
</div>
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.governorate') }}</label>
        <input disabled type="text" class="form-control"  value="{{ old('full_address', $patient->government->name??'') }}" readonly/>
    </div>
</div>
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.city') }}</label>
        <input disabled type="text" class="form-control"  value="{{ old('full_address', $patient->getcity->name ?? '') }}" readonly/>
    </div>
</div>
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.area') }}</label>
        <input disabled type="text" class="form-control"  value="{{ old('full_address', $patient->getarea->name ?? '') }}" readonly/>
    </div>
</div>

<div class="col-4">
    <div class="mb-3">
        <label for="full_address" class="form-label">{{ trans('patient_trans.full_address') }}</label>
        <input readonly type="text" class="form-control" placeholder="{{ trans('patient_trans.enter_full_address') }}" id="full_address" name="full_address" value="{{ old('full_address', $patient->full_address) }}" />
    </div>
</div>
<div class="col-4">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.jop') }}</label>
        <select id="jop" class="js-example-basic-single" name="jop_id"  disabled
              onchange="get()">
            <option value="" selected>
                {{ trans('patient_trans.select_option') }}
            </option>
            @foreach ($jops as $data)
                <option value="{{ $data->id }}" {{ $patient->jop_id == $data->id ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? $data->name_ar : $data->name_en }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-4">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.how_to_identify_place') }}</label>
        <select id="how_to_identify_place" class="js-example-basic-single" name="how_to_identify_place_id" disabled
            onchange="get()">
            <option value="" selected>
                {{ trans('patient_trans.select_option') }}
            </option>
            @foreach ($how_to_identify_place as $data)
                <option value="{{ $data->id }}" {{ $patient->how_to_identify_place_id == $data->id ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? $data->name_ar : $data->name_en }}</option>
            @endforeach
        </select>
    </div>
</div>

