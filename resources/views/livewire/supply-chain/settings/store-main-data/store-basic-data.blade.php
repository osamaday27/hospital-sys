<div class="p-3">
    <h5 class="mb-3">{{ __('supply_chain_trans.store_maindata') }}</h5>

    <form wire:submit.prevent="save">
        <div class="row g-3">
            <!-- الكود اليدوي، الاسم بالعربي، الاسم بالإنجليزي -->
            @foreach ([
                ['manual_code', __('supply_chain_trans.manual_code')],
                ['name_ar', __('supply_chain_trans.name_ar')],
                ['name_en', __('supply_chain_trans.name_en')]
            ] as [$field, $label])
                <div class="col-md-4">
                    <label class="form-label">{{ $label }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model.defer="{{ $field }}">
                </div>
            @endforeach

            <!-- الحالة، نوع المخزن، الفرع -->
            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.status') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="status">
                    <option value="active">{{ __('supply_chain_trans.active') }}</option>
                    <option value="inactive">{{ __('supply_chain_trans.inactive') }}</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.store_type') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="store_type">
                    <option value="main">{{ __('supply_chain_trans.main') }}</option>
                    <option value="sub">{{ __('supply_chain_trans.sub') }}</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.branch') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="branch_id">
                    <option value="">{{ __('supply_chain_trans.select_branch') }}</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- العنوان -->
            @foreach ([
                ['address_ar', __('supply_chain_trans.address_ar')],
                ['address_en', __('supply_chain_trans.address_en')],
            ] as [$field, $label])
                <div class="col-md-6">
                    <label class="form-label">{{ $label }}</label>
                    <input type="text" class="form-control" wire:model.defer="{{ $field }}">
                </div>
            @endforeach

            <!-- وسيلة الاتصال -->
            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.contact_type') }}</label>
                <select class="form-select" wire:model="contact_type">
                    @foreach(['email', 'phone', 'fax', 'landline'] as $type)
                        <option value="{{ $type }}">{{ __('supply_chain_trans.' . $type) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.contact_value') }}</label>
                <input type="text" class="form-control" wire:model="contact_value">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100" wire:click="addContactMethod">
                    + {{ __('supply_chain_trans.add_contact_method') }}
                </button>
            </div>

            <!-- عرض وسائل الاتصال -->
            <div class="col-12">
                <div class="row g-2">
                    @foreach($contact_methods as $index => $method)
                        @if (isset($method['type'], $method['value']))
                            <div class="col-md-4">
                                <div class="bg-info text-white rounded px-3 py-2 d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <strong>{{ ucfirst($method['type']) }}:</strong> {{ $method['value'] }}
                                    </div>
                                    <button type="button" wire:click="removeContactMethod({{ $index }})" class="btn-close btn-close-white ms-2"></button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- الحقول الرقمية -->
            @foreach ([
                ['profit_margin', __('supply_chain_trans.profit_margin%')],
                ['daily_avg_consumption', __('supply_chain_trans.daily_avg_consumption')],
                ['min_order_quantity', __('supply_chain_trans.min_order_quantity')],
                ['max_order_quantity', __('supply_chain_trans.max_order_quantity')],
                ['opening_balance', __('supply_chain_trans.opening_balance')],
            ] as [$field, $label])
                <div class="col-md-4">
                    <label class="form-label">{{ $label }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model.defer="{{ $field }}">
                </div>
            @endforeach

            <!-- طريقة التسعير -->
            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.pricing_method') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="pricing_method">
                    <option value="cost">{{ __('supply_chain_trans.cost') }}</option>
                    <option value="selling">{{ __('supply_chain_trans.selling') }}</option>
                </select>
            </div>

            <!-- Checkboxes -->
            <div class="col-md-4 form-check form-switch mt-4">
                <input class="form-check-input" type="checkbox" wire:model="is_virtual" id="is_virtual">
                <label class="form-check-label" for="is_virtual">{{ __('supply_chain_trans.is_virtual') }}</label>
            </div>
            <div class="col-md-4 form-check form-switch mt-4">
                <input class="form-check-input" type="checkbox" wire:model="is_inventory_allowed" id="is_inventory_allowed">
                <label class="form-check-label" for="is_inventory_allowed">{{ __('supply_chain_trans.is_inventory_allowed') }}</label>
            </div>

            <!-- زر الحفظ -->
            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="ri-save-line"></i> {{ __('supply_chain_trans.save') }}
                </button>
            </div>
        </div>
    </form>

    <!-- سكربت التنبيه -->
    <script>
        window.addEventListener('store-saved', () => {
            Swal.fire({
                icon: 'success',
                title: '{{ __('supply_chain_trans.store_saved_successfully') }}',
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('goToTab', (tabName, storeId) => {
            const tabButton = document.querySelector(`[data-bs-target="#${tabName}"]`);
            if (tabButton) {
                tabButton.click();
            }
            Livewire.emitTo('supply-chain.settings.store-main-data.store-keeper', 'setStoreId', storeId);
        });
    </script>
</div>
