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
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.from_date') }}</label>
                    <input type="date" class="form-control" wire:model="from_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.to_date') }}</label>
                    <input type="date" class="form-control" wire:model="to_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.status') }}</label>
                    <select class="form-select" wire:model="statusFilter">
                        <option value="">{{ __('glaccounts_trans.all') }}</option>
                        <option value="draft">{{ __('glaccounts_trans.statuses.draft') }}</option>
                        <option value="posted">{{ __('glaccounts_trans.statuses.posted') }}</option>
                        <option value="cancelled">{{ __('glaccounts_trans.statuses.cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="loadData" class="btn btn-primary w-100">
                        <i class="mdi mdi-magnify"></i> {{ __('glaccounts_trans.search') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الميزان -->
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
                        $sumDebit = 0;
                        $sumCredit = 0;
                    @endphp

                    @forelse($balances as $row)
                        @php
                            $sumDebit += $row['debit'];
                            $sumCredit += $row['credit'];
                        @endphp
                        <tr>
                            <td>{{ $row['code'] }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td><span class="badge bg-{{ $row['type_color'] }}">{{ $row['type'] }}</span></td>
                            <td class="text-success fw-bold text-end">{{ number_format($row['debit'],2) }}</td>
                            <td class="text-danger fw-bold text-end">{{ number_format($row['credit'],2) }}</td>
                            <td class="fw-bold text-end">{{ number_format($row['balance'],2) }}</td>
                            <td>
                                <span class="badge bg-{{ $row['balance_type'] == 'مدين' ? 'success' : 'danger' }}">
                                    {{ $row['balance_type'] }}
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
                        <td class="text-success">{{ number_format($sumDebit,2) }}</td>
                        <td class="text-danger">{{ number_format($sumCredit,2) }}</td>
                        <td colspan="2">
                            @if($sumDebit == $sumCredit)
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
