<div class="p-3">
    <h5 class="mb-3">{{ __('supply_chain_trans.store_products_management') }}</h5>

    {{-- Select2 assets --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    <div class="row g-3 align-items-end mb-4">
        {{-- اختيار منتج مفرد --}}
        <div class="col-md-4">
            <label class="form-label">{{ __('supply_chain_trans.select_product') }}</label>
            <div wire:ignore>
                <select id="productSelect" class="form-select" data-placeholder="{{ __('supply_chain_trans.select_product') }}...">
                    <option value="">{{ __('supply_chain_trans.choose') }}</option>
                    @foreach (($products ?? []) as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name_ar }} / {{ $product->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('product_id')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        {{-- اختيار مجموعة وإضافة كل أصنافها --}}
        <div class="col-md-4">
            <label class="form-label">{{ __('supply_chain_trans.select_category') }}</label>
            <div wire:ignore>
                <select id="categorySelect" class="form-select" data-placeholder="{{ __('supply_chain_trans.select_category') }}...">
                    <option value="">{{ __('supply_chain_trans.choose') }}</option>
                    @foreach (($categories ?? []) as $cat)
                        <option value="{{ $cat->id }}">
                            {{ $cat->name_ar }} / {{ $cat->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('category_id')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        {{-- حالة الإدراج --}}
        <div class="col-md-2">
            <label class="form-label">{{ __('supply_chain_trans.status') }}</label>
            <select class="form-select" wire:model.defer="status">
                <option value="active">{{ __('supply_chain_trans.active') }}</option>
                <option value="inactive">{{ __('supply_chain_trans.inactive') }}</option>
            </select>
        </div>

        {{-- أزرار الإضافة --}}
        <div class="col-md-2 d-grid gap-2">
            <button class="btn btn-primary" wire:click="addProduct">
                + {{ __('supply_chain_trans.add') }}
            </button>
            <button class="btn btn-success" wire:click="addCategoryProducts">
                + {{ __('supply_chain_trans.add_category_products') }}
            </button>
            @error('storeId')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="alert alert-info py-2">
        <i class="ri-information-line"></i>
        {{ __('supply_chain_trans.bulk_add_hint', ['note' => __('supply_chain_trans.duplicates_will_be_skipped')]) }}
    </div>

    {{-- ✅ شريط البحث في الجدول --}}
    <div class="row align-items-center g-2 mb-3">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text"><i class="ri-search-line"></i></span>
                <input
                    type="text"
                    class="form-control"
                    wire:model.debounce.400ms="search"
                    wire:keydown.escape="$set('search','')"
                    placeholder="ابحث بالاسم / الكود / الباركود / المجموعة ..."
                >
                @if($search)
                    <button class="btn btn-light" type="button" wire:click="$set('search','')">
                        {{ __('supply_chain_trans.clear') ?? 'مسح' }}
                    </button>
                @endif
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-secondary">
                {{ __('supply_chain_trans.results') ?? 'النتائج' }}: {{ $storeProducts->total() }}
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>{{ __('supply_chain_trans.product_code') }}</th>
                    <th>{{ __('supply_chain_trans.barcode') }}</th>
                    <th>{{ __('supply_chain_trans.unified_code') }}</th>
                    <th>{{ __('supply_chain_trans.product_name') }}</th>
                    <th>{{ __('supply_chain_trans.product_group') }}</th>

                    <th class="text-center">
                        <div>{{ __('supply_chain_trans.order_min') }}</div>
                        <small class="text-muted">{{ __('supply_chain_trans.qty_and_unit') }}</small>
                    </th>
                    <th class="text-center">
                        <div>{{ __('supply_chain_trans.order_max') }}</div>
                        <small class="text-muted">{{ __('supply_chain_trans.qty_and_unit') }}</small>
                    </th>
                    <th class="text-center">
                        <div>{{ __('supply_chain_trans.reorder_point') }}</div>
                        <small class="text-muted">{{ __('supply_chain_trans.qty_and_unit') }}</small>
                    </th>

                    <th>{{ __('supply_chain_trans.status') }}</th>
                    <th>{{ __('supply_chain_trans.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($storeProducts as $row)
                    <tr wire:key="row-{{ $row->id }}">
                        <td>{{ $row->product?->product_code ?? '-' }}</td>
                        <td>{{ $row->product?->barcode ?? '-' }}</td>
                        <td>{{ $row->product?->unified_code ?? '-' }}</td>
                        <td>{{ $row->product?->name_ar ?? '-' }} / {{ $row->product?->name_en ?? '-' }}</td>
                        <td>
                            {{ $row->product?->category?->name_ar ?? '-' }}
                            /
                            {{ $row->product?->category?->name_en ?? '-' }}
                        </td>

                        {{-- Min --}}
                        <td style="min-width: 240px;">
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <input type="number" min="0" step="0.000001"
                                           class="form-control form-control-sm"
                                           wire:model.lazy="rows.{{ $row->id }}.order_min_qty"
                                           wire:change="saveInline({{ $row->id }}, 'order_min_qty')"
                                           wire:loading.attr="disabled"
                                           wire:target="saveInline"
                                           placeholder="{{ __('supply_chain_trans.quantity') }}">
                                    @error('rows.'.$row->id.'.order_min_qty')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex-grow-1">
                                    <select class="form-select form-select-sm"
                                            wire:model.lazy="rows.{{ $row->id }}.order_min_unit_id"
                                            wire:change="saveInline({{ $row->id }}, 'order_min_unit_id')"
                                            wire:loading.attr="disabled"
                                            wire:target="saveInline">
                                        <option value="">{{ __('supply_chain_trans.select_unit') }}</option>
                                        @foreach(($unitsByProduct[$row->product_id] ?? []) as $u)
                                            <option value="{{ $u['id'] }}">
                                                {{ $u['name_ar'] }} / {{ $u['name_en'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rows.'.$row->id.'.order_min_unit_id')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>

                        {{-- Max --}}
                        <td style="min-width: 240px;">
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <input type="number" min="0" step="0.000001"
                                           class="form-control form-control-sm"
                                           wire:model.lazy="rows.{{ $row->id }}.order_max_qty"
                                           wire:change="saveInline({{ $row->id }}, 'order_max_qty')"
                                           wire:loading.attr="disabled"
                                           wire:target="saveInline"
                                           placeholder="{{ __('supply_chain_trans.quantity') }}">
                                    @error('rows.'.$row->id.'.order_max_qty')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex-grow-1">
                                    <select class="form-select form-select-sm"
                                            wire:model.lazy="rows.{{ $row->id }}.order_max_unit_id"
                                            wire:change="saveInline({{ $row->id }}, 'order_max_unit_id')"
                                            wire:loading.attr="disabled"
                                            wire:target="saveInline">
                                        <option value="">{{ __('supply_chain_trans.select_unit') }}</option>
                                        @foreach(($unitsByProduct[$row->product_id] ?? []) as $u)
                                            <option value="{{ $u['id'] }}">
                                                {{ $u['name_ar'] }} / {{ $u['name_en'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rows.'.$row->id.'.order_max_unit_id')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>

                        {{-- Reorder --}}
                        <td style="min-width: 240px;">
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <input type="number" min="0" step="0.000001"
                                           class="form-control form-control-sm"
                                           wire:model.lazy="rows.{{ $row->id }}.reorder_point_qty"
                                           wire:change="saveInline({{ $row->id }}, 'reorder_point_qty')"
                                           wire:loading.attr="disabled"
                                           wire:target="saveInline"
                                           placeholder="{{ __('supply_chain_trans.quantity') }}">
                                    @error('rows.'.$row->id.'.reorder_point_qty')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex-grow-1">
                                    <select class="form-select form-select-sm"
                                            wire:model.lazy="rows.{{ $row->id }}.reorder_point_unit_id"
                                            wire:change="saveInline({{ $row->id }}, 'reorder_point_unit_id')"
                                            wire:loading.attr="disabled"
                                            wire:target="saveInline">
                                        <option value="">{{ __('supply_chain_trans.select_unit') }}</option>
                                        @foreach(($unitsByProduct[$row->product_id] ?? []) as $u)
                                            <option value="{{ $u['id'] }}">
                                                {{ $u['name_ar'] }} / {{ $u['name_en'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rows.'.$row->id.'.reorder_point_unit_id')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge {{ $row->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $row->status == 'active' ? __('supply_chain_trans.active') : __('supply_chain_trans.inactive') }}
                            </span>
                            <button class="btn btn-sm btn-outline-info ms-1" wire:click="toggleStatus({{ $row->id }})">
                                <i class="ri-refresh-line"></i> {{ __('supply_chain_trans.toggle_status') }}
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $row->id }})">
                                <i class="ri-delete-bin-line"></i> {{ __('supply_chain_trans.delete') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">{{ __('supply_chain_trans.no_products') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- روابط الترقيم --}}
    <div class="mt-3">
        {{ $storeProducts->links() }}
    </div>

    {{-- مودال الحذف --}}
    <div wire:ignore.self class="modal fade" id="deleteModal1" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                        colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <h4 class="mt-3">{{ __('call_center_trans.h4_delete') }}</h4>
                    <p>{{ __('call_center_trans.p_delete') }}</p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('call_center_trans.cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="delete">
                            {{ __('call_center_trans.yes_delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="liveToast" class="toast align-items-center position-fixed top-0 start-50 translate-middle-x mt-3 z-3" role="alert" aria-live="assertive" aria-atomic="true" style="min-width:320px;">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    {{-- Select2 + Toast handlers --}}
    <script>
        document.addEventListener('livewire:load', function () {
            function initSelect2() {
                const $prod = $('#productSelect');
                const $cat  = $('#categorySelect');

                if (!$.fn.select2) return;

                if ($prod.hasClass('select2-hidden-accessible')) $prod.select2('destroy');
                $prod.select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl',
                    width: '100%',
                    minimumResultsForSearch: 0,
                    placeholder: $prod.data('placeholder') || "{{ __('supply_chain_trans.select_product') }}...",
                    dropdownParent: $('body')
                }).off('change').on('change', function () {
                    @this.set('product_id', $(this).val());
                });

                if ($cat.hasClass('select2-hidden-accessible')) $cat.select2('destroy');
                $cat.select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl',
                    width: '100%',
                    minimumResultsForSearch: 0,
                    placeholder: $cat.data('placeholder') || "{{ __('supply_chain_trans.select_category') }}...",
                    dropdownParent: $('body')
                }).off('change').on('change', function () {
                    @this.set('category_id', $(this).val());
                });
            }

            initSelect2();
            Livewire.hook('message.processed', initSelect2);

            window.addEventListener('show-delete-modal1', () => {
                new bootstrap.Modal(document.getElementById('deleteModal1')).show();
            });
            window.addEventListener('hide-delete-modal', () => {
                const modalEl = document.getElementById('deleteModal1');
                const inst = bootstrap.Modal.getInstance(modalEl);
                if (inst) inst.hide();
            });

            window.addEventListener('toast', (e) => {
                const { message = '', classes = '' } = (e.detail || {});
                const el = document.getElementById('liveToast');
                const body = el.querySelector('.toast-body');
                el.className = 'toast align-items-center position-fixed top-0 start-50 translate-middle-x mt-3 z-3';
                if (classes) el.className += ' ' + classes;
                body.textContent = message;
                new bootstrap.Toast(el, { delay: 2500 }).show();
            });
        });
    </script>
</div>
