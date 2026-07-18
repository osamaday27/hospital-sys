<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title"style="color:#dee2e6">
            <i class="mdi mdi-heart-pulse me-2" style="color:#dee2e6"></i>
            {{ trans('surgical_trans.surgical_procedure_form') }}
        </h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit.prevent="save">
            <div class="row g-3">
                <!-- Basic Information Section -->
                <div class="col-12">
                    <div class="border-bottom pb-2 mb-3">
                        <h5 class="text-primary">
                            <i class="mdi mdi-information-outline me-1"></i>
                            {{ trans('surgical_trans.basic_information') }}
                        </h5>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" wire:model.defer="name_ar"
                            value="{{ $service->getTranslation('name', 'ar') ?? '' }}" id="name_ar" readonly>
                        <label for="name_ar">{{ trans('surgical_trans.name_ar') }}</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" wire:model.defer="name_en"
                            value="{{ $service->getTranslation('name', 'en') ?? '' }}" id="name_en" readonly>
                        <label for="name_en">{{ trans('surgical_trans.name_en') }}</label>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="col-12">
                    <div class="border-bottom pb-2 mt-4 mb-3">
                        <h5 class="text-primary">
                            <i class="mdi mdi-text-box-outline me-1"></i>
                            {{ trans('surgical_trans.description') }}
                        </h5>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <textarea class="form-control" wire:model.defer="description_ar" id="description_ar" style="height: 100px"></textarea>
                        <label for="description_ar">{{ trans('surgical_trans.description_ar') }}</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <textarea class="form-control" wire:model.defer="description_en" id="description_en" style="height: 100px"></textarea>
                        <label for="description_en">{{ trans('surgical_trans.description_en') }}</label>
                    </div>
                </div>

                <!-- Procedure Details Section -->
                <div class="col-12">
                    <div class="border-bottom pb-2 mt-4 mb-3">
                        <h5 class="text-primary">
                            <i class="mdi mdi-medical-bag me-1"></i>
                            {{ trans('surgical_trans.procedure_details') }}
                        </h5>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select @error('category') is-invalid @enderror" wire:model.defer="category"
                            id="category">
                            <option value="">{{ trans('surgical_trans.choose') }}</option>
                            <option value="minor">{{ trans('surgical_trans.minor') }}</option>
                            <option value="major">{{ trans('surgical_trans.major') }}</option>
                            <option value="advanced">{{ trans('surgical_trans.advanced') }}</option>
                        </select>
                        <label for="category" >{{ trans('surgical_trans.category') }}</label>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select @error('technique') is-invalid @enderror"
                            wire:model.defer="technique" id="technique">
                            <option value="">{{ trans('surgical_trans.choose') }}</option>
                            <option value="open">{{ trans('surgical_trans.open') }}</option>
                            <option value="laparoscopic">{{ trans('surgical_trans.laparoscopic') }}</option>
                            <option value="robotic">{{ trans('surgical_trans.robotic') }}</option>
                            <option value="other">{{ trans('surgical_trans.other') }}</option>
                        </select>
                        <label for="technique">{{ trans('surgical_trans.technique') }}</label>
                        @error('technique')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select @error('complexity') is-invalid @enderror"
                            wire:model.defer="complexity" id="complexity">
                            <option value="">{{ trans('surgical_trans.choose') }}</option>
                            <option value="simple">{{ trans('surgical_trans.simple') }}</option>
                            <option value="moderate">{{ trans('surgical_trans.moderate') }}</option>
                            <option value="complex">{{ trans('surgical_trans.complex') }}</option>
                        </select>
                        <label for="complexity">{{ trans('surgical_trans.complexity') }}</label>
                        @error('complexity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Duration Section -->
                <div class="col-md-6">
                    <label class="form-label">{{ trans('surgical_trans.expected_duration') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                        <input type="number" class="form-control" wire:model.defer="expected_duration_hours"
                            placeholder="{{ trans('surgical_trans.hours') }}">
                        <span class="input-group-text">{{ trans('surgical_trans.hours') }}</span>
                        <input type="number" class="form-control" wire:model.defer="expected_duration_minutes"
                            placeholder="{{ trans('surgical_trans.minutes') }}">
                        <span class="input-group-text">{{ trans('surgical_trans.minutes') }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ trans('surgical_trans.anesthesia_duration') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                        <input type="number" class="form-control" wire:model.defer="anesthesia_duration_hours"
                            placeholder="{{ trans('surgical_trans.hours') }}">
                        <span class="input-group-text">{{ trans('surgical_trans.hours') }}</span>
                        <input type="number" class="form-control" wire:model.defer="anesthesia_duration_minutes"
                            placeholder="{{ trans('surgical_trans.minutes') }}">
                        <span class="input-group-text">{{ trans('surgical_trans.minutes') }}</span>
                    </div>
                </div>

                <!-- Medical Details Section -->
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" wire:model.defer="surgical_method" id="surgical_method">
                            <option value="">{{ trans('surgical_trans.choose') }}</option>
                            <option value="open">{{ trans('surgical_trans.open') }}</option>
                            <option value="laparoscopic">{{ trans('surgical_trans.laparoscopic') }}</option>
                            <option value="robotic">{{ trans('surgical_trans.robotic') }}</option>
                        </select>
                        <label for="surgical_method">{{ trans('surgical_trans.surgical_method') }}</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" wire:model.defer="anesthesia_type" id="anesthesia_type" >
                            <option value="">{{ trans('surgical_trans.choose') }}</option>
                            <option value="general">{{ trans('surgical_trans.general') }}</option>
                            <option value="local">{{ trans('surgical_trans.local') }}</option>
                            <option value="regional">{{ trans('surgical_trans.regional') }}</option>
                            <option value="other">{{ trans('surgical_trans.other') }}</option>
                        </select>
                        <label for="anesthesia_type">{{ trans('surgical_trans.anesthesia_type') }}</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" wire:model.defer="ventilation_method" id="ventilation_method">
                            <option value="">{{ trans('surgical_trans.choose') }}</option>
                            <option value="mask">{{ trans('surgical_trans.mask') }}</option>
                            <option value="laryngeal_tube">{{ trans('surgical_trans.laryngeal_tube') }}</option>
                            <option value="spontaneous">{{ trans('surgical_trans.spontaneous') }}</option>
                        </select>
                        <label for="ventilation_method">{{ trans('surgical_trans.ventilation_method') }}</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="blood_test"
                            wire:model.defer="blood_compatibility_test">
                        <label class="form-check-label" for="blood_test">
                            <i class="mdi mdi-blood-bag me-1"></i>
                            {{ trans('surgical_trans.blood_compatibility_test') }}
                        </label>
                    </div>
                </div>

                <!-- Searchable Multi-Select Sections -->
                <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold">
                        {{ trans('surgical_trans.pre_op_tests') }}
                        <small class="text-muted">({{ count($selected_tests) }}
                            {{ trans('surgical_trans.selected') }})</small>
                    </label>

                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control"
                            placeholder="{{ trans('surgical_trans.search_tests') }}" wire:model="testSearch">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            wire:click="toggleSelectAll('tests')">
                            {{ trans('surgical_trans.select_all') }}
                        </button>
                    </div>

                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30px"></th>
                                    <th>{{ trans('surgical_trans.name') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filteredTests as $id => $name)
                                    <tr>
                                        <td>
                                            <input type="checkbox" wire:model="selected_tests"
                                                value="{{ $id }}">
                                        </td>
                                        <td>{{ $name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            {{ trans('surgical_trans.no_results') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>



                <!-- الأدوية قبل العملية - عرضها كجدول قابل للبحث والاختيار -->
                <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold">
                        {{ trans('surgical_trans.pre_op_medications') }}
                        <small class="text-muted">({{ count($selected_medications) }}
                            {{ trans('surgical_trans.selected') }})</small>
                    </label>

                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control"
                            placeholder="{{ trans('surgical_trans.search_medications') }}"
                            wire:model="medicationSearch">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            wire:click="toggleSelectAll('medications')">
                            {{ trans('surgical_trans.select_all') }}
                        </button>
                    </div>

                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30px"></th>
                                    <th>{{ trans('surgical_trans.name') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filteredMedications as $id => $name)
                                    <tr>
                                        <td>
                                            <input type="checkbox" wire:model="selected_medications"
                                                value="{{ $id }}">
                                        </td>
                                        <td>{{ $name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            {{ trans('surgical_trans.no_results') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- الأدوات الجراحية - عرضها كجدول قابل للبحث والاختيار -->
                <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold">
                        {{ trans('surgical_trans.surgical_tools') }}
                        <small class="text-muted">({{ count($selected_tools) }}
                            {{ trans('surgical_trans.selected') }})</small>
                    </label>

                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control"
                            placeholder="{{ trans('surgical_trans.search_tools') }}" wire:model="toolSearch">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            wire:click="toggleSelectAll('tools')">
                            {{ trans('surgical_trans.select_all') }}
                        </button>
                    </div>

                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30px"></th>
                                    <th>{{ trans('surgical_trans.name') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filteredTools as $id => $name)
                                    <tr>
                                        <td>
                                            <input type="checkbox" wire:model="selected_tools"
                                                value="{{ $id }}">
                                        </td>
                                        <td>{{ $name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            {{ trans('surgical_trans.no_results') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Complications and Instructions -->
                <div class="col-12">
                    <div class="border-bottom pb-2 mt-4 mb-3">
                        <h5 class="text-primary">
                            <i class="mdi mdi-alert-circle-outline me-1"></i>
                            {{ trans('surgical_trans.complications_instructions') }}
                        </h5>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <textarea class="form-control" wire:model.defer="possible_complications_ar" id="complications_ar"
                            style="height: 100px"></textarea>
                        <label for="complications_ar">{{ trans('surgical_trans.possible_complications_ar') }}</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <textarea class="form-control" wire:model.defer="possible_complications_en" id="complications_en"
                            style="height: 100px"></textarea>
                        <label for="complications_en">{{ trans('surgical_trans.possible_complications_en') }}</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <textarea class="form-control" wire:model.defer="preparation_instructions_ar" id="instructions_ar"
                            style="height: 100px"></textarea>
                        <label for="instructions_ar">{{ trans('surgical_trans.preparation_instructions_ar') }}</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <textarea class="form-control" wire:model.defer="preparation_instructions_en" id="instructions_en"
                            style="height: 100px"></textarea>
                        <label for="instructions_en">{{ trans('surgical_trans.preparation_instructions_en') }}</label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="row mt-4">
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-outline-secondary me-2" wire:click="resetForm">
                        <i class="mdi mdi-close-circle-outline me-1"></i>
                        {{ trans('surgical_trans.reset') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i>
                        {{ trans('surgical_trans.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        $('.select2').on('change', function(e) {
            let data = $(this).val();
            let model = $(this).data('model');
            @this.set(model, data);
        });

        Livewire.hook('message.processed', (message, component) => {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    });
</script>

<style>
    .required:after {
        content: " *";
        color: red;
    }

    .form-floating textarea.form-control {
        height: 100px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .border-bottom {
        border-bottom: 1px solid #dee2e6 !important;
    }
</style>
