<!-- resources/views/livewire/medical/outpatient-service-form.blade.php -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="ri-stethoscope-line me-2"></i> {{ trans('outpatient_trans.add_service') }}</h5>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-3">
            <div class="col-md-5">
                <label for="selected_service" class="form-label">{{ trans('outpatient_trans.choose_service') }}</label>
                <select wire:model="selected_service" class="form-select">
                    <option value="">-- {{ trans('outpatient_trans.choose') }} --</option>
                    @foreach($available_services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
                @error('selected_service') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-2">
                <label for="quantity" class="form-label">{{ trans('outpatient_trans.quantity') }}</label>
                <input type="number" wire:model="quantity" class="form-control" min="1">
            </div>
            <div class="col-md-2">
                <label for="discount" class="form-label">{{ trans('outpatient_trans.discount') }}</label>
                <input type="number" wire:model="discount" class="form-control">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button wire:click="addService" class="btn btn-success w-100">
                    <i class="ri-add-line"></i> {{ trans('buttons.add') }}
                </button>
            </div>
        </div>

        @if (count($services) > 0)
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ trans('outpatient_trans.service') }}</th>
                            <th>{{ trans('outpatient_trans.total') }}</th>
                            <th>{{ trans('outpatient_trans.contracting_value') }}</th>
                            <th>{{ trans('outpatient_trans.patient_value') }}</th>
                            <th>{{ trans('outpatient_trans.discount') }}</th>
                            <th>{{ trans('outpatient_trans.quantity') }}</th>
                            <th>{{ trans('buttons.delete') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $index => $s)
                            <tr>
                                <td>{{ $s['name'] }}</td>
                                <td>{{ number_format($s['total'], 2) }}</td>
                                <td>{{ number_format($s['contracting_value'], 2) }}</td>
                                <td>{{ number_format($s['patient_value'], 2) }}</td>
                                <td>{{ number_format($s['discount'], 2) }}</td>
                                <td>{{ $s['quantity'] }}</td>
                                <td><button wire:click="removeService({{ $index }})" class="btn btn-danger btn-sm"><i class="ri-delete-bin-6-line"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="row mt-4">
            <div class="col-md-2">
                <label class="form-label">{{ trans('outpatient_trans.total') }}</label>
                <input type="text" class="form-control" readonly value="{{ number_format($total, 2) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ trans('outpatient_trans.contracting_value') }}</label>
                <input type="text" class="form-control" readonly value="{{ number_format($contract_value, 2) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ trans('outpatient_trans.patient_value') }}</label>
                <input type="text" class="form-control" readonly value="{{ number_format($patient_value, 2) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ trans('outpatient_trans.paid') }}</label>
                <input type="number" class="form-control" wire:model="paid">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ trans('outpatient_trans.unpaid') }}</label>
                <input type="text" class="form-control" readonly value="{{ number_format($unpaid, 2) }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button wire:click="save" class="btn btn-primary w-100">
                    <i class="ri-save-line"></i> {{ trans('buttons.save') }}
                </button>
            </div>
        </div>
    </div>
</div>
