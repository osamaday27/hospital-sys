<div class="card shadow-sm" dir="rtl">
    <div class="card-header bg-warning d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-dark">
            <i class="mdi mdi-file-document-edit me-2"></i> {{ __('suppliers.edit_service_invoice') }}
        </h5>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="mdi mdi-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <form wire:submit.prevent="save" class="needs-validation" novalidate>
            <div class="row g-3 mb-4">
                <!-- المورد -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.supplier') }} <span class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="supplier_id" class="form-select select2">
                            <option value="">{{ __('suppliers.choose_supplier') }}</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}" @if ($supplier_id == $sup->id) selected @endif>
                                    {{ $sup->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('supplier_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <!-- رقم الفاتورة -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.invoice_no') }} <span class="text-danger">*</span></label>
                    <input type="text" wire:model="invoice_no" class="form-control form-control-sm" required>
                    @error('invoice_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <!-- تاريخ الفاتورة -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.invoice_date') }} <span class="text-danger">*</span></label>
                    <input type="date" wire:model="invoice_date" class="form-control form-control-sm" required>
                    @error('invoice_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <!-- تاريخ الاستحقاق -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.due_date') }}</label>
                    <input type="date" wire:model="due_date" class="form-control form-control-sm">
                </div>

                <!-- الفرع -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.branch') }}</label>
                    <div wire:ignore>
                        <select id="branch_id" class="form-select select2">
                            <option value="">{{ __('suppliers.choose_branch') }}</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @if ($branch_id == $branch->id) selected @endif>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- جدول البنود -->
            <div class="table-responsive mt-3">
                <table class="table table-bordered align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('suppliers.service') }}</th>
                            <th>{{ __('suppliers.description') }}</th>
                            <th>{{ __('suppliers.qty') }}</th>
                            <th>{{ __('suppliers.unit_price') }}</th>
                            <th>{{ __('suppliers.discount') }}</th>
                            <th>{{ __('suppliers.tax') }}</th>
                            <th>{{ __('suppliers.total') }}</th>
                            <th>{{ __('suppliers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $row)
                            <tr>
                                <!-- الخدمة -->
                                <td>
                                    <div wire:ignore>
                                        <select class="form-select form-select-sm service-select select2" data-index="{{ $index }}">
                                            <option value="">{{ __('suppliers.choose_service') }}</option>
                                            @foreach ($services as $srv)
                                                <option value="{{ $srv->id }}" @if ($row['service_id'] == $srv->id) selected @endif>
                                                    {{ $srv->name_ar }} - {{ $srv->price }} {{ $srv->unit->name_ar ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>

                                <!-- الوصف -->
                                <td>
                                    <input type="text" wire:model="items.{{ $index }}.description"
                                           class="form-control form-control-sm">
                                </td>

                                <!-- الكمية -->
                                <td>
                                    <input type="number" min="1" wire:model="items.{{ $index }}.qty"
                                           wire:input="calculateTotals" class="form-control form-control-sm text-center">
                                </td>

                                <!-- السعر -->
                                <td>
                                    <input type="number" step="0.01" wire:model="items.{{ $index }}.unit_price"
                                           wire:input="calculateTotals" class="form-control form-control-sm text-end">
                                </td>

                                <!-- الخصم -->
                                <td>
                                    <input type="number" step="0.01" wire:model="items.{{ $index }}.discount"
                                           wire:input="calculateTotals" class="form-control form-control-sm text-end">
                                </td>

                                <!-- الضريبة -->
                                <td>
                                    <div class="input-group input-group-sm">
                                        <select wire:model="items.{{ $index }}.tax_type" wire:change="calculateTotals"
                                                class="form-select form-select-sm w-50">
                                            <option value="percent">%</option>
                                            <option value="value">ج.م</option>
                                        </select>
                                        <input type="number" step="0.01" wire:model="items.{{ $index }}.tax"
                                               wire:input="calculateTotals" class="form-control form-control-sm text-end">
                                    </div>
                                </td>

                                <!-- الإجمالي -->
                                <td class="fw-semibold text-success text-end">{{ $row['total'] }} ج.م</td>

                                <!-- حذف -->
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle"
                                            wire:click="removeItem({{ $index }})">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- ملخص الفاتورة -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <h6 class="border-bottom pb-2 mb-3"><i class="mdi mdi-credit-card me-2"></i> {{ __('suppliers.payment_method') }}</h6>

                        <div wire:ignore>
                            <select id="payment_method" class="form-select form-select-sm select2">
                                <option value="cash" @if ($payment_method == 'cash') selected @endif>كاش</option>
                                <option value="visa" @if ($payment_method == 'visa') selected @endif>فيزا</option>
                                <option value="bank" @if ($payment_method == 'bank') selected @endif>تحويل بنكي</option>
                            </select>
                        </div>

                        @if ($payment_method == 'bank')
                            <div class="mt-2">
                                <input type="text" wire:model="bank_name" class="form-control form-control-sm mb-2" placeholder="اسم البنك">
                                <input type="text" wire:model="bank_details" class="form-control form-control-sm" placeholder="تفاصيل التحويل">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <h6 class="border-bottom pb-2 mb-3"><i class="mdi mdi-calculator me-2"></i> {{ __('suppliers.invoice_summary') }}</h6>
                        <div class="d-flex justify-content-between mb-2"><span>{{ __('suppliers.subtotal') }}</span><span>{{ $subtotal }} ج.م</span></div>
                        <div class="d-flex justify-content-between mb-2"><span>{{ __('suppliers.discount') }}</span><span class="text-danger">- {{ $discount }} ج.م</span></div>
                        <div class="d-flex justify-content-between mb-2"><span>{{ __('suppliers.tax') }}</span><span>{{ $tax }} ج.م</span></div>
                        <div class="d-flex justify-content-between border-top pt-2 mb-2"><span class="fw-bold">{{ __('suppliers.total_amount') }}</span><span class="fw-bold">{{ $total_amount }} ج.م</span></div>
                        <div class="d-flex justify-content-between mb-2"><span>{{ __('suppliers.paid_amount') }}</span><span>{{ $paid_amount }} ج.م</span></div>
                        <div class="d-flex justify-content-between"><span>{{ __('suppliers.remaining_amount') }}</span><span class="{{ $remaining_amount > 0 ? 'text-danger' : 'text-success' }}">{{ $remaining_amount }} ج.م</span></div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-start">
                <button type="submit" class="btn btn-warning px-4">
                    <i class="mdi mdi-content-save-check me-1"></i> {{ __('suppliers.update_invoice') }}
                </button>
                <a href="{{ route('serviceinvoicesindex') }}" class="btn btn-secondary px-4 ms-2">
                    <i class="mdi mdi-cancel me-1"></i> {{ __('suppliers.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function initSelect2() {
        $('.select2').select2({
            width: '100%',
            dir: "rtl"
        }).on('change', function () {
            let value = $(this).val();
            if ($(this).attr('id') === 'supplier_id') {
                @this.set('supplier_id', value);
            } else if ($(this).attr('id') === 'branch_id') {
                @this.set('branch_id', value);
            } else if ($(this).attr('id') === 'payment_method') {
                @this.set('payment_method', value);
            } else if ($(this).hasClass('service-select')) {
                let index = $(this).data('index');
                @this.set(`items.${index}.service_id`, value);
            }
        });
    }

    document.addEventListener('livewire:load', function () {
        initSelect2();
        Livewire.hook('message.processed', () => { initSelect2(); });
    });
</script>
