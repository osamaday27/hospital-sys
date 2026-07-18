@extends('layouts.masterliveware')

@section('title')
    {{ trans('bank_trans.cheque_issue') }}
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
                                <li class="breadcrumb-item"><a href="#">{{ __('bank_trans.bank_programs_title') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('bank_trans.cheque_issue') }}</li>
                            </ol>
                        </nav>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="row justify-content-evenly mt-3">
        <div class="col-lg-12">
            @livewire('finance.banks.cheque-issue')
        </div>
    </div>
@endsection
