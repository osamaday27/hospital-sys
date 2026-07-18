{{-- resources/views/livewire/surgical/surgery-supply-form.blade.php --}}
<div class="container-fluid">
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="color:#fff">
                <i class="mdi mdi-needle me-2"></i> {{ trans('surgical_trans.supply_issue_title') }}
            </h5>
            <span class="badge bg-light text-dark">
                <i class="mdi mdi-counter me-1"></i>
                {{ count($cart) }} {{ trans('surgical_trans.items') }}
            </span>
        </div>

        <div class="card-body">
            {{-- اختيار المخزن + البحث --}}
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-warehouse me-1"></i> {{ trans('surgical_trans.select_store') }}
                    </label>
                    <select class="form-select" wire:model="store_id">
                        <option value="">{{ trans('surgical_trans.choose') }}...</option>
                        @foreach ($stores as $st)
                            <option value="{{ $st['id'] }}">
                                {{ app()->getLocale() === 'ar' ? $st['name_ar'] : $st['name_en'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('store_id') <small class="text-danger">{{ $message }}</small> @enderror
                    <small class="text-muted d-block mt-1">
                        {{ __('سيتم تحميل المستلزمات الافتراضية المرتبطة بالعملية تلقائيًا بعد اختيار المخزن.') }}
                    </small>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-magnify me-1"></i> {{ trans('surgical_trans.search_product') }}
                    </label>
                    <input type="text" class="form-control" placeholder="{{ trans('surgical_trans.search_placeholder') }}" wire:model.debounce.400ms="search">
                </div>
            </div>

            {{-- ========== جديد: جدول المستلزمات الافتراضية قبل الإضافة للسلة ==========
                 تقدر تعدّل الوحدة والكمية، وتضيف محدد أو الكل للسلة --}}
            @if(!empty($default_items))
                <div class="border rounded p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="mdi mdi-format-list-bulleted me-1"></i> {{ __('المستلزمات الافتراضية للعملية') }}
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleDefaultSelectAll">
                                <i class="mdi mdi-select-all me-1"></i>{{ $default_select_all ? __('إلغاء تحديد الكل') : __('تحديد الكل') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-success" wire:click="addAllDefaultsToCart">
                                <i class="mdi mdi-cart-arrow-down me-1"></i>{{ __('إضافة المحدد إلى السلة') }}
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:48px">{{ __('اختر') }}</th>
                                    <th style="width:120px">{{ trans('surgical_trans.code') }}</th>
                                    <th>{{ trans('surgical_trans.product_name') }}</th>
                                    <th style="width:180px">{{ trans('surgical_trans.unit') }}</th>
                                    <th style="width:140px">{{ trans('surgical_trans.quantity') }}</th>
                                    <th style="width:220px">{{ trans('surgical_trans.balance_now') }}</th>
                                    <th style="width:120px">{{ trans('surgical_trans.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($default_items as $idx => $r)
                                    @php
                                        $lack = (float)$r['quantity'] > (float)$r['current_balance'];
                                    @endphp
                                    <tr class="{{ $lack ? 'table-danger' : '' }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input" wire:model="default_items.{{ $idx }}.checked">
                                        </td>
                                        <td>{{ $r['system_code'] }}</td>
                                        <td>{{ app()->getLocale()==='ar' ? $r['name_ar'] : $r['name_en'] }}</td>
                                        <td>
                                            <select class="form-select form-select-sm"
                                                    wire:change="changeDefaultUnit({{ $idx }}, $event.target.value)">
                                                @foreach($r['units'] as $u)
                                                    <option value="{{ $u['id'] }}" @selected($u['id']==$r['unit_id'])>{{ $u['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" min="0.0001" step="0.0001" class="form-control form-control-sm"
                                                   wire:input="changeDefaultQty({{ $idx }}, $event.target.value)"
                                                   value="{{ $r['quantity'] }}">
                                        </td>
                                        <td>
                                            <div>{{ rtrim(rtrim(number_format($r['current_balance'], 4, '.', ''), '0'), '.') }}</div>
                                            @if(!empty($r['current_balance_formatted']))
                                                <small class="text-muted">{{ $r['current_balance_formatted'] }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    wire:click="addDefaultRowToCart({{ $idx }})">
                                                <i class="mdi mdi-cart-plus"></i> {{ __('أضف للسلة') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-success" wire:click="addAllDefaultsToCart">
                            <i class="mdi mdi-cart-arrow-down me-1"></i>{{ __('إضافة المحدد إلى السلة') }}
                        </button>
                    </div>
                </div>
            @endif
            {{-- ================== نهاية الافتراضي ================== --}}

            {{-- قائمة المنتجات المتاحة --}}
            <div class="mb-3">
                <div class="table-responsive" style="max-height: 230px; overflow-y:auto;">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ trans('surgical_trans.code') }}</th>
                                <th>{{ trans('surgical_trans.product_name') }}</th>
                                <th style="width:120px">{{ trans('surgical_trans.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($loadingProducts)
                            <tr>
                                <td colspan="3">
                                    <div class="d-flex align-items-center">
                                        <div class="spinner-border spinner-border-sm me-2"></div>
                                        <span>{{ trans('surgical_trans.loading') }}...</span>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @forelse($available_products as $p)
                                <tr>
                                    <td>{{ $p['system_code'] }}</td>
                                    <td>{{ app()->getLocale() === 'ar' ? $p['name_ar'] : $p['name_en'] }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"
                                                wire:click="selectProduct({{ (int)$p['id'] }})">
                                            <i class="mdi mdi-check"></i> {{ trans('surgical_trans.select') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        {{ trans('surgical_trans.no_products_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- اختيار الوحدة والكمية للبحث اليدوي --}}
            @if($selected_product)
                <div class="border rounded p-3 mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-pill me-1"></i> {{ trans('surgical_trans.selected_product') }}
                            </label>
                            <input type="text" class="form-control"
                                   value="{{ $selected_product['system_code'] }} - {{ app()->getLocale() === 'ar' ? $selected_product['name_ar'] : $selected_product['name_en'] }}"
                                   readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-scale-balance me-1"></i> {{ trans('surgical_trans.unit') }}
                            </label>
                            <select class="form-select" wire:model="selected_unit_id">
                                <option value="">{{ trans('surgical_trans.choose') }}...</option>
                                @foreach ($selected_units as $u)
                                    <option value="{{ $u['id'] }}">{{ app()->getLocale() === 'ar' ? $u['name_ar'] : $u['name_en'] }}</option>
                                @endforeach
                            </select>
                            @error('selected_unit_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-counter me-1"></i> {{ trans('surgical_trans.quantity') }}
                            </label>
                            <input type="number" min="0.0001" step="0.0001" class="form-control" wire:model.lazy="quantity">
                            @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-database me-1"></i> {{ trans('surgical_trans.current_balance') }}
                            </label>
                            <input type="text" class="form-control"
                                   value="{{ rtrim(rtrim(number_format($selected_unit_balance, 4, '.', ''), '0'), '.') }} {{ $selected_unit_name ?: trans('surgical_trans.unit_short') }}"
                                   readonly>
                        </div>

                        <div class="col-12 mt-2">
                            <button class="btn btn-success" wire:click="addToCart">
                                <i class="mdi mdi-cart-plus me-1"></i> {{ trans('surgical_trans.add_to_cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- السلة --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 110px">{{ trans('surgical_trans.code') }}</th>
                            <th>{{ trans('surgical_trans.product_name') }}</th>
                            <th style="width: 140px">{{ trans('surgical_trans.unit') }}</th>
                            <th style="width: 140px">{{ trans('surgical_trans.quantity') }}</th>
                            <th style="width: 200px">{{ trans('surgical_trans.balance_now') }}</th>
                            <th style="width: 160px">{{ trans('surgical_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cart as $index => $row)
                            @php
                                $exceeds = (float)$row['quantity'] > (float)$row['current_balance'];
                            @endphp
                            <tr class="{{ $exceeds ? 'table-danger' : '' }}">
                                <td>{{ $row['system_code'] }}</td>
                                <td>{{ app()->getLocale() === 'ar' ? $row['product_name_ar'] : $row['product_name_en'] }}</td>
                                <td>{{ $row['unit_name_ar'] }}</td>
                                <td>{{ rtrim(rtrim(number_format($row['quantity'], 4, '.', ''), '0'), '.') }}</td>
                                <td>
                                    <div>{{ rtrim(rtrim(number_format($row['current_balance'], 4, '.', ''), '0'), '.') }}</div>
                                    @if(!empty($row['current_balance_formatted']))
                                        <small class="text-muted">{{ $row['current_balance_formatted'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="removeFromCart({{ $index }})">
                                        <i class="mdi mdi-delete-outline"></i> {{ trans('surgical_trans.remove') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    {{ trans('surgical_trans.cart_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ملاحظات + حفظ --}}
            <div class="row g-3 mt-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-note-text-outline me-1"></i> {{ trans('surgical_trans.notes') }}
                    </label>
                    <textarea class="form-control" rows="2" wire:model.defer="notes" placeholder="{{ trans('surgical_trans.notes_placeholder') }}"></textarea>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100" wire:click="save">
                        <i class="mdi mdi-content-save-outline me-1"></i> {{ trans('surgical_trans.save_issue') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- السجل السابق لصرف المستلزمات على هذه العملية --}}
    <div class="card mt-4">
        <div class="card-header bg-light d-flex align-items-center">
            <i class="mdi mdi-history me-2"></i>
            <strong>{{ __('سجل الصرف السابق لهذه العملية') }}</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:140px">{{ __('رقم الحركة') }}</th>
                            <th style="width:160px">{{ __('تاريخ الصرف') }}</th>
                            <th style="width:120px">{{ trans('surgical_trans.code') }}</th>
                            <th>{{ trans('surgical_trans.product_name') }}</th>
                            <th style="width:120px">{{ trans('surgical_trans.unit') }}</th>
                            <th style="width:120px">{{ __('الكمية (وحدة الطلب)') }}</th>
                            <th style="width:140px">{{ __('الكمية بالصغرى') }}</th>
                            <th style="width:120px">{{ __('باتش') }}</th>
                            <th style="width:120px">{{ __('انتهاء') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issued_items as $it)
                            <tr>
                                <td>{{ $it['movement_number'] }}</td>
                                <td>{{ $it['issue_date'] }}</td>
                                <td>{{ $it['product_code'] }}</td>
                                <td>{{ app()->getLocale() === 'ar' ? $it['product_name_ar'] : $it['product_name_en'] }}</td>
                                <td>{{ $it['unit_name'] }}</td>
                                <td>{{ $it['qty_unit'] }}</td>
                                <td>{{ $it['qty_base'] }}</td>
                                <td>{{ $it['batch_no'] }}</td>
                                <td>{{ $it['expiry_date'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">
                                    {{ __('لا يوجد صرف سابق مسجل لهذه العملية.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
  window.addEventListener('dbg', e => console.log('LW-DBG:', e.detail));
</script>
