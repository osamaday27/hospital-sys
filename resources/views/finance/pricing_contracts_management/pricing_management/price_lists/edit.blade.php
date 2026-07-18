@extends('layouts.master')

@section('title')
    {{ trans('pricing_list.edit') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('pricing_list.edit') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('price_lists.index') }}">{{ trans('pricing_list.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('pricing_list.edit') }}</li>
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

                    <form action="{{ route('price_lists.update', $priceList->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">


                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">{{ trans('pricing_list.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control"
                                        value="{{ old('name_ar', $priceList->getTranslation('name', 'ar')) }}" required>
                                    @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('pricing_list.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en', $priceList->getTranslation('name', 'en')) }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fiscal Year -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fiscal_year"
                                        class="form-label">{{ trans('pricing_list.fiscal_year') }}</label>
                                    <input type="text" name="fiscal_year" id="fiscal_year" class="form-control"
                                        value="{{ old('fiscal_year', $priceList->fiscal_year) }}" required>
                                    @error('fiscal_year')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Branches -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="branches" class="form-label">{{ trans('pricing_list.branches') }}</label>
                                    <select name="branches" id="branches"class="js-example-basic-single">
                                        <option value="" disabled>{{ trans('pricing_list.select_type') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branches', $priceList->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->getTranslation('name', app()->getLocale()) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branches')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date"
                                        class="form-label">{{ trans('pricing_list.start_date') }}</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ old('start_date', $priceList->start_date) }}" required>
                                    @error('start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">{{ trans('pricing_list.end_date') }}</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ old('end_date', $priceList->end_date) }}" required>
                                    @error('end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- List Type -->
                            <div class="col-md-6" style="margin-top: 36px;">
                                <div class="mb-3">
                                    <label for="list_type" class="form-label">{{ trans('pricing_list.list_type') }}</label>
                                    <div class="form-check form-check-inline">
                                        <label>
                                            <input type="radio" name="list_type" value="cash" required
                                                {{ old('list_type', $priceList->list_type) == 'cash' ? 'checked' : '' }}>
                                            {{ trans('pricing_list.cash') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label>
                                            <input type="radio" name="list_type" value="contracts" required
                                                {{ old('list_type', $priceList->list_type) == 'contracts' ? 'checked' : '' }}>
                                            {{ trans('pricing_list.Contracts') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label>
                                            <input type="radio" name="list_type" value="all" required
                                                {{ old('list_type', $priceList->list_type) == 'all' ? 'checked' : '' }}>
                                            {{ trans('pricing_list.all') }}
                                        </label>
                                    </div>
                                    @error('list_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6" style="margin-top: 36px;">
                                <!-- Status -->
                                <div class="mb-3" style="display: block ruby;padding: 10px;">
                                    <label class="form-label">{{ trans('service_settings.status') }}</label>
                                    <div class="form-check">
                                        <input type="radio" name="status" id="status_active" class="form-check-input"
                                            value="active" {{ $priceList->status == 'active' ? 'checked' : '' }} required>
                                        <label for="status_active"
                                            class="form-check-label">{{ trans('service_settings.active') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="status" id="status_inactive"
                                            class="form-check-input" value="inactive"
                                            {{ $priceList->status == 'inactive' ? 'checked' : '' }}>
                                        <label for="status_inactive"
                                            class="form-check-label">{{ trans('service_settings.inactive') }}</label>
                                    </div>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <!-- Currency -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">{{ trans('pricing_list.currency') }}</label>
                                    <select name="currency" id="currency"class="js-example-basic-single">
                                        <option value="" disabled>{{ trans('pricing_list.select_type') }}</option>
                                        @foreach ($currencys as $currency)
                                            <option value="{{ $currency->id }}"
                                                {{ old('currency', $priceList->currency_id) == $currency->id ? 'selected' : '' }}>
                                                {{ $currency->getTranslation('name', app()->getLocale()) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary">{{ trans('pricing_list.update') }}</button>
                            <a href="{{ route('price_lists.index') }}"
                                class="btn btn-secondary">{{ trans('pricing_list.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
