{{-- resources/views/livewire/costing/settings/service-boms-index.blade.php --}}
<div class="container-fluid py-3" dir="rtl" wire:ignore.self>

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-primary">
                <i class="mdi mdi-clipboard-list-outline me-2"></i> {{ __('costing_trans.title') }}
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
                <div class="col-md-4">
                    <label class="form-label">{{ __('costing_trans.search') }}</label>
                    <input type="text" class="form-control"
                           wire:model.debounce.500ms="search"
                           placeholder="{{ __('costing_trans.search_placeholder') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('costing_trans.per_page') }}</label>
                    <select class="form-select" wire:model="perPage">
                        @foreach([10,20,30,50,100] as $pp)
                            <option value="{{ $pp }}">{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('costing_trans.status') }}</label>
                    <select class="form-select" wire:model="status">
                        <option value="">{{ __('costing_trans.all') }}</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}">{{ __('costing_trans.status_'.$st) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 text-end">
                    <button class="btn btn-primary w-100" wire:click="create">
                        <i class="mdi mdi-plus"></i> {{ __('costing_trans.add_new_bom') }}
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
                        <th>{{ __('costing_trans.service') }}</th>
                        <th>{{ __('costing_trans.version') }}</th>
                        <th>{{ __('costing_trans.status') }}</th>
                        <th>{{ __('costing_trans.active') }}</th>
                        <th class="text-end">{{ __('costing_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td>{{ $rows->firstItem() + $i }}</td>
                            <td><span class="badge bg-secondary">{{ $row->code }}</span></td>
                            <td>{{ $row->getTranslation('name', app()->getLocale()) }}</td>
                            <td>{{ $row->service?->getTranslation('name', app()->getLocale()) }}</td>
                            <td>{{ $row->version }}</td>
                            <td>
                                @if($row->status==='draft')
                                    <span class="badge bg-secondary">{{ __('costing_trans.status_draft') }}</span>
                                @elseif($row->status==='approved')
                                    <span class="badge bg-success">{{ __('costing_trans.status_approved') }}</span>
                                @else
                                    <span class="badge bg-outline-secondary">{{ __('costing_trans.status_archived') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($row->is_active)
                                    <span class="badge bg-success">{{ __('costing_trans.active_yes') }}</span>
                                @else
                                    <span class="badge bg-outline-secondary">{{ __('costing_trans.active_no') }}</span>
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
                        <tr><td colspan="8" class="text-center text-muted py-4">{{ __('costing_trans.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">{{ $rows->links() }}</div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="bomModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        @if($editing_id)
                            <i class="mdi mdi-pencil-outline me-1"></i> {{ __('costing_trans.edit_bom') }}
                        @else
                            <i class="mdi mdi-plus me-1"></i> {{ __('costing_trans.add_bom') }}
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('costing_trans.close') }}"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Level --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.level') }}</label>
                            <select class="form-select" wire:model="level_id">
                                <option value="">{{ __('costing_trans.all') }}</option>
                                @foreach($levels as $lv)
                                    <option value="{{ $lv->id }}">{{ $lv->getTranslation('name', app()->getLocale()) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sublevel --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.sublevel') }}</label>
                            <select class="form-select" wire:model="sublevel_id" @disabled(!$level_id)>
                                <option value="">{{ __('costing_trans.all') }}</option>
                                @foreach($sublevels as $sl)
                                    <option value="{{ $sl->id }}">{{ $sl->getTranslation('name', app()->getLocale()) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Service --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('costing_trans.service') }} <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.defer="service_id">
                                <option value="">{{ __('costing_trans.select_service') }}</option>
                                @foreach($services as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->getTranslation('name', app()->getLocale()) }}</option>
                                @endforeach
                            </select>
                            @error('service_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Code --}}
                        <div class="col-md-2">
                            <label class="form-label">{{ __('costing_trans.code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="code">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Name AR --}}
                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="name_ar">
                            @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Name EN --}}
                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.name_en') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name_en">
                            @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Version --}}
                        <div class="col-md-2">
                            <label class="form-label">{{ __('costing_trans.version') }}</label>
                            <input type="number" min="1" class="form-control" wire:model.defer="version">
                            @error('version') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-3">
                            <label class="form-label">{{ __('costing_trans.status') }}</label>
                            <select class="form-select" wire:model.defer="status_input">
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}">{{ __('costing_trans.status_'.$st) }}</option>
                                @endforeach
                            </select>
                            @error('status_input') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Active --}}
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="active" wire:model.defer="is_active">
                                <label class="form-check-label" for="active">{{ __('costing_trans.active') }}</label>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="col-md-5">
                            <label class="form-label">{{ __('costing_trans.notes') }}</label>
                            <input type="text" class="form-control" wire:model.defer="notes">
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Items Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ __('costing_trans.items') }}</h6>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary btn-sm" wire:click="addItem('supply')">{{ __('costing_trans.add_supply') }}</button>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="addItem('drug')">{{ __('costing_trans.add_drug') }}</button>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="addItem('resource')">{{ __('costing_trans.add_resource') }}</button>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="addItem('misc')">{{ __('costing_trans.add_misc') }}</button>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th style="width:140px">{{ __('costing_trans.item_type') }}</th>
                                    <th>{{ __('costing_trans.desc_ar') }}</th>
                                    <th>{{ __('costing_trans.desc_en') }}</th>
                                    <th style="width:110px">{{ __('costing_trans.unit') }}</th>
                                    <th style="width:120px" class="text-end">{{ __('costing_trans.qty') }}</th>
                                    <th style="width:150px" class="text-end">{{ __('costing_trans.unit_cost') }}</th>
                                    <th style="width:150px" class="text-end">{{ __('costing_trans.total_cost') }}</th>
                                    <th style="width:150px">{{ __('costing_trans.cost_center') }}</th>
                                    <th style="width:140px">{{ __('costing_trans.resource_rate') }}</th>
                                    <th style="width:120px">{{ __('costing_trans.duration') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $i => $it)
                                    @php $itemType = data_get($items, $i.'.item_type', ''); @endphp
                                    <tr>
                                        <td>{{ $i+1 }}</td>

                                        <td>
                                            <select class="form-select form-select-sm" wire:model="items.{{ $i }}.item_type">
                                                <option value="supply">{{ __('costing_trans.type_supply') }}</option>
                                                <option value="drug">{{ __('costing_trans.type_drug') }}</option>
                                                <option value="resource">{{ __('costing_trans.type_resource') }}</option>
                                                <option value="misc">{{ __('costing_trans.type_misc') }}</option>
                                            </select>
                                        </td>

                                        <td><input type="text" class="form-control form-control-sm" wire:model.lazy="items.{{ $i }}.description_ar"></td>
                                        <td><input type="text" class="form-control form-control-sm" wire:model.lazy="items.{{ $i }}.description_en"></td>
                                        <td><input type="text" class="form-control form-control-sm" wire:model.lazy="items.{{ $i }}.unit"></td>

                                        <td>
                                            <input type="number" step="0.0001" class="form-control form-control-sm text-end"
                                                   wire:model.lazy="items.{{ $i }}.qty"
                                                   wire:change="recalcRow({{ $i }})">
                                        </td>

                                        <td>
                                            <input type="number" step="0.0001" class="form-control form-control-sm text-end"
                                                   wire:model.lazy="items.{{ $i }}.unit_cost"
                                                   wire:change="recalcRow({{ $i }})">
                                        </td>

                                        <td class="text-end">{{ number_format((float)($it['total_cost'] ?? 0), 2) }}</td>

                                        <td>
                                            <select class="form-select form-select-sm" wire:model="items.{{ $i }}.cost_center_id">
                                                <option value="">{{ __('costing_trans.none') }}</option>
                                                @foreach($centers as $cc)
                                                    <option value="{{ $cc->id }}">
                                                        {{ $cc->code }} — {{ $cc->getTranslation('name', app()->getLocale()) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <select class="form-select form-select-sm"
                                                    wire:model="items.{{ $i }}.resource_rate_id"
                                                    @disabled($itemType !== 'resource')>
                                                <option value="">{{ __('costing_trans.none') }}</option>
                                                @foreach($rates as $rr)
                                                    <option value="{{ $rr->id }}">
                                                        {{ $rr->code }} — {{ $rr->getTranslation('name', app()->getLocale()) }} ({{ $rr->unit }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm"
                                                   wire:model.lazy="items.{{ $i }}.duration_minutes"
                                                   @disabled($itemType !== 'resource')>
                                        </td>

                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $i }})">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted py-3">
                                            {{ __('costing_trans.no_items') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if(count($items))
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="7" class="text-end">{{ __('costing_trans.total') }}</th>
                                        <th class="text-end">
                                            {{ number_format(
                                                collect($items)->sum(function ($x) {
                                                    return (float) ($x['total_cost'] ?? 0);
                                                }),
                                                2
                                            ) }}
                                        </th>
                                        <th colspan="4"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
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

    {{-- Scripts (Bootstrap Modal hooks) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('bomModal');
            const modal = new bootstrap.Modal(modalEl);
            window.addEventListener('open-bom-modal', () => modal.show());
            window.addEventListener('close-bom-modal', () => modal.hide());
        });
    </script>
</div>
