<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-money-cny-circle-line me-2"></i> {{ __('bank_trans.thresholds_title') }}</h5>
            <button wire:click="resetFields" class="btn btn-light btn-sm">
                <i class="ri-add-circle-line"></i> {{ __('bank_trans.new_threshold') }}
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
                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.min_amount') }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model="min_amount">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.max_amount') }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model="max_amount">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.required_role') }}</label>
                    <input type="text" class="form-control" wire:model="required_role" placeholder="{{ __('bank_trans.enter_required_role') }}">
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
                        <th>{{ __('bank_trans.min_amount') }}</th>
                        <th>{{ __('bank_trans.max_amount') }}</th>
                        <th>{{ __('bank_trans.required_role') }}</th>
                        <th>{{ __('bank_trans.status') }}</th>
                        <th>{{ __('bank_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($thresholds as $th)
                        <tr>
                            <td>{{ $th->id }}</td>
                            <td>{{ number_format($th->min_amount, 2) }}</td>
                            <td>{{ $th->max_amount ? number_format($th->max_amount, 2) : '-' }}</td>
                            <td>{{ $th->required_role }}</td>
                            <td>
                                <span class="badge bg-{{ $th->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ __('bank_trans.' . $th->status) }}
                                </span>
                            </td>
                            <td>
                                <button wire:click="edit({{ $th->id }})" class="btn btn-sm btn-warning">
                                    <i class="ri-edit-2-fill"></i>
                                </button>
                                <button wire:click="delete({{ $th->id }})" class="btn btn-sm btn-danger"
                                    onclick="confirm('{{ __('bank_trans.confirm_delete') }}') || event.stopImmediatePropagation()">
                                    <i class="ri-delete-bin-5-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('bank_trans.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $thresholds->links() }}
        </div>
    </div>
</div>
