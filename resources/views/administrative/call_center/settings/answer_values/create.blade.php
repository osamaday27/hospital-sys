@extends('layouts.master_table')

@section('title')
    {{ trans('call_center_trans.create_value') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('answer_values.index') }}">{{ trans('call_center_trans.answers_value') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.create_value') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ trans('call_center_trans.create_value') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('answer_values.store') }}" method="POST" id="answer-value-form">
                        @csrf
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="name_ar" class="form-label">{{ trans('inpatient_trans.name_ar') }}</label>
                                <input type="text" id="name_ar" name="name_ar" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name_en" class="form-label">{{ trans('inpatient_trans.name_en') }}</label>
                                <input type="text" id="name_en" name="name_en" class="form-control" required>
                            </div>
                            <input type="hidden" id="type_id" name="type_id" value="{{ $answerType->id }}">
                            @if ($answerType->type == 'value')
                                <div class="mb-3">
                                    <label for="value" class="form-label">{{ trans('inpatient_trans.value') }}</label>
                                    <input type="number" id="value" name="value" class="form-control">
                                </div>
                            @endif
                        </div>

                        <div class="text-end">
                            <a href="{{ url()->previous() }}" class="btn btn-light">{{ trans('buttons.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ trans('buttons.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
