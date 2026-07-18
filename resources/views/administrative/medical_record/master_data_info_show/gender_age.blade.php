<!-- الجنسية -->
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.nationality') }}</label>
        <select onchange="get();" id="ForminputStats" class="js-example-basic-single" name="nationality" disabled>
            <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
            @foreach ($nationality as $data)
                <option value="{{ $data->id }}" {{ isset($patient) && $patient->nationality == $data->id ? 'selected' : '' }}>
                    {{ $data->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<!-- الرقم القومي -->
<div class="col-3">
    <div class="mb-3">
        <label for="national_id" class="form-label">{{ trans('patient_trans.national_id') }}</label>
        <input type="text" class="form-control" name="national_id" id="national_id" onchange="get(); updateDetails();"
            placeholder="{{ trans('patient_trans.national_id') }}" 
            value="{{ isset($patient) ? $patient->national_id : old('national_id') }}" readonly>
    </div>
</div>

<!-- رقم الجواز -->
<div class="col-3">
    <div class="mb-3">
        <label for="passport_number" class="form-label">{{ trans('patient_trans.passport_number') }}</label>
        <input type="text" class="form-control" name="passport_number" id="passport_number" onchange="get();"
            placeholder="{{ trans('patient_trans.passport_number') }}" 
            value="{{ isset($patient) ? $patient->passport_number : old('passport_number') }}" readonly>
    </div>
</div>

<!-- الحالة الاجتماعية -->
<div class="col-3">
    <div class="mb-3">
        <label for="ForminputState" class="form-label">{{ trans('patient_trans.marital_status') }}</label>
        <select onchange="get();" id="ForminputState" class="form-select" name="marital_status" disabled>
            <option value="" selected>{{ trans('patient_trans.select_option') }}</option>
            <option value="1" {{ isset($patient) && $patient->marital_status == 1 ? 'selected' : '' }}>{{ trans('patient_trans.single') }}</option>
            <option value="2" {{ isset($patient) && $patient->marital_status == 2 ? 'selected' : '' }}>{{ trans('patient_trans.married') }}</option>
            <option value="3" {{ isset($patient) && $patient->marital_status == 3 ? 'selected' : '' }}>{{ trans('patient_trans.widower') }}</option>
            <option value="4" {{ isset($patient) && $patient->marital_status == 4 ? 'selected' : '' }}>{{ trans('patient_trans.divorced') }}</option>
        </select>
    </div>
</div>

<!-- تاريخ الميلاد -->
<div class="col-3">
    <div class="mb-3">
        <label class="form-label">{{ trans('patient_trans.date_birth') }}</label>
        <input onchange="calu(this),get();" name="date_of_birth" type="date" class="form-control"
            data-date-format="d M, Y" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" 
            value="{{ isset($patient) ? $patient->date_of_birth : old('date_of_birth') }}" readonly/>
    </div>
</div>

<!-- العمر باليوم -->
<div class="col-2">
    <div class="mb-3">
        <label for="age_days" class="form-label">{{ trans('patient_trans.age_days') }}</label>
        <input type="number" class="form-control" name="age_days" id="age_days" readonly onchange="get();"
            value="{{ isset($patient) ? $patient->age_days : old('age_days') }}" readonly> 
    </div>
</div>

<!-- العمر بالشهر -->
<div class="col-2">
    <div class="mb-3">
        <label for="age_months" class="form-label">{{ trans('patient_trans.age_months') }}</label>
        <input type="number" class="form-control" name="age_months" id="age_months" readonly onchange="get();"
            value="{{ isset($patient) ? $patient->age_months : old('age_months') }}" readonly>
    </div>
</div>

<!-- العمر بالسنة -->
<div class="col-2">
    <div class="mb-3">
        <label for="age_years" class="form-label">{{ trans('patient_trans.age_years') }}</label>
        <input type="number" class="form-control" name="age_years" id="age_years" readonly onchange="get();"
            value="{{ isset($patient) ? $patient->age_years : old('age_years') }}">
    </div>
</div>

<!-- الجنس -->
<div class="col-2">
    <div class="mb-3">
        <label class="form-label" style="display: block;">{{ trans('patient_trans.gender') }}</label>
        <div class="form-check form-check-inline" style="padding-top: 10px;">
            <input class="form-check-input" type="radio" name="gender" id="genderMale" value="1"
                {{ isset($patient) && $patient->gender == 1 ? 'checked' : '' }} style="top: 10px;" onchange="get();" readonly>
            <label class="form-check-label" for="genderMale">
                <i class="fas fa-male"></i> {{ trans('patient_trans.male') }}
            </label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="0"
                {{ isset($patient) && $patient->gender == 0 ? 'checked' : '' }} style="top: 10px;" onchange="get();" readonly>
            <label class="form-check-label" for="genderFemale">
                <i class="fas fa-female"></i> {{ trans('patient_trans.female') }}
            </label>
        </div>
    </div>
</div>
 <script>
       function updateDetails() {
           const nationalId = document.getElementById("national_id").value;

           // التحقق من طول الرقم القومي (يجب أن يكون 14 رقماً)
           if (nationalId.length !== 14) {
               alert("الرقم القومي غير صحيح!");
               return;
           }

           // استخراج تاريخ الميلاد
           const yearPrefix = nationalId[0] === '2' ? '19' : '20';
           const birthYear = yearPrefix + nationalId.substring(1, 3);
           const birthMonth = nationalId.substring(3, 5);
           const birthDay = nationalId.substring(5, 7);

           const dateOfBirth = `${birthYear}-${birthMonth}-${birthDay}`;
           const dateOfBirthInput = document.querySelector('input[name="date_of_birth"]');
           dateOfBirthInput.value = dateOfBirth;

           // استدعاء دالة حساب العمر
           calu(dateOfBirthInput);

           // استخراج الجنس (1 ذكر، 0 أنثى)
           const genderCode = nationalId[12];
           const genderMale = document.getElementById("genderMale");
           const genderFemale = document.getElementById("genderFemale");
           if (genderCode % 2 === 0) {
               genderFemale.checked = true;
           } else {
               genderMale.checked = true;
           }
       }

       function calu(element) {
           const birthDate = new Date(element.value);
           const today = new Date();

           let years = today.getFullYear() - birthDate.getFullYear();
           let months = today.getMonth() - birthDate.getMonth();
           let days = today.getDate() - birthDate.getDate();

           // التحقق إذا كان تاريخ الميلاد هو اليوم
           if (
               birthDate.getFullYear() === today.getFullYear() &&
               birthDate.getMonth() === today.getMonth() &&
               birthDate.getDate() === today.getDate()
           ) {
               years = 0;
               months = 0;
               days = 1; // ضبط العمر ليكون يومًا واحدًا
           } else {
               if (days < 0) {
                   months--;
                   days += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
               }
               if (months < 0) {
                   years--;
                   months += 12;
               }
           }

           document.getElementById('age_years').value = years;
           document.getElementById('age_months').value = months;
           document.getElementById('age_days').value = days;
       }
   </script>
