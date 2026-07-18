<div class="card">
    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mb-4 g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">
                    <i class="mdi mdi-medical-bag me-1"></i> {{ trans('surgical_trans.service_select') }}
                </label>
                <div class="form-group">
                    <select wire:model="service_id" class="form-select select2-searchable" id="serviceSelect">
                        <option value="">{{ trans('surgical_trans.choose') }}...</option>
                        @foreach ($serviceOptions as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">
                    <i class="mdi mdi-numeric me-1"></i> {{ trans('surgical_trans.quantity') }}
                </label>
                <input type="number" wire:model="quantity" class="form-control" min="1">
            </div>
            
            <div class="col-md-2">
                <button class="btn btn-primary waves-effect waves-light" wire:click="addService">
                    <i class="mdi mdi-plus-circle-outline me-1"></i> {{ trans('surgical_trans.addservis') }}
                </button>
            </div>
        </div>

        @php
            // دمج المحفوظ + الجديد إن وُجد
            $hasMerged   = isset($allRows) && is_array($allRows);
            $rows        = $hasMerged ? $allRows : ($services ?? []);
            $savedCount  = isset($saved_services) && is_array($saved_services) ? count($saved_services) : 0;
        @endphp

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th><i class="mdi mdi-medical-bag me-1"></i> {{ trans('surgical_trans.service') }}</th>
                        <th><i class="mdi mdi-calculator me-1"></i> {{ trans('surgical_trans.total') }}</th>
                        <th><i class="mdi mdi-handshake me-1"></i> {{ trans('surgical_trans.contract_share') }}</th>
                        <th><i class="mdi mdi-account me-1"></i> {{ trans('surgical_trans.patient_share') }}</th>
                        <th><i class="mdi mdi-tag-off me-1"></i> {{ trans('surgical_trans.discount') }}</th>
                        <th><i class="mdi mdi-numeric me-1"></i> {{ trans('surgical_trans.quantity') }}</th>
                        <th>{{ trans('surgical_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $index => $row)
                        @php
                            // صف محفوظ لو 'is_saved' = true أو لو بنعرض من $services القديمة من القاعدة
                            $isSavedRow = isset($row['is_saved']) ? (bool)$row['is_saved'] : ($hasMerged ? false : true);
                            // لو الصف جديد داخل الدمج: إزاحته داخل مصفوفة الجديد تساوي (index - savedCount)
                            $newIndex   = $index - $savedCount;
                        @endphp
                        <tr>
                            <td>
                                {{ $row['name'] ?? '' }}
                                @if($isSavedRow)
                                    <span class="badge bg-success ms-2">{{ __('محفوظ') }}</span>
                                @else
                                    <span class="badge bg-secondary ms-2">{{ __('جديد') }}</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float)($row['total'] ?? 0), 2) }}</td>
                            <td class="text-end">{{ number_format((float)($row['contract_value'] ?? 0), 2) }}</td>
                            <td class="text-end">{{ number_format((float)($row['patient_value'] ?? 0), 2) }}</td>
                            <td class="text-end">{{ number_format((float)($row['discount'] ?? 0), 2) }}</td>
                            <td class="text-center">{{ (int)($row['quantity'] ?? 1) }}</td>
                            <td class="text-center">
                                @if(!$isSavedRow)
                                    <button class="btn btn-danger btn-sm waves-effect waves-light" 
                                            wire:click="removeService({{ $newIndex }})">
                                        <i class="mdi mdi-delete-outline"></i> {{ trans('surgical_trans.delete') }}
                                    </button>
                                @else
                                    <span class="text-muted">{{ __('لا يمكن حذف المحفوظ من هنا') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="mdi mdi-information-outline me-1"></i> 
                                {{ trans('surgical_trans.no_services_added') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button wire:click="save" class="btn btn-success waves-effect waves-light">
                <i class="mdi mdi-content-save-check-outline me-1"></i> {{ trans('surgical_trans.save_services') }}
            </button>
        </div>
       <!-- <div class="row mt-4 g-3">
            <div class="col-md-3">
                <div class="border p-3 rounded bg-light">
                    <label class="form-label fw-bold text-primary">
                        <i class="mdi mdi-account me-1"></i> {{ trans('surgical_trans.patient_total') }}
                    </label>
                    <input type="text" class="form-control bg-white" wire:model="patient_value" readonly>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="border p-3 rounded bg-light">
                    <label class="form-label fw-bold text-danger">
                        <i class="mdi mdi-tag-off me-1"></i> {{ trans('surgical_trans.discount') }}
                    </label>
                    <input type="number" class="form-control bg-white" wire:model="discount" readonly>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="border p-3 rounded bg-light">
                    <label class="form-label fw-bold text-success">
                        <i class="mdi mdi-cash-check me-1"></i> {{ trans('surgical_trans.paid') }}
                    </label>
                    <input type="number" class="form-control bg-white" wire:model="paid" readonly>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="border p-3 rounded bg-light">
                    <label class="form-label fw-bold text-warning">
                        <i class="mdi mdi-cash-remove me-1"></i> {{ trans('surgical_trans.remaining') }}
                    </label>
                    <input type="text" class="form-control bg-white" wire:model="unpaid" readonly>
                </div>
            </div>
        </div>

    --></div>
</div>

<style>
    .select2-searchable.select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-searchable.select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        color: #495057;
    }

    .select2-searchable.select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }

    .select2-searchable.select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 6px 12px;
    }

    .select2-searchable.select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #405189;
        color: white;
    }

    .select2-searchable.select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #f3f6f9;
        color: #495057;
    }

    .select2-searchable.select2-container--open .select2-dropdown {
        border-color: #405189;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 0.375rem;
    }
    
    .table th {
        white-space: nowrap;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
<script>
    document.addEventListener('livewire:load', function () {
        initSelect2();

        Livewire.hook('message.processed', (message, component) => {
            initSelect2();
        });

        function initSelect2() {
            let el = $('#serviceSelect');
            if (!el.hasClass("select2-hidden-accessible")) {
                el.select2({
                    placeholder: '{{ trans("surgical_trans.choose_service") }}',
                    width: '100%',
                    dropdownParent: el.closest('.form-group'),
                    allowClear: true,
                    minimumResultsForSearch: 3,
                    language: {
                        noResults: function() {
                            return "{{ trans('surgical_trans.no_results_found') }}";
                        }
                    }
                });

                el.on('change', function () {
                    @this.set('service_id', $(this).val());
                });
            }
        }
    });
</script>

<style>
    .select2-searchable.select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-searchable.select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        color: #495057;
    }

    .select2-searchable.select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }

    .select2-searchable.select2-container--open .select2-dropdown {
        border-color: #405189;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 0.375rem;
    }
</style>
