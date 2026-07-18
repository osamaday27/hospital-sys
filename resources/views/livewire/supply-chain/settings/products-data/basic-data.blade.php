<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show text-center mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h4>{{ trans('supply_chain_trans.product_data') }}</h4>

    <!-- السطر الأول: كود النظام للصنف - الاسم بالعربية - الاسم بالإنجليزية -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="system_code" class="form-label">{{ trans('supply_chain_trans.system_code') }}</label>
            <input
                type="text"
                id="system_code"
                class="form-control"
                wire:model="system_code"
                @if($item_coding_method !== 'manual') disabled @endif
                placeholder="{{ $item_coding_method === 'manual' ? trans('supply_chain_trans.enter_system_code') : '' }}"
            >
            @error('system_code') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-4">
            <label for="name_ar" class="form-label">{{ trans('general.name_ar') }}</label>
            <input type="text" id="name_ar" class="form-control" wire:model="name_ar">
            @error('name_ar') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-4">
            <label for="name_en" class="form-label">{{ trans('general.name_en') }}</label>
            <input type="text" id="name_en" class="form-control" wire:model="name_en">
            @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

 <!-- السطر الثاني: مجموعة الصنف - الحالة - الوصف -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="product_category" class="form-label">{{ trans('supply_chain_trans.product_category') }}</label>
            <select id="product_category" class="form-select" wire:model="product_category_id">
                <option value="">{{ trans('supply_chain_trans.select_product_category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name_ar }}</option>
                @endforeach
            </select>
            @error('product_category_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4">
            <label for="status" class="form-label">{{ trans('general.status') }}</label>
            <select id="productdatastatus" class="form-select" wire:model="status">
                <option value="">{{ trans('supply_chain_trans.select_status') }}</option>
                <option value="active">{{ trans('supply_chain_trans.active') }}</option>
                <option value="inactive">{{ trans('supply_chain_trans.inactive') }}</option>
            </select>
            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4">
            <label for="description" class="form-label">{{ trans('supply_chain_trans.description') }}</label>
            <input type="text" id="description" class="form-control" wire:model="description">
            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- السطر الثالث: الباركود - كود المنتج - الكود الموحد -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="barcode" class="form-label">{{ trans('supply_chain_trans.barcode') }}</label>
            <input type="text" id="barcode" class="form-control" wire:model="barcode">
            @error('barcode') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4">
            <label for="product_code" class="form-label">{{ trans('supply_chain_trans.product_code') }}</label>
            <input type="text" id="product_code" class="form-control" wire:model="product_code">
            @error('product_code') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4">
            <label for="unified_code" class="form-label">{{ trans('supply_chain_trans.unified_code') }}</label>
            <input type="text" id="unified_code" class="form-control" wire:model="unified_code">
            @error('unified_code') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- السطر الرابع: هامش الربح - نوع الصنف - الرقم الدفتري -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="profit_margin" class="form-label">{{ trans('supply_chain_trans.profit_margin') }}</label>
            <input type="number" step="0.01" id="profit_margin" class="form-control" wire:model="profit_margin">
            @error('profit_margin') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4">
            <label for="product_type" class="form-label">{{ trans('supply_chain_trans.product_type') }}</label>
            <select id="product_type" class="form-select" wire:model="product_type">
                <option value="">{{ trans('supply_chain_trans.select_product_type') }}</option>
                <option value="local">{{ trans('supply_chain_trans.local') }}</option>
                <option value="imported">{{ trans('supply_chain_trans.imported') }}</option>
            </select>
            @error('product_type') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4">
            <label for="ledger_number" class="form-label">{{ trans('supply_chain_trans.ledger_number') }}</label>
            <input type="text" id="ledger_number" class="form-control" wire:model="ledger_number">
            @error('ledger_number') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- السطر الخامس: Checkbox لضريبة القيمة المضافة - حقل إدخال الضريبة (معروض عند الاختيار) - تاريخ انتهاء الصلاحية -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="form-check">
                <input type="checkbox" id="vat" class="form-check-input" wire:model="vat">
                <label for="vat" class="form-check-label">{{ trans('supply_chain_trans.vat') }}</label>
            </div>
        </div>
        <div class="col-md-4">
            @if($vat)
                <label for="vat_value" class="form-label">{{ trans('supply_chain_trans.vat_value') }}</label>
                <input type="number" step="0.01" id="vat_value" class="form-control" wire:model="vat_value">
                @error('vat_value') <span class="text-danger">{{ $message }}</span> @enderror
            @endif
        </div>
        <div class="col-md-4">
            <label for="expiration_date" class="form-label">{{ trans('supply_chain_trans.expiration_date') }}</label>
            <input type="date" id="expiration_date" class="form-control" wire:model="expiration_date">
            @error('expiration_date') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>
<!-- السطر الخامس: تحديد إذا كان الصنف حرج -->
<div class="row mb-3">
  <div class="col-md-4">
    <div class="form-check form-switch mt-4">
      <input
        type="checkbox"
        id="is_critical"
        class="form-check-input"
        wire:model="is_critical"
      >
      <label for="is_critical" class="form-check-label">صنف حرج</label>
    </div>
    @error('is_critical') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

   <!-- قسم رفع الصور -->
   <div class="row mb-3">
    <div class="col-12">
        <h5>{{ trans('supply_chain_trans.product_images') }}</h5>
        <input type="file" wire:model="images" multiple class="form-control">
        @error('images.*') <span class="text-danger">{{ $message }}</span> @enderror

        @if($images)
            <div class="mt-2 d-flex gap-2 flex-wrap">
                @foreach($images as $image)
                    <img src="{{ $image->temporaryUrl() }}" width="100" class="img-thumbnail">
                @endforeach
            </div>
        @endif

        @if($existingImages)
            <div class="mt-4">
                <h6>{{ trans('supply_chain_trans.saved_images') }}</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($existingImages as $index => $img)
                        <div class="position-relative">
                            <img src="{{ asset('attachments/' . $img) }}" width="100" class="img-thumbnail">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                    wire:click="deleteExistingImage({{ $index }})" title="{{ trans('general.delete') }}">
                                &times;
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- أزرار الحفظ والتنقل -->

    <div class="d-flex gap-2">
        <button class="btn btn-primary" wire:click="saveData">
            {{ $productId ? trans('general.update') : trans('general.save') }}
        </button>

        @if($productId)
            <button class="btn btn-outline-info" wire:click="$emit('goToTab', 'tab2')">
                {{ trans('supply_chain_trans.goto_product_units') }} →
            </button>
        @endif
    </div>
</div>
