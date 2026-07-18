<div class="tab-pane fade" id="reassessmentform" role="tabpanel">
    <div class="row">
        <!-- فورم الإضافة -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">{{ __('doctor_trans.reassessmentform') }}</div>
                <div class="card-body">
                    <style>
                        h1 {
                            text-align: center;
                            color: #333;
                            margin-bottom: 30px;
                        }

                        .form-container {
                            background-color: #fff;
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        }

                        .section-title {
                            font-size: 18px;
                            font-weight: bold;
                            color: #555;
                            margin-top: 20px;
                            margin-bottom: 10px;
                            border-bottom: 2px solid #ddd;
                            padding-bottom: 5px;
                        }

                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                        }

                        th,
                        td {
                            border: 1px solid #ddd;
                            padding: 10px;
                            text-align: right;
                        }

                        th {
                            background-color: #f2f2f2;
                            font-weight: bold;
                        }

                        input[type="text"],
                        input[type="date"],
                        input[type="time"],
                        select {
                            width: 100%;
                            padding: 8px;
                            border: 1px solid #ccc;
                            border-radius: 4px;
                            box-sizing: border-box;
                        }

                        input[type="checkbox"] {
                            margin-left: 10px;
                        }

                        .checkbox-group {
                            display: flex;
                            align-items: center;
                            margin-bottom: 10px;
                        }

                        .checkbox-group label {
                            margin-left: 5px;
                        }

                        .signature-section {
                            margin-top: 30px;
                        }

                        .signature-section h2 {
                            font-size: 16px;
                            color: #444;
                            margin-bottom: 10px;
                        }

                        .signature-section input {
                            width: 200px;
                        }

                        .note {
                            font-size: 14px;
                            color: #777;
                            margin-top: 10px;
                        }
                    </style>
                    <h1>نموذج الفحص الاجتماعي الاقتصادي والثقافي والنفسي</h1>
                  
                    <div class="form-container">
                        <!-- فحص الأمراض المعدية -->
                        <div class="section-title">فحص الأمراض المعدية</div>
                        <table>
                            <tr>
                                <th>كحة</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="cough-no" name="cough-no">
                                        <label for="cough-no">لا</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="cough-yes" name="cough-yes">
                                        <label for="cough-yes">نعم</label>
                                        <input type="text" id="cough-details" name="cough-details"
                                            placeholder="حدد البداية">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>حمى</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="fever-no" name="fever-no">
                                        <label for="fever-no">لا</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="fever-yes" name="fever-yes">
                                        <label for="fever-yes">نعم</label>
                                        <input type="text" id="fever-details" name="fever-details"
                                            placeholder="حدد البداية">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>طفح جلدي</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="rash-no" name="rash-no">
                                        <label for="rash-no">لا</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="rash-yes" name="rash-yes">
                                        <label for="rash-yes">نعم</label>
                                        <input type="text" id="rash-details" name="rash-details"
                                            placeholder="حدد البداية">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>سفر للخارج في خلال الشهر الماضي</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="travel-no" name="travel-no">
                                        <label for="travel-no">لا</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="travel-yes" name="travel-yes">
                                        <label for="travel-yes">نعم</label>
                                        <input type="text" id="travel-details" name="travel-details"
                                            placeholder="حدد البلد">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>الإجراءات المتخذة مع الحالات المشتبه بها</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="wear-mask" name="wear-mask">
                                        <label for="wear-mask">طلب من المريض ارتداء الكمامة</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="separate-patient" name="separate-patient">
                                        <label for="separate-patient">تطبيق التباعد بين المريض والآخرين في
                                            الانتظار</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="notify-doctor" name="notify-doctor">
                                        <label for="notify-doctor">إبلاغ الطبيب</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="accelerate-examination"
                                            name="accelerate-examination">
                                        <label for="accelerate-examination">إدخال المريض للطبيب خلال 15 دقيقة</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>يجب تنبيه الطبيب عند اختيار علامة*</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="notify-doctor-yes" name="notify-doctor-yes">
                                        <label for="notify-doctor-yes">تم تنبيه الطبيب (نعم)</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="notify-doctor-no" name="notify-doctor-no">
                                        <label for="notify-doctor-no">لا</label>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!-- الفحص الحركي/الوظيفي -->
                        <div class="section-title">الفحص الحركي/الوظيفي</div>
                        <table>
                            <tr>
                                <th>الاعتماد على الغير</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="live-alone" name="live-alone">
                                        <label for="live-alone">يعيش بمفرده</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="no-problem" name="no-problem">
                                        <label for="no-problem">لا يوجد مشكلة</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="total-dependence" name="total-dependence">
                                        <label for="total-dependence">اعتماد كلي</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="movement-problems" name="movement-problems">
                                        <label for="movement-problems">مشاكل في الحركة</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>الأجهزة المساعدة</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="no-assistance" name="no-assistance">
                                        <label for="no-assistance">لا يوجد</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="walker" name="walker">
                                        <label for="walker">مشاية</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="wheelchair" name="wheelchair">
                                        <label for="wheelchair">كرسي متحرك</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="crutches" name="crutches">
                                        <label for="crutches">عكاز</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="other-assistance" name="other-assistance">
                                        <label for="other-assistance">أخرى</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>يجب تنبيه الطبيب عند اختيار علامة*</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="notify-doctor-functional-yes"
                                            name="notify-doctor-functional-yes">
                                        <label for="notify-doctor-functional-yes">تم تنبيه الطبيب (نعم)</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="notify-doctor-functional-no"
                                            name="notify-doctor-functional-no">
                                        <label for="notify-doctor-functional-no">لا</label>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!-- فحص الألم -->
                        <div class="section-title">فحص الألم</div>
                        <table>
                            <tr>
                                <th>هل يوجد ألم؟</th>
                                <td>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="pain-no" name="pain-no">
                                        <label for="pain-no">لا</label>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="pain-yes" name="pain-yes">
                                        <label for="pain-yes">نعم</label>
                                        <span class="note">(استخدم نموذج تقييم وعلاج الألم)</span>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!-- العلامات الحيوية -->
                        <div class="section-title">العلامات الحيوية</div>
                        <table>
                            <tr>
                                <th>درجة الحرارة</th>
                                <td><input type="text" id="temperature" name="temperature" placeholder="سيلزية">
                                </td>
                            </tr>
                            <tr>
                                <th>معدل التنفس</th>
                                <td><input type="text" id="respiratory-rate" name="respiratory-rate"
                                        placeholder="/دقيقة"></td>
                            </tr>
                            <tr>
                                <th>ضغط الدم</th>
                                <td><input type="text" id="blood-pressure" name="blood-pressure"
                                        placeholder="مم زئبق"></td>
                            </tr>
                            <tr>
                                <th>النبض</th>
                                <td><input type="text" id="pulse" name="pulse" placeholder="/دقيقة"></td>
                            </tr>
                            <tr>
                                <th>السكر العشوائي</th>
                                <td><input type="text" id="random-sugar" name="random-sugar"
                                        placeholder="ملجم/دل"></td>
                            </tr>
                            <tr>
                                <th>الوزن</th>
                                <td><input type="text" id="weight" name="weight" placeholder="كجم"></td>
                            </tr>
                            <tr>
                                <th>الطول</th>
                                <td><input type="text" id="height" name="height" placeholder="سم"></td>
                            </tr>
                        </table>

                    
                        <div class="card mb-3">
                            <div class="card-header">فحص مخاطر السقوط/الفحص الوظيفي</div>
                            <div class="card-body">
                                <p>تطبق اجراءات السلامة الآتية لان جميع مرضي العيون من الفئات الأكثر عرضه للسقوط:</p>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> قدم المساعدة
                                    للمريض عند الحاجة</div>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> تأكد من
                                    استخدام المريض وسائل مناسبة للمساعدة على الحركة</div>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> مراجعة
                                    الطبيب للأدوية الحالية للمريض وتغييرها إن لزم الأمر</div>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> تحويل المريض
                                    إلى التخصص المناسب إذا كانت الحالة المرضية تسبب خطر السقوط</div>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> تجنب زحام
                                    الحجرة، ترك الباب مفتوح، توفير إضاءة كافية، رفع جوانب السرير، ضبط ارتفاع السرير
                                </div>
                                <p>توعية المريض/الأهل بمخاطر السقوط تشمل:</p>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> عدم التحرك
                                    بمفرده وطلب المساعدة عند الحاجة</div>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> عدم الوقوف
                                    فجأة أو الانحناء لالتقاط الأشياء</div>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> عدم ترك
                                    الطفل بمفرده</div>
                                <div class="form-check"><input class="form-check-input" type="checkbox"> إبلاغ الفريق
                                    الطبي عند الشعور بالدوخة أو فقدان التوازن</div>
                            </div>
                        </div>
                        <div class="card p-4 shadow">
                            <h2 class="text-center mb-4">ملخص التقييم التمريضي</h2>

                               

                                <div class="mb-3">
                                    <label class="form-label">تستطيع العناية بنفسك دون مساعدة؟</label>
                                    <div>
                                        <input type="radio" name="self_care" value="نعم"> نعم
                                        <input type="radio" name="self_care" value="لا"> لا
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">كل التعليمات تم شرحها بوضوح؟</label>
                                    <div>
                                        <input type="radio" name="instructions_clear" value="نعم"> نعم
                                        <input type="radio" name="instructions_clear" value="لا"> لا
                                    </div>
                                </div>

                              
                                <h4 class="mt-4">ملخص الفحص والتقييم التمريضي</h4>
                                <div class="mb-3">
                                    <label>تم تنبيه الطبيب</label>
                                    <input type="radio" name="doctor_alert" value="نعم"> نعم
                                    <input type="radio" name="doctor_alert" value="لا"> لا
                                </div>

                                <div class="mb-3">
                                    <label>الفحص الاجتماعي، الاقتصادي والثقافي والنفسي</label>
                                    <input type="radio" name="social_exam" value="طبيعي"> طبيعي
                                    <input type="radio" name="social_exam" value="غير طبيعي"> غير طبيعي
                                    <input type="text" class="form-control mt-2"
                                        placeholder="اذكر التفاصيل إذا كانت غير طبيعية">
                                </div>

                                <div class="mb-3">
                                    <label>المرضي ممن لهم احتياجات خاصة</label>
                                    <input type="radio" name="special_needs" value="نعم"> نعم
                                    <input type="radio" name="special_needs" value="لا"> لا
                                    <input type="text" class="form-control mt-2"
                                        placeholder="اذكر التفاصيل إذا كانت نعم">
                                </div>

                                <div class="mb-3">
                                    <label>فحص الأمراض المعدية</label>
                                    <input type="radio" name="infectious_disease" value="نعم"> نعم
                                    <input type="radio" name="infectious_disease" value="لا"> لا
                                    <input type="text" class="form-control mt-2" placeholder="مشتبه ب...">
                                </div>

                                <div class="mb-3">
                                    <label>فحص الألم</label>
                                    <input type="radio" name="pain_check" value="لا يوجد"> لا يوجد
                                    <input type="radio" name="pain_check" value="يوجد"> يوجد
                                    <input type="text" class="form-control mt-2" placeholder="درجة الألم">
                                </div>

                                <div class="mb-3">
                                    <label>الحساسية/تفاعلات دواء عكسية</label>
                                    <input type="radio" name="allergy_check" value="لا يوجد"> لا يوجد
                                    <input type="radio" name="allergy_check" value="يوجد"> يوجد
                                    <input type="text" class="form-control mt-2" placeholder="المسبب">
                                </div>

                                <div class="mb-3">
                                    <label>توجد نتائج حرجة</label>
                                    <input type="radio" name="critical_results" value="نعم"> نعم
                                    <input type="radio" name="critical_results" value="لا"> لا
                                </div>
                                <div class="mb-3">
                                    <label>الفحص التغذوي</label>
                                    <input type="radio" name="nutrition_check" value="طبيعي"> طبيعي
                                    <input type="radio" name="nutrition_check" value="غير طبيعي"> غير طبيعي
                                </div>

                               
                        </div>
                            <button type="submit" class="btn btn-primary">حفظ</button>

                    </div>

                </div>
            </div>
        </div>


    </div>
</div>
