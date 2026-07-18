@extends('layouts.master')

@section('title', __('jobs.create'))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('jop_settings.store') }}" method="POST">
                @csrf
                <div class="card-header">
                    <h4>{{ __('jobs.create') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Arabic Name -->
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name_ar" class="form-label">{{ __('jobs.name_ar') }}</label>
                                <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                            </div>
                        </div>
                        <!-- English Name -->
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name_en" class="form-label">{{ __('jobs.name_en') }}</label>
                                <input type="text" class="form-control" id="name_en" name="name_en" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">{{ __('jobs.save') }}</button>
                    <a href="{{ route('jop_settings.index') }}" class="btn btn-danger">{{ __('jobs.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
