<div class="container-fluid py-3" dir="rtl" wire:ignore.self>

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="×"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="×"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-info">
                <i class="mdi mdi-calendar-check-outline me-2"></i> {{ __('costing_trans.title') }}
            </h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">{{ __('costing_trans.settings') }}</a></li>
                <li class="breadcrumb-item active">{{ __('costing_trans.title') }}</li>
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
                        <option value="draft">{{ __('costing_trans.status_draft') }}</option>
                        <option value="running">{{ __('costing_trans.status_running') }}</option>
                        <option value="completed">{{ __('costing_trans.status_completed') }}</option>
                        <option value="posted">{{ __('costing_trans.status_posted') }}</option>
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

                <div class="col-md-1 text-end">
                    <button class="btn btn-primary w-100" wire:click="create">
                        <i class="mdi mdi-plus"></i> {{ __('costing_trans.add_batch') }}
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
                        <th>{{ __('costing_trans.code') }}</th>
                        <th>{{ __('costing_trans.period') }}</th>
                        <th>{{ __('costing_trans.scope') }}</th>
                        <th class="text-end">{{ __('costing_trans.direct_total') }}</th>
                        <th class="text-end">{{ __('costing_trans.overhead_total') }}</th>
                        <th class="text-end">{{ __('costing_trans.grand_total') }}</th>
                        <th>{{ __('costing_trans.status') }}</th>
                        <th class="text-end">{{ __('costing_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td>{{ $rows->firstItem() + $i }}</td>
                            <td><span class="badge bg-secondary">{{ $row->code }}</span></td>
                            <td>{{ optional($row->period_start)->format('Y-m-d') }} — {{ optional($row->period_end)->format('Y-m-d') }}</td>
                            <td>
                                @if($row->scope_type==='hospital')
                                    {{ __('costing_trans.scope_hospital') }}
                                @elseif($row->scope_type==='centers')
                                    {{ __('costing_trans.scope_centers') }}
                                @else
                                    {{ __('costing_trans.scope_services') }}
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($row->direct_total, 2) }}</td>
                            <td class="text-end">{{ number_format($row->overhead_total, 2) }}</td>
                            <td class="text-end"><strong>{{ number_format($row->grand_total, 2) }}</strong></td>
                            <td>
                                @switch($row->status)
                                    @case('draft')    <span class="badge bg-secondary">{{ __('costing_trans.status_draft') }}</span> @break
                                    @case('running')  <span class="badge bg-info">{{ __('costing_trans.status_running') }}</span> @break
                                    @case('completed')<span class="badge bg-success">{{ __('costing_trans.status_completed') }}</span> @break
                                    @case('posted')   <span class="badge bg-primary">{{ __('costing_trans.status_posted') }}</span> @break
                                @endswitch
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $row->id }})" title="{{ __('costing_trans.edit') }}">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </button>

                                    {{-- تغيير الحالة --}}
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            {{ __('costing_trans.change_status') }}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" wire:click.prevent="setStatus({{ $row->id }}, 'draft')">{{ __('costing_trans.status_draft') }}</a></li>
                                            <li><a class="dropdown-item" wire:click.prevent="setStatus({{ $row->id }}, 'running')">{{ __('costing_trans.status_running') }}</a></li>
                                            <li><a class="dropdown-item" wire:click.prevent="setStatus({{ $row->id }}, 'completed')">{{ __('costing_trans.status_completed') }}</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" wire:click.prevent="setStatus({{ $row->id }}, 'posted')">{{ __('costing_trans.status_posted') }}</a></li>
                                        </ul>
                                    </div>

                                    <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $row->id }})" title="{{ __('costing_trans.delete') }}">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">{{ __('costing_trans.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">{{ $rows->links() }}</div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="batchModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        @if($editing_id) <i class="mdi mdi-pencil-outline me-1"></i> {{ __('costing_trans.edit_batch') }}
                        @else <i class="mdi mdi-plus me-1"></i> {{ __('costing_trans.add_batch') }} @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('costing_trans.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.code') }}</label>
                            <input type="text" class="form-control" wire:model.defer="code" placeholder="{{ __('costing_trans.auto_if_empty') }}">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.period_start') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model.defer="period_start">
                            @error('period_start') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.period_end') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model.defer="period_end">
                            @error('period_end') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.scope') }} <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.defer="scope_type">
                                <option value="hospital">{{ __('costing_trans.scope_hospital') }}</option>
                                <option value="centers">{{ __('costing_trans.scope_centers') }}</option>
                                <option value="services" disabled>{{ __('costing_trans.scope_services') }}</option>
                            </select>
                            @error('scope_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        @if($scope_type === 'centers')
                            <div class="col-12">
                                <label class="form-label">{{ __('costing_trans.choose_centers') }}</label>
                                <div class="row g-2">
                                    @foreach($centers as $cc)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       id="modalcc{{ $cc->id }}"
                                                       value="{{ $cc->id }}"
                                                       wire:model.defer="scope_ids">
                                                <label class="form-check-label" for="modalcc{{ $cc->id }}">
                                                    <span class="badge bg-secondary">{{ $cc->code }}</span>
                                                    — {{ $cc->getTranslation('name', app()->getLocale()) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('scope_ids') <small class="text-danger d-block">{{ $message }}</small> @enderror
                            </div>
                        @endif

                        <div class="col-12">
                            <label class="form-label">{{ __('costing_trans.notes') }}</label>
                            <textarea class="form-control" rows="2" wire:model.defer="notes"></textarea>
                            @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        @if($editing_id)
                            <div class="col-12">
                                <div class="alert alert-warning mb-0 small">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    {{ __('costing_trans.edit_hint') }}
                                </div>
                            </div>
                        @endif
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

    {{-- Scripts to control modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('batchModal');
            const modal = new bootstrap.Modal(modalEl);

            window.addEventListener('open-batch-modal', () => modal.show());
            window.addEventListener('close-batch-modal', () => modal.hide());
        });
    </script>
</div>
