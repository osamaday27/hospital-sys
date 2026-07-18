<div class="row">
<input type="hidden" value="{{$patient->id}}" name="id">

    <div class="col-3">
        <!-- Profile Image Section -->
        <div class="col-12 text-center" style="margin-bottom: 10px;margin-top: 40px;">
            <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                <img src="{{ asset($patient->img??'assets/images/users/user-dummy-img.jpg') }}" readonly
                    class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                    <input id="profile-img-file-input" type="file" class="profile-img-file-input"
                        accept="image/png, image/jpeg" name="img_url" onchange="get();">
                    <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                        <span class="avatar-title rounded-circle bg-light text-body">
                            <i class="ri-camera-fill"></i>
                        </span>
                    </label>
                </div>
            </div>
            <p class="text-muted mb-0">{{ trans('patient_trans.upload_patient') }}</p>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label for="ForminputState" class="form-label">{{ trans('patient_trans.contracting') }}</label>
                <select id="ForminputState" class="js-example-basic-single" name="insurance_contracting" disabled>
                    <option value="" disabled>{{ trans('patient_trans.select_option') }}</option>
                    @foreach ($contract as $data)
                        <option value="{{ $data->id }}" {{ $patient->insurance_contracting == $data->id ? 'selected' : '' }}>
                            {{ $data->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="col-9">
        <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <label for="full_name" class="form-label">{{ trans('patient_trans.full_name') }}</label>
                    <input type="text" class="form-control" placeholder="{{ trans('patient_trans.enter_full_name') }}" readonly
                        id="full_name" name="full_name" value="{{ $patient->full_name }}" onchange="splitAndTranslateName(this.value),get();" />
                </div>
            </div>

            <!-- الاسم الأول (عربي) -->
            <div class="col-3">
                <div class="mb-3">
                    <label for="first_name_ar" class="form-label">{{ trans('patient_trans.first_name_ar') }}</label>
                    <input type="text" class="form-control" name="first_name_ar" id="first_name_ar"  readonly
                        value="{{ $patient->first_name_ar }}" onchange="get();" placeholder="{{ trans('patient_trans.enter_first_name_ar') }}">
                </div>
            </div>

            <!-- الاسم الثاني (عربي) -->
            <div class="col-3">
                <div class="mb-3">
                    <label for="second_name_ar" class="form-label">{{ trans('patient_trans.second_name_ar') }}</label>
                    <input type="text" class="form-control" name="second_name_ar" id="second_name_ar" readonly
                        value="{{ $patient->second_name_ar }}" onchange="get();" placeholder="{{ trans('patient_trans.enter_second_name_ar') }}">
                </div>
            </div>

            <!-- الاسم الثالث (عربي) -->
            <div class="col-3">
                <div class="mb-3">
                    <label for="third_name_ar" class="form-label">{{ trans('patient_trans.third_name_ar') }}</label>
                    <input type="text" class="form-control" name="third_name_ar" id="third_name_ar"  readonly
                        value="{{ $patient->third_name_ar }}" onchange="get();" placeholder="{{ trans('patient_trans.enter_third_name_ar') }}">
                </div>
            </div>

            <!-- الاسم الرابع (عربي) -->
            <div class="col-3">
                <div class="mb-3">
                    <label for="fourth_name_ar" class="form-label">{{ trans('patient_trans.fourth_name_ar') }}</label>
                    <input type="text" class="form-control" name="fourth_name_ar" id="fourth_name_ar" readonly
                        value="{{ $patient->fourth_name_ar }}" onchange="get();" placeholder="{{ trans('patient_trans.enter_fourth_name_ar') }}">
                </div>
            </div>

            <!-- الاسم الأول (إنجليزي) -->
            <div class="col-3">
                <div class="mb-3">
                    <label for="first_name_en" class="form-label">{{ trans('patient_trans.first_name_en') }}</label>
                    <input type="text" class="form-control" name="first_name_en" id="first_name_en" readonly
                        value="{{ $patient->first_name_en }}" onchange="get();" placeholder="{{ trans('patient_trans.enter_first_name_en') }}">
                </div>
            </div>

            <!-- الاسم الثاني (إنجليزي) -->
            <div class="col-3">
                <div class="mb-3">
                    <label for="second_name_en" class="form-label">{{ trans('patient_trans.second_name_en') }}</label>
                    <input type="text" class="form-control" name="second_name_en" id="second_name_en" readonly
                        value="{{ $patient->second_name_en }}" onchange="get();" placeholder="{{ trans('patient_trans.enter_second_name_en') }}">
                </div>
            </div>

            <!-- الاسم الثالث (إنجليزي) -->
            <div class="col-3">
                <div class="mb-3">
                    <label for="third_name_en" class="form-label">{{ trans('patient_trans.third_name_en') }}</label>
                    <input type="text" class="form-control" name="third_name_en" id="third_name_en" readonly
                        value="{{ $patient->third_name_en }}" onchange="get();" placeholder="{{ trans('patient_trans.enter_third_name_en') }}">
                </div>
            </div>

            <!-- الاسم الرابع (إنجليزي) -->
            <div class="col-3">
                <div class="mb-3">
                    <label for="fourth_name_en" class="form-label">{{ trans('patient_trans.fourth_name_en') }}</label>
                    <input type="text" class="form-control" name="fourth_name_en" id="fourth_name_en" readonly
                        value="{{ $patient->fourth_name_en }}" onchange="get();" placeholder="{{ trans('patient_trans.enter_fourth_name_en') }}">
                </div>
            </div>
        </div>

    </div>

</div>
<script>
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

async function translateToEnglish(nameParts) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // الحصول على الـ CSRF Token
    
    try {
        // إرسال طلب إلى السيرفر للحصول على الترجمات
        const response = await fetch('/translate-names', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token, // إضافة الـ CSRF Token إلى رأس الطلب
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
function updateContracting() {
    console.log("تم تغيير التأمين");
}

// دالة لتحميل صورة الملف الشخصي
function updateProfileImage() {
    console.log("تم تغيير صورة الملف الشخصي");
}

</script>
