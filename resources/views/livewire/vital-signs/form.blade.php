<div class="tab-pane show active" id="vital_signs" role="tabpanel" aria-labelledby="vital-signs-tab">
    <div class="row g-3">
        <!-- فورم الإضافة -->
        <div class="col-lg-3">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="mdi mdi-heart-pulse me-2"></i>
                    <h5 class="mb-0" style="color: #fff;">{{ __('doctor_trans.add_vital_sign') }}</h5>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="save" class="needs-validation" novalidate>
                        <input type="hidden" value="{{ $patient_id }}">
                        <input type="hidden" value="{{ $patient_visits_id }}">
                        <input type="hidden" value="{{ $age }}">

                        <!-- الوزن والطول مع مؤشر كتلة الجسم -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.weight_height') }}</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" wire:model.lazy="weight" class="form-control"
                                               placeholder="{{ __('doctor_trans.weight') }}" min="0" max="300" step="0.1">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    @error('weight') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" wire:model.lazy="height" class="form-control"
                                               placeholder="{{ __('doctor_trans.height') }}" min="0" max="250" step="0.1">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                    @error('height') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            @php
                                $bmiVal = $bmi;
                                $bmiCat = '';
                                $bmiClass = '';
                                if($bmiVal){
                                    if($bmiVal < 18.5){ $bmiCat='نقص وزن'; $bmiClass='bg-info'; }
                                    elseif($bmiVal < 25){ $bmiCat='طبيعي'; $bmiClass='bg-success'; }
                                    elseif($bmiVal < 30){ $bmiCat='زيادة وزن'; $bmiClass='bg-warning'; }
                                    else { $bmiCat='سمنة'; $bmiClass='bg-danger'; }
                                }
                                $bmiPercent = $bmiVal ? min(max((($bmiVal-15)/20)*100,0),100) : 0;
                            @endphp
                            <div class="mt-2">
                                <label class="form-label fw-bold">{{ __('doctor_trans.bmi') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" readonly value="{{ $bmi }}">
                                    <span class="input-group-text {{ $bmiClass }}">{{ $bmiCat }}</span>
                                </div>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div class="progress-bar {{ $bmiClass }}" role="progressbar" style="width: {{ $bmiPercent }}%"></div>
                                </div>
                                @error('bmi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- درجة الحرارة -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.temperature') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.1" wire:model.lazy="temperature" class="form-control" min="25" max="45">
                                <span class="input-group-text">°C</span>
                            </div>
                            @error('temperature') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @php $r = $ranges['temp']; @endphp
                            @if(!is_null($temperature) && ($temperature < $r[0] || $temperature > $r[1]))
                                <div class="form-text text-danger">⚠️ درجة الحرارة غير طبيعية</div>
                            @endif
                        </div>

                        <!-- معدل ضربات القلب -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.heart_rate') }}</label>
                            <div class="input-group">
                                <input type="number" wire:model.lazy="heart_rate" class="form-control" min="30" max="250">
                                <span class="input-group-text">bpm</span>
                            </div>
                            @error('heart_rate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @php $r = $ranges['hr']; @endphp
                            @if(!is_null($heart_rate) && ($heart_rate < $r[0] || $heart_rate > $r[1]))
                                <div class="form-text text-danger">⚠️ معدل النبض غير طبيعي</div>
                            @endif
                        </div>

                        <!-- معدل التنفس -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.respiratory_rate') }}</label>
                            <div class="input-group">
                                <input type="number" wire:model.lazy="respiratory_rate" class="form-control" min="5" max="60">
                                <span class="input-group-text">bpm</span>
                            </div>
                            @error('respiratory_rate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @php $r = $ranges['rr']; @endphp
                            @if(!is_null($respiratory_rate) && ($respiratory_rate < $r[0] || $respiratory_rate > $r[1]))
                                <div class="form-text text-danger">⚠️ معدل التنفس غير طبيعي</div>
                            @endif
                        </div>

                        <!-- ضغط الدم -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.blood_pressure') }}</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" wire:model.lazy="systolic_bp" class="form-control" placeholder="{{ __('doctor_trans.systolic') }}" min="50" max="250">
                                        <span class="input-group-text">SYS</span>
                                    </div>
                                    @error('systolic_bp') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" wire:model.lazy="diastolic_bp" class="form-control" placeholder="{{ __('doctor_trans.diastolic') }}" min="30" max="150">
                                        <span class="input-group-text">DIA</span>
                                    </div>
                                    @error('diastolic_bp') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            @php
                                $sysRange = $ranges['bp_sys'];
                                $diaRange = $ranges['bp_dia'];
                                $warn = '';
                                if(!is_null($systolic_bp) && !is_null($diastolic_bp)){
                                    if($systolic_bp < $sysRange[0] || $diastolic_bp < $diaRange[0]) $warn = '⚠️ ضغط الدم منخفض';
                                    elseif($systolic_bp > $sysRange[1] || $diastolic_bp > $diaRange[1]) $warn = '⚠️ ضغط الدم مرتفع';
                                }
                            @endphp
                            @if($warn) <div id="bpWarning" class="text-danger small mt-1">{{ $warn }}</div> @endif
                        </div>

                        <!-- تشبع الأكسجين -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.oxygen_saturation') }}</label>
                            <div class="input-group">
                                <input type="number" wire:model.lazy="oxygen_saturation" class="form-control" min="50" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                            @error('oxygen_saturation') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @php $r = $ranges['oxy']; @endphp
                            @if(!is_null($oxygen_saturation) && $oxygen_saturation < $r[0])
                                <div class="form-text text-danger">⚠️ تشبع الأكسجين منخفض</div>
                            @endif
                        </div>

                        <!-- سكر الدم -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.blood_sugar') }}</label>
                            <div class="input-group">
                                <input type="number" wire:model.lazy="blood_sugar" class="form-control" min="0" max="500">
                                <span class="input-group-text">mg/dL</span>
                            </div>
                            @error('blood_sugar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @php $r = $ranges['sugar']; @endphp
                            @if(!is_null($blood_sugar) && ($blood_sugar < $r[0] || $blood_sugar > $r[1]))
                                <div class="form-text text-danger">⚠️ سكر الدم غير طبيعي</div>
                            @endif
                        </div>

                        <!-- مقياس الألم -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.pain_scale') }}</label>
                            <div class="d-flex align-items-center">
                                <input type="range" wire:model="pain_scale" class="form-range" min="0" max="10" step="0.5" id="painScale">
                                <span class="ms-3 fw-bold" id="painScaleValue">{{ $pain_scale }}</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>0</span>
                                <span>10</span>
                            </div>
                            @error('pain_scale') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('doctor_trans.notes') }}</label>
                            <textarea wire:model.lazy="notes" class="form-control" rows="2"></textarea>
                            @error('notes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- زر الإضافة -->
                        <button type="submit" class="btn btn-primary w-100 py-2" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="mdi mdi-plus-circle-outline me-1"></i> {{ __('doctor_trans.add') }}
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- جدول العرض -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <span>{{ __('doctor_trans.previous_vitals') }}</span>
                    <span class="badge bg-light text-dark">{{ $vitals->total() }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap examination-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{ __('doctor_trans.date') }}</th>
                                    <th>{{ __('doctor_trans.temperature') }} (°C)</th>
                                    <th>{{ __('doctor_trans.heart_rate') }} (bpm)</th>
                                    <th>{{ __('doctor_trans.respiratory_rate') }} (bpm)</th>
                                    <th>{{ __('doctor_trans.blood_pressure') }} (mmHg)</th>
                                    <th>{{ __('doctor_trans.oxygen_saturation') }} (%)</th>
                                    <th>{{ __('doctor_trans.blood_sugar') }} (mg/dL)</th>
                                    <th>{{ __('doctor_trans.pain_scale') }}</th>
                                    <th>{{ __('doctor_trans.notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vitals as $v)
                                    @php
                                        $clsTemp = (!is_null($v->temperature)       && ($v->temperature       < $ranges['temp'][0]   || $v->temperature       > $ranges['temp'][1]))   ? 'background-color:#f8d7da' : '';
                                        $clsHr   = (!is_null($v->heart_rate)        && ($v->heart_rate        < $ranges['hr'][0]     || $v->heart_rate        > $ranges['hr'][1]))     ? 'background-color:#f8d7da' : '';
                                        $clsRr   = (!is_null($v->respiratory_rate)  && ($v->respiratory_rate  < $ranges['rr'][0]     || $v->respiratory_rate  > $ranges['rr'][1]))     ? 'background-color:#f8d7da' : '';
                                        $clsOxy  = (!is_null($v->oxygen_saturation) &&  $v->oxygen_saturation < $ranges['oxy'][0])                                         ? 'background-color:#f8d7da' : '';
                                        $clsSug  = (!is_null($v->blood_sugar)       && ($v->blood_sugar       < $ranges['sugar'][0]   || $v->blood_sugar       > $ranges['sugar'][1])) ? 'background-color:#f8d7da' : '';
                                        $isBpAbn = (!is_null($v->systolic_bp) && ($v->systolic_bp < $ranges['bp_sys'][0] || $v->systolic_bp > $ranges['bp_sys'][1]))
                                                || (!is_null($v->diastolic_bp) && ($v->diastolic_bp < $ranges['bp_dia'][0] || $v->diastolic_bp > $ranges['bp_dia'][1]));
                                        $clsBp   = $isBpAbn ? 'background-color:#f8d7da' : '';
                                    @endphp
                                    <tr>
                                        <td>{{ $v->created_at?->format('Y-m-d H:i') }}</td>
                                        <td style="{{ $clsTemp }}">{{ $v->temperature ?? '-' }}</td>
                                        <td style="{{ $clsHr }}">{{ $v->heart_rate ?? '-' }}</td>
                                        <td style="{{ $clsRr }}">{{ $v->respiratory_rate ?? '-' }}</td>
                                        <td style="{{ $clsBp }}">
                                            @if($v->systolic_bp && $v->diastolic_bp)
                                                {{ $v->systolic_bp }}/{{ $v->diastolic_bp }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="{{ $clsOxy }}">{{ $v->oxygen_saturation ?? '-' }}</td>
                                        <td style="{{ $clsSug }}">{{ $v->blood_sugar ?? '-' }}</td>
                                        <td>{{ $v->pain_scale ?? '-' }}</td>
                                        <td>{{ $v->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">لا توجد قراءات سابقة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($vitals,'hasPages') && $vitals->hasPages())
                        <div class="mt-3">
                            {{ $vitals->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
