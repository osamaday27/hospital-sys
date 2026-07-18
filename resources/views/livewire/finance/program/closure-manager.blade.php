<div class="container-fluid py-3">

    {{-- ✅ تنبيهات --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔹 رأس الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 text-primary">
            <i class="mdi mdi-lock-outline me-2"></i> {{ __('glaccounts_trans.title') }}
        </h4>
        <div>
            <button class="btn btn-outline-danger btn-sm me-2">
                <i class="mdi mdi-file-pdf"></i> {{ __('glaccounts_trans.export_pdf') }}
            </button>
            <button class="btn btn-outline-success btn-sm">
                <i class="mdi mdi-file-excel"></i> {{ __('glaccounts_trans.export_excel') }}
            </button>
        </div>
    </div>

    {{-- 🆕 إضافة إقفال جديد --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">
            <i class="mdi mdi-plus-circle-outline me-1"></i> {{ __('glaccounts_trans.add_new') }}
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.period_start') }}</label>
                    <input type="date" wire:model="period_start" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.period_end') }}</label>
                    <input type="date" wire:model="period_end" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.type') }}</label>
                    <select wire:model="type" class="form-select">
                        <option value="">{{ __('glaccounts_trans.type') }}</option>
                        <option value="monthly">{{ __('glaccounts_trans.monthly') }}</option>
                        <option value="yearly">{{ __('glaccounts_trans.yearly') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button wire:click="createClosure" class="btn btn-primary w-100">
                        <i class="mdi mdi-lock-check"></i> {{ __('glaccounts_trans.create_btn') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔎 فلاتر البحث --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">
            <i class="mdi mdi-filter-outline me-1"></i> {{ __('glaccounts_trans.filters') }}
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.period_start') }}</label>
                    <input type="date" wire:model="filter_start" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.period_end') }}</label>
                    <input type="date" wire:model="filter_end" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('glaccounts_trans.type') }}</label>
                    <select wire:model="filter_type" class="form-select">
                        <option value="">{{ __('glaccounts_trans.type') }}</option>
                        <option value="monthly">{{ __('glaccounts_trans.monthly') }}</option>
                        <option value="yearly">{{ __('glaccounts_trans.yearly') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button wire:click="searchClosures" class="btn btn-info flex-fill">
                        <i class="mdi mdi-magnify"></i> {{ __('glaccounts_trans.search') }}
                    </button>
                    <button wire:click="resetFilters" class="btn btn-secondary flex-fill">
                        <i class="mdi mdi-refresh"></i> {{ __('glaccounts_trans.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 📋 قائمة الإقفالات --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">
            <i class="mdi mdi-format-list-bulleted me-1"></i> {{ __('glaccounts_trans.list') }}
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>{{ __('glaccounts_trans.id') }}</th>
                        <th>{{ __('glaccounts_trans.period') }}</th>
                        <th>{{ __('glaccounts_trans.type') }}</th>
                        <th>{{ __('glaccounts_trans.status') }}</th>
                        <th>{{ __('glaccounts_trans.total_debit') }}</th>
                        <th>{{ __('glaccounts_trans.total_credit') }}</th>
                        <th>{{ __('glaccounts_trans.created_by') }}</th>
                        <th>{{ __('glaccounts_trans.approved_by') }}</th>
                        <th>{{ __('glaccounts_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($closures as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->period_start }} → {{ $c->period_end }}</td>
                            <td>
                                <span class="badge bg-gradient-info">{{ $c->type == 'monthly' ? __('glaccounts_trans.monthly') : __('glaccounts_trans.yearly') }}</span>
                            </td>
                            <td>
                                @if($c->status == 'closed')
                                    <span class="badge bg-success">{{ __('glaccounts_trans.closed') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('glaccounts_trans.open') }}</span>
                                @endif
                            </td>
                            <td class="text-success fw-bold">{{ number_format($c->lines->sum('debit'), 2) }}</td>
                            <td class="text-danger fw-bold">{{ number_format($c->lines->sum('credit'), 2) }}</td>
                            <td>{{ $c->created_by }}</td>
                            <td>{{ $c->approved_by ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" title="{{ __('glaccounts_trans.details') }}">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" title="{{ __('glaccounts_trans.approve') }}">
                                        <i class="mdi mdi-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted text-center">لا توجد بيانات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
