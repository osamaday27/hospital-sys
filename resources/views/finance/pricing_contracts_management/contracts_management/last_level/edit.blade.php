@extends('layouts.master')

@section('title')
    {{ trans('contract_settings.edit_last_level_contract') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('contract_settings.edit_last_level_contract') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('management_contract_last_level.index') }}">{{ trans('contract_settings.contracts') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('contract_settings.edit_first_level_contract') }}</li>
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

                    <form action="{{ route('management_contract_last_level.update', $contract->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Price List -->
                                <div class="mb-3">
                                    <label for="second_level_id"
                                        class="form-label">{{ trans('contract_settings.first_level_contract') }}</label>
                                    <select name="second_level_id" id="second_level_id" class="js-example-basic-single"
                                        required>
                                        <option value="">{{ trans('contract_settings.select') }}</option>
                                        @foreach ($contractsecondlevel as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == old('second_level_id', $contract->second_level_id) ? 'selected' : '' }}>
                                                {{ $data->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('second_level_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Company Code -->
                                <div class="mb-3">
                                    <label for="company_code"
                                        class="form-label">{{ trans('contract_settings.company_code') }}</label>
                                    <input type="text" name="company_code" id="company_code" class="form-control"
                                        value="{{ old('company_code', $contract->company_code) }}" required>
                                    @error('company_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('contract_settings.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control"
                                        value="{{ old('name_ar', $contract->getTranslation('name', 'ar')) }}" required>
                                    @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- English Name -->
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('contract_settings.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_ar', $contract->getTranslation('name', 'en')) }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Commercial Register -->
                                <div class="mb-3">
                                    <label for="commercial_register"
                                        class="form-label">{{ trans('contract_settings.commercial_register') }}</label>
                                    <input type="text" name="commercial_register" id="commercial_register"
                                        class="form-control"
                                        value="{{ old('commercial_register', $contract->commercial_registration) }}" required>
                                    @error('commercial_register')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address"
                                        class="form-label">{{ trans('contract_settings.address') }}</label>
                                    <input type="text" name="address" id="address" class="form-control"
                                        value="{{ old('address', $contract->address) }}" required>
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Notes -->
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ trans('contract_settings.notes') }}</label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        value="{{ old('notes', $contract->notes) }}">
                                    @error('notes')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Claims Due Period (Days) -->
                                <div class="mb-3">
                                    <label for="claims_due_period"
                                        class="form-label">{{ trans('contract_settings.claims_due_period') }}</label>
                                    <input type="number" name="claim_due_period" id="claims_due_period"
                                        class="form-control"
                                        value="{{ old('claim_due_period', $contract->claim_due_period) }}" required>
                                    @error('claim_due_period')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Sending Period (Days) -->
                                <div class="mb-3">
                                    <label for="sending_period"
                                        class="form-label">{{ trans('contract_settings.sending_period') }}</label>
                                    <input type="number" name="send_period" id="sending_period" class="form-control"
                                        value="{{ old('send_period', $contract->send_period) }}" required>
                                    @error('sending_period')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Contract Start Date -->
                                <div class="mb-3">
                                    <label for="contract_start_date"
                                        class="form-label">{{ trans('contract_settings.contract_start_date') }}</label>
                                    <input type="date" name="contract_start_date" id="contract_start_date"
                                        class="form-control"
                                        value="{{ old('contract_start_date', $contract->contract_start_date) }}" required>
                                    @error('contract_start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Contract Type (Cash / Credit) -->
                                <div class="mb-3">
                                    <label for="contract_type"
                                        class="form-label">{{ trans('contract_settings.contract_type') }}</label>
                                    <select name="contract_type" id="contract_type" class="form-control" required>
                                        <option value="cash"
                                            {{ old('contract_type', $contract->entity_type) == 'cash' ? 'selected' : '' }}>
                                            {{ trans('contract_settings.cash') }}</option>
                                        <option value="credit"
                                            {{ old('contract_type', $contract->entity_type) == 'credit' ? 'selected' : '' }}>
                                            {{ trans('contract_settings.credit') }}</option>
                                    </select>
                                    @error('contract_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <button type="submit"
                                    class="btn btn-primary">{{ trans('buttons.update') }}</button>
                                <a href="{{ route('management_contract_last_level.index') }}"
                                    class="btn btn-lastary">{{ trans('buttons.cancel') }}</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
