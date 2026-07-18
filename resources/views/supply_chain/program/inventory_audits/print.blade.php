@extends('layouts.master')

@section('title', $title ?? __('supply_chain_trans.inventory_audit'))

@section('content')
    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-lg btn-primary px-5 shadow print-btn">
            <i class="ri-printer-line me-2"></i> {{ __('supply_chain_trans.print') }}
        </button>
    </div>

    <style>
        @media print {
            @page { size: A4 landscape; margin: 10mm 15mm; }
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .print-btn { display: none !important; }
        }
        body { direction: rtl; }
        .report-container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,.08);
            border-radius: 8px;
        }
        .report-header { text-align:center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 3px double #444; }
        .company-info { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
        .company-logo { height: 70px; object-fit: contain; }
        .company-text { text-align:center; flex-grow:1; }
        .company-name { font-size: 22px; font-weight: bold; color: #003366; }
        .report-title { font-size: 20px; font-weight: bold; color:#003366; margin-top: 10px; }
        .header-table, .items-table { width:100%; border-collapse: collapse; margin: 15px 0; background:#f8f9fa; }
        .header-table td, .items-table th, .items-table td { border:1px solid #dee2e6; padding:10px; text-align:center; font-size: 13px; }
        .items-table th { background:#003366; color:#fff; }
        .items-table tr:nth-child(even) { background:#fdfdfd; }
        .badge { display:inline-block; padding:.35em .6em; border-radius: .35rem; font-size: .85em; }
        .badge-secondary { background:#6c757d; color:#fff; }
        .attachments a { margin-inline-end: 12px; text-decoration:none; }
        .thumb { width: 60px; height: 60px; object-fit: cover; border:1px solid #ddd; border-radius:6px; }
        .signature-table { width:100%; border-collapse:collapse; margin-top:25px; }
        .signature-table td { border:1px solid #dee2e6; padding:40px 10px 15px; text-align:center; background:#f8f9fa; position:relative; }
        .signature-line { position:absolute; bottom:65px; left:10%; width:80%; height:1px; background:#666; }
    </style>

    <div class="report-container">
        <div class="report-header">
            <div class="company-info">
                <img src="{{ asset('assets/images/logo.png') }}" class="company-logo" alt="Logo">
                <div class="company-text">
                    <div class="company-name">مستشفى دنيا العيون</div>
                    <div>إدارة المخازن</div>
                    <div class="report-title">{{ $title ?? __('supply_chain_trans.inventory_audit') }}</div>
                </div>
                <div class="text-start" style="font-size:13px">
                    <div><i class="ri-time-line"></i> {{ __('supply_chain_trans.print_date') ?? 'تاريخ الطباعة' }}: {{ now()->format('Y-m-d H:i') }}</div>
                    <div><i class="ri-user-line"></i> {{ __('supply_chain_trans.created_by') }}: {{ $creator->name ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- رأس المحضر --}}
        <table class="header-table">
            <tr>
                <td><strong>{{ __('supply_chain_trans.code') }}</strong></td>
                <td>{{ $audit->code }}</td>
                <td><strong>{{ __('supply_chain_trans.store') }}</strong></td>
                <td>{{ $store->name_ar ?? $store->name_en ?? $store->name ?? ('#'.$audit->store_id) }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('supply_chain_trans.audit_date') }}</strong></td>
                <td>{{ optional($audit->audit_date)->format('Y-m-d') }}</td>
                <td><strong>{{ __('supply_chain_trans.status') }}</strong></td>
                <td>
                    @php
                        $status = $audit->status ?: 'default';
                        $label  = __('supply_chain_trans.'.$status);
                    @endphp
                    <span class="badge badge-secondary">{{ $label }}</span>
                </td>
            </tr>
            <tr>
                <td><strong>{{ __('supply_chain_trans.start_date') }}</strong></td>
                <td>{{ $audit->start_date }}</td>
                <td><strong>{{ __('supply_chain_trans.end_date') }}</strong></td>
                <td>{{ $audit->end_date }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('supply_chain_trans.committee_members') }}</strong></td>
                <td colspan="3">
                    {{ implode('، ', $committeeNames) ?: '—' }}
                </td>
            </tr>
            <tr>
                <td><strong>{{ __('supply_chain_trans.notes') }}</strong></td>
                <td colspan="3">{{ $audit->notes ?: '—' }}</td>
            </tr>
        </table>

        {{-- تفاصيل البنود --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="25%">{{ __('supply_chain_trans.product') }}</th>
                    <th width="10%">{{ __('supply_chain_trans.unit') }}</th>
                    <th width="12%">{{ __('supply_chain_trans.batch_no') }}</th>
                    <th width="10%">{{ __('supply_chain_trans.expiry_date') }}</th>
                    <th width="10%">{{ __('supply_chain_trans.system_qty') }}</th>
                    <th width="10%">{{ __('supply_chain_trans.actual_qty') }}</th>
                    <th width="10%">{{ __('supply_chain_trans.diff_qty') }}</th>
                    <th width="8%">{{ __('supply_chain_trans.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $i => $d)
                    @php
                        $product = $d->product;
                        $unitName = $d->productUnit?->smallUnit?->name_ar
                                   ?? $d->productUnit?->largeUnit?->name_ar
                                   ?? '-';
                        $status = $d->status ?: 'match';
                        $statusLabel = __('supply_chain_trans.'.$status);
                    @endphp
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>
                            {{ $product->name_ar ?? $product->name_en ?? ('#'.$d->product_id) }}
                            @if(!empty($product->product_code ?? $product->system_code))
                                <div class="text-muted small">{{ $product->product_code ?? $product->system_code }}</div>
                            @endif
                        </td>
                        <td>{{ $unitName }}</td>
                        <td>{{ $d->batch_no ?: '—' }}</td>
                        <td>{{ optional($d->expiry_date)->format('Y-m-d') ?: '—' }}</td>
                        <td>{{ number_format($d->system_quantity, 4) }}</td>
                        <td>{{ number_format($d->actual_quantity, 4) }}</td>
                        <td>{{ number_format($d->difference_quantity, 4) }}</td>
                        <td>{{ $statusLabel }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        {{-- التوقيعات --}}
        <table class="signature-table">
            <tr>
                <td>
                    {{ __('supply_chain_trans.prepared_by') ?? 'أعدّه' }}
                    <div class="signature-line"></div>
                    <div style="margin-top: 20px;">{{ __('supply_chain_trans.name') ?? 'الاسم' }}: .........................</div>
                    <div>{{ __('supply_chain_trans.date') ?? 'التاريخ' }}: .........................</div>
                </td>
                <td>
                    {{ __('supply_chain_trans.store_keeper') ?? 'أمين المخزن' }}
                    <div class="signature-line"></div>
                    <div style="margin-top: 20px;">{{ __('supply_chain_trans.name') ?? 'الاسم' }}: .........................</div>
                    <div>{{ __('supply_chain_trans.date') ?? 'التاريخ' }}: .........................</div>
                </td>
                <td>
                    {{ __('supply_chain_trans.manager') ?? 'المدير' }}
                    <div class="signature-line"></div>
                    <div style="margin-top: 20px;">{{ __('supply_chain_trans.name') ?? 'الاسم' }}: .........................</div>
                    <div>{{ __('supply_chain_trans.date') ?? 'التاريخ' }}: .........................</div>
                </td>
            </tr>
        </table>
    </div>
@endsection
