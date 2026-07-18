@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_allowance_type') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.edit_allowance_type') }}</h4>
            </div>
        </div>
    </div>

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
                    <form action="{{ route('hr_allowances_types.update', $allowance_type->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">{{ trans('hr.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar', $allowance_type->name_ar) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('hr.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en', $allowance_type->name_en) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.update') }}</button>
                            <a href="{{ route('hr_allowances_types.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 