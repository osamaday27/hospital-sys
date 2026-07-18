@extends('layouts.master')

@section('content')
<div class="no-print mb-4 text-center">
    <button onclick="window.print()" class="btn btn-lg btn-primary px-5 shadow">
        <i class="ri-printer-line me-2"></i> {{ trans('reports_trans.print_report') }}
    </button>
</div>

<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 0mm 0mm;
            counter-increment: page;
        }
        body {
            font-family: 'Cairo', 'Arial', sans-serif;
            direction: rtl;
            color: #000;
            line-height: 1.3;
        }
        .no-print { display: none !important; }
        .page-number::after {
            content: "Page " counter(page);
            position: fixed;
            bottom: 5mm;
            text-align: center;
            width: 100%;
            font-size: 9px;
            color: #666;
        }
    }

    body { background: #f5f5f5; }

    .report-container {
        max-width: 210mm;
        margin: auto;
        background: #fff;
        padding: 12px 18px;
        box-shadow: 0 0 8px rgba(0,0,0,0.08);
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #0d47a1;
        padding-bottom: 5px;
        margin-bottom: 10px;
    }

    .header-logo img { height: 45px; }

    .header-center {
        flex: 1;
        text-align: center;
        padding: 0 10px;
    }

    .header-center h2 {
        color: #0d47a1;
        font-weight: 700;
        margin: 0;
        font-size: 15px;
    }

    .header-center small {
        color: #666;
        font-size: 10px;
        display: block;
        margin-top: 2px;
    }

    .header-meta {
        text-align: right;
        font-size: 10px;
        color: #555;
        line-height: 1.4;
    }

    .report-title {
        background: linear-gradient(90deg, #0d47a1, #1565c0);
        color: white;
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        padding: 5px 8px;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .section-title {
        font-size: 11px;
        font-weight: 700;
        color: #0d47a1;
        border-left: 3px solid #1976d2;
        padding-left: 6px;
        margin-top: 12px;
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
        background: #f8f9fa;
        padding: 3px 6px;
        border-radius: 3px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 6px 10px;
        margin-bottom: 8px;
    }

    .info-item {
        background: #fafbfc;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 10px;
        line-height: 1.4;
    }

    .info-item strong {
        color: #0d47a1;
        display: block;
        font-size: 9px;
        margin-bottom: 2px;
        font-weight: 600;
    }

    .details-box {
        background: #fafbfc;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        padding: 8px 10px;
        line-height: 1.5;
        font-size: 10px;
        color: #333;
        margin-top: 6px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .narrative-text {
        font-size: 10px;
        color: #222;
        line-height: 1.5;
    }

    .narrative-text p {
        margin-bottom: 6px;
        text-align: justify;
        direction: ltr;
    }

    .narrative-text h6 {
        font-weight: 700;
        color: #0d47a1;
        font-size: 10px;
        margin-top: 8px;
        margin-bottom: 4px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 2px;
    }

    .alert-box {
        background: #fff3cd;
        border-left: 3px solid #ff9800;
        border-radius: 3px;
        padding: 6px 8px;
        font-size: 10px;
        color: #856404;
        margin-top: 8px;
    }

    .signatures {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 20px;
        border-top: 1px dashed #bbb;
        padding-top: 10px;
    }

    .signature {
        width: 30%;
        text-align: center;
        font-size: 9px;
    }

    .signature img {
        max-height: 40px;
        margin-bottom: 3px;
        object-fit: contain;
    }

    .signature .line {
        border-top: 1px solid #333;
        margin-top: 3px;
        font-weight: bold;
        padding-top: 2px;
    }

    .report-footer {
        margin-top: 15px;
        text-align: center;
        font-size: 8px;
        color: #666;
        border-top: 1px solid #e0e0e0;
        padding-top: 8px;
    }

    .confidential {
        display: inline-block;
        background: #c62828;
        color: #fff;
        font-size: 8px;
        padding: 2px 8px;
        border-radius: 10px;
        margin-top: 5px;
        font-weight: bold;
    }
</style>

<div class="report-container page-number">
    {{-- Header --}}
    <div class="report-header">
        <div class="header-logo">
            <img src="{{ asset('assets/images/his_logo.png') }}" alt="Logo">
        </div>
        <div class="header-center">
            <h2>{{ $hospital_name ?? 'EYE World Hospital' }}</h2>
            <small>Medical Administration Department | الادارة الطبية</small>
        </div>
        <div class="header-meta">
            <div><strong>MRN:</strong> {{ $report->patient->id ?? 'N/A' }}</div>
            <div><strong>Date:</strong> {{ now()->format('Y-m-d') }}</div>
        </div>
    </div>

    {{-- Title --}}
    <div class="report-title">
        {{ $report->report_type ?? 'Corneal Cross Linking' }} / {{ $report->report_type_ar ?? 'تقرير العملية' }}
    </div>

    {{-- Patient Info --}}
    <div class="section-title">
        <span>Patient Information</span>
        <span>بيانات المريض</span>
    </div>

    <div class="info-grid">
        <div class="info-item"><strong>Patient Name / اسم المريض</strong>{{ $report->patient->full_name ?? 'N/A' }}</div>
        <div class="info-item"><strong>Age / العمر</strong>{{ $report->patient->age ?? 'N/A' }}</div>
        <div class="info-item"><strong>Gender / الجنس</strong>{{ $report->patient->gender ?? 'N/A' }}</div>
        <div class="info-item"><strong>Surgery Date / تاريخ العملية</strong>{{ $report->surgery_date ?? now()->format('Y-m-d') }}</div>
        <div class="info-item"><strong>Surgeon / الجراح</strong>{{ $report->surgeon ?? 'N/A' }}</div>
        <div class="info-item"><strong>Anesthesia / التخدير</strong>{{ $report->anesthesia ?? 'N/A' }}</div>
    </div>

    {{-- Diagnosis --}}
    <div class="section-title">
        <span>Diagnosis</span>
        <span>التشخيص</span>
    </div>

    <div class="info-grid">
        <div class="info-item"><strong>Preoperative / قبل العملية</strong>{{ $report->preoperative_diagnosis ?? 'N/A' }}</div>
        <div class="info-item"><strong>Postoperative / بعد العملية</strong>{{ $report->postoperative_diagnosis ?? 'N/A' }}</div>
    </div>

    {{-- Procedure --}}
    <div class="section-title">
        <span>Procedure</span>
        <span>الإجراء</span>
    </div>
    <div class="details-box">{{ $report->surgical_procedure ?? 'N/A' }}</div>

    {{-- Operative Narrative --}}
    <div class="section-title">
        <span>Operative Narrative</span>
        <span>تفاصيل العملية</span>
    </div>

    @php $details = json_decode($report->operative_details, true); @endphp
    <div class="details-box narrative-text">
        @if(isset($details['intro']))
            <h6>Introductory Statement</h6>
            <p>{{ trim($details['intro']) }}</p>
        @endif
        @if(isset($details['details']))
            <h6>Operative Details</h6>
            <p>{{ trim($details['details']) }}</p>
        @endif
    </div>

    {{-- Complications --}}
    @if(!empty($report->complications) && $report->complications != 'None')
        <div class="alert-box">
            <strong>Complications / المضاعفات:</strong> {{ $report->complications }}
        </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature"><div class="line">Director / المدير</div></div>
        <div class="signature"><div class="line">Dept. Head / رئيس القسم</div></div>
        <div class="signature">
            @if(!empty($report->surgeon_signature))
                <img src="{{ asset('storage/signatures/'.$report->surgeon_signature) }}" alt="Surgeon Signature">
            @else
                <img src="{{ asset('assets/images/signature-placeholder.png') }}" alt="Signature Placeholder">
            @endif
            <div class="line">Surgeon / الجراح</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="report-footer">
        <p>Generated: {{ now()->format('Y-m-d H:i') }} | {{ $hospital_name ?? 'EYE World Hospital' }} | Tel: {{ $hospital_phone ?? 'N/A' }}</p>
    </div>
</div>

{{-- ✅ طباعة تلقائية عند فتح الصفحة --}}
<script>
    window.onload = function() {
        setTimeout(() => {
            window.print();
            setTimeout(() => window.close(), 1500); // تقفل بعد الطباعة بـ 1.5 ثانية
        }, 800);
    };
</script>
@endsection
