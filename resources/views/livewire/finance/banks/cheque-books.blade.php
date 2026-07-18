<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0"><i class="mdi mdi-book-plus-outline me-2"></i> {{ __('bank_trans.cheque_books') }}</h5>
        </div>

        <div class="card-body">
            {{-- تنبيه --}}
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- الفورم --}}
            <form wire:submit.prevent="save" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.book_number') }}</label>
                    <input type="text" wire:model="book_number" class="form-control">
                    @error('book_number') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

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

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.start_number') }}</label>
                    <input type="number" wire:model="start_number" class="form-control">
                    @error('start_number') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('bank_trans.end_number') }}</label>
                    <input type="number" wire:model="end_number" class="form-control">
                    @error('end_number') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">{{ __('bank_trans.notes') }}</label>
                    <textarea wire:model="notes" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-3-fill"></i> {{ __('bank_trans.save') }}
                    </button>
                </div>
            </form>

            {{-- الجدول --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('bank_trans.book_number') }}</th>
                            <th>{{ __('bank_trans.bank_account') }}</th>
                            <th>{{ __('bank_trans.range') }}</th>
                            <th>{{ __('bank_trans.status') }}</th>
                            <th>{{ __('bank_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($chequeBooks as $book)
                            <tr>
                                <td>{{ $book->id }}</td>
                                <td>{{ $book->book_number }}</td>
                                <td>{{ $book->bankAccount?->account_name }}</td>
                                <td>{{ $book->start_number }} - {{ $book->end_number }}</td>
                                <td>
                                    <span class="badge bg-{{ $book->status == 'active' ? 'success' : ($book->status == 'inactive' ? 'secondary' : 'danger') }}">
                                        {{ __('bank_trans.'.$book->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($book->status == 'active')
                                        <button class="btn btn-sm btn-warning" wire:click="changeStatus({{ $book->id }}, 'inactive')">
                                            {{ __('bank_trans.deactivate') }}
                                        </button>
                                        <button class="btn btn-sm btn-danger" wire:click="changeStatus({{ $book->id }}, 'closed')">
                                            {{ __('bank_trans.close') }}
                                        </button>
                                    @elseif ($book->status == 'inactive')
                                        <button class="btn btn-sm btn-success" wire:click="changeStatus({{ $book->id }}, 'active')">
                                            {{ __('bank_trans.activate') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('bank_trans.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $chequeBooks->links() }}
        </div>
    </div>
</div>
