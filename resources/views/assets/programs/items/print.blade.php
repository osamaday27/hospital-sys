{{-- resources/views/assets/programs/items/print.blade.php --}}
@extends('layouts.master_table')
@section('title', __('assets.print_card'))

@php
  // نفس الدالة المستخدمة في _show
  $toPublicUrl = function (?string $path): ?string {
      if (!$path) return null;
      $p = str_replace('\\','/',$path);
      $p = ltrim($p, '/');
      $p = preg_replace('/^public\//i', '', $p);
      return asset($p);
  };
  $qrUrl = $toPublicUrl($row->qr_code_path ?? null);
@endphp

@section('content')
<div class="container my-4">
  <div class="card" style="width: 360px; margin: 0 auto; border:1px solid #ddd;">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="fw-bold mb-1">{{ $row->name_ar ?? $row->name_en ?? $row->code }}</div>
          <div class="text-muted small mb-2">{{ $row->code }}</div>
          <div class="small">
            <div>@lang('assets.category'): {{ $row->category? ($row->category->name_ar ?? $row->category->name_en) : '-' }}</div>
            <div>@lang('assets.location'): {{ $row->location? ($row->location->name_ar ?? $row->location->name_en) : '-' }}</div>
            <div>@lang('assets.serial_no'): {{ $row->serial_no ?? '-' }}</div>
          </div>
        </div>

        <div>
          @if($qrUrl)
            <img src="{{ $qrUrl }}" alt="QR" style="width:110px; height:110px;">
          @else
            <div class="text-muted" style="width:110px;height:110px;border:1px dashed #999;display:flex;align-items:center;justify-content:center;">
              @lang('assets.no_qr')
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="text-center mt-3 d-print-none">
    <button onclick="window.print()" class="btn btn-primary"><i class="ri-printer-line"></i> @lang('assets.print')</button>
  </div>
</div>
@endsection
