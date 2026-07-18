<div class="container-fluid py-4">

    <!-- Account Selector + Balance -->
    <div class="card shadow-lg border-0 mb-4 rounded-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center bg-gradient-primary text-white rounded-4 p-4">
            <div>
                <h4 class="mb-2">
                    <i class="mdi mdi-bank-outline me-2"></i>
                    <select wire:model="account_id" class="form-select d-inline-block w-auto bg-light text-dark rounded-pill">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_number }})</option>
                        @endforeach
                    </select>
                </h4>
                <p class="mb-0">{{ $account?->iban ?? __('bank_trans.account') }}</p>
            </div>
            <div class="text-end">
                <h5 class="mb-1">{{ __('bank_trans.balance') }}</h5>
                <span class="fw-bold fs-3 badge bg-success px-3 py-2 shadow">
                    {{ number_format($summary['balance'] ?? 0,2) }} {{ $account?->currency ?? 'EGP' }}
                </span>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-4 mb-4 text-center">
        <div class="col-md-3">
            <div class="card border-0 shadow h-100 rounded-4 p-3 hover-card">
                <i class="mdi mdi-cash-plus text-success fs-1 mb-2"></i>
                <h6>{{ __('bank_trans.total_deposits') }}</h6>
                <p class="fw-bold text-success fs-5">{{ number_format($summary['total_deposits'],2) }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow h-100 rounded-4 p-3 hover-card">
                <i class="mdi mdi-cash-minus text-danger fs-1 mb-2"></i>
                <h6>{{ __('bank_trans.total_withdraws') }}</h6>
                <p class="fw-bold text-danger fs-5">{{ number_format($summary['total_withdraws'],2) }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow h-100 rounded-4 p-3 hover-card">
                <i class="mdi mdi-bank-transfer text-info fs-1 mb-2"></i>
                <h6>{{ __('bank_trans.total_transfers') }}</h6>
                <p class="fw-bold text-info fs-5">{{ number_format($summary['total_transfers'],2) }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow h-100 rounded-4 p-3 hover-card">
                <i class="mdi mdi-book-open-variant text-warning fs-1 mb-2"></i>
                <h6>{{ __('bank_trans.cheques_count') }}</h6>
                <p class="fw-bold text-warning fs-5">{{ $summary['cheques_count'] }}</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills nav-justified rounded-3 bg-light mb-4 p-2 shadow-sm" id="bankTabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#transactions">{{ __('bank_trans.bank_transactions') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#cheques">{{ __('bank_trans.cheque_workflow') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#charts">{{ __('bank_trans.charts') }}</a></li>
    </ul>

    <div class="tab-content">

        <!-- Transactions -->
        <div class="tab-pane fade show active" id="transactions">
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header bg-light d-flex justify-content-between rounded-top-4">
                    <h6 class="mb-0"><i class="mdi mdi-file-document-outline me-2"></i>{{ __('bank_trans.bank_transactions') }}</h6>
                    <div>
                        <input type="date" wire:model="from_date" class="form-control d-inline w-auto rounded-pill">
                        <input type="date" wire:model="to_date" class="form-control d-inline w-auto rounded-pill">
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>{{ __('bank_trans.date') }}</th>
                                <th>{{ __('bank_trans.type') }}</th>
                                <th>{{ __('bank_trans.amount') }}</th>
                                <th>{{ __('bank_trans.reference') }}</th>
                                <th>{{ __('bank_trans.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                                <tr>
                                    <td>{{ $txn->id }}</td>
                                    <td>{{ $txn->transaction_date }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bank_trans.'.$txn->type) }}</span></td>
                                    <td class="fw-bold">{{ number_format($txn->amount,2) }}</td>
                                    <td>{{ $txn->reference }}</td>
                                    <td>{{ $txn->notes }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">{{ __('bank_trans.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>

        <!-- Cheques -->
        <div class="tab-pane fade" id="cheques">
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header bg-light rounded-top-4">
                    <h6 class="mb-0"><i class="mdi mdi-book-open-page-variant me-2"></i>{{ __('bank_trans.cheque_workflow') }}</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>{{ __('bank_trans.cheque_number') }}</th>
                                <th>{{ __('bank_trans.payee') }}</th>
                                <th>{{ __('bank_trans.amount') }}</th>
                                <th>{{ __('bank_trans.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cheques as $chq)
                                <tr>
                                    <td>{{ $chq->id }}</td>
                                    <td>{{ $chq->cheque_number }}</td>
                                    <td>{{ $chq->payee }}</td>
                                    <td>{{ number_format($chq->amount,2) }}</td>
                                    <td><span class="badge bg-info">{{ __('bank_trans.'.$chq->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">{{ __('bank_trans.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $cheques->links() }}
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="tab-pane fade" id="charts">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-light rounded-top-4">
                    <h6 class="mb-0"><i class="mdi mdi-chart-line me-2"></i>{{ __('bank_trans.charts') }}</h6>
                </div>
                <div class="card-body">
                    <canvas id="transactionsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("livewire:load", () => {
    const ctx = document.getElementById('transactionsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['{{ __("bank_trans.total_deposits") }}', '{{ __("bank_trans.total_withdraws") }}', '{{ __("bank_trans.total_transfers") }}'],
            datasets: [{
                label: '{{ __("bank_trans.amounts") }}',
                data: [{{ $summary['total_deposits'] }}, {{ $summary['total_withdraws'] }}, {{ $summary['total_transfers'] }}],
                backgroundColor: ['#28a745','#dc3545','#17a2b8']
            }]
        }
    });
});
</script>

<style>
.hover-card:hover {
    transform: translateY(-5px);
    transition: 0.3s ease;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15) !important;
}
</style>
