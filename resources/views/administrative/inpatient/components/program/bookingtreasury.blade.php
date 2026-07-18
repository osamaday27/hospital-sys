<!-- Section: Payment Information -->
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="bx bx-credit-card"></i> {{ trans('inpatient_trans.payment_section') }}
        </div>
        <div class="card-body">
            <div class="row">
                <!-- المبلغ المدفوع -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-dollar-circle"></i> {{ trans('inpatient_trans.paid_amount') }}
                        </label>
                        <input type="number" step="0.01" name="amount_paid" class="form-control" required>
                    </div>
                </div>

                <!-- طريقة الدفع -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-wallet"></i> {{ trans('inpatient_trans.payment_method') }}
                        </label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">{{ trans('inpatient_trans.cash') }}</option>
                            <option value="credit_card">{{ trans('inpatient_trans.credit_card') }}</option>
                            <option value="bank_transfer">{{ trans('inpatient_trans.bank_transfer') }}</option>
                            <option value="insurance">{{ trans('inpatient_trans.insurance') }}</option>
                        </select>
                    </div>
                </div>

                <!-- ملاحظات -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bx bx-note"></i> {{ trans('inpatient_trans.payment_notes') }}
                        </label>
                        <textarea name="payment_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
