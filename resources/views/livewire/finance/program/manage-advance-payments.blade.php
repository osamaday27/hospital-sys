<div class="container-fluid py-4" dir="rtl">
    {{-- رسائل --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($patient)
        {{-- بيانات المريض --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-gradient-primary text-white p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="mdi mdi-account me-2"></i> {{ $patient->full_name }}</h5>
                @if($booking)
                    <span class="badge bg-warning text-dark"><i class="mdi mdi-hospital-building me-1"></i> حجز #{{ $booking->id }}</span>
                @endif
            </div>
            <div class="card-body row text-center">
                <div class="col-md-4">
                    <span class="fw-bold text-muted"><i class="mdi mdi-card-account-details-outline me-1"></i> رقم الملف</span>
                    <div class="text-dark">{{ $patient->medical_number }}</div>
                </div>
                <div class="col-md-4">
                    <span class="fw-bold text-muted"><i class="mdi mdi-card-account-details me-1"></i> الرقم القومي</span>
                    <div class="text-dark">{{ $patient->national_id ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <span class="fw-bold text-muted"><i class="mdi mdi-cash me-1"></i> سعر العملية</span>
                    <div class="badge bg-success">{{ number_format($package_price,2) }} {{ $currency }}</div>
                </div>
            </div>
        </div>

        {{-- تفاصيل التعاقد + الملخص --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-gradient-info text-white p-3">
                <h5 class="mb-0"><i class="mdi mdi-file-document me-2"></i> {{ __('finance_trans.contract_details') }}</h5>
            </div>
            <div class="card-body row text-center">
                <div class="col-md-3">
                    <span class="fw-bold text-muted">{{ __('finance_trans.contract_name') }}</span>
                    <div class="badge bg-primary">{{ $contract_name }}</div>
                </div>
                <div class="col-md-3">
                    <span class="fw-bold text-muted">{{ __('finance_trans.package_price') }}</span>
                    <div class="badge bg-success">{{ number_format($total_net_price,2) }} {{ $currency }}</div>
                </div>
                <div class="col-md-3">
                    <span class="fw-bold text-muted">نصيب المريض</span>
                    <div class="badge bg-warning text-dark">{{ number_format($total_patient_amount,2) }} {{ $currency }}</div>
                </div>
                <div class="col-md-3">
                    <span class="fw-bold text-muted">نصيب جهة التعاقد</span>
                    <div class="badge bg-secondary">{{ number_format($total_insurer_amount,2) }} {{ $currency }}</div>
                </div>
            </div>
        </div>

        {{-- تسعير العمليات تفصيلياً --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-gradient-light p-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="mdi mdi-format-list-bulleted me-2"></i> تفاصيل الأسعار حسب العمليات</h5>
                <small class="text-muted">العملة: {{ $currency }}</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>العملية</th>
                                <th>السعر قبل الخصم</th>
                                <th>الخصم %</th>
                                <th>الصافي</th>
                                <th>مساهمة المريض %</th>
                                <th>مبلغ المريض</th>
                                <th>جهة التعاقد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servicesPricing as $idx => $row)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="text-start">{{ $row['service_name'] }}</td>
                                    <td><span class="badge bg-outline-secondary  text-dark">{{ number_format($row['gross_price'],2) }} {{ $currency }}</span></td>
                                    <td><span class="badge bg-outline-info  text-dark">{{ rtrim(rtrim(number_format($row['discount_percentage'],2,'.',''), '0'), '.') }}%</span></td>
                                    <td><span class="badge bg-outline-success  text-dark">{{ number_format($row['net_price'],2) }} {{ $currency }}</span></td>
                                    <td><span class="badge bg-outline-warning text-dark">{{ rtrim(rtrim(number_format($row['patient_percentage'],2,'.',''), '0'), '.') }}%</span></td>
                                    <td><span class="badge bg-outline-primary  text-dark">{{ number_format($row['patient_amount'],2) }} {{ $currency }}</span></td>
                                    <td><span class="badge bg-outline-dark  text-dark">{{ number_format($row['insurer_amount'],2) }} {{ $currency }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 text-muted">لا توجد عمليات مُسعّرة للعقد الحالي.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($servicesPricing))
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">الإجمالي:</th>
                                    <th class="text-dark">{{ number_format($total_gross_price,2) }} {{ $currency }}</th>
                                    <th class="text-dark">
                                        {{-- متوسط خصم مرجّح للعرض --}}
                                        {{ rtrim(rtrim(number_format($discount_percentage,2,'.',''), '0'), '.') }}%
                                    </th >
                                    <th class="text-dark">{{ number_format($total_net_price,2) }} {{ $currency }}</th>
                                    <th class="text-dark">
                                        {{ rtrim(rtrim(number_format($patient_contribution,2,'.',''), '0'), '.') }}%
                                    </th>
                                    <th class="text-dark">{{ number_format($total_patient_amount,2) }} {{ $currency }}</th>
                                    <th class="text-dark">{{ number_format($total_insurer_amount,2) }} {{ $currency }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- إضافة دفعة مقدمة --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-gradient-success text-white p-3">
                <h5 class="mb-0 d-flex align-items-center"><i class="mdi mdi-cash-multiple me-2"></i> {{ __('finance_trans.add_advance_payment') }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save" class="row g-3">
                    {{-- المبلغ --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><i class="mdi mdi-cash me-1"></i> {{ __('finance_trans.amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $currency }}</span>
                            <input type="number" step="0.01" class="form-control" wire:model="amount">
                        </div>
                        @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- طريقة الدفع --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><i class="mdi mdi-credit-card me-1"></i> {{ __('finance_trans.payment_method') }}</label>
                        <select class="form-select" wire:model="payment_method">
                            <option value="cash">{{ __('finance_trans.cash') }}</option>
                            <option value="visa">{{ __('finance_trans.visa') }}</option>
                            <option value="bank_transfer">{{ __('finance_trans.bank_transfer') }}</option>
                        </select>
                        @error('payment_method') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- تاريخ الدفع --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><i class="mdi mdi-calendar me-1"></i> {{ __('finance_trans.payment_date') }}</label>
                        <input type="date" class="form-control" wire:model="payment_date">
                        @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- تفاصيل الفيزا --}}
                    @if($payment_method === 'visa')
                        <div class="col-12 bg-light p-3 rounded border">
                            <h6 class="fw-bold text-dark"><i class="mdi mdi-credit-card"></i> {{ __('finance_trans.visa_details') }}</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" wire:model="card_number" placeholder="{{ __('finance_trans.card_number') }} (آخر 4 أرقام)">
                                    @error('card_number') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" wire:model="card_bank" placeholder="{{ __('finance_trans.card_bank') }}">
                                    @error('card_bank') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('finance_trans.choose_machine') }}</label>
                                    <select class="form-select" wire:model="bank_machine_id">
                                        <option value="">{{ __('finance_trans.choose_machine') }}</option>
                                        @foreach($bankMachines as $machine)
                                            <option value="{{ $machine->id }}">{{ $machine->machine_code }} - {{ $machine->location }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_machine_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- تفاصيل التحويل البنكي --}}
                    @if($payment_method === 'bank_transfer')
                        <div class="col-12 bg-light p-3 rounded border">
                            <h6 class="fw-bold text-dark"><i class="mdi mdi-bank"></i> {{ __('finance_trans.bank_transfer_details') }}</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" wire:model="transfer_number" placeholder="{{ __('finance_trans.transfer_number') }}">
                                    @error('transfer_number') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" wire:model="transfer_bank" placeholder="{{ __('finance_trans.transfer_bank') }}">
                                    @error('transfer_bank') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('finance_trans.choose_bank') }}</label>
                                    <select class="form-select" wire:model="bank_id">
                                        <option value="">{{ __('finance_trans.choose_bank') }}</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->name_ar }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- الخزنة والملاحظات --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('finance_trans.locker') }}</label>
                        <select class="form-select" wire:model="locker_id">
                            <option value="">{{ __('finance_trans.choose_locker') }}</option>
                            @foreach($lockers as $locker)
                                <option value="{{ $locker->id }}">{{ $locker->name_ar }}</option>
                            @endforeach
                        </select>
                        @error('locker_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('finance_trans.notes') }}</label>
                        <textarea class="form-control" wire:model="notes" rows="2" placeholder="{{ __('finance_trans.notes_placeholder') }}"></textarea>
                    </div>

                    {{-- زر الحفظ --}}
                    <div class="col-12 text-end">
                        <button class="btn btn-success px-4">
                            <i class="mdi mdi-content-save"></i> {{ __('finance_trans.save_payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- سجل الدفعات --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-info text-white p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="mdi mdi-history me-2"></i> {{ __('finance_trans.payment_history') }}</h5>
                <span class="badge bg-light text-dark">{{ $history->count() }} {{ __('finance_trans.payments') }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('finance_trans.date') }}</th>
                            <th>{{ __('finance_trans.amount') }}</th>
                            <th>{{ __('finance_trans.method') }}</th>
                            <th>{{ __('finance_trans.user') }}</th>
                            <th>{{ __('finance_trans.notes') }}</th>
                            <th>{{ __('finance_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $index => $pay)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('d/m/Y') }}</td>
                                <td><span class="badge bg-success">{{ number_format($pay->amount,2) }} {{ $pay->currency }}</span></td>
                                <td>
                                    <span class="badge bg-primary">{{ __('finance_trans.' . $pay->payment_method) }}</span>
                                    @if($pay->payment_method === 'visa')
                                        <div class="small text-muted"><i class="mdi mdi-credit-card-chip"></i> {{ $pay->card_number }} - {{ $pay->card_bank }}</div>
                                        <div class="small text-info"><i class="mdi mdi-bank-transfer"></i> {{ $pay->bankMachine->machine_code ?? '-' }}</div>
                                    @endif
                                    @if($pay->payment_method === 'bank_transfer')
                                        <div class="small text-muted"><i class="mdi mdi-bank"></i> {{ $pay->transfer_bank ?? '-' }}</div>
                                        <div class="small text-info"><i class="mdi mdi-numeric"></i> {{ $pay->transfer_number ?? '-' }}</div>
                                        <div class="small text-secondary"><i class="mdi mdi-bank-outline"></i> {{ $pay->bank->name_ar ?? '-' }}</div>
                                    @endif
                                </td>
                                <td>{{ $pay->user->name ?? '-' }}</td>
                                <td>{{ $pay->notes ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('finance.advance_payment.print',$pay->id) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                        <i class="mdi mdi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-muted">{{ __('finance_trans.no_payments') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
