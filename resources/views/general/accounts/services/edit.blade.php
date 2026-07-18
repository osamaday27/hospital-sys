@extends('layouts.master')
@section('title')
    {{ trans('main_trans.title') }}



@stop


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ trans('settings_trans.settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('settings_trans.settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('settings_trans.services') }}</li>
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
                    <h5 class="card-title mb-0">{{ trans('settings_trans.services_settings') }}</h5>
                    {{-- Message --}}
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                                data-dismiss="alert">
                                <i class="fa fa-times"></i>
                            </button>
                            <strong>Success !</strong> {{ session('success') }}
                        </div>
                    @endif

                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                                data-dismiss="alert">
                                <i class="fa fa-times"></i>
                            </button>
                            <strong>Error !</strong> {{ session('error') }}
                        </div>
                    @endif

                </div>
                <div class="card-body">
                    <form action="{{ route('services_settings.update', 'test') }}" method="post">
                        {{ method_field('patch') }}
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <input id="id" name="id" value="{{ $services->id }}" hidden>
                                    <label for="name_ar"
                                        class="form-servicesel">{{ trans('settings_trans.name_ar') }}</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        placeholder="{{ trans('settings_trans.name_ar_enter') }}"
                                        value="{{ $services->getTranslation('name', 'ar') }}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-servicesel">{{ trans('settings_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        placeholder="{{ trans('settings_trans.name_en_enter') }}"
                                        value="{{ $services->getTranslation('name', 'en') }}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-servicesel">{{ trans('settings_trans.description') }}</label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        placeholder="{{ trans('settings_trans.description_enter') }}"
                                        value="{{ $services->description }}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-servicesel">{{ trans('settings_trans.preparation') }}</label>
                                    <input type="text" class="form-control" id="preparation" name="preparation"
                                        placeholder="{{ trans('settings_trans.preparation_enter') }}"
                                        value="{{ $services->preparation }}">
                                </div>
                            </div><!--end col-->
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit"
                                        class="btn btn-primary">{{ trans('settings_trans.update') }}</button>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </form>


                </div>
            </div>
        </div>
    </div>

@endsection
