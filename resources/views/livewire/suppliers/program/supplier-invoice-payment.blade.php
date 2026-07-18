<div dir="rtl">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-4">
                <!-- Header -->
                <div class="card-header bg-gradient bg-info text-white rounded-top d-flex align-items-center">
                    <i class="ri-bank-card-line me-2 fs-5"></i>
                    <h4 class="mb-0 text-light">{{ __('suppliers.add_payment') }}</h4>
                </div>

                <div class="card-body p-4">
                    {{-- رسالة نجاح --}}
                    @if (session()->has('success'))
                        <div class="alert alert-success shadow-sm rounded-pill px-4">
                            <i class="ri-check-double-line me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <!-- معلومات الفاتورة -->
                    <div class="border rounded-3 p-3 bg-light mb-4">
                        <h5 class="mb-3 text-secondary">
                            <i class="ri-file-list-3-line me-2"></i> {{ __('suppliers.invoice_info') }}
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <strong><i class="ri-file-paper-2-line text-info"></i> {{ __('suppliers.invoice_no') }}:</strong>
                                <span class="text-info">{{ $invoice->invoice_no }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong><i class="ri-user-3-line text-info"></i> {{ __('suppliers.supplier') }}:</strong>
                                <span>{{ $invoice->supplier->name_ar ?? '' }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong><i class="ri-money-dollar-circle-line text-success"></i> {{ __('suppliers.total_amount') }}:</strong>
                                <span class="fw-bold text-success">{{ number_format($invoice->total_amount, 2) }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong><i class="ri-error-warning-line text-danger"></i> {{ __('suppliers.remaining_amount') }}:</strong>
                                <span class="fw-bold text-danger">{{ number_format($invoice->remaining_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form wire:submit.prevent="save" class="row g-3">
                        {{-- الملاحظات --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><i class="ri-sticky-note-line me-1 text-info"></i> {{ __('suppliers.notes') }}</label>
                            <textarea class="form-control shadow-sm" wire:model="notes" rows="2"></textarea>
                        </div>

                        {{-- الأقساط --}}
                        <div class="col-12">
                            <h5 class="mt-4 mb-3 text-secondary"><i class="ri-calendar-check-line me-2"></i> {{ __('suppliers.installments') }}</h5>

                            @foreach ($installments as $index => $inst)
                                <div class="border rounded-3 p-3 mb-3 shadow-sm bg-white" wire:key="inst-{{ $inst['uuid'] }}">
                                    <div class="row g-3">
                                        {{-- المبلغ --}}
                                        <div class="col-md-2">
                                            <label class="form-label"><i class="ri-money-dollar-circle-line"></i> {{ __('suppliers.amount') }}</label>
                                            <input type="number" step="0.01" class="form-control shadow-sm"
                                                wire:model="installments.{{ $index }}.amount">
                                        </div>

                                        {{-- وسيلة الدفع --}}
                                        <div class="col-md-2">
                                            <label class="form-label"><i class="ri-exchange-dollar-line"></i> {{ __('suppliers.method') }}</label>
                                            <select class="form-select shadow-sm" wire:model="installments.{{ $index }}.method">
                                                <option value="cash">{{ __('suppliers.cash') }}</option>
                                                <option value="bank_transfer">{{ __('suppliers.bank_transfer') }}</option>
                                                <option value="cheque">{{ __('suppliers.cheque') }}</option>
                                                <option value="visa">{{ __('suppliers.visa') }}</option>
                                                <option value="other">{{ __('suppliers.other') }}</option>
                                            </select>
                                        </div>

                                        {{-- التاريخ --}}
                                        <div class="col-md-2">
                                            <label class="form-label"><i class="ri-calendar-line"></i> {{ __('suppliers.due_date') }}</label>
                                            <input type="date" class="form-control shadow-sm"
                                                wire:model="installments.{{ $index }}.due_date">
                                        </div>

                                        {{-- المرجع --}}
                                        <div class="col-md-2">
                                            <label class="form-label"><i class="ri-hashtag"></i> {{ __('suppliers.reference_no') }}</label>
                                            <input type="text" class="form-control shadow-sm"
                                                wire:model="installments.{{ $index }}.reference_no">
                                        </div>

                                        {{-- الخزنة لو كاش فقط --}}
                                        @if ($inst['method'] === 'cash')
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold"><i class="ri-home-line me-1 text-info"></i> {{ __('suppliers.locker') }}</label>
                                                <select class="form-select shadow-sm" wire:model="installments.{{ $index }}.locker_id">
                                                    <option value="">{{ __('suppliers.select_locker') }}</option>
                                                    @foreach ($lockers as $locker)
                                                        <option value="{{ $locker->id }}">{{ $locker->name_ar }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        {{-- شيك --}}
                                        @if ($inst['method'] === 'cheque')
                                            <div class="col-md-3">
                                                <label class="form-label"><i class="ri-bank-line"></i> {{ __('suppliers.bank') }}</label>
                                                <select class="form-select shadow-sm" wire:model="installments.{{ $index }}.bank_id">
                                                    <option value="">{{ __('suppliers.select_bank') }}</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->name_ar }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label"><i class="ri-bank-card-2-line"></i> {{ __('suppliers.bank_account') }}</label>
                                                <select class="form-select shadow-sm" wire:model="installments.{{ $index }}.bank_account_id">
                                                    <option value="">{{ __('suppliers.select_account') }}</option>
                                                    @foreach ($inst['bankAccounts'] ?? [] as $acc)
                                                        <option value="{{ $acc['id'] }}">{{ $acc['account_name'] }} - {{ $acc['account_number'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label"><i class="ri-book-2-line"></i> {{ __('suppliers.cheque_book') }}</label>
                                                <select class="form-select shadow-sm" wire:model="installments.{{ $index }}.cheque_book_id">
                                                    <option value="">{{ __('suppliers.select_checkbook') }}</option>
                                                    @foreach ($inst['chequeBooks'] ?? [] as $book)
                                                        <option value="{{ $book['id'] }}">{{ $book['book_number'] }} ({{ $book['start_number'] }} - {{ $book['end_number'] }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label"><i class="ri-file-list-line"></i> {{ __('suppliers.cheque_leaf') }}</label>
                                                <select class="form-select shadow-sm" wire:model="installments.{{ $index }}.cheque_leaf_id">
                                                    <option value="">{{ __('suppliers.select_cheque') }}</option>
                                                    @foreach ($inst['chequeLeaves'] ?? [] as $leaf)
                                                        <option value="{{ $leaf['id'] }}">{{ $leaf['cheque_number'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        {{-- تحويل بنكي / فيزا --}}
                                        @if (in_array($inst['method'], ['bank_transfer','visa']))
                                            <div class="col-md-4">
                                                <label class="form-label"><i class="ri-bank-line"></i> {{ __('suppliers.bank') }}</label>
                                                <select class="form-select shadow-sm" wire:model="installments.{{ $index }}.bank_id">
                                                    <option value="">{{ __('suppliers.select_bank') }}</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->name_ar }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label"><i class="ri-bank-card-line"></i> {{ __('suppliers.bank_account') }}</label>
                                                <select class="form-select shadow-sm" wire:model="installments.{{ $index }}.bank_account_id">
                                                    <option value="">{{ __('suppliers.select_account') }}</option>
                                                    @foreach ($inst['bankAccounts'] ?? [] as $acc)
                                                        <option value="{{ $acc['id'] }}">{{ $acc['account_name'] }} - {{ $acc['account_number'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        {{-- أخرى --}}
                                        @if ($inst['method'] === 'other')
                                            <div class="col-md-6">
                                                <label class="form-label"><i class="ri-edit-2-line"></i> {{ __('suppliers.other_details') }}</label>
                                                <input type="text" class="form-control shadow-sm"
                                                    wire:model="installments.{{ $index }}.other_details"
                                                    placeholder="{{ __('suppliers.enter_details') }}">
                                            </div>
                                        @endif

                                        {{-- حذف --}}
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                wire:click="removeInstallment({{ $index }})">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="button" class="btn btn-outline-info rounded-pill shadow-sm" wire:click="addInstallment">
                                <i class="ri-add-line"></i> {{ __('suppliers.add_installment') }}
                            </button>
                        </div>

                        <!-- زر الحفظ -->
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-success px-5 rounded-pill shadow">
                                <i class="ri-check-line me-1"></i> {{ __('suppliers.save_payment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
