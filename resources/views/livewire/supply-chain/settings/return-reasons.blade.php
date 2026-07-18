<div>
    <!-- الجزء العلوي: إضافة / تعديل سبب الإرجاع -->
    <div class="card mb-4">
        <div class="card-header">
            @if($isEditMode)
                {{ trans('supply_chain_trans.edit_return_reason') }}
            @else
                {{ trans('supply_chain_trans.add_return_reason') }}
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <!-- الاسم العربي -->
                <div class="col-md-6">
                    <label for="name_ar" class="form-label">{{ trans('general.name_ar') }}</label>
                    <input type="text" id="name_ar" class="form-control" wire:model.defer="name_ar">
                    @error('name_ar') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- الاسم الإنجليزي -->
                <div class="col-md-6">
                    <label for="name_en" class="form-label">{{ trans('general.name_en') }}</label>
                    <input type="text" id="name_en" class="form-control" wire:model.defer="name_en">
                    @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <!-- الحالة -->
                <div class="col-md-6">
                    <label for="status" class="form-label">{{ trans('general.status') }}</label>
                    <select id="reasonsstatus" class="form-select" wire:model.defer="status">
                        <option value="">{{ trans('supply_chain_trans.select_status') }}</option>
                        <option value="active">{{ trans('supply_chain_trans.active') }}</option>
                        <option value="inactive">{{ trans('supply_chain_trans.inactive') }}</option>
                    </select>
                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <!-- أزرار الإجراء -->
                <div class="col-md-12 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" wire:click.prevent="saveReturnReason">
                        @if($isEditMode)
                            {{ trans('general.update') }}
                        @else
                            {{ trans('general.save') }}
                        @endif
                    </button>
                    @if($isEditMode)
                        <button type="button" class="btn btn-secondary ms-2" wire:click="resetForm">
                            {{ trans('general.cancel') }}
                        </button>
                    @endif
                    @if(session()->has('success'))
                        <div class="alert alert-success mx-auto d-inline-block">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- الجزء السفلي: عرض بيانات أسباب الإرجاع -->
    <div class="card">
        <div class="card-header">
            {{ trans('supply_chain_trans.return_reasons_data') }}
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('general.name_ar') }}</th>
                        <th>{{ trans('general.name_en') }}</th>
                        <th>{{ trans('general.status') }}</th>
                        <th>{{ trans('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnReasons as $reason)
                        <tr>
                            <td>{{ $reason->name_ar }}</td>
                            <td>{{ $reason->name_en }}</td>
                            <td>{{ trans('supply_chain_trans.' . $reason->status) }}</td>
                            <td>
                                <button wire:click="editReturnReason({{ $reason->id }})" class="btn btn-sm btn-warning">
                                    {{ trans('general.edit') }}
                                </button>
                                <button wire:click="deleteReturnReason({{ $reason->id }})" class="btn btn-sm btn-danger"
                                    onclick="confirm('{{ trans('supply_chain_trans.confirm_delete_return_reason') }}') || event.stopImmediatePropagation()">
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
</div>
