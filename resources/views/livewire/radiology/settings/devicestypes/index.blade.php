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
                <i class="mdi mdi-table"></i> {{ __('radiology_devicestypes.list_tab') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'form' ? 'active' : '' }}"
                    wire:click="$set('activeTab','form')">
                <i class="mdi mdi-plus-circle-outline"></i>
                {{ $device_id ? __('radiology_devicestypes.edit_tab') : __('radiology_devicestypes.create_tab') }}
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- 📋 قائمة --}}
        @if ($activeTab === 'list')
            <div class="card shadow-sm rounded-4">
                <div class="card-header bg-gradient-primary fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="mdi mdi-table"></i> {{ __('radiology_devicestypes.list_title') }}</span>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control" style="width:200px"
                               placeholder="🔍 {{ __('radiology_devicestypes.search') }}"
                               wire:model.debounce.500ms="search">
                        <button class="btn btn-success rounded-pill px-4 shadow-sm"
                                wire:click="createNew">
                            <i class="mdi mdi-plus-circle-outline"></i> {{ __('radiology_devicestypes.add_new') }}
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('radiology_devicestypes.name') }}</th>
                                <th>{{ __('radiology_devicestypes.type') }}</th>
                                <th>{{ __('radiology_devicestypes.category') }}</th>
                                <th>{{ __('radiology_devicestypes.status') }}</th>
                                <th>{{ __('radiology_devicestypes.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $device)
                                <tr>
                                    <td>{{ $device->id }}</td>
                                    <td>{{ $device->getTranslation('name', app()->getLocale()) }}</td>
                                    <td>{{ $device->type }}</td>
                                    <td>{{ $device->category == 'diagnostic' ? __('radiology_devicestypes.diagnostic') : __('radiology_devicestypes.therapeutic') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $device->status == 'active' ? 'success' : 'danger' }}">
                                            {{ $device->status == 'active' ? __('radiology_devicestypes.active') : __('radiology_devicestypes.inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="edit({{ $device->id }})" class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $device->id }})" class="btn btn-sm btn-danger">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-muted py-3">{{ __('radiology_devicestypes.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $devices->links() }}</div>
            </div>
        @endif

        {{-- 📝 نموذج --}}
        @if ($activeTab === 'form')
            <div class="card shadow-lg rounded-4 stylish-card">
                <div class="card-header bg-gradient-success fw-bold">
                    <i class="mdi mdi-plus-circle-outline me-2"></i>
                    {{ $device_id ? __('radiology_devicestypes.edit_title') : __('radiology_devicestypes.create_title') }}
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('radiology_devicestypes.name_ar') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name.ar">
                            @error('name.ar') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('radiology_devicestypes.name_en') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name.en">
                            @error('name.en') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('radiology_devicestypes.type') }}</label>
                            <select class="form-select" wire:model="type">
                                <option value="">{{ __('اختر') }}</option>
                                <option value="X-Ray">X-Ray</option>
                                <option value="Digital X-Ray">Digital X-Ray</option>
                                <option value="MRI">MRI</option>
                                <option value="CT">CT</option>
                                <option value="Ultrasound">Ultrasound</option>
                                <option value="Mammography">Mammography</option>
                                <option value="Nuclear Medicine">Nuclear Medicine</option>
                            </select>
                            @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('radiology_devicestypes.category') }}</label>
                            <select class="form-select" wire:model="category">
                                <option value="">{{ __('اختر') }}</option>
                                <option value="diagnostic">{{ __('radiology_devicestypes.diagnostic') }}</option>
                                <option value="therapeutic">{{ __('radiology_devicestypes.therapeutic') }}</option>
                            </select>
                            @error('category') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('radiology_devicestypes.description_ar') }}</label>
                            <textarea class="form-control" wire:model="description.ar"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('radiology_devicestypes.description_en') }}</label>
                            <textarea class="form-control" wire:model="description.en"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('radiology_devicestypes.status') }}</label>
                            <select class="form-select" wire:model.defer="status">
                                <option value="active">{{ __('radiology_devicestypes.active') }}</option>
                                <option value="inactive">{{ __('radiology_devicestypes.inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-success rounded-pill px-4 shadow-sm">
                                <i class="mdi mdi-content-save-outline"></i>
                                {{ $device_id ? __('radiology_devicestypes.update') : __('radiology_devicestypes.save') }}
                            </button>
                            <button type="button" wire:click="resetInput"
                                    class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="mdi mdi-plus-circle-outline"></i>
                                {{ __('radiology_devicestypes.new') }}
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
            title: '{{ __("radiology_devicestypes.delete_title") }}',
            text: '{{ __("radiology_devicestypes.delete_text") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#0d6efd',
            confirmButtonText: '{{ __("radiology_devicestypes.delete_confirm") }}',
            cancelButtonText: '{{ __("radiology_devicestypes.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('delete', id);
                Swal.fire('{{ __("radiology_devicestypes.deleted_title") }}', '{{ __("radiology_devicestypes.deleted_msg") }}', 'success');
            }
        })
    }
</script>
