@extends('layouts.master_table')
@section('title') {{ __('assets.disposal.create_title') }} @stop

@section('content')
@include('assets.programs.disposal.flash')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('assets.programs.disposal.index') }}">{{ __('assets.disposal.index_title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('إنشاء') }}</li>
  </ol>
</nav>

<form method="post" action="{{ route('assets.programs.disposal.store') }}" enctype="multipart/form-data" class="card">
  @csrf
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">{{ __('assets.disposal.method') }}</label>
        <select name="method" class="form-select" required>
          <option value="sale"     {{ old('method')==='sale'     ? 'selected' : '' }}>{{ __('assets.disposal.m_sale') }}</option>
          <option value="donation" {{ old('method')==='donation' ? 'selected' : '' }}>{{ __('assets.disposal.m_donation') }}</option>
          <option value="scrap"    {{ old('method')==='scrap'    ? 'selected' : '' }}>{{ __('assets.disposal.m_scrap') }}</option>
        </select>
        @error('method') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-8">
        <label class="form-label">{{ __('assets.disposal.reason') }}</label>
        <input type="text" name="reason" class="form-control" maxlength="500" value="{{ old('reason') }}">
        @error('reason') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-12">
        <label class="form-label">{{ __('assets.disposal.assets_select') }}</label>
        <div id="items-wrap"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-item">
          <i class="ri-add-line"></i> {{ __('assets.add') }}
        </button>
        @error('items') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-12">
        <label class="form-label">{{ __('assets.attachments') }}</label>
        <input type="file" name="attachments[]" class="form-control" multiple>
        @error('attachments.*') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-12">
        <label class="form-label">{{ __('assets.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
        @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
    </div>
  </div>

  <div class="card-footer d-flex justify-content-end gap-2">
    <a href="{{ route('assets.programs.disposal.index') }}" class="btn btn-light">{{ __('assets.back') }}</a>
    <button class="btn btn-success">{{ __('assets.save') }}</button>
  </div>
</form>

<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function assetSelect($sel){
  $sel.select2({
    theme:'bootstrap-5',
    ajax:{
      url:'{{ route("assets.programs.disposal.ajax.assets") }}',
      dataType:'json',
      delay:250,
      data: params => ({ q: params.term }),
      processResults: data => ({ results: data })
    },
    placeholder: '{{ __("assets.disposal.assets_select") }}'
  });
}

function addRow(prefill){
  const id  = Date.now() + Math.floor(Math.random()*999);
  const qty = (prefill && prefill.qty) ? prefill.qty : 1;
  const row = $(`
    <div class="row g-2 align-items-end mb-2" data-row="${id}">
      <div class="col-md-8">
        <select name="items[${id}][asset_id]" class="form-select js-asset" required></select>
      </div>
      <div class="col-md-3">
        <input type="number" name="items[${id}][qty]" class="form-control" min="1" value="${qty}" required>
      </div>
      <div class="col-md-1 text-end">
        <button type="button" class="btn btn-sm btn-outline-danger js-del"><i class="ri-delete-bin-6-line"></i></button>
      </div>
    </div>
  `);
  $('#items-wrap').append(row);
  const $asset = row.find('.js-asset');
  assetSelect($asset);

  if (prefill && prefill.asset_id) {
    const opt = new Option(prefill.asset_id, prefill.asset_id, true, true);
    $asset.append(opt).trigger('change');
  }

  row.on('click','.js-del', function(){ row.remove(); });
}

document.addEventListener('DOMContentLoaded', function(){
  @if (old('items'))
    $('#items-wrap').empty();
    const oldItems = @json(old('items'));
    Object.keys(oldItems).forEach(function(key){
      const it = oldItems[key] || {};
      addRow({ asset_id: it.asset_id, qty: it.qty });
    });
  @else
    addRow();
  @endif

  document.getElementById('add-item').addEventListener('click', function(){ addRow(); });
});
</script>
@endsection
