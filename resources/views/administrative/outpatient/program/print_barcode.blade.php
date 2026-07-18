@extends('layouts.master')

@section('content')
    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-success btn-lg shadow">
            <i class="ri-printer-fill me-2"></i> {{ __('outpatient_trans.print_barcode') }}
        </button>
    </div>

    <div class="barcode-container text-center"
        style="max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h4 style="margin-bottom:20px;">{{ __('outpatient_trans.patient_barcode') }}</h4>

        <div style="font-size: 18px; font-weight:bold; margin-bottom:10px;">
            {{ $patient->full_name }}
        </div>

        <div style="margin-bottom:10px;">
            <strong>{{ __('outpatient_trans.age') }}:</strong>
            {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }}
        </div>
        <div style="margin-bottom:10px;">
            <strong>{{ __('outpatient_trans.gender') }}:</strong>
            @if ($patient->gender == 'male')
                {{ __('outpatient_trans.male') }}
            @elseif($patient->gender == 'female')
                {{ __('outpatient_trans.female') }}
            @else
                {{ __('outpatient_trans.unknown') }}
            @endif
        </div>
        <div style="margin-bottom:10px;">

            @if ($patient->nationality == '3')
                <strong>
                    {{ __('outpatient_trans.national_id') }}:
                </strong>
                @if (!empty($patient->national_id))
                    {{ $patient->national_id }}
                @else
                    {{ __('outpatient_trans.unknown') }}
                @endif
            @elseif($patient->nationality != '3')
            <strong>
                {{ __('outpatient_trans.passport') }}:
            </strong>
                @if (!empty($patient->passport_number))
                    {{ $patient->passport_number }}
                @else
                    {{ __('outpatient_trans.unknown') }}
                @endif
            @else
                {{ __('outpatient_trans.unknown') }}
            @endif
        </div>


        <div style="margin-bottom:20px;">
            <strong>{{ __('outpatient_trans.medical_number') }}:</strong>
            {{ $patient->medical_number }}
        </div>

        {{-- هنا الباركود --}}
        <div style="margin-top: 20px;margin-right: 28%;">

            {!! DNS1D::getBarcodeHTML($patient->medical_number, 'C128', 2, 60) !!}
        </div>
    </div>
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            .no-print {
                display: none !important;
            }

            .barcode-container {
                width: 9cm;
                /* عرض أصغر */
                height: auto;
                padding: 10px !important;
                margin: 0 auto !important;
                border: none !important;
                box-shadow: none !important;
            }

            @page {
                size: A6 portrait;
                /* حجم ورقة صغير */
                margin: 10mm;
            }
        }

        .barcode-container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
    </style>

@endsection
