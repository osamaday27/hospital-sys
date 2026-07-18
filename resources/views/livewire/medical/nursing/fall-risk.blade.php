{{-- resources/views/livewire/medical/nursing/fall‐risk‐screening.blade.php --}}

<div>
    {{-- رسالة النجاح --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form @if($selectedRecord) wire:submit.prevent="update" @else wire:submit.prevent="save" @endif>
        <div class="card card-section">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-procedures mr-2"></i>فحص مخاطر السقوط/الفحص الوظيفي</h5>
            </div>
            <div class="card-body">
                <p class="font-weight-bold">تطبق إجراءات السلامة الآتية لأن جميع مرضى العيون من الفئات الأكثر عرضة للسقوط:</p>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="assist_patient" class="form-check-input"> قدم المساعدة للمريض عند الحاجة (الذهاب للحمام، مساعدة على الحركة، …)
                        </label>
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="ensure_aids" class="form-check-input"> تأكد من استخدام المريض وسائل مناسبة للمساعدة على الحركة
                        </label>
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="review_medications" class="form-check-input"> يقوم الطبيب بمراجعة الأدوية الحالية للمريض وتغييرها أو تحويله إذا كانت سبباً في خطر السقوط
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="refer_conditions" class="form-check-input"> إذا كانت الحالة المرضية الثانوية سبباً في خطر السقوط يحوّل الطبيب المريض للتخصص المناسب
                        </label>
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="avoid_crowding" class="form-check-input"> تجنب زحام الحجرة، ترك الباب مفتوح، توفير إضاءة، رفع جوانب السرير، وضعية منخفضة
                        </label>
                    </div>
                </div>

                <p class="font-weight-bold">توعية المريض/الأهل بمخاطر السقوط تشمل:</p>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="advise_no_self_move" class="form-check-input"> التنبيه على المريض بعدم التحرك بمفرده وطلب المساعدة عند الحاجة
                        </label>
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="advise_no_sudden_stand" class="form-check-input"> التنبيه على المريض بعدم الوقوف فجأة أو الانحناء لالتقاط الأشياء
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="advise_no_left_alone_child" class="form-check-input"> التنبيه على الأهل بعدم ترك الطفل بمفرده
                        </label>
                        <label class="form-check-label d-block">
                            <input type="checkbox" wire:model.defer="advise_report_dizziness" class="form-check-input"> التنبيه على المريض بإخبار الفريق الطبي عند الشعور بالدوخة أو فقدان التوازن
                        </label>
                    </div>
                </div>

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

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        {{ $selectedRecord ? 'تحديث' : 'حفظ' }}
                    </button>
                    @if($selectedRecord)
                        <button type="button" wire:click.prevent="cancelEdit" class="btn btn-secondary px-4">إلغاء</button>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- سجل الفحص السابق --}}
    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">سجل مخاطر السقوط</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th>إجراءات السلامة</th>
                        <th>التوعية</th>
                        <th>تنبيه الطبيب</th>
                        <th class="text-center">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $r)
                        <tr>
                            <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($r->user)->name }}</td>
                            <td>
                                @if($r->assist_patient) مساعده، @endif
                                @if($r->ensure_aids) وسائل مساعدة، @endif
                                @if($r->review_medications) مراجعة أدوية، @endif
                                @if($r->refer_conditions) إحالة، @endif
                                @if($r->avoid_crowding) تجنب زحام @endif
                            </td>
                            <td>
                                @if($r->advise_no_self_move) عدم التحرك، @endif
                                @if($r->advise_no_sudden_stand) عدم الوقوف فجأة، @endif
                                @if($r->advise_no_left_alone_child) عدم ترك الطفل، @endif
                                @if($r->advise_report_dizziness) إبلاغ دوخة @endif
                            </td>
                            <td>{{ $r->notify_doctor_yes ? 'نعم' : 'لا' }}</td>
                            <td class="text-center">
                                <button wire:click="viewRecord({{ $r->id }})" class="btn btn-sm btn-info">عرض</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">لا توجد سجلات سابقة</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 pe-3">
                {{ $records->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>
</div>
