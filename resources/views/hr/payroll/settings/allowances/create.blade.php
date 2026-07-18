@extends('layouts.master')

@section('title')
    {{ trans('hr.add_allowance') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.add_allowance') }}</h4>
            </div>
        </div>
    </div>
    <!-- End Page Title -->

    <!-- Start Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('hr_allowances.store') }}" method="POST" class="p-4">
                        @csrf
                        <div class="row g-4">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ trans('hr.name') }}</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Allowance Type -->
                            <div class="col-md-6">
                                <label for="allowance_id" class="form-label">{{ trans('hr.allowance_type') }}</label>
                                <select name="allowance_id" id="allowance_id" class="js-example-basic-single" required>
                                    <option value="">--Select--</option>
                                    @foreach($allowance_types as $type)
                                        <option value="{{ $type->id }}" {{ old('allowance_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('allowance_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label">{{ trans('hr.amount') }}</label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                    value="{{ old('amount') }}" required>
                                @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.save') }}</button>
                            <a href="{{ route('hr_allowances.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->

    <script>
        $(document).ready(function() {
            // Initialize Select2 if used
            if ($.fn.select2) {
                $('.js-example-basic-single').select2();
            }
        });
    </script>
@endsection 