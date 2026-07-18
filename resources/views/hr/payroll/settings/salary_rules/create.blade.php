@extends('layouts.master_table')

@section('title')
    {{ trans('hr.add_salary_rule') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hr_salary_rules.index') }}">{{ trans('hr.salary_rules_title') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.add_salary_rule') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.add_salary_rule') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_salary_rules.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ trans('hr.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">{{ trans('hr.type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">{{ trans('hr.select_option') }}</option>
                                        <option value="addition" {{ old('type') === 'addition' ? 'selected' : '' }}>{{ trans('hr.addition') }}</option>
                                        <option value="deduction" {{ old('type') === 'deduction' ? 'selected' : '' }}>{{ trans('hr.deduction') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rule_type" class="form-label">{{ trans('hr.rule_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('rule_type') is-invalid @enderror" 
                                            id="rule_type" name="rule_type" required>
                                        <option value="">{{ trans('hr.select_option') }}</option>
                                    </select>
                                    @error('rule_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="added_automatically" class="form-label">{{ trans('hr.added_automatically') }}</label>
                                    <select class="form-select @error('added_automatically') is-invalid @enderror" 
                                            id="added_automatically" name="added_automatically">
                                        <option value="0" {{ old('added_automatically') === '0' ? 'selected' : '' }}>{{ trans('hr.no') }}</option>
                                        <option value="1" {{ old('added_automatically') === '1' ? 'selected' : '' }}>{{ trans('hr.yes') }}</option>
                                    </select>
                                    @error('added_automatically')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 sequence-field" style="display: none;">
                                <div class="mb-3">
                                    <label for="sequence" class="form-label">{{ trans('hr.sequence') }} <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('sequence') is-invalid @enderror" 
                                           id="sequence" name="sequence" value="{{ old('sequence', 0) }}" min="0" required>
                                    @error('sequence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">{{ trans('hr.status') }}</label>
                                    <select class="form-select @error('is_active') is-invalid @enderror" 
                                            id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>{{ trans('hr.active') }}</option>
                                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>{{ trans('hr.inactive') }}</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line align-bottom me-1"></i> {{ trans('hr.save') }}
                            </button>
                            <a href="{{ route('hr_salary_rules.index') }}" class="btn btn-light">
                                <i class="ri-arrow-go-back-line align-bottom me-1"></i> {{ trans('hr.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        const ruleTypes = {
            deduction: [
                { value: 'absence', label: '{{ __("hr.absence") }}' },
                { value: 'lateness', label: '{{ __("hr.lateness") }}' },
                { value: 'penalties', label: '{{ __("hr.penalties") }}' },
                { value: 'loans', label: '{{ __("hr.loans") }}' },
                { value: 'tax', label: '{{ __("hr.tax") }}' },
                { value: 'insurance', label: '{{ __("hr.social_insurance") }}' },
                { value: 'manual', label: '{{ __("hr.manual") }}' }
            ],
            addition: [
                { value: 'incentive', label: '{{ __("hr.incentive") }}' },
                { value: 'overtime', label: '{{ __("hr.overtime") }}' },
                { value: 'basic_salary', label: '{{ __("hr.basic_salary") }}' },
                { value: 'allowances', label: '{{ __("hr.allowances") }}' },
                { value: 'gross_salary', label: '{{ __("hr.gross_salary") }}' },
                { value: 'net_salary', label: '{{ __("hr.net_salary") }}' },
                { value: 'manual', label: '{{ __("hr.manual") }}' }
            ]
        };

        $('#type').change(function() {
            const type = $(this).val();
            const $ruleType = $('#rule_type');
            $ruleType.empty();
            $ruleType.append('<option value="">{{ __("hr.select_option") }}</option>');

            if (type && ruleTypes[type]) {
                ruleTypes[type].forEach(rule => {
                    const selected = rule.value === '{{ old("rule_type") }}' ? 'selected' : '';
                    $ruleType.append(`<option value="${rule.value}" ${selected}>${rule.label}</option>`);
                });
            }
        });

        // Trigger change event if type is pre-selected
        if ($('#type').val()) {
            $('#type').trigger('change');
        }

        // Show/hide sequence field based on added_automatically
        $('#added_automatically').change(function() {
            if ($(this).val() === '1') {
                $('.sequence-field').show();
                $('#sequence').prop('required', true);
            } else {
                $('.sequence-field').hide();
                $('#sequence').prop('required', false);
            }
        });

        // Trigger change event on page load
        $('#added_automatically').trigger('change');

        // Form submission handling
        $('form').submit(function() {
            $('button[type="submit"]').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ trans("hr.saving") }}');
        });
    });
</script>
@endsection 
