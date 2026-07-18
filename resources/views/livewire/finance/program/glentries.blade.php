<div class="container-fluid py-3" dir="rtl">

    {{-- ✅ التنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 🔹 رأس الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-info mb-0">
            <i class="mdi mdi-book-account-outline me-2"></i> {{ __('glaccounts_trans.journal_entries') }}
        </h3>
        <button class="btn btn-success px-4" wire:click="startNewEntry">
            <i class="mdi mdi-plus"></i> {{ __('glaccounts_trans.add_entry') }}
        </button>
    </div>

    <!-- 🔹 فورم إضافة قيد جديد -->
    @if($showForm)
    <div class="card border-0 shadow-lg mb-4">
        <div class="card-header bg-gradient bg-primary text-white">
            <h5 class="mb-0">{{ __('glaccounts_trans.add_entry') }}</h5>
        </div>
        <div class="card-body">
            <!-- بيانات رأس القيد -->
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.date') }}</label>
                    <input type="date" class="form-control" wire:model="date">
                    @error('date') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.reference_type') }}</label>
                    <input type="text" class="form-control" wire:model="reference_type" placeholder="فاتورة / إيصال ...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.reference_id') }}</label>
                    <input type="number" class="form-control" wire:model="reference_id">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.description') }}</label>
                    <input type="text" class="form-control" wire:model="description">
                </div>
            </div>

            <hr>

            <!-- 🔹 إدخال الأسطر -->
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('glaccounts_trans.account') }}</label>
                    <input type="text" class="form-control" wire:model.debounce.500ms="searchAccount" placeholder="🔍 كود أو اسم الحساب">
                    <select wire:model="selected_account" class="form-select mt-1">
                        <option value="">{{ __('glaccounts_trans.choose_account') }}</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->fullName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('glaccounts_trans.debit') }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model="line_debit">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('glaccounts_trans.credit') }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model="line_credit">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.cost_center') }}</label>
                    <input type="text" class="form-control" wire:model="line_cost_center" placeholder="{{ __('glaccounts_trans.optional') }}">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary w-100 mt-4" wire:click="addLine">
                        <i class="mdi mdi-plus-circle"></i>
                    </button>
                </div>
            </div>

            <!-- 🔹 جدول الأسطر -->
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('glaccounts_trans.account') }}</th>
                            <th>{{ __('glaccounts_trans.debit') }}</th>
                            <th>{{ __('glaccounts_trans.credit') }}</th>
                            <th>{{ __('glaccounts_trans.cost_center') }}</th>
                            <th>{{ __('glaccounts_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lines as $i => $line)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $line['account_name'] }}</td>
                            <td class="text-success fw-bold">{{ number_format($line['debit'], 2) }}</td>
                            <td class="text-danger fw-bold">{{ number_format($line['credit'], 2) }}</td>
                            <td>{{ $line['cost_center'] ?? '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" wire:click="removeLine({{ $i }})">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">{{ __('glaccounts_trans.no_lines') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="2">{{ __('glaccounts_trans.totals') }}</th>
                            <th>{{ number_format($total_debit,2) }}</th>
                            <th>{{ number_format($total_credit,2) }}</th>
                            <th colspan="2">
                                @if($total_debit === $total_credit && $total_debit > 0)
                                    <span class="badge bg-success">{{ __('glaccounts_trans.balanced') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('glaccounts_trans.not_balanced') }}</span>
                                @endif
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-secondary" wire:click="cancelEntry">{{ __('glaccounts_trans.cancel') }}</button>
                <button class="btn btn-success px-4" wire:click="saveEntry" @if($total_debit !== $total_credit) disabled @endif>
                    <i class="mdi mdi-content-save"></i> {{ __('glaccounts_trans.save_entry') }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- 🔹 جدول عرض القيود المحفوظة -->
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">{{ __('glaccounts_trans.existing_entries') }}</div>
        <div class="card-body p-0">
            <!-- 🔍 الفلاتر والبحث -->
<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">{{ __('glaccounts_trans.search') }}</label>
                <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="ابحث بالكود / الوصف / المرجع">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('glaccounts_trans.from_date') }}</label>
                <input type="date" class="form-control" wire:model="from_date">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('glaccounts_trans.to_date') }}</label>
                <input type="date" class="form-control" wire:model="to_date">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('glaccounts_trans.status') }}</label>
                <select class="form-select" wire:model="statusFilter">
                    <option value="">{{ __('glaccounts_trans.all') }}</option>
                    <option value="draft">{{ __('glaccounts_trans.statuses.draft') }}</option>
                    <option value="posted">{{ __('glaccounts_trans.statuses.posted') }}</option>
                    <option value="cancelled">{{ __('glaccounts_trans.statuses.cancelled') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('glaccounts_trans.date') }}</th>
                        <th>{{ __('glaccounts_trans.description') }}</th>
                        <th>{{ __('glaccounts_trans.total') }}</th>
                        <th>{{ __('glaccounts_trans.user') }}</th>
                        <th>{{ __('glaccounts_trans.status') }}</th>
                        <th>{{ __('glaccounts_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($entry->date)->format('Y-m-d') }}</td>
                        <td>{{ $entry->description }}</td>
                        <td class="fw-bold">{{ number_format($entry->lines->sum('debit'),2) }}</td>
                        <td>{{ $entry->user?->name }}</td>
                        <td>
                            <span class="badge bg-{{ $entry->status == 'posted' ? 'success' : ($entry->status=='draft'?'warning':'danger') }}">
                                {{ __('glaccounts_trans.statuses.' . $entry->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info"
                                wire:click="showDetails({{ $entry->id }})"
                                data-bs-toggle="modal"
                                data-bs-target="#entryDetailsModal">
                                <i class="mdi mdi-eye"></i> {{ __('glaccounts_trans.details') }}
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">{{ __('glaccounts_trans.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2">
            {{ $entries->links() }}
        </div>
    </div>

    <!-- 🔹 مودال تفاصيل القيد -->
    <div wire:ignore.self class="modal fade" id="entryDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                @if($selectedEntry)
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        {{ __('glaccounts_trans.details') }} #{{ $selectedEntry->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>{{ __('glaccounts_trans.date') }}:</strong> {{ \Carbon\Carbon::parse($selectedEntry->date)->format('Y-m-d') }}</div>
                        <div class="col-md-3"><strong>{{ __('glaccounts_trans.reference_type') }}:</strong> {{ $selectedEntry->reference_type }}</div>
                        <div class="col-md-3"><strong>{{ __('glaccounts_trans.reference_id') }}:</strong> {{ $selectedEntry->reference_id }}</div>
                        <div class="col-md-3"><strong>{{ __('glaccounts_trans.user') }}:</strong> {{ $selectedEntry->user?->name }}</div>
                    </div>
                    <p><strong>{{ __('glaccounts_trans.description') }}:</strong> {{ $selectedEntry->description }}</p>

                    <hr>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('glaccounts_trans.account') }}</th>
                                <th>{{ __('glaccounts_trans.debit') }}</th>
                                <th>{{ __('glaccounts_trans.credit') }}</th>
                                <th>{{ __('glaccounts_trans.cost_center') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedEntry->lines as $line)
                            <tr>
                                <td>{{ $line->account?->fullName() }}</td>
                                <td class="text-success fw-bold">{{ number_format($line->debit,2) }}</td>
                                <td class="text-danger fw-bold">{{ number_format($line->credit,2) }}</td>
                                <td>{{ $line->cost_center ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <th>{{ __('glaccounts_trans.totals') }}</th>
                                <th>{{ number_format($selectedEntry->lines->sum('debit'),2) }}</th>
                                <th>{{ number_format($selectedEntry->lines->sum('credit'),2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
