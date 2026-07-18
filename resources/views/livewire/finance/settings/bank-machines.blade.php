<div class="container-fluid py-4" dir="rtl">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-credit-card-outline fs-4"></i>
                <h5 class="mb-0">{{ __('bank_machine_trans.title') }}</h5>
            </div>
            <button wire:click="resetInput" class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#machineModal">
                <i class="mdi mdi-plus-circle"></i> {{ __('bank_machine_trans.add_machine') }}
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
                            <th><i class="mdi mdi-barcode"></i> {{ __('bank_machine_trans.code') }}</th>
                            <th><i class="mdi mdi-desktop-classic"></i> {{ __('bank_machine_trans.type') }}</th>
                            <th><i class="mdi mdi-map-marker-outline"></i> {{ __('bank_machine_trans.location') }}</th>
                            <th><i class="mdi mdi-bank"></i> {{ __('bank_machine_trans.bank') }}</th>
                            <th><i class="mdi mdi-home-city-outline"></i> {{ __('bank_machine_trans.branch') }}</th>
                            <th><i class="mdi mdi-check-decagram"></i> {{ __('bank_machine_trans.status') }}</th>
                            <th><i class="mdi mdi-cog-outline"></i> {{ __('bank_machine_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($machines as $machine)
                            <tr>
                                <td>{{ $machine->machine_code }}</td>
                                <td>{{ $machine->machine_type }}</td>
                                <td>{{ $machine->location }}</td>
                                <td>{{ $machine->bank?->name_ar }}</td>
                                <td>{{ $machine->branch?->name }}</td>
                                <td>
                                    <span class="badge 
                                        @if($machine->status == 'active') bg-success 
                                        @elseif($machine->status == 'inactive') bg-danger 
                                        @else bg-warning @endif">
                                        {{ $machine->status == 'active' ? __('bank_machine_trans.active') : ($machine->status == 'inactive' ? __('bank_machine_trans.inactive') : __('bank_machine_trans.maintenance')) }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $machine->id }})" data-bs-toggle="modal"
                                        data-bs-target="#machineModal" class="btn btn-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button wire:click="delete({{ $machine->id }})" class="btn btn-danger btn-sm">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="mdi mdi-database-off"></i> {{ __('bank_machine_trans.no_data') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal لإضافة/تعديل ماكينة -->
    <div wire:ignore.self class="modal fade" id="machineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="mdi mdi-credit-card me-2"></i>
                        {{ $isEdit ? __('bank_machine_trans.edit_machine') : __('bank_machine_trans.add_machine') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-bank"></i> {{ __('bank_machine_trans.bank') }}</label>
                            <select class="form-select" wire:model="bank_id">
                                <option value="">{{ __('bank_machine_trans.bank') }}</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name_ar }}</option>
                                @endforeach
                            </select>
                            @error('bank_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-home-city-outline"></i> {{ __('bank_machine_trans.branch') }}</label>
                            <select class="form-select" wire:model="branch_id">
                                <option value="">{{ __('bank_machine_trans.branch') }}</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="mdi mdi-barcode"></i> {{ __('bank_machine_trans.code') }}</label>
                            <input type="text" class="form-control" wire:model="machine_code">
                            @error('machine_code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="mdi mdi-desktop-classic"></i> {{ __('bank_machine_trans.type') }}</label>
                            <select class="form-select" wire:model="machine_type">
                                <option value="ATM">ATM</option>
                                <option value="POS">POS</option>
                                <option value="Kiosk">Kiosk</option>
                            </select>
                            @error('machine_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="mdi mdi-map-marker-outline"></i> {{ __('bank_machine_trans.location') }}</label>
                            <input type="text" class="form-control" wire:model="location">
                            @error('location') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="mdi mdi-check-decagram"></i> {{ __('bank_machine_trans.status') }}</label>
                            <select class="form-select" wire:model="status">
                                <option value="active">{{ __('bank_machine_trans.active') }}</option>
                                <option value="inactive">{{ __('bank_machine_trans.inactive') }}</option>
                                <option value="maintenance">{{ __('bank_machine_trans.maintenance') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i> {{ __('bank_machine_trans.cancel') }}
                    </button>
                    <button type="button" wire:click="save" class="btn btn-primary">
                        <i class="mdi mdi-content-save"></i> {{ __('bank_machine_trans.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('close-modal', () => {
        let modal = bootstrap.Modal.getInstance(document.getElementById('machineModal'));
        modal.hide();
    });
</script>
@endpush
