<div class="container-fluid py-3" dir="rtl" wire:ignore.self>

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert" aria-label="×"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert" aria-label="×"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-info">
                <i class="mdi mdi-clipboard-edit-outline me-2"></i> {{ __('costing_trans.titleadjustments') }}
            </h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">{{ __('costing_trans.program') }}</a></li>
                <li class="breadcrumb-item active">{{ __('costing_trans.titleadjustments') }}</li>
            </ol>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('costing_trans.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('costing_trans.search_placeholder') }}" wire:model.debounce.500ms="search">
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
                    <label class="form-label">{{ __('costing_trans.batch') }}</label>
                    <select class="form-select" wire:model="batch_id">
                        <option value="">{{ __('costing_trans.all') }}</option>
                        @foreach($batches as $b)
                            <option value="{{ $b->id }}">{{ $b->code }} — {{ optional($b->period_start)->format('Y-m-d') }}–{{ optional($b->period_end)->format('Y-m-d') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.line_type') }}</label>
                    <select class="form-select" wire:model="line_type">
                        <option value="">{{ __('costing_trans.all') }}</option>
                        @foreach($lineTypes as $lt)
                            <option value="{{ $lt }}">{{ __('costing_trans.lt_'.$lt) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.case_type') }}</label>
                    <select class="form-select" wire:model="case_type">
                        <option value="">{{ __('costing_trans.all') }}</option>
                        @foreach($caseTypes as $ct)
                            <option value="{{ $ct }}">{{ __('costing_trans.ct_'.$ct) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.from_date') }}</label>
                    <input type="date" class="form-control" wire:model="from_date">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.to_date') }}</label>
                    <input type="date" class="form-control" wire:model="to_date">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.per_page') }}</label>
                    <select class="form-select" wire:model="perPage">
                        @foreach([10,20,30,50,100] as $pp)
                            <option value="{{ $pp }}">{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 text-end">
                    <button class="btn btn-primary w-100" wire:click="create">
                        <i class="mdi mdi-plus"></i> {{ __('costing_trans.add') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('costing_trans.batch') }}</th>
                        <th>{{ __('costing_trans.effective_date') }}</th>
                        <th>{{ __('costing_trans.line_type') }}</th>
                        <th>{{ __('costing_trans.item_type') }}</th>
                        <th>{{ __('costing_trans.case_type') }}</th>
                        <th>{{ __('costing_trans.reference') }}</th>
                        <th class="text-end">{{ __('costing_trans.amount') }}</th>
                        <th>{{ __('costing_trans.status') }}</th>
                        <th class="text-end">{{ __('costing_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td>{{ $rows->firstItem() + $i }}</td>
                            <td>
                                @if($row->batch_id)
                                    <span class="badge bg-secondary">{{ $row->batch?->code }}</span>
                                @else
                                    <span class="text-muted">{{ __('costing_trans.no_batch') }}</span>
                                @endif
                            </td>
                            <td>{{ optional($row->effective_date)->format('Y-m-d') }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ __('costing_trans.lt_'.$row->line_type) }}</span></td>
                            <td>{{ __('costing_trans.it_'.$row->item_type) }}</td>
                            <td>{{ __('costing_trans.ct_'.$row->case_type) }}</td>
                            <td>{{ $row->reference }}</td>
                            <td class="text-end">
                                @if($row->adj_type==='decrease') - @endif
                                {{ number_format(abs((float)$row->amount), 2) }}
                            </td>
                            <td>
                                @if($row->status==='draft')
                                    <span class="badge bg-secondary">{{ __('costing_trans.st_draft') }}</span>
                                @elseif($row->status==='approved')
                                    <span class="badge bg-success">{{ __('costing_trans.st_approved') }}</span>
                                @else
                                    <span class="badge bg-primary">{{ __('costing_trans.st_posted') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $row->id }})" title="{{ __('costing_trans.edit') }}">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </button>

                                    @if($row->status==='draft')
                                        <button class="btn btn-sm btn-outline-warning" wire:click="approve({{ $row->id }})" title="{{ __('costing_trans.approve') }}">
                                            <i class="mdi mdi-check-decagram-outline"></i>
                                        </button>
                                    @endif

                                    @if($row->status==='approved')
                                        <button class="btn btn-sm btn-outline-info" wire:click="post({{ $row->id }})" title="{{ __('costing_trans.post') }}">
                                            <i class="mdi mdi-upload"></i>
                                        </button>
                                    @endif

                                    @if($row->status!=='posted')
                                        <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $row->id }})" title="{{ __('costing_trans.delete') }}">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">{{ __('costing_trans.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">{{ $rows->links() }}</div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="adjModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        @if($editing_id) <i class="mdi mdi-pencil-outline me-1"></i> {{ __('costing_trans.edit') }}
                        @else <i class="mdi mdi-plus me-1"></i> {{ __('costing_trans.add') }} @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('costing_trans.close') }}"></button>
                </div>
                <div class="modal-body">
                    @error('target')
                        <div class="alert alert-warning py-2">{{ $message }}</div>
                    @enderror

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.batch') }}</label>
                            <select class="form-select" wire:model.defer="form_batch_id">
                                <option value="">{{ __('costing_trans.no_batch') }}</option>
                                @foreach($batches as $b)
                                    <option value="{{ $b->id }}">{{ $b->code }} — {{ optional($b->period_start)->format('Y-m-d') }}–{{ optional($b->period_end)->format('Y-m-d') }}</option>
                                @endforeach
                            </select>
                            @error('form_batch_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.effective_date') }}</label>
                            <input type="date" class="form-control" wire:model.defer="form_effective_date">
                            @error('form_effective_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.reference') }}</label>
                            <input type="text" class="form-control" wire:model.defer="form_reference">
                            @error('form_reference') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.line_type') }}</label>
                            <select class="form-select" wire:model.defer="form_line_type">
                                @foreach($lineTypes as $lt)
                                    <option value="{{ $lt }}">{{ __('costing_trans.lt_'.$lt) }}</option>
                                @endforeach
                            </select>
                            @error('form_line_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.item_type') }}</label>
                            <select class="form-select" wire:model.defer="form_item_type">
                                @foreach($itemTypes as $it)
                                    <option value="{{ $it }}">{{ __('costing_trans.it_'.$it) }}</option>
                                @endforeach
                            </select>
                            @error('form_item_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.case_type') }}</label>
                            <select class="form-select" wire:model.defer="form_case_type">
                                @foreach($caseTypes as $ct)
                                    <option value="{{ $ct }}">{{ __('costing_trans.ct_'.$ct) }}</option>
                                @endforeach
                            </select>
                            @error('form_case_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.case_id') }}</label>
                            <input type="number" class="form-control"
                                   wire:model.defer="form_case_id"
                                   @if($form_case_type==='other') disabled @endif>
                            @error('form_case_id') <small class="text-danger">{{ $message }}</small> @enderror
                            @if($form_case_type==='other')
                                <small class="text-muted">{{ __('costing_trans.hint_case_disabled') }}</small>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.service_id') }}</label>
                            <input type="number" class="form-control" wire:model.defer="form_service_id">
                            @error('form_service_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.cost_center') }}</label>
                            <select class="form-select" wire:model.defer="form_cost_center_id">
                                <option value="">{{ __('costing_trans.none') }}</option>
                                @foreach($centers as $cc)
                                    <option value="{{ $cc->id }}">{{ $cc->code }} — {{ $cc->getTranslation('name', app()->getLocale()) }}</option>
                                @endforeach
                            </select>
                            @error('form_cost_center_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.adj_type') }}</label>
                            <select class="form-select" wire:model.defer="form_adj_type">
                                @foreach($adjTypes as $at)
                                    <option value="{{ $at }}">{{ __('costing_trans.at_'.$at) }}</option>
                                @endforeach
                            </select>
                            @error('form_adj_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.amount') }}</label>
                            <input type="number" step="0.000001" class="form-control" wire:model.defer="form_amount">
                            @error('form_amount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('costing_trans.reason') }}</label>
                            <input type="text" class="form-control" wire:model.defer="form_reason">
                            @error('form_reason') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.status') }}</label>
                            <select class="form-select" wire:model.defer="form_status">
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}">{{ __('costing_trans.st_'.$s) }}</option>
                                @endforeach
                            </select>
                            @error('form_status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('costing_trans.close') }}</button>
                    <button class="btn btn-primary" wire:click="save">
                        <i class="mdi mdi-content-save-outline me-1"></i> {{ __('costing_trans.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal control --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('adjModal');
            const modal = new bootstrap.Modal(modalEl);
            window.addEventListener('open-adj-modal', () => modal.show());
            window.addEventListener('close-adj-modal', () => modal.hide());
        });
    </script>
</div>
