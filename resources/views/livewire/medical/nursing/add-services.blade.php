{{-- resources/views/livewire/medical/nursing/add-services.blade.php --}}

<div class="card">
    <!-- بطاقة معلومات المريض والحجز -->
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0 text-primary">{{ __('outpatient_trans.patient_reservation_info') }}</h4>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- معلومات المريض --}}
            <div class="col-md-6">
                <div class="border p-3 mb-3 rounded">
                    <h5 class="mb-3">{{ __('outpatient_trans.patient_info') }}</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('outpatient_trans.patient_name') }}:</label>
                            <p class="form-control-plaintext">{{ $patient->full_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('patient_trans.file_number') }}:</label>
                            <p class="form-control-plaintext">{{ $patient->medical_number ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('patient_trans.national_id') }}:</label>
                            <p class="form-control-plaintext">{{ $patient->national_id ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('patient_trans.mobile') }}:</label>
                            <p class="form-control-plaintext">{{ $patient->phone_number ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('patient_trans.birth_date') }}:</label>
                            <p class="form-control-plaintext">{{ $patient->date_of_birth ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- معلومات الحجز --}}
            <div class="col-md-6">
                <div class="border p-3 mb-3 rounded">
                    <h5 class="mb-3">{{ __('outpatient_trans.reservation_info') }}</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('outpatient_trans.reservation_id') }}:</label>
                            <p class="form-control-plaintext">{{ $reservation->id ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('outpatient_trans.reservation_date') }}:</label>
                            <p class="form-control-plaintext">{{ $reservation->date_reservations ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('outpatient_trans.clinic') }}:</label>
                            <p class="form-control-plaintext">{{ $reservation->clinic->name_ar ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('outpatient_trans.doctor') }}:</label>
                            <p class="form-control-plaintext">{{ $reservation->doctor->full_name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('outpatient_trans.reservation_status') }}:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $reservation->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ __('outpatient_trans.status_'.$reservation->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold">{{ __('outpatient_trans.visit_type') }}:</label>
                            <p class="form-control-plaintext">{{ __($reservation->service->name ?? '-') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- قسم إضافة الخدمات --}}
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0 text-primary">{{ __('outpatient_trans.add_services') }}</h4>
        </div>
        <div class="card-body">

            {{-- رسالة النجاح --}}
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            {{-- رسالة الخطأ --}}
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

             {{-- عرض الخدمات السابقة --}}
             @if(count($existingServices) > 0)
             <h5 class="mb-3">{{ __('outpatient_trans.previous_services') }}</h5>
             <div class="table-responsive mb-4">
                 <table class="table table-bordered table-hover">
                     <thead class="table-light">
                         <tr>
                             <th>{{ __('outpatient_trans.sublevel') }}</th>
                             <th>{{ __('outpatient_trans.service') }}</th>
                             <th class="text-center">{{ __('outpatient_trans.quantity') }}</th>
                             <th class="text-end">{{ __('outpatient_trans.contract_value') }}</th>
                             <th class="text-end">{{ __('outpatient_trans.patient_value') }}</th>
                             <th class="text-end">{{ __('outpatient_trans.total_price') }}</th>
                             <th width="4%"></th>
                         </tr>
                     </thead>
                     <tbody>
                         @foreach($existingServices as $index => $item)
                             <tr>
                                 <td>{{ $item['sublevel_name'] }}</td>
                                 <td>{{ $item['service_name'] }}</td>
                                 <td class="text-center">{{ $item['quantity'] }}</td>
                                 <td class="text-end">{{ number_format($item['contract_value'], 2) }}</td>
                                 <td class="text-end">{{ number_format($item['patient_value'], 2) }}</td>
                                 <td class="text-end fw-bold">{{ number_format($item['total_price'], 2) }}</td>
                                 <td class="text-center">
                                     <button wire:click="removeExistingService({{ $index }})"
                                             class="btn btn-danger btn-sm"
                                             title="{{ __('buttons.delete') }}">
                                         {{ __('buttons.delete') }}
                                     </button>
                                 </td>
                             </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
         @endif


            {{-- نموذج إضافة خدمة جديدة --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">{{ __('outpatient_trans.service_sublevel') }}</label>
                    <select wire:model="sublevel_id" class="form-select">
                        <option value="">{{ __('outpatient_trans.select_sublevel') }}</option>
                        @foreach($sublevels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('outpatient_trans.service') }}</label>
                    <select wire:model="service_id" class="form-select">
                        <option value="">{{ __('outpatient_trans.select_service') }}</option>
                        @foreach($servicesOptions as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('outpatient_trans.quantity') }}</label>
                    <input type="number" wire:model="quantity" class="form-control" min="1" value="1">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="addService" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>{{ __('buttons.add') }}
                    </button>
                </div>
            </div>

            {{-- قائمة الخدمات الجديدة --}}
            @if(count($servicesList) > 0)
                            <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('outpatient_trans.sublevel') }}</th>
                                <th>{{ __('outpatient_trans.service') }}</th>
                                <th class="text-center">{{ __('outpatient_trans.quantity') }}</th>
                                <th class="text-end">{{ __('outpatient_trans.contract_value') }}</th>
                                <th class="text-end">{{ __('outpatient_trans.patient_value') }}</th>
                                <th class="text-end">{{ __('outpatient_trans.total_price') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servicesList as $index => $item)
                                <tr>
                                    <td>{{ $item['sublevel_name'] }}</td>
                                    <td>{{ $item['service_name'] }}</td>
                                    <td class="text-center">{{ $item['quantity'] }}</td>
                                    <td class="text-end">{{ number_format($item['contract_value'], 2) }}</td>
                                    <td class="text-end">{{ number_format($item['patient_value'], 2) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($item['total_price'], 2) }}</td>
                                    <td class="text-center">
                                        <button wire:click="removeService({{ $index }})" class="btn btn-danger btn-sm" title="{{ __('buttons.delete') }}">
                                            {{ __('buttons.delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr>
                                <th colspan="3" class="text-end">{{ __('outpatient_trans.total') }}:</th>
                                <th class="text-end">{{ number_format($this->totalContractContribution, 2) }}</th>
                                <th class="text-end">{{ number_format($this->totalPatientContribution, 2) }}</th>
                                <th class="text-end">{{ number_format($this->totalPricing, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button wire:click="resetForm" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-2"></i>{{ __('buttons.reset') }}
                    </button>
                    <button wire:click="saveServices" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>{{ __('buttons.save_services') }}
                    </button>
                </div>
            @else
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>{{ __('outpatient_trans.no_services_added') }}</h4>
                    <p class="mb-0">{{ __('outpatient_trans.add_services_instructions') }}</p>
                </div>
            @endif

        </div>
    </div>
</div>
