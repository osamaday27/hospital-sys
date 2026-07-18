<div>
    <!-- الجزء العلوي: إضافة / تعديل حركة مخزنية -->
    <div class="card mb-4">
        <div class="card-header">
            @if($isEditMode)
                {{ trans('supply_chain_trans.edit_movement_type') }}
            @else
                {{ trans('supply_chain_trans.add_movement_type') }}
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <!-- التأثير على الرصيد -->
                <div class="col-md-4">
                    <label for="impact_balance" class="form-label">{{ trans('supply_chain_trans.impact_balance') }}</label>
                    <select id="impact_balance" class="form-select" wire:model.defer="impact_balance">
                        <option value="">{{ trans('supply_chain_trans.select_impact_balance') }}</option>
                        <option value="increase">{{ trans('supply_chain_trans.increase') }}</option>
                        <option value="decrease">{{ trans('supply_chain_trans.decrease') }}</option>
                    </select>
                    @error('impact_balance') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- النوع -->
                <div class="col-md-4">
                    <label for="movement_type" class="form-label">{{ trans('supply_chain_trans.movement_type') }}</label>
                    <select id="movement_type" class="form-select" wire:model.defer="movement_type">
                        <option value="">{{ trans('supply_chain_trans.select_movement_type') }}</option>
                        <option value="expense">{{ trans('supply_chain_trans.expense') }}</option>
                        <option value="return">{{ trans('supply_chain_trans.return') }}</option>
                    </select>
                    @error('movement_type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- الوحدة -->
                <div class="col-md-4">
                    <label for="unit" class="form-label">{{ trans('supply_chain_trans.unit') }}</label>
                    <select id="unit" class="form-select" wire:model.defer="unit">
                        <option value="">{{ trans('supply_chain_trans.select_unit') }}</option>
                        <option value="small">{{ trans('supply_chain_trans.small') }}</option>
                        <option value="medium">{{ trans('supply_chain_trans.medium') }}</option>
                        <option value="large">{{ trans('supply_chain_trans.large') }}</option>
                    </select>
                    @error('unit') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <!-- زر الحفظ والتعديل -->
                <div class="col-md-12 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" wire:click.prevent="saveMovementType">
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

    <!-- الجزء السفلي: عرض بيانات أنواع الحركات المخزنية -->
    <div class="card">
        <div class="card-header">
            {{ trans('supply_chain_trans.movement_types_data') }}
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('supply_chain_trans.impact_balance') }}</th>
                        <th>{{ trans('supply_chain_trans.movement_type') }}</th>
                        <th>{{ trans('supply_chain_trans.unit') }}</th>
                        <th>{{ trans('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <!-- تحويل القيمة الإنجليزية إلى الترجمة المناسبة -->
                            <td>{{ trans('supply_chain_trans.' . $movement->impact_balance) }}</td>
                            <td>{{ trans('supply_chain_trans.' . $movement->movement_type) }}</td>
                            <td>{{ trans('supply_chain_trans.' . $movement->unit) }}</td>
                            <td>
                                <button wire:click="editMovementType({{ $movement->id }})" class="btn btn-sm btn-warning">
                                    {{ trans('general.edit') }}
                                </button>
                                <button wire:click="deleteMovementType({{ $movement->id }})" class="btn btn-sm btn-danger"
                                    onclick="confirm('{{ trans('supply_chain_trans.confirm_delete_movement_type') }}') || event.stopImmediatePropagation()">
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
