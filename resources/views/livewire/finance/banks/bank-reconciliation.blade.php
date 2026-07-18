<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-dark text-white">
            <h5 class="mb-0"><i class="mdi mdi-scale-balance me-2"></i> {{ __('bank_trans.reconciliation') }}</h5>
        </div>

        <div class="card-body">
            {{-- رسائل --}}
            @if(session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- فورم --}}
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.bank') }}</label>
                    <select wire:model="bank_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_bank') }}</option>
                        @foreach($banks as $b)
                            <option value="{{ $b->id }}">{{ $b->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('bank_id') <span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.account') }}</label>
                    <select wire:model="account_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_account') }}</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->account_name }}</option>
                        @endforeach
                    </select>
                    @error('account_id') <span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.period') }}</label>
                    <input type="month" wire:model="period" class="form-control">
                    @error('period') <span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.opening_balance') }}</label>
                    <input type="number" step="0.01" wire:model="opening_balance" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.closing_balance') }}</label>
                    <input type="number" step="0.01" wire:model="closing_balance" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.difference') }}</label>
                    <input type="text" wire:model="difference" class="form-control" readonly>
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-{{ $isEdit ? 'warning' : 'success' }}">
                        <i class="mdi mdi-content-save"></i>
                        {{ $isEdit ? __('bank_trans.update') : __('bank_trans.save') }}
                    </button>
                    <button type="button" wire:click="resetFields" class="btn btn-secondary">
                        <i class="mdi mdi-refresh"></i> {{ __('bank_trans.reset') }}
                    </button>
                </div>
            </form>

            {{-- جدول --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('bank_trans.bank') }}</th>
                            <th>{{ __('bank_trans.account') }}</th>
                            <th>{{ __('bank_trans.period') }}</th>
                            <th>{{ __('bank_trans.opening_balance') }}</th>
                            <th>{{ __('bank_trans.closing_balance') }}</th>
                            <th>{{ __('bank_trans.difference') }}</th>
                            <th>{{ __('bank_trans.reconciled_by') }}</th>
                            <th>{{ __('bank_trans.reconciliation_date') }}</th>
                            <th>{{ __('bank_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                            <tr>
                                <td>{{ $rec->id }}</td>
                                <td>{{ $rec->bank->name_ar ?? '-' }}</td>
                                <td>{{ $rec->account->account_name ?? '-' }}</td>
                                <td>{{ $rec->period }}</td>
                                <td>{{ number_format($rec->opening_balance,2) }}</td>
                                <td>{{ number_format($rec->closing_balance,2) }}</td>
                                <td>{{ number_format($rec->difference,2) }}</td>
                                <td>{{ $rec->reconciler->name ?? '-' }}</td>
                                <td>{{ $rec->reconciliation_date ?? '-' }}</td>
                                <td>
                                    <button wire:click="edit({{ $rec->id }})" class="btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button wire:click="delete({{ $rec->id }})" class="btn btn-sm btn-danger">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                    @if(!$rec->reconciliation_date)
                                        <button wire:click="approve({{ $rec->id }})" class="btn btn-sm btn-success">
                                            <i class="mdi mdi-check-decagram"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">{{ __('bank_trans.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $records->links() }}
        </div>
    </div>
</div>
