<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold" style="color: #fff">
            <i class="ri-history-line me-2"></i> {{ __('supply_chain_trans.previous_movementsopening_balance') }}
        </h5>
        <a href="{{ route('opening_balance') }}" class="btn btn-light btn-sm text-primary fw-bold">
            <i class="ri-add-circle-line me-1"></i> {{ __('supply_chain_trans.add_item_to_store') }}
        </a>
    </div>

    <div class="card-body p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                    <input wire:model.debounce.500ms="search" class="form-control border-start-0" 
                           placeholder="{{ __('supply_chain_trans.search_product') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select wire:model="store_id" class="form-select select2">
                    <option value="">{{ __('supply_chain_trans.all_stores') }}</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select wire:model="supplier_id" class="form-select select2">
                    <option value="">{{ __('supply_chain_trans.all_suppliers') }}</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ app()->getLocale() == 'ar' ? $sup->name_ar : $sup->name_en }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive rounded-3 border">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('supply_chain_trans.movement_number') }}</th>
                        <th>{{ __('supply_chain_trans.movement_date') }}</th>
                        <th>{{ __('supply_chain_trans.store') }}</th>
                        <th>{{ __('supply_chain_trans.supplier') }}</th>
                        <th>{{ __('supply_chain_trans.total_items') }}</th>
                        <th>{{ __('supply_chain_trans.created_by') }}</th>
                        <th>{{ __('supply_chain_trans.created_at') }}</th>
                        <th class="text-center">{{ __('supply_chain_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr class="align-middle">
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td><code>{{ $movement->movement_number ?? '-' }}</code></td>
                            <td>{{ $movement->movement_date->format('Y-m-d')  }}</td>
                            <td><span class="badge bg-info-light text-info">{{ $movement->store->name_ar ?? '-' }}</span></td>
                            <td>{{ $movement->supplier->name_ar ?? '-' }}</td>
                            <td><span class="badge bg-secondary text-white">{{ $movement->items_count ?? '1' }}</span></td>
                            <td>{{ $movement->user->name ?? '-' }}</td>
                            <td class="text-muted small">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <a href="/opening_balance/{{ $movement->movement_number }}/show" class="btn btn-sm btn-outline-info">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="/opening_balance/{{ $movement->movement_number }}/print" class="btn btn-sm btn-outline-info">
                                    <i class="ri-printer-line"></i>
                                </a>
                                <a href="/opening_balance/{{ $movement->movement_number }}/edit"  class="btn btn-sm btn-outline-warning">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <button wire:click="rollback({{ $movement->movement_number }})" class="btn btn-sm btn-outline-danger">
                                    <i class="ri-arrow-go-back-line"></i>
                                </button>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="ri-database-2-line fs-2"></i>
                                <p class="mt-2">{{ __('supply_chain_trans.no_records_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                {{ __('Showing') }} {{ $movements->firstItem() }} - {{ $movements->lastItem() }} 
                {{ __('of') }} {{ $movements->total() }} {{ __('records') }}
            </div>
            <div>
                {{ $movements->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    .select2 {
        width: 100% !important;
    }
    .card {
        border-radius: 12px;
    }
    .table-hover tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        padding: 5px 8px;
        border-radius: 4px;
        font-weight: 500;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: $(this).data('placeholder'),
            allowClear: true
        });

        $('.select2').on('change', function(e) {
            @this.set($(this).attr('wire:model'), $(this).val());
        });
    });
</script>
@endpush
