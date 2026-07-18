@extends('layouts.master')

@section('content')

<div class="no-print mb-4 text-center">
    <button onclick="window.print()" class="btn btn-success btn-lg shadow">
        <i class="ri-printer-fill me-2"></i> {{ __('lockers_trans.print_normal_receipt') }}
    </button>
</div>

<div class="report-container" style="max-width: 800px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1);">

    <div class="report-header d-flex justify-content-between align-items-center mb-4" style="border-bottom: 2px solid #0078d7; padding-bottom: 15px;">
        <img src="{{ asset('assets/images/his_logo.png') }}" alt="Logo" style="height: 80px;">
        <div style="text-align: center; flex-grow: 1;">
            <div style="font-size: 24px; font-weight: bold; color: #0078d7;">{{ __('lockers_trans.hospital_name') }}</div>
            <div style="font-size: 16px; color: #555;">{{ __('lockers_trans.financial_department') }}</div>
            <div style="margin-top: 5px; font-size: 18px; font-weight: bold; color: #333;">{{ __('finance_trans.advance_payment_receipt') }}</div>
        </div>
        <div class="text-end" style="font-size: 14px;">
            <div><i class="ri-time-line me-1"></i> {{ __('lockers_trans.print_date') }}: {{ now()->format('Y-m-d H:i') }}</div>
            <div style="font-weight: bold; color: #0078d7;">#{{ $payment->id }}</div>
        </div>
    </div>

    <table class="table table-bordered text-sm mb-4">
        <tr>
            <td><strong>{{ __('lockers_trans.patient_name') }}:</strong> {{ $payment->patient?->full_name }}</td>
            <td><strong>{{ __('lockers_trans.file_number') }}:</strong> {{ $payment->patient?->medical_number }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('finance_trans.payment_date') }}:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
            <td><strong>{{ __('finance_trans.payment_method') }}:</strong> {{ __('finance_trans.' . $payment->payment_method) }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('lockers_trans.locker') }}:</strong> {{ $payment->locker?->name_ar }}</td>
            <td><strong>{{ __('lockers_trans.createby') }}:</strong> {{ $payment->user?->name ?? '-' }}</td>
        </tr>
    </table>

    <table class="table table-bordered text-center mb-4">
        <thead class="table-light">
            <tr>
                <th>{{ __('finance_trans.amount') }}</th>
                <th>{{ __('finance_trans.currency') }}</th>
                <th>{{ __('finance_trans.notes') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-success fw-bold">{{ number_format($payment->amount,2) }}</td>
                <td>{{ $payment->currency }}</td>
                <td>{{ $payment->notes ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer mt-4 pt-3 border-top text-center text-muted">
        <div>{{ __('lockers_trans.thanks_message') }}</div>
        <div style="font-size: 12px;">{{ __('lockers_trans.legal_document_note') }}</div>
    </div>

</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .report-container {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
        }
    }
</style>
@endsection
