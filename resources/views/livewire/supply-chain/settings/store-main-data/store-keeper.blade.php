<div class="p-3">
    <h5 class="mb-3">{{ __('supply_chain_trans.store_keepers_management') }}</h5>


    {{-- نموذج الإضافة --}}
    <form wire:submit.prevent="save">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.select_keeper') }}</label>
                <select class="form-select" wire:model.defer="user_id" id="user-select">
                    <option value="">{{ __('supply_chain_trans.select_user') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('general.status') }}</label>
                <select class="form-select" wire:model.defer="status">
                    <option value="active">{{ __('supply_chain_trans.active') }}</option>
                    <option value="inactive">{{ __('supply_chain_trans.inactive') }}</option>
                </select>
            </div>

            <div class="col-md-4">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" wire:model="is_main_keeper" wire:change="toggleAllPermissions" id="main_keeper">
                    <label class="form-check-label" for="main_keeper">{{ __('supply_chain_trans.main_keeper') }}</label>
                </div>
            </div>

            {{-- الصلاحيات --}}
            <div class="col-md-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="can_issue" id="can_issue">
                    <label class="form-check-label" for="can_issue">{{ __('supply_chain_trans.permission_issue') }}</label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="can_add" id="can_add">
                    <label class="form-check-label" for="can_add">{{ __('supply_chain_trans.permission_add') }}</label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="can_inventory" id="can_inventory">
                    <label class="form-check-label" for="can_inventory">{{ __('supply_chain_trans.permission_inventory') }}</label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="can_report" id="can_report">
                    <label class="form-check-label" for="can_report">{{ __('supply_chain_trans.permission_report') }}</label>
                </div>
            </div>

            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">
                    {{ __('general.save') }}
                </button>
            </div>

            @error('storeId')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </form>

    <hr class="my-4">

    {{-- جدول عرض الأمناء --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>{{ __('general.name') }}</th>
                    <th>{{ __('supply_chain_trans.permissions') }}</th>
                    <th>{{ __('general.status') }}</th>
                    <th>{{ __('general.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keepers as $keeper)
                    <tr>
                        <td>{{ $keeper->user->name }}</td>
                        <td>
                            @if($keeper->is_main_keeper)
                                <span class="badge bg-dark">{{ __('supply_chain_trans.main_keeper_badge') }}</span>
                            @endif
                            @if($keeper->can_issue)
                                <span class="badge bg-primary">{{ __('supply_chain_trans.permission_issue') }}</span>
                            @endif
                            @if($keeper->can_add)
                                <span class="badge bg-success">{{ __('supply_chain_trans.permission_add') }}</span>
                            @endif
                            @if($keeper->can_inventory)
                                <span class="badge bg-warning">{{ __('supply_chain_trans.permission_inventory') }}</span>
                            @endif
                            @if($keeper->can_report)
                                <span class="badge bg-info">{{ __('supply_chain_trans.permission_report') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $keeper->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $keeper->status == 'active' ? __('supply_chain_trans.active') : __('general.inactive') }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" wire:click="edit({{ $keeper->id }})">
                                {{ __('general.edit') }}
                            </button>
                            <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $keeper->id }})">
                                {{ __('general.delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- مودال تأكيد الحذف --}}
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                               colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px">
                    </lord-icon>
                    <h4 class="mt-3">{{ __('call_center_trans.h4_delete') }}</h4>
                    <p>{{ __('call_center_trans.p_delete') }}</p>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('call_center_trans.cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="delete">
                            {{ __('call_center_trans.yes_delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- سكربت التهيئة للمودال والتوست --}}
    <script>
        document.addEventListener('livewire:load', function () {
            // عرض مودال التأكيد
            window.addEventListener('show-delete-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            });

            // إخفاء مودال التأكيد
            window.addEventListener('hide-delete-modal', () => {
                const modalEl = document.getElementById('deleteModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        });
    </script>

</div>
