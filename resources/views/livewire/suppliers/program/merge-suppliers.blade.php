<div dir="rtl">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white">
            <h5 class="text-white"><i class="mdi mdi-merge"></i> {{ __('suppliers.merge_suppliers') }}</h5>
        </div>
        <div class="card-body">
            @if(session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('suppliers.main_supplier') }}</label>
                    <select wire:model="mainSupplier" class="form-select">
                        <option value="">{{ __('suppliers.choose_main_supplier') }}</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name_ar }} - {{ $sup->code }}</option>
                        @endforeach
                    </select>
                    @error('mainSupplier') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('suppliers.duplicate_supplier') }}</label>
                    <select wire:model="mergedSupplier" class="form-select">
                        <option value="">{{ __('suppliers.choose_duplicate_supplier') }}</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name_ar }} - {{ $sup->code }}</option>
                        @endforeach
                    </select>
                    @error('mergedSupplier') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button wire:click="merge" class="btn btn-danger">
                    <i class="mdi mdi-merge"></i> {{ __('suppliers.execute_merge') }}
                </button>

                <a href="suppliers_mergelog" class="btn btn-secondary">
                    <i class="mdi mdi-history"></i> {{ __('suppliers.merge_history') }}
                </a>
            </div>
        </div>
    </div>
</div>
