<div>
    <!-- الجزء العلوي: إضافة / تعديل فترة مالية -->
    <div class="card mb-4">
        <div class="card-header">
            @if($isEditMode)
                {{ trans('supply_chain_trans.edit_financial_period') }}
            @else
                {{ trans('supply_chain_trans.add_financial_period') }}
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <!-- الاسم بالعربي -->
                <div class="col-md-6">
                    <label for="name_ar" class="form-label">{{ trans('general.name_ar') }}</label>
                    <input type="text" id="name_ar" class="form-control" wire:model.defer="name_ar">
                    @error('name_ar') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- الاسم بالإنجليزية -->
                <div class="col-md-6">
                    <label for="name_en" class="form-label">{{ trans('general.name_en') }}</label>
                    <input type="text" id="name_en" class="form-control" wire:model.defer="name_en">
                    @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <!-- تاريخ بداية الفترة -->
                <div class="col-md-6">
                    <label for="start_date" class="form-label">{{ trans('supply_chain_trans.start_date') }}</label>
                    <input type="date" id="start_date" class="form-control" wire:model.defer="start_date">
                    @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- تاريخ نهاية الفترة -->
                <div class="col-md-6">
                    <label for="end_date" class="form-label">{{ trans('supply_chain_trans.end_date') }}</label>
                    <input type="date" id="end_date" class="form-control" wire:model.defer="end_date">
                    @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <!-- أزرار الإجراء -->
                <div class="col-md-12 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" wire:click.prevent="saveFinancialPeriod">
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

    <!-- الجزء السفلي: عرض بيانات الفترات المالية -->
    <div class="card">
        <div class="card-header">
            {{ trans('supply_chain_trans.financial_period_data') }}
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('general.name_ar') }}</th>
                        <th>{{ trans('general.name_en') }}</th>
                        <th>{{ trans('supply_chain_trans.start_date') }}</th>
                        <th>{{ trans('supply_chain_trans.end_date') }}</th>
                        <th>{{ trans('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financialPeriods as $period)
                        <tr>
                            <td>{{ $period->name_ar }}</td>
                            <td>{{ $period->name_en }}</td>
                            <td>{{ $period->start_date }}</td>
                            <td>{{ $period->end_date }}</td>
                            <td>
                                <button wire:click="editFinancialPeriod({{ $period->id }})" class="btn btn-sm btn-warning">
                                    {{ trans('general.edit') }}
                                </button>
                                <button wire:click="deleteFinancialPeriod({{ $period->id }})" class="btn btn-sm btn-danger"
                                    onclick="confirm('{{ trans('supply_chain_trans.confirm_delete_financial_period') }}') || event.stopImmediatePropagation()">
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
