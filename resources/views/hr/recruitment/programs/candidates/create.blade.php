@extends('layouts.master_table')

@section('title')
    {{ trans('hr.create_candidate') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('hr_candidates.index') }}">{{ trans('hr.recruitment_candidates') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.create_candidate') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.create_candidate') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_candidates.store') }}" method="POST" enctype="multipart/form-data" id="candidate-form">
                        @csrf
                        
                        <!-- Requisition Selection -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="requisition_id" class="form-label">{{ trans('hr.job_requisition') }} <span class="text-danger">*</span></label>
                                    <select name="requisition_id" id="requisition_id" class="form-select js-example-basic-single @error('requisition_id') is-invalid @enderror" required>
                                        <option value="">{{ trans('hr.select_requisition') }}</option>
                                        @foreach($requisitions as $requisition)
                                            <option value="{{ $requisition->id }}" {{ old('requisition_id', request('requisition_id')) == $requisition->id ? 'selected' : '' }}>
                                                {{ $requisition->requisition_name }} - {{ $requisition->job->name_ar ?? $requisition->job->job_title }} ({{ $requisition->candidates->count() }}/{{ $requisition->number_of_candidates }} {{ trans('hr.candidates') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('requisition_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="card border">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ trans('hr.personal_information') }}</h5>
                            </div>
                            <div class="card-body">
                                <!-- Names -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">{{ trans('hr.first_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="first_name" 
                                                   id="first_name" 
                                                   class="form-control @error('first_name') is-invalid @enderror" 
                                                   value="{{ old('first_name') }}" 
                                                   placeholder="{{ trans('hr.enter_first_name') }}"
                                                   required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="second_name" class="form-label">{{ trans('hr.second_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="second_name" 
                                                   id="second_name" 
                                                   class="form-control @error('second_name') is-invalid @enderror" 
                                                   value="{{ old('second_name') }}" 
                                                   placeholder="{{ trans('hr.enter_second_name') }}"
                                                   required>
                                            @error('second_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="third_name" class="form-label">{{ trans('hr.third_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="third_name" 
                                                   id="third_name" 
                                                   class="form-control @error('third_name') is-invalid @enderror" 
                                                   value="{{ old('third_name') }}" 
                                                   placeholder="{{ trans('hr.enter_third_name') }}"
                                                   required>
                                            @error('third_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fourth_name" class="form-label">{{ trans('hr.fourth_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="fourth_name" 
                                                   id="fourth_name" 
                                                   class="form-control @error('fourth_name') is-invalid @enderror" 
                                                   value="{{ old('fourth_name') }}" 
                                                   placeholder="{{ trans('hr.enter_fourth_name') }}"
                                                   required>
                                            @error('fourth_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Birth Date and Gender -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="birth_date" class="form-label">{{ trans('hr.date_birth') }} <span class="text-danger">*</span></label>
                                            <input type="date" 
                                                   name="birth_date" 
                                                   id="birth_date" 
                                                   class="form-control @error('birth_date') is-invalid @enderror" 
                                                   value="{{ old('birth_date') }}" 
                                                   required>
                                            @error('birth_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gender" class="form-label">{{ trans('hr.gender') }} <span class="text-danger">*</span></label>
                                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                                <option value="">{{ trans('hr.select_option') }}</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ trans('hr.male') }}</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ trans('hr.female') }}</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- National ID and Email -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="national_id" class="form-label">{{ trans('hr.identification_number') }} <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="national_id" 
                                                   id="national_id" 
                                                   class="form-control @error('national_id') is-invalid @enderror" 
                                                   value="{{ old('national_id') }}" 
                                                   placeholder="{{ trans('hr.enter_national_id') }}"
                                                   required>
                                            @error('national_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">{{ trans('hr.email') }} <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                   name="email" 
                                                   id="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="{{ trans('hr.enter_email') }}"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- City and Nationality -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="city" class="form-label">{{ trans('hr.city') }} <span class="text-danger">*</span></label>
                                            <select name="city" id="city" class="form-select js-example-basic-single @error('city') is-invalid @enderror" required>
                                                <option value="">{{ trans('hr.select_city') }}</option>
                                                @foreach($cities as $city_item)
                                                    <option value="{{ $city_item->id }}" {{ old('city') == $city_item->id ? 'selected' : '' }}>
                                                        {{ $city_item->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nationality" class="form-label">{{ trans('hr.nationality') }} <span class="text-danger">*</span></label>
                                            <select name="nationality" id="nationality" class="form-select js-example-basic-single @error('nationality') is-invalid @enderror" required>
                                                <option value="">{{ trans('hr.select_nationality') }}</option>
                                                @foreach($nationalities as $nationality_item)
                                                    <option value="{{ $nationality_item->id }}" {{ old('nationality') == $nationality_item->id ? 'selected' : '' }}>
                                                        {{ $nationality_item->name  }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('nationality')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CV File Upload -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="cv_file" class="form-label">{{ trans('hr.cv_file') }}</label>
                                    <input type="file" 
                                           name="cv_file" 
                                           id="cv_file" 
                                           class="form-control @error('cv_file') is-invalid @enderror" 
                                           accept=".pdf,.doc,.docx">
                                    <div class="form-text">{{ trans('hr.cv_file_help') }}</div>
                                    @error('cv_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('hr_candidates.index') }}" class="btn btn-light">
                                        {{ trans('hr.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submit-btn">
                                        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinner"></span>
                                        {{ trans('hr.save') }}
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
        $('.js-example-basic-single').select2();
        
        $('#candidate-form').on('submit', function() {
            $('#submit-btn').prop('disabled', true);
            $('#spinner').removeClass('d-none');
        });
    });
</script>
@endsection
