<div class="container-fluid py-3">
    <!-- Header with Progress Indicator -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex align-items-center" style="margin-bottom: 19px;">
                <h2 class="fw-bold mb-0">
                    <i class="ri-close-circle-line me-2 text-danger"></i>
                    {{ __('lockers_trans.submit_cancellation_request') }}
                </h2>
               
            </div>
            
      
    <!-- Main Card -->
    <div class="card border-0 shadow-lg">
        <!-- Card Header with Warning -->
        <div class="card-header bg-gradient-danger text-white d-flex justify-content-between align-items-center">
            <div style="color: red;">
                <i class="ri-alert-fill me-2"></i>
                <strong>{{ __('lockers_trans.warning') }}:</strong> {{ __('lockers_trans.cancellation_may_have_fee') }}
            </div>
            <button class="btn btn-sm btn-light" wire:click="showPolicy">
                <i class="ri-information-line me-1"></i> {{ __('lockers_trans.view_policy') }}
            </button>
        </div>
        

        <!-- Card Body -->
        <div class="card-body">
            <!-- Reservation Summary -->
            <div class="alert alert-warning bg-light-warning border-start border-4 border-warning">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold"><i class="ri-calendar-line me-2"></i> {{ __('lockers_trans.reservation_info') }}</h6>                        <ul class="list-unstyled">
                            <li><strong>{{ __('lockers_trans.reservation_number') }}:</strong> #{{ $invoice->number }}</li>
                            <li><strong>{{ __('lockers_trans.reservation_date') }}:</strong> {{ $invoice->created_at->format('d/m/Y') }}</li>
                            <li><strong>{{ __('lockers_trans.reservation_status') }}:</strong> 
                                <span class="badge bg-success">{{ __('lockers_trans.confirmed') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold"><i class="ri-user-line me-2"></i> {{ __('lockers_trans.client_info') }}</h6>                        <ul class="list-unstyled">
                                <ul class="list-unstyled">
                                <li>
                                    <strong>{{ __('lockers_trans.client_name') }}:</strong> {{ $invoice->patient->full_name ?? '-' }}
                                </li>
                            
                                @if($invoice->patient->phone_number)
                                <li>
                                    <strong>{{ __('lockers_trans.primary_phone') }}:</strong>
                                    <a href="tel:{{ $invoice->patient->phone_number }}" class="text-decoration-none">
                                        <i class="ri-phone-line me-1"></i> {{ $invoice->patient->phone_number }}
                                    </a>
                                </li>
                                @endif
                            
                                @if($invoice->patient->phone_number2)
                                <li>
                                    <strong>{{ __('lockers_trans.secondary_phone') }}:</strong>
                                    <a href="tel:{{ $invoice->patient->phone_number2 }}" class="text-decoration-none">
                                        <i class="ri-phone-line me-1"></i> {{ $invoice->patient->phone_number2 }}
                                    </a>
                                </li>
                                @endif
                            
                                @if($invoice->patient->whatsapp)
                                <li>
                                    <strong>{{ __('lockers_trans.whatsapp') }}:</strong>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->patient->whatsapp) }}"
                                       target="_blank" class="text-success text-decoration-none">
                                        <i class="ri-whatsapp-line me-1"></i> {{ $invoice->patient->whatsapp }}
                                    </a>
                                </li>
                                @endif
                            
                                <li>
                                    <strong>{{ __('lockers_trans.client_email') }}:</strong> {{ $invoice->patient->email ?? '-' }}
                                </li>
                            </ul>
                            
                    </div>
                </div>
            </div>

            <!-- Cancellation Form -->
            <form wire:submit.prevent="submit" class="needs-validation" novalidate>
                <div class="row g-3">
                    <!-- Reason Selection -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="ri-questionnaire-line me-2 text-danger"></i>
                            {{ __('lockers_trans.cancellation_reason') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-light-danger">
                                <i class="ri-arrow-down-s-line text-danger"></i>
                            </span>
                            <select class="form-select border-start-0 ps-2" 
                                    wire:model.defer="cancellation_reason" 
                                    required
                                    style="cursor: pointer;">
                                <option value="" disabled selected>{{ __('lockers_trans.select_reason') }}</option>
                                @foreach($reasons as $id => $name)
                                    <option value="{{ $id }}" data-icon="ri-{{ $icons[$id] ?? 'close-line' }}">
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cancellation_reason') 
                            <div class="invalid-feedback d-block">
                                <i class="ri-error-warning-line me-1"></i> {{ $message }}
                            </div> 
                            @enderror
                        </div>
                    </div>

                    <!-- Dynamic Fields Based on Reason -->
                    @if(in_array($cancellation_reason, [2,5]))
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="ri-calendar-event-line me-2"></i>
                            التاريخ البديل المقترح
                        </label>
                        <input type="date" class="form-control" 
                               min="{{ now()->addDays(2)->format('Y-m-d') }}"
                               wire:model.defer="alternative_date">
                    </div>
                    @endif

                    <!-- Notes Field -->
                    <div class="col-12">
                        <label class="form-label fw-bold">
                            <i class="ri-edit-2-line me-2 text-danger"></i>
                            {{ __('lockers_trans.cancellation_notes') }}
                            <span class="text-muted small">(اختياري)</span>
                        </label>
                        <textarea class="form-control" 
                                  wire:model.defer="cancellation_notes" 
                                  rows="3"
                                  placeholder="الرجاء توضيح سبب الإلغاء بالتفصيل إن أمكن..."></textarea>
                        <div class="form-text">سيتم مراجعة ملاحظاتك من قبل فريق الدعم</div>
                        @error('cancellation_notes') 
                        <div class="invalid-feedback d-block">
                            <i class="ri-error-warning-line me-1"></i> {{ $message }}
                        </div> 
                        @enderror
                    </div>

                    <!-- Warning Box -->
                    <div class="col-12">
                        <div class="alert alert-danger bg-light-danger border-0 d-flex">
                            <i class="ri-alert-line me-3 fs-4 text-danger"></i>
                            <div>
                                <h6 class="alert-heading fw-bold">تنبيه هام!</h6>
                                <p class="mb-0">سيتم تطبيق رسوم إلغاء بنسبة 20% من قيمة الحجز إذا تم الإلغاء خلال أقل من 24 ساعة من الموعد المحدد</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 mt-2">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" wire:click="goBack">
                                <i class="ri-arrow-left-line me-1"></i> رجوع
                            </button>
                            <div>
                                <button type="button" class="btn btn-light me-2" wire:click="resetForm">
                                    <i class="ri-close-line me-1"></i> إلغاء
                                </button>
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="ri-send-plane-line me-1"></i> {{ __('lockers_trans.send_request') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <i class="ri-time-line me-1"></i> آخر تحديث: {{ now()->format('Y-m-d H:i') }}
            </small>
           
        </div>
    </div>
</div>

<!-- Cancellation Policy Modal (hidden by default) -->
<div class="modal fade" id="policyModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="ri-file-paper-2-line me-2"></i> سياسة الإلغاء
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Policy content would be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> إغلاق
                </button>
            </div>
        </div>
    </div>
</div>