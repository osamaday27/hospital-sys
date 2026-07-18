{{-- resources/views/livewire/medical/nursing/allergy-assessment.blade.php --}}

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
                <i class="fas fa-allergies mr-2"></i>
                {{ $selectedRecord ? 'تعديل الحساسية/تفاعلات دواء عكسية' : 'الحساسية/تفاعلات دواء عكسية' }}
            </div>

            <table class="form-table w-100">
                <tr>
                    <th>هل يوجد مسببات للحساسية؟ <span class="text-danger">*</span></th>
                    <td>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-check-label me-3">
                                    <input type="checkbox" wire:model.defer="allergies_no" class="form-check-input"> لا
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label me-3">
                                    <input type="checkbox" wire:model.defer="allergies_yes" class="form-check-input"> نعم
                                </label>
                            </div>
                        </div>

                        @if($allergies_yes)
                            <div class="mb-2"><strong>إذا كانت الإجابة نعم حدد مسببات الحساسية:</strong></div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-check-label">
                                        <input type="checkbox" wire:model.defer="allergy_food" class="form-check-input"> طعام (Food)
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-check-label">
                                        <input type="checkbox" wire:model.defer="allergy_medication" class="form-check-input"> دواء (Medication)
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-check-label me-2">
                                        <input type="checkbox" wire:model.defer="allergy_others" class="form-check-input"> أخرى (Others)
                                    </label>
                                    <input type="text" wire:model.defer="allergy_others_details" class="form-control-custom" placeholder="أخرى، اذكر التفاصيل" style="width:60%; display:inline-block;">
                                </div>
                            </div>
                            @error('allergy_others_details') <span class="text-danger">{{ $message }}</span> @enderror
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>يجب تنبيه الطبيب عند اختيار علامة <span class="text-danger">*</span></th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label">
                                    <input type="checkbox" wire:model.defer="notify_doctor_yes" class="form-check-input"> تم تنبيه الطبيب (نعم)
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label">
                                    <input type="checkbox" wire:model.defer="notify_doctor_no" class="form-check-input"> لا
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

    {{-- سجل الفحوصات السابقة --}}
    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">سجل الحساسية/تفاعلات دواء عكسية</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th>المسببات</th>
                        <th class="text-center">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($record->user)->name }}</td>
                            <td>
                                @if(!$record->allergies_yes) لا @else نعم:<br>
                                    @if($record->allergy_food) طعام، @endif
                                    @if($record->allergy_medication) دواء، @endif
                                    @if($record->allergy_others) أخرى({{ $record->allergy_others_details }}) @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <button wire:click="viewRecord({{ $record->id }})" class="btn btn-sm btn-info">عرض</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">لا توجد سجلات سابقة</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 pe-3">
                {{ $records->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>
</div>
