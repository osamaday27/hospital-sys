@extends('layouts.master')
@section('title')
{{ trans('main_trans.title') }}
@stop


@section('content')
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ trans('settings_trans.settings') }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('settings_trans.settings') }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('settings_trans.nationalities_settings') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <div class="flex-grow-1">
                                    <form class="tablelist-form" autocomplete="off">

                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="choices-single-default" class="form-label text-muted">Default</label>
                                                    <select class="form-control data-choices" data-choices name="choices-single-default" id="choices-single-default">
                                                        <option value="">This is a placeholder</option>
                                                        <option value="Choice 1">Choice 1</option>
                                                        <option value="Choice 2">Choice 2</option>
                                                        <option value="Choice 3">Choice 3</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="recipient-name" class="col-form-label">{{ trans('settings_trans.name_ar') }}:</label>
                                                    <input type="text" class="form-control" id="recipient-name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="recipient-name" class="col-form-label">{{ trans('settings_trans.name_en') }}:</label>
                                                    <input type="text" class="form-control" id="recipient-name">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#varyingcontentModal" data-bs-whatever="@mdo" class="btn btn-info add-btn" data-bs-toggle="modal" data-bs-target="#showModal"><i class="ri-add-fill me-1 align-bottom"></i>{{ trans('settings_trans.add') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
@endsection
