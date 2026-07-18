<div class="card shadow border-0">
    <div class="card-header bg-gradient-primary text-white py-3">
        <h5 class="mb-0">الإعدادات العامة للعيادات الخارجية</h5>
    </div>
    <div class="card-body">
        @if(session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form wire:submit.prevent="save">
            <div class="mb-3">
                <label class="form-label">الحساب المحاسبي للعيادات الخارجية</label>
                <select wire:model="gl_account_id"   class="form-control @error('gl_account_id') is-invalid @enderror" >
                    <option value="">اختر الحساب</option>
                    @foreach($glAccounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->fullName() }}
                        </option>
                    @endforeach
                </select>
                @error('gl_account_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">حفظ</button>
        </form>
    </div>
</div>
