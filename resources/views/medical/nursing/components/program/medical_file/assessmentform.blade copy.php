<div class="tab-pane fade" id="assessmentform" role="tabpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-user-md mr-2"></i>{{ __('doctor_trans.examineing') }}</h4>
                </div>
                <div class="card-body">
                    <style>
                        .form-container {
                            background-color: #f8fafc;
                            padding: 25px;
                            border-radius: 10px;
                            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
                            margin-bottom: 25px;
                            border: 1px solid #e2e8f0;
                        }

                        .section-title {
                            font-size: 18px;
                            font-weight: 600;
                            color: #2d3748;
                            margin-top: 25px;
                            margin-bottom: 15px;
                            padding-bottom: 8px;
                            border-bottom: 2px solid #4299e1;
                            position: relative;
                        }

                        .section-title:after {
                            content: "";
                            position: absolute;
                            bottom: -2px;
                            left: 0;
                            width: 100px;
                            height: 2px;
                            background: #3182ce;
                        }

                        .form-table {
                            width: 100%;
                            border-collapse: separate;
                            border-spacing: 0;
                            margin-bottom: 20px;
                            border-radius: 8px;
                            overflow: hidden;
                            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                        }

                        .form-table th {
                            background-color: #4299e1;
                            color: white;
                            font-weight: 500;
                            padding: 12px 15px;
                            text-align: right;
                            width: 25%;
                        }

                        .form-table td {
                            background-color: white;
                            padding: 12px 15px;
                            border: 1px solid #e2e8f0;
                        }

                        .checkbox-group {
                            display: flex;
                            align-items: center;
                            margin-bottom: 8px;
                            padding: 5px;
                            border-radius: 4px;
                            transition: all 0.2s;
                        }

                        .checkbox-group:hover {
                            background-color: #ebf8ff;
                        }

                        .checkbox-group label {
                            margin-right: 8px;
                            cursor: pointer;
                            color: #4a5568;
                        }

                        .checkbox-group input[type="checkbox"] {
                            transform: scale(1.2);
                            margin-left: 8px;
                            cursor: pointer;
                        }

                        .form-control-custom {
                            border: 1px solid #cbd5e0;
                            border-radius: 6px;
                            padding: 8px 12px;
                            transition: all 0.3s;
                            width: 100%;
                        }

                        .form-control-custom:focus {
                            border-color: #4299e1;
                            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
                            outline: none;
                        }

                        .note {
                            font-size: 13px;
                            color: #718096;
                            font-style: italic;
                            margin-top: 5px;
                        }

                        .card-section {
                            margin-bottom: 25px;
                            border: none;
                            border-radius: 10px;
                            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                        }

                        .card-section .card-header {
                            background-color: #edf2f7;
                            border-bottom: 1px solid #e2e8f0;
                            font-weight: 600;
                            color: #2d3748;
                            padding: 12px 20px;
                        }

                        .card-section .card-body {
                            padding: 20px;
                        }

                        .form-check-label {
                            color: #4a5568;
                        }

                        .form-check-input {
                            transform: scale(1.1);
                            margin-left: 5px;
                        }

                        .btn-save {
                            background-color: #4299e1;
                            border: none;
                            padding: 10px 25px;
                            font-weight: 500;
                            letter-spacing: 0.5px;
                            transition: all 0.3s;
                        }

                        .btn-save:hover {
                            background-color: #3182ce;
                            transform: translateY(-2px);
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        }

                        h2.form-title {
                            color: #2d3748;
                            font-weight: 600;
                            text-align: center;
                            margin-bottom: 30px;
                            position: relative;
                            padding-bottom: 15px;
                        }

                        h2.form-title:after {
                            content: "";
                            position: absolute;
                            bottom: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 100px;
                            height: 3px;
                            background: linear-gradient(to right, #4299e1, #38b2ac);
                        }

                        .radio-group {
                            display: flex;
                            align-items: center;
                            margin-bottom: 10px;
                        }

                        .radio-group label {
                            margin-left: 10px;
                            margin-right: 20px;
                            cursor: pointer;
                        }

                        .nutrition-table th {
                            background-color: #e2e8f0;
                            color: #2d3748;
                            font-weight: 600;
                        }

                        .nutrition-table td {
                            text-align: center;
                            vertical-align: middle;
                        }

                        .nutrition-table .form-control {
                            border-radius: 4px;
                            border: 1px solid #cbd5e0;
                        }
                    </style>

                    <h2 class="form-title">
                        <i class="fas fa-file-medical-alt mr-2"></i>
                        نموذج الفحص الاجتماعي الاقتصادي والثقافي والنفسي
                    </h2>
                    <form method="POST" action="{{ route('reassessmentform.store') }}">
                        @csrf
                      <input type="hidden" value="{{ $patient->id }}" name="patient_id"/>
                      <input type="hidden" value="{{ $patient_visits->id?? 'null' }}" name="visit_id"/>
                      <input type="hidden" value="{{ $reservations->id?? 'null' }}" name="reservation_id"/>
  
                    <!-- الفحص الاجتماعي الاقتصادي والثقافي والنفسي -->
                    <div class="form-container">
                        <div class="section-title">
                            <i class="fas fa-home mr-2"></i>
                            الفحص الاجتماعي الاقتصادي والثقافي والنفسي
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>الحالة المعيشية</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="living-alone" name="living-alone" class="form-check-input">
                                                <label for="living-alone">يعيش بمفرده</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="living-with-family" name="living-with-family" class="form-check-input">
                                                <label for="living-with-family">يعيش مع الأسرة</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="living-with-others" name="living-with-others" class="form-check-input">
                                                <label for="living-with-others">يعيش مع آخرين</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="living-other" name="living-other" class="form-check-input">
                                                <label for="living-other">أخرى</label>
                                                <input type="text" id="living-other-details" name="living-other-details" class="form-control-custom" placeholder="اذكر التفاصيل" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>احتياجات خاصة</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="special-needs-none" name="special-needs-none" class="form-check-input">
                                                <label for="special-needs-none">لا يوجد</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="hearing" name="hearing" class="form-check-input">
                                                <label for="hearing">سمع</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="speech" name="speech" class="form-check-input">
                                                <label for="speech">تحدث</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="language-barrier" name="language-barrier" class="form-check-input">
                                                <label for="language-barrier">عائق لغة</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="other-needs" name="other-needs" class="form-check-input">
                                                <label for="other-needs">أخرى</label>
                                                <input type="text" id="other-needs-details" name="other-needs-details" class="form-control-custom" placeholder="اذكر التفاصيل" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>الحالة الاجتماعية</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="single" name="single" class="form-check-input">
                                                <label for="single">أعزب</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="married" name="married" class="form-check-input">
                                                <label for="married">متزوج</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="has-children" name="has-children" class="form-check-input">
                                                <label for="has-children">يوجد أطفال</label>
                                                <input type="text" id="number-of-children" name="number-of-children" class="form-control-custom" placeholder="عدد الأطفال" style="width: 50%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>هل تدخن؟</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="smoking-no" name="smoking-no" class="form-check-input">
                                                <label for="smoking-no">لا</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="smoking-yes" name="smoking-yes" class="form-check-input">
                                                <label for="smoking-yes">نعم</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="smoking-previous" name="smoking-previous" class="form-check-input">
                                                <label for="smoking-previous">سابقاً</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="text" id="cigarettes-per-day" name="cigarettes-per-day" class="form-control-custom" placeholder="عدد السجائر يومياً" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>الفحص النفسي</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="no-psychological-issues" name="no-psychological-issues" class="form-check-input">
                                                <label for="no-psychological-issues">لا يوجد</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="anxiety" name="anxiety" class="form-check-input">
                                                <label for="anxiety">قلق</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="anger" name="anger" class="form-check-input">
                                                <label for="anger">غضبان</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="depression" name="depression" class="form-check-input">
                                                <label for="depression">مكتئب</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>التغطية الطبية</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="can-afford" name="can-afford" class="form-check-input">
                                                <label for="can-afford">قادر على التحمل</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="insured" name="insured" class="form-check-input">
                                                <label for="insured">مؤمن عليه</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="cannot-afford" name="cannot-afford" class="form-check-input">
                                                <label for="cannot-afford">غير قادر على تحمل التكلفة</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>وجود آثار للعنف أو سوء المعاملة</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="no-violence" name="no-violence" class="form-check-input">
                                                <label for="no-violence">لا يوجد</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="violence-present" name="violence-present" class="form-check-input">
                                                <label for="violence-present">يوجد</label>
                                                <input type="text" id="violence-details" name="violence-details" class="form-control-custom" placeholder="اذكر التفاصيل" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>الحالة الوظيفية</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="not-working" name="not-working" class="form-check-input">
                                                <label for="not-working">لا يعمل</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="working" name="working" class="form-check-input">
                                                <label for="working">يعمل</label>
                                                <input type="text" id="occupation" name="occupation" class="form-control-custom" placeholder="المهنة" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>يجب تنبيه الطبيب عند اختيار علامة*</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor-yes" name="notify-doctor-yes" class="form-check-input">
                                                <label for="notify-doctor-yes">تم تنبيه الطبيب (نعم)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor-no" name="notify-doctor-no" class="form-check-input">
                                                <label for="notify-doctor-no">لا</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- تقييم المرضى ممن لهم احتياجات خاصة -->
                    <div class="form-container">
                        <div class="section-title">
                            <i class="fas fa-wheelchair mr-2"></i>
                            تقييم المرضى ممن لهم احتياجات خاصة
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>في مرحلة المراهقة</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="no-exercise" name="no-exercise" class="form-check-input">
                                                <label for="no-exercise">لا يمارس الرياضة</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="overweight" name="overweight" class="form-check-input">
                                                <label for="overweight">زيادة مفرطة في الوزن</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="hates-school" name="hates-school" class="form-check-input">
                                                <label for="hates-school">يكره المدرسة</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="family-issues" name="family-issues" class="form-check-input">
                                                <label for="family-issues">مشكلة في التعامل مع الأهل</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>مسن (أكبر من 70 سنة)</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="normal-elderly" name="normal-elderly" class="form-check-input">
                                                <label for="normal-elderly">طبيعي</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="cannot-count" name="cannot-count" class="form-check-input">
                                                <label for="cannot-count">غير قادر على العد</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="doesnt-know-facility" name="doesnt-know-facility" class="form-check-input">
                                                <label for="doesnt-know-facility">لا يعرف اسم المرافق</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>إعاقة جسدية</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="physical-disability-yes" name="physical-disability-yes" class="form-check-input">
                                        <label for="physical-disability-yes">نعم</label>
                                        <input type="text" id="physical-disability-details" name="physical-disability-details" class="form-control-custom" placeholder="اذكر التفاصيل" style="width: 60%; display: inline-block;">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>ضعف المناعة</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="weak-immunity-yes" name="weak-immunity-yes" class="form-check-input">
                                        <label for="weak-immunity-yes">نعم</label>
                                        <input type="text" id="weak-immunity-reason" name="weak-immunity-reason" class="form-control-custom" placeholder="السبب" style="width: 60%; display: inline-block;">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>آلام مزمنة</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="chronic-pain-yes" name="chronic-pain-yes" class="form-check-input">
                                        <label for="chronic-pain-yes">نعم</label>
                                        <span class="note">(استخدم نموذج تقييم وعلاج الألم)</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>يجب تنبيه الطبيب عند اختيار علامة*</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor-special-yes" name="notify-doctor-special-yes" class="form-check-input">
                                                <label for="notify-doctor-special-yes">تم تنبيه الطبيب (نعم)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor-special-no" name="notify-doctor-special-no" class="form-check-input">
                                                <label for="notify-doctor-special-no">لا</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- فحص الأمراض المعدية -->
                    <div class="form-container">
                        <div class="section-title">
                            <i class="fas fa-virus mr-2"></i>
                            فحص الأمراض المعدية
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>كحة</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="cough_no" name="cough_no" class="form-check-input">
                                                <label for="cough_no">لا</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="cough_yes" name="cough_yes" class="form-check-input">
                                                <label for="cough_yes">نعم</label>
                                                <input type="text" id="cough_details" name="cough_details" class="form-control-custom" placeholder="حدد البداية" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>حمى</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="fever_no" name="fever_no" class="form-check-input">
                                                <label for="fever_no">لا</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="fever_yes" name="fever_yes" class="form-check-input">
                                                <label for="fever_yes">نعم</label>
                                                <input type="text" id="fever_details" name="fever_details" class="form-control-custom" placeholder="حدد البداية" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>طفح جلدي</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="rash_no" name="rash_no" class="form-check-input">
                                                <label for="rash_no">لا</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="rash_yes" name="rash_yes" class="form-check-input">
                                                <label for="rash_yes">نعم</label>
                                                <input type="text" id="rash_details" name="rash_details" class="form-control-custom" placeholder="حدد البداية" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>سفر للخارج في خلال الشهر الماضي</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="travel_no" name="travel_no" class="form-check-input">
                                                <label for="travel_no">لا</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="travel_yes" name="travel_yes" class="form-check-input">
                                                <label for="travel_yes">نعم</label>
                                                <input type="text" id="travel_details" name="travel_details" class="form-control-custom" placeholder="حدد البلد" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>الإجراءات المتخذة مع الحالات المشتبه بها</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="wear_mask" name="wear_mask" class="form-check-input">
                                                <label for="wear_mask">طلب من المريض ارتداء الكمامة</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="separate_patient" name="separate_patient" class="form-check-input">
                                                <label for="separate_patient">تطبيق التباعد بين المريض والآخرين في الانتظار</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor" name="notify-doctor" class="form-check-input">
                                                <label for="notify-doctor">إبلاغ الطبيب</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="accelerate-examination" name="accelerate-examination" class="form-check-input">
                                                <label for="accelerate-examination">إدخال المريض للطبيب خلال 15 دقيقة</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>يجب تنبيه الطبيب عند اختيار علامة*</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor-infectious-yes" name="notify-doctor-infectious-yes" class="form-check-input">
                                                <label for="notify-doctor-infectious-yes">تم تنبيه الطبيب (نعم)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor-infectious-no" name="notify-doctor-infectious-no" class="form-check-input">
                                                <label for="notify-doctor-infectious-no">لا</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- الفحص الحركي/الوظيفي -->
                    <div class="form-container">
                        <div class="section-title">
                            <i class="fas fa-walking mr-2"></i>
                            الفحص الحركي/الوظيفي
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>الاعتماد على الغير</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="live-alone" name="live-alone" class="form-check-input">
                                                <label for="live-alone">يعيش بمفرده</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="no-problem" name="no-problem" class="form-check-input">
                                                <label for="no-problem">لا يوجد مشكلة</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="total-dependence" name="total-dependence" class="form-check-input">
                                                <label for="total-dependence">اعتماد كلي</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="movement-problems" name="movement-problems" class="form-check-input">
                                                <label for="movement-problems">مشاكل في الحركة</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>الأجهزة المساعدة</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="no-assistance" name="no-assistance" class="form-check-input">
                                                <label for="no-assistance">لا يوجد</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="walker" name="walker" class="form-check-input">
                                                <label for="walker">مشاية</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="wheelchair" name="wheelchair" class="form-check-input">
                                                <label for="wheelchair">كرسي متحرك</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="crutches" name="crutches" class="form-check-input">
                                                <label for="crutches">عكاز</label>
                                            </div>
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="other-assistance" name="other-assistance" class="form-check-input">
                                                <label for="other-assistance">أخرى</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>يجب تنبيه الطبيب عند اختيار علامة*</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor-functional-yes" name="notify-doctor-functional-yes" class="form-check-input">
                                                <label for="notify-doctor-functional-yes">تم تنبيه الطبيب (نعم)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="notify-doctor-functional-no" name="notify-doctor-functional-no" class="form-check-input">
                                                <label for="notify-doctor-functional-no">لا</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- فحص الألم -->
                    <div class="form-container">
                        <div class="section-title">
                            <i class="fas fa-pain mr-2"></i>
                            فحص الألم
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>هل يوجد ألم؟</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="pain-no" name="pain-no" class="form-check-input">
                                                <label for="pain-no">لا</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="pain-yes" name="pain-yes" class="form-check-input">
                                                <label for="pain-yes">نعم</label>
                                                <span class="note">(استخدم نموذج تقييم وعلاج الألم)</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- الحقوق والواجبات -->
                    <div class="form-container">
                        <div class="section-title">
                            <i class="fas fa-balance-scale mr-2"></i>
                            الحقوق والواجبات
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>هل تم إخبارك بحقوقك وواجباتك؟</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="rights-yes" name="rights-yes" class="form-check-input">
                                                <label for="rights-yes">نعم</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="rights-no" name="rights-no" class="form-check-input">
                                                <label for="rights-no">لا</label>
                                                <input type="text" id="rights-action" name="rights-action" class="form-control-custom" placeholder="وضح الإجراء المتخذ" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- الحساسية/تفاعلات دواء عكسية -->
                    <div class="form-container">
                        <div class="section-title">
                            <i class="fas fa-allergies mr-2"></i>
                            الحساسية/تفاعلات دواء عكسية
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>هل يوجد مسببات للحساسية؟</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="allergies-no" name="allergies-no" class="form-check-input">
                                                <label for="allergies-no">لا</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-group">
                                                <input type="checkbox" id="allergies-yes" name="allergies-yes" class="form-check-input">
                                                <label for="allergies-yes">نعم</label>
                                                <input type="text" id="allergies-details" name="allergies-details" class="form-control-custom" placeholder="حدد المسببات" style="width: 60%; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- العلامات الحيوية -->
                    <div class="form-container">
                        <div class="section-title">
                            <i class="fas fa-heartbeat mr-2"></i>
                            العلامات الحيوية
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th>درجة الحرارة</th>
                                <td><input type="text" id="temperature" name="temperature" class="form-control-custom" placeholder="سيلزية"></td>
                            </tr>
                            <tr>
                                <th>معدل التنفس</th>
                                <td><input type="text" id="respiratory-rate" name="respiratory-rate" class="form-control-custom" placeholder="/دقيقة"></td>
                            </tr>
                            <tr>
                                <th>ضغط الدم</th>
                                <td><input type="text" id="blood-pressure" name="blood-pressure" class="form-control-custom" placeholder="مم زئبق"></td>
                            </tr>
                            <tr>
                                <th>النبض</th>
                                <td><input type="text" id="pulse" name="pulse" class="form-control-custom" placeholder="/دقيقة"></td>
                            </tr>
                            <tr>
                                <th>السكر العشوائي</th>
                                <td><input type="text" id="random-sugar" name="random-sugar" class="form-control-custom" placeholder="ملجم/دل"></td>
                            </tr>
                            <tr>
                                <th>الوزن</th>
                                <td><input type="text" id="weight" name="weight" class="form-control-custom" placeholder="كجم"></td>
                            </tr>
                            <tr>
                                <th>الطول</th>
                                <td><input type="text" id="height" name="height" class="form-control-custom" placeholder="سم"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- الفحص التغذوي -->
                    <div class="card card-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-utensils mr-2"></i>الفحص التغذوي - نوع الغذاء بالمنزل</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered text-center nutrition-table">
                                <thead>
                                    <tr>
                                        <th>طبيعة أكل خاصة</th>
                                        <th colspan="3">السكر</th>
                                        <th colspan="3">الملح</th>
                                        <th colspan="3">البروتين</th>
                                        <th colspan="3">الدهون</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>عالي</th>
                                        <th>قليل</th>
                                        <th>طبيعي</th>
                                        <th>عالي</th>
                                        <th>قليل</th>
                                        <th>طبيعي</th>
                                        <th>عالي</th>
                                        <th>قليل</th>
                                        <th>طبيعي</th>
                                        <th>عالي</th>
                                        <th>قليل</th>
                                        <th>طبيعي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" class="form-control form-control-custom"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td><input type="checkbox" class="form-check-input"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- تقييم التغذية -->
                    <div class="card card-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clipboard-check mr-2"></i>تقييم التغذية</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> طبيعي
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> غثيان
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> قيء
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> فقدان الوزن واضح
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> صعوبة بالبلع
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فحص السيدات وكبار السن -->
                    <div class="card card-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-friends mr-2"></i>فحص السيدات وكبار السن</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> حامل
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> ترضع
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> أكبر من ٧٠ سنة
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- دليل التقييم -->
                    <div class="card card-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clipboard-list mr-2"></i>دليل التقييم</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> عديم الخطورة (٠ - ٢ مخاطر)
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> *عالي الخطورة (أكثر من ٢ مخاطر)
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> تم تنبيه الطبيب: نعم
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> تم تنبيه الطبيب: لا
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فحص مخاطر السقوط/الفحص الوظيفي -->
                    <div class="card card-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-procedures mr-2"></i>فحص مخاطر السقوط/الفحص الوظيفي</h5>
                        </div>
                        <div class="card-body">
                            <p class="font-weight-bold">تطبق اجراءات السلامة الآتية لان جميع مرضي العيون من الفئات الأكثر عرضه للسقوط:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> قدم المساعدة للمريض عند الحاجة
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> تأكد من استخدام المريض وسائل مناسبة للمساعدة على الحركة
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> مراجعة الطبيب للأدوية الحالية للمريض وتغييرها إن لزم الأمر
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> تحويل المريض إلى التخصص المناسب إذا كانت الحالة المرضية تسبب خطر السقوط
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> تجنب زحام الحجرة، ترك الباب مفتوح، توفير إضاءة كافية، رفع جوانب السرير، ضبط ارتفاع السرير
                                    </div>
                                </div>
                            </div>
                            
                            <p class="font-weight-bold mt-4">توعية المريض/الأهل بمخاطر السقوط تشمل:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> عدم التحرك بمفرده وطلب المساعدة عند الحاجة
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> عدم الوقوف فجأة أو الانحناء لالتقاط الأشياء
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> عدم ترك الطفل بمفرده
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="form-check-input"> إبلاغ الفريق الطبي عند الشعور بالدوخة أو فقدان التوازن
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التخطيط للخروج -->
                    <div class="card card-section">
                        <div class="card-header bg-light-primary">
                            <h5 class="mb-0"><i class="fas fa-door-open mr-2"></i>التخطيط للخروج</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">مكان العناية بعد الخروج</label>
                                        <input type="text" class="form-control-custom" placeholder="أدخل المكان">
                                    </div>
                                    
                                    <div class="form-group mt-4">
                                        <label class="font-weight-bold">هل لديك مساعدة في البيت؟</label>
                                        <div class="radio-group">
                                            <input type="radio" name="help_at_home" id="help_yes" value="نعم">
                                            <label for="help_yes">نعم</label>
                                            <input type="radio" name="help_at_home" id="help_no" value="لا">
                                            <label for="help_no">لا</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">تستطيع العناية بنفسك دون مساعدة؟</label>
                                        <div class="radio-group">
                                            <input type="radio" name="self_care" id="self_care_yes" value="نعم">
                                            <label for="self_care_yes">نعم</label>
                                            <input type="radio" name="self_care" id="self_care_no" value="لا">
                                            <label for="self_care_no">لا</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mt-4">
                                        <label class="font-weight-bold">كل التعليمات تم شرحها بوضوح؟</label>
                                        <div class="radio-group">
                                            <input type="radio" name="instructions_clear" id="instructions_yes" value="نعم">
                                            <label for="instructions_yes">نعم</label>
                                            <input type="radio" name="instructions_clear" id="instructions_no" value="لا">
                                            <label for="instructions_no">لا</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h6 class="font-weight-bold">قد يحتاج المريض مساعدة للقيام بالمهام التالية:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="reminder" name="reminder" class="form-check-input">
                                            <label for="reminder">تذكره بالمواعيد</label>
                                        </div>
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="medication" name="medication" class="form-check-input">
                                            <label for="medication">تناول الأدوية</label>
                                        </div>
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="stairs" name="stairs" class="form-check-input">
                                            <label for="stairs">صعود السلالم</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="toilet" name="toilet" class="form-check-input">
                                            <label for="toilet">استخدام الحمام</label>
                                        </div>
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="bathing" name="bathing" class="form-check-input">
                                            <label for="bathing">الاستحمام</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h6 class="font-weight-bold">ملخص الفحص والتقييم التمريضي</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">تم تنبيه الطبيب</label>
                                            <div class="radio-group">
                                                <input type="radio" name="doctor_alert" id="doctor_alert_yes" value="نعم">
                                                <label for="doctor_alert_yes">نعم</label>
                                                <input type="radio" name="doctor_alert" id="doctor_alert_no" value="لا">
                                                <label for="doctor_alert_no">لا</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-bold">الفحص الاجتماعي، الاقتصادي والثقافي والنفسي</label>
                                            <div class="radio-group">
                                                <input type="radio" name="social_exam" id="social_exam_normal" value="طبيعي">
                                                <label for="social_exam_normal">طبيعي</label>
                                                <input type="radio" name="social_exam" id="social_exam_abnormal" value="غير طبيعي">
                                                <label for="social_exam_abnormal">غير طبيعي</label>
                                            </div>
                                            <input type="text" class="form-control-custom mt-2" placeholder="اذكر التفاصيل إذا كانت غير طبيعية">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-bold">المرضي ممن لهم احتياجات خاصة</label>
                                            <div class="radio-group">
                                                <input type="radio" name="special_needs" id="special_needs_yes" value="نعم">
                                                <label for="special_needs_yes">نعم</label>
                                                <input type="radio" name="special_needs" id="special_needs_no" value="لا">
                                                <label for="special_needs_no">لا</label>
                                            </div>
                                            <input type="text" class="form-control-custom mt-2" placeholder="اذكر التفاصيل إذا كانت نعم">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">فحص الأمراض المعدية</label>
                                            <div class="radio-group">
                                                <input type="radio" name="infectious_disease" id="infectious_disease_yes" value="نعم">
                                                <label for="infectious_disease_yes">نعم</label>
                                                <input type="radio" name="infectious_disease" id="infectious_disease_no" value="لا">
                                                <label for="infectious_disease_no">لا</label>
                                            </div>
                                            <input type="text" class="form-control-custom mt-2" placeholder="مشتبه ب...">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-bold">فحص الألم</label>
                                            <div class="radio-group">
                                                <input type="radio" name="pain_check" id="pain_check_no" value="لا يوجد">
                                                <label for="pain_check_no">لا يوجد</label>
                                                <input type="radio" name="pain_check" id="pain_check_yes" value="يوجد">
                                                <label for="pain_check_yes">يوجد</label>
                                            </div>
                                            <input type="text" class="form-control-custom mt-2" placeholder="درجة الألم">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-bold">الحساسية/تفاعلات دواء عكسية</label>
                                            <div class="radio-group">
                                                <input type="radio" name="allergy_check" id="allergy_check_no" value="لا يوجد">
                                                <label for="allergy_check_no">لا يوجد</label>
                                                <input type="radio" name="allergy_check" id="allergy_check_yes" value="يوجد">
                                                <label for="allergy_check_yes">يوجد</label>
                                            </div>
                                            <input type="text" class="form-control-custom mt-2" placeholder="المسبب">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">توجد نتائج حرجة</label>
                                            <div class="radio-group">
                                                <input type="radio" name="critical_results" id="critical_results_yes" value="نعم">
                                                <label for="critical_results_yes">نعم</label>
                                                <input type="radio" name="critical_results" id="critical_results_no" value="لا">
                                                <label for="critical_results_no">لا</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">الفحص التغذوي</label>
                                            <div class="radio-group">
                                                <input type="radio" name="nutrition_check" id="nutrition_check_normal" value="طبيعي">
                                                <label for="nutrition_check_normal">طبيعي</label>
                                                <input type="radio" name="nutrition_check" id="nutrition_check_abnormal" value="غير طبيعي">
                                                <label for="nutrition_check_abnormal">غير طبيعي</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- زر الحفظ -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-save">
                            <i class="fas fa-save mr-2"></i>حفظ البيانات
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>