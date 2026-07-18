<div>
    <!-- الجزء العلوي: إضافة / تعديل السنة المالية -->
    <div class="card mb-4">
        <div class="card-header">
            @if($isEditMode)
                {{ trans('supply_chain_trans.edit_financial_year') }}
            @else
                {{ trans('supply_chain_trans.add_financial_year') }}
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <!-- كود السنة -->
                <div class="col-md-6">
                    <label for="year_code" class="form-label">{{ trans('supply_chain_trans.year_code') }}</label>
                    <input type="text" id="year_code" class="form-control" wire:model.defer="year_code">
                    @error('year_code') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- عدد الفترات -->
                <div class="col-md-6">
                    <label for="periods_count" class="form-label">{{ trans('supply_chain_trans.periods_count') }}</label>
                    <input type="number" id="periods_count" class="form-control" wire:model.defer="periods_count">
                    @error('periods_count') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <!-- تاريخ بداية السنة المالية -->
                <div class="col-md-6">
                    <label for="start_date" class="form-label">{{ trans('supply_chain_trans.start_date') }}</label>
                    <input type="date" id="start_date" class="form-control" wire:model.defer="start_date">
                    @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- تاريخ نهاية السنة المالية -->
                <div class="col-md-6">
                    <label for="end_date" class="form-label">{{ trans('supply_chain_trans.end_date') }}</label>
                    <input type="date" id="end_date" class="form-control" wire:model.defer="end_date">
                    @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <!-- أزرار الإجراء -->
                <div class="col-md-12 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" wire:click.prevent="saveFinancialYear">
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

    <!-- الجزء السفلي: عرض بيانات السنة المالية -->
    <div class="card">
        <div class="card-header">
            {{ trans('supply_chain_trans.financial_year_data') }}
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('supply_chain_trans.year_code') }}</th>
                        <th>{{ trans('supply_chain_trans.periods_count') }}</th>
                        <th>{{ trans('supply_chain_trans.start_date') }}</th>
                        <th>{{ trans('supply_chain_trans.end_date') }}</th>
                        <th>{{ trans('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financialYears as $year)
                        <tr>
                            <td>{{ $year->year_code }}</td>
                            <td>{{ $year->periods_count }}</td>
                            <td>{{ $year->start_date }}</td>
                            <td>{{ $year->end_date }}</td>
                            <td>
                                <button wire:click="editFinancialYear({{ $year->id }})" class="btn btn-sm btn-warning">
                                    {{ trans('general.edit') }}
                                </button>
                                <button wire:click="deleteFinancialYear({{ $year->id }})" class="btn btn-sm btn-danger"
                                    onclick="confirm('{{ trans('supply_chain_trans.confirm_delete_financial_year') }}') || event.stopImmediatePropagation()">
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
