{{-- resources/views/livewire/finance/claims/claim-index.blade.php --}}
<div class="container-fluid py-4" dir="rtl" wire:ignore.self>

    {{-- التنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- الهيدر --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 text-info fw-bold">
            <i class="mdi mdi-file-document-box-outline me-2"></i> {{ __('claims_trans.claims_manager') }}
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('insurance_claims_index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-refresh"></i> {{ __('claims_trans.refresh') }}
            </a>
        </div>
    </div>

    {{-- فلاتر --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">{{ __('claims_trans.status') }}</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="all">{{ __('claims_trans.all') }}</option>
                        <option value="draft">{{ __('claims_trans.draft') }}</option>
                        <option value="submitted">{{ __('claims_trans.submitted') }}</option>
                        <option value="approved">{{ __('claims_trans.approved') }}</option>
                        <option value="paid">{{ __('claims_trans.paid') }}</option>
                        <option value="rejected">{{ __('claims_trans.rejected') }}</option>
                        <option value="cancelled">{{ __('claims_trans.cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('claims_trans.date_from') }}</label>
                    <input type="date" class="form-control" wire:model.live="from_date">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('claims_trans.date_to') }}</label>
                    <input type="date" class="form-control" wire:model.live="to_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('claims_trans.company_or_contract') }}</label>
                    <select class="form-select" wire:model.live="contract_id">
                        <option value="">{{ __('claims_trans.all') }}</option>
                        @foreach($contracts as $ct)
                            <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('claims_trans.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('claims_trans.search_placeholder_claims') }}" wire:model.live="search">
                </div>

                <div class="col-12 d-flex justify-content-between align-items-center pt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="showDeleted" wire:model.live="show_deleted">
                        <label class="form-check-label" for="showDeleted">{{ __('claims_trans.show_deleted') }}</label>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0">{{ __('claims_trans.per_page') }}</label>
                        <select class="form-select" style="width:auto" wire:model.live="perPage">
                            <option>10</option><option>25</option><option>50</option><option>100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول المطالبات --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th><a href="#" wire:click.prevent="sortBy('id')">#</a></th>
                        <th>{{ __('claims_trans.company_or_contract') }}</th>
                        <th><a href="#" wire:click.prevent="sortBy('total_claim')">{{ __('claims_trans.total_claim') }}</a></th>
                        <th>{{ __('claims_trans.total_patient_share') }}</th>
                        <th>{{ __('claims_trans.total_contract_share') }}</th>
                        <th>{{ __('claims_trans.total_discount') }}</th>
                        <th>{{ __('claims_trans.invoices_count') }}</th>
                        <th>{{ __('claims_trans.status') }}</th>
                        <th class="text-end">{{ __('claims_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($claims as $c)
                    <tr @class(['table-danger'=> $c->deleted_at])>
                        <td>{{ $c->id }}</td>
                        <td>{{ $c->contract->name ?? '-' }}</td>
                        <td class="fw-semibold">{{ number_format($c->total_claim,2) }}</td>
                        <td>{{ number_format($c->total_patient_share,2) }}</td>
                        <td>{{ number_format($c->total_contract_share,2) }}</td>
                        <td>{{ number_format($c->total_discount,2) }}</td>
                        <td><span class="badge bg-light text-dark">{{ $c->invoices_count ?? 0 }}</span></td>
                        <td>
                            @php
                                $cls = match($c->status) {
                                    'draft'     => 'bg-secondary',
                                    'submitted' => 'bg-info',
                                    'approved'  => 'bg-primary',
                                    'paid'      => 'bg-success',
                                    'rejected'  => 'bg-danger',
                                    'cancelled' => 'bg-warning text-dark',
                                    default     => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $cls }}">{{ __('claims_trans.'.$c->status) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-secondary" wire:click="openDetails({{ $c->id }})">
                                    <i class="mdi mdi-eye-outline"></i> {{ __('claims_trans.details') }}
                                </button>

                                @if(!$c->deleted_at)
                                    @if($c->status === 'draft')
                                        <button class="btn btn-sm btn-info" wire:click="submit({{ $c->id }})">
                                            <i class="mdi mdi-send"></i> {{ __('claims_trans.submit') }}
                                        </button>
                                    @endif
                                    @if($c->status === 'submitted')
                                        <button class="btn btn-sm btn-primary" wire:click="approve({{ $c->id }})">
                                            <i class="mdi mdi-check"></i> {{ __('claims_trans.approve') }}
                                        </button>
                                    @endif
                                    @if($c->status === 'approved')
                                        <button class="btn btn-sm btn-success" wire:click="markPaid({{ $c->id }})">
                                            <i class="mdi mdi-cash-check"></i> {{ __('claims_trans.mark_paid') }}
                                        </button>
                                    @endif

                                    @if(!in_array($c->status,['paid']))
                                        <button class="btn btn-sm btn-warning" wire:click="cancel({{ $c->id }})">
                                            <i class="mdi mdi-cancel"></i> {{ __('claims_trans.cancel') }}
                                        </button>
                                    @endif

                                    <button class="btn btn-sm btn-outline-danger" wire:click="reject({{ $c->id }})">
                                        <i class="mdi mdi-close-circle"></i> {{ __('claims_trans.reject') }}
                                    </button>

                                    <button class="btn btn-sm btn-danger" wire:click="deleteClaim({{ $c->id }})">
                                        <i class="mdi mdi-trash-can-outline"></i> {{ __('claims_trans.delete') }}
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-warning" wire:click="restoreClaim({{ $c->id }})">
                                        <i class="mdi mdi-backup-restore"></i> {{ __('claims_trans.restore') }}
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="mdi mdi-information-outline"></i> {{ __('claims_trans.no_data') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($claims->hasPages())
            <div class="card-footer">
                {{ $claims->links() }}
            </div>
        @endif
    </div>

    {{-- مودال التفاصيل --}}
    <div class="modal @if($showDetailsModal) show d-block @endif" tabindex="-1" style="@if($showDetailsModal) display:block; background:rgba(0,0,0,.4) @endif">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content" wire:ignore.self>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-file-document-edit-outline me-2"></i>
                        {{ __('claims_trans.claim_details') }} #{{ $selected_claim_id }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeDetails"></button>
                </div>
                <div class="modal-body">
                    @if($selectedClaim)
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="text-muted">{{ __('claims_trans.company_or_contract') }}</label>
                                <div class="fw-semibold">{{ $selectedClaim->contract->name ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted">{{ __('claims_trans.patient_name') }}</label>
                                <div class="fw-semibold">
                                    {{ $selectedClaim->patient->full_name ?? ($selectedClaim->patient->name ?? '-') }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted">{{ __('claims_trans.status') }}</label>
                                <div class="fw-semibold">{{ __('claims_trans.'.$selectedClaim->status) }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted">{{ __('claims_trans.user') }}</label>
                                <div class="fw-semibold">{{ $selectedClaim->user->name ?? '-' }}</div>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted">{{ __('claims_trans.notes') }}</label>
                                <div class="fw-semibold">{{ $selectedClaim->notes ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light fw-bold">
                                        {{ __('claims_trans.totals') }}
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 mb-2">{{ __('claims_trans.total_claim') }}:</div>
                                            <div class="col-6 text-end fw-bold">{{ number_format($selectedClaim->total_claim,2) }}</div>

                                            <div class="col-6 mb-2">{{ __('claims_trans.total_discount') }}:</div>
                                            <div class="col-6 text-end">{{ number_format($selectedClaim->total_discount,2) }}</div>

                                            <div class="col-6 mb-2">{{ __('claims_trans.total_patient_share') }}:</div>
                                            <div class="col-6 text-end">{{ number_format($selectedClaim->total_patient_share,2) }}</div>

                                            <div class="col-6 mb-2">{{ __('claims_trans.total_contract_share') }}:</div>
                                            <div class="col-6 text-end">{{ number_format($selectedClaim->total_contract_share,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light fw-bold">
                                        {{ __('claims_trans.key_dates') }}
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 mb-2">{{ __('claims_trans.submission_date') }}:</div>
                                            <div class="col-6 text-end">{{ $selectedClaim->submission_date ?? '-' }}</div>

                                            <div class="col-6 mb-2">{{ __('claims_trans.approval_date') }}:</div>
                                            <div class="col-6 text-end">{{ $selectedClaim->approval_date ?? '-' }}</div>

                                            <div class="col-6 mb-2">{{ __('claims_trans.payment_date') }}:</div>
                                            <div class="col-6 text-end">{{ $selectedClaim->payment_date ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- جدول الفواتير المرتبطة --}}
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-light fw-bold">
                                {{ __('claims_trans.linked_invoices') }}
                                <span class="badge bg-light text-dark ms-2">{{ count($selectedInvoices) }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('claims_trans.invoice_code') }}</th>
                                            <th>{{ __('claims_trans.patient_name') }}</th>
                                            <th>{{ __('claims_trans.clinic') }}</th>
                                            <th>{{ __('claims_trans.total') }}</th>
                                            <th>{{ __('claims_trans.discount') }}</th>
                                            <th>{{ __('claims_trans.patient_share') }}</th>
                                            <th>{{ __('claims_trans.contract_share') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($selectedInvoices as $inv)
                                            <tr>
                                                <td>{{ $inv['id'] }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $inv['code'] ?? $inv['number'] }}</span></td>
                                                <td>
                                                    {{ $inv['patient']['full_name'] ?? ($inv['patient']['name'] ?? '-') }}
                                                </td>
                                                <td>{{ $inv['reservations']['clinic']['name'] ?? '-' }}</td>
                                                <td>{{ number_format($inv['total'] ?? 0,2) }}</td>
                                                <td>{{ number_format($inv['discount'] ?? 0,2) }}</td>
                                                <td>{{ number_format($inv['patient_contribution'] ?? 0,2) }}</td>
                                                <td>{{ number_format($inv['contract_contribution'] ?? 0,2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="text-center text-muted">{{ __('claims_trans.no_data') }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- جدول أسطر المطالبة --}}
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-light fw-bold">
                                {{ __('claims_trans.claim_lines') }}
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('claims_trans.service') }}</th>
                                            <th>{{ __('claims_trans.quantity') }}</th>
                                            <th>{{ __('claims_trans.unit_price') }}</th>
                                            <th>{{ __('claims_trans.gross') }}</th>
                                            <th>{{ __('claims_trans.discount') }}</th>
                                            <th>{{ __('claims_trans.patient_share') }}</th>
                                            <th>{{ __('claims_trans.contract_share') }}</th>
                                            <th>{{ __('claims_trans.net_payable') }}</th>
                                            <th>{{ __('claims_trans.line_notes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($selectedLines as $ln)
                                            <tr>
                                                <td>{{ $ln['service'] }}</td>
                                                <td>{{ number_format($ln['quantity'],2) }}</td>
                                                <td>{{ number_format($ln['unit_price'],2) }}</td>
                                                <td>{{ number_format($ln['gross'],2) }}</td>
                                                <td>{{ number_format($ln['discount'],2) }}</td>
                                                <td>{{ number_format($ln['patient_share'],2) }}</td>
                                                <td>{{ number_format($ln['contract_share'],2) }}</td>
                                                <td>{{ number_format($ln['net_payable'],2) }}</td>
                                                <td>{{ $ln['notes'] }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="9" class="text-center text-muted">{{ __('claims_trans.no_data') }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    @else
                        <div class="text-center text-muted">{{ __('claims_trans.no_data') }}</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="closeDetails">{{ __('claims_trans.close') }}</button>
                </div>
            </div>
        </div>
    </div>

</div>
