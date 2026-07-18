@extends('layouts.masterliveware')

@section('title')
    {{ trans('surgical_trans.copy_surgical_settings') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- 🧭 عنوان الصفحة والخبز --}}
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font ">{{ trans('surgical_trans.copy_surgical_settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('surgical_settings.index') }}">
                             {{ trans('surgical_trans.settings') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ trans('surgical_trans.copy_surgical_settings') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- 🧩 مكون النسخ --}}
    <div class="row">
        <div class="col-12">
            @livewire('surgical.copy-surgical-tools')
        </div>
    </div>
@endsection
