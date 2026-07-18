{{-- resources/views/livewire/finance/program/booking-details.blade.php --}}
<div class="container-fluid surgical-booking-details">
    <!-- Patient Header Card -->
    <!-- Patient Header Card -->
    <div class="card patient-header-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center"
                            style="width:64px;height:64px;">
                            <i class="mdi mdi-account-circle mdi-48px text-dark"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="mb-1 text-truncate">{{ $booking->patient->full_name ?? '-' }}</h2>
                            <div class="d-flex flex-wrap gap-3 patient-meta">
                                <div class="meta-chip">
                                    <span class="text-muted">{{ __('surgical_trans.booking_date') }}:</span>
                                    <strong>{{ $booking->booking_date }}</strong>
                                </div>
                                <div class="meta-chip">
                                    <span class="text-muted">{{ __('surgical_trans.room') }}:</span>
                                    <strong>{{ $booking->room->name_ar ?? '-' }}</strong>
                                </div>
                                <div class="meta-chip">
                                    <span class="text-muted">{{ __('surgical_trans.status') }}:</span>
                                    <span
                                        class="badge status-badge 
                                    {{ $booking->status == 'completed' ? 'bg-success' : ($booking->status == 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ __('surgical_trans.status_' . $booking->status) }}
                                    </span>
                                </div>

                                <div class="meta-chip">
                                    <span class="text-muted">{{ __('surgical_trans.title') }}:</span>
                                    <strong>
                                        {{ $booking->services->name ?? '-' }}
                                        -
                                        {{ __('surgical_trans.procedure_side_' . ($booking->procedure_side ?? '')) }}
                                    </strong>
                                </div>

                                <!-- ✅ اسم العقد -->
                                <div class="meta-chip">
                                    <span class="text-muted">{{ __('surgical_trans.contract_name') }}:</span>
                                    <strong>{{ $contract_name ?? '-' }}</strong>
                                </div>
                                <!-- /اسم العقد -->

                            </div>
                        </div>
                    </div>
                </div><!--/col-->

                <div class="col-md-4">
                    <div class="quick-stats">
                        <div class="stat-item">
                            <div class="stat-value">{{ number_format($totalServices, 2) }}</div>
                            <div class="stat-label">{{ __('surgical_trans.services_total') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ number_format($totalPayments, 2) }}</div>
                            <div class="stat-label">{{ __('surgical_trans.payments_total') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value {{ $balance < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(abs($balance), 2) }}
                            </div>
                            <div class="stat-label">
                                {{ $balance < 0 ? __('surgical_trans.remaining') : __('surgical_trans.credit') }}
                            </div>
                        </div>
                    </div>
                </div><!--/col-->
            </div>
        </div>
    </div>

    <!-- Main Tabs Navigation -->
    <ul class="nav nav-tabs surgical-tabs mb-4" id="surgicalBookingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services"
                type="button" role="tab">
                <i class="mdi mdi-medical-bag me-2"></i> {{ __('surgical_trans.services') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="supplies-tab" data-bs-toggle="tab" data-bs-target="#supplies" type="button"
                role="tab">
                <i class="mdi mdi-pill me-2"></i> {{ __('surgical_trans.supplies') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button"
                role="tab">
                <i class="mdi mdi-cash-multiple me-2"></i> {{ __('surgical_trans.financial') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button"
                role="tab">
                <i class="mdi mdi-account-group me-2"></i> {{ __('surgical_trans.medical_team') }}
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="surgicalBookingTabsContent">
        <!-- Services Tab -->
        <div class="tab-pane fade show active" id="services" role="tabpanel" aria-labelledby="services-tab">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="mdi mdi-information-outline me-1"></i>
                        {{ __('surgical_trans.note_package_includes') }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover surgical-services-table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:260px;">{{ __('surgical_trans.service') }}</th>
                                    <th class="text-end">{{ __('surgical_trans.quantity') }}</th>
                                    <th class="text-end">{{ __('surgical_trans.price') }}</th>
                                    <th class="text-end">{{ __('surgical_trans.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $includedIds = collect($included_services ?? [])
                                        ->pluck('service_id')
                                        ->all();
                                @endphp
                                @forelse($services as $service)
                                    @php
                                        $isIncluded = in_array((int) $service->service_id, $includedIds, true);
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong class="d-block text-truncate">
                                                {{ $service->service->name ?? '-' }}
                                            </strong>
                                            <div class="mt-1">
                                                @if ($isIncluded)
                                                    <span
                                                        class="badge bg-success">{{ __('surgical_trans.included') }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger">{{ __('surgical_trans.extra') }}</span>
                                                @endif
                                            </div>
                                            @if ($service->notes)
                                                <div class="text-muted small mt-1">{{ $service->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $service->quantity }}</td>
                                        <td class="text-end">
                                            {{ number_format($service->computed_unit_price ?? ($service->price ?? 0), 2) }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($service->computed_total ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-clipboard-remove-outline mdi-48px mb-2 d-block"></i>
                                            <p class="mb-0">{{ __('surgical_trans.no_services') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($services->count() > 0)
                                <tfoot>
                                    <tr class="table-light">
                                        <th colspan="3">{{ __('surgical_trans.total') }}</th>
                                        <th class="text-end">{{ number_format($totalServices, 2) }}</th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    @if (!empty($extra_services))
                        <div class="mt-3">
                            <h6 class="mb-2"><i class="mdi mdi-alert-decagram-outline me-1"></i>
                                {{ __('surgical_trans.extras_on_patient') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('surgical_trans.service') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.quantity') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.price') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.total') }}</th>
                                            <th>{{ __('surgical_trans.reason') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($extra_services as $es)
                                            <tr>
                                                <td>{{ $es['service_name'] }}</td>
                                                <td class="text-end">
                                                    {{ rtrim(rtrim(number_format($es['quantity'], 3, '.', ''), '0'), '.') }}
                                                </td>
                                                <td class="text-end">{{ number_format($es['unit_price'], 2) }}</td>
                                                <td class="text-end fw-bold">{{ number_format($es['total'], 2) }}</td>
                                                <td class="text-muted">{{ $es['reason'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Supplies Tab -->
        <div class="tab-pane fade" id="supplies" role="tabpanel" aria-labelledby="supplies-tab">
            <div class="card">
                <div class="card-body">
                    @forelse($issues as $issue)
                        <div class="supply-issue mb-4">
                            <div
                                class="issue-header d-flex flex-wrap justify-content-between align-items-center p-3 bg-light rounded-top">
                                <div class="d-flex align-items-center gap-2">
                                    <strong>#{{ $issue->movement_number }}</strong>
                                    <span class="text-muted">{{ $issue->created_at?->format('Y-m-d H:i') }}</span>
                                </div>
                                <div>
                                    <span
                                        class="badge {{ $issue->status == 'approved' ? 'bg-success' : ($issue->status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ __('surgical_trans.issue_status_' . $issue->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('surgical_trans.product') }}</th>
                                            <th class="text-center">{{ __('surgical_trans.batch_no') }}</th>
                                            <th class="text-center">{{ __('surgical_trans.expiry_date') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.requested') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.issued') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($issue->items as $item)
                                            @php
                                                $incRow = collect($included_items ?? [])->firstWhere(
                                                    'item_id',
                                                    (int) $item->product_id,
                                                );
                                                $includedQty = (float) ($incRow['included_quantity'] ?? 0);
                                                $usedQty = (float) ($incRow['used_quantity'] ?? 0);
                                                $isExtraAll = $includedQty <= 0;
                                                $isExcess =
                                                    $includedQty > 0 && (float) $item->quantity_issued > $includedQty;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <span
                                                        class="d-block text-truncate">{{ $item->product->name_ar ?? '-' }}</span>
                                                    <div class="text-muted small">{{ $item->unit->name_ar ?? '-' }}
                                                    </div>
                                                    @if ($isExtraAll)
                                                        <span
                                                            class="badge bg-danger">{{ __('surgical_trans.extra') }}</span>
                                                    @elseif($isExcess)
                                                        <span
                                                            class="badge bg-warning text-dark">{{ __('surgical_trans.excess_qty') }}</span>
                                                        <small class="text-muted d-block">
                                                            {{ __('surgical_trans.included_qty') }}:
                                                            {{ rtrim(rtrim(number_format($includedQty, 3, '.', ''), '0'), '.') }}
                                                        </small>
                                                    @else
                                                        <span
                                                            class="badge bg-success">{{ __('surgical_trans.included') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->batch_no ?? '-' }}</td>
                                                <td class="text-center">{{ $item->expiry_date ?? '-' }}</td>
                                                <td class="text-end">
                                                    {{ rtrim(rtrim(number_format($item->quantity_requested, 3, '.', ''), '0'), '.') }}
                                                </td>
                                                <td class="text-end fw-bold">
                                                    {{ rtrim(rtrim(number_format($item->quantity_issued, 3, '.', ''), '0'), '.') }}
                                                </td>
                                                <td class="text-end">
                                                    @if ($isExtraAll)
                                                        <span
                                                            class="text-danger">{{ __('surgical_trans.extra') }}</span>
                                                    @elseif($isExcess)
                                                        <span
                                                            class="text-warning">{{ __('surgical_trans.excess_qty') }}</span>
                                                    @else
                                                        <span
                                                            class="text-success">{{ __('surgical_trans.included') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="3">{{ __('surgical_trans.total') }}</th>
                                            <th class="text-end">
                                                {{ rtrim(rtrim(number_format($issue->items->sum('quantity_requested'), 3, '.', ''), '0'), '.') }}
                                            </th>
                                            <th class="text-end">
                                                {{ rtrim(rtrim(number_format($issue->items->sum('quantity_issued'), 3, '.', ''), '0'), '.') }}
                                            </th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="mdi mdi-package-variant-remove mdi-48px mb-3 d-block"></i>
                            <p class="fs-5 mb-0">{{ __('surgical_trans.no_issued_supplies') }}</p>
                        </div>
                    @endforelse

                    @if (!empty($extra_items))
                        <div class="mt-3">
                            <h6 class="mb-2"><i class="mdi mdi-alert-decagram-outline me-1"></i>
                                {{ __('surgical_trans.extras_on_patient') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('surgical_trans.product') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.quantity') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.price') }}</th>
                                            <th class="text-end">{{ __('surgical_trans.total') }}</th>
                                            <th>{{ __('surgical_trans.reason') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($extra_items as $ei)
                                            <tr>
                                                <td>{{ $ei['item_name'] }}</td>
                                                <td class="text-end">
                                                    {{ rtrim(rtrim(number_format($ei['quantity'], 3, '.', ''), '0'), '.') }}
                                                    {{ $ei['unit'] ?? '' }}</td>
                                                <td class="text-end">{{ number_format($ei['unit_price'], 2) }}</td>
                                                <td class="text-end fw-bold">{{ number_format($ei['total'], 2) }}</td>
                                                <td class="text-muted">{{ $ei['reason'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Financial Tab -->
        <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="mdi mdi-receipt me-2"></i>
                                {{ __('surgical_trans.invoices') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @forelse($invoices as $invoice)
                                <div class="invoice-item border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>#{{ $invoice->number }}</strong>
                                            <div class="text-muted small">{{ $invoice->created_at?->format('Y-m-d') }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">{{ number_format($invoice->total, 2) }}</div>
                                            <span
                                                class="badge {{ $invoice->status == 'paid' ? 'bg-success' : ($invoice->status == 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                {{ __('surgical_trans.invoice_status_' . $invoice->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="mdi mdi-file-remove-outline mdi-48px mb-2 d-block"></i>
                                    <p class="mb-0">{{ __('surgical_trans.no_invoices') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="mdi mdi-cash me-2"></i>
                                {{ __('surgical_trans.payments') }}
                            </h5>

                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary">
                                    {{ __('surgical_trans.package_price') }}: {{ number_format($package_price, 2) }}
                                </span>

                                {{-- ✅ زر إضافة خدمات العملية للفاتورة --}}
                              <button type="button" class="btn btn-sm btn-outline-primary"
        wire:click.prevent="openAddPackageModal">
    <i class="mdi mdi-playlist-plus me-1"></i>
    {{ __('surgical_trans.add_package_services_to_invoice') }}
</button>

                            </div>
                        </div>
                        <div class="card-body">
                            @forelse($payments as $payment)
                                <div class="payment-item border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="text-capitalize">{{ $payment->payment_method }}</strong>
                                            <div class="text-muted small">{{ $payment->payment_date }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">
                                                {{ number_format($payment->amount, 2) }}
                                            </div>
                                            <div class="text-muted small">{{ $payment->reference }}</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="mdi mdi-wallet-outline mdi-48px mb-2 d-block"></i>
                                    <p class="mb-0">{{ __('surgical_trans.no_payments') }}</p>
                                </div>
                            @endforelse

                            <div class="payment-summary mt-4 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('surgical_trans.package_price') }}:</span>
                                    <strong>{{ number_format($package_price, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content_between mb-2">
                                    <span>{{ __('surgical_trans.extras_total') }}:</span>
                                    <strong class="text-danger">{{ number_format($extras_total, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('surgical_trans.grand_total') }}:</span>
                                    <strong class="text-dark">{{ number_format($grand_total, 2) }}</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('surgical_trans.services_total') }}:</span>
                                    <strong>{{ number_format($totalServices, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('surgical_trans.payments_total') }}:</span>
                                    <strong class="text-success">{{ number_format($totalPayments, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between fw-bold fs-5 pt-2 border-top">
                                    <span>{{ $balance < 0 ? __('surgical_trans.remaining') : __('surgical_trans.credit') }}:</span>
                                    <strong class="{{ $balance < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format(abs($balance), 2) }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--/col-->
            </div>
        </div>

        <!-- Medical Team Tab -->
        <div class="tab-pane fade" id="team" role="tabpanel" aria-labelledby="team-tab">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($medical_team as $member)
                            <div class="col-md-6 col-lg-4">
                                <div class="team-member-card p-3 mb-1 border rounded h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar rounded-circle bg-light d-flex align-items-center justify-content-center"
                                            style="width:48px;height:48px;">
                                            <i class="mdi mdi-account-circle mdi-36px text-dark"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h6 class="mb-1 text-truncate">{{ $member->employee->full_name ?? '-' }}
                                            </h6>
                                            <div class="text-muted small mb-1 text-truncate">
                                                {{ $member->role ?? '-' }}</div>
                                            <div class="text-dark fw-bold">{{ number_format($member->fee, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 text-muted">
                                    <i class="mdi mdi-account-off-outline mdi-48px mb-2 d-block"></i>
                                    <p class="fs-5 mb-0">{{ __('surgical_trans.no_medical_team') }}</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-4 action-buttons">
        <button class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i> {{ __('surgical_trans.back') }}
        </button>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary">
                <i class="mdi mdi-email-outline me-2"></i> {{ __('surgical_trans.send_statement') }}
            </button>
            <button onclick="window.print()" class="btn btn-success">
                <i class="mdi mdi-printer-outline me-2"></i> {{ __('surgical_trans.print_account_statement') }}
            </button>
        </div>
    </div>
</div>

{{-- مودال إضافة خدمات العملية للفاتورة --}}
{{-- مودال إضافة خدمات العملية للفاتورة --}}
<div class="modal fade" id="addPackageModal" tabindex="-1" aria-labelledby="addPackageModalLabel"
    aria-hidden="true" wire:key="addpkg-modal-{{ $bookingId }}-{{ count($packageServicesPreview) }}">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addPackageModalLabel">
                    <i class="mdi mdi-playlist-plus me-2"></i>
                    {{ __('surgical_trans.add_package_services_to_invoice') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-1"></i>
                    {{ __('surgical_trans.note_package_auto_add_all') }}
                </div>

                @if (!count($packageServicesPreview))
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-clipboard-text-off-outline mdi-48px d-block mb-2"></i>
                        <div>{{ __('surgical_trans.no_package_services_found') }}</div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('surgical_trans.service') }}</th>
                                    <th class="text-end">{{ __('surgical_trans.quantity') }}</th>
                                    <th class="text-end">{{ __('surgical_trans.unit_price') }}</th>
                                    <th class="text-end">{{ __('surgical_trans.total') }}</th>
                                    <th class="text-end">{{ __('surgical_trans.contract_contribution') }}</th>
                                    <th class="text-end">{{ __('surgical_trans.patient_contribution') }}</th>
                                </tr>
                            </thead>
                            <tbody wire:key="pkg-preview-body-{{ md5(json_encode($packageServicesPreview)) }}">
                                @foreach ($packageServicesPreview as $row)
                                    <tr>
                                        <td class="text-truncate">{{ $row['service_name'] }}</td>
                                        <td class="text-end">
                                            {{ rtrim(rtrim(number_format($row['quantity'], 3, '.', ''), '0'), '.') }}
                                        </td>
                                        <td class="text-end">{{ number_format($row['unit_price'], 2) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($row['total_price'], 2) }}</td>
                                        <td class="text-end text-success">
                                            {{ number_format($row['contract_value'], 2) }}</td>
                                        <td class="text-end text-danger">
                                            {{ number_format($row['patient_value'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    $sumTotal = array_sum(array_column($packageServicesPreview, 'total_price'));
                                    $sumContract = array_sum(array_column($packageServicesPreview, 'contract_value'));
                                    $sumPatient = array_sum(array_column($packageServicesPreview, 'patient_value'));
                                @endphp
                                <tr class="table-light">
                                    <th colspan="3">{{ __('surgical_trans.total') }}</th>
                                    <th class="text-end">{{ number_format($sumTotal, 2) }}</th>
                                    <th class="text-end text-success">{{ number_format($sumContract, 2) }}</th>
                                    <th class="text-end text-danger">{{ number_format($sumPatient, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="mdi mdi-close-circle-outline me-1"></i>{{ __('surgical_trans.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" wire:click="confirmAddPackageToInvoice"
                    @if (!count($packageServicesPreview)) disabled @endif>
                    <i class="mdi mdi-check-all me-1"></i>{{ __('surgical_trans.confirm_add') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Base */
    .surgical-booking-details {}

    html[dir="rtl"] .surgical-tabs .nav-link i {
        margin-left: 8px;
        margin-right: 0;
    }

    html[dir="rtl"] .patient-header-card {
        border-right: 4px solid #3f80ea;
        border-left: none;
    }

    /* Header Card */
    .patient-header-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-left: 4px solid #3f80ea;
    }

    .patient-meta .meta-chip {
        line-height: 1.2;
    }

    /* Quick Stats */
    .quick-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        text-align: center;
    }

    .stat-item {
        padding: 10px;
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 10px;
    }

    .stat-value {
        font-size: 1.35rem;
        font-weight: 700;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
    }

    /* Tabs */
    .surgical-tabs .nav-link {
        border: none;
        padding: 12px 18px;
        font-weight: 600;
        color: #495057;
        border-bottom: 3px solid transparent;
        border-radius: 0;
    }

    .surgical-tabs .nav-link:hover {
        color: #2563eb;
    }

    .surgical-tabs .nav-link.active {
        color: #3f80ea;
        background: transparent;
        border-bottom: 3px solid #3f80ea;
    }

    /* Tables & Cards */
    .surgical-services-table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.4px;
        color: #6c757d;
    }

    .table-hover tbody tr:hover {
        background: rgba(63, 128, 234, 0.05);
    }

    .supply-issue {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .issue-header {
        background: #f8f9fa;
    }

    .team-member-card {
        transition: all 0.25s ease;
        background: #fff;
    }

    .team-member-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }

    .payment-summary {
        background: #f8fafc;
    }

    /* Print */
    @media print {

        .surgical-tabs,
        .action-buttons {
            display: none !important;
        }

        .tab-content>.tab-pane {
            display: block !important;
            opacity: 1 !important;
        }

        .patient-header-card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        .card,
        .table {
            break-inside: avoid;
        }

        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    /* Small screens */
    @media (max-width: 768px) {
        .stat-value {
            font-size: 1.1rem;
        }

        .quick-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

{{-- سكربت تهيئة المودال وحفظ التاب --}}
<script>
document.addEventListener('livewire:load', () => {
    if (typeof bootstrap === 'undefined') return;

    const STORAGE_KEY = 'surgicalBooking.activeTab';

    if (window.Livewire && Livewire.hook) {
        Livewire.hook('message.processed', () => {
            const savedTarget = localStorage.getItem(STORAGE_KEY);
            if (savedTarget) {
                const btn = document.querySelector(`button[data-bs-target="${savedTarget}"]`);
                if (btn) new bootstrap.Tab(btn).show();
            }
        });
    }

    const addPkgModalEl = document.getElementById('addPackageModal');
    const addPkgModal = addPkgModalEl ? new bootstrap.Modal(addPkgModalEl) : null;

    window.addEventListener('show-add-package-modal', () => { addPkgModal && addPkgModal.show(); });
    window.addEventListener('hide-add-package-modal', () => { addPkgModal && addPkgModal.hide(); });

    const tabsEl = document.getElementById('surgicalBookingTabs');
    const tabButtons = tabsEl ? tabsEl.querySelectorAll('[data-bs-toggle="tab"]') : [];
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', (e) => {
            const target = e.target.getAttribute('data-bs-target');
            if (target) {
                localStorage.setItem(STORAGE_KEY, target);
                history.replaceState(null, '', target);
            }
        });
    });

    (function initFirstTab() {
        const hashId = window.location.hash ? window.location.hash.replace('#', '') : null;
        const savedTarget = localStorage.getItem(STORAGE_KEY);
        let initialTarget = null;

        if (hashId && document.querySelector(`button[data-bs-target="#${hashId}"]`)) {
            initialTarget = `#${hashId}`;
        } else if (savedTarget && document.querySelector(`button[data-bs-target="${savedTarget}"]`)) {
            initialTarget = savedTarget;
        }

        if (initialTarget) {
            const btn = document.querySelector(`button[data-bs-target="${initialTarget}"]`);
            if (btn) new bootstrap.Tab(btn).show();
        } else {
            const defaultBtn = document.getElementById('services-tab');
            if (defaultBtn) new bootstrap.Tab(defaultBtn).show();
        }
    })();
});
</script>