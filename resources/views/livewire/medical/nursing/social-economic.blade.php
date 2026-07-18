{{-- resources/views/livewire/medical/nursing/social-economic.blade.php --}}

<div>
    {{-- رسالة النجاح --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- النموذج: حفظ أو تعديل بناءً على حالة التحرير --}}
    <form @if ($selectedRecord) wire:submit.prevent="update" @else wire:submit.prevent="save" @endif>
        <!-- الفحص الاجتماعي الاقتصادي والثقافي والنفسي -->
        <div class="form-container">
            <div class="section-title">
                <i class="fas fa-home mr-2"></i>
                {{ $selectedRecord ? 'تعديل الفحص الاجتماعي والاقتصادي' : 'الفحص الاجتماعي الاقتصادي والثقافي والنفسي' }}
            </div>

            <table class="form-table w-100">
                <tr>
                    <th>الحالة المعيشية</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="living_alone" class="form-check-input">
                                    يعيش بمفرده
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="living_with_family"
                                        class="form-check-input"> يعيش مع الأسرة
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="living_with_others"
                                        class="form-check-input"> يعيش مع آخرين
                                </label>
                                <div class="d-flex align-items-center mb-2">
                                    <label class="form-check-label me-2">
                                        <input type="checkbox" wire:model.defer="living_other_details"
                                            class="form-check-input"> أخرى
                                    </label>
                                    <input type="text" wire:model.defer="living_other_details"
                                        class="form-control-custom" placeholder="اذكر التفاصيل" style="width:60%;">
                                </div>
                            </div>
                        </div>
                        @error('living_other_details')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>

                <tr>
                    <th>احتياجات خاصة</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="special_needs_none"
                                        class="form-check-input"> لا يوجد
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="hearing" class="form-check-input"> سمع
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="speech" class="form-check-input"> تحدث
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="language_barrier" class="form-check-input">
                                    عائق لغة
                                </label>
                                <div class="d-flex align-items-center mb-2">
                                    <label class="form-check-label me-2">
                                        <input type="checkbox" wire:model.defer="other_needs_details"
                                            class="form-check-input"> أخرى
                                    </label>
                                    <input type="text" wire:model.defer="other_needs_details"
                                        class="form-control-custom" placeholder="اذكر التفاصيل" style="width:60%;">
                                </div>
                            </div>
                        </div>
                        @error('other_needs_details')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>

                <tr>
                    <th>الحالة الاجتماعية</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="single" class="form-check-input"> أعزب
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="married" class="form-check-input"> متزوج
                                </label>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="form-check-label me-2">
                                        <input type="checkbox" wire:model.defer="has_children" class="form-check-input">
                                        يوجد أطفال
                                    </label>
                                    <input type="text" wire:model.defer="number_of_children"
                                        class="form-control-custom" placeholder="عدد الأطفال" style="width:50%;">
                                </div>
                                @error('number_of_children')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>هل تدخن؟</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="smoking_no" class="form-check-input"> لا
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="smoking_yes" class="form-check-input"> نعم
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="smoking_previous"
                                        class="form-check-input"> سابقاً
                                </label>
                                <input type="text" wire:model.defer="cigarettes_per_day"
                                    class="form-control-custom" placeholder="عدد السجائر يومياً" style="width:60%;">
                                @error('cigarettes_per_day')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>الفحص النفسي</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="no_psychological_issues"
                                        class="form-check-input"> لا يوجد
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="anxiety" class="form-check-input"> قلق
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="anger" class="form-check-input"> غضبان
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="depression" class="form-check-input">
                                    مكتئب
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>التغطية الطبية</th>
                    <td>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="can_afford" class="form-check-input">
                                    قادر على التحمل
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="insured" class="form-check-input"> مؤمن
                                    عليه
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="cannot_afford" class="form-check-input">
                                    غير قادر على تحمل التكلفة
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>آثار للعنف أو سوء المعاملة</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="no_violence" class="form-check-input">
                                    لا يوجد
                                </label>
                            </div>
                            <div class="col-md-6 d-flex align-items-center mb-2">
                                <label class="form-check-label me-2">
                                    <input type="checkbox" wire:model.defer="violence_present"
                                        class="form-check-input"> يوجد
                                </label>
                                <input type="text" wire:model.defer="violence_details" class="form-control-custom"
                                    placeholder="اذكر التفاصيل" style="width:60%;">
                            </div>
                        </div>
                        @error('violence_details')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>

                <tr>
                    <th>الحالة الوظيفية</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="not_working" class="form-check-input">
                                    لا يعمل
                                </label>
                            </div>
                            <div class="col-md-6 d-flex align-items-center mb-2">
                                <label class="form-check-label me-2">
                                    <input type="checkbox" wire:model.defer="working" class="form-check-input"> يعمل
                                </label>
                                <input type="text" wire:model.defer="occupation" class="form-control-custom"
                                    placeholder="المهنة" style="width:60%;">
                            </div>
                        </div>
                        @error('occupation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>

                <tr>
                    <th>تنبيه الطبيب عند اختيار علامة *</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label">
                                    <input type="checkbox" wire:model.defer="notify_doctor_yes"
                                        class="form-check-input"> تم تنبيه الطبيب (نعم)
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label">
                                    <input type="checkbox" wire:model.defer="notify_doctor_no"
                                        class="form-check-input"> لا
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-4">
                {{ $selectedRecord ? 'تعديل البيانات' : 'حفظ البيانات' }}
            </button>
            @if ($selectedRecord)
                <button type="button" wire:click.prevent="cancelEdit" class="btn btn-secondary px-4">إلغاء</button>
            @endif
        </div>
    </form>

    {{-- سجل الفحوصات السابقة --}}
    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">
            سجل الفحوصات السابقة
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th class="text-center">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($record->user)->name }}</td>

                            <td class="text-center">
                                <button wire:click="viewRecord({{ $record->id }})" class="btn btn-sm btn-info">
                                    عرض
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">لا توجد سجلات سابقة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 pe-3">
                {{ $records->links('livewire::bootstrap') }}
            </div>

        </div>
    </div>
</div>
