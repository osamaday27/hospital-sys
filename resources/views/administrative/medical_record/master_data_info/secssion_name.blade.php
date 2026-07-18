<div class="card patient-form-card">
   
    <div class="card-body">
        <div class="row">
            <!-- الصورة الشخصية -->
            <div class="col-md-3">
                <div class="profile-section text-center p-3 border-end">
                    <div class="profile-image-container mb-3">
                        <div class="position-relative d-inline-block">
                            <img src="{{ asset('assets/images/users/user-dummy-img.jpg') }}"
                                class="rounded-circle avatar-xl img-thumbnail user-profile-image shadow" 
                                alt="user-profile-image" id="profile-image-preview">
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                <input id="profile-img-file-input" type="file" class="profile-img-file-input d-none" 
                                    accept="image/png, image/jpeg" name="img_url" onchange="previewImage(this)">
                                <label for="profile-img-file-input" class="profile-photo-edit avatar-xs cursor-pointer">
                                    <span class="avatar-title rounded-circle bg-primary text-white">
                                        <i class="ri-camera-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <h5 class="text-muted mb-3">{{ trans('patient_trans.profile_photo') }}</h5>
                    
                    <!-- حقل التأمين -->
                    <div class="insurance-field mt-4 required-field">
                        <label for="ForminputState" class="form-label fw-bold required-label">{{ trans('patient_trans.contracting') }}</label>
                        <select id="ForminputState" class="form-select select2-insurance" 
                                name="insurance_contracting" onchange="handleInsuranceChange()" required>
                            <option value="" selected disabled>{{ trans('patient_trans.select_option') }}</option>
                            @foreach ($contract as $data)
                                <option value="{{ $data->id }}" {{ old('insurance_contracting') == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                    </div>
                </div>
            </div>

            <!-- معلومات المريض -->
            <div class="col-md-9">
                <div class="patient-details-section">
                    <!-- الاسم الكامل -->
                    <div class="full-name-section mb-4 required-field">
                        <label for="full_name" class="form-label fw-bold required-label">{{ trans('patient_trans.full_name') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="ri-user-line"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg" 
                                   id="full_name" name="full_name" required   value="{{ old('full_name') }}"
                                   placeholder="{{ trans('patient_trans.enter_full_name') }}"
                                   oninput="splitAndTranslateName(this.value)">
                        </div>
                        <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                    </div>

                    <!-- الأسماء العربية -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="section-title bg-light p-2 mb-3">
                                <i class="ri-translate me-1"></i> {{ trans('patient_trans.arabic_names') }}
                            </h6>
                        </div>
                        <div class="col-md-3 required-field">
                            <div class="mb-3">
                                <label for="first_name_ar" class="form-label required-label">{{ trans('patient_trans.first_name_ar') }}</label>
                                <input type="text" class="form-control" name="first_name_ar" 
                                       id="first_name_ar" required placeholder="الاسم الأول" value="{{ old('first_name_ar') }}">
                                <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 required-field">
                            <div class="mb-3">
                                <label for="second_name_ar" class="form-label required-label">{{ trans('patient_trans.second_name_ar') }}</label>
                                <input type="text" class="form-control" name="second_name_ar" 
                                       id="second_name_ar" required placeholder="الاسم الثاني" value="{{ old('second_name_ar') }}">
                                <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 required-field">
                            <div class="mb-3">
                                <label for="third_name_ar" class="form-label required-label">{{ trans('patient_trans.third_name_ar') }}</label>
                                <input type="text" class="form-control" name="third_name_ar" 
                                       id="third_name_ar" required placeholder="الاسم الثالث" value="{{ old('third_name_ar') }}">
                                <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 required-field">
                            <div class="mb-3">
                                <label for="fourth_name_ar" class="form-label required-label">{{ trans('patient_trans.fourth_name_ar') }}</label>
                                <input type="text" class="form-control" required name="fourth_name_ar" 
                                       id="fourth_name_ar"  placeholder="الاسم الرابع" value="{{ old('fourth_name_ar') }}">
                                <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- الأسماء الإنجليزية -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="section-title bg-light p-2 mb-3">
                                <i class="ri-global-line me-1"></i> {{ trans('patient_trans.english_names') }}
                            </h6>
                        </div>
                        <div class="col-md-3 required-field">
                            <div class="mb-3">
                                <label for="first_name_en" class="form-label required-label">{{ trans('patient_trans.first_name_en') }}</label>
                                <input type="text" class="form-control" name="first_name_en"   value="{{ old('first_name_en') }}"
                                       id="first_name_en" required placeholder="First Name">
                                <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 required-field">
                            <div class="mb-3">
                                <label for="second_name_en" class="form-label required-label">{{ trans('patient_trans.second_name_en') }}</label>
                                <input type="text" class="form-control" name="second_name_en"  value="{{ old('second_name_en') }}"
                                       id="second_name_en" required placeholder="Second Name">
                                <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 required-field">
                            <div class="mb-3">
                                <label for="third_name_en" class="form-label required-label">{{ trans('patient_trans.third_name_en') }}</label>
                                <input type="text" class="form-control" name="third_name_en"  value="{{ old('third_name_en') }}"
                                       id="third_name_en" required placeholder="Third Name">
                                <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 required-field">
                            <div class="mb-3">
                                <label for="fourth_name_en" class="form-label required-label">{{ trans('patient_trans.fourth_name_en') }}</label>
                                <input type="text" class="form-control" name="fourth_name_en"  value="{{ old('fourth_name_en') }}"
                                       id="fourth_name_en" required  placeholder="Fourth Name">
                                <small class="text-muted required-hint">{{ trans('patient_trans.required_field') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .patient-form-card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }
    
    .profile-section {
        background-color: #f8f9fa;
        border-radius: 10px;
        height: 100%;
    }
    
    .profile-image-container {
        position: relative;
        margin: 0 auto;
        width: fit-content;
    }
    
    .user-profile-image {
        border: 3px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .user-profile-image:hover {
        transform: scale(1.05);
        border-color: #0d6efd;
    }
    
    .profile-photo-edit {
        position: absolute;
        bottom: 10px;
        right: 10px;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .section-title {
        border-radius: 5px;
        font-size: 0.9rem;
        color: #495057;
    }
    
    .select2-insurance {
        width: 100% !important;
    }
    
    .insurance-field {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .full-name-section .input-group-text {
        min-width: 45px;
        justify-content: center;
    }
    
    /* أنماط للحقول المطلوبة */
    .required-field input:required,
    .required-field select:required {
        border-left: 3px solid #dc3545 !important;
        background-color: rgba(220, 53, 69, 0.05) !important;
    }

    /* أنماط للحقول المطلوبة بعد الإدخال الصحيح */
    .required-field input:required:valid, 
    .required-field select:required:valid {
        border-left: 3px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.05) !important;
    }

    /* أنماط للحقول عند التركيز */
    .required-field input:required:focus,
    .required-field select:required:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }

    .required-field input:required:valid:focus, 
    .required-field select:required:valid:focus {
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
    }

    /* تنسيق علامة النجمة */
    .required-label::after {
        content: " *";
        color: #dc3545;
        font-size: 1em;
    }

    /* إخفاء النص المساعد عند الإدخال الصحيح */
    .required-field input:valid + .required-hint,
    .required-field select:valid + .required-hint {
        display: none;
    }

    /* أنماط إضافية للتحقق */
    .is-invalid {
        border-left: 3px solid #dc3545 !important;
        background-color: rgba(220, 53, 69, 0.05) !important;
    }

    .is-valid {
        border-left: 3px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.05) !important;
    }

    .is-invalid:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        border-color: #dc3545 !important;
    }

    .is-valid:focus {
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
        border-color: #28a745 !important;
    }
    
    @media (max-width: 768px) {
        .profile-section {
            border-right: none;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }
    }
</style>

<script>
    // معاينة الصورة المختارة
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-image-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // دالة لتحويل الأسماء لتصبح موحدة
    function normalizeName(name) {
        return name.replace(/[أإآ]/g, 'ا');
    }

    // دالة تقسيم الاسم وترجمته
    async function splitAndTranslateName(fullName) {
        const normalizedFullName = normalizeName(fullName.trim());
        const nameParts = normalizedFullName.split(/\s+/);

        // ملء الحقول العربية
        updateArabicFields(nameParts);

        // ترجمة الأسماء إلى الإنجليزية
        const translatedParts = await translateToEnglish(nameParts);

        // ملء الحقول الإنجليزية
        updateEnglishFields(translatedParts);
    }

    // دالة لملء الحقول العربية
    function updateArabicFields(nameParts) {
        document.getElementById('first_name_ar').value = nameParts[0] || '';
        document.getElementById('second_name_ar').value = nameParts[1] || '';
        document.getElementById('third_name_ar').value = nameParts[2] || '';
        document.getElementById('fourth_name_ar').value = nameParts[3] || '';
        if (nameParts.length > 4) {
            document.getElementById('fourth_name_ar').value += " " + nameParts.slice(4).join(' ');
        }
    }

    // دالة لملء الحقول الإنجليزية
    function updateEnglishFields(nameParts) {
        document.getElementById('first_name_en').value = nameParts[0] || '';
        document.getElementById('second_name_en').value = nameParts[1] || '';
        document.getElementById('third_name_en').value = nameParts[2] || '';
        document.getElementById('fourth_name_en').value = nameParts[3] || '';
    }

    // توليد الأسماء العربية تلقائياً
    function autoFillArabicNames() {
        const fullName = document.getElementById('full_name').value.trim();
        if (fullName) {
            const normalizedFullName = normalizeName(fullName);
            const nameParts = normalizedFullName.split(/\s+/);
            updateArabicFields(nameParts);
        }
    }

    async function translateToEnglish(nameParts) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            const response = await fetch('/translate-names', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ names: nameParts }),
            });

            if (!response.ok) {
                throw new Error('Failed to fetch translations');
            }

            const translations = await response.json();
            return nameParts.map(name => translations[name] || name);
        } catch (error) {
            console.error('Error during translation:', error);
            return nameParts;
        }
    }

    // دالة للتعامل مع تحديث التأمين
    function handleInsuranceChange() {
        const selectElement = document.getElementById('ForminputState');
        console.log("تم تغيير شركة التأمين إلى:", selectElement.options[selectElement.selectedIndex].text);
    }

    // تهيئة Select2 لحقل التأمين
    $(document).ready(function() {
     
        // إضافة حدث blur لجميع الحقول المطلوبة
        document.querySelectorAll('input[required], select[required]').forEach(input => {
            input.addEventListener('blur', function() {
                this.reportValidity();
                // إضافة أو إزالة class بناءً على الصحة
                if (this.checkValidity()) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                }
            });
        });

        // التحقق من الصحة عند إرسال النموذج
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredInputs = document.querySelectorAll('input[required], select[required]');
            let isValid = true;
            
            requiredInputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // يمكنك إضافة رسالة تنبيه هنا إذا لزم الأمر
                alert('الرجاء ملء جميع الحقول المطلوبة بشكل صحيح');
            }
        });
    });
</script>