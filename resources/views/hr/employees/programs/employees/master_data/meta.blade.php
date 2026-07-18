<div class="row mt-2">
    <div class="col-3">
        <!-- Profile Image Section -->
        <div class="text-center mb-2 mt-4">
            <div class="profile-user position-relative d-inline-block mx-auto">
                <img src="{{ asset('assets/images/users/user-dummy-img.jpg') }}"
                    class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                    <input id="profile-img-file-input" type="file" class="profile-img-file-input"
                        accept="image/png, image/jpeg" name="employee_photo" onchange="get();">
                    <label for="profile-img-file-input" class="avatar-xs">
                        <span class="avatar-title rounded-circle bg-light text-body">
                            <i class="ri-camera-fill"></i>
                        </span>
                    </label>
                </div>
            </div>
            <p class="text-muted mb-0">Upload Photo</p>
        </div>
        
        <!-- Gender Select -->
        <div class="mb-3">
            <label for="gender" class="form-label">{{ trans('hr.gender') }}</label>
            <select id="gender" class="js-example-basic-single" name="gender" onchange="get();">
                <option value="{{ trans('hr.Male') }}">{{ trans('hr.Male') }}</option>
                <option value="{{ trans('hr.Female') }}">{{ trans('hr.Female') }}</option>
            </select>
        </div>
    </div>

    <div class="col-9">
        <div class="row">
            <!-- Full Name Input -->
            <div class="col-12 mb-3 mt-3">
                <label for="full_name" class="form-label">{{ trans('hr.full_name') }}</label>
                <input type="text" onchange="splitAndTranslateName(this.value),get();" class="form-control" placeholder="{{ trans('hr.enter_full_name') }}" id="full_name" name="full_name">
            </div>

            <!-- Arabic Name Fields (First, Second, Third, Fourth) -->
            @foreach(['first', 'second', 'third', 'fourth'] as $name)
                <div class="col-3 mb-3">
                    <label for="{{ $name }}_name_ar" class="form-label">{{ trans("hr.{$name}_name_ar") }}</label>
                    <input type="text" class="form-control" name="{{ $name }}_name_ar" id="{{ $name }}_name_ar"
                        onchange="get();" placeholder="{{ trans('hr.enter_'.$name.'_name_ar') }}">
                </div>
            @endforeach

            <!-- English Name Fields (First, Second, Third, Fourth) -->
            @foreach(['first', 'second', 'third', 'fourth'] as $name)
                <div class="col-3 mb-3">
                    <label for="{{ $name }}_name_en" class="form-label">{{ trans("hr.{$name}_name_en") }}</label>
                    <input type="text" class="form-control" name="{{ $name }}_name_en" id="{{ $name }}_name_en"
                        onchange="get();" placeholder="{{ trans('hr.enter_'.$name.'_name_en') }}">
                </div>
            @endforeach

            <div class="col-3 mb-3">
                <label for="employee_code" class="form-label">{{ trans("hr.employee_code") }}</label>
                <input type="text" class="form-control" name="employee_code" id="employee_code"
                    onchange="get();" placeholder="{{ trans('hr.enter_employee_code') }}" >
            </div>

            <div class="col-3 mb-3">
                <label for="gahar_code" class="form-label">{{ trans("hr.gahar_code") }}</label>
                <input type="text" class="form-control" name="gahar_code" id="gahar_code"
                    onchange="get();" placeholder="{{ trans('hr.enter_gahar_code') }}" >
            </div>

            <div class="col-3 mb-3">
                <label for="employee_status" class="form-label">{{ trans('hr.employee_status') }}</label>
                <select id="employee_status" class="js-example-basic-single" name="employee_status" onchange="get();">
                    <option value='in_service'>{{ trans('hr.in_service') }}</option>
                    <option value='out_of_service'>{{ trans('hr.out_of_service') }}</option>
                </select>
            </div>



            <!-- Marital Status -->
            <div class="col-3 mb-3">
                <label for="marital_status" class="form-label">{{ trans('hr.marital_status') }}</label>
                <select id="marital_status" class="js-example-basic-single" name="marital_status" onchange="get();">
                    <option value='single'>{{ trans('hr.single') }}</option>
                    <option value='married'>{{ trans('hr.married') }}</option>
                    <option value='widower'>{{ trans('hr.widower') }}</option>
                    <option value='divorced'>{{ trans('hr.divorced') }}</option>
                </select>
            </div>

            <!-- Identification and Passport Numbers -->
            @foreach(['identification_number'] as $field)
                <div class="col-3 mb-3">
                    <label for="{{ $field }}" class="form-label">{{ trans("hr.{$field}") }}</label>
                    <input type="text" class="form-control" name="{{ $field }}" id="{{ $field }}" onchange="autoFillFromID(this.value);get();"
                        placeholder="{{ trans("hr.{$field}") }}">
                </div>
            @endforeach
            <div class="col-3 mb-3">
                <label for="date_of_birth" class="form-label">{{ trans('hr.date_of_birth') }}</label>
                <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" onchange="get();">
            </div>
            <script>
            function autoFillFromID(id) {
                if (id.length === 14) {
                    let century = id[0];
                    let year = id.substring(1, 3);
                    let month = id.substring(3, 5);
                    let day = id.substring(5, 7);
                    let genderDigit = id[12];
                    let fullYear = '';
                    if (century === '2') fullYear = '19' + year;
                    else if (century === '3') fullYear = '20' + year;
                    if (month >= 1 && month <= 12 && day >= 1 && day <= 31) {
                        document.getElementById('date_of_birth').value = `${fullYear}-${month}-${day}`;
                    }
                    if (genderDigit % 2 === 0) {
                        document.getElementById('gender').value = '{{ trans('hr.Female') }}';
                    } else {
                        document.getElementById('gender').value = '{{ trans('hr.Male') }}';
                    }
                }
            }
            </script>
            @foreach(['passport_number'] as $field)
                <div class="col-3 mb-3">
                    <label for="{{ $field }}" class="form-label">{{ trans("hr.{$field}") }}</label>
                    <input type="text" class="form-control" name="{{ $field }}" id="{{ $field }}" onchange="get();"
                        placeholder="{{ trans("hr.{$field}") }}">
                </div>
            @endforeach

            <!-- Religion -->
            <div class="col-3 mb-3">
                <label for="religion" class="form-label">{{ trans('hr.religion') }}</label>
                <select id="religion" class="js-example-basic-single" name="religion" onchange="get();">
                    <option value="{{ trans('hr.muslim') }}">{{ trans('hr.muslim') }}</option>
                    <option value="{{ trans('hr.chrestian') }}">{{ trans('hr.chrestian') }}</option>
                    <option value="{{ trans('hr.other') }}">{{ trans('hr.other') }}</option>
                </select>
            </div>

            <!-- Country, City, Area, Full Address -->
            @foreach(['country', 'city', 'district', 'full_address'] as $field)
                <div class="col-3 mb-3">
                    @if($field == 'country')
                        <label for="{{ $field }}" class="form-label">{{ trans('hr.country') }}</label>
                        <select id="{{ $field }}" class="js-example-basic-single" name="{{ $field }}" onchange="get();">
                            <option value="" selected>{{ trans('hr.select_option') }}</option>
                            @foreach ($country as $data)
                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    @elseif($field == 'city')
                        <label for="{{ $field }}" class="form-label">{{ trans('hr.city') }}</label>
                        <select id="{{ $field }}" class="js-example-basic-single" name="{{ $field }}" onchange="get();">
                            <option value="" selected>{{ trans('hr.select_option') }}</option>
                            @foreach ($cities as $data)
                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <label for="{{ $field }}" class="form-label">{{ trans('hr.' . $field) }}</label>
                        <input class="form-control" name="{{ $field }}" id="{{ $field }}" onchange="get();"
                            placeholder="{{ trans('hr.' . $field) }}" />
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
<script>
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
</script>