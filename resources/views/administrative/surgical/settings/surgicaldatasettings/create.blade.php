@extends('layouts.masterliveware')

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
            @livewire('surgical.setting-form', ['id' => Crypt::encryptString($service->id)])

        </div>
    </div>
@endsection
