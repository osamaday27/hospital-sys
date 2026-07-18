{{-- resources/views/livewire/supply-chain/inventory/show-inventory-audit.blade.php --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">
            <i class="ri-eye-line me-2"></i>
            {{ __('supply_chain_trans.audit_details') }} - {{ $audit->store->name_ar ?? '-' }}
        </h5>
        <a href="{{ route('inventory.audit.index') }}" class="btn btn-light btn-sm">
            <i class="ri-arrow-go-back-line me-1"></i> {{ __('supply_chain_trans.back') }}
        </a>
    </div>

    <div class="card-body">
        <div class="mb-4">
            <strong>{{ __('supply_chain_trans.audit_date') }}:</strong> {{ $audit->audit_date }}<br>
            <strong>{{ __('supply_chain_trans.status') }}:</strong>
            <span class="badge bg-{{ $audit->status == 'draft' ? 'warning' : ($audit->status == 'finalized' ? 'info' : 'success') }}">
                {{ __('supply_chain_trans.' . $audit->status) }}
            </span><br>
            <strong>{{ __('supply_chain_trans.notes') }}:</strong> {{ $audit->notes ?? '-' }}
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('supply_chain_trans.product') }}</th>
                        <th>{{ __('supply_chain_trans.unit') }}</th>
                        <th>{{ __('supply_chain_trans.system_quantity') }}</th>
                        <th>{{ __('supply_chain_trans.actual_quantity') }}</th>
                        <th>{{ __('supply_chain_trans.difference') }}</th>
                        <th>{{ __('supply_chain_trans.reason') }}</th>
                        <th>{{ __('supply_chain_trans.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audit->details as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name_ar ?? '-' }}</td>
                            <td>{{ $item->unit->name_ar ?? '-' }}</td>
                            <td>{{ $item->system_quantity }}</td>
                            <td>{{ $item->actual_quantity }}</td>
                            <td>
                                @php
                                    $diff = $item->actual_quantity - $item->system_quantity;
                                    $type = $diff > 0 ? 'زيادة' : ($diff < 0 ? 'عجز' : '-');
                                @endphp
                                <span class="badge bg-{{ $diff > 0 ? 'success' : ($diff < 0 ? 'danger' : 'secondary') }}">
                                    {{ number_format($diff, 2) }} ({{ $type }})
                                </span>
                            </td>
                            <td>{{ $item->reason ?? '-' }}</td>
                            <td>{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ __('supply_chain_trans.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
