<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0" style="color: #fff !important;">{{ __('lockers_trans.receipts_handover_screen') }}</h3>
        </div>
        
        @if(session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show m-3">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card-body">
            <!-- فلترة البحث -->
            <div class="row g-3 mb-4 p-3 bg-light rounded">
                <div class="col-md-3">
                    <label class="form-label fw-bold">{{ __('lockers_trans.select_locker') }}</label>
                    <select wire:model="selectedLocker" class="form-select">
                        <option value="">{{ __('lockers_trans.choose') }}</option>
                        @foreach($lockers as $locker)
                            <option value="{{ $locker->id }}">{{ app()->getLocale()=='ar' ? $locker->name_ar : $locker->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">{{ __('lockers_trans.employee') }}</label>
                    <select wire:model="selectedEmployee" class="form-select">
                        <option value="">{{ __('lockers_trans.choose') }}</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">{{ __('lockers_trans.from_date') }}</label>
                    <input type="date" wire:model="fromDate" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">{{ __('lockers_trans.to_date') }}</label>
                    <input type="date" wire:model="toDate" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button wire:click.prevent="selectAll" class="btn btn-outline-primary flex-grow-1">
                        <i class="fas fa-check-double me-2"></i>
                        {{ __('lockers_trans.select_all_receipts') }}
                    </button>
                </div>
            </div>

            @if($selectedLocker && $selectedEmployee)
                <!-- إحصائيات سريعة -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ __('lockers_trans.paid_receipts') }}
                                </h5>
                                <p class="card-text h4">{{ $paidReceipts->count() }} {{ __('lockers_trans.receipts') }}</p>
                                <p class="card-text">{{ __('lockers_trans.total') }}: {{ number_format($paidReceipts->sum('total_paid'), 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h5 class="card-title text-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ __('lockers_trans.due_receipts') }}
                                </h5>
                                <p class="card-text h4">{{ $dueReceipts->count() }} {{ __('lockers_trans.receipts') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">
                                    <i class="fas fa-times-circle me-2"></i>
                                    {{ __('lockers_trans.canceled_receipts') }}
                                </h5>
                                <p class="card-text h4">{{ $canceledReceipts->count() }} {{ __('lockers_trans.receipts') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب للإيصالات -->
                <ul class="nav nav-tabs mb-4" id="receiptsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button" role="tab">
                            {{ __('lockers_trans.paid_receipts') }} ({{ $paidReceipts->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="due-tab" data-bs-toggle="tab" data-bs-target="#due" type="button" role="tab">
                            {{ __('lockers_trans.due_receipts') }} ({{ $dueReceipts->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="canceled-tab" data-bs-toggle="tab" data-bs-target="#canceled" type="button" role="tab">
                            {{ __('lockers_trans.canceled_receipts') }} ({{ $canceledReceipts->count() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="receiptsTabContent">
                    <!-- Paid Receipts Tab -->
                    <div class="tab-pane fade show active" id="paid" role="tabpanel">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" wire:model="searchPaid" placeholder="{{ __('lockers_trans.search') }}" class="form-control">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50px"><input type="checkbox" wire:click="selectAll"></th>
                                        <th>{{ __('lockers_trans.invoice_number') }}</th>
                                        <th>{{ __('lockers_trans.receipt_id') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.total_paid') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.patient_amount') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.company_amount') }}</th>
                                        <th>{{ __('lockers_trans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paidReceipts as $r)
                                        <tr>
                                            <td><input type="checkbox" value="{{ $r->id }}" wire:model="selectedReceipts"></td>
                                            <td>{{ $r->invoices_number }}</td>
                                            <td>{{ $r->id }}</td>
                                            <td class="text-end">{{ number_format($r->total_paid,2) }}</td>
                                            <td class="text-end">{{ number_format($r->invoice->patient_contribution,2) }}</td>
                                            <td class="text-end">{{ number_format($r->invoice->contract_contribution,2) }}</td>
                                            <td>
                                                <button wire:click.prevent="reviewInvoice({{ $r->invoice->id }})" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>
                                                    {{ __('lockers_trans.review') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('lockers_trans.no_paid_receipts_found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Due Receipts Tab -->
                    <div class="tab-pane fade" id="due" role="tabpanel">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" wire:model="searchDue" placeholder="{{ __('lockers_trans.search') }}" class="form-control">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50px"><input type="checkbox" wire:click="selectAll"></th>
                                        <th>{{ __('lockers_trans.invoice_number') }}</th>
                                        <th>{{ __('lockers_trans.receipt_id') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.total_paid') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.patient_amount') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.company_amount') }}</th>
                                        <th>{{ __('lockers_trans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dueReceipts as $r)
                                        <tr>
                                            <td><input type="checkbox" value="{{ $r->id }}" wire:model="selectedReceipts"></td>
                                            <td>{{ $r->invoices_number }}</td>
                                            <td>{{ $r->id }}</td>
                                            <td class="text-end">{{ number_format($r->total_paid,2) }}</td>
                                            <td class="text-end">{{ number_format($r->invoice->patient_contribution,2) }}</td>
                                            <td class="text-end">{{ number_format($r->invoice->contract_contribution,2) }}</td>
                                            <td>
                                                <button wire:click.prevent="reviewInvoice({{ $r->invoice->id }})" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>
                                                    {{ __('lockers_trans.review') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('lockers_trans.no_due_receipts_found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Canceled Receipts Tab -->
                    <div class="tab-pane fade" id="canceled" role="tabpanel">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" wire:model="searchCanceled" placeholder="{{ __('lockers_trans.search') }}" class="form-control">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50px"><input type="checkbox" wire:click="selectAll"></th>
                                        <th>{{ __('lockers_trans.invoice_number') }}</th>
                                        <th>{{ __('lockers_trans.receipt_id') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.total_paid') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.patient_amount') }}</th>
                                        <th class="text-end">{{ __('lockers_trans.company_amount') }}</th>
                                        <th>{{ __('lockers_trans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($canceledReceipts as $r)
                                        <tr>
                                            <td><input type="checkbox" value="{{ $r->id }}" wire:model="selectedReceipts"></td>
                                            <td>{{ $r->invoices_number }}</td>
                                            <td>{{ $r->id }}</td>
                                            <td class="text-end">{{ number_format($r->total_paid,2) }}</td>
                                            <td class="text-end">{{ number_format($r->invoice->patient_contribution,2) }}</td>
                                            <td class="text-end">{{ number_format($r->invoice->contract_contribution,2) }}</td>
                                            <td>
                                                <button wire:click.prevent="reviewInvoice({{ $r->invoice->id }})" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>
                                                    {{ __('lockers_trans.review') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('lockers_trans.no_canceled_receipts_found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- نموذج التسليم -->
                <div class="card border-primary mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('lockers_trans.receipt_handover_form') }}</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="receive">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('lockers_trans.total_selected_amount') }}</label>
                                    <div class="form-control bg-light py-2">
                                        {{ number_format($selectedReceiptsAmount, 2) }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('lockers_trans.delivered_amount') }}</label>
                                    <input type="number" step="0.01" wire:model="deliveredAmount" class="form-control">
                                    @error('deliveredAmount')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                @if($deliveredAmount < $paidReceipts->sum('total_paid'))
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">{{ __('lockers_trans.shortage_reason') }}</label>
                                        <input type="text" wire:model="shortageReason" class="form-control">
                                        @error('shortageReason')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <div>
                                    <span class="fw-bold me-2">{{ __('lockers_trans.selected_receipts') }}:</span>
                                    <span class="badge bg-primary">{{ count($selectedReceipts) }}</span>
                                </div>
                                <div>
                                    <button type="button" wire:click.prevent="confirmReview" class="btn btn-primary me-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ __('lockers_trans.confirm_review') }}
                                    </button>
                                    <button type="submit" class="btn btn-success" @if(!count($selectedReceipts)) disabled @endif>
                                        <i class="fas fa-hand-holding-usd me-2"></i>
                                        {{ __('lockers_trans.receive') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal تفاصيل الفاتورة -->
                @if($showInvoiceId)
                    <div class="modal fade show d-block" tabindex="-1" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">{{ __('lockers_trans.invoice_details') }}</h5>
                                    <button type="button" class="btn-close btn-close-white" wire:click="$set('showInvoiceId', null)"></button>
                                </div>
                                <div class="modal-body">
                                    @livewire('finance.program.invoice-details', ['invoice_id' =>$showInvoiceId])
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" wire:click="$set('showInvoiceId', null)">
                                        {{ __('lockers_trans.close') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-backdrop fade show"></div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-lock-open fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">{{ __('lockers_trans.select_locker_and_employee') }}</h4>
                    <p class="text-muted">{{ __('lockers_trans.please_select_locker_and_employee_to_view_receipts') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>


<style>
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    .table th {
        white-space: nowrap;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .card {
        border-radius: 0.5rem;
    }
</style>
