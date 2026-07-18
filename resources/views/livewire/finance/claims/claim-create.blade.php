<div class="container-fluid py-4" dir="rtl">
    {{-- 🔔 التنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 📝 عنوان الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-gradient m-0">
            <i class="mdi mdi-file-plus-outline me-2"></i>
            {{ __('claims_trans.create_claim') }}
        </h3>
        <button class="btn btn-success btn-lg shadow-sm rounded-pill px-4" wire:click="saveClaim">
            <i class="mdi mdi-content-save-outline me-1"></i>
            {{ __('claims_trans.save_claim') }}
        </button>
    </div>

    {{-- 🎯 الفلاتر --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">{{ __('claims_trans.from_date') }}</label>
                    <input type="date" class="form-control shadow-sm" wire:model="from_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">{{ __('claims_trans.to_date') }}</label>
                    <input type="date" class="form-control shadow-sm" wire:model="to_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">{{ __('claims_trans.contract') }}</label>
                    <select class="form-select shadow-sm" wire:model="contract_id">
                        <option value="">{{ __('claims_trans.select_contract') }}</option>
                        @foreach ($contracts as $contract)
                            <option value="{{ $contract->id }}">{{ $contract->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100 shadow-sm" wire:click="searchInvoices">
                        <i class="mdi mdi-magnify"></i> {{ __('claims_trans.search') }}
                    </button>
                </div>
                <div class="col-md-1">
                    <select wire:model="perPage" class="form-select shadow-sm">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 جدول الفواتير --}}
    @if ($invoices->count() > 0)
        <div class="card shadow-lg border-0">
            <div class="card-header bg-light">
                <h5 class="fw-bold text-secondary mb-0">
                    <i class="mdi mdi-file-table-outline me-2"></i>
                    {{ __('claims_trans.invoices_list') }}
                </h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>{{ __('claims_trans.invoice_number') }}</th>
                            <th>{{ __('claims_trans.patient') }}</th>
                            <th>{{ __('claims_trans.contract_current') }}</th>
                            <th>{{ __('claims_trans.contract_reservation') }}</th>
                            <th>{{ __('claims_trans.reservation_type') }}</th> {{-- نوع الحجز --}}
                            <th>{{ __('claims_trans.reservation_details') }}</th> {{-- تفاصيل الحجز --}}
                            <th>{{ __('claims_trans.services') }}</th>
                            <th>{{ __('claims_trans.total') }}</th>
                            <th>{{ __('claims_trans.patient_share') }}</th>
                            <th>{{ __('claims_trans.contract_share') }}</th>
                            <th>{{ __('claims_trans.discount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $inv)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $inv->id }}</span></td>
                                <td>{{ $inv->number }}</td>
                                <td>{{ $inv->patient?->full_name }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $this->getcontractname($inv->patient?->insurance_contracting) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        {{ $this->getcontractname($inv->reservations?->contract_id) }}
                                    </span>
                                </td>
                                {{-- نوع الحجز --}}
                                {{-- نوع الحجز --}}
                                <td>
                                    @php
                                        $type =  $inv->reservation_type ?? null;
                                    @endphp

                                    @if ($type === 'out' || $type === 'clinic'  || $type === '1')
                                        <span class="badge bg-info">
                                            <i class="mdi mdi-stethoscope"></i> {{ __('claims_trans.outpatient') }}
                                        </span>
                                    @elseif($type === 'surgery' || $type === 'operation')
                                        <span class="badge bg-danger">
                                            <i class="mdi mdi-hospital-building"></i> {{ __('claims_trans.surgery') }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="mdi mdi-help-circle"></i> {{ __('claims_trans.unknown') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- تفاصيل الحجز --}}
                                <td class="text-start">
                                    @if ($inv->reservations)
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="mdi mdi-hospital-building"></i>
                                                {{ $inv->reservations->clinic?->name_ar ?? '-' }}</li>
                                            <li><i class="mdi mdi-account-heart"></i>
                                                {{ $inv->reservations->doctor?->full_name ?? '-' }}</li>
                                            <li><i class="mdi mdi-calendar-clock"></i>
                                                {{ $inv->reservations->date_reservations ?? '-' }}</li>
                                                  <li><i class="mdi mdi-dots-circle"></i>
                                                {{ $inv->reservations->service->name ?? '-' }}</li>
                                        </ul>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                {{-- الخدمات --}}
                                <td class="text-start">
                                    <ul class="list-unstyled">
                                        @foreach ($inv->services as $srv)
                                            <li>{{ $srv->service?->name }}
                                                ({{ number_format($srv->total_pricing, 2) }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ number_format($inv->total, 2) }}</td>
                                <td class="text-danger">{{ number_format($inv->patient_contribution, 2) }}</td>
                                <td class="text-success">{{ number_format($inv->contract_contribution, 2) }}</td>
                                <td class="text-warning">{{ number_format($inv->discount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                {{-- 📌 الباجينشن --}}
                <div class="mt-3">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>

        {{-- 🔹 إجمالي الفترة كلها --}}
        <div class="card mt-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">{{ __('claims_trans.period_totals') }}</h5>
                <p>{{ __('claims_trans.total') }}: {{ number_format($allTotals['total_claim'] ?? 0, 2) }}</p>
                <p>{{ __('claims_trans.patient_share') }}:
                    {{ number_format($allTotals['total_patient_share'] ?? 0, 2) }}</p>
                <p>{{ __('claims_trans.contract_share') }}:
                    {{ number_format($allTotals['total_contract_share'] ?? 0, 2) }}</p>
                <p>{{ __('claims_trans.discount') }}: {{ number_format($allTotals['total_discount'] ?? 0, 2) }}</p>
            </div>
        </div>
    @endif
</div>

<style>
    .btn-gradient-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: #fff;
        border: none;
        transition: 0.3s;
    }

    .btn-gradient-success:hover {
        background: linear-gradient(45deg, #20c997, #28a745);
        transform: translateY(-2px);
    }

    .text-gradient {
        background: linear-gradient(45deg, #007bff, #6610f2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    table thead th {
        font-weight: bold;
        font-size: 14px;
    }

    table tbody td {
        font-size: 13px;
    }
</style>
