@extends('layouts.master_table')

@section('title')
    {{ isset($company)
        ? trans('suppliers.edit_supplier_company')
        : trans('suppliers.create_supplier_company') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('suppliers_company.index') }}">
                                {{ trans('suppliers.supplier_companies') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($company)
                                ? trans('suppliers.edit_supplier_company')
                                : trans('suppliers.create_supplier_company') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        {{ isset($company)
                            ? trans('suppliers.edit_supplier_company')
                            : trans('suppliers.create_supplier_company') }}
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST"
                          action="{{ isset($company)
                              ? route('suppliers_company.update', $company->id)
                              : route('suppliers_company.store') }}">
                        @csrf
                        @if(isset($company))
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.manual_code') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input name="manual_code"
                                       value="{{ old('manual_code', $company->manual_code ?? '') }}"
                                       class="form-control">
                                @error('manual_code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.name_ar') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input name="name_ar"
                                       value="{{ old('name_ar', $company->name_ar ?? '') }}"
                                       class="form-control">
                                @error('name_ar')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.name_en') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input name="name_en"
                                       value="{{ old('name_en', $company->name_en ?? '') }}"
                                       class="form-control">
                                @error('name_en')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.activity') }}
                                </label>
                                <input name="activity"
                                       value="{{ old('activity', $company->activity ?? '') }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.commercial_registration_no') }}
                                </label>
                                <input name="commercial_registration_no"
                                       value="{{ old('commercial_registration_no', $company->commercial_registration_no ?? '') }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.tax_card_no') }}
                                </label>
                                <input name="tax_card_no"
                                       value="{{ old('tax_card_no', $company->tax_card_no ?? '') }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.domain') }}
                                </label>
                                <input name="domain"
                                       value="{{ old('domain', $company->domain ?? '') }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.phone') }}
                                </label>
                                <input name="phone"
                                       value="{{ old('phone', $company->phone ?? '') }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.email') }}
                                </label>
                                <input name="email"
                                       value="{{ old('email', $company->email ?? '') }}"
                                       class="form-control">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ trans('suppliers.fax') }}
                                </label>
                                <input name="fax"
                                       value="{{ old('fax', $company->fax ?? '') }}"
                                       class="form-control">
                            </div>

                            {{-- الدولة --}}
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('suppliers.country') }}</label>
                                <select name="country_id" id="country_id" class="form-select">
                                    <option value="">{{ trans('supply_chain_trans.choose') }}</option>
                                    @foreach($countries as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('country_id', $company->country_id ?? '') == $c->id ? 'selected' : '' }}>
                                        {{ $c->getTranslation('name', app()->getLocale()) }}
                                    </option>

                                    @endforeach
                                </select>
                                @error('country_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- المحافظة --}}
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('suppliers.government') }}</label>
                                <select name="government_id" id="government_id" class="form-select">
                                    <option value="">{{ trans('supply_chain_trans.choose') }}</option>
                                    @foreach($governments as $g)
                                        <option value="{{ $g->id }}"
                                            {{ old('government_id', $company->government_id ?? '') == $g->id ? 'selected' : '' }}>
                                            {{ app()->getLocale() === 'ar' ? $g->name_ar : $g->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('government_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- المدينة --}}
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('suppliers.city') }}</label>
                                <select name="city_id" id="city_id" class="form-select">
                                    <option value="">{{ trans('supply_chain_trans.choose') }}</option>
                                    @foreach($cities as $ct)
                                        <option value="{{ $ct->id }}"
                                            {{ old('city_id', $company->city_id ?? '') == $ct->id ? 'selected' : '' }}>
                                            {{ app()->getLocale() === 'ar' ? $ct->name_ar : $ct->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">{{ trans('suppliers.notes') }}</label>
                                <textarea name="notes" class="form-control">{{ old('notes', $company->notes ?? '') }}</textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ trans('suppliers.status') }} <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="active"
                                        {{ old('status', $company->status ?? '') === 'active' ? 'selected' : '' }}>
                                        {{ trans('suppliers.active') }}
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $company->status ?? '') === 'inactive' ? 'selected' : '' }}>
                                        {{ trans('suppliers.inactive') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">{{ trans('general.save') }}</button>
                            <a href="{{ route('suppliers_company.index') }}" class="btn btn-secondary">{{ trans('general.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(function() {
        const chooseText  = '{{ trans("supply_chain_trans.choose") }}';
        const govUrlBase  = '{{ url("suppliers_company/get-governments") }}/';
        const cityUrlBase = '{{ url("suppliers_company/get-cities") }}/';
        const locale     = '{{ app()->getLocale() }}';

        function populateGovernments(countryId, selectedGov = null) {
    $('#government_id').html(`<option value="">${chooseText}</option>`);
    $('#city_id').html(`<option value="">${chooseText}</option>`);
    if (!countryId) return;
    $.get(govUrlBase + countryId, function(items) {
        items.forEach(i => {
            $('#government_id').append(
                `<option value="${i.id}">${i.name}</option>`
            );
        });
        if (selectedGov) $('#government_id').val(selectedGov).trigger('change');
    });
}

function populateCities(govId, selectedCity = null) {
    $('#city_id').html(`<option value="">${chooseText}</option>`);
    if (!govId) return;
    $.get(cityUrlBase + govId, function(items) {
        items.forEach(i => {
            $('#city_id').append(
                `<option value="${i.id}">${i.name}</option>`
            );
        });
        if (selectedCity) $('#city_id').val(selectedCity);
    });
}


        $('#country_id').on('change', function() {
            populateGovernments($(this).val());
        });

        $('#government_id').on('change', function() {
            populateCities($(this).val());
        });

        // تهيئة القيم عند التعديل أو إعادة التوجيه بعد فلاءد
        @if(old('country_id') || isset($company))
            const preCountry = '{{ old("country_id", $company->country_id ?? "") }}';
            const preGov     = '{{ old("government_id", $company->government_id ?? "") }}';
            const preCity    = '{{ old("city_id", $company->city_id ?? "") }}';
            if (preCountry) {
                $('#country_id').val(preCountry);
                populateGovernments(preCountry, preGov);
                if (preGov) populateCities(preGov, preCity);
            }
        @endif
    });
</script>
@endsection
