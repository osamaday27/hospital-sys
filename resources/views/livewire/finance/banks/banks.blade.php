<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-bank-fill me-2"></i> {{ __('bank_trans.banks_title') }}</h5>
            <button wire:click="resetFields" class="btn btn-light btn-sm">
                <i class="ri-add-circle-line"></i> {{ __('bank_trans.new_bank') }}
            </button>
        </div>

        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form -->
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.branch') }}</label>
                    <select wire:model="branch_id" class="form-select">
                        <option value="">{{ __('bank_trans.branch') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.name_ar') }}</label>
                    <input type="text" class="form-control" wire:model="name_ar">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.name_en') }}</label>
                    <input type="text" class="form-control" wire:model="name_en">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.account_number') }}</label>
                    <input type="text" class="form-control" wire:model="account_number">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.iban') }}</label>
                    <input type="text" class="form-control" wire:model="iban">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.swift_code') }}</label>
                    <input type="text" class="form-control" wire:model="swift_code">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.currency') }}</label>
                    <input type="text" class="form-control" wire:model="currency">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.balance') }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model="balance">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.status') }}</label>
                    <select wire:model="status" class="form-select">
                        <option value="active">{{ __('bank_trans.active') }}</option>
                        <option value="inactive">{{ __('bank_trans.inactive') }}</option>
                    </select>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-3-fill"></i> {{ $isEdit ? __('bank_trans.update') : __('bank_trans.save') }}
                    </button>
                </div>
            </form>

            <!-- Table -->
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('bank_trans.name_ar') }}</th>
                        <th>{{ __('bank_trans.branch') }}</th>
                        <th>{{ __('bank_trans.account_number') }}</th>
                        <th>{{ __('bank_trans.currency') }}</th>
                        <th>{{ __('bank_trans.balance') }}</th>
                        <th>{{ __('bank_trans.status') }}</th>
                        <th>{{ __('bank_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($banks as $bank)
                        <tr>
                            <td>{{ $bank->id }}</td>
                            <td>{{ $bank->name_ar }}</td>
                            <td>{{ $bank->branch?->name_ar }}</td>
                            <td>{{ $bank->account_number }}</td>
                            <td>{{ $bank->currency }}</td>
                            <td>{{ number_format($bank->balance, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $bank->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ __('bank_trans.' . $bank->status) }}
                                </span>
                            </td>
                            <td>
                                <button wire:click="edit({{ $bank->id }})" class="btn btn-sm btn-warning">
                                    <i class="ri-edit-2-fill"></i>
                                </button>
                                <button wire:click="delete({{ $bank->id }})" class="btn btn-sm btn-danger"
                                    onclick="confirm('{{ __('bank_trans.confirm_delete') }}') || event.stopImmediatePropagation()">
                                    <i class="ri-delete-bin-5-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ __('bank_trans.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $banks->links() }}
        </div>
    </div>
</div>
