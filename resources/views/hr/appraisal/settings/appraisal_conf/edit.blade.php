@extends('layouts.master_table')

@section('title')
    {{ trans('hr.edit_appraisal_conf') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('hr_appraisal_conf.index') }}">{{ trans('hr.appraisal_conf_title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_appraisal_conf') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.edit_appraisal_conf') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_appraisal_conf.update', $appraisalConf->id) }}" method="POST" id="appraisal-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="job_id" class="form-label">{{ trans('hr.job_position') }} <span class="text-danger">*</span></label>
                                    <select class="form-select js-example-basic-single @error('job_id') is-invalid @enderror" id="job_id" name="job_id" required>
                                        <option value="">{{ trans('hr.select_option') }}</option>
                                        @foreach($jobs as $job)
                                            <option value="{{ $job->id }}" {{ (old('job_id', $appraisalConf->job_id) == $job->id) ? 'selected' : '' }}>
                                                {{ app()->getLocale() === 'ar' ? $job->name_ar : $job->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('job_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Terms Sections -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ trans('hr.appraisal_terms') }}</h5>
                                <p class="text-muted">{{ trans('hr.terms_selection_help') }}</p>
                            </div>
                        </div>

                        <!-- KPIs Section -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0 text-white">{{ trans('hr.KPIs') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="kpis-container">
                                            @if(isset($termsByCategory['KPIs']) && count($termsByCategory['KPIs']) > 0)
                                                @foreach($termsByCategory['KPIs'] as $termName)
                                                    <div class="term-input-group mb-2">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="terms[KPIs][]" value="{{ $termName }}" placeholder="{{ trans('hr.enter_term_name') }}">
                                                            <input type="number" step="0.01" min="0" max="100" class="form-control ms-2" name="percentages[KPIs][]" value="{{ $appraisalConf->terms->where('term_name', $termName)->where('category', 'KPIs')->first()->percentage ?? '' }}" placeholder="%" style="width: 90px;" oninput="if(this.value>100)this.value=100;">
                                                            <button type="button" class="btn btn-outline-danger remove-term">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="term-input-group mb-2">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="terms[KPIs][]" placeholder="{{ trans('hr.enter_term_name') }}">
                                                        <button type="button" class="btn btn-outline-danger remove-term" style="display: none;">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary add-term" data-category="KPIs">
                                            <i class="ri-add-line"></i> {{ trans('hr.add_term') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Discipline Section -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">{{ trans('hr.discipline') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="discipline-container">
                                            @if(isset($termsByCategory['discipline']) && count($termsByCategory['discipline']) > 0)
                                                @foreach($termsByCategory['discipline'] as $termName)
                                                    <div class="term-input-group mb-2">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="terms[discipline][]" value="{{ $termName }}" placeholder="{{ trans('hr.enter_term_name') }}">
                                                            <input type="number" step="0.01" min="0" max="100" class="form-control ms-2" name="percentages[discipline][]" value="{{ $appraisalConf->terms->where('term_name', $termName)->where('category', 'discipline')->first()->percentage ?? '' }}" placeholder="%" style="width: 90px;" oninput="if(this.value>100)this.value=100;">
                                                            <button type="button" class="btn btn-outline-danger remove-term">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="term-input-group mb-2">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="terms[discipline][]" placeholder="{{ trans('hr.enter_term_name') }}">
                                                        <button type="button" class="btn btn-outline-danger remove-term" style="display: none;">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-warning add-term" data-category="discipline">
                                            <i class="ri-add-line"></i> {{ trans('hr.add_term') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Efficiency Section -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">{{ trans('hr.efficiency') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="efficiency-container">
                                            @if(isset($termsByCategory['efficiency']) && count($termsByCategory['efficiency']) > 0)
                                                @foreach($termsByCategory['efficiency'] as $termName)
                                                    <div class="term-input-group mb-2">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="terms[efficiency][]" value="{{ $termName }}" placeholder="{{ trans('hr.enter_term_name') }}">
                                                            <input type="number" step="0.01" min="0" max="100" class="form-control ms-2" name="percentages[efficiency][]" value="{{ $appraisalConf->terms->where('term_name', $termName)->where('category', 'efficiency')->first()->percentage ?? '' }}" placeholder="%" style="width: 90px;" oninput="if(this.value>100)this.value=100;">
                                                            <button type="button" class="btn btn-outline-danger remove-term">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="term-input-group mb-2">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="terms[efficiency][]" placeholder="{{ trans('hr.enter_term_name') }}">
                                                        <button type="button" class="btn btn-outline-danger remove-term" style="display: none;">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-success add-term" data-category="efficiency">
                                            <i class="ri-add-line"></i> {{ trans('hr.add_term') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('terms')
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-danger">{{ $message }}</div>
                                </div>
                            </div>
                        @enderror

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('hr_appraisal_conf.index') }}" class="btn btn-secondary">
                                        {{ trans('hr.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('hr.update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        // Initialize select2 for job selection
        $('.js-example-basic-single').select2();

        // Add term functionality
        $('.add-term').click(function() {
            const category = $(this).data('category');
            const containerId = category.toLowerCase() + '-container';
            const container = $(`#${containerId}`);
            
            const newGroup = $(`
                <div class="term-input-group mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" name="terms[${category}][]" placeholder="{{ trans('hr.enter_term_name') }}">
                        <button type="button" class="btn btn-outline-danger remove-term">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
            `);
            
            container.append(newGroup);
            updateRemoveButtons();
        });

        // Remove term functionality
        $(document).on('click', '.remove-term', function() {
            $(this).closest('.term-input-group').remove();
            updateRemoveButtons();
        });

        // Update remove buttons visibility
        function updateRemoveButtons() {
            $('.term-input-group').each(function() {
                const container = $(this).closest('.card-body');
                const groups = container.find('.term-input-group');
                
                if (groups.length > 1) {
                    groups.find('.remove-term').show();
                } else {
                    groups.find('.remove-term').hide();
                }
            });
        }

        // Initial update
        updateRemoveButtons();

        // Form validation
        $('#appraisal-form').submit(function(e) {
            let hasTerms = false;
            let totalPercentage = 0;
            $('input[name^="terms["]').each(function(i) {
                if ($(this).val().trim() !== '') {
                    hasTerms = true;
                    // Find the corresponding percentage input
                    let percentInput = $(this).closest('.input-group').find('input[type=number]');
                    let percentVal = parseFloat(percentInput.val()) || 0;
                    totalPercentage += percentVal;
                }
            });
            if (!hasTerms) {
                e.preventDefault();
                alert('{{ trans("hr.at_least_one_term_required") }}');
                return false;
            }
            if (Math.abs(totalPercentage - 100) > 0.01) {
                e.preventDefault();
                alert('{{ trans("hr.total_percentage_must_be_100") }}: ' + totalPercentage.toFixed(2) + '%');
                return false;
            }
        });
    });
</script>
@endsection 