@extends('layouts.masterliveware')

@section('title')
    {{ trans('bank_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">{{ __('bank_trans.home') }}</a></li>
                                <li class="breadcrumb-item"><a href="banks_settings">{{ __('bank_trans.settings') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('bank_trans.currencies_title') }}</li>
                            </ol>
                        </nav>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
        <!-- Content Row -->
        <div class="row justify-content-evenly mt-3">

            <!-- Service Management Section -->
            <div class="col-lg-12">
             
                @livewire('finance.banks.currencies')
            </div>
        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->
    </div>
@endsection
