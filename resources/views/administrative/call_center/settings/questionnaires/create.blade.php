
@extends('layouts.master_table')

@section('title')
    {{ trans('call_center_trans.create_questionnaire') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('questionnaires.index') }}">{{ trans('call_center_trans.questionnaires') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.create_questionnaire') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('call_center_trans.create_questionnaire') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('questionnaires.store') }}" method="POST">
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

                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">{{ trans('inpatient_trans.type') }}</label>
                                <select id="type" name="type" class="form-control" required>
                                    <option value="evaluation">{{ trans('call_center_trans.evaluation') }}</option>
                                    <option value="questionnaire">{{ trans('call_center_trans.questionnaire') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ trans('inpatient_trans.status') }}</label>
                                <select  name="status" class="form-control" required>
                                    <option value="1">{{ trans('call_center_trans.active') }}</option>
                                    <option value="0">{{ trans('call_center_trans.inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('questionnaires.index') }}" class="btn btn-secondary">
                                <i class="ri-close-line"></i> {{ trans('call_center_trans.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> {{ trans('call_center_trans.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
