@extends('layouts.master')

@section('title')
    {{ trans('glaccounts_trans.title') }}
@stop

@section('content')
<div class="container-fluid py-3" dir="rtl">

    {{-- رسائل التنبيه --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- قسم البرامج -->
    <div class="row mt-4">
        <div class="col-12">
            @include('partials.icon', [
                'title' => __('glaccounts_trans.glaccounts_programs'),
                'cards' => [
                    [
                        'icon' => 'mdi mdi-clipboard-check-outline',
                        'text' => __('glaccounts_trans.journal_entries'),
                        'link' => 'gl_entries',
                    ],
                    [
                        'icon' => 'mdi mdi-file-chart-outline',
                        'text' => __('glaccounts_trans.trial_balance'),
                        'link' => 'trial_balance',
                    ],
                    [
                        'icon' => 'mdi mdi-finance',
                        'text' => __('glaccounts_trans.financial_statements'),
                        'link' => 'financial_statements',
                    ],
                    [
                        'icon' => 'mdi mdi-bank-transfer',
                        'text' => __('glaccounts_trans.posting'),
                        'link' => 'gl_posting',
                    ],
                    [
                        'icon' => 'mdi mdi-archive-check-outline',
                        'text' => __('glaccounts_trans.monthly_closing'),
                        'link' => 'gl_closing',
                    ],
                ],
            ])
        </div>
    </div>
</div>
@endsection
