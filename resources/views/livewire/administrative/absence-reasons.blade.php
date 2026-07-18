<div>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white">
                <i class="ri-list-unordered me-2"></i> {{ __('patient_trans.absence_reasons') }}
            </h5>
        </div>
        <div class="card-body">
            {{-- ✅ فورم الإضافة / التعديل --}}
            <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label>{{ __('patient_trans.reason_ar') }}</label>
                        <input type="text" wire:model="name_ar" class="form-control">
                        @error('name_ar') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('patient_trans.reason_en') }}</label>
                        <input type="text" wire:model="name_en" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('patient_trans.status') }}</label>
                        <select wire:model="status" class="form-select">
                            <option value="1">{{ __('patient_trans.active') }}</option>
                            <option value="0">{{ __('patient_trans.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-{{ $updateMode ? 'warning' : 'success' }} w-100">
                            {{ $updateMode ? __('buttons.update') : __('buttons.add') }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- ✅ جدول عرض الأسباب --}}
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('patient_trans.reason_ar') }}</th>
                        <th>{{ __('patient_trans.reason_en') }}</th>
                        <th>{{ __('patient_trans.status') }}</th>
                        <th>{{ __('buttons.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reasons as $reason)
                        <tr>
                            <td>{{ $reason->id }}</td>
                            <td>{{ $reason->name_ar }}</td>
                            <td>{{ $reason->name_en }}</td>
                            <td>
                                @if($reason->status)
                                    <span class="badge bg-success">{{ __('patient_trans.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('patient_trans.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="edit({{ $reason->id }})" class="btn btn-sm btn-warning">
                                    <i class="ri-edit-2-line"></i>
                                </button>
                                <button wire:click="delete({{ $reason->id }})" class="btn btn-sm btn-danger">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $reasons->links() }}
        </div>
    </div>
</div>
