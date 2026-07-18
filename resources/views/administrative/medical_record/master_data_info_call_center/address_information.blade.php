<div class="row">
    <!-- Location Section -->
    <div class="col-md-12 mb-4">
        <h5 class="mb-3">{{ trans('patient_trans.location_info') }}</h5>
        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="id_country" class="form-label ">{{ trans('patient_trans.country') }}</label>
                    <select id="id_country" class="form-select select2" name="id_country" >
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
                    <select id="id_governmentes" name="id_governmentes" class="form-select select2" disabled>
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="id_city" class="form-label">{{ trans('patient_trans.city') }}</label>
                    <select id="id_city" name="id_city" class="form-select select2" disabled>
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="id_area" class="form-label">{{ trans('patient_trans.area') }}</label>
                    <select id="id_area" name="id_area" class="form-select select2" disabled>
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
                    <label for="jop" class="form-label ">{{ trans('patient_trans.jop') }}</label>
                    <select id="jop" class="form-select select2" name="jop_id" >
                        <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                        @foreach ($jops as $data)
                            <option value="{{ $data->id }}" {{ old('jop_id') == $data->id ? 'selected' : '' }}  >
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
                            <option value="{{ $data->id }}" {{ old('how_to_identify_place_id') == $data->id ? 'selected' : '' }}>
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

<script>
$(document).ready(function() {
    // Initialize select2
 
    // Country change event
    $('#id_country').change(function() {
        const countryId = $(this).val();
        $('#id_governmentes').prop('disabled', !countryId);
        
        if (countryId) {
            $.get('/get-governorates/' + countryId, function(data) {
                $('#id_governmentes').html('<option value="">' + $('#id_governmentes').data('select-option') + '</option>');
                $.each(data, function(key, value) {
                    $('#id_governmentes').append('<option value="'+key+'">'+value+'</option>');
                });
            });
        } else {
            $('#id_governmentes').html('<option value="">' + $('#id_governmentes').data('select-option') + '</option>');
            $('#id_city, #id_area').prop('disabled', true).html('<option value="">' + $('#id_city').data('select-option') + '</option>');
        }
    });
    
    // Governorate change event
    $('#id_governmentes').change(function() {
        const govId = $(this).val();
        $('#id_city').prop('disabled', !govId);
        
        if (govId) {
            $.get('/get-cities/' + govId, function(data) {
                $('#id_city').html('<option value="">' + $('#id_city').data('select-option') + '</option>');
                $.each(data, function(key, value) {
                    $('#id_city').append('<option value="'+key+'">'+value+'</option>');
                });
            });
        } else {
            $('#id_city, #id_area').prop('disabled', true).html('<option value="">' + $('#id_city').data('select-option') + '</option>');
        }
    });
    
    // City change event
    $('#id_city').change(function() {
        const cityId = $(this).val();
        $('#id_area').prop('disabled', !cityId);
        
        if (cityId) {
            $.get('/get-areas/' + cityId, function(data) {
                $('#id_area').html('<option value="">' + $('#id_area').data('select-option') + '</option>');
                $.each(data, function(key, value) {
                    $('#id_area').append('<option value="'+key+'">'+value+'</option>');
                });
            });
        } else {
            $('#id_area').prop('disabled', true).html('<option value="">' + $('#id_area').data('select-option') + '</option>');
        }
    });
    
    // Form validation
    $('form').submit(function(e) {
        let isValid = true;
        
        // Validate required fields
        $('.required').each(function() {
            const $field = $(this).find('select, input');
            if (!$field.val()) {
                $field.addClass('is-invalid');
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
            }
        });
      
    });
});
</script>