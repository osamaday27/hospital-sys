<div class="patient-data-form">
    <!-- صف المعلومات الأساسية -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-user-circle me-2"></i>المعلومات الأساسية</h3>
        <div class="row">
            <!-- الجنسية -->
            <div class="col-md-3 mb-3">
                <label for="nationality" class="form-label"><i class="fas fa-globe me-2"></i>الجنسية</label>
                <select class="form-select select2-nationality" id="nationality" name="nationality" >
                    <option value="" selected disabled>اختر الجنسية</option>
                    @foreach ($nationality as $data)
                        <option value="{{ $data->id }}" {{ old('nationality') == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- الرقم القومي -->
            <div class="col-md-3 mb-3">
                <label for="national_id" class="form-label"><i class="fas fa-id-card me-2"></i>الرقم القومي</label>
                <input type="text" class="form-control" id="national_id" name="national_id" 
                       placeholder="14 رقمًا" maxlength="14"
                       oninput="validateNationalId(this); updateDetails()" value="{{ old('national_id') }}">
                <div class="invalid-feedback" id="national_id_feedback"></div>
            </div>
            
            <!-- رقم الجواز -->
            <div class="col-md-3 mb-3">
                <label for="passport_number" class="form-label"><i class="fas fa-passport me-2"></i>رقم الجواز</label>
                <input type="text" class="form-control" id="passport_number" name="passport_number" 
                       placeholder="لغير المصريين" value="{{ old('passport_number') }}">
            </div>
            
            <!-- الحالة الاجتماعية -->
            <div class="col-md-3 mb-3">
                <label for="marital_status" class="form-label"><i class="fas fa-heart me-2"></i>الحالة الاجتماعية</label>
                <select class="form-select" id="marital_status" name="marital_status">
                    <option value="" selected disabled>اختر الحالة</option>
                    <option value="1" {{ old('marital_status') == 1 ? 'selected' : '' }}>أعزب</option>
                    <option value="2" {{ old('marital_status') == 2 ? 'selected' : '' }}>متزوج</option>
                    <option value="3" {{ old('marital_status') == 3 ? 'selected' : '' }}>أرمل</option>
                    <option value="4" {{ old('marital_status') == 4 ? 'selected' : '' }}>مطلق</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- صف المعلومات الشخصية -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-info-circle me-2"></i>المعلومات الشخصية</h3>
        <div class="row">
            <!-- تاريخ الميلاد -->
            <div class="col-md-3 mb-3">
                <label for="date_of_birth" class="form-label"><i class="fas fa-calendar-alt me-2"></i>تاريخ الميلاد</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                       max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                       onchange="calculateAge(this)" value="{{ old('date_of_birth') }}">
            </div>
            
            <!-- العمر -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fas fa-clock me-2"></i>العمر</label>
                <div class="row g-2">
                    <div class="col-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light">سنوات</span>
                            <input type="number" class="form-control" id="age_years"  name="age_years" readonly 
                                   value="{{ old('age_years', 0) }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light">شهور</span>
                            <input type="number" class="form-control" id="age_months" name="age_months" readonly
                                   value="{{ old('age_months', 0) }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light">أيام</span>
                            <input type="number" class="form-control" id="age_days" name="age_days" readonly
                                   value="{{ old('age_days', 0) }}">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- الجنس - التصميم المحسن -->
            <div class="col-md-3 mb-3">
                <label class="form-label required-field"><i class="fas fa-venus-mars me-2"></i>الجنس</label>
                <div class="gender-selection" id="genderContainer">
                    <div class="gender-option">
                        <input type="radio" name="gender" id="genderMale" value="male" class="gender-input"  
                               onchange="validateGender()" {{ old('gender') == 'male' ? 'checked' : '' }}   >
                        <label for="genderMale" class="gender-label">
                            <div class="gender-icon male-icon">
                                <i class="ri-men-fill"></i>
                            </div>
                            <span class="gender-text">ذكر</span>
                        </label>
                    </div>
                    <div class="gender-option">
                        <input type="radio" name="gender" id="genderFemale" value="female" class="gender-input" 
                               onchange="validateGender()" {{ old('gender') == 'female' ? 'checked' : '' }}>
                        <label for="genderFemale" class="gender-label">
                            <div class="gender-icon female-icon">
                                <i class="ri-women-fill"></i>
                            </div>
                            <span class="gender-text">أنثى</span>
                        </label>
                    </div>
                </div>
                <div class="invalid-feedback" id="genderFeedback">يجب اختيار الجنس</div>
            </div>
        </div>
    </div>
</div>

<style>
    .patient-data-form {
        background-color: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    
    .form-section {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .section-title {
        font-size: 18px;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        color: #3498db;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }
    
    .form-label.required-field::after {
        content: " *";
        color: #e74c3c;
    }
    
    .form-label i {
        margin-right: 8px;
        color: #7f8c8d;
    }
    
    .select2-nationality .select2-selection {
        height: 42px;
        display: flex;
        align-items: center;
    }
    
    /* تصميم جديد لاختيار الجنس */
    .gender-selection {
        display: flex;
        gap: 15px;
        padding: 5px;
    }
    
    .gender-option {
        flex: 1;
    }
    
    .gender-input {
        display: none;
    }
    
    .gender-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 10px;
        border-radius: 8px;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e0e0e0;
        text-align: center;
        height: 100%;
    }
    
    .gender-input:checked + .gender-label {
        border-color: #3498db;
        background-color: #e3f2fd;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    }
    
    .gender-icon {
        font-size: 24px;
        margin-bottom: 8px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .male-icon {
        color: #2980b9;
        background-color: #d4e6f1;
    }
    
    .female-icon {
        color: #e91e63;
        background-color: #f8d7e6;
    }
    
    .gender-text {
        font-weight: 500;
        color: #333;
    }
    
    .invalid-feedback {
        display: none;
        color: #e74c3c;
        font-size: 0.875em;
        margin-top: 5px;
    }
    
    .was-validated .gender-input:invalid ~ .invalid-feedback {
        display: block;
    }
    
    .was-validated .gender-input:invalid + .gender-label {
        border-color: #e74c3c;
    }
    
    @media (max-width: 768px) {
        .form-section {
            padding: 15px;
        }
        
        .gender-selection {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
<script>
    // أكواد المحافظات المصرية الصالحة
    const VALID_GOVERNORATE_CODES = [
        '01','02','03','04','11','12','13','14','15','16','17','18','19',
        '21','22','23','24','25','26','27','28','29','31','32','33','34','35','88'
    ];
    
    // التحقق الآني من صحة الرقم القومي
    function validateNationalId(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
        const feedback = document.getElementById("national_id_feedback");
        feedback.classList.remove("d-block");
        feedback.textContent = "";
    
        if (input.value.length > 14) {
            input.value = input.value.slice(0, 14);
        }
    
        if (input.value.length > 0 && input.value.length < 14) {
            input.classList.add("is-invalid");
            feedback.textContent = "يجب أن يتكون الرقم القومي من 14 رقمًا";
            feedback.classList.add("d-block");
            return false;
        }
    
        if (input.value.length === 14) {
            const result = validateEgyptianNationalID(input.value);
            if (!result.isValid) {
                input.classList.add("is-invalid");
                feedback.textContent = result.errorMessage;
                feedback.classList.add("d-block");
                return false;
            }
            input.classList.remove("is-invalid");
            return true;
        }
    
        input.classList.remove("is-invalid");
        return false;
    }
    
    // التحقق بالتفصيل من الرقم القومي
    function validateEgyptianNationalID(id) {
        if (!/^\d{14}$/.test(id)) {
            return { isValid: false, errorMessage: "يجب أن يحتوي الرقم القومي على 14 رقمًا" };
        }
        const century = id[0];
        if (century !== '2' && century !== '3') {
            return { isValid: false, errorMessage: "الرقم الأول في الرقم القومي غير صحيح" };
        }
        const year = (century === '2' ? '19' : '20') + id.substring(1, 3);
        const month = id.substring(3, 5), day = id.substring(5, 7);
        if (!isValidDate(year, month, day)) {
            return { isValid: false, errorMessage: "تاريخ الميلاد في الرقم القومي غير صحيح" };
        }
        if (!VALID_GOVERNORATE_CODES.includes(id.substring(7, 9))) {
            return { isValid: false, errorMessage: "كود المحافظة في الرقم القومي غير صحيح" };
        }
    
        return { isValid: true };
    }
    
    function isValidDate(y, m, d) {
        const yy = parseInt(y,10), mm = parseInt(m,10), dd = parseInt(d,10);
        if (mm<1||mm>12||dd<1||dd>31) return false;
        const dt = new Date(yy, mm-1, dd);
        return dt.getFullYear()===yy && dt.getMonth()===mm-1 && dt.getDate()===dd;
    }
    
    // حساب رقم التحقق بـ Mod 11 (الوزن = رقم الموضع)
    function verifyCheckDigit(id) {
        let sum = 0;
        // i من 0 إلى 12، والوزن = i+1
        for (let i = 0; i < 13; i++) {
            sum += parseInt(id[i], 10) * (i + 1);
        }
        return (sum % 11) === parseInt(id[13], 10);
    }
    

    
    
    // تحديث الحقول تلقائيًا من الرقم القومي
    function updateDetails() {
        const input = document.getElementById("national_id");
        if (!validateNationalId(input)) return;
    
        const id = input.value;
        const century = id[0];
        const yearPref = century === '2' ? '19' : '20';
        const dob = `${yearPref}${id.substring(1,3)}-${id.substring(3,5)}-${id.substring(5,7)}`;
        const dobInput = document.getElementById("date_of_birth");
        dobInput.value = dob;
        calculateAge(dobInput);
    
        const genderEl = (parseInt(id[12]) % 2 === 0)
            ? document.getElementById("genderFemale")
            : document.getElementById("genderMale");
        genderEl.checked = true;
        
        // تحديث التصميم عند الاختيار التلقائي
        document.querySelectorAll('.gender-input').forEach(el => {
            if(el.checked) {
                el.dispatchEvent(new Event('change'));
            }
        });
    }
    
    // حساب العمر من تاريخ الميلاد
    function calculateAge(el) {
        const bd = new Date(el.value);
        if (isNaN(bd)) return;
        const today = new Date();
        let y = today.getFullYear() - bd.getFullYear();
        let m = today.getMonth() - bd.getMonth();
        let d = today.getDate() - bd.getDate();
        if (d < 0) {
            m--;
            d += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
        }
        if (m < 0) {
            y--;
            m += 12;
        }
        document.getElementById("age_years").value = y;
        document.getElementById("age_months").value = m;
        document.getElementById("age_days").value = d;
    }
    
    // تحقق من اختيار الجنس
    function validateGender() {
        const genderSelected = document.querySelector('input[name="gender"]:checked');
        const feedback = document.getElementById('genderFeedback');
        if (!genderSelected) {
            feedback.style.display = 'block';
            return false;
        }
        feedback.style.display = 'none';
        return true;
    }
    
    // إضافة حدث تغيير لتصميم العناصر عند الاختيار
    document.querySelectorAll('.gender-input').forEach(el => {
        el.addEventListener('change', function() {
            if(this.checked) {
                document.getElementById('genderFeedback').style.display = 'none';
            }
        });
    });
    
    // نموذج التحقق العام عند الإرسال
    document.getElementById('patientForm').addEventListener('submit', function(e) {
        let isValid = true;
        
        // تحقق من صحة الحقول القياسية
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            isValid = false;
        }
        
        // تحقق مخصص للجنس
        if (!validateGender()) {
            e.preventDefault();
            e.stopPropagation();
            isValid = false;
        }
        
        this.classList.add('was-validated');
        
        if (!isValid) {
            // التمرير إلى أول حقل غير صالح
            const firstInvalid = this.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
    </script>
    