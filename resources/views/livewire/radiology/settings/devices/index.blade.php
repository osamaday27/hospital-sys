<div>
    {{-- ✅ التنبيهات --}}
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
                <i class="mdi mdi-table"></i>
                {{ __('devices.list_tab') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'form' ? 'active' : '' }}"
                    wire:click="$set('activeTab','form')">
                <i class="mdi mdi-plus-circle-outline"></i>
                {{ $device_id ? __('devices.edit_tab') : __('devices.create_tab') }}
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- 📋 القائمة --}}
        @if ($activeTab === 'list')
            <div class="card shadow-sm rounded-4 stylish-card">

                {{-- 🔎 البحث وزر جديد --}}
                <div class="card-header bg-gradient-primary fw-bold d-flex justify-content-between align-items-center">
                    <span>
                        <i class="mdi mdi-table"></i> {{ __('devices.list_title') }}
                    </span>
                    <div class="d-flex gap-2">
                        <input type="text"
                               class="form-control w-50"
                               placeholder="{{ __('devices.search_placeholder') }}"
                               wire:model.debounce.500ms="search">
                        <button wire:click="createNew"
                                class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="mdi mdi-plus-circle-outline"></i>
                            {{ __('devices.add_new') }}
                        </button>
                    </div>
                </div>

                {{-- ✅ جدول الأجهزة --}}
                <div class="card-body p-0">
                    <table class="table table-striped table-hover text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('devices.name') }}</th>
                                <th>{{ __('devices.type') }}</th>
                                <th>{{ __('devices.room') }}</th>
                                <th>{{ __('devices.status') }}</th>
                                <th>{{ __('devices.installation_date') }}</th>
                                <th>{{ __('devices.maintenance_date') }}</th>
                                <th>{{ __('devices.vendor') }}</th>
                                <th>{{ __('devices.serial_no') }}</th>
                                <th>{{ __('devices.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($devices as $device)
                                <tr>
                                    <td>{{ $device->id }}</td>
                                    <td>{{ $device->getTranslation('name', app()->getLocale()) }}</td>
                                    <td>{{ $device->type?->getTranslation('name', app()->getLocale()) }}</td>
                                    <td>{{ $device->room?->getTranslation('name', app()->getLocale()) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $device->status == 'available' ? 'success' : ($device->status == 'maintenance' ? 'warning' : 'danger') }}">
                                            {{ __('devices.status_' . $device->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $device->installation_date }}</td>
                                    <td>{{ $device->maintenance_date }}</td>
                                    <td>{{ $device->vendor }}</td>
                                    <td>{{ $device->serial_no }}</td>
                                    <td>
                                        <button wire:click="edit({{ $device->id }})"
                                                class="btn btn-sm btn-warning rounded-pill">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $device->id }})"
                                                class="btn btn-sm btn-danger rounded-pill">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                @if ($device->description)
                                    <tr>
                                        <td colspan="10" class="text-start text-muted ps-5">
                                            <i class="mdi mdi-text-box-outline me-2"></i>
                                            {{ $device->getTranslation('description', app()->getLocale()) }}
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-3">
                                        ⚠️ {{ __('devices.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $devices->links() }}
                </div>
            </div>
        @endif

        {{-- 📝 النموذج --}}
        @if ($activeTab === 'form')
            <div class="card shadow-lg rounded-4 stylish-card">
                <div class="card-header bg-gradient-success fw-bold">
                    <i class="mdi mdi-plus-circle-outline me-2"></i>
                    {{ $device_id ? __('devices.edit_title') : __('devices.create_title') }}
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save" class="row g-3">

                        {{-- Name AR --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.name_ar') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name.ar">
                            @error('name.ar') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Name EN --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.name_en') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name.en">
                            @error('name.en') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Description AR --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.description_ar') }}</label>
                            <textarea class="form-control" wire:model.defer="description.ar"></textarea>
                        </div>

                        {{-- Description EN --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.description_en') }}</label>
                            <textarea class="form-control" wire:model.defer="description.en"></textarea>
                        </div>

                        {{-- Type --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.type') }}</label>
                            <select class="form-select" wire:model="device_type_id">
                                <option value="">{{ __('devices.select_type') }}</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">
                                        {{ $type->getTranslation('name', app()->getLocale()) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('device_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Room --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.room') }}</label>
                            <select class="form-select" wire:model="room_id">
                                <option value="">{{ __('devices.select_room') }}</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}">
                                        {{ $room->getTranslation('name', app()->getLocale()) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.status') }}</label>
                            <select class="form-select" wire:model.defer="status">
                                <option value="available">{{ __('devices.status_available') }}</option>
                                <option value="maintenance">{{ __('devices.status_maintenance') }}</option>
                                <option value="inactive">{{ __('devices.status_inactive') }}</option>
                            </select>
                        </div>

                        {{-- Installation Date --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.installation_date') }}</label>
                            <input type="date" class="form-control" wire:model.defer="installation_date">
                        </div>

                        {{-- Maintenance Date --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.maintenance_date') }}</label>
                            <input type="date" class="form-control" wire:model.defer="maintenance_date">
                        </div>

                        {{-- Vendor --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.vendor') }}</label>
                            <input type="text" class="form-control" wire:model.defer="vendor">
                        </div>

                        {{-- Serial No --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('devices.serial_no') }}</label>
                            <input type="text" class="form-control" wire:model.defer="serial_no">
                        </div>

                        {{-- أزرار --}}
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-success rounded-pill px-4 shadow-sm">
                                <i class="mdi mdi-content-save"></i>
                                {{ $device_id ? __('devices.update') : __('devices.save') }}
                            </button>
                            <button type="button"
                                    wire:click="$set('activeTab','list')"
                                    class="btn btn-secondary rounded-pill px-4 shadow-sm">
                                <i class="mdi mdi-cancel"></i> {{ __('devices.cancel') }}
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
            title: '{{ __("devices.delete_title") }}',
            text: '{{ __("devices.delete_text") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#0d6efd',
            confirmButtonText: '{{ __("devices.delete_confirm") }}',
            cancelButtonText: '{{ __("devices.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('delete', id);
                Swal.fire('{{ __("devices.deleted_title") }}',
                          '{{ __("devices.deleted_msg") }}',
                          'success');
            }
        })
    }
</script>
