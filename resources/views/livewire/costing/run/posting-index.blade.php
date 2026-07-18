<div class="container-fluid py-3" dir="rtl" wire:ignore.self>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-info">
                <i class="mdi mdi-account-cash-outline me-2"></i> {{ __('costing_trans.title_costing_posting') }}
            </h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">{{ __('costing_trans.back') }}</a></li>
                <li class="breadcrumb-item active">{{ __('costing_trans.title') }}</li>
            </ol>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label">{{ __('costing_trans.cost_batch') }}</label>
                    <select class="form-select" wire:model="selected_cost_batch_id">
                        <option value="">{{ __('costing_trans.select_cost_batch') }}</option>
                        @foreach($batches as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->code }} — {{ optional($b->period_start)->format('Y-m-d') }}–{{ optional($b->period_end)->format('Y-m-d') }} ({{ $b->status }})
                            </option>
                        @endforeach
                    </select>
                    @error('selected_cost_batch_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('costing_trans.notes') }}</label>
                    <input type="text" class="form-control" wire:model.defer="notes" placeholder="{{ __('costing_trans.notes_ph') }}">
                </div>

                <div class="col-md-2 text-end">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-primary w-100" wire:click="postToGl" @disabled(!$selected_cost_batch_id)>
                        <i class="mdi mdi-upload me-1"></i> {{ __('costing_trans.post_to_gl') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <label class="form-label">{{ __('costing_trans.search') }}</label>
                    <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="{{ __('costing_trans.search_ph') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.status') }}</label>
                    <select class="form-select" wire:model="status">
                        <option value="">{{ __('costing_trans.all') }}</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}">{{ __('costing_trans.st_'.$s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.per_page') }}</label>
                    <select class="form-select" wire:model="perPage">
                        @foreach([10,20,50,100] as $pp)
                            <option value="{{ $pp }}">{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('costing_trans.batch_code') }}</th>
                        <th>{{ __('costing_trans.period') }}</th>
                        <th>{{ __('costing_trans.status') }}</th>
                        <th class="text-end">{{ __('costing_trans.total_debit') }}</th>
                        <th class="text-end">{{ __('costing_trans.total_credit') }}</th>
                        <th>{{ __('costing_trans.entries_count') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td>{{ $rows->firstItem() + $i }}</td>
                            <td><span class="badge bg-secondary">{{ $row->code }}</span></td>
                            <td>{{ optional($row->period_start)->format('Y-m-d') }} — {{ optional($row->period_end)->format('Y-m-d') }}</td>
                            <td>
                                @if($row->status==='posted')
                                    <span class="badge bg-success">{{ __('costing_trans.st_posted') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('costing_trans.st_draft') }}</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($row->total_debit,2) }}</td>
                            <td class="text-end">{{ number_format($row->total_credit,2) }}</td>
                            <td>{{ $row->entries_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">{{ __('costing_trans.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $rows->links() }}
            </div>
        </div>
    </div>

</div>
