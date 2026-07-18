<div class="row">
    <!-- Location Section -->
    
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.country') }}</label>
        <input disabled type="text" class="form-control"  value="{{ old('full_address', $patient->countries->name??'') }}" />
    </div>
</div>
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.governorate') }}</label>
        <input disabled type="text" class="form-control"  value="{{ old('full_address', $patient->government->name??'') }}" />
    </div>
</div>
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.city') }}</label>
        <input disabled type="text" class="form-control"  value="{{ old('full_address', $patient->getcity->name ?? '') }}" />
    </div>
</div>
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.area') }}</label>
        <input disabled type="text" class="form-control"  value="{{ old('full_address', $patient->getarea->name ?? '') }}" />
    </div>
</div>
    <div class="col-md-12 mb-4">
        <h5 class="mb-3">{{ trans('patient_trans.location_info') }}</h5>
       <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="id_country" class="form-label required">{{ trans('patient_trans.country') }}</label>
                    <select id="country_required" class="js-example-basic-single" name="country_required" required>
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                        @foreach ($country as $data)
                            <option value="{{ $data->id }}" {{ old('id_country') == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">{{ trans('patient_trans.country_required') }}</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="id_governmentes" class="form-label">{{ trans('patient_trans.governorate') }}</label>
                    <select id="id_governmentes" name="id_governmentes"  class="js-example-basic-single"
                                onselect="get_city($(this).val())" onchange="get_city($(this).val())"
                                onvolumechange="get_city($(this).val())">
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="id_city" class="form-label">{{ trans('patient_trans.city') }}</label>
                    <select id="id_city" name="id_city" class="js-example-basic-single"
                                onselect="get_area($(this).val())" onchange="get_area($(this).val())"
                                onvolumechange="get_area($(this).val())">
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="id_area" class="form-label">{{ trans('patient_trans.area') }}</label>
                    <select id="id_area" name="id_area" class="js-example-basic-single" >
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="full_address" class="form-label">{{ trans('patient_trans.full_address') }}</label>
                    <input type="text" class="form-control"  
                           placeholder="{{ trans('patient_trans.enter_full_address') }}" 
                           id="full_address" name="full_address" value="{{ old('full_address') }}" />
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Info Section -->
    <div class="col-md-12">
        <h5 class="mb-3">{{ trans('patient_trans.additional_info') }}</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="jop" class="form-label required">{{ trans('patient_trans.jop') }}</label>
                    <select id="jop" class="form-select select2" name="jop_id" required>
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                        @foreach ($jops as $data)
                            <option value="{{ $data->id }}" {{ old('jop_id') == $data->id ? 'selected' : '' }} {{$patient->jop_id == $data->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">{{ trans('patient_trans.jop_required') }}</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="how_to_identify_place" class="form-label required">
                        {{ trans('patient_trans.how_to_identify_place') }}
                    </label>
                    <select id="how_to_identify_place" class="form-select select2" name="how_to_identify_place_id" required>
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                        @foreach ($how_to_identify_place as $data)
                            <option value="{{ $data->id }}" {{ old('how_to_identify_place_id') == $data->id ? 'selected' : '' }} {{$patient->how_to_identify_place_id == $data->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">{{ trans('patient_trans.how_to_identify_required') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

