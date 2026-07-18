@extends('layouts.master_table')

@section('title')
  {{ trans('assets.asset_conversions.show_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('assets.programs.asset_conversions.index') }}">{{ trans('assets.asset_conversions.index_title') }}</a>
          </li>
          <li class="breadcrumb-item active">
            {{ trans('assets.asset_conversions.show_title') }}
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
  <div class="card-body row g-3">
    <div class="col-md-3">
      <div class="text-muted small">{{ trans('assets.asset_conversions.col_product') }}</div>
      <div class="fw-semibold">
        {{ ($row->product->name_ar ?? $row->product->name_en ?? '') }} ({{ $row->product->product_code }})
      </div>
    </div>
    <div class="col-md-3">
      <div class="text-muted small">{{ trans('assets.asset_conversions.col_store') }}</div>
      <div class="fw-semibold">{{ $row->store->name_ar ?? $row->store->name_en ?? '' }}</div>
    </div>
    <div class="col-md-2">
      <div class="text-muted small">{{ trans('assets.asset_conversions.col_batch') }}</div>
      <div class="fw-semibold">{{ $row->batch_no ?? '-' }}</div>
    </div>
    <div class="col-md-2">
      <div class="text-muted small">{{ trans('assets.asset_conversions.col_expiry') }}</div>
      <div class="fw-semibold">{{ optional($row->expiry_date)->format('Y-m-d') ?? '-' }}</div>
    </div>
    <div class="col-md-2">
      <div class="text-muted small">{{ trans('assets.asset_conversions.col_qty') }}</div>
      <div class="fw-semibold">{{ (float)$row->qty_in_base }}</div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
      @if($row->status === 'active')
        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
          <i class="ri-close-circle-line"></i> {{ trans('assets.asset_conversions.btn_cancel') }}
        </button>
      @else
        <span class="badge bg-secondary align-self-center">{{ trans('assets.asset_conversions.status_cancelled') }}</span>
      @endif

      <a href="{{ route('assets.programs.asset_conversions.index') }}" class="btn btn-light">
        {{ trans('assets.asset_conversions.btn_back') }}
      </a>
    </div>
  </div>
</div>

{{-- Modal إلغاء --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="{{ route('assets.programs.asset_conversions.cancel', $row->id) }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">{{ trans('assets.asset_conversions.modal_cancel_title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">{{ trans('assets.asset_conversions.modal_cancel_reason') }}</label>
        <textarea name="reason" class="form-control" rows="3" placeholder="{{ trans('assets.asset_conversions.modal_cancel_ph') }}"></textarea>
        <div class="alert alert-warning mt-2">
          {{ trans('assets.asset_conversions.modal_cancel_warn') }}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('assets.asset_conversions.btn_close') }}</button>
        <button type="submit" class="btn btn-danger">{{ trans('assets.asset_conversions.btn_confirm_cancel') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
