<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="mdi mdi-link-variant me-2"></i> {{ __('bank_trans.gl_integration_title') }}
            </h5>
            <input type="text" class="form-control w-25" placeholder="{{ __('bank_trans.search') }}" wire:model="search">
        </div>

        <div class="card-body">
            {{-- تنبيه --}}
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- النموذج --}}
            <form wire:submit.prevent="save" class="row g-3 mb-4">
                <div class="col-md-5">
                    <label class="form-label fw-bold">{{ __('bank_trans.bank_account') }}</label>
                    <select wire:model="account_id" class="form-select" @if($editMode) disabled @endif>
                        <option value="">{{ __('bank_trans.choose_bank_account') }}</option>
                        @foreach ($bankAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->account_name }} - {{ $acc->account_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-bold">{{ __('bank_trans.gl_account') }}</label>
                    <select wire:model="gl_account_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_gl_account') }}</option>
                        @foreach ($glAccounts as $gl)
                            <option value="{{ $gl->id }}">{{ $gl->name_ar }} ({{ $gl->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 text-end align-self-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-3-fill"></i> {{ $editMode ? __('bank_trans.update') : __('bank_trans.save') }}
                    </button>
                </div>
            </form>

            {{-- جدول --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('bank_trans.bank_account') }}</th>
                            <th>{{ __('bank_trans.gl_account') }}</th>
                            <th>{{ __('bank_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bankAccounts as $acc)
                            <tr>
                                <td>{{ $acc->id }}</td>
                                <td>{{ $acc->account_name }} - {{ $acc->account_number }}</td>
                                <td>{{ $acc->glAccount?->name_ar ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" wire:click="edit({{ $acc->id }})">
                                        <i class="ri-edit-2-fill"></i> {{ __('bank_trans.edit') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('bank_trans.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $bankAccounts->links() }}
        </div>
    </div>
</div>
