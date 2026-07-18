@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_public_holiday') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ trans('hr.edit_public_holiday') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('hr_public_holidays.index') }}">{{ trans('hr.public_holidays') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_public_holiday') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('hr_public_holidays.update', $holiday->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ trans('hr.public_holiday_name') }}</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ $holiday->name }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from" class="form-label">{{ trans('hr.valid_from') }}</label>
                                    <input type="date" name="from" id="from" class="form-control" value="{{ $holiday->from }}" required>
                                </div>
                       

                           
                                <div class="mb-3">
                                    <label for="to" class="form-label">{{ trans('hr.valid_to') }}</label>
                                    <input type="date" name="to" id="to" class="form-control" value="{{ $holiday->to }}" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ trans('hr.description') }}</label>
                                    <textarea name="description" id="description" class="form-control" rows="3">{{ $holiday->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">{{ trans('buttons.update') }}</button>
                            <a href="{{ route('hr_public_holidays.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
