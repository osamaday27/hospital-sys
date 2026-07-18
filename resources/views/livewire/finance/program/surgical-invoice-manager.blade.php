{{-- resources/views/livewire/finance/program/surgical-invoice-manager.blade.php --}}
<div class="container-fluid py-3" wire:ignore.self>

    {{-- تنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- رأس الصفحة المختصر (يظهر دائماً) --}}
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h4 class="mb-0 text-info">
                <i class="mdi mdi-clipboard-text-outline me-1"></i>
                {{ __('finance_trans.surgical_invoice') }}
            </h4>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-light text-dark">
                    {{ __('finance_trans.invoice_no') }}:
                    <strong>{{ $invoice?->number ?? '-' }}</strong>
                </span>
                <span class="badge bg-light text-dark">
                    {{ __('finance_trans.status') }}:
                    @php
                        $st = $invoice?->status ?? '-';
                        $map = ['paid'=>'bg-success','partial'=>'bg-warning','unpaid'=>'bg-danger'];
                        $cls = $map[$st] ?? 'bg-secondary';
                    @endphp
                    <strong><span class="badge {{ $cls }}">{{ $st }}</span></strong>
                </span>
            </div>
        </div>
    </div>

    {{-- التابات --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white border-0">
            <ul class="nav nav-pills flex-wrap gap-2" id="invoiceTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link {{ $active_tab==='overview' ? 'active' : '' }}"
                        id="tab-overview"
                        data-bs-toggle="pill"
                        data-bs-target="#pane-overview"
                        type="button" role="tab" aria-controls="pane-overview"
                        aria-selected="{{ $active_tab==='overview' ? 'true' : 'false' }}"
                        wire:click="setActiveTab('overview')">
                        <i class="mdi mdi-information-outline me-1"></i>{{ __('finance_trans.overview') ?? 'Overview' }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link {{ $active_tab==='surgery' ? 'active' : '' }}"
                        id="tab-surgery"
                        data-bs-toggle="pill"
                        data-bs-target="#pane-surgery"
                        type="button" role="tab" aria-controls="pane-surgery"
                        aria-selected="{{ $active_tab==='surgery' ? 'true' : 'false' }}"
                        wire:click="setActiveTab('surgery')">
                        <i class="mdi mdi-hospital-building me-1"></i>{{ __('finance_trans.operation_details') }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link {{ $active_tab==='services' ? 'active' : '' }}"
                        id="tab-services"
                        data-bs-toggle="pill"
                        data-bs-target="#pane-services"
                        type="button" role="tab" aria-controls="pane-services"
                        aria-selected="{{ $active_tab==='services' ? 'true' : 'false' }}"
                        wire:click="setActiveTab('services')">
                        <i class="mdi mdi-format-list-bulleted me-1"></i>{{ __('finance_trans.invoice_services') }}
                    </button>
                </li>

                {{-- التاب الجديد --}}
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link {{ $active_tab==='linked' ? 'active' : '' }}"
                        id="tab-linked"
                        data-bs-toggle="pill"
                        data-bs-target="#pane-linked"
                        type="button" role="tab" aria-controls="pane-linked"
                        aria-selected="{{ $active_tab==='linked' ? 'true' : 'false' }}"
                        wire:click="setActiveTab('linked')">
                        <i class="mdi mdi-link-variant me-1"></i>{{ __('finance_trans.surgery_linked_services') ?? 'Surgery Linked Services' }}
                    </button>
                </li>

                <li class="nav-item ms-auto" role="presentation">
                    <button
                        class="nav-link {{ $active_tab==='summary' ? 'active' : '' }}"
                        id="tab-summary"
                        data-bs-toggle="pill"
                        data-bs-target="#pane-summary"
                        type="button" role="tab" aria-controls="pane-summary"
                        aria-selected="{{ $active_tab==='summary' ? 'true' : 'false' }}"
                        wire:click="setActiveTab('summary')">
                        <i class="mdi mdi-finance me-1"></i>{{ __('finance_trans.invoice_summary') }}
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body pt-0">
            <div class="tab-content" id="invoiceTabsContent">

                {{-- ========== تبويب 1: نظرة عامة ========== --}}
                <div class="tab-pane fade {{ $active_tab==='overview' ? 'show active' : '' }}" id="pane-overview" role="tabpanel" aria-labelledby="tab-overview" tabindex="0">
                    {{-- اختيار العملية النشطة + زر تحميل من العقد/الافتراضي --}}
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <label class="form-label mb-0">{{ __('finance_trans.surgery') }}</label>
                                <select class="form-select form-select-sm" style="min-width:240px" wire:model="active_surgery_id">
                                    @forelse($booking_surgeries as $sx)
                                        <option value="{{ $sx['id'] }}">
                                            {{ $sx['name'] }} @if($sx['side']) — ({{ $sx['side'] }}) @endif
                                        </option>
                                    @empty
                                        <option value="">{{ __('finance_trans.no_surgeries') }}</option>
                                    @endforelse
                                </select>

                                <button class="btn btn-outline-primary btn-sm"
                                        wire:click="reloadDefaults"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="reloadDefaults">
                                        <i class="mdi mdi-reload me-1"></i>{{ __('finance_trans.reload_from_contract') }}
                                    </span>
                                    <span wire:loading wire:target="reloadDefaults">
                                        <span class="spinner-border spinner-border-sm me-1"></span>{{ __('finance_trans.loading') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- بيانات المريض/الحجز --}}
                    <div class="card mb-2 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('finance_trans.patient') }}</label>
                                    <input type="text" class="form-control" value="{{ $patient->full_name ?? '-' }}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('finance_trans.reservation_id') }}</label>
                                    <input type="text" class="form-control" value="{{ $reservation_id }}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('finance_trans.visit_id') }}</label>
                                    <input type="text" class="form-control" value="{{ $visit_id ?? '-' }}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('finance_trans.contract') }}</label>
                                    <input type="text" class="form-control" value="{{ $contract_id ? ('#'.$contract_id) : __('finance_trans.no_contract') }}" disabled>
                                </div>
                                <div class="col-md-12">
                                    <small class="text-muted">
                                        {{ __('finance_trans.note') }}:
                                        {{ __('finance_trans.surgeries_multi_hint') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== تبويب 2: تفاصيل العملية (كروت متعددة) ========== --}}
                <div class="tab-pane fade {{ $active_tab==='surgery' ? 'show active' : '' }}" id="pane-surgery" role="tabpanel" aria-labelledby="tab-surgery" tabindex="0">
                    @foreach ($booking_surgeries as $sx)
                        @php $sid = $sx['id']; $d = $surgery_details[$sid] ?? null; @endphp
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="mdi mdi-hospital-building me-1"></i>
                                    {{ __('finance_trans.operation_details') }} — {{ $sx['name'] }}
                                    @if($sx['side'])
                                        <span class="badge bg-light text-dark ms-2">{{ $sx['side'] }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                @if($d)
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.surgeon_fee') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.surgeon_fee">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.assistant_fee') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.assistant_fee">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.anesthesia_fee') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.anesthesia_fee">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.anesthesia_type') }}</label>
                                        <input type="text" class="form-control"
                                            wire:model.defer="surgery_details.{{ $sid }}.anesthesia_type">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.nursing_fee') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.nursing_fee">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.operation_room_fee') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.operation_room_fee">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.additional_time_fee') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.additional_time_fee">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.supplies_cost') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.supplies_cost">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.other_services') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.other_services">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.discount') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.discount">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.vat_rate') }} %</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.vat_rate">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.subtotal') }}</label>
                                        <input type="number" class="form-control" value="{{ $d['subtotal'] }}" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.vat_amount') }}</label>
                                        <input type="number" class="form-control" value="{{ $d['vat_amount'] }}" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">{{ __('finance_trans.total_price') }}</label>
                                        <input type="number" class="form-control fw-bold" value="{{ $d['total_price'] }}" disabled>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.patient_value') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.patient_value">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('finance_trans.contract_value') }}</label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model.debounce.500ms="surgery_details.{{ $sid }}.contract_value">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('finance_trans.notes') }}</label>
                                        <input type="text" class="form-control"
                                            wire:model.defer="surgery_details.{{ $sid }}.notes">
                                    </div>
                                </div>
                                @else
                                    <div class="text-muted">{{ __('finance_trans.no_surgery_detail_found') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ========== تبويب 3: الخدمات كبنود فاتورة ========== --}}
                <div class="tab-pane fade {{ $active_tab==='services' ? 'show active' : '' }}" id="pane-services" role="tabpanel" aria-labelledby="tab-services" tabindex="0">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span>
                                <i class="mdi mdi-format-list-bulleted me-1"></i>
                                {{ __('finance_trans.invoice_services') }}
                            </span>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light btn-sm" wire:click.prevent="addServiceRow">
                                    <i class="mdi mdi-plus"></i> {{ __('finance_trans.add_service') }}
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div wire:loading.flex wire:target="addServiceRow,removeServiceRow,saveAll,reloadDefaults,addLinkedItemToInvoice,addAllLinkedItemsOfSurgery" class="align-items-center mb-2">
                                <span class="spinner-border spinner-border-sm me-2"></span>{{ __('finance_trans.loading') }}
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:22%">{{ __('finance_trans.service') }}</th>
                                            <th style="width:10%">{{ __('finance_trans.quantity') }}</th>
                                            <th style="width:10%">{{ __('finance_trans.included_qty') ?? 'Included' }}</th>
                                            <th style="width:12%">{{ __('finance_trans.unit_price') }}</th>
                                            <th style="width:12%">{{ __('finance_trans.total') }}</th>
                                            <th style="width:12%">{{ __('finance_trans.contract_value') }}</th>
                                            <th style="width:12%">{{ __('finance_trans.patient_value') }}</th>
                                            <th style="width:10%">{{ __('finance_trans.discount') }}</th>
                                            <th style="width:8%">{{ __('finance_trans.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($services as $i => $row)
                                            @php
                                                $sid = (int)($row['service_id'] ?? 0);
                                                $isUnique = in_array($sid, $nonRepeatableServiceIds ?? []);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <select class="form-select" wire:model="services.{{ $i }}.service_id">
                                                            <option value="">{{ __('finance_trans.choose') }}</option>
                                                            @foreach ($allServices as $srv)
                                                                <option value="{{ $srv->id }}">
                                                                    {{ $srv->getTranslation('name', app()->getLocale()) ?? $srv->id }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if($isUnique)
                                                            <span class="badge bg-info text-dark">{{ __('finance_trans.non_repeatable') ?? 'Unique' }}</span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($row['note']))
                                                        <small class="text-muted d-block mt-1">{{ $row['note'] }}</small>
                                                    @endif
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        wire:model.debounce.500ms="services.{{ $i }}.quantity">
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        wire:model.debounce.500ms="services.{{ $i }}.included_qty">
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        wire:model.debounce.500ms="services.{{ $i }}.unit_price">
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        wire:model="services.{{ $i }}.total_price" disabled>
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        wire:model.debounce.500ms="services.{{ $i }}.contract_value">
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        wire:model.debounce.500ms="services.{{ $i }}.patient_value">
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        wire:model.debounce.500ms="services.{{ $i }}.discount">
                                                </td>

                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-danger"
                                                            wire:click.prevent="removeServiceRow({{ $i }})">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">
                                                    {{ __('finance_trans.no_services') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== التاب الجديد: الخدمات المرتبطة بالعمليات للحجز ========== --}}
                <div class="tab-pane fade {{ $active_tab==='linked' ? 'show active' : '' }}" id="pane-linked" role="tabpanel" aria-labelledby="tab-linked" tabindex="0">
                    @forelse($booking_surgeries as $sx)
                        @php $sid = $sx['id']; $list = $surgery_linked_services[$sid] ?? []; @endphp
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div class="fw-semibold">
                                    <i class="mdi mdi-link-variant me-1"></i>
                                    {{ $sx['name'] }} @if($sx['side']) — <span class="text-muted">({{ $sx['side'] }})</span>@endif
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary"
                                            wire:click="addAllLinkedItemsOfSurgery({{ $sid }})"
                                            @if(empty($list)) disabled @endif>
                                        <i class="mdi mdi-plus-circle-outline me-1"></i>{{ __('finance_trans.add_all_to_invoice') ?? 'Add all to invoice' }}
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(!empty($list))
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:45%">{{ __('finance_trans.service') }}</th>
                                                    <th style="width:10%">{{ __('finance_trans.quantity') }}</th>
                                                    <th style="width:15%">{{ __('finance_trans.unit_price') }}</th>
                                                    <th style="width:15%">{{ __('finance_trans.note') ?? 'Note' }}</th>
                                                    <th style="width:15%">{{ __('finance_trans.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($list as $row)
                                                    <tr>
                                                        <td>
                                                            <span class="fw-semibold">
                                                                {{ $row['service_name'] ?? ('#'.$row['service_id']) }}
                                                            </span>
                                                            <div class="small text-muted">ID: {{ $row['service_id'] }}</div>
                                                        </td>
                                                        <td>{{ number_format($row['quantity'] ?? 0, 2) }}</td>
                                                        <td>{{ number_format($row['unit_price'] ?? 0, 2) }}</td>
                                                        <td>{{ $row['notes'] ?? '' }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary"
                                                                    wire:click="addLinkedItemToInvoice({{ $sid }}, {{ $row['service_id'] }})">
                                                                <i class="mdi mdi-plus"></i> {{ __('finance_trans.add_to_invoice') ?? 'Add to invoice' }}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-muted">{{ __('finance_trans.no_linked_services') ?? 'No linked services for this surgery.' }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">{{ __('finance_trans.no_surgeries') }}</div>
                    @endforelse
                </div>

                {{-- ========== تبويب 4: الملخص والدفعات + زر الحفظ ========== --}}
                @php
                    $details_total = 0; $details_patient = 0;
                    foreach($booking_surgeries as $sx){
                        $sid = $sx['id'];
                        if(isset($surgery_details[$sid])){
                            $details_total  += (float)$surgery_details[$sid]['total_price'];
                            $details_patient+= (float)$surgery_details[$sid]['patient_value'];
                        }
                    }
                @endphp

                <div class="tab-pane fade {{ $active_tab==='summary' ? 'show active' : '' }}" id="pane-summary" role="tabpanel" aria-labelledby="tab-summary" tabindex="0">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="mdi mdi-finance me-1"></i>
                                        {{ __('finance_trans.invoice_summary') }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12 d-flex justify-content-between">
                                            <span>{{ __('finance_trans.details_total_without_services') }}</span>
                                            <strong>{{ number_format($details_total,2) }}</strong>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between">
                                            <span>{{ __('finance_trans.services_total') }}</span>
                                            <strong>{{ number_format($services_total,2) }}</strong>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between">
                                            <span>{{ __('finance_trans.invoice_total') }}</span>
                                            <strong>{{ number_format($invoice_total_preview,2) }}</strong>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between">
                                            <span>{{ __('finance_trans.patient_contribution') }}</span>
                                            <strong>{{ number_format($invoice_patient_preview,2) }}</strong>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between">
                                            <span>{{ __('finance_trans.advance_paid') }}</span>
                                            <strong>{{ number_format($advance_paid,2) }}</strong>
                                        </div>
                                        @php
                                            $residual_preview_view = max(0, $invoice_patient_preview - $advance_paid);
                                        @endphp
                                        <div class="col-12 d-flex justify-content-between">
                                            <span>{{ __('finance_trans.residual') }}</span>
                                            <strong class="{{ $residual_preview_view>0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($residual_preview_view,2) }}
                                            </strong>
                                        </div>

                                        <div class="col-12">
                                            <small class="text-muted">
                                                {{ __('finance_trans.status') }}:
                                                <strong>{{ $invoice?->status ?? '-' }}</strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- الدفعات المقدّمة --}}
                        <div class="col-lg-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-cash-check me-1"></i> {{ __('finance_trans.advance_payments') ?? 'Advance Payments' }}</span>
                                    <span class="badge bg-primary">{{ __('finance_trans.count') ?? 'Count' }}: {{ count($advance_payments ?? []) }}</span>
                                </div>
                                <div class="card-body">
                                    @if(!empty($advance_payments))
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('finance_trans.date') ?? 'Date' }}</th>
                                                        <th>{{ __('finance_trans.method') ?? 'Method' }}</th>
                                                        <th>{{ __('finance_trans.amount') ?? 'Amount' }}</th>
                                                        <th>{{ __('finance_trans.notes') ?? 'Notes' }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($advance_payments as $ap)
                                                        <tr>
                                                            <td>{{ $ap['payment_date'] ?? '-' }}</td>
                                                            <td>{{ $ap['payment_method'] ?? '-' }}</td>
                                                            <td>{{ number_format($ap['amount'] ?? 0,2) }}</td>
                                                            <td>{{ $ap['notes'] ?? '' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-muted">{{ __('finance_trans.no_advance_payments') ?? 'No advance payments' }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- أزرار الحفظ --}}
                        <div class="col-12 d-flex align-items-end justify-content-end">
                            <div class="d-flex gap-2">
                                <button class="btn btn-success" wire:click="saveAll" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="saveAll">
                                        <i class="mdi mdi-content-save me-1"></i>
                                        {{ __('finance_trans.save_update_invoice') }}
                                    </span>
                                    <span wire:loading wire:target="saveAll">
                                        <span class="spinner-border spinner-border-sm me-1"></span>{{ __('finance_trans.saving') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- /tab-pane summary --}}
            </div>
        </div>
    </div>

    {{-- ملاحظة: لا يوجد JS مخصص مطلوب لأن Bootstrap 5 يتعامل مع التابات تلقائياً عبر data-bs-* --}}
</div>
