<div class="card shadow-sm" dir="rtl">
    <div class="card-header bg-primary d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">
            <i class="mdi mdi-file-document me-2"></i> {{ __('suppliers.supplier_service_invoices') }}
        </h5>
        <span class="badge bg-light text-dark">فاتورة جديدة</span>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="mdi mdi-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- نموذج إضافة فاتورة خدمة -->
        <form wire:submit.prevent="saveInvoice" class="needs-validation" novalidate>
            <div class="row g-3 mb-4">
                <!-- المورد -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.supplier') }} <span class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="supplier_id" class="form-select form-select-sm select2" data-placeholder="{{ __('suppliers.choose_supplier') }}">
                            <option value="">{{ __('suppliers.choose_supplier') }}</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}" @if ($supplier_id == $sup->id) selected @endif>{{ $sup->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('supplier_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <!-- رقم الفاتورة -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.invoice_no') }} <span class="text-danger">*</span></label>
                    <input type="text" wire:model="invoice_no" class="form-control form-control-sm" placeholder="أدخل رقم الفاتورة" required>
                    @error('invoice_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <!-- تاريخ الفاتورة -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.invoice_date') }} <span class="text-danger">*</span></label>
                    <input type="date" wire:model="invoice_date" class="form-control form-control-sm" required>
                    @error('invoice_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <!-- اختيار الفرع -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('suppliers.branch') }}</label>
                    <div wire:ignore>
                        <select id="branch_id" class="form-select form-select-sm select2" data-placeholder="{{ __('suppliers.choose_branch') }}">
                            <option value="">{{ __('suppliers.choose_branch') }}</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @if ($branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- بنود الخدمات -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h6 class="mb-0"><i class="mdi mdi-cart-outline me-2"></i> {{ __('suppliers.invoice_items') }}</h6>
                <button type="button" class="btn btn-primary btn-sm px-3" wire:click="addItem">
                    <i class="mdi mdi-plus-circle-outline me-1"></i> {{ __('suppliers.add_service') }}
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="18%">{{ __('suppliers.service') }}</th>
                            <th width="20%">{{ __('suppliers.description') }}</th>
                            <th width="8%">{{ __('suppliers.qty') }}</th>
                            <th width="12%">{{ __('suppliers.unit_price') }}</th>
                            <th width="10%">{{ __('suppliers.discount') }}</th>
                            <th width="20%">{{ __('suppliers.tax') }}</th>
                            <th width="12%">{{ __('suppliers.total') }}</th>
                            <th width="5%">{{ __('suppliers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $row)
                            <tr>
                                <!-- الخدمة -->
                                <td>
                                    <div wire:ignore>
                                        <select class="form-select form-select-sm service-select select2" data-index="{{ $index }}" data-placeholder="{{ __('suppliers.choose_service') }}">
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
                                    <input type="text" wire:model="items.{{ $index }}.description" class="form-control form-control-sm" placeholder="وصف الخدمة">
                                </td>

                                <!-- الكمية -->
                                <td>
                                    <input type="number" step="1" min="1" wire:model="items.{{ $index }}.qty" wire:input="calculateTotals" class="form-control form-control-sm text-center">
                                </td>

                                <!-- السعر -->
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" wire:model="items.{{ $index }}.unit_price" wire:input="calculateTotals" class="form-control form-control-sm text-end">
                                        <span class="input-group-text">ج.م</span>
                                    </div>
                                </td>

                                <!-- الخصم -->
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" wire:model="items.{{ $index }}.discount" wire:input="calculateTotals" class="form-control form-control-sm text-end">
                                        <span class="input-group-text">ج.م</span>
                                    </div>
                                </td>

                                <!-- نوع الضريبة + القيمة/النسبة -->
                                <td>
                                    <div class="input-group input-group-sm">
                                        <select wire:model="items.{{ $index }}.tax_type" wire:change="calculateTotals" class="form-select form-select-sm w-50 " data-placeholder="{{ __('suppliers.tax_type') }}">
                                            <option value="percent">%</option>
                                            <option value="value">ج.م</option>
                                        </select>
                                        <input type="number" step="0.01" wire:model="items.{{ $index }}.tax" wire:input="calculateTotals" class="form-control form-control-sm text-end" placeholder="قيمة/نسبة">
                                    </div>
                                </td>

                                <!-- الإجمالي -->
                                <td class="fw-semibold text-success text-end">{{ $row['total'] }} ج.م</td>

                                <!-- حذف -->
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" wire:click="removeItem({{ $index }})">
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
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="mdi mdi-credit-card me-2"></i> {{ __('suppliers.payment_method') }}
                        </h6>

                        <div class="row mb-2">
                            <div class="col-md-4 fw-semibold">{{ __('suppliers.payment_method') }}</div>
                            <div class="col-md-8">
                                <div wire:ignore>
                                    <select id="payment_method" class="form-select form-select-sm select2" data-placeholder="{{ __('suppliers.choose_payment_method') }}">
                                        <option value="cash">كاش</option>
                                        <option value="visa">فيزا</option>
                                        <option value="bank">تحويل بنكي</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if ($payment_method == 'bank')
                            <div class="row mb-2">
                                <div class="col-md-4 fw-semibold">اسم البنك</div>
                                <div class="col-md-8">
                                    <input type="text" wire:model="bank_name" class="form-control form-control-sm" placeholder="اسم البنك">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 fw-semibold">تفاصيل التحويل</div>
                                <div class="col-md-8">
                                    <input type="text" wire:model="bank_details" class="form-control form-control-sm" placeholder="تفاصيل التحويل">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- الملخص -->
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="mdi mdi-calculator me-2"></i> {{ __('suppliers.invoice_summary') }}
                        </h6>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">{{ __('suppliers.subtotal') }}:</span>
                            <span class="fw-semibold">{{ $subtotal }} ج.م</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">{{ __('suppliers.discount') }}:</span>
                            <span class="fw-semibold text-danger">- {{ $discount }} ج.م</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">{{ __('suppliers.tax') }}:</span>
                            <span class="fw-semibold">{{ $tax }} ج.م</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 border-top pt-2">
                            <span class="fw-bold fs-6">{{ __('suppliers.total_amount') }}:</span>
                            <span class="fw-bold fs-6 ">{{ $total_amount }} ج.م</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">{{ __('suppliers.paid_amount') }}:</span>
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input disabled type="number" step="0.01" wire:model="paid_amount" wire:input="calculateTotals" class="form-control form-control-sm text-end">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">{{ __('suppliers.remaining_amount') }}:</span>
                            <span class="fw-semibold {{ $remaining_amount > 0 ? 'text-danger' : 'text-success' }}">{{ $remaining_amount }} ج.م</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-start">
                <button type="submit" class="btn btn-success btn-sm px-3">
                    <i class="mdi mdi-content-save-check me-1"></i> {{ __('suppliers.save_invoice') }}
                </button>
                <button type="button" class="btn btn-secondary btn-sm px-3 ms-2">
                    <i class="mdi mdi-cancel me-1"></i> إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function initSelect2() {
        $('.select2').select2({
            placeholder: $(this).data('placeholder') ?? "اختر...",
            allowClear: true,
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

        Livewire.hook('message.processed', (message, component) => {
            initSelect2();
        });
    });
</script>
