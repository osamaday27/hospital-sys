<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
            <i class="ri-contract-line me-2"></i>
            {{ __('contract_settings.title') }}
        </h4>
    </div>
        <script>
        window.addEventListener('show-success', event => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: event.detail.message,
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>


  @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="ri-checkbox-circle-line me-2"></i>
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
    <div class="card-body">
        <form wire:submit.prevent="save">
            @csrf
            <div class="row">
                <!-- Discount Percentage and Patient Contribution Sections -->
                @foreach (['all_services', 'clinic_services', 'inpatient_services', 'surgical_services', 'lab_services', 'emergency_services', 'rad_services', 'medications', 'supplies'] as $type)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="ri-percent-line me-2"></i>
                                    {{ __('contract_settings.' . $type . '_title') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="ri-discount-percent-line me-1"></i>
                                        {{ __('contract_settings.discount_percentage_' . $type) }}
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                            wire:model="discount_percentage_{{ $type }}" class="form-control">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('discount_percentage_' . $type)
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-0">
                                    <label class="form-label">
                                        <i class="ri-user-shared-line me-1"></i>
                                        {{ __('contract_settings.patient_contribution_' . $type) }}
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                            wire:model="patient_contribution_{{ $type }}" class="form-control">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('patient_contribution_' . $type)
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Services Configuration Section -->
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">
                        <i class="ri-list-settings-line me-2"></i>
                        {{ __('contract_settings.services_configuration') }}
                    </h5>
                    <div class="d-flex">
                        <div class="input-group ms-3" style="width: 250px;">
                            <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                            <input type="text" class="form-control"
                                placeholder="{{ __('contract_settings.search_services') }}"
                                wire:model.debounce.500ms="search">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>{{ __('contract_settings.service_name') }}</th>
                                <th width="150" class="text-center">{{ __('contract_settings.require_approval') }}
                                </th>
                                <th width="150" class="text-center">{{ __('contract_settings.not_covered') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($all_services as $index => $srv)
                                <tr
                                    class="@if (in_array($srv->id, $services_require_approval_ids ?? [])) table-warning @endif
                                           @if (in_array($srv->id, $services_not_covered_ids ?? [])) table-danger @endif">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ri-service-line me-2 text-primary" style="color: #1373c3 !important;"></i>
                                            {{ $srv->name }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:model="services_require_approval_ids" value="{{ $srv->id }}">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:model="services_not_covered_ids" value="{{ $srv->id }}">
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="ri-emotion-sad-line display-5"></i>
                                        <p class="mt-2 mb-0">{{ __('contract_settings.no_services_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>


                    <!-- Pagination -->
                    @if ($all_services->hasPages())
                        <div class="card-footer border-top bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    {{ trans('datatables.showing') }} {{ $all_services->firstItem() }} -
                                    {{ $all_services->lastItem() }}
                                    {{ trans('datatables.of') }} {{ $all_services->total() }}
                                </div>
                                <div class="mt-2 d-flex justify-content-center">
                                    {{ $all_services->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" wire:click="resetForm">
                    <i class="ri-refresh-line me-1"></i>
                    {{ __('contract_settings.reset') }}
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i>
                    {{ __('contract_settings.save') }}
                </button>
            </div>
  @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="ri-checkbox-circle-line me-2"></i>
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
          
        </form>
    </div>
</div>
