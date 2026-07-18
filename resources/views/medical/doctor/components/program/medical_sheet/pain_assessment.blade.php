<style>
    .card {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }

    .pain-scale,
    .faces-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .pain-box {
        width: 50px;
        height: 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        font-weight: bold;
        color: white;
        margin: 5px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .pain-box:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .pain-box.selected {
        border: 3px solid black;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
    }

    .level-1 {
        background-color: #98FB98;
    }

    /* أخضر فاتح */
    .level-2 {
        background-color: #228B22;
    }

    /* أخضر غامق */
    .level-3 {
        background-color: #006400;
    }

    /* أخضر داكن */
    .level-4 {
        background-color: #FFDAB9;
    }

    /* بيج فاتح */
    .level-5 {
        background-color: #FFA07A;
    }

    /* برتقالي فاتح */
    .level-6 {
        background-color: #CD5C5C;
    }

    /* أحمر فاتح */
    .level-7 {
        background-color: #B22222;
    }

    /* أحمر متوسط */
    .level-8 {
        background-color: #FF4500;
    }

    /* أحمر قوي */
    .level-9 {
        background-color: #DC143C;
    }

    /* أحمر قاتم */
    .level-10 {
        background-color: #8B0000;
    }

    /* أحمر غامق */

    .result,
    .resultface {
        margin-top: 20px;
        font-size: 22px;
        font-weight: bold;
        text-align: center;
        color: #333;
    }

    .faces-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
        gap: 20px;
    }

    .face {
        text-align: center;
        margin: 10px;
        cursor: pointer;
        border: 2px solid transparent;
        padding: 10px;
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
        background-color: #f9f9f9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .face:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .face.selected {
        border-color: black;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .face img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        transition: transform 0.2s;
    }

    .face span {
        display: block;
        font-size: 18px;
        font-weight: bold;
        margin-top: 5px;
        color: #555;
    }

    .selectable-cell {
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .selectable-cell:hover {
        background-color: #f0f0f0;
        /* تغيير اللون عند التمرير */
    }

    .selected {
        background-color: #dcdcdc;
        /* تغيير اللون عند التحديد */
    }

    /* تحسين تنسيق العناوين */
    h4 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    /* تنسيق قسم نوع الألم لعرض الخيارات في صف واحد */
    .pain-type-container {
        margin-bottom: 20px;
    }

    .pain-type-options {
        display: flex;
        flex-wrap: wrap;
        /* يسمح بلف الخيارات إذا كانت أكبر من العرض */
        gap: 15px;
        /* المسافة بين الخيارات */
    }

    .pain-type-options label {
        font-size: 16px;
        cursor: pointer;
        margin: 0;
        /* إزالة الهوامش الافتراضية */
    }


    /* تصميم مربع الملاحظات */
    .notes-container {
        margin-bottom: 20px;
    }

    textarea {
        font-size: 16px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        resize: vertical;
        /* يسمح بتغيير حجم المربع عمودياً */
    }

    /* تحسين حجم النص عند الكتابة */
    textarea:focus {
        outline: none;
        border-color: #4CAF50;
        /* تغيير لون الحد عند التركيز */
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
    }
</style>

<h2>التقييم الرقمي للألم</h2>
<p>يستخدم للمريض البالغ الواعى: يتم سؤال المريض إذا كان عدم وجود ألم يساوى صفر وأقصى ألم يساوى 10، كم تعطى لنفسك؟</p>
<p>يرجى تحديد مستوى الألم:</p>

<!-- Card for Numeric Scale -->
<div class="card">
    <div class="pain-scale">
        <div class="pain-box level-1" data-value="1">1</div>
        <div class="pain-box level-2" data-value="2">2</div>
        <div class="pain-box level-3" data-value="3">3</div>
        <div class="pain-box level-4" data-value="4">4</div>
        <div class="pain-box level-5" data-value="5">5</div>
        <div class="pain-box level-6" data-value="6">6</div>
        <div class="pain-box level-7" data-value="7">7</div>
        <div class="pain-box level-8" data-value="8">8</div>
        <div class="pain-box level-9" data-value="9">9</div>
        <div class="pain-box level-10" data-value="10">10</div>
    </div>
    <input id="digital_assessment" name="digital_assessment" value="" type="hidden" />
    <div class="result">لم يتم تحديد مستوى الألم بعد.</div>
</div>

<h2>مقياس Wong-Baker FACES لتقييم الألم</h2>
<p>يستخدم للأطفال من عمر أكبر من 3 سنوات وللكبار الغير متعلمين او إذا كان مستوى الوعى غير طبيعى.</p>
<p>يرجى اختيار الوجه الذي يعبر عن مستوى الألم لديك:</p>

<!-- Card for Faces Scale -->
<div class="card">
    <div class="faces-container">
        <div class="face" data-value="0">
            <svg viewBox="0 0 100 100" width="100" height="100">
                <circle cx="50" cy="50" r="45" stroke="black" stroke-width="2" fill="#98FB98" />
                <circle cx="35" cy="40" r="5" fill="black" />
                <circle cx="65" cy="40" r="5" fill="black" />
                <path d="M 30 60 Q 50 80, 70 60" stroke="black" stroke-width="2" fill="none" />
            </svg>
            <span>0</span>
        </div>
        <div class="face" data-value="2">
            <svg viewBox="0 0 100 100" width="100" height="100">
                <circle cx="50" cy="50" r="45" stroke="black" stroke-width="2" fill="#228B22" />
                <circle cx="35" cy="40" r="5" fill="black" />
                <circle cx="65" cy="40" r="5" fill="black" />
                <path d="M 30 65 Q 50 75, 70 65" stroke="black" stroke-width="2" fill="none" />
            </svg>
            <span>2</span>
        </div>
        <div class="face" data-value="4">
            <svg viewBox="0 0 100 100" width="100" height="100">
                <circle cx="50" cy="50" r="45" stroke="black" stroke-width="2" fill="#FFD700" />
                <circle cx="35" cy="40" r="5" fill="black" />
                <circle cx="65" cy="40" r="5" fill="black" />
                <line x1="30" y1="70" x2="70" y2="70" stroke="black" stroke-width="2" />
            </svg>
            <span>4</span>
        </div>
        <div class="face" data-value="6">
            <svg viewBox="0 0 100 100" width="100" height="100">
                <circle cx="50" cy="50" r="45" stroke="black" stroke-width="2" fill="#FFA07A" />
                <circle cx="35" cy="40" r="5" fill="black" />
                <circle cx="65" cy="40" r="5" fill="black" />
                <path d="M 30 75 Q 50 60, 70 75" stroke="black" stroke-width="2" fill="none" />
            </svg>
            <span>6</span>
        </div>
        <div class="face" data-value="8">
            <svg viewBox="0 0 100 100" width="100" height="100">
                <circle cx="50" cy="50" r="45" stroke="black" stroke-width="2" fill="#FF4500" />
                <circle cx="35" cy="40" r="5" fill="black" />
                <circle cx="65" cy="40" r="5" fill="black" />
                <path d="M 30 80 Q 50 55, 70 80" stroke="black" stroke-width="2" fill="none" />
            </svg>
            <span>8</span>
        </div>
        <div class="face" data-value="10">
            <svg viewBox="0 0 100 100" width="100" height="100">
                <circle cx="50" cy="50" r="45" stroke="black" stroke-width="2" fill="#8B0000" />
                <circle cx="35" cy="40" r="5" fill="black" />
                <circle cx="65" cy="40" r="5" fill="black" />
                <path d="M 30 85 Q 50 50, 70 85" stroke="black" stroke-width="2" fill="none" />
            </svg>
            <span>10</span>
        </div>
    </div>
    <input id="face_assessment" name="face_assessment" value="" type="hidden" />
    <div class="resultface">لم يتم تحديد مستوى الألم بعد.</div>
</div>
<!-- Card for Faces Scale -->
<h4>FLACC scale</h4>
<p>يستخدم للأطفال من عمر شهرين حتى7 سنوات وللكبار الذين لا يستطيعون التحدث.</p>
<div class="card">
    <table class="table table-bordered border-secondary table-nowrap">
        <thead>
            <tr>
                <th style="text-align: center;">المعيار</th>
                <th style="text-align: center;">(0) درجة</th>
                <th style="text-align: center;">(1) درجة</th>
                <th style="text-align: center;">(2) درجة</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">الوجه</td>
                <td class="selectable-cell">
                    <input type="radio" name="face" value="0"> لا يوجد تعبير أو ابتسامة معينة
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="face" value="1"> عبوس، غير مهتم
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="face" value="2"> ارتعاش متكرر للذقن، الفك المشدود
                </td>
            </tr>

            <tr>
                <td style="text-align: center;">الساقين</td>
                <td class="selectable-cell">
                    <input type="radio" name="legs" value="0"> وضع طبيعى أو استرخاء
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="legs" value="1"> مضطربة، لا تهدأ، متوترة
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="legs" value="2"> الركل
                </td>
            </tr>

            <tr>
                <td style="text-align: center;">النشاط</td>
                <td class="selectable-cell">
                    <input type="radio" name="activity" value="0"> وضع طبيعى، يتحرك بسهولة
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="activity" value="1"> تحول، ذهاب واياب، توتر
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="activity" value="2"> يتقوس، رجيج، جامد
                </td>
            </tr>

            <tr>
                <td style="text-align: center;">البكاء</td>
                <td class="selectable-cell">
                    <input type="radio" name="crying" value="0"> لا يبكى (مستيقظ أو نائم)
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="crying" value="1"> يشتكى أو يذبل، شى عرضية
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="crying" value="2"> البكاء بثبات، صراخ أو تنهدات، شكاوى متكررة
                </td>
            </tr>

            <tr>
                <td style="text-align: center;">القدرة على السيطرة</td>
                <td class="selectable-cell">
                    <input type="radio" name="control" value="0"> استرخاء
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="control" value="1"> طمأنته عن طريق اللمس أو المعانقة
                </td>
                <td class="selectable-cell">
                    <input type="radio" name="control" value="2"> من الصعب أن تريحه
                </td>
            </tr>
        </tbody>
    </table>
    <input id="child_assessment" name="child_assessment" value="" type="hidden" />

    <!-- مكان لعرض النتيجة -->
    <div class="result">
        <strong>النتيجة الإجمالية: <span id="total-score">0</span> درجة</strong>
    </div>
</div>

<h3>تقييم وعلاج الألم</h3>
<p>يملأ بواسطة الطبيب:</p>
<div class="card">
    <table border="1" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="padding: 8px;">التقييم المستخدم</th>
                <th style="padding: 8px;">الدرجة</th>
                <th style="padding: 8px;">مكان الألم</th>
                <th style="padding: 8px;">مستمر/ متقطع</th>
                <th style="padding: 8px;">نوع الألم</th>
                <th style="padding: 8px;">يزيد خلال</th>
                <th style="padding: 8px;">يقل خلال</th>
                <th style="padding: 8px;">العلاج</th>
                <th style="padding: 8px;">إعادة التقييم</th>
            </tr>
        </thead>
        <tbody id="pain-assessment-table">
            <tr>
                <td><input type="text" name="assessment[]" style="width: 100%;"></td>
                <td><input type="number" name="degree[]" style="width: 100%;"></td>
                <td><input type="text" name="pain-location[]" style="width: 100%;"></td>
                <td><input type="text" name="continuous-intermittent[]" style="width: 100%;"></td>
                <td><input type="text" name="pain-type[]" style="width: 100%;"></td>
                <td><input type="text" name="increases-during[]" style="width: 100%;"></td>
                <td><input type="text" name="decreases-during[]" style="width: 100%;"></td>
                <td><input type="text" name="treatment[]" style="width: 100%;"></td>
                <td><input type="text" name="reassessment[]" style="width: 100%;"></td>
            </tr>
        </tbody>
    </table>

    <button type="button" class="btn btn-soft-primary waves-effect waves-light shadow-none" onclick="addRow()">إضافة
        صف</button>
</div>
<div class="pain-type-container">
    <h4>نوع الألم:</h4>
    <div class="mb-3">
        <label class="form-label">نوع الألم</label>
        <div class="d-flex flex-wrap">
            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type1" name="pain-type" value="موجع">
                <label class="form-check-label" for="pain-type1">موجع</label>
            </div>

            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type2" name="pain-type" value="محرق">
                <label class="form-check-label" for="pain-type2">محرق</label>
            </div>

            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type3" name="pain-type" value="تقلص">
                <label class="form-check-label" for="pain-type3">تقلص</label>
            </div>

            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type4" name="pain-type" value="ساحق">
                <label class="form-check-label" for="pain-type4">ساحق</label>
            </div>

            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type5" name="pain-type" value="ثقيل">
                <label class="form-check-label" for="pain-type5">ثقيل</label>
            </div>

            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type6" name="pain-type" value="تنميل">
                <label class="form-check-label" for="pain-type6">تنميل</label>
            </div>

            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type7" name="pain-type" value="وخز">
                <label class="form-check-label" for="pain-type7">وخز</label>
            </div>

            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type8" name="pain-type" value="طعن">
                <label class="form-check-label" for="pain-type8">طعن</label>
            </div>

            <div class="form-check form-check-outline form-check-secondary mb-3 me-3">
                <input class="form-check-input" type="radio" id="pain-type9" name="pain-type" value="منبض">
                <label class="form-check-label" for="pain-type9">منبض</label>
            </div>
        </div>
    </div>
</div>

<div class="notes-container">
    <h4>ملاحظات:</h4>
    <textarea name="additional_notes" id="additional_notes" rows="5" placeholder="أدخل الملاحظات هنا..."
        style="width: 100%;"></textarea>
</div>

<button type="button" onclick="submitPainAssessments()" class="btn btn-primary waves-effect waves-light">حفظ
    التقييم</button>
<script>
    function addRow() {
        var row = `<tr>
        <td><input type="text" name="assessment[]" style="width: 100%;"></td>
        <td><input type="number" name="degree[]" style="width: 100%;"></td>
        <td><input type="text" name="pain-location[]" style="width: 100%;"></td>
        <td><input type="text" name="continuous-intermittent[]" style="width: 100%;"></td>
        <td><input type="text" name="pain-type[]" style="width: 100%;"></td>
        <td><input type="text" name="increases-during[]" style="width: 100%;"></td>
        <td><input type="text" name="decreases-during[]" style="width: 100%;"></td>
        <td><input type="text" name="treatment[]" style="width: 100%;"></td>
        <td><input type="text" name="reassessment[]" style="width: 100%;"></td>
    </tr>`;
        $("#pain-assessment-table").append(row);
    }
</script>

<script>
    const boxes = document.querySelectorAll('.pain-box');
    const result = document.querySelector('.result');
    const resultface = document.querySelector('.resultface');

    boxes.forEach(box => {
        box.addEventListener('click', function() {
            boxes.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            const value = this.getAttribute('data-value');
            if (value >= 1 && value <= 3) {
                result.innerHTML = `مستوى الألم: ${value} - <span style="color:green;">ألم ضعيف</span>`;
            } else if (value >= 4 && value <= 6) {
                result.innerHTML =
                    `مستوى الألم: ${value} - <span style="color:orange;">ألم متوسط</span>`;
            } else {
                result.innerHTML = `مستوى الألم: ${value} - <span style="color:red;">ألم شديد</span>`;
            }
            $('#digital_assessment').val(value);
        });
    });

    const faces = document.querySelectorAll('.face');
    faces.forEach(face => {
        face.addEventListener('click', function() {
            faces.forEach(f => f.classList.remove('selected'));
            this.classList.add('selected');
            const value = this.getAttribute('data-value');
            let description = "";
            if (value == 0) description = "لا يوجد ألم";
            else if (value == 2) description = "ألم خفيف";
            else if (value == 4) description = "ألم متوسط";
            else if (value == 6) description = "ألم متوسط - شديد";
            else if (value == 8) description = "ألم شديد";
            else if (value == 10) description = "ألم شديد جدًا";
            resultface.innerHTML = `مستوى الألم: ${value} - ${description}`;
            $('#face_assessment').val(value);

        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // جعل خلايا الجدول قابلة للتحديد
        document.querySelectorAll('.selectable-cell').forEach(cell => {
            cell.addEventListener('click', function() {
                this.classList.toggle('selected');
            });
        });

        // التعامل مع radio buttons داخل الجدول فقط
        document.querySelectorAll('.card table').forEach(table => {
            table.addEventListener('change', function(event) {
                if (event.target.type === 'radio') {
                    let totalScore = 0;

                    // حساب المجموع فقط من الخيارات المحددة داخل نفس الجدول
                    table.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                        totalScore += parseInt(radio.value);
                    });

                    // تحديث النتيجة في الجدول نفسه
                    table.closest('.card').querySelector('#total-score').textContent =
                        totalScore;
                    table.closest('.card').querySelector('#child_assessment').value =
                    totalScore;
                }
            });
        });
    });
</script>

<script>
    // قاموس لتحويل أسماء أنواع الألم من العربية إلى الإنجليزية
    let painTypeTranslations = {
        "موجع": "Dull",
        "محرق": "Burning",
        "تقلص": "Cramping",
        "ساحق": "Crushing",
        "ثقيل": "Heavy",
        "تنميل": "Tingling",
        "وخز": "Pricking",
        "طعن": "Stabbing",
        "منبض": "Pulsating"
    };

    function submitPainAssessments() {
        // تجميع البيانات من النموذج
        let selectedPainType = $("input[name='pain-type']:checked").val();
        let translatedPainType = painTypeTranslations[selectedPainType] || null;
        var data = {
            assessment: [],
            degree: [],
            pain_location: [],
            continuous_intermittent: [],
            pain_type_t: [],
            increases_during: [],
            decreases_during: [],
            treatment: [],
            reassessment: [],
            id_patient: $("#id_patient").val(),
            id_visit: $("#id_visit").val(),
            id_reservation: $("#id_reservation").val(),
            type_visit: $("#type_visit").val(),
            digital_assessment: $("#digital_assessment").val(),
            face_assessment: $("#face_assessment").val(),
            child_assessment: $("#child_assessment").val(),
            notes: $("#additional_notes").val(),
            status: "pending",
            pain_typef: translatedPainType,
            user_id: $("#user_id").val(),
            _token: $('meta[name="csrf-token"]').attr("content") // تأمين الطلب عبر CSRF
        };

        // جمع البيانات من المدخلات التي تحتوي على تقييم الألم
        $("input[name='assessment[]']").each(function() {
            data.assessment.push($(this).val());
        });

        $("input[name='degree[]']").each(function() {
            data.degree.push($(this).val());
        });

        $("input[name='pain-location[]']").each(function() {
            data.pain_location.push($(this).val());
        });

        $("input[name='continuous-intermittent[]']").each(function() {
            data.continuous_intermittent.push($(this).val());
        });


        $("input[name='pain-type[]']").each(function() {
            data.pain_type_t.push($(this).val());
        });
        $("input[name='increases-during[]']").each(function() {
            data.increases_during.push($(this).val());
        });

        $("input[name='decreases-during[]']").each(function() {
            data.decreases_during.push($(this).val());
        });

        $("input[name='treatment[]']").each(function() {
            data.treatment.push($(this).val());
        });

        $("input[name='reassessment[]']").each(function() {
            data.reassessment.push($(this).val());
        });

        // إرسال البيانات عبر AJAX
        $.ajax({
            url: "{{ route('pain_assessment.store') }}",
            type: "POST",
            data: data,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم حفظ التقييمات بنجاح!',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = "<ul>";
                $.each(errors, function(key, value) {
                    errorMessage += "<li>" + value[0] + "</li>";
                });
                errorMessage += "</ul>";
                Swal.fire({
                    icon: 'error',
                    title: 'حدث خطأ!',
                    html: errorMessage
                });
            }
        });
    }
</script>
