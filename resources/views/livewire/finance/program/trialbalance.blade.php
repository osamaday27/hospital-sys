<div class="container-fluid py-3" dir="rtl">

    <!-- رأس الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0">
            <i class="mdi mdi-scale-balance me-2"></i> {{ __('glaccounts_trans.trial_balance') }}
        </h3>
        <div>
            <button class="btn btn-outline-success me-2" onclick="window.print()">
                <i class="mdi mdi-printer"></i> {{ __('glaccounts_trans.print') }}
            </button>
            <button class="btn btn-outline-info me-2">
                <i class="mdi mdi-file-excel"></i> {{ __('glaccounts_trans.export_excel') }}
            </button>
            <button class="btn btn-outline-danger">
                <i class="mdi mdi-file-pdf-box"></i> {{ __('glaccounts_trans.export_pdf') }}
            </button>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('glaccounts_trans.from_date') }}</label>
                    <input type="date" class="form-control" wire:model="from_date">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('glaccounts_trans.to_date') }}</label>
                    <input type="date" class="form-control" wire:model="to_date">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button wire:click="loadData" class="btn btn-primary w-100">
                        <i class="mdi mdi-magnify"></i> {{ __('glaccounts_trans.search') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول ميزان المراجعة -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>{{ __('glaccounts_trans.code') }}</th>
                        <th>{{ __('glaccounts_trans.account') }}</th>
                        <th>{{ __('glaccounts_trans.type') }}</th>
                        <th>{{ __('glaccounts_trans.debit') }}</th>
                        <th>{{ __('glaccounts_trans.credit') }}</th>
                        <th>{{ __('glaccounts_trans.balance') }}</th>
                        <th>{{ __('glaccounts_trans.balance_type') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp
                    @forelse($balances as $bal)
                        @php
                            $totalDebit += $bal['debit'];
                            $totalCredit += $bal['credit'];
                        @endphp
                        <tr>
                            <td>{{ $bal['code'] }}</td>
                            <td>{{ $bal['name'] }}</td>
                            <td><span class="badge bg-{{ $bal['type_color'] ?? 'secondary' }}">{{ $bal['type'] }}</span></td>
                            <td class="text-success fw-bold text-end">{{ number_format($bal['debit'],2) }}</td>
                            <td class="text-danger fw-bold text-end">{{ number_format($bal['credit'],2) }}</td>
                            <td class="fw-bold text-end">{{ number_format($bal['balance'],2) }}</td>
                            <td>
                                <span class="badge bg-{{ $bal['balance_type'] == 'مدين' ? 'success' : 'danger' }}">
                                    {{ $bal['balance_type'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">{{ __('glaccounts_trans.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-secondary fw-bold text-center">
                    <tr>
                        <td colspan="3">{{ __('glaccounts_trans.totals') }}</td>
                        <td class="text-success">{{ number_format($totalDebit,2) }}</td>
                        <td class="text-danger">{{ number_format($totalCredit,2) }}</td>
                        <td colspan="2">
                            @if($totalDebit == $totalCredit)
                                <span class="badge bg-success">{{ __('glaccounts_trans.balanced') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('glaccounts_trans.not_balanced') }}</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
