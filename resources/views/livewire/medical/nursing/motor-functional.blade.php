{{-- resources/views/livewire/medical/nursing/functional-assessment.blade.php --}}

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
                <i class="fas fa-walking mr-2"></i>
                {{ $selectedRecord ? 'تعديل الفحص الحركي/الوظيفي' : 'الفحص الحركي/الوظيفي' }}
            </div>

            <table class="form-table w-100">
                <tr>
                    <th>الاعتماد على الغير</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="live_alone" class="form-check-input"> يعيش بمفرده
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="no_problem" class="form-check-input"> لا يوجد مشكلة
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="total_dependence" class="form-check-input"> اعتماد كلي
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="movement_problems" class="form-check-input"> مشاكل في الحركة
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>الأجهزة المساعدة</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="no_assistance" class="form-check-input"> لا يوجد
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="walker" class="form-check-input"> مشاية
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="wheelchair" class="form-check-input"> كرسي متحرك
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="crutches" class="form-check-input"> عكاز
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="other_assistance" class="form-check-input"> أخرى
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>يجب تنبيه الطبيب عند اختيار علامة *</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="notify_doctor_functional_yes" class="form-check-input"> تم تنبيه الطبيب (نعم)
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="notify_doctor_functional_no" class="form-check-input"> لا
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
        <div class="card-header bg-secondary text-white">سجل الفحص الحركي/الوظيفي</div>
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
                                @if($record->live_alone) يعيش بمفرده<br>@endif
                                @if($record->no_problem) لا يوجد مشكلة<br>@endif
                                @if($record->total_dependence) اعتماد كلي<br>@endif
                                @if($record->movement_problems) مشاكل في الحركة<br>@endif
                                الأجهزة المساعدة:
                                @if($record->no_assistance) لا يوجد، @endif
                                @if($record->walker) مشاية، @endif
                                @if($record->wheelchair) كرسي متحرك، @endif
                                @if($record->crutches) عكاز، @endif
                                @if($record->other_assistance) أخرى، @endif
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
