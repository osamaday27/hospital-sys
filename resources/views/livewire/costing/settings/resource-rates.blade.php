{{-- resources/views/livewire/costing/settings/resource-rates-index.blade.php --}}
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
            <h4 class="mb-0 text-primary">
                <i class="mdi mdi-gauge me-2"></i> {{ __('costing_trans.title') }}
            </h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">
                    <a href="{{ url()->previous() }}">{{ __('costing_trans.settings') }}</a>
                </li>
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
                    <input type="text" class="form-control" placeholder="{{ __('costing_trans.search_placeholder') }}"
                           wire:model.debounce.500ms="search">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.type') }}</label>
                    <select class="form-select" wire:model="type">
                        <option value="">{{ __('costing_trans.all') }}</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.status') }}</label>
                    <select class="form-select" wire:model="status">
                        <option value="">{{ __('costing_trans.all') }}</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}">{{ __('costing_trans.'.$s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.per_page') }}</label>
                    <select class="form-select" wire:model="perPage">
                        @foreach([10,20,30,50,100] as $pp)
                            <option value="{{ $pp }}">{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 text-end">
                    <button class="btn btn-primary w-100" wire:click="create">
                        <i class="mdi mdi-plus"></i> {{ __('costing_trans.add_new') }}
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
                        <th>{{ __('costing_trans.name') }}</th>
                        <th>{{ __('costing_trans.type') }}</th>
                        <th>{{ __('costing_trans.cost_center') }}</th>
                        <th>{{ __('costing_trans.unit') }}</th>
                        <th class="text-end">{{ __('costing_trans.rate') }}</th>
                        <th>{{ __('costing_trans.status') }}</th>
                        <th class="text-end">{{ __('costing_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td>{{ $rows->firstItem() + $i }}</td>
                            <td><span class="badge bg-secondary">{{ $row->code }}</span></td>
                            <td>{{ $row->getTranslation('name', app()->getLocale()) }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ $row->type }}</span></td>
                            <td>
                                @if($row->costCenter)
                                    <span class="text-muted">{{ $row->costCenter->code }}</span> —
                                    {{ $row->costCenter->getTranslation('name', app()->getLocale()) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ __('costing_trans.'.$row->unit) }}</td>
                            <td class="text-end">{{ number_format($row->rate, 2) }}</td>
                            <td>
                                @if($row->status === 'active')
                                    <span class="badge bg-success">{{ __('costing_trans.active') }}</span>
                                @else
                                    <span class="badge bg-outline-secondary">{{ __('costing_trans.inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $row->id }})" title="{{ __('costing_trans.edit') }}">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $row->id }})" title="{{ __('costing_trans.delete') }}">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="mdi mdi-information-outline me-2"></i> {{ __('costing_trans.no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $rows->links() }}
            </div>
        </div>
    </div>

    {{-- Modal: Add/Edit --}}
    <div class="modal fade" id="resModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        @if($editing_id) <i class="mdi mdi-pencil-outline me-2"></i> {{ __('costing_trans.edit') }}
                        @else <i class="mdi mdi-plus me-2"></i> {{ __('costing_trans.add_new') }} @endif
                        — {{ __('costing_trans.title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('costing_trans.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="code">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="name_ar">
                            @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.name_en') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name_en">
                            @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.type') }} <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.defer="type_input">
                                @foreach($types as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('type_input') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.cost_center') }}</label>
                            <select class="form-select" wire:model.defer="cost_center_id">
                                <option value="">{{ __('costing_trans.none') }}</option>
                                @foreach($centers as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->code }} — {{ $c->getTranslation('name', app()->getLocale()) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cost_center_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.unit') }} <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.defer="unit">
                                @foreach($units as $u)
                                    <option value="{{ $u }}">{{ __('costing_trans.'.$u) }}</option>
                                @endforeach
                            </select>
                            @error('unit') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.rate') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control text-end" wire:model.defer="rate">
                            @error('rate') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.status') }} <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.defer="status_input">
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}">{{ __('costing_trans.'.$s) }}</option>
                                @endforeach
                            </select>
                            @error('status_input') <small class="text-danger">{{ $message }}</small> @enderror
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

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('resModal');
            const modal = new bootstrap.Modal(modalEl);

            window.addEventListener('open-res-modal', () => modal.show());
            window.addEventListener('close-res-modal', () => modal.hide());
        });
    </script>
</div>
