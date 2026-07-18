<form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" accept=".xlsx, .xls, .csv">
    <button type="submit">رفع الملف</button>
</form>
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
