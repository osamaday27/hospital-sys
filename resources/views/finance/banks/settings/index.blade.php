@extends('layouts.master')

@section('title')
    {{ trans('bank_trans.title') }}
@stop

@section('content')
    <div class="container-fluid py-3" dir="rtl">

        {{-- رسائل التنبيه --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
                <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
                <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- قسم إعدادات البنوك -->
        <div class="row">
            <div class="col-12">
                @include('partials.icon', [
                    'title' => __('bank_trans.bank_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-bank',
                            'text' => __('bank_trans.banks'),
                            'link' => route('finance_banks'),
                        ],
                        [
                            'icon' => 'mdi mdi-domain',
                            'text' => __('bank_trans.branches'),
                            'link' => route('finance_bank_branches'),
                        ],
                        [
                            'icon' => 'mdi mdi-credit-card',
                            'text' => __('bank_trans.bank_accounts'),
                            'link' => route('finance_bank_accounts'),
                        ],
                        [
                            'icon' => 'mdi mdi-cash-multiple',
                            'text' => __('bank_trans.currencies'),
                            'link' => route('finance_currencies'),
                        ],
                        [
                            'icon' => 'mdi mdi-shield-account',
                            'text' => __('bank_trans.bank_permissions'),
                            'link' => route('finance_bank_permissions'),
                        ],
                        [
                            'icon' => 'mdi mdi-scale-balance',
                            'text' => __('bank_trans.BankThresholds'),
                            'link' => route('finance_bank_thresholds'),
                        ],
                        [
                            'icon' => 'mdi mdi-link-variant',
                            'text' => __('bank_trans.gl_integration_title'),
                            'link' => route('finance_bank_gl_integration'),
                        ],
                        [
                           'icon' => 'mdi mdi-currency-usd',
                            'text' => __('bank_trans.default_currency_title'),
                            'link' => route('finance_default_currency'),
                        ],
                    ],
                ])
            </div>
        </div>
        <!-- End Content Row -->

    </div>
@endsection
