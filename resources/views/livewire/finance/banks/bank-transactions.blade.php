<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0"><i class="mdi mdi-bank-transfer me-2"></i> {{ __('bank_trans.bank_transactions') }}</h5>
        </div>

        <div class="card-body">

            {{-- رسائل --}}
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- فورم --}}
            <form wire:submit.prevent="save" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.bank') }}</label>
                    <select wire:model="bank_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_bank') }}</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.account') }}</label>
                    <select wire:model="account_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_account') }}</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->account_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.date') }}</label>
                    <input type="date" wire:model="transaction_date" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.type') }}</label>
                    <select wire:model="type" class="form-select">
                        <option value="deposit">{{ __('bank_trans.deposit') }}</option>
                        <option value="withdrawal">{{ __('bank_trans.withdrawal') }}</option>
                        <option value="transfer">{{ __('bank_trans.transfer') }}</option>
                        <option value="fees">{{ __('bank_trans.fees') }}</option>
                        <option value="interest">{{ __('bank_trans.interest') }}</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.amount') }}</label>
                    <input type="number" step="0.01" wire:model="amount" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.reference') }}</label>
                    <input type="text" wire:model="reference" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('bank_trans.notes') }}</label>
                    <input type="text" wire:model="notes" class="form-control">
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-success">
                        <i class="ri-save-3-fill"></i>
                        {{ $isEdit ? __('bank_trans.update') : __('bank_trans.add') }}
                    </button>
                </div>
            </form>

            {{-- جدول --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('bank_trans.date') }}</th>
                            <th>{{ __('bank_trans.bank') }}</th>
                            <th>{{ __('bank_trans.account') }}</th>
                            <th>{{ __('bank_trans.type') }}</th>
                            <th>{{ __('bank_trans.amount') }}</th>
                            <th>{{ __('bank_trans.reference') }}</th>
                            <th>{{ __('bank_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                            <tr>
                                <td>{{ $tx->id }}</td>
                                <td>{{ $tx->transaction_date }}</td>
                                <td>{{ $tx->bank->name_ar ?? '-' }}</td>
                                <td>{{ $tx->account->account_number ?? '-' }}</td>
                                <td>{{ __('bank_trans.'.$tx->type) }}</td>
                                <td>{{ number_format($tx->amount,2) }}</td>
                                <td>{{ $tx->reference }}</td>
                                <td>
                                    <button wire:click="edit({{ $tx->id }})" class="btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button wire:click="delete({{ $tx->id }})" class="btn btn-sm btn-danger">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $transactions->links() }}
        </div>
    </div>
</div>
