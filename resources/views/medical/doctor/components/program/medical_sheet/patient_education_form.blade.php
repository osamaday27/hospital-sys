  <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <form method="POST" id="patient_education_form">
                  @csrf
                  <div class="card-header">
                      <h4>نموذج تثقيف المرضى/ذويهم</h4>
                  </div>
                  <div class="card-body">
                      <div class="row">
                          <!-- عرض جميع رسائل الأخطاء في بداية النموذج -->
                          @if ($errors->any())
                              <div class="alert alert-danger">
                                  <ul>
                                      @foreach ($errors->all() as $error)
                                          <li>{{ $error }}</li>
                                      @endforeach
                                  </ul>
                              </div>
                          @endif

                          <!-- 🟢 القسم الأول: تقييم الاحتياجات -->
                          <div class="card mb-3">
                              <div class="card-header">
                                  <h5 class="card-title">تقييم الاحتياجات</h5>
                              </div>
                              <div class="card-body">
                                  <div class="row">
                                      <div class="col-md-4">
                                          <label for="assessment_date" class="form-label">تاريخ التقييم:</label>
                                          <input type="date"
                                              class="form-control @error('assessment_date') is-invalid @enderror"
                                              id="assessment_date" name="assessment_date"
                                              value="{{ old('assessment_date') }}" required>
                                          @error('assessment_date')
                                              <div class="invalid-feedback">{{ $message }}</div>
                                          @enderror
                                      </div>
                                      <div class="col-md-4">
                                          <label for="assessment_time" class="form-label">الوقت:</label>
                                          <input type="time"
                                              class="form-control @error('assessment_time') is-invalid @enderror"
                                              id="assessment_time" name="assessment_time"
                                              value="{{ old('assessment_time') }}" required>
                                          @error('assessment_time')
                                              <div class="invalid-feedback">{{ $message }}</div>
                                          @enderror
                                      </div>
                                      <div class="col-md-4">
                                          <label for="department" class="form-label">القسم:</label>
                                          <input type="text"
                                              class="form-control @error('department') is-invalid @enderror"
                                              id="department" name="department" value="{{ old('department') }}"
                                              required>
                                          @error('department')
                                              <div class="invalid-feedback">{{ $message }}</div>
                                          @enderror
                                      </div>
                                  </div>
                              </div>
                          </div>



                          <div class="col-lg-12">
                              <div class="mb-3">
                                  <label class="form-label fw-bold">متلقي التثقيف:</label>
                                  <div class="form-check form-check-inline">
                                      <input type="radio"
                                          class="form-check-input @error('education_recipient') is-invalid @enderror"
                                          id="patient" name="education_recipient" value="المريض"
                                          {{ old('education_recipient') == 'المريض' ? 'checked' : '' }} required>
                                      <label class="form-check-label" for="patient">المريض</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input type="radio"
                                          class="form-check-input @error('education_recipient') is-invalid @enderror"
                                          id="others" name="education_recipient" value="آخرون"
                                          {{ old('education_recipient') == 'آخرون' ? 'checked' : '' }} required>
                                      <label class="form-check-label" for="others">آخرون</label>
                                  </div>
                                  @error('education_recipient')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>

                          <div class="col-lg-12">
                              <div class="mb-3 d-inline-block">
                                  <label for="relation_to_patient" class="form-label">علاقته بالمريض:</label>
                                  <input type="text"
                                      class="form-control form-control-sm @error('relation_to_patient') is-invalid @enderror"
                                      id="relation_to_patient" name="relation_to_patient"
                                      value="{{ old('relation_to_patient') }}" required disabled
                                      style="width: auto; display: inline-block;">
                                  @error('relation_to_patient')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>

                          <script>
                              document.addEventListener('DOMContentLoaded', function() {
                                  let othersRadio = document.getElementById('others');
                                  let relationInput = document.getElementById('relation_to_patient');

                                  // Toggle the "علاقته بالمريض" input based on the radio button selection
                                  function toggleRelationInput() {
                                      relationInput.disabled = !othersRadio.checked;
                                  }

                                  // Add event listeners for the radio buttons
                                  othersRadio.addEventListener('change', toggleRelationInput);
                                  document.getElementById('patient').addEventListener('change', toggleRelationInput);

                                  // Initialize the state when the page loads
                                  toggleRelationInput();
                              });
                          </script>

                          <!-- ✅ مستوى التعليم -->
                          <div class="col-lg-12">
                              <div class="mb-3">
                                  <label class="form-label fw-bold">مستوى التعليم:</label>
                                  <div class="form-check form-check-inline">
                                      <input type="radio"
                                          class="form-check-input @error('education_level') is-invalid @enderror"
                                          id="educated" name="education_level" value="متعلم"
                                          {{ old('education_level') == 'متعلم' ? 'checked' : '' }} required>
                                      <label class="form-check-label" for="educated">متعلم</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input type="radio"
                                          class="form-check-input @error('education_level') is-invalid @enderror"
                                          id="uneducated" name="education_level" value="غير متعلم"
                                          {{ old('education_level') == 'غير متعلم' ? 'checked' : '' }} required>
                                      <label class="form-check-label" for="uneducated">غير متعلم</label>
                                  </div>
                                  @error('education_level')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>

                          <!-- ✅ قابلية التعلم -->
                          <div class="col-lg-12">
                              <div class="mb-3">
                                  <label class="form-label fw-bold">قابلية التعلم:</label>
                                  <div class="form-check form-check-inline">
                                      <input type="radio"
                                          class="form-check-input @error('learning_ability') is-invalid @enderror"
                                          id="learning_yes" name="learning_ability" value="نعم"
                                          {{ old('learning_ability') == 'نعم' ? 'checked' : '' }} required>
                                      <label class="form-check-label" for="learning_yes">نعم</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input type="radio"
                                          class="form-check-input @error('learning_ability') is-invalid @enderror"
                                          id="learning_no" name="learning_ability" value="لا"
                                          {{ old('learning_ability') == 'لا' ? 'checked' : '' }} required>
                                      <label class="form-check-label" for="learning_no">لا</label>
                                  </div>
                                  @error('learning_ability')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>
                          <!-- ✅ اللغة الأساسية -->
                          <div class="col-lg-12">
                              <div class="mb-3">
                                  <label class="form-label fw-bold">اللغة الأساسية:</label>
                                  <div class="form-check form-check-inline">
                                      <input type="radio"
                                          class="form-check-input @error('main_language') is-invalid @enderror"
                                          id="arabic" name="main_language" value="عربي"
                                          {{ old('main_language') == 'عربي' ? 'checked' : '' }} required>
                                      <label class="form-check-label" for="arabic">عربي</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input type="radio"
                                          class="form-check-input @error('main_language') is-invalid @enderror"
                                          id="other_language" name="main_language" value="أخرى"
                                          {{ old('main_language') == 'أخرى' ? 'checked' : '' }} required>
                                      <label class="form-check-label" for="other_language">تحديد أخرى:</label>
                                  </div>
                                  <input type="text"
                                      class="form-control d-inline-block w-auto ms-2 @error('other_language') is-invalid @enderror"
                                      name="other_language" id="other_language_input"
                                      value="{{ old('other_language') }}" placeholder="اكتب اللغة"
                                      {{ old('main_language') == 'أخرى' ? '' : 'disabled' }}>
                                  @error('main_language')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                  @enderror
                                  @error('other_language')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>

                          <script>
                              document.addEventListener('DOMContentLoaded', function() {
                                  let otherLanguageRadio = document.getElementById('other_language');
                                  let otherLanguageInput = document.getElementById('other_language_input');

                                  function toggleOtherLanguage() {
                                      otherLanguageInput.disabled = !otherLanguageRadio.checked;
                                  }

                                  otherLanguageRadio.addEventListener('change', toggleOtherLanguage);
                                  document.getElementById('arabic').addEventListener('change', toggleOtherLanguage);

                                  toggleOtherLanguage();
                              });
                          </script>


                          <hr> <!-- خط فاصل بين الأقسام -->

                          <!-- ✅ التعليمات -->
                          <p class="fw-bold">التعليمات: للعوائق والتدخلات ضع علامة في المربع المناسب، يمكنك وضع علامة
                              في
                              أكثر من مربع واحد تحت كل فئة</p>

                          <!-- ✅ العوائق -->
                          <div class="section-title fw-bold">العوائق</div>
                          <div class="mb-3">
                              <div class="row">
                                  <!-- 🟢 الصف الأول -->
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="noIssue"
                                          name="barriers[]" value="لا يوجد"
                                          {{ in_array('لا يوجد', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="noIssue">لا يوجد</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="fearAnxiety"
                                          name="barriers[]" value="القلق / الخوف"
                                          {{ in_array('القلق / الخوف', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="fearAnxiety">القلق / الخوف</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="languageDiff"
                                          name="barriers[]" value="اختلاف اللغة"
                                          {{ in_array('اختلاف اللغة', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="languageDiff">اختلاف اللغة</label>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- 🟢 الصف الثاني -->
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="hopelessness"
                                          name="barriers[]" value="فقدان الأمل / اليأس"
                                          {{ in_array('فقدان الأمل / اليأس', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="hopelessness">فقدان الأمل / اليأس</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="education"
                                          name="barriers[]" value="التعليم"
                                          {{ in_array('التعليم', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="education">التعليم</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="beliefsValues"
                                          name="barriers[]" value="المعتقدات والقيم"
                                          {{ in_array('المعتقدات والقيم', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="beliefsValues">المعتقدات والقيم</label>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- 🟢 الصف الثالث -->
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="cultureDiff"
                                          name="barriers[]" value="اختلاف الثقافة"
                                          {{ in_array('اختلاف الثقافة', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="cultureDiff">اختلاف الثقافة</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="physicalDisability"
                                          name="barriers[]" value="إعاقة بدنية"
                                          {{ in_array('إعاقة بدنية', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="physicalDisability">إعاقة بدنية</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="painDiscomfort"
                                          name="barriers[]" value="ألم / عدم الراحة"
                                          {{ in_array('ألم / عدم الراحة', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="painDiscomfort">ألم / عدم الراحة</label>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- 🟢 الصف الرابع -->
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="cognitiveImpairment"
                                          name="barriers[]" value="ضعف الإدراك"
                                          {{ in_array('ضعف الإدراك', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="cognitiveImpairment">ضعف الإدراك</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="emotionalState"
                                          name="barriers[]" value="العاطفة / الحالة النفسية"
                                          {{ in_array('العاطفة / الحالة النفسية', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="emotionalState">العاطفة / الحالة النفسية</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="child"
                                          name="barriers[]" value="طفل"
                                          {{ in_array('طفل', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="child">طفل</label>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- 🟢 الصف الخامس (أخرى + إدخال نصي) -->
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="otherBarrier"
                                          name="barriers[]" value="أخرى" onclick="toggleBarrierInput()"
                                          {{ in_array('أخرى', old('barriers', [])) ? 'checked' : '' }}>
                                      <label for="otherBarrier">أخرى:</label>
                                  </div>
                                  <div class="col-md-8">
                                      <input type="text"
                                          class="form-control @error('other_barrier') is-invalid @enderror"
                                          name="other_barrier" id="otherBarrierInput"
                                          value="{{ old('other_barrier') }}" placeholder="حدد العائق"
                                          {{ in_array('أخرى', old('barriers', [])) ? '' : 'disabled' }}>
                                      @error('other_barrier')
                                          <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
                              </div>
                          </div>

                          <script>
                              function toggleBarrierInput() {
                                  let otherBarrier = document.getElementById('otherBarrier');
                                  let otherBarrierInput = document.getElementById('otherBarrierInput');
                                  otherBarrierInput.disabled = !otherBarrier.checked;
                              }

                              document.addEventListener('DOMContentLoaded', toggleBarrierInput);
                          </script>


                          <hr> <!-- خط فاصل بين الأقسام -->

                          <!-- ✅ التدخلات -->
                          <div class="section-title fw-bold">التدخلات لتقليل العوائق</div>
                          <div class="mb-3">
                              <div class="row">
                                  <!-- الصف الأول -->
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="noIssue"
                                          name="interventions[]" value="لا يوجد"
                                          {{ in_array('لا يوجد', old('interventions', [])) ? 'checked' : '' }}>
                                      <label for="noIssue">لا يوجد</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="translation"
                                          name="interventions[]" value="توفير وسيلة للترجمة"
                                          {{ in_array('توفير وسيلة للترجمة', old('interventions', [])) ? 'checked' : '' }}>
                                      <label for="translation">توفير وسيلة للترجمة</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="educationOthers"
                                          name="interventions[]" value="تثقيف الأهل / آخرين"
                                          {{ in_array('تثقيف الأهل / آخرين', old('interventions', [])) ? 'checked' : '' }}>
                                      <label for="educationOthers">تثقيف الأهل / آخرين</label>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- الصف الثاني -->
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="beliefsValues"
                                          name="interventions[]" value="احترام المعتقدات والقيم"
                                          {{ in_array('احترام المعتقدات والقيم', old('interventions', [])) ? 'checked' : '' }}>
                                      <label for="beliefsValues">احترام المعتقدات والقيم</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="repetitionReview"
                                          name="interventions[]" value="التكرار / المراجعة"
                                          {{ in_array('التكرار / المراجعة', old('interventions', [])) ? 'checked' : '' }}>
                                      <label for="repetitionReview">التكرار / المراجعة</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="reassuranceSupport"
                                          name="interventions[]" value="الطمأنة والدعم"
                                          {{ in_array('الطمأنة والدعم', old('interventions', [])) ? 'checked' : '' }}>
                                      <label for="reassuranceSupport">الطمأنة والدعم</label>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- الصف الثالث -->
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="cultureRespect"
                                          name="interventions[]" value="احترام الثقافات / الديانات"
                                          {{ in_array('احترام الثقافات / الديانات', old('interventions', [])) ? 'checked' : '' }}>
                                      <label for="cultureRespect">احترام الثقافات / الديانات</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="checkbox" class="form-check-input" id="otherOption"
                                          name="interventions[]" value="أخرى" onclick="toggleInterventionInput()"
                                          {{ in_array('أخرى', old('interventions', [])) ? 'checked' : '' }}>
                                      <label for="otherOption">أخرى:</label>
                                  </div>
                                  <div class="col-md-4">
                                      <input type="text"
                                          class="form-control @error('other_intervention') is-invalid @enderror"
                                          name="other_intervention" id="otherInput"
                                          value="{{ old('other_intervention') }}" placeholder="أدخل التفاصيل"
                                          {{ in_array('أخرى', old('interventions', [])) ? '' : 'disabled' }}>
                                      @error('other_intervention')
                                          <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
                              </div>
                          </div>

                          <script>
                              function toggleInterventionInput() {
                                  let otherOption = document.getElementById('otherOption');
                                  let otherInput = document.getElementById('otherInput');
                                  otherInput.disabled = !otherOption.checked;
                              }

                              document.addEventListener('DOMContentLoaded', toggleInterventionInput);
                          </script>
                          <!-- ✅ بيانات الممرضة -->
                          <div class="row mb-3">
                              <div class="col-md-4">
                                  <label class="form-label fw-bold">اسم الممرضة:</label>
                                  <input type="text"
                                      class="form-control @error('nurse_name') is-invalid @enderror"
                                      name="nurse_name" value="{{ old('nurse_name') }}"
                                      placeholder="ادخل اسم الممرضة">
                                  @error('nurse_name')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="col-md-4">
                                  <label class="form-label fw-bold">التاريخ:</label>
                                  <input type="date"
                                      class="form-control @error('nurse_date') is-invalid @enderror"
                                      name="nurse_date" value="{{ old('nurse_date') }}">
                                  @error('nurse_date')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="col-md-4">
                                  <label class="form-label fw-bold">الوقت:</label>
                                  <input type="time"
                                      class="form-control @error('nurse_time') is-invalid @enderror"
                                      name="nurse_time" value="{{ old('nurse_time') }}">
                                  @error('nurse_time')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>
                          <hr> <!-- خط فاصل بين الأقسام -->

                          <hr> <!-- خط فاصل بين الأقسام -->
                          <!-- ✅ جدول تثقيف المرضى -->
                          <div class="section-title fw-bold text-center mb-3">تثقيف المرضى/ذويهم</div>
                          <div class="table-responsive">
                              <table class="table table-bordered text-center align-middle">
                                  <thead class="table-light">
                                      <tr>
                                          <th rowspan="2">ضع ✔ في حالة وجود احتياج للتثقيف</th>
                                          <th colspan="2">الجلسة الأولى</th>
                                          <th colspan="2">الجلسة الثانية</th>
                                          <th colspan="2">الجلسة الثالثة</th>
                                      </tr>
                                      <tr>
                                          <th>التاريخ</th>
                                          <th>القائم بالتثقيف</th>
                                          <th>التاريخ</th>
                                          <th>القائم بالتثقيف</th>
                                          <th>التاريخ</th>
                                          <th>القائم بالتثقيف</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @php
                                          $topics = [
                                              'التشخيص والحالة الحالية',
                                              'خطة الرعاية / البدائل',
                                              'النتيجة المتوقعة',
                                              'علاج الألم',
                                              'الأدوية',
                                              'تعليمات الخروج/المتابعة',
                                              'العناية بالجرح / stoma',
                                              'زرعات/الأجهزة القابلة للزرع',
                                              'الاستخدام الآمن للأدوات / الأجهزة',
                                              'النظام الغذائي',
                                              'الموافقات المستنيرة',
                                              'إجراءات غسيل الأيدى',
                                              'الإقلاع عن التدخين',
                                              'حقوق وواجبات المرضى',
                                              'مخاطر السقوط',
                                              'رعاية ما قبل / بعد الولادة',
                                              'الرعاية الشخصية / النظافة',
                                              'آداب السعال',
                                              'أخرى:',
                                          ];
                                      @endphp

                                      @foreach ($topics as $index => $topic)
                                          <tr>
                                              <td class="text-start">
                                                  <label class="form-check-label fw-bold">{{ $topic }}</label>
                                                  <input type="checkbox" class="form-check-input ms-2"
                                                      name="education_sessions[{{ $index }}][checked]"
                                                      value="1"
                                                      {{ old("education.$index.checked") ? 'checked' : '' }}>
                                              </td>
                                              @for ($i = 1; $i <= 3; $i++)
                                                  <td>
                                                      <input type="date"
                                                          class="form-control @error("education.$index.session_$i.date") is-invalid @enderror"
                                                          name="education_sessions[{{ $index }}][session_{{ $i }}][date]"
                                                          value="{{ old("education.$index.session_$i.date") }}">
                                                      @error("education.$index.session_$i.date")
                                                          <div class="invalid-feedback">{{ $message }}</div>
                                                      @enderror
                                                  </td>
                                                  <td>
                                                      <input type="text"
                                                          class="form-control @error("education.$index.session_$i.trainer") is-invalid @enderror"
                                                          name="education_sessions[{{ $index }}][session_{{ $i }}][trainer]"
                                                          value="{{ old("education.$index.session_$i.trainer") }}"
                                                          placeholder="اسم القائم بالتثقيف">
                                                      @error("education.$index.session_$i.trainer")
                                                          <div class="invalid-feedback">{{ $message }}</div>
                                                      @enderror
                                                  </td>
                                              @endfor
                                          </tr>
                                      @endforeach
                                  </tbody>
                              </table>
                          </div>
                          <hr> <!-- خط فاصل بين الأقسام -->
                          <!-- ✅ طريقة التعليم / التثقيف -->
                          <div class="section-title fw-bold text-center mb-3">طريقة التعليم / التثقيف</div>
                          <div class="row mb-3">
                              <div class="col-md-3">
                                  <label class="form-label">تعليمات شفهية</label>
                                  <input type="checkbox" class="custom-checkbox"
                                      name="education_method[oral_instructions]" value="1"
                                      {{ old('education_method.oral_instructions') ? 'checked' : '' }}>
                              </div>
                              <div class="col-md-3">
                                  <label class="form-label">نموذج / عينات</label>
                                  <input type="checkbox" class="custom-checkbox"
                                      name="education_method[models_samples]" value="1"
                                      {{ old('education_method.models_samples') ? 'checked' : '' }}>
                              </div>
                              <div class="col-md-3">
                                  <label class="form-label">مواد كتابية</label>
                                  <input type="checkbox" class="custom-checkbox"
                                      name="education_method[written_materials]" value="1"
                                      {{ old('education_method.written_materials') ? 'checked' : '' }}>
                              </div>
                              <div class="col-md-3">
                                  <label class="form-label">سمعي / بصري</label>
                                  <input type="checkbox" class="custom-checkbox"
                                      name="education_method[audio_visual]" value="1"
                                      {{ old('education_method.audio_visual') ? 'checked' : '' }}>
                              </div>
                          </div>

                          <!-- ✅ تقييم المتلقى -->
                          <div class="section-title fw-bold text-center mb-3">تقييم المتلقى</div>
                          <div class="row mb-3">
                              <div class="col-md-3">
                                  <label class="form-label">مستوعب</label>
                                  <input type="radio" name="evaluation" value="understood" class="custom-radio"
                                      {{ old('evaluation') == 'understood' ? 'checked' : '' }}>
                              </div>
                              <div class="col-md-3">
                                  <label class="form-label">غير مستوعب</label>
                                  <input type="radio" name="evaluation" value="not_understood"
                                      class="custom-radio"
                                      {{ old('evaluation') == 'not_understood' ? 'checked' : '' }}>
                              </div>
                              <div class="col-md-6">
                                  <label class="form-label">الإجراء المتخذ</label>
                                  <input type="text" class="form-control" name="action_taken"
                                      value="{{ old('action_taken') }}" placeholder="أدخل الإجراء المتخذ">
                              </div>
                          </div>

                          <!-- ✅ زر الحفظ -->
                          <button type="submit" class="btn btn-primary">حفظ</button>
                      </div>
                  </div>
              </form>


          </div>
      </div>
  </div>
  <script>
      $(document).ready(function() {
          // معالجة الطلب عند إرسال النموذج
          $('#patient_education_form').on('submit', function(e) {
              e.preventDefault();

              let formData = $(this).serialize();

              $.ajax({
                  url: "{{ route('patienteducation.store') }}",
                  type: "POST",
                  data: formData,
                  success: function(response) {
                      Swal.fire({
                          icon: 'success',
                          title: 'تم الحجز بنجاح',
                          text: response.message,
                      });
                      $('#testForm')[0].reset();
                      table.ajax.reload();
                  },
                  error: function(xhr) {
                      let errors = xhr.responseJSON.errors;
                      let errorMessage = '';
                      $.each(errors, function(key, value) {
                          errorMessage += value[0] + '\n';
                      });

                      Swal.fire({
                          icon: 'error',
                          title: 'خطأ في الإدخال',
                          text: errorMessage,
                      });
                  }
              });
          });
      });
  </script>
