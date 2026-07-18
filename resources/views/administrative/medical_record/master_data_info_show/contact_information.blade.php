<div class="col-3">
    <div class="mb-3">
        <label for="phonenumberInput" class="form-label">{{ trans('patient_trans.phone_1') }}</label>
        <input onchange="get();" type="tel" name="phone_number" class="form-control"
            placeholder="{{ trans('patient_trans.enter_phone') }}" id="phonenumberInput"
            value="{{ old('phone_number', $patient->phone_number) }}" contenteditable="true" readonly>
    </div>
</div><!--end col-->

<div class="col-3">
    <div class="mb-3">
        <label for="phonenumberInput2" class="form-label">{{ trans('patient_trans.phone_2') }}</label>
        <input onchange="get();" type="tel" name="phone_number2" class="form-control"
            placeholder="{{ trans('patient_trans.enter_phone') }}" id="phonenumberInput2"
            value="{{ old('phone_number2', $patient->phone_number2) }}" contenteditable="true" readonly>
    </div>
</div><!--end col-->

<div class="col-3">
    <div class="mb-3">
        <label for="whatsappInput" class="form-label">{{ trans('patient_trans.whatsapp') }}</label>
        <input onchange="get();" type="tel" name="whatsapp" class="form-control"
            placeholder="{{ trans('patient_trans.enter_phone') }}" id="whatsappInput"
            value="{{ old('whatsapp', $patient->whatsapp) }}" contenteditable="true" readonly>
    </div>
</div><!--end col-->

<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.best_way_communicate') }}</label>
        <select onchange="get();" id="ForminputState" class="form-select" name="the_best_way_to_communicate" disabled>
            <option value="" selected>
                {{ trans('patient_trans.select_option') }}
            </option>
            <option value="phone_1" {{ old('the_best_way_to_communicate', $patient->the_best_way_to_communicate) == 'phone_1' ? 'selected' : '' }}>
                {{ trans('patient_trans.Phone_1') }}
            </option>
            <option value="phone_2" {{ old('the_best_way_to_communicate', $patient->the_best_way_to_communicate) == 'phone_2' ? 'selected' : '' }}>
                {{ trans('patient_trans.Phone_2') }}
            </option>
            <option value="whatsapp" {{ old('the_best_way_to_communicate', $patient->the_best_way_to_communicate) == 'whatsapp' ? 'selected' : '' }}>
                {{ trans('patient_trans.whatsapp') }}
            </option>
        </select>
    </div>
</div><!--end col-->
