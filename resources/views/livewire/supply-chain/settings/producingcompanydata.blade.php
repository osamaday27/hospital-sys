<div>
    <!-- الجزء العلوي: إضافة / تعديل بيانات الشركة المنتجة -->
    <div class="card mb-4">
        <div class="card-header">
            @if($isEditMode)
                {{ trans('supply_chain_trans.edit_company') }}
            @else
                {{ trans('supply_chain_trans.add_company') }}
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <!-- اسم الشركة بالعربي -->
                <div class="col-md-6">
                    <label for="name_ar" class="form-label">{{ trans('general.name_ar') }}</label>
                    <input type="text" id="name_ar" class="form-control" wire:model.defer="name_ar">
                    @error('name_ar') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- اسم الشركة بالإنجليزي -->
                <div class="col-md-6">
                    <label for="name_en" class="form-label">{{ trans('general.name_en') }}</label>
                    <input type="text" id="name_en" class="form-control" wire:model.defer="name_en">
                    @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <!-- العنوان -->
                <div class="col-md-6">
                    <label for="address" class="form-label">{{ trans('general.address') }}</label>
                    <input type="text" id="address" class="form-control" wire:model.defer="address">
                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- الملاحظات -->
                <div class="col-md-6">
                    <label for="notes" class="form-label">{{ trans('general.notes') }}</label>
                    <textarea id="notes" class="form-control" wire:model.defer="notes"></textarea>
                    @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <!-- أزرار الإجراء -->
                <div class="col-md-12 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" wire:click.prevent="saveCompany">
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

    <!-- الجزء السفلي: عرض بيانات الشركات المنتجة -->
    <div class="card">
        <div class="card-header">
            {{ trans('supply_chain_trans.producing_company_data') }}
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('general.name_ar') }}</th>
                        <th>{{ trans('general.name_en') }}</th>
                        <th>{{ trans('general.address') }}</th>
                        <th>{{ trans('general.notes') }}</th>
                        <th>{{ trans('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>{{ $company->name_ar }}</td>
                            <td>{{ $company->name_en }}</td>
                            <td>{{ $company->address }}</td>
                            <td>{{ $company->notes }}</td>
                            <td>
                                <button wire:click="editCompany({{ $company->id }})" class="btn btn-sm btn-warning">
                                    {{ trans('general.edit') }}
                                </button>
                                <button wire:click="deleteCompany({{ $company->id }})" class="btn btn-sm btn-danger"
                                    onclick="confirm('{{ trans('supply_chain_trans.confirm_delete_company') }}') || event.stopImmediatePropagation()">
                                    {{ trans('general.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ trans('supply_chain_trans.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
