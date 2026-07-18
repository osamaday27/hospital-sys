<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-primary">
                <i class="ri-percent-line me-2"></i> نظام إدارة طلبات الخصم
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                    <li class="breadcrumb-item active">طلب خصم جديد</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow-lg border-0 rounded-3">
        <!-- Card Header with Gradient -->
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <i class="ri-money-dollar-circle-line me-2"></i>
                <span class="fw-bold fs-5">{{ __('lockers_trans.discount_request') }}</span>
            </div>
            <span class="badge bg-white text-primary">رقم الفاتورة: #{{ $selected_invoice->number ?? '---' }}</span>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4">
            <!-- Success Message -->
            @if (session()->has('success'))
                <div class="alert alert-success d-flex align-items-center">
                    <i class="ri-checkbox-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Invoice Summary (Visual Card) -->
            @if($selected_invoice)
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <div class="info-card bg-light-blue p-4 rounded-3 border-start border-4 border-primary">
                        <div class="row text-center">
                            <div class="col-md-4 border-end">
                                <div class="stat-value text-primary fw-bold">{{ $selected_invoice->number }}</div>
                                <div class="stat-label text-muted small">{{ __('lockers_trans.invoice_number') }}</div>
                            </div>
                            <div class="col-md-4 border-end">
                                <div class="stat-value text-success fw-bold">{{ number_format($selected_invoice->patient_contribution, 2) }} <small>{{ $selected_invoice->currency }}</small></div>
                                <div class="stat-label text-muted small">{{ __('lockers_trans.patient_contribution') }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-value text-warning fw-bold">{{ number_format($selected_invoice->discount, 2) }} <small>{{ $selected_invoice->currency }}</small></div>
                                <div class="stat-label text-muted small">{{ __('lockers_trans.current_discount') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Section -->
            <form wire:submit.prevent="submit" class="needs-validation" novalidate>
                <div class="row g-4">
                    <!-- Discount Type -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="ri-coupon-line me-2"></i>
                            {{ __('lockers_trans.discount_type') }}
                        </label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" wire:model="discount_type" value="percentage" id="percentage" autocomplete="off">
                            <label class="btn btn-outline-primary" for="percentage">
                                <i class="ri-percent-line me-1"></i> {{ __('lockers_trans.percentage') }}
                            </label>

                            <input type="radio" class="btn-check" wire:model="discount_type" value="amount" id="amount" autocomplete="off">
                            <label class="btn btn-outline-primary" for="amount">
                                <i class="ri-money-dollar-circle-line me-1"></i> {{ __('lockers_trans.fixed_amount') }}
                            </label>
                        </div>
                        @error('discount_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Dynamic Input Field -->
                    <div class="col-md-6">
                        @if($discount_type == 'percentage')
                            <label class="form-label fw-bold">
                                <i class="ri-number-1 me-2"></i>
                                {{ __('lockers_trans.percentage_value') }}
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" wire:model="percentage" step="0.01" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('percentage') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            
                            @if($calculated_amount)
                            <div class="mt-2 alert alert-info py-2">
                                <i class="ri-calculator-line me-2"></i>
                                {{ __('lockers_trans.calculated_amount') }}: 
                                <strong>{{ number_format($calculated_amount, 2) }} {{ $selected_invoice->currency ?? '' }}</strong>
                            </div>
                            @endif
                            
                        @elseif($discount_type == 'amount')
                            <label class="form-label fw-bold">
                                <i class="ri-money-dollar-circle-line me-2"></i>
                                {{ __('lockers_trans.discount_amount') }}
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" wire:model="amount" step="0.01" min="0" required>
                                <span class="input-group-text">{{ $selected_invoice->currency ?? 'SAR' }}</span>
                            </div>
                            @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    <!-- Reason Field -->
                    <div class="col-12">
                        <label class="form-label fw-bold">
                            <i class="ri-question-line me-2"></i>
                            {{ __('lockers_trans.reason') }}
                        </label>
                        <textarea class="form-control" rows="3" wire:model="reason" placeholder="{{ __('lockers_trans.reason_placeholder') }}" required></textarea>
                        @error('reason') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/locker_desktop" type="button" class="btn btn-secondary" >
                                <i class="ri-close-line me-1"></i>{{ __('lockers_trans.cancel') }} 
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ri-send-plane-line me-1"></i> {{ __('lockers_trans.submit_discount_request') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <i class="ri-information-line me-1"></i>{{ __('lockers_trans.application_will_reviewed_relevant_department') }}
            </small>
            <div class="status-badge">
                <span class="badge bg-info">
                    <i class="ri-time-line me-1"></i>{{ __('lockers_trans.Waiting_for_review') }} 
                </span>
            </div>
        </div>
    </div>
</div>