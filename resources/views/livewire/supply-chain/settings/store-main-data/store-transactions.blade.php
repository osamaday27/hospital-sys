<div class="p-4">
    {{-- رسالة النجاح --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h5 class="mb-3">{{ __('supply_chain_trans.store_transactions_settings') }}</h5>

    {{-- تحديد الكل --}}
    <div class="form-check mb-2">
        <input type="checkbox" class="form-check-input" wire:model="selectAll" id="selectAll">
        <label class="form-check-label" for="selectAll">
            {{ __('supply_chain_trans.select_all') }}
        </label>
    </div>

    {{-- الحركات المخزنية --}}
    @foreach($transactions as $key => $value)
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" wire:model="transactions.{{ $key }}" id="{{ $key }}">
            <label class="form-check-label" for="{{ $key }}">
                {{ $this->getArabicLabel($key) }}
            </label>
        </div>
    @endforeach

    {{-- زر الحفظ --}}
    <button class="btn btn-primary mt-3" wire:click="save">
        {{ __('supply_chain_trans.save') }}
    </button>
    @error('storeId')
    <span class="text-danger small">{{ $message }}</span>
@enderror
   
</div>
