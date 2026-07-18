@extends('layouts.master_doctor')

@section('title')
  {{ __('supply_chain_trans.edit_item_request') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Breadcrumb --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('create_item_request.index') }}">
              {{ __('supply_chain_trans.item_requests') }}
            </a>
          </li>
          <li class="breadcrumb-item active">
            {{ __('supply_chain_trans.edit_item_request') }}
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

{{-- Validation Errors --}}
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('create_item_request.update', $item_request->id) }}" method="POST">
  @csrf
  @method('PUT')

  {{-- بيانات الطلب --}}
  <div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0">{{ __('supply_chain_trans.request_data') }}</h5></div>
    <div class="card-body">
      <div class="row gy-3">
        <div class="col-md-4">
          <label class="form-label">{{ __('supply_chain_trans.request_number') }}</label>
          <input type="text" class="form-control" readonly value="{{ $item_request->request_number }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('supply_chain_trans.request_date') }}</label>
          <input type="date" class="form-control" readonly value="{{ $item_request->request_date->format('Y-m-d') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
          <select id="store-select" name="store_id" class="form-select select2" required>
            <option value="">{{ __('general.select') }}</option>
            @foreach($stores as $s)
              <option value="{{ $s->id }}"
                {{ $item_request->store_id == $s->id ? 'selected' : '' }}>
                {{ $s->name_ar }} – {{ $s->name_en }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- إضافة الأصناف --}}
  <div class="row gy-3 mb-3">
    <div class="col-md-6">
      <label class="form-label">{{ __('supply_chain_trans.category') }}</label>
      <select id="group-select" class="form-select select2" disabled>
        <option value="">{{ __('general.select') }}</option>
        @foreach($groups as $g)
          <option value="{{ $g->id }}">{{ $g->name_ar }} – {{ $g->name_en }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('supply_chain_trans.item') }}</label>
      <select id="item-select" class="form-select select2" disabled>
        <option value="">{{ __('general.select') }}</option>
      </select>
    </div>
  </div>

  <div class="text-end mb-3">
    <button type="button" id="add-item" class="btn btn-outline-primary" disabled>
      <i class="ri-add-line"></i> {{ __('general.add') }}
    </button>
  </div>

  {{-- جدول البنود مع عمود الحالة --}}
  <div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0">{{ __('supply_chain_trans.request_items') }}</h5></div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered text-center mb-0" id="items-table">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>{{ __('supply_chain_trans.item_name_ar') }}</th>
              <th>{{ __('supply_chain_trans.item_name_en') }}</th>
              <th>{{ __('supply_chain_trans.status') }}</th>
              <th>{{ __('general.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($details as $i => $d)
              @php
                switch($d->status) {
                  case 'approved':  $badge = 'bg-success'; break;
                  case 'rejected':  $badge = 'bg-danger';  break;
                  default:          $badge = 'bg-warning text-dark';
                }
              @endphp
              <tr data-id="{{ $d->item_id }}">
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->item->name_ar }}</td>
                <td>{{ $d->item->name_en }}</td>
                <td>
                  <span class="badge {{ $badge }}">
                    {{ __('supply_chain_trans.' . $d->status) }}
                  </span>
                </td>
                <td>
                  @if($d->status === 'pending')
                    <button type="button" class="btn btn-sm btn-danger remove-item">×</button>
                    <input type="hidden" name="items[]" value="{{ $d->item_id }}">
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- أزرار الحفظ والرجوع --}}
  <div class="text-end">
    <a href="{{ route('create_item_request.index') }}" class="btn btn-secondary">
      {{ __('general.back') }}
    </a>
    <button type="submit" class="btn btn-primary">
      {{ __('general.save') }}
    </button>
  </div>
</form>

<script>
$(function(){
  $('.select2').select2({ width:'100%' });

  // ** تفعيل المجموعات إذا كان المخزن محددًا مسبقًا عند الفتح **
  if ($('#store-select').val()) {
    $('#group-select').prop('disabled', false);
  }

  // 1) تعطيل باقي الحقول حتى يتغير المخزن
  $('#item-select, #add-item').prop('disabled', true);

  // 2) عند تغيير المخزن
  $('#store-select').on('change', function(){
    if ($(this).val()) {
      $('#group-select').prop('disabled', false);
    } else {
      $('#group-select').val('').trigger('change').prop('disabled', true);
      $('#item-select').empty()
        .append('<option>{{ __("general.select") }}</option>')
        .prop('disabled', true);
      $('#add-item').prop('disabled', true);
    }
  });

  // 3) باقي الكود كما هو...
  $('#group-select').on('change', function(){
    let cat   = $(this).val(),
        store = $('#store-select').val();
    if (!cat || !store) {
      return $('#item-select').prop('disabled', true)
        .empty().append('<option>{{ __("general.select") }}</option>');
    }
    $.getJSON('{{ route("create_item_request.items") }}', {
      category_id: cat,
      store_id:    store
    }, function(data){
      let $sel = $('#item-select').prop('disabled', false).empty()
                   .append('<option>{{ __("general.select") }}</option>');
      data.forEach(function(it){
        if (it.exists_in_store === 1) return;
        $sel.append(`
          <option value="${it.id}"
                  data-ar="${it.name_ar}"
                  data-en="${it.name_en}">
            ${it.name_ar} – ${it.name_en}
          </option>`);
      });
      $('#add-item').prop('disabled', true);
    });
  });

  $('#item-select').on('change', function(){
    $('#add-item').prop('disabled', !$(this).val());
  });

  $('#add-item').on('click', function(){
    let $opt = $('#item-select option:selected'), id = $opt.val();
    if (!id) return;
    let ar = $opt.data('ar'), en = $opt.data('en');
    $('#items-table tbody').append(`
      <tr data-id="${id}">
        <td></td>
        <td>${ar}</td>
        <td>${en}</td>
        <td><span class="badge bg-warning text-dark">{{ __('supply_chain_trans.pending') }}</span></td>
        <td>
          <button type="button" class="btn btn-sm btn-danger remove-item">×</button>
          <input type="hidden" name="items[]" value="${id}">
        </td>
      </tr>`);
    updateSerial();
    $opt.remove();
    $('#add-item').prop('disabled', true);
  });

  $(document).on('click', '.remove-item', function(){
    let $tr  = $(this).closest('tr'),
        id   = $tr.data('id'),
        ar   = $tr.find('td:nth-child(2)').text(),
        en   = $tr.find('td:nth-child(3)').text();
    $tr.remove(); updateSerial();
    $('#item-select').append(`
      <option value="${id}"
              data-ar="${ar}"
              data-en="${en}">
        ${ar} – ${en}
      </option>`);
  });

  function updateSerial(){
    $('#items-table tbody tr').each(function(i){
      $(this).find('td:first').text(i+1);
    });
  }
});
</script>

@endsection
