<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0"><i class="mdi mdi-cash-plus me-2"></i> {{ __('bank_trans.cheque_issue') }}</h5>
        </div>

        <div class="card-body">
            {{-- رسائل --}}
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="ri-alert-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- الفورم --}}
            <form wire:submit.prevent="save" class="row g-3">
                {{-- اختيار الحساب البنكي --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.bank_account') }}</label>
                    <select wire:model="bank_account_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_bank_account') }}</option>
                        @foreach ($bankAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->account_name }} - {{ $acc->account_number }}</option>
                        @endforeach
                    </select>
                    @error('bank_account_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- اختيار دفتر الشيك --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.cheque_book') }}</label>
                    <select wire:model="cheque_book_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_cheque_book') }}</option>
                        @foreach ($chequeBooks as $book)
                            <option value="{{ $book->id }}">{{ $book->book_number }} ({{ $book->start_number }} - {{ $book->end_number }})</option>
                        @endforeach
                    </select>
                    @error('cheque_book_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- اختيار ورقة الشيك --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.cheque_leaf') }}</label>
                    <select wire:model="cheque_leaf_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_cheque_leaf') }}</option>
                        @foreach ($chequeLeaves as $leaf)
                            <option value="{{ $leaf->id }}">{{ $leaf->cheque_number }}</option>
                        @endforeach
                    </select>
                    @error('cheque_leaf_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- باقي البيانات --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('bank_trans.payee') }}</label>
                    <input type="text" wire:model="payee" class="form-control">
                    @error('payee') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('bank_trans.amount') }}</label>
                    <input type="number" step="0.01" wire:model="amount" class="form-control">
                    @error('amount') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('bank_trans.issue_date') }}</label>
                    <input type="date" wire:model="issue_date" class="form-control">
                    @error('issue_date') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('bank_trans.due_date') }}</label>
                    <input type="date" wire:model="due_date" class="form-control">
                    @error('due_date') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('bank_trans.notes') }}</label>
                    <textarea wire:model="notes" class="form-control" rows="2"></textarea>
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
