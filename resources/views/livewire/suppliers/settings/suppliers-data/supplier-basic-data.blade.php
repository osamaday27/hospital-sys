<div class="p-3">
    <h5 class="mb-3">{{ __('suppliers.supplier_basic_data') }}</h5>

    <form wire:submit.prevent="save">
        <div class="row g-3">

            <!-- الكود اليدوي -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.code') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model.defer="code">
                @error('code') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- مجموعة الموردين -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.suppliers_group') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="suppliersgroup_id">
                    <option value="">{{ __('suppliers.select_suppliers_group') }}</option>
                    @foreach($supplierGroups as $g)
                        <option value="{{ $g->id }}">{{ $g->name_ar }} / {{ $g->name_en }}</option>
                    @endforeach
                </select>
                @error('suppliersgroup_id') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- الاسم بالعربية -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.name_ar') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model.defer="name_ar">
                @error('name_ar') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- الاسم بالإنجليزية -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.name_en') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model.defer="name_en">
                @error('name_en') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- الفرع -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.branch') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="branch_id">
                    <option value="">{{ __('suppliers.select_branch') }}</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
                @error('branch_id') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- نوع المورد -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.supplier_type') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="supplier_type">
                    <option value="service">{{ __('suppliers.service_supplier') }}</option>
                    <option value="goods">{{ __('suppliers.goods_supplier') }}</option>
                    <option value="both">{{ __('suppliers.both_supplier') }}</option>
                </select>
                @error('supplier_type') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- شركة المورد -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.supplier_company') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="suppliercompany_id">
                    <option value="">{{ __('suppliers.select_supplier_company') }}</option>
                    @foreach($supplierCompanies as $c)
                        <option value="{{ $c->id }}">{{ $c->name_ar }} / {{ $c->name_en }}</option>
                    @endforeach
                </select>
                @error('suppliercompany_id') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- رقم البطاقة الضريبية -->
            <div class="col-md-6">
                <label class="form-label">{{ __('suppliers.tax_card_number') }}</label>
                <input type="text" class="form-control" wire:model.defer="tax_card_number">
            </div>

            <!-- رقم السجل التجاري -->
            <div class="col-md-6">
                <label class="form-label">{{ __('suppliers.commercial_registration_number') }}</label>
                <input type="text" class="form-control" wire:model.defer="commercial_registration_number">
            </div>

            <!-- مجال الشركة -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.company_field') }}</label>
                <input type="text" class="form-control" wire:model.defer="company_field">
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.country') }}</label>
                {{-- هنا غير defer إلى live --}}
                <select class="form-select" wire:model="country_id">
                  <option value="">{{ __('suppliers.select_country') }}</option>
                  @foreach($countries as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                  @endforeach
                </select>
                @error('country_id') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.government') }}</label>
                <select class="form-select" wire:model="government_id">
                  <option value="">{{ __('suppliers.select_government') }}</option>
                  @foreach($governments as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                  @endforeach
                </select>
                @error('government_id') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.city') }}</label>
                <select class="form-select" wire:model="city_id">
                  <option value="">{{ __('suppliers.select_city') }}</option>
                  @foreach($cities as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                  @endforeach
                </select>
                @error('city_id') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>


            <!-- وسيلة الاتصال -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.contact_type') }}</label>
                <select class="form-select" wire:model="contact_type">
                    @foreach(['email','phone','fax','landline'] as $type)
                        <option value="{{ $type }}">{{ __('suppliers.'.$type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.contact_value') }}</label>
                <input type="text" class="form-control" wire:model="contact_value">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100" wire:click="addContactMethod">
                    + {{ __('suppliers.add_contact_method') }}
                </button>
            </div>

            <!-- عرض وسائل الاتصال المُضافة -->
            <div class="col-12">
                <div class="row g-2">
                    @foreach($contact_methods as $i => $m)
                        @if(isset($m['type'],$m['value']))
                            <div class="col-md-4">
                                <div class="bg-info text-white rounded px-3 py-2 d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <strong>{{ ucfirst($m['type']) }}:</strong> {{ $m['value'] }}
                                    </div>
                                    <button type="button" wire:click="removeContactMethod({{ $i }})" class="btn-close btn-close-white ms-2"></button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- الحالة -->
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.status') }} <span class="text-danger">*</span></label>
                <select class="form-select" wire:model.defer="status">
                    <option value="active">{{ __('suppliers.active') }}</option>
                    <option value="inactive">{{ __('suppliers.inactive') }}</option>
                </select>
                @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- زر الحفظ -->
            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="ri-save-line"></i> {{ __('suppliers.save') }}
                </button>
            </div>

        </div>
    </form>

    <script>
        window.addEventListener('supplier-saved', () => {
            Swal.fire({
                icon: 'success',
                title: '{{ __('suppliers.supplier_saved_successfully') }}',
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>
</div>
