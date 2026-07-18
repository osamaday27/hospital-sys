<div  dir="rtl">
    <div class="card shadow-lg rounded-2xl">
        <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white">
            <h4 class="mb-0 text-white"><i class="mdi mdi-factory"></i> {{ __('suppliers.suppliers_accounts') }}</h4>
            <div class="d-flex gap-2">
                <input type="text" wire:model.debounce.500ms="search" class="form-control form-control-sm"
                       placeholder="{{ __('suppliers.search_supplier') }}">
                <select wire:model="groupFilter" class="form-select form-select-sm">
                    <option value="">{{ __('suppliers.all_groups') }}</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}">{{ $grp->name_ar }}</option>
                    @endforeach
                </select>
                <input type="date" wire:model="dateFrom" class="form-control form-control-sm">
                <input type="date" wire:model="dateTo" class="form-control form-control-sm">
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover text-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('suppliers.name') }}</th>
                        <th>{{ __('suppliers.group') }}</th>
                        <th>{{ __('suppliers.total_invoices') }}</th>
                        <th>{{ __('suppliers.total_payments') }}</th>
                        <th>{{ __('suppliers.opening_balance') }}</th>
                        <th>{{ __('suppliers.additions') }}</th>
                        <th>{{ __('suppliers.discounts') }}</th>
                        <th>{{ __('suppliers.final_balance') }}</th>
                        <th>{{ __('suppliers.last_invoice_date') }}</th>
                        <th>{{ __('suppliers.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        @php
                            $summary = $this->calculateSummary($supplier->id);
                            $lastInvoice = $supplier->invoices()
                                ->orderBy('invoice_date','desc')
                                ->value('invoice_date');
                        @endphp
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>{{ $supplier->name_ar }}</td>
                            <td>{{ $supplier->group->name_ar ?? '-' }}</td>
                            <td class="text-end">{{ number_format($summary['invoices'], 2) }}</td>
                            <td class="text-success text-end">{{ number_format($summary['payments'], 2) }}</td>
                            <td class="text-info text-end">{{ number_format($summary['opening'], 2) }}</td>
                            <td class="text-secondary text-end">{{ number_format($summary['additions'], 2) }}</td>
                            <td class="text-danger text-end">{{ number_format($summary['discounts'], 2) }}</td>
                            <td>
                                <span class="fw-bold {{ $summary['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($summary['balance'], 2) }}
                                </span>
                            </td>
                            <td>{{ $lastInvoice ?? '-' }}</td>
                            <td>
                                <a href="{{ route('suppliers_accounts.details', $supplier->id) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="mdi mdi-eye"></i> {{ __('suppliers.view_details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">{{ __('suppliers.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</div>
