<div class="container-fluid py-4" dir="rtl">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-bank fs-4"></i>
                <h5 class="mb-0">{{ __('bank_trans.title') }}</h5>
            </div>
            <button wire:click="resetInput" class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#bankModal">
                <i class="mdi mdi-plus-circle"></i> {{ __('bank_trans.add_bank') }}
            </button>
        </div>

        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle-outline me-2"></i>
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th><i class="mdi mdi-alphabetical-variant"></i> {{ __('bank_trans.name_ar') }}</th>
                            <th><i class="mdi mdi-alphabetical"></i> {{ __('bank_trans.name_en') }}</th>
                            <th><i class="mdi mdi-card-text-outline"></i> {{ __('bank_trans.account_number') }}</th>
                            <th><i class="mdi mdi-format-list-numbered"></i> {{ __('bank_trans.iban') }}</th>
                            <th><i class="mdi mdi-swap-horizontal-bold"></i> {{ __('bank_trans.swift') }}</th>
                            <th><i class="mdi mdi-currency-usd"></i> {{ __('bank_trans.currency') }}</th>
                            <th><i class="mdi mdi-cash-multiple"></i> {{ __('bank_trans.balance') }}</th>
                            <th><i class="mdi mdi-home-city-outline"></i> {{ __('bank_trans.branch') }}</th>
                            <th><i class="mdi mdi-check-decagram"></i> {{ __('bank_trans.status') }}</th>
                            <th><i class="mdi mdi-cog-outline"></i> {{ __('bank_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banks as $bank)
                            <tr>
                                <td>{{ $bank->name_ar }}</td>
                                <td>{{ $bank->name_en }}</td>
                                <td>{{ $bank->account_number }}</td>
                                <td>{{ $bank->iban }}</td>
                                <td>{{ $bank->swift_code }}</td>
                                <td>{{ $bank->currency }}</td>
                                <td><span class="badge bg-info">{{ number_format($bank->balance, 2) }}</span></td>
                                <td>{{ $bank->branch?->name }}</td>
                                <td>
                                    <span class="badge {{ $bank->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $bank->status == 'active' ? __('bank_trans.active') : __('bank_trans.inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $bank->id }})" data-bs-toggle="modal"
                                        data-bs-target="#bankModal" class="btn btn-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button wire:click="delete({{ $bank->id }})" class="btn btn-danger btn-sm">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    <i class="mdi mdi-database-off"></i> {{ __('bank_trans.no_data') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal لإضافة/تعديل بنك -->
    <div wire:ignore.self class="modal fade" id="bankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="mdi mdi-bank me-2"></i>
                        {{ $isEdit ? __('bank_trans.edit_bank') : __('bank_trans.add_bank') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-alphabetical-variant"></i> {{ __('bank_trans.name_ar') }}</label>
                            <input type="text" class="form-control" wire:model="name_ar">
                            @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-alphabetical"></i> {{ __('bank_trans.name_en') }}</label>
                            <input type="text" class="form-control" wire:model="name_en">
                            @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-card-text-outline"></i> {{ __('bank_trans.account_number') }}</label>
                            <input type="text" class="form-control" wire:model="account_number">
                            @error('account_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-format-list-numbered"></i> {{ __('bank_trans.iban') }}</label>
                            <input type="text" class="form-control" wire:model="iban">
                            @error('iban') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-swap-horizontal-bold"></i> {{ __('bank_trans.swift') }}</label>
                            <input type="text" class="form-control" wire:model="swift_code">
                            @error('swift_code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="mdi mdi-currency-usd"></i> {{ __('bank_trans.currency') }}</label>
                            <input type="text" class="form-control" wire:model="currency">
                            @error('currency') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="mdi mdi-cash-multiple"></i> {{ __('bank_trans.balance') }}</label>
                            <input type="number" class="form-control" wire:model="balance">
                            @error('balance') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-home-city-outline"></i> {{ __('bank_trans.branch') }}</label>
                            <select class="form-select" wire:model="branch_id">
                                <option value="">{{ __('bank_trans.branch') }}</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-check-decagram"></i> {{ __('bank_trans.status') }}</label>
                            <select class="form-select" wire:model="status">
                                <option value="active">{{ __('bank_trans.active') }}</option>
                                <option value="inactive">{{ __('bank_trans.inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i> {{ __('bank_trans.cancel') }}
                    </button>
                    <button type="button" wire:click="save" class="btn btn-primary">
                        <i class="mdi mdi-content-save"></i> {{ __('bank_trans.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('close-modal', () => {
        let modal = bootstrap.Modal.getInstance(document.getElementById('bankModal'));
        modal.hide();
    });
</script>
