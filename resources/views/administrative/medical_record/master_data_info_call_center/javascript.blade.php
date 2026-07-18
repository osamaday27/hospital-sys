<script>
// resources/js/patient-form.js

// احصل على توكن CSRF
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// محافظات مصر الصالحة
const VALID_GOVERNORATE_CODES = [
  '01','02','03','04','11','12','13','14','15','16','17','18','19',
  '21','22','23','24','25','26','27','28','29','31','32','33','34','35','88'
];

// ذاكرة تخزين مؤقت للترجمات
const translationCache = {};

/** 1. معاينة الصورة المختارة **/
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => document.getElementById('profile-image-preview').src = e.target.result;
    reader.readAsDataURL(input.files[0]);
  }
}

/** 2. تطبيع الأسماء عربياً **/
function normalizeName(name) {
  return name.replace(/[أإآ]/g, 'ا').trim();
}

/** 3. ملء حقول الاسم العربية **/
function updateArabicFields(parts) {
  document.getElementById('first_name_ar').value  = parts[0] || '';
  document.getElementById('second_name_ar').value = parts[1] || '';
  document.getElementById('third_name_ar').value  = parts[2] || '';
  document.getElementById('fourth_name_ar').value = parts.slice(3).join(' ') || '';
}

/** 4. ملء حقول الاسم الإنجليزية **/
function updateEnglishFields(parts) {
  document.getElementById('first_name_en').value  = parts[0] || '';
  document.getElementById('second_name_en').value = parts[1] || '';
  document.getElementById('third_name_en').value  = parts[2] || '';
  document.getElementById('fourth_name_en').value = parts[3] || '';
}

/** 5. تقسيم وترجمة الاسم **/
async function splitAndTranslateName(fullName) {
  const parts = normalizeName(fullName).split(/\s+/);
  updateArabicFields(parts);
  const cached = parts.map(p => translationCache[p]);
  if (cached.every(t => t !== undefined)) {
    updateEnglishFields(cached);
    return;
  }
  try {
    const res = await fetch('/translate-names', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ names: parts })
    });
    if (res.ok) {
      const translations = await res.json();
      const enParts = parts.map(p => {
        const tr = translations[p] || p;
        translationCache[p] = tr;
        return tr;
      });
      updateEnglishFields(enParts);
    }
  } catch (err) {
    console.error('خطأ في الترجمة:', err);
    updateEnglishFields(parts);
  }
}

/** 6. تعبئة عربية تلقائياً **/
function autoFillArabicNames() {
  const full = document.getElementById('full_name').value;
  if (!full.trim()) return;
  updateArabicFields(normalizeName(full).split(/\s+/));
}

/** 7. تغيير شركة التأمين **/
function handleInsuranceChange() {
  const sel = document.getElementById('ForminputState');
  console.log('شركة التأمين المختارة:', sel.options[sel.selectedIndex].text);
}

/** 8. التحقق من رقم الهاتف **/
function validatePhoneNumber(input, feedbackId) {
  let v = input.value.replace(/\D/g, '');
  input.value = v;
  const fb = document.getElementById(feedbackId);
  fb.classList.remove('d-block'); fb.textContent = '';
  if (!v) return true;
  if (v.length !== 11 || !/^01[0-2,5]\d{8}$/.test(v)) {
    input.classList.add('is-invalid');
    fb.textContent = v.length !== 11
      ? 'يجب أن يتكون رقم الهاتف من 11 رقمًا'
      : 'يجب أن يبدأ بـ 010 أو 011 أو 012 أو 015';
    fb.classList.add('d-block');
    return false;
  }
  input.classList.remove('is-invalid');
  return true;
}

/** 9. التحقق من الرقم القومي **/
function validateNationalId(input) {
  let id = input.value.replace(/\D/g, '');
  input.value = id;
  const fb = document.getElementById('national_id_feedback');
  fb.classList.remove('d-block'); fb.textContent = ''; input.classList.remove('is-invalid');

  if (id.length > 14) { id = id.slice(0,14); input.value = id; }
  if (id.length < 14) {
    if (id.length > 0) {
      input.classList.add('is-invalid');
      fb.textContent = 'يجب أن يتكون من 14 رقمًا'; fb.classList.add('d-block');
    }
    return false;
  }

  const res = validateEgyptianNationalID(id);
  if (!res.isValid) {
    input.classList.add('is-invalid'); fb.textContent = res.errorMessage; fb.classList.add('d-block');
    return false;
  }

  updateDetailsFromNationalId(id);
  return true;
}

function validateEgyptianNationalID(id) {
  if (!/^\d{14}$/.test(id)) return { isValid:false,errorMessage:'الرقم يجب أن يتكون من 14 رقمًا' };
  const c = id.charAt(0);
  if (!['2','3'].includes(c)) return { isValid:false,errorMessage:'البداية غير صحيحة (2 أو 3)' };
  const y = parseInt(id.substr(1,2),10);
  const m = parseInt(id.substr(3,2),10);
  const d = parseInt(id.substr(5,2),10);
  const fy = (c === '2' ? 1900 : 2000) + y;
  if (!isValidDate(fy,m,d)) return { isValid:false,errorMessage:'تاريخ الميلاد غير صحيح' };
  if (!VALID_GOVERNORATE_CODES.includes(id.substr(7,2)))
    return { isValid:false,errorMessage:'كود المحافظة غير صحيح' };
  if (!verifyCheckDigit(id))
    return { isValid:false,errorMessage:'رقم التحقق غير صحيح' };
  return { isValid:true };
}

function isValidDate(year, month, day) {
  if (month < 1 || month > 12 || day < 1 || day > 31) return false;
  if ([4,6,9,11].includes(month) && day > 30) return false;
  if (month === 2) {
    const leap = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
    if (day > (leap ? 29 : 28)) return false;
  }
  return true;
}

function verifyCheckDigit(id) {
  let sum = 0;
  for (let i = 0; i < 13; i++) {
    let d = parseInt(id.charAt(i),10);
    let p = d * (i % 2 === 0 ? 2 : 1);
    if (p > 9) p = Math.floor(p / 10) + (p % 10);
    sum += p;
  }
  const chk = (10 - (sum % 10)) % 10;
  return chk === parseInt(id.charAt(13),10);
}

function updateDetailsFromNationalId(id) {
  const c = id.charAt(0);
  const y = parseInt(id.substr(1,2),10);
  const fy = (c === '2' ? 1900 : 2000) + y;
  const mo = id.substr(3,2);
  const da = id.substr(5,2);
  document.getElementById('date_of_birth').value = `${fy}-${mo}-${da}`;
  calculateAge();
  const gd = parseInt(id.charAt(12),10);
  document.getElementById(gd % 2 === 0 ? 'genderFemale' : 'genderMale').checked = true;
  document.getElementById('genderFeedback').style.display = 'none';
}

function calculateAge() {
  const dob = new Date(document.getElementById('date_of_birth').value);
  if (isNaN(dob)) return;
  const t = new Date();
  let years = t.getFullYear() - dob.getFullYear();
  let months = t.getMonth() - dob.getMonth();
  let days = t.getDate() - dob.getDate();
  if (days < 0) {
    months--;
    days += new Date(t.getFullYear(), t.getMonth(), 0).getDate();
  }
  if (months < 0) {
    years--;
    months += 12;
  }
  document.getElementById('age_years').value = years;
  document.getElementById('age_months').value = months;
  document.getElementById('age_days').value = days;
}

function validateGender() {
  const sel = document.querySelector('input[name="gender"]:checked');
  const fb = document.getElementById('genderFeedback');
  if (!sel) { fb.style.display = 'block'; return false; }
  fb.style.display = 'none';
  return true;
}

/** 10. السلاسل التتابعية: المحافظات، المدن، المناطق **/
function initCascades() {
  $('#id_country').change(function() {
    const cid = $(this).val();
    $('#id_governmentes').prop('disabled',!cid).html(`<option>${$('#id_governmentes').data('select-option')}</option>`);
    $('#id_city,#id_area').prop('disabled',true)
      .html(`<option>${$('#id_city').data('select-option')}</option>`);
    if (!cid) return;
    $.get(`/get-governorates/${cid}`, data => {
      $.each(data, (k, v) => {
        $('#id_governmentes').append(`<option value="${k}">${v}</option>`);
      });
    }).fail(() => showToast('حدث خطأ أثناء جلب بيانات المحافظات','danger'));
  });

  $('#id_governmentes').change(function() {
    const g = $(this).val();
    $('#id_city').prop('disabled',!g)
      .html(`<option>${$('#id_city').data('select-option')}</option>`);
    $('#id_area').prop('disabled',true)
      .html(`<option>${$('#id_area').data('select-option')}</option>`);
    if (!g) return;
    $.get(`/get-cities/${g}`, data => {
      $.each(data, (k, v) => {
        $('#id_city').append(`<option value="${k}">${v}</option>`);
      });
    }).fail(() => showToast('حدث خطأ أثناء جلب بيانات المدن','danger'));
  });

  $('#id_city').change(function() {
    const c = $(this).val();
    $('#id_area').prop('disabled',!c)
      .html(`<option>${$('#id_area').data('select-option')}</option>`);
    if (!c) return;
    $.get(`/get-areas/${c}`, data => {
      $.each(data, (k, v) => {
        $('#id_area').append(`<option value="${k}">${v}</option>`);
      });
    }).fail(() => showToast('حدث خطأ أثناء جلب بيانات المناطق','danger'));
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('patientForm');
  const reqs = form.querySelectorAll('[required]');

  // أحداث الحقول
  document.querySelectorAll('input, select, textarea').forEach(f => {
    f.addEventListener('input', () => validateField(f));
    f.addEventListener('blur', () => validateField(f));
  });

  document.getElementById('national_id').addEventListener('input', function() {
    validateNationalId(this);
  });

  document.getElementById('date_of_birth').addEventListener('change', calculateAge);

  $('#profile-image-input').change(function() { previewImage(this); });
  document.getElementById('full_name').addEventListener('blur', function() {
    autoFillArabicNames(); splitAndTranslateName(this.value);
  });
  $('#ForminputState').change(handleInsuranceChange);
  $('input[data-validate="phone"]').on('input blur', function() { validatePhoneNumber(this, this.dataset.feedback); });

  initCascades();
  updateProgress();

  form.addEventListener('submit', e => {
    let ok = true;
    reqs.forEach(f => { if (!validateField(f)) ok = false; });
    if (!validateGender()) ok = false;
    if (!ok) {
      e.preventDefault();
      const fb = form.querySelector('.is-invalid');
      if (fb) fb.scrollIntoView({ behavior: 'smooth', block: 'center' });
      showToast('يوجد أخطاء في النموذج، يرجى تصحيحها','danger');
    } else showToast('جاري إرسال البيانات...','info');
  });
});

</script>