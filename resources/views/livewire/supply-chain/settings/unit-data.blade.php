<div>
    <!-- الجزء العلوي: إضافة / تعديل الوحدة -->
    <div class="card mb-4">
        <div class="card-header">
            {{ $isEditMode ? trans('supply_chain_trans.edit_unit') : trans('supply_chain_trans.add_unit') }}
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name_ar" class="form-label">{{ trans('general.name_ar') }}</label>
                    <input type="text" id="name_ar" class="form-control" wire:model.defer="name_ar">
                    @error('name_ar') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6">
                    <label for="name_en" class="form-label">{{ trans('general.name_en') }}</label>
                    <input type="text" id="name_en" class="form-control" wire:model.defer="name_en">
                    @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="unit_type" class="form-label">{{ trans('supply_chain_trans.unit_type') }}</label>
                    <select id="unit_type" class="form-select" wire:model.defer="unit_type">
                        <option value="">{{ trans('supply_chain_trans.select_unit_type') }}</option>
                        <option value="small">{{ trans('supply_chain_trans.unit_small') }}</option>
                        <option value="medium">{{ trans('supply_chain_trans.unit_medium') }}</option>
                        <option value="large">{{ trans('supply_chain_trans.unit_large') }}</option>
                    </select>
                    @error('unit_type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" wire:click.prevent="saveUnit">
                        {{ $isEditMode ? trans('general.update') : trans('general.save') }}
                    </button>
                    @if($isEditMode)
                        <button type="button" class="btn btn-secondary ms-2" wire:click="resetForm">
                            {{ trans('general.cancel') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- الجزء السفلي: عرض الوحدات -->
    <div class="card">
        <div class="card-header">{{ trans('supply_chain_trans.unit_data') }}</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('general.name_ar') }}</th>
                        <th>{{ trans('general.name_en') }}</th>
                        <th>{{ trans('supply_chain_trans.unit_type') }}</th>
                        <th>{{ trans('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        <tr>
                            <td>{{ $unit->name_ar }}</td>
                            <td>{{ $unit->name_en }}</td>
                            <td>
                                @if($unit->unit_type === 'small')
                                    {{ trans('supply_chain_trans.unit_small') }}
                                @elseif($unit->unit_type === 'medium')
                                    {{ trans('supply_chain_trans.unit_medium') }}
                                @elseif($unit->unit_type === 'large')
                                    {{ trans('supply_chain_trans.unit_large') }}
                                @endif
                            </td>
                            <td>
                                <button wire:click="editUnit({{ $unit->id }})" class="btn btn-sm btn-warning">
                                    {{ trans('general.edit') }}
                                </button>
                                <button class="btn btn-sm btn-danger" wire:click="$emit('confirmDelete', {{ $unit->id }})">
                                    {{ trans('general.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ trans('supply_chain_trans.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal تأكيد الحذف -->
    <div wire:ignore.self class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('general.confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{ trans('supply_chain_trans.confirm_delete_unit') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('general.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">
                        {{ trans('general.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- سكريبتات التحكم في الحذف والتوست -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let deleteId = null;

            Livewire.on('confirmDelete', id => {
                deleteId = id;
                const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                modal.show();
            });

            document.getElementById('confirm-delete-btn').addEventListener('click', function () {
                if (deleteId) {
                    Livewire.emit('deleteUnit', deleteId);
                    deleteId = null;
                }
            });

            window.addEventListener('hide-delete-modal', () => {
                const modalEl = document.getElementById('confirmDeleteModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            });

            window.addEventListener('show-toast', event => {
                Toastify({
                    text: event.detail.message,
                    duration: 3000,
                    gravity: 'top',
                    position: 'center',
                    backgroundColor: event.detail.type === 'success' ? "green" : "red",
                }).showToast();
            });
        });
    </script>
</div>
