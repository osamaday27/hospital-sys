<div class="container-fluid py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="mdi mdi-calendar-check me-2"></i> متابعة المريض
            </h6>
            <div class="text-muted small">
                Patient #{{ $patient_id }} | Visit #{{ $visit_id }}
            </div>
        </div>

        <div class="card-body">
            @if ($message)
                <div class="alert alert-success alert-dismissible fade show">
                    {!! $message !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($error)
                <div class="alert alert-danger alert-dismissible fade show">
                    {!! $error !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card border-0 h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="mdi mdi-tune-variant me-1"></i> إعداد المتابعة</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">العيادة</label>
                                <select class="form-select" wire:model="clinic_id">
                                    <option value="">— اختر العيادة —</option>
                                    @foreach ($clinics as $c)
                                        <option value="{{ $c['id'] }}">{{ $c['name_ar'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">الطبيب</label>
                                <select class="form-select" wire:model="doctor_id">
                                    <option value="">— اختر الطبيب —</option>
                                    @foreach ($doctors as $d)
                                        <option value="{{ $d['id'] }}">{{ $d['full_name'] }}</option>
                                    @endforeach
                                </select>
                                @if (!$doctors)
                                    <div class="text-muted small mt-1">لا يوجد جدول للطبيب في هذه العيادة.</div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تاريخ الكشف</label>
                                    <input type="date" class="form-control" wire:model="base_date">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">عدد الأيام بعد الكشف</label>
                                    <input type="number" min="0" max="365" class="form-control"
                                        wire:model.lazy="days_after" list="common-days">
                                    <datalist id="common-days">
                                        <option value="7"></option>
                                        <option value="10"></option>
                                        <option value="14"></option>
                                        <option value="21"></option>
                                        <option value="28"></option>
                                        <option value="30"></option>
                                        <option value="42"></option>
                                    </datalist>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">نوع المتابعة</label>
                                <div class="btn-group w-100">
                                    <button type="button"
                                        class="btn btn-outline-primary @if ($followup_type === 'follow_up') active @endif"
                                        wire:click="$set('followup_type','follow_up')">
                                        Follow Up
                                    </button>
                                    <button type="button"
                                        class="btn btn-outline-primary @if ($followup_type === 'follow_up_opr') active @endif"
                                        wire:click="$set('followup_type','follow_up_opr')">
                                        Follow Up (Operation)
                                    </button>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">تفضيل الفترة</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <label
                                        class="btn btn-sm btn-outline-dark @if ($period === 'any') active @endif"
                                        wire:click="$set('period','any')">أي</label>
                                    <label
                                        class="btn btn-sm btn-outline-dark @if ($period === 'morning') active @endif"
                                        wire:click="$set('period','morning')">صباحًا</label>
                                    <label
                                        class="btn btn-sm btn-outline-dark @if ($period === 'afternoon') active @endif"
                                        wire:click="$set('period','afternoon')">بعد الظهر</label>
                                    <label
                                        class="btn btn-sm btn-outline-dark @if ($period === 'evening') active @endif"
                                        wire:click="$set('period','evening')">مساءً</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ملاحظات المتابعة</label>
                                <textarea class="form-control" wire:model.defer="notes" rows="3" placeholder="أدخل ملاحظات للطبيب أو للرسيبشن..."></textarea>
                            </div>

                            <div class="mt-3">
                                <div class="small text-muted">أقرب تاريخ مستهدف:
                                    <strong>{{ \Carbon\Carbon::parse($target_date)->format('d/m/Y') }}</strong></div>
                                <div wire:loading class="small text-info mt-1"><i class="mdi mdi-loading mdi-spin"></i>
                                    جاري تحديث الاقتراحات...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="mdi mdi-calendar-clock me-1"></i> أقرب المواعيد المتاحة</h6>
                            <span class="badge bg-light text-dark">{{ $clinicName ?? '-' }} /
                                {{ $doctorName ?? '-' }}</span>
                        </div>
                        <div class="card-body">
                            @if (empty($proposals))
                                <div class="alert alert-warning mb-0">لا توجد مواعيد متاحة خلال الفترة القادمة. جرّب
                                    تغيير الطبيب أو الفترة أو زيادة الأيام.</div>
                            @else
                                <div class="list-group mb-3">
                                    @foreach ($proposals as $p)
                                        <label class="list-group-item list-group-item-action d-flex align-items-center">
                                            <input class="form-check-input me-2" type="radio" name="proposal"
                                                wire:model="selected_proposal" value="{{ $p['key'] }}">
                                            <div>
                                                <div class="fw-bold">{{ $p['label'] }}</div>
                                                <div class="text-muted small">Key: {{ $p['key'] }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-primary" wire:click="bookSelected"
                                        @disabled(!$selected_proposal)>
                                        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> حجز تلقائي الآن
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
