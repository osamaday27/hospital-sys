@extends('layouts.master_table')
@section('title', __('assets.brands'))
@section('content')

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('assets.settings.brands.index') }}">@lang('assets.brands')</a>
          </li>
          <li class="breadcrumb-item active">{{ $x->name }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="row g-3">
      <div class="col-md-4">
        <div class="border p-2 rounded mb-3">
          <div class="small text-muted">@lang('assets.code')</div>
          <div class="fw-semibold">{{ $x->code }}</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="border p-2 rounded mb-3">
          <div class="small text-muted">@lang('assets.name')</div>
          <div class="fw-semibold">{{ $x->name }}</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="border p-2 rounded mb-3">
          <div class="small text-muted">@lang('assets.status')</div>
          <div class="fw-semibold">
            {!! $x->is_active
                ? '<span class="badge bg-success">'.__('assets.active').'</span>'
                : '<span class="badge bg-secondary">'.__('assets.inactive').'</span>' !!}
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
