<div>
    {{-- The best athlete wants his opponent at his best. --}}
</div>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="ri-archive-line fs-4 me-3"></i>
                <h4 class="mb-0">{{ __('supply_chain_trans.movement_details') }}: <span class="fw-light">{{ $movement_number }}</span></h4>
            </div>
            <a href="{{ route('view_opening_balance') }}" class="btn btn-outline-light btn-sm rounded-pill" style="background-color: #3577f1 !important;">
                <i class="ri-arrow-left-line me-1"></i> {{ __('supply_chain_trans.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="card-body p-4">
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show rounded">
                <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- بطاقة ملخص الحركة -->
        @if($movement_summary)
        <div class="card mb-4 border">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted small mb-2">{{ __('supply_chain_trans.store') }}</h6>
                            <p class="mb-0 fw-bold">{{ $movement_summary->store->name_ar ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted small mb-2">{{ __('supply_chain_trans.supplier') }}</h6>
                            <p class="mb-0 fw-bold">{{ $movement_summary->supplier->name_ar ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted small mb-2">{{ __('supply_chain_trans.user') }}</h6>
                            <p class="mb-0 fw-bold">{{ $movement_summary->user->name ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted small mb-2">{{ __('supply_chain_trans.movement_date') }}</h6>
                            <p class="mb-0 fw-bold">{{ $movement_summary->movement_date }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted small mb-2">{{ __('supply_chain_trans.invoice_no') }}</h6>
                            <p class="mb-0 fw-bold">{{ $movement_summary->invoice_no ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted small mb-2">{{ __('supply_chain_trans.reference_no') }}</h6>
                            <p class="mb-0 fw-bold">{{ $movement_summary->reference_no ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted small mb-2">{{ __('supply_chain_trans.created_at') }}</h6>
                            <p class="mb-0 fw-bold">{{ $movement_summary->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted small mb-2">{{ __('supply_chain_trans.notes') }}</h6>
                            <p class="mb-0 fw-bold">{{ $movement_summary->notes ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- جدول العناصر -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead class="table-dark" style="background-color: #3577f1 !important;">
                    <tr style="background-color: #3577f1 !important;">
                        <th width="20%">{{ __('supply_chain_trans.product') }}</th>
                        <th width="10%">{{ __('supply_chain_trans.code') }}</th>
                        <th width="8%">{{ __('supply_chain_trans.unit') }}</th>
                        <th width="8%">{{ __('supply_chain_trans.quantity') }}</th>
                        <th width="10%">{{ __('supply_chain_trans.cost_price') }}</th>
                        <th width="10%">{{ __('supply_chain_trans.sale_price') }}</th>
                        <th width="12%">{{ __('supply_chain_trans.batch_no') }}</th>
                        <th width="12%">{{ __('supply_chain_trans.expiry_date') }}</th>
                        <th width="10%">{{ __('supply_chain_trans.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movement_items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="ri-box-3-line me-2 text-primary"></i>
                                    {{ $item->product->name_ar ?? '-' }}
                                </div>
                            </td>
                            <td><span class="badge bg-secondary">{{ $item->product->product_code ?? '-' }}</span></td>
                            <td>{{ $item->unit->name_ar ?? '-' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->unitData->cost_price ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->unitData->sale_price ?? 0, 2) }}</td>
                            <td><span class="badge bg-info">{{ $item->batch_no }}</span></td>
                            <td @if($item->expiry_date && now()->diffInDays($item->expiry_date, false) < 30) class="text-danger fw-bold" @endif>
                                {{ $item->expiry_date }}
                            </td>
                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ri-database-2-line fs-1 text-muted mb-2"></i>
                                    <span class="text-muted">{{ __('supply_chain_trans.no_items_found') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- زر التراجع -->
        <div class="mt-4 text-end">
            <button wire:click="rollbackMovement" class="btn btn-danger rounded-pill px-4"
                onclick="return confirm('{{ __('supply_chain_trans.confirm_rollback') }}')">
                <i class="ri-arrow-go-back-line me-1"></i> 
                {{ __('supply_chain_trans.rollback_movement') }}
            </button>
        </div>
    </div>
</div>