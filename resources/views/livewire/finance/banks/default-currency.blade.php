<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-info text-white">
            <h5 class="mb-0"><i class="ri-money-dollar-circle-line me-2"></i> {{ __('bank_trans.default_currency_title') }}</h5>
        </div>

        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('bank_trans.default_currency') }}</label>
                    <select wire:model="default_currency_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_currency') }}</option>
                        @foreach ($currencies as $cur)
                            <option value="{{ $cur->id }}">{{ $cur->name_ar }} ({{ $cur->code }})</option>
                        @endforeach
                    </select>
                    @error('default_currency_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-3-fill"></i> {{ __('bank_trans.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
