{{-- resources/views/livewire/medical/nursing/special-needs.blade.php --}}

<div>
    {{-- رسالة النجاح --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form @if($selectedRecord) wire:submit.prevent="update" @else wire:submit.prevent="save" @endif>
        <div class="form-container">
            <div class="section-title">
                <i class="fas fa-wheelchair mr-2"></i>
                {{ $selectedRecord ? 'تعديل تقييم الاحتياجات الخاصة' : 'تقييم المرضى ممن لهم احتياجات خاصة' }}
            </div>

            <table class="form-table w-100">
                <tr>
                    <th>في مرحلة المراهقة</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="no_exercise" class="form-check-input"> لا يمارس الرياضة
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="overweight" class="form-check-input"> زيادة مفرطة في الوزن
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="hates_school" class="form-check-input"> يكره المدرسة
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="family_issues" class="form-check-input"> مشكلة في التعامل مع الأهل
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>مسن (أكبر من 70 سنة)</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="normal_elderly" class="form-check-input"> طبيعي
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="cannot_count" class="form-check-input"> غير قادر على العد
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="doesnt_know_facility" class="form-check-input"> لا يعرف اسم المرافق
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>إعاقة جسدية</th>
                    <td>
                        <div class="d-flex align-items-center mb-2">
                            <label class="form-check-label me-2">
                                <input type="checkbox" wire:model.defer="physical_disability_yes" class="form-check-input"> نعم
                            </label>
                            <input type="text" wire:model.defer="physical_disability_details" class="form-control-custom" placeholder="اذكر التفاصيل" style="width:60%;">
                        </div>
                        @error('physical_disability_details') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>

                <tr>
                    <th>ضعف المناعة</th>
                    <td>
                        <div class="d-flex align-items-center mb-2">
                            <label class="form-check-label me-2">
                                <input type="checkbox" wire:model.defer="weak_immunity_yes" class="form-check-input"> نعم
                            </label>
                            <input type="text" wire:model.defer="weak_immunity_reason" class="form-control-custom" placeholder="السبب" style="width:60%;">
                        </div>
                        @error('weak_immunity_reason') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>

                <tr>
                    <th>آلام مزمنة</th>
                    <td>
                        <div class="form-check mb-2">
                            <input type="checkbox" wire:model.defer="chronic_pain_yes" class="form-check-input" id="chronicPain">
                            <label class="form-check-label" for="chronicPain">نعم <span class="note">(استخدم نموذج تقييم وعلاج الألم)</span></label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>يجب تنبيه الطبيب عند اختيار علامة *</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="notify_doctor_special_yes" class="form-check-input"> تم تنبيه الطبيب (نعم)
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="notify_doctor_special_no" class="form-check-input"> لا
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    {{ $selectedRecord ? 'تحديث' : 'حفظ' }}
                </button>
                @if($selectedRecord)
                    <button type="button" wire:click.prevent="cancelEdit" class="btn btn-secondary px-4">إلغاء</button>
                @endif
            </div>
        </div>
    </form>

    {{-- سجل التقييمات السابقة --}}
    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">سجل التقييمات السابقة</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th>تفاصيل</th>
                        <th class="text-center">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($record->user)->name }}</td>
                            <td>
                                @if($record->physical_disability_yes) إعاقة جسدية: {{ $record->physical_disability_details }}<br>@endif
                                @if($record->weak_immunity_yes) ضعف مناعة: {{ $record->weak_immunity_reason }}<br>@endif
                                @if($record->chronic_pain_yes) آلام مزمنة @endif
                            </td>
                            <td class="text-center">
                                <button wire:click="viewRecord({{ $record->id }})" class="btn btn-sm btn-info">عرض</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">لا توجد تقييمات سابقة</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 pe-3">
                {{ $records->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>
</div>
