@extends('layouts.master_table')
@section('title', __('assets.general_settings'))
@section('content')

{{-- رسائل النجاح --}}
@if(session('success'))
  <div class="alert alert-success text-center"
       style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;">
    {{ session('success') }}
  </div>
  <script>
    $(function(){ setTimeout(()=>$('.alert-success').fadeOut(600,function(){$(this).remove();}), 3000); });
  </script>
@endif

{{-- رسائل الأخطاء --}}
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">@lang('assets.general_settings')</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="post" action="{{ route('assets.settings.general.update') }}">
      @csrf
      @method('PUT')

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">@lang('assets.code_prefix')</label>
          <input class="form-control @error('code_prefix') is-invalid @enderror"
                 name="code_prefix"
                 value="{{ old('code_prefix',$row->code_prefix) }}" required>
          @error('code_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label">@lang('assets.qr_size')</label>
          <input type="number"
                 class="form-control @error('qr_size_px') is-invalid @enderror"
                 name="qr_size_px"
                 value="{{ old('qr_size_px',$row->qr_size_px) }}"
                 min="64" max="1024" required>
          @error('qr_size_px') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label">@lang('assets.qr_margin')</label>
          <input type="number"
                 class="form-control @error('qr_margin') is-invalid @enderror"
                 name="qr_margin"
                 value="{{ old('qr_margin',$row->qr_margin) }}"
                 min="0" max="20" required>
          @error('qr_margin') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3 d-flex align-items-end">
          <div class="form-check">
            <input type="checkbox"
                   class="form-check-input @error('enable_rfid') is-invalid @enderror"
                   id="enable_rfid"
                   name="enable_rfid"
                   value="1" @checked($row->enable_rfid)>
            <label for="enable_rfid" class="form-check-label">@lang('assets.enable_rfid')</label>
            @error('enable_rfid') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <label class="form-label">@lang('assets.qr_disk')</label>
          <input class="form-control @error('qr_disk') is-invalid @enderror"
                 name="qr_disk"
                 value="{{ old('qr_disk',$row->qr_disk) }}" required>
          @error('qr_disk') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label class="form-label">@lang('assets.qr_path')</label>
          <input class="form-control @error('qr_path') is-invalid @enderror"
                 name="qr_path"
                 value="{{ old('qr_path',$row->qr_path) }}" required>
          @error('qr_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mt-3 text-end">
        <button class="btn btn-primary">@lang('assets.save')</button>
      </div>
    </form>
  </div>
</div>
@endsection
