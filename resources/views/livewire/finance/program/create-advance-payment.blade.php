<div wire:ignore.self class="modal fade" id="advancePaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-cash-plus"></i> {{ __('finance_trans.add_advance_payment') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($patient)
                    <div class="alert alert-info">
                        {{ __('finance_trans.patient') }}: <strong>{{ $patient->full_name }}</strong>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('finance_trans.amount') }}</label>
                        <input type="number" class="form-control" wire:model="amount">
                        @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('finance_trans.currency') }}</label>
                        <input type="text" class="form-control" wire:model="currency">
                        @error('currency') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('finance_trans.payment_method') }}</label>
                        <select class="form-control" wire:model="payment_method">
                            <option value="cash">{{ __('finance_trans.cash') }}</option>
                            <option value="card">{{ __('finance_trans.card') }}</option>
                            <option value="transfer">{{ __('finance_trans.bank_transfer') }}</option>
                        </select>
                        @error('payment_method') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('finance_trans.locker') }}</label>
                        <select class="form-control" wire:model="locker_id">
                            <option value="">{{ __('finance_trans.select_locker') }}</option>
                            @foreach($lockers as $locker)
                                <option value="{{ $locker->id }}">{{ $locker->name }}</option>
                            @endforeach
                        </select>
                        @error('locker_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('finance_trans.payment_date') }}</label>
                        <input type="date" class="form-control" wire:model="payment_date">
                        @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">{{ __('finance_trans.notes') }}</label>
                        <textarea class="form-control" wire:model="notes"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('finance_trans.cancel') }}</button>
                <button class="btn btn-success" wire:click="save">{{ __('finance_trans.save') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('showAdvancePaymentModal', () => {
    var modal = new bootstrap.Modal(document.getElementById('advancePaymentModal'));
    modal.show();
});
window.addEventListener('hideAdvancePaymentModal', () => {
    var modal = bootstrap.Modal.getInstance(document.getElementById('advancePaymentModal'));
    modal.hide();
});
</script>
