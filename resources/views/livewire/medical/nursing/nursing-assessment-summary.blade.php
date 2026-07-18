{{-- resources/views/livewire/medical/nursing/infectious-summary.blade.php --}}

<div>
    {{-- رسالة نجاح الحفظ --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- سجل ملخص فحص الأمراض المعدية --}}
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            سجل ملخص فحص الأمراض المعدية
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0 text-right">
                <thead>
                    <tr>
                        <th>التاريخ والوقت</th>
                        <th>الكحة</th>
                        <th>الحمى</th>
                        <th>الطفح</th>
                        <th>السفر</th>
                        <th>الإجراءات</th>
                        <th>تنبيه الطبيب</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summaries as $s)
                        <tr>
                            <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                {{ $s->cough }} 
                                @if($s->cough === 'نعم') (بداية: {{ $s->cough_onset }}) @endif
                            </td>
                            <td>
                                {{ $s->fever }} 
                                @if($s->fever === 'نعم') (بداية: {{ $s->fever_onset }}) @endif
                            </td>
                            <td>
                                {{ $s->rash }} 
                                @if($s->rash === 'نعم') (بداية: {{ $s->rash_onset }}) @endif
                            </td>
                            <td>
                                {{ $s->travel }} 
                                @if($s->travel === 'نعم') (بلد: {{ $s->travel_destination }}) @endif
                            </td>
                            <td>
                                @if($s->action_mask) الكمامة، @endif
                                @if($s->action_distance) تباعد، @endif
                                @if($s->action_notify) إبلاغ، @endif
                                @if($s->action_accelerate) تسريع @endif
                            </td>
                            <td>{{ $s->notify_doctor }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد سجلات سابقة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- نموذج الإدخال الجديد --}}
    <form wire:submit.prevent="saveSummary">
        <div class="card">
            <div class="card-header bg-primary text-white">
                ملخص فحص الأمراض المعدية
            </div>
            <div class="card-body">
                {{-- الكحة --}}
                <div class="mb-3 row align-items-center">
                    <label class="col-sm-3 col-form-label">كحة</label>
                    <div class="col-sm-9">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="cough" id="cough_no" value="لا">
                            <label class="form-check-label" for="cough_no">لا</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="cough" id="cough_yes" value="نعم">
                            <label class="form-check-label" for="cough_yes">نعم</label>
                        </div>
                        @if($cough === 'نعم')
                            <input type="text"
                                   class="form-control mt-2"
                                   wire:model.defer="cough_onset"
                                   placeholder="حدد بداية الكحة">
                        @endif
                        @error('cough') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- الحمى --}}
                <div class="mb-3 row align-items-center">
                    <label class="col-sm-3 col-form-label">حمى</label>
                    <div class="col-sm-9">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="fever" id="fever_no" value="لا">
                            <label class="form-check-label" for="fever_no">لا</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="fever" id="fever_yes" value="نعم">
                            <label class="form-check-label" for="fever_yes">نعم</label>
                        </div>
                        @if($fever === 'نعم')
                            <input type="text"
                                   class="form-control mt-2"
                                   wire:model.defer="fever_onset"
                                   placeholder="حدد بداية الحمى">
                        @endif
                        @error('fever') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- الطفح الجلدي --}}
                <div class="mb-3 row align-items-center">
                    <label class="col-sm-3 col-form-label">طفح جلدي</label>
                    <div class="col-sm-9">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="rash" id="rash_no" value="لا">
                            <label class="form-check-label" for="rash_no">لا</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="rash" id="rash_yes" value="نعم">
                            <label class="form-check-label" for="rash_yes">نعم</label>
                        </div>
                        @if($rash === 'نعم')
                            <input type="text"
                                   class="form-control mt-2"
                                   wire:model.defer="rash_onset"
                                   placeholder="حدد بداية الطفح">
                        @endif
                        @error('rash') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- السفر --}}
                <div class="mb-3 row align-items-center">
                    <label class="col-sm-3 col-form-label">سفر للخارج في الشهر الماضي</label>
                    <div class="col-sm-9">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="travel" id="travel_no" value="لا">
                            <label class="form-check-label" for="travel_no">لا</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="travel" id="travel_yes" value="نعم">
                            <label class="form-check-label" for="travel_yes">نعم</label>
                        </div>
                        @if($travel === 'نعم')
                            <input type="text"
                                   class="form-control mt-2"
                                   wire:model.defer="travel_destination"
                                   placeholder="حدد البلد">
                        @endif
                        @error('travel') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- إجراءات الحالات المشتبه بها --}}
                <div class="mb-3">
                    <label class="form-label">الإجراءات المتخذة مع الحالات المشتبه بها:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model.defer="action_mask" id="action_mask">
                        <label class="form-check-label" for="action_mask">طلب من المريض ارتداء الكمامة</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model.defer="action_distance" id="action_distance">
                        <label class="form-check-label" for="action_distance">تطبيق التباعد بين المريض والآخرين في الانتظار</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model.defer="action_notify" id="action_notify">
                        <label class="form-check-label" for="action_notify">إبلاغ الطبيب</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model.defer="action_accelerate" id="action_accelerate">
                        <label class="form-check-label" for="action_accelerate">إدخال المريض للطبيب خلال 15 دقيقة</label>
                    </div>
                </div>

                {{-- تنبيه الطبيب --}}
                <div class="mb-3 row align-items-center">
                    <label class="col-sm-3 col-form-label">يجب تنبيه الطبيب عند اختيار علامة*</label>
                    <div class="col-sm-9">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="notify_doctor" id="notify_doctor_yes" value="نعم">
                            <label class="form-check-label" for="notify_doctor_yes">نعم</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" wire:model.defer="notify_doctor" id="notify_doctor_no" value="لا">
                            <label class="form-check-label" for="notify_doctor_no">لا</label>
                        </div>
                        @error('notify_doctor') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ الملخص</button>
            </div>
        </div>
    </form>
</div>
