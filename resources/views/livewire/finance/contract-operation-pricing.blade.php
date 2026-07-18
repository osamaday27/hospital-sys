<div class="card shadow-sm border-0" dir="rtl">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">
            {{ __('contract_trans.surgery_pricing') }} - {{ __('contract_trans.contract_number') }}: {{ $contract->id }}
        </h5>
        <div>
            <span class="badge bg-white text-dark">
                {{ $contract->getTranslation('name', app()->getLocale()) }}
            </span>
        </div>
    </div>

    <div class="card-body">
        {{-- رسائل التنبيهات --}}
        @if (session()->has('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="mdi mdi-check-circle-outline me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="mdi mdi-alert-circle-outline me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <form wire:submit.prevent="save">
            <div class="row g-3 mb-3">
                {{-- اسم العملية (يظهر تلقائي من الخارج) --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('contract_trans.surgery_service') }}</label>
                    <input type="text" 
                           class="form-control bg-light" 
                           value="{{ optional($surgeries->firstWhere('id', $surgery_id))->getTranslation('name', app()->getLocale()) }}" 
                           readonly>
                </div>

                {{-- نوع التخدير --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('contract_trans.anesthesia_type') }}</label>
                    <select class="form-select" wire:model.defer="anesthesia_type">
                        <option value="">{{ __('surgical_trans.choose') }}</option>
                        <option value="عام">{{ __('surgical_trans.general') }}</option>
                        <option value="نصفي">{{ __('surgical_trans.partial') }}</option>
                        <option value="موضعي">{{ __('surgical_trans.local') }}</option>
                    </select>
                </div>

                {{-- الصف الأول --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.surgeon_fee') }}</label>
                    <input type="number" wire:model.defer="surgeon_fee" class="form-control" step="0.01" inputmode="decimal" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.assistant_fee') }}</label>
                    <input type="number" wire:model.defer="assistant_fee" class="form-control" step="0.01" inputmode="decimal" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.anesthesia_fee') }}</label>
                    <input type="number" wire:model.defer="anesthesia_fee" class="form-control" step="0.01" inputmode="decimal" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.nursing_fee') }}</label>
                    <input type="number" wire:model.defer="nursing_fee" class="form-control" step="0.01" inputmode="decimal" />
                </div>

                {{-- الصف الثاني --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.operation_room_fee') }}</label>
                    <input type="number" wire:model.defer="operation_room_fee" class="form-control" step="0.01" inputmode="decimal" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.additional_time_fee') }}</label>
                    <input type="number" wire:model.defer="additional_time_fee" class="form-control" step="0.01" inputmode="decimal" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.supplies_cost') }}</label>
                    <input type="number" wire:model.defer="supplies_cost" class="form-control" step="0.01" inputmode="decimal" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.other_services') }}</label>
                    <input type="number" wire:model.defer="other_services" class="form-control" step="0.01" inputmode="decimal" />
                </div>

                {{-- الصف الثالث --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.discount') }}</label>
                    <input type="number" wire:model.defer="discount" class="form-control" step="0.01" inputmode="decimal" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('surgical_trans.vat') }}</label>
                    <input type="number" wire:model.defer="vat" class="form-control" step="0.01" inputmode="decimal" />
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-success">{{ __('surgical_trans.total_price') }}</label>
                    <input type="number" wire:model.defer="total_price" class="form-control bg-light text-success fw-bold" readonly />
                </div>
            </div>

            {{-- زر الحفظ --}}
            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-save me-1"></i> {{ __('surgical_trans.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
