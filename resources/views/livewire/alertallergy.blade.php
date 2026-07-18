@if ($allergy_count > 0)
    <div class="alert alert-danger alert-dismissible fade show mt-2 py-1 px-2 w-100" role="alert"
        style="font-size: 0.85rem;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="ri-alarm-warning-fill me-1 text-danger" style="font-size: 1rem;"></i>
                <strong class="me-2 text-danger">⚠️ {{ __('doctor_trans.allergy_alert') }}</strong>
                <span
                    class="badge bg-warning bg-opacity-10 text-danger border border-warning border-opacity-25 ms-2 px-2 py-1"
                    style="font-size: 0.75rem;">
                    {{ $allergy_food_count }}
                </span>
                <span
                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 ms-2 px-2 py-1"
                    style="font-size: 0.75rem;">
                    {{ $allergy_durg_count }}
                </span>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 me-1" data-bs-toggle="modal"
                    data-bs-target="#allergiesModal" style="font-size: 0.75rem;">
                    {{ __('doctor_trans.more') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="allergiesModal" tabindex="-1" aria-labelledby="allergiesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-danger border-2">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title fw-bold" id="allergiesModalLabel" style="color: #fff;">
                        <i class="ri-alarm-warning-line me-2"></i> {{ __('doctor_trans.allergy_full_record') }}
                    </h6>
                    <button type="button" class="btn-close btn-close-white m-0 p-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-danger">
                                <tr>
                                    <th width="120" class="py-2 ps-3">{{ __('doctor_trans.allergy_type') }}</th>
                                    <th class="py-2">{{ __('doctor_trans.substance') }}</th>
                                    <th width="120" class="py-2 pe-3">{{ __('doctor_trans.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allergies as $allergy)
                                    @if ($allergy->drug_allergy)
                                        <tr class="align-middle">
                                            <td class="ps-3">
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-1 px-2"
                                                    style="font-size: 0.7rem;">
                                                    <i class="ri-medicine-bottle-line me-1"></i>
                                                    {{ __('doctor_trans.drug') }}
                                                </span>

                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $allergy->drug_allergy }}</div>
                                                @if ($allergy->notes)
                                                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                        {{ $allergy->notes }}</div>
                                                @endif
                                            </td>
                                            <td class="text-muted small pe-3" style="font-size: 0.75rem;">
                                                {{ $allergy->created_at->format('Y-m-d') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach ($allergies as $allergy)
                                    @if ($allergy->food_allergy)
                                        <tr class="align-middle">
                                            <td class="ps-3">
                                                <span
                                                    class="badge bg-warning  bg-opacity-10 text-warning  border border-warning  border-opacity-25 py-1 px-2"
                                                    style="font-size: 0.7rem;">
                                                    <i class="ri-restaurant-line me-1"></i>
                                                    {{ __('doctor_trans.food') }}
                                                </span>

                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $allergy->food_allergy }}</div>
                                                @if ($allergy->notes)
                                                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                        {{ $allergy->notes }}</div>
                                                @endif
                                            </td>
                                            <td class="text-muted small pe-3" style="font-size: 0.75rem;">
                                                {{ $allergy->created_at->format('Y-m-d') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-danger px-3 py-1" data-bs-dismiss="modal">
                        {{ __('doctor_trans.understood') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
