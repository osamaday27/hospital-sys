@extends('layouts.masterliveware')

@section('title')
    {{ trans('costing_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ url(app()->getLocale().'/costing_settings') }}">
                                    {{ __('costing_trans.costing_settings') }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ __('costing_trans.overhead_pools') }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Section -->
    <div class="row justify-content-evenly mt-3">
        <div class="col-lg-12">
           @livewire('costing.overheadpools')
        </div>
    </div>
@endsection
