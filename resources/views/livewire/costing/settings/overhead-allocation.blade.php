<div class="container-fluid py-3" dir="rtl" wire:ignore.self>
    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h4 class="mb-0 text-info">
                <i class="mdi mdi-transit-connection-variant me-2"></i> {{ __('costing_trans.overhead_allocation') }}
            </h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">{{ __('costing_trans.settings') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('costing_trans.overhead_allocation') }}</li>
            </ol>
        </div>
    </div>

    {{-- Controls --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('costing_trans.pool') }} <span
                            class="text-danger">*</span></label>
                    <select class="form-select" wire:model="pool_id">
                        <option value="">{{ __('costing_trans.select_pool') }}</option>
                        @foreach ($pools as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->code }} — {{ $p->getTranslation('name', app()->getLocale()) }}
                                ({{ optional($p->period_start)->format('Y-m-d') }} —
                                {{ optional($p->period_end)->format('Y-m-d') }})
                                [{{ number_format($p->amount, 2) }}]
                            </option>
                        @endforeach
                    </select>
                    @error('pool_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('costing_trans.method') }} <span
                            class="text-danger">*</span></label>
                    <select class="form-select" wire:model="method">
                        <option value="percent">{{ __('costing_trans.method_percent') }}</option>
                        <option value="driver">{{ __('costing_trans.method_driver') }}</option>
                    </select>
                    @error('method')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-3" @if ($method !== 'driver') style="display:none" @endif>
                    <label class="form-label">{{ __('costing_trans.driver_code') }}</label>
                    <input type="text" class="form-control" wire:model.defer="driver_code"
                        placeholder="{{ __('costing_trans.driver_placeholder') }}">
                    @error('driver_code')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('costing_trans.notes') }}</label>
                    <input type="text" class="form-control" wire:model.defer="notes">
                    @error('notes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('costing_trans.pool_amount') }}</label>
                    <input type="text" class="form-control" value="{{ number_format($pool_amount, 2) }}" readonly>
                </div>
                <div class="col-md-5">
                    <label class="form-label">{{ __('costing_trans.period') }}</label>
                    <input type="text" class="form-control" value="{{ $pool_period }}" readonly>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-outline-secondary" wire:click="prepareLines" @disabled(!$pool_id)>
                        <i class="mdi mdi-reload"></i> {{ __('costing_trans.load_centers') }}
                    </button>
                    <button class="btn btn-outline-info" wire:click="normalizePercent" @disabled($method !== 'percent')>
                        <i class="mdi mdi-scale-balance"></i> {{ __('costing_trans.normalize_percent') }}
                    </button>
                    <button class="btn btn-primary" wire:click="recalculate" @disabled(!$pool_id)>
                        <i class="mdi mdi-calculator-variant-outline"></i> {{ __('costing_trans.recalculate') }}
                    </button>

                </div>
            </div>
        </div>
    </div>

    {{-- Lines Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('costing_trans.center_code') }}</th>
                        <th>{{ __('costing_trans.center_name') }}</th>
                        <th class="text-center" style="width:160px">
                            @if ($method === 'percent')
                                {{ __('costing_trans.percent') }} (%)
                            @else
                                {{ __('costing_trans.driver_value') }}
                            @endif
                        </th>
                        <th class="text-end" style="width:180px">{{ __('costing_trans.allocated_amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lines as $i => $ln)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><span class="badge bg-secondary">{{ $ln['code'] }}</span></td>
                            <td>{{ $ln['name'] }}</td>
                            <td class="text-center">
                                @if ($method === 'percent')
                                    <input type="number" step="0.0001" class="form-control text-center"
                                        wire:model.lazy="lines.{{ $i }}.percent" wire:change="recalculate">
                                @else
                                    <input type="number" step="0.0001" class="form-control text-center"
                                        wire:model.lazy="lines.{{ $i }}.driver_value"
                                        wire:change="recalculate">
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($ln['allocated'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="mdi mdi-information-outline me-2"></i> {{ __('costing_trans.no_lines') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($lines))
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">{{ __('costing_trans.totals') }}</th>
                            <th class="text-center">
                                @if ($method === 'percent')
                                    {{ number_format(collect($lines)->sum(fn($x) => (float) ($x['percent'] ?? 0)), 4) }}%
                                @else
                                    {{ number_format(collect($lines)->sum(fn($x) => (float) ($x['driver_value'] ?? 0)), 4) }}
                                @endif
                            </th>
                            <th class="text-end">{{ number_format(collect($lines)->sum('allocated'), 2) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-success" wire:click="saveRule" @disabled(!$pool_id)>
                    <i class="mdi mdi-content-save-outline me-1"></i> {{ __('costing_trans.save_rule') }}
                </button>
            </div>

        </div>
    </div>

    {{-- Past rules --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ __('costing_trans.previous_rules') }}</h6>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control"
                        placeholder="{{ __('costing_trans.search_rules') }}" wire:model.debounce.500ms="search">
                    <select class="form-select" wire:model="perPage" style="width:120px">
                        @foreach ([10, 20, 30, 50, 100] as $pp)
                            <option value="{{ $pp }}">{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('costing_trans.pool') }}</th>
                        <th>{{ __('costing_trans.method') }}</th>
                        <th>{{ __('costing_trans.driver_code') }}</th>
                        <th class="text-end">{{ __('costing_trans.total_amount') }}</th>
                        <th>{{ __('costing_trans.status') }}</th>
                        <th>{{ __('costing_trans.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $i => $r)
                        <tr>
                            <td>{{ $rules->firstItem() + $i }}</td>
                            <td>
                                <span class="text-muted">{{ $r->pool?->code }}</span> —
                                {{ $r->pool?->getTranslation('name', app()->getLocale()) }}
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    {{ $r->method === 'percent' ? __('costing_trans.method_percent') : __('costing_trans.method_driver') }}
                                </span>
                            </td>
                            <td>{{ $r->driver_code ?: '—' }}</td>
                            <td class="text-end">{{ number_format($r->total_amount, 2) }}</td>
                            <td>
                                @if ($r->status === 'draft')
                                    <span class="badge bg-secondary">{{ __('costing_trans.status_draft') }}</span>
                                @elseif($r->status === 'approved')
                                    <span class="badge bg-success">{{ __('costing_trans.status_approved') }}</span>
                                @else
                                    <span class="badge bg-primary">{{ __('costing_trans.status_posted') }}</span>
                                @endif
                            </td>
                            <td>{{ $r->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                {{ __('costing_trans.no_rules') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $rules->links() }}
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('allocation-saved', () => {
            // ممكن تضيف Toast/إغلاق مودال مستقبلًا
        });
    </script>
</div>
