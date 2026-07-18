{{-- resources/views/livewire/supply-chain/inventory/index-inventory-audit.blade.php --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">
            <i class="ri-clipboard-line me-2"></i>
            {{ __('supply_chain_trans.inventory_audit_list') }}
        </h5>
        <a href="inventory_audits_create" class="btn btn-light btn-sm text-primary">
            <i class="ri-add-line"></i> {{ __('supply_chain_trans.new_audit') }}
        </a>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success">
                <i class="ri-checkbox-circle-line me-1"></i> {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('supply_chain_trans.store') }}</th>
                        <th>{{ __('supply_chain_trans.audit_date') }}</th>
                        <th>{{ __('supply_chain_trans.status') }}</th>
                        <th>{{ __('supply_chain_trans.user') }}</th>
                        <th>{{ __('supply_chain_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $index => $audit)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $audit->store->name_ar ?? '-' }}</td>
                            <td>{{ $audit->audit_date }}</td>
                            <td>
                                <span class="badge bg-{{ $audit->status == 'draft' ? 'warning' : ($audit->status == 'finalized' ? 'info' : 'success') }}">
                                    {{ __('supply_chain_trans.' . $audit->status) }}
                                </span>
                            </td>
                            <td>{{ $audit->user->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('inventory.audit.show', $audit->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ri-eye-line"></i> {{ __('supply_chain_trans.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('supply_chain_trans.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
