@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_job') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.edit_job') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                            href="{{ url('/' . ($page = 'hr_jobs')) }}">{{ trans('hr.job_title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_job') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title -->

    <!-- Start Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-error">
                            {{ session('error') }}
                        </div>
                    @endif


                    <form action="{{ route('hr_jobs.update', $job_id->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('hr.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control"
                                        value="{{ $job_id->name_ar }}" required>
                                    @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- English Name -->
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('hr.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ $job_id->name_en }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="section_id" class="form-label">{{ trans('hr.section') }}</label>
                                <select name="section_id" id="section_id" class="js-example-basic-single" required>
                                    <option value="">{{ trans('hr.select_option') }}</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ old('section_id', $job_id->section_id) == $section->id ? 'selected' : '' }}>{{ $section->name_ar }}</option>
                                    @endforeach
                                </select>
                                @error('section_id')
                                    <span class="text-warning">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <button type="submit"
                                    class="btn btn-success">{{ trans('buttons.save') }}</button>
                                <a href="{{ route('hr_jobs.index') }}"
                                    class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                            </div>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var currentSectionId = '{{ old('section_id', $job_id->section_id) }}';
                            var select = document.getElementById('section_id');
                            if (select && currentSectionId) {
                                select.value = currentSectionId;
                                if (window.$ && typeof $('#section_id').trigger === 'function') {
                                    $('#section_id').val(currentSectionId).trigger('change');
                                }
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
