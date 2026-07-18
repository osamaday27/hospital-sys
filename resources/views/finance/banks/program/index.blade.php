@extends('layouts.master')

@section('title')
    {{ trans('bank_trans.bank_programs_title') }}
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

        <!-- قسم برامج البنوك -->
        <div class="row mt-4">
            <div class="col-12">
                @include('partials.icon', [
                    'title' => __('bank_trans.bank_programs_title'),
                    'cards' => [
                        // دورة الشيكات
                        [
                            'icon' => 'mdi mdi-book-open-variant',
                            'text' => __('bank_trans.cheque_books'),
                            'link' => route('finance_bank_cheques.ChequeBooks'),
                        ],
                
                        [
                            'icon' => 'mdi mdi-cash-plus',
                            'text' => __('bank_trans.cheque_issue'),
                            'link' => route('finance_bank_cheques.issue'),
                        ],
                        [
                            'icon' => 'mdi mdi-check-decagram',
                            'text' => __('bank_trans.workflow'),
                            'link' => route('finance_bank_cheques.approval'),
                        ],
                
                        [
                            'icon' => 'mdi mdi-scale-balance',
                            'text' => __('bank_trans.cheque_reconciliation'),
                            'link' => route('finance_bank_cheques.reconciliation'),
                        ],
                
                        // الحركات البنكية الأخرى
                        [
                            'icon' => 'mdi mdi-bank-transfer',
                            'text' => __('bank_trans.bank_transfers'),
                            'link' => route('finance_bank_transfers'),
                        ],
                        [
                            'icon' => 'mdi mdi-bank-transfer',
                            'text' => __('bank_trans.BankAccountDashboard'),
                            'link' => route('finance.banks.dashboard'),
                        ],
                    ],
                ])
            </div>
        </div>
    </div>
@endsection
