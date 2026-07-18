@extends('layouts.master')

@section('title')
    {{ trans('surgical_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('surgical_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('surgicaldatasettings.index') }}">{{ trans('surgical_trans.surgical_dataset') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('surgical_trans.create_surgical_setting') }}</li>
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

                    <form action="{{ route('surgicaldatasettings.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h4 class="mb-sm-0 font" style="padding-bottom: 20px;">{{ $service->name }}</h4>
                                    <input type="hidden" id="service_id" name="service_id" value="{{ $service->id }}">
                                </div>
                            </div>

                            @foreach (['name', 'description', 'possible_complications', 'preparation_instructions'] as $field)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="{{ $field }}_ar">{{ trans("surgical_trans.$field" . '_ar') }}</label>
                                        <input type="text" class="form-control" id="{{ $field }}_ar"
                                            name="{{ $field }}_ar"
                                            value="{{ old($field . '_ar', $surgicaldatasetting->{$field . '_ar'} ?? '') }}">
                                        @error($field . '_ar')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="{{ $field }}_en">{{ trans("surgical_trans.$field" . '_en') }}</label>
                                        <input type="text" class="form-control" id="{{ $field }}_en"
                                            name="{{ $field }}_en"
                                            value="{{ old($field . '_en', $surgicaldatasetting->{$field . '_en'} ?? '') }}">
                                        @error($field . '_en')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            @foreach (['category' => ['minor', 'major', 'advanced'], 'technique' => ['open', 'laparoscopic', 'robotic', 'other'], 'complexity' => ['simple', 'moderate', 'complex'], 'surgical_method' => ['open', 'laparoscopic', 'robotic'], 'anesthesia_type' => ['general', 'local', 'regional', 'other'], 'ventilation_method' => ['mask', 'laryngeal_tube', 'spontaneous']] as $field => $options)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="{{ $field }}">{{ trans("surgical_trans.$field") }}</label>
                                        <select class="js-example-basic-single form-control" id="{{ $field }}"
                                            name="{{ $field }}">
                                            <option value="">{{ trans("surgical_trans.select_$field") }}</option>
                                            @foreach ($options as $option)
                                                <option value="{{ $option }}"
                                                    {{ old($field, $surgicaldatasetting->$field ?? '') == $option ? 'selected' : '' }}>
                                                    {{ trans("surgical_trans.$option") }}</option>
                                            @endforeach
                                        </select>
                                        @error($field)
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            @foreach (['expected_duration', 'anesthesia_duration'] as $field)
                                @php
                                    // تفكيك البيانات: استخراج الساعات والدقائق
                                    $hours = old(
                                        $field . '_hours',
                                        isset($surgicaldatasetting->{$field})
                                            ? explode(':', $surgicaldatasetting->{$field})[0]
                                            : 0,
                                    );
                                    $minutes = old(
                                        $field . '_minutes',
                                        isset($surgicaldatasetting->{$field})
                                            ? explode(':', $surgicaldatasetting->{$field})[1]
                                            : 0,
                                    );
                                @endphp

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="{{ $field }}_hours">{{ trans("surgical_trans.$field") }}
                                            ({{ trans('surgical_trans.hours') }})</label>
                                        <input type="number" class="form-control" id="{{ $field }}_hours"
                                            name="{{ $field }}_hours" min="0" value="{{ $hours }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="{{ $field }}_minutes">{{ trans("surgical_trans.$field") }}
                                            ({{ trans('surgical_trans.minutes') }})</label>
                                        <input type="number" class="form-control" id="{{ $field }}_minutes"
                                            name="{{ $field }}_minutes" min="0" max="59"
                                            value="{{ $minutes }}">
                                    </div>
                                </div>
                            @endforeach


                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="blood_compatibility_test"
                                        name="blood_compatibility_test" value="1"
                                        {{ old('blood_compatibility_test', $surgicaldatasetting->blood_compatibility_test ?? '') ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="blood_compatibility_test">{{ trans('surgical_trans.blood_compatibility_test') }}</label>
                                </div>
                            </div>

                            @foreach (['pre_op_tests', 'pre_op_medications', 'surgical_tools', 'anesthesia_medications'] as $field)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="{{ $field }}">{{ trans("surgical_trans.$field") }}</label>
                                        <textarea class="form-control" id="{{ $field }}" name="{{ $field }}">{{ old($field, $surgicaldatasetting->$field ?? '') }}</textarea>
                                        @error($field)
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">{{ trans('surgical_trans.save') }}</button>
                            <a href="{{ route('surgicaldatasettings.index') }}"
                                class="btn btn-secondary">{{ trans('surgical_trans.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
