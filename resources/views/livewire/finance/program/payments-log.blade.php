<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-money-bill-wave me-2"></i>{{ __('finance_trans.title') }}
            </h4>
        </div>

        <div class="card-body">
            @if(session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="mb-3">
                <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="{{ __('finance_trans.search_placeholder') }}">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('finance_trans.patient') }}</th>
                            <th>{{ __('finance_trans.amount') }}</th>
                            <th>{{ __('finance_trans.status') }}</th>
                            <th>{{ __('finance_trans.user') }}</th>
                            <th>{{ __('finance_trans.created_at') }}</th>
                            <th>{{ __('finance_trans.options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                            <tr>
                                <td>{{ $receipt->id }}</td>
                                <td>{{ $receipt->patient->full_name ?? '-' }}</td>
                                <td>{{ number_format($receipt->amount, 2) }}</td>
                                <td>
                                    @if($receipt->status === 'confirmed')
                                        <span class="badge bg-success">{{ __('finance_trans.confirmed') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('finance_trans.cancelled') }}</span>
                                    @endif
                                </td>
                                <td>{{ $receipt->user->name ?? '-' }}</td>
                                <td>{{ $receipt->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <button wire:click="viewDetails({{ $receipt->id }})" class="btn btn-sm btn-info">
                                        {{ __('finance_trans.viewDetails') }}
                                    </button>
                                    @if($receipt->status === 'confirmed')
                                        <button wire:click="confirmCancellation({{ $receipt->id }})" class="btn btn-sm btn-danger">
                                            {{ __('finance_trans.delete') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('finance_trans.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $receipts->links() }}
            </div>
        </div>
    </div>

    <!-- Modal التفاصيل -->
    <div wire:ignore.self class="modal fade" id="receiptDetailsModal" tabindex="-1" aria-labelledby="detailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailsLabel">{{ __('finance_trans.details_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($selectedReceipt)
                        <dl class="row">
                            <dt class="col-sm-3">{{ __('finance_trans.patient') }}</dt>
                            <dd class="col-sm-9">{{ $selectedReceipt->patient->full_name ?? '-' }}</dd>

                            <dt class="col-sm-3">{{ __('finance_trans.amount') }}</dt>
                            <dd class="col-sm-9">{{ number_format($selectedReceipt->amount, 2) }}</dd>

                            <dt class="col-sm-3">{{ __('finance_trans.status') }}</dt>
                            <dd class="col-sm-9">
                                @if($selectedReceipt->status === 'confirmed')
                                    <span class="badge bg-success">{{ __('finance_trans.confirmed') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('finance_trans.cancelled') }}</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">{{ __('finance_trans.user') }}</dt>
                            <dd class="col-sm-9">{{ $selectedReceipt->user->name ?? '-' }}</dd>

                            <dt class="col-sm-3">{{ __('finance_trans.created_at') }}</dt>
                            <dd class="col-sm-9">{{ $selectedReceipt->created_at->format('Y-m-d H:i') }}</dd>

                            <dt class="col-sm-3">{{ __('finance_trans.notes') }}</dt>
                            <dd class="col-sm-9">{{ $selectedReceipt->notes ?? '-' }}</dd>
                        </dl>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('finance_trans.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal تأكيد الإلغاء -->
    <div wire:ignore.self class="modal fade" id="confirmCancelModal" tabindex="-1" aria-labelledby="cancelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelLabel">{{ __('finance_trans.cancel_confirm_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('finance_trans.cancel_confirm_text') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('finance_trans.cancel') }}</button>
                    <button wire:click="cancelReceipt" class="btn btn-danger">{{ __('finance_trans.confirm_cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('showReceiptDetails', () => {
            var modal = new bootstrap.Modal(document.getElementById('receiptDetailsModal'));
            modal.show();
        });

        window.addEventListener('showCancelConfirm', () => {
            var modal = new bootstrap.Modal(document.getElementById('confirmCancelModal'));
            modal.show();
        });

        Livewire.on('refreshPayments', () => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('confirmCancelModal'));
            if(modal) modal.hide();
        });
    </script>
</div>
