
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0"><i class="mdi mdi-shield-account me-2"></i> {{ __('bank_trans.permissions_title') }}</h5>
        </div>

        <div class="card-body">
            {{-- رسائل --}}
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- الفورم --}}
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">{{ __('bank_trans.user') }}</label>
                    <select wire:model="user_id" class="form-select">
                        <option value="">{{ __('bank_trans.choose_user') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- صلاحيات --}}
                <div class="col-md-12">
                    <label class="form-label fw-bold">{{ __('bank_trans.permissions') }}</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="can_issue_cheque">
                        <label class="form-check-label">{{ __('bank_trans.can_issue_cheque') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="can_approve_cheque">
                        <label class="form-check-label">{{ __('bank_trans.can_approve_cheque') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="can_clear_cheque">
                        <label class="form-check-label">{{ __('bank_trans.can_clear_cheque') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="can_cancel_cheque">
                        <label class="form-check-label">{{ __('bank_trans.can_cancel_cheque') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="can_bounce_cheque">
                        <label class="form-check-label">{{ __('bank_trans.can_bounce_cheque') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="can_reconcile_cheque">
                        <label class="form-check-label">{{ __('bank_trans.can_reconcile_cheque') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="can_view_reports">
                        <label class="form-check-label">{{ __('bank_trans.can_view_reports') }}</label>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-3-fill"></i> {{ $isEdit ? __('bank_trans.update') : __('bank_trans.save') }}
                    </button>
                    @if($isEdit)
                        <button type="button" wire:click="resetFields" class="btn btn-secondary">
                            <i class="ri-close-line"></i> {{ __('bank_trans.cancel') }}
                        </button>
                    @endif
                </div>
            </form>

            {{-- جدول --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('bank_trans.user') }}</th>
                            <th>{{ __('bank_trans.permissions') }}</th>
                            <th>{{ __('bank_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $perm)
                            <tr>
                                <td>{{ $perm->id }}</td>
                                <td>{{ $perm->user->name }}</td>
                                <td>
                                    @if($perm->can_issue_cheque) <span class="badge bg-primary">{{ __('bank_trans.can_issue_cheque') }}</span> @endif
                                    @if($perm->can_approve_cheque) <span class="badge bg-info">{{ __('bank_trans.can_approve_cheque') }}</span> @endif
                                    @if($perm->can_clear_cheque) <span class="badge bg-success">{{ __('bank_trans.can_clear_cheque') }}</span> @endif
                                    @if($perm->can_cancel_cheque) <span class="badge bg-danger">{{ __('bank_trans.can_cancel_cheque') }}</span> @endif
                                    @if($perm->can_bounce_cheque) <span class="badge bg-warning">{{ __('bank_trans.can_bounce_cheque') }}</span> @endif
                                    @if($perm->can_reconcile_cheque) <span class="badge bg-dark">{{ __('bank_trans.can_reconcile_cheque') }}</span> @endif
                                    @if($perm->can_view_reports) <span class="badge bg-secondary">{{ __('bank_trans.can_view_reports') }}</span> @endif
                                </td>
                                <td>
                                    <button wire:click="edit({{ $perm->id }})" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></button>
                                    <button wire:click="delete({{ $perm->id }})" class="btn btn-sm btn-danger"><i class="ri-delete-bin-6-line"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $permissions->links() }}
        </div>
    </div>
</div>
