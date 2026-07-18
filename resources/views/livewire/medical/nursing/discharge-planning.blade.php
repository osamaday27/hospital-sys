{{-- resources/views/livewire/medical/nursing/discharge-planning.blade.php --}}
<div>
    {{-- رسالة النجاح --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form @if($selectedRecord) wire:submit.prevent="update" @else wire:submit.prevent="save" @endif>
        <div class="card card-section mb-4">
            <div class="card-header bg-light-primary">
                <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>التخطيط للخروج</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">مكان العناية بعد الخروج</label>
                        <input type="text" wire:model="care_place" class="form-control" placeholder="أدخل المكان">
                        @error('care_place') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">هل لديك مساعدة في البيت؟</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="help_at_home" id="help_at_home_yes" value="1">
                            <label class="form-check-label" for="help_at_home_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="help_at_home" id="help_at_home_no" value="0">
                            <label class="form-check-label" for="help_at_home_no">لا</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">تستطيع العناية بنفسك دون مساعدة؟</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="self_care" id="self_care_yes" value="1">
                            <label class="form-check-label" for="self_care_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="self_care" id="self_care_no" value="0">
                            <label class="form-check-label" for="self_care_no">لا</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">كل التعليمات تم شرحها بوضوح؟</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="instructions_clear" id="instructions_clear_yes" value="1">
                            <label class="form-check-label" for="instructions_clear_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="instructions_clear" id="instructions_clear_no" value="0">
                            <label class="form-check-label" for="instructions_clear_no">لا</label>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mt-4">قد يحتاج المريض مساعدة للقيام بالمهام التالية:</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="need_assist_reminder" id="need_assist_reminder">
                            <label class="form-check-label" for="need_assist_reminder">تذكره بالمواعيد</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="need_assist_medication" id="need_assist_medication">
                            <label class="form-check-label" for="need_assist_medication">تناول الأدوية</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="need_assist_stairs" id="need_assist_stairs">
                            <label class="form-check-label" for="need_assist_stairs">صعود السلالم</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="need_assist_toilet" id="need_assist_toilet">
                            <label class="form-check-label" for="need_assist_toilet">استخدام الحمام</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="need_assist_bathing" id="need_assist_bathing">
                            <label class="form-check-label" for="need_assist_bathing">الاستحمام</label>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mt-4">ملخص الفحص والتقييم التمريضي</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">تم تنبيه الطبيب</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="doctor_alert" id="doctor_alert_yes" value="1">
                            <label class="form-check-label" for="doctor_alert_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="doctor_alert" id="doctor_alert_no" value="0">
                            <label class="form-check-label" for="doctor_alert_no">لا</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">الفحص الاجتماعي، الاقتصادي والثقافي والنفسي</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="social_exam" id="social_exam_normal" value="1">
                            <label class="form-check-label" for="social_exam_normal">طبيعي</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="social_exam" id="social_exam_abnormal" value="0">
                            <label class="form-check-label" for="social_exam_abnormal">غير طبيعي</label>
                        </div>
                        <input type="text" wire:model="social_exam_details" class="form-control mt-2" placeholder="اذكر التفاصيل">
                        @error('social_exam_details') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">المرضي ممن لهم احتياجات خاصة</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="special_needs" id="special_needs_yes" value="1">
                            <label class="form-check-label" for="special_needs_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="special_needs" id="special_needs_no" value="0">
                            <label class="form-check-label" for="special_needs_no">لا</label>
                        </div>
                        <input type="text" wire:model="special_needs_details" class="form-control mt-2" placeholder="اذكر التفاصيل">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">فحص الأمراض المعدية</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="infectious" id="infectious_yes" value="1">
                            <label class="form-check-label" for="infectious_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="infectious" id="infectious_no" value="0">
                            <label class="form-check-label" for="infectious_no">لا</label>
                        </div>
                        <input type="text" wire:model="infectious_details" class="form-control mt-2" placeholder="مشتبه ب:">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">الفحص الحركي/الوظيفي</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="functional_exam" id="functional_normal" value="1">
                            <label class="form-check-label" for="functional_normal">طبيعي</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="functional_exam" id="functional_abnormal" value="0">
                            <label class="form-check-label" for="functional_abnormal">غير طبيعي</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">فحص الألم</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="pain" id="pain_no" value="0">
                            <label class="form-check-label" for="pain_no">لا يوجد</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="pain" id="pain_yes" value="1">
                            <label class="form-check-label" for="pain_yes">يوجد</label>
                        </div>
                        @if($pain == 1)
                            <input type="text" wire:model="pain_degree" class="form-control mt-2" placeholder="درجة الألم">
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">الحساسية/تفاعلات دواء عكسية</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="allergy" id="allergy_no" value="0">
                            <label class="form-check-label" for="allergy_no">لا يوجد</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="allergy" id="allergy_yes" value="1">
                            <label class="form-check-label" for="allergy_yes">يوجد</label>
                        </div>
                        @if($allergy == 1)
                            <input type="text" wire:model="allergy_details" class="form-control mt-2" placeholder="المسبب">
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">توجد نتائج حرجة</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="critical_results" id="critical_results_yes" value="1">
                            <label class="form-check-label" for="critical_results_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="critical_results" id="critical_results_no" value="0">
                            <label class="form-check-label" for="critical_results_no">لا</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block">الفحص التغذوي</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="nutrition_exam" id="nutrition_normal" value="1">
                            <label class="form-check-label" for="nutrition_normal">طبيعي</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model="nutrition_exam" id="nutrition_abnormal" value="0">
                            <label class="form-check-label" for="nutrition_abnormal">غير طبيعي</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الممرضة المسجلة</label>
                        <input type="text" class="form-control" value="{{ $selectedRecord ? $selectedRecord->nurse_signature : Auth::user()->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تاريخ/وقت التسجيل</label>
                        <input type="text" class="form-control" value="{{ $selectedRecord ? \Carbon\Carbon::parse($selectedRecord->nurse_date)->format('d/m/Y') . ' ' . $selectedRecord->nurse_time : now()->format('d/m/Y H:i') }}" readonly>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-4">{{ $selectedRecord ? 'تحديث' : 'حفظ' }}</button>
                    @if($selectedRecord)
                        <button type="button" wire:click.prevent="cancelEdit" class="btn btn-secondary px-4">إلغاء</button>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">سجل التخطيط للخروج</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>المستخدم</th>
                            <th>مكان العناية</th>
                            <th>مساعدة بالبيت</th>
                            <th>العناية الذاتية</th>
                            <th>تعليمات واضحة</th>
                            <th>مهام مساعدة</th>
                            <th>الملخص</th>
                            <th>الممرضة</th>
                            <th>تاريخ/وقت التسجيل</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $r)
                            <tr>
                                <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ optional($r->user)->name }}</td>
                                <td>{{ $r->care_place }}</td>
                                <td>{{ $r->help_at_home ? 'نعم' : 'لا' }}</td>
                                <td>{{ $r->self_care ? 'نعم' : 'لا' }}</td>
                                <td>{{ $r->instructions_clear ? 'نعم' : 'لا' }}</td>
                                <td>
                                    {{ $r->need_assist_reminder ? 'تذكره،' : '' }}
                                    {{ $r->need_assist_medication ? 'أدوية،' : '' }}
                                    {{ $r->need_assist_stairs ? 'سلالم،' : '' }}
                                    {{ $r->need_assist_toilet ? 'حمام،' : '' }}
                                    {{ $r->need_assist_bathing ? 'استحمام' : '' }}
                                </td>
                                <td>
                                    {{ $r->doctor_alert ? 'تم تنبيه،' : '' }}
                                    {{ $r->social_exam ? 'اجتماعي طبيعي،' : 'غير طبيعي' }}
                                </td>
                                <td>{{ $r->nurse_signature }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->nurse_date)->format('d/m/Y') }} {{ $r->nurse_time }}</td>
                                <td>
                                    <button wire:click="viewRecord({{ $r->id }})" class="btn btn-sm btn-info" title="عرض">
                                        عرض
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center py-4">لا توجد سجلات سابقة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3 pe-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>