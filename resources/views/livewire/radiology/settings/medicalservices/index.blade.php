<div>
    {{-- ✅ رسائل التنبيه --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔹 Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'list' ? 'active' : '' }}"
                    wire:click="$set('activeTab','list')">
                <i class="mdi mdi-table"></i> {{ __('medicalservices.list_tab') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'form' ? 'active' : '' }}"
                    wire:click="$set('activeTab','form')">
                <i class="mdi mdi-plus-circle-outline"></i>
                {{ $service_id ? __('medicalservices.edit_tab') : __('medicalservices.create_tab') }}
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- 📋 القائمة --}}
        @if ($activeTab === 'list')
            <div class="card shadow-sm rounded-4 stylish-card">
                <div class="card-header bg-gradient-primary fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="mdi mdi-table"></i> {{ __('medicalservices.list_title') }}</span>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control w-50"
                               placeholder="{{ __('medicalservices.search_placeholder') }}"
                               wire:model.debounce.500ms="search">
                        <button wire:click="createNew"
                                class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="mdi mdi-plus-circle-outline"></i> {{ __('medicalservices.add_new') }}
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <table class="table table-striped table-hover text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('medicalservices.name') }}</th>
                                <th>{{ __('medicalservices.device') }}</th>
                                <th>{{ __('medicalservices.icd10_code') }}</th>
                                <th>{{ __('medicalservices.cpt_code') }}</th>
                                <th>{{ __('medicalservices.status') }}</th>
                                <th>{{ __('medicalservices.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($services as $service)
                                <tr>
                                    <td>{{ $service->id }}</td>
                                    <td>{{ $service->getTranslation('name', app()->getLocale()) }}</td>
                                    <td>{{ optional($service->device)->getTranslation('name', app()->getLocale()) }}</td>
                                    <td>{{ $service->icd10_code }}</td>
                                    <td>{{ $service->cpt_code }}</td>
                                    <td>
                                        <span class="badge {{ $service->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ __('medicalservices.' . $service->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="edit({{ $service->id }})"
                                                class="btn btn-sm btn-warning rounded-pill">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $service->id }})"
                                                class="btn btn-sm btn-danger rounded-pill">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        ⚠️ {{ __('medicalservices.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $services->links() }}
                </div>
            </div>
        @endif

        {{-- 📝 النموذج --}}
        @if ($activeTab === 'form')
            <div class="card shadow-lg rounded-4 stylish-card">
                <div class="card-header bg-gradient-success fw-bold">
                    <i class="mdi mdi-plus-circle-outline me-2"></i>
                    {{ $service_id ? __('medicalservices.edit_title') : __('medicalservices.create_title') }}
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save" class="row g-3">

                        {{-- Name AR --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('medicalservices.name_ar') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name.ar">
                            @error('name.ar') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Name EN --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('medicalservices.name_en') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name.en">
                            @error('name.en') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Description --}}
                        <div class="col-md-12">
                            <label class="form-label">{{ __('medicalservices.description') }}</label>
                            <textarea class="form-control" rows="2" wire:model.defer="description.{{ app()->getLocale() }}"></textarea>
                        </div>

                        {{-- Device --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('medicalservices.device') }}</label>
                            <select class="form-select" wire:model.defer="device_id">
                                <option value="">{{ __('medicalservices.select_device') }}</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}">{{ $device->getTranslation('name', app()->getLocale()) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ICD10 + CPT --}}
                        <div class="col-md-3">
                            <label class="form-label">{{ __('medicalservices.icd10_code') }}</label>
                            <input type="text" class="form-control" wire:model.defer="icd10_code">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('medicalservices.cpt_code') }}</label>
                            <input type="text" class="form-control" wire:model.defer="cpt_code">
                        </div>

                        {{-- Instructions --}}
                        <div class="col-md-12">
                            <label class="form-label">{{ __('medicalservices.instructions') }}</label>
                            <textarea class="form-control" rows="2" wire:model.defer="instructions.{{ app()->getLocale() }}"></textarea>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('medicalservices.status') }}</label>
                            <select class="form-select" wire:model.defer="status">
                                <option value="active">{{ __('medicalservices.active') }}</option>
                                <option value="inactive">{{ __('medicalservices.inactive') }}</option>
                            </select>
                        </div>

                        {{-- Save Button --}}
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-success rounded-pill px-4 shadow-sm">
                                <i class="mdi mdi-content-save"></i>
                                {{ $service_id ? __('medicalservices.update') : __('medicalservices.save') }}
                            </button>
                            <button type="button" wire:click="$set('activeTab','list')"
                                    class="btn btn-secondary rounded-pill px-4 shadow-sm">
                                <i class="mdi mdi-cancel"></i> {{ __('medicalservices.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ✅ SweetAlert2 --}}
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '{{ __("medicalservices.delete_title") }}',
            text: '{{ __("medicalservices.delete_text") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#0d6efd',
            confirmButtonText: '{{ __("medicalservices.delete_confirm") }}',
            cancelButtonText: '{{ __("medicalservices.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('delete', id);
                Swal.fire('{{ __("medicalservices.deleted_title") }}',
                          '{{ __("medicalservices.deleted_msg") }}',
                          'success');
            }
        })
    }
</script>
