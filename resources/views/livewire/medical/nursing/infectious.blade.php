{{-- resources/views/livewire/medical/nursing/infectious-assessment.blade.php --}}

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
                <i class="fas fa-virus mr-2"></i>
                {{ $selectedRecord ? 'تعديل فحص الأمراض المعدية' : 'فحص الأمراض المعدية' }}
            </div>

            <table class="form-table w-100">
                <tr>
                    <th>كحة</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="cough_no" class="form-check-input"> لا
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="cough_yes" class="form-check-input"> نعم
                                </label>
                                <input type="text" wire:model.defer="cough_details" class="form-control-custom" placeholder="حدد البداية" style="width:60%; display:inline-block;">
                                @error('cough_details') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>حمى</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="fever_no" class="form-check-input"> لا
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="fever_yes" class="form-check-input"> نعم
                                </label>
                                <input type="text" wire:model.defer="fever_details" class="form-control-custom" placeholder="حدد البداية" style="width:60%; display:inline-block;">
                                @error('fever_details') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>طفح جلدي</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="rash_no" class="form-check-input"> لا
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="rash_yes" class="form-check-input"> نعم
                                </label>
                                <input type="text" wire:model.defer="rash_details" class="form-control-custom" placeholder="حدد البداية" style="width:60%; display:inline-block;">
                                @error('rash_details') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>سفر للخارج في خلال الشهر الماضي</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="travel_no" class="form-check-input"> لا
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="travel_yes" class="form-check-input"> نعم
                                </label>
                                <input type="text" wire:model.defer="travel_details" class="form-control-custom" placeholder="حدد البلد" style="width:60%; display:inline-block;">
                                @error('travel_details') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>الإجراءات المتخذة مع الحالات المشتبه بها</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="wear_mask" class="form-check-input"> طلب من المريض ارتداء الكمامة
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="separate_patient" class="form-check-input"> تطبيق التباعد في الانتظار
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="notify_doctor_action" class="form-check-input"> إبلاغ الطبيب
                                </label>
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="accelerate_examination" class="form-check-input"> إدخال خلال 15 دقيقة
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
                                    <input type="checkbox" wire:model.defer="notify_doctor_infectious_yes" class="form-check-input"> نعم
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="notify_doctor_infectious_no" class="form-check-input"> لا
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
        <div class="card-header bg-secondary text-white">سجل الفحوصات السابقة</div>
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
                                @if($record->cough_yes) كحة منذ: {{ $record->cough_details }}<br>@endif
                                @if($record->fever_yes) حمى منذ: {{ $record->fever_details }}<br>@endif
                                @if($record->rash_yes) طفح جلدي منذ: {{ $record->rash_details }}<br>@endif
                                @if($record->travel_yes) سافر إلى: {{ $record->travel_details }}<br>@endif
                                الإجراءات: 
                                @if($record->wear_mask) كمامة، @endif
                                @if($record->separate_patient) تباعد، @endif
                                @if($record->notify_doctor_action) إبلاغ، @endif
                                @if($record->accelerate_examination) تسريع.@endif
                            </td>
                            <td class="text-center">
                                <button wire:click="viewRecord({{ $record->id }})" class="btn btn-sm btn-info">
                                    عرض
                                </button>
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
