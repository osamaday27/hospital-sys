<div class="row g-3">
    <!-- رقم الهاتف 1 -->
    <div class="col-md-3">
        <div class="form-floating phone-input">
            <input type="tel" class="form-control" id="phone1Input" name="phone_number" required
                   placeholder="{{ trans('patient_trans.enter_phone') }}" value="{{ old('phone_number', $patient->phone_number) }}"
                   onchange="validatePhoneNumber(this, 'phone1Feedback')">
            <label for="phone1Input" class="text-muted">
                <i class="fas fa-phone-alt me-2"></i>{{ trans('patient_trans.phone_1') }}
            </label>
            <div class="invalid-feedback" id="phone1Feedback"></div>
            <span class="input-icon"><i class="fas fa-mobile-alt"></i></span>
        </div>
    </div>

    <!-- رقم الهاتف 2 -->
    <div class="col-md-3">
        <div class="form-floating phone-input">
            <input type="tel" class="form-control" id="phone2Input" name="phone_number2"
                   placeholder="{{ trans('patient_trans.enter_phone') }}" value="{{ old('phone_number2', $patient->phone_number2) }}" >
            <label for="phone2Input" class="text-muted">
                <i class="fas fa-phone me-2"></i>{{ trans('patient_trans.phone_2') }}
            </label>
            <div class="invalid-feedback" id="phone2Feedback"></div>
            <span class="input-icon"><i class="fas fa-mobile-alt"></i></span>
        </div>
    </div>

    <!-- واتساب -->
    <div class="col-md-3">
        <div class="form-floating whatsapp-input">
            <input type="tel" class="form-control" id="whatsappInput" name="whatsapp"
                   placeholder="{{ trans('patient_trans.enter_phone') }}"    value="{{ old('whatsapp', $patient->whatsapp) }}">
            <label for="whatsappInput" class="text-muted">
                <i class="fab fa-whatsapp me-2"></i>{{ trans('patient_trans.whatsapp') }}
            </label>
            <div class="invalid-feedback" id="whatsappFeedback"></div>
            <span class="input-icon whatsapp-icon"><i class="fab fa-whatsapp"></i></span>
        </div>
    </div>

    <!-- أفضل طريقة للتواصل -->
    <div class="col-md-3">
        <div class="form-floating">
            <select class="form-select" id="communicationMethod" name="the_best_way_to_communicate">
                <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
                <option value="phone1" {{ old('the_best_way_to_communicate', $patient->the_best_way_to_communicate) == 'phone1' ? 'selected' : '' }}>{{ trans('patient_trans.Phone_1') }}</option>
                <option value="phone2" {{ old('the_best_way_to_communicate', $patient->the_best_way_to_communicate) == 'phone2' ? 'selected' : '' }}>{{ trans('patient_trans.Phone_2') }}</option>
                <option value="whatsapp" {{ old('the_best_way_to_communicate', $patient->the_best_way_to_communicate) == 'whatsapp' ? 'selected' : '' }}>{{ trans('patient_trans.whatsapp') }}</option>
            </select>
            <label for="communicationMethod" class="text-muted">
                <i class="fas fa-comments me-2"></i>{{ trans('patient_trans.best_way_communicate') }}
            </label>
        </div>
    </div>
</div>

<style>
    .phone-input, .whatsapp-input {
        position: relative;
    }
    
    .input-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 3;
    }
    
    .whatsapp-icon {
        color: #25D366;
    }
    
    .phone-input .form-control, 
    .whatsapp-input .form-control {
        padding-left: 40px;
    }
    
    .form-floating label {
        padding-left: 40px;
    }
    
    .form-floating>.form-control:focus~label, 
    .form-floating>.form-control:not(:placeholder-shown)~label, 
    .form-floating>.form-select~label {
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        padding-left: 40px;
    }
    
    @media (max-width: 768px) {
        .input-icon {
            left: 15px;
        }
        
        .form-floating label {
            padding-left: 35px;
        }
    }

    /* Force invalid-feedback to show when .d-block is present */
    .invalid-feedback.d-block {
        display: block;
    }
</style>

<script>
function validatePhoneNumber(input, feedbackId) {
    let val = input.value.trim();
    const feedback = document.getElementById(feedbackId);
    // Reset feedback
    feedback.classList.remove('d-block');
    feedback.textContent = '';
    input.classList.remove('is-invalid');
    
    // Keep only digits
    val = val.replace(/\D/g, '');
    input.value = val;
    
    // If empty, no validation
    if (val === '') return true;
    
    // Length must be 11
    if (val.length !== 11) {
        input.classList.add('is-invalid');
        feedback.textContent = 'يجب أن يتكون رقم الهاتف من 11 رقمًا';
        feedback.classList.add('d-block');
        return false;
    }
    
    // Must start with Egyptian prefixes
    if (!/^01(0|1|2|5)/.test(val)) {
        input.classList.add('is-invalid');
        feedback.textContent = 'يجب أن يبدأ الرقم بـ 010 أو 011 أو 012 أو 015';
        feedback.classList.add('d-block');
        return false;
    }
    
    // Valid
    return true;
}

document.getElementById('communicationMethod')
    .addEventListener('change', function() {
        console.log('طريقة التواصل المفضلة: ' + this.value);
    });
</script>
