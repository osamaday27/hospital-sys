<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-building-4-line me-2"></i> {{ __('bank_trans.branches_title') }}</h5>
            <button wire:click="resetFields" class="btn btn-light btn-sm">
                <i class="ri-add-circle-line"></i> {{ __('bank_trans.new_branch') }}
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
                    <label class="form-label">{{ __('bank_trans.name_ar') }}</label>
                    <input type="text" class="form-control" wire:model="name_ar">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.name_en') }}</label>
                    <input type="text" class="form-control" wire:model="name_en">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('bank_trans.address') }}</label>
                    <input type="text" class="form-control" wire:model="address">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.phone') }}</label>
                    <input type="text" class="form-control" wire:model="phone">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('bank_trans.email') }}</label>
                    <input type="email" class="form-control" wire:model="email">
                </div>

                <div class="col-md-3">
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
                        <th>{{ __('bank_trans.name_en') }}</th>
                        <th>{{ __('bank_trans.address') }}</th>
                        <th>{{ __('bank_trans.phone') }}</th>
                        <th>{{ __('bank_trans.email') }}</th>
                        <th>{{ __('bank_trans.status') }}</th>
                        <th>{{ __('bank_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($branches as $branch)
                        <tr>
                            <td>{{ $branch->id }}</td>
                            <td>{{ $branch->name_ar }}</td>
                            <td>{{ $branch->name_en }}</td>
                            <td>{{ $branch->address }}</td>
                            <td>{{ $branch->phone }}</td>
                            <td>{{ $branch->email }}</td>
                            <td>
                                <span class="badge bg-{{ $branch->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ __('bank_trans.' . $branch->status) }}
                                </span>
                            </td>
                            <td>
                                <button wire:click="edit({{ $branch->id }})" class="btn btn-sm btn-warning">
                                    <i class="ri-edit-2-fill"></i>
                                </button>
                                <button wire:click="delete({{ $branch->id }})" class="btn btn-sm btn-danger"
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

            {{ $branches->links() }}
        </div>
    </div>
</div>
