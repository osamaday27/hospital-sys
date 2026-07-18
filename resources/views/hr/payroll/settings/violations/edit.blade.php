@extends('layouts.master_table')

@section('title')
    {{ trans('hr.edit_violation') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hr_violations.index') }}">{{ trans('hr.violations_title') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_violation') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.edit_violation') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_violations.update', $violation->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="violation_name_en" class="form-label">{{ trans('hr.violation_name_en') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('violation_name_en') is-invalid @enderror" 
                                           id="violation_name_en" name="violation_name_en" 
                                           value="{{ old('violation_name_en', $violation->violation_name_en) }}" required>
                                    @error('violation_name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="violation_name_ar" class="form-label">{{ trans('hr.violation_name_ar') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('violation_name_ar') is-invalid @enderror" 
                                           id="violation_name_ar" name="violation_name_ar" 
                                           value="{{ old('violation_name_ar', $violation->violation_name_ar) }}" required dir="rtl">
                                    @error('violation_name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ trans('hr.update') }}</button>
                            <a href="{{ route('hr_violations.index') }}" class="btn btn-light">{{ trans('hr.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Arabic input handling
            $('#violation_name_ar').on('keydown', function(e) {
                if (e.keyCode == 32) { // Space bar
                    e.preventDefault();
                    var start = this.selectionStart;
                    var end = this.selectionEnd;
                    var value = $(this).val();
                    
                    $(this).val(value.substring(0, start) + ' ' + value.substring(end));
                    
                    this.selectionStart = this.selectionEnd = start + 1;
                }
            });

            // Form submission handling
            $('form').submit(function() {
                $('button[type="submit"]').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ trans("hr.updating") }}');
            });
        });
    </script>
@endpush