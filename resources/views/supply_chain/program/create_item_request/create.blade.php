@extends('layouts.master_doctor')

@section('title')
  {{ __('supply_chain_trans.create_item_request') }}
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
            {{ __('supply_chain_trans.create_item_request') }}
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

<form action="{{ route('create_item_request.store') }}" method="POST">
  @csrf

  {{-- بيانات الطلب --}}
  <div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0">{{ __('supply_chain_trans.request_data') }}</h5></div>
    <div class="card-body">
      <div class="row gy-3">
        <div class="col-md-4">
          <label class="form-label">{{ __('supply_chain_trans.request_number') }}</label>
          <input type="text" name="request_number" class="form-control" readonly
                 value="{{ old('request_number', $requestNumber) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('supply_chain_trans.request_date') }}</label>
          <input type="date" name="request_date" class="form-control" readonly
                 value="{{ now()->format('Y-m-d') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
          <select id="store-select" name="store_id" class="form-select select2" required>
            <option value="">{{ __('general.select') }}</option>
            @foreach($stores as $s)
              <option value="{{ $s->id }}"
                {{ old('store_id') == $s->id ? 'selected' : '' }}>
                {{ $s->name_ar }} – {{ $s->name_en }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- اختيار المجموعة والصنف --}}
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

  <div class="mb-3 text-end">
    <button type="button" id="add-item" class="btn btn-outline-primary" disabled>
      <i class="ri-add-line"></i> {{ __('general.add') }}
    </button>
  </div>

  {{-- جدول البنود --}}
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
              <th>{{ __('general.actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- أزرار الحفظ والرجوع --}}
  <div class="text-end">
    <a href="{{ route('create_item_request.index') }}" class="btn btn-secondary">{{ __('general.back') }}</a>
    <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
  </div>
</form>

<script>
$(function() {
  $('.select2').select2({ width: '100%' });

  // 1) تعطيل حتى اختيار مخزن
  $('#group-select, #item-select, #add-item').prop('disabled', true);

  // 2) بعد اختيار المخزن فعّل الفئة
  $('#store-select').on('change', function() {
    $(this).val()
      ? $('#group-select').prop('disabled', false)
      : ( $('#group-select').val('').trigger('change').prop('disabled', true),
          $('#item-select').empty().append('<option>{{ __("general.select") }}</option>').prop('disabled', true),
          $('#add-item').prop('disabled', true) );
  });

  // 3) عند اختيار الفئة: جلب كل الأصناف مع العلامة، ثم فلترتها في الجافاسكربت
  $('#group-select').on('change', function() {
    let cat    = $(this).val(),
        store  = $('#store-select').val();
    if (!cat || !store) {
      return $('#item-select').prop('disabled', true)
        .empty().append('<option>{{ __("general.select") }}</option>');
    }
    $.getJSON('{{ route("create_item_request.items") }}', {
      category_id: cat,
      store_id:    store
    }, function(data) {
      let $sel = $('#item-select').prop('disabled', false).empty()
                   .append('<option>{{ __("general.select") }}</option>');
      data.forEach(function(it) {
        // **هنا الفلترة**: تجاهل الأصناف التي exist_in_store == 1
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

  // 4) تفعيل زر الإضافة عند اختيار الصنف
  $('#item-select').on('change', function() {
    $('#add-item').prop('disabled', !$(this).val());
  });

  // 5) إضافة الصنف وإخفاؤه من القائمة
  $('#add-item').on('click', function() {
    let $opt = $('#item-select option:selected'),
        id    = $opt.val();
    if (!id) return;
    let ar = $opt.data('ar'), en = $opt.data('en');
    $('#items-table tbody').append(`
      <tr data-id="${id}">
        <td></td>
        <td>${ar}</td>
        <td>${en}</td>
        <td>
          <button type="button" class="btn btn-sm btn-danger remove-item">×</button>
          <input type="hidden" name="items[]" value="${id}">
        </td>
      </tr>`);
    updateSerial();
    $opt.remove();
    $('#add-item').prop('disabled', true);
  });

  // 6) حذف صف وإعادة الخيار إلى القائمة
  $(document).on('click', '.remove-item', function() {
    let $tr  = $(this).closest('tr'),
        id   = $tr.data('id'),
        ar   = $tr.find('td:nth-child(2)').text(),
        en   = $tr.find('td:nth-child(3)').text();
    $tr.remove(); updateSerial();
    $('#item-select').append(`
      <option value="${id}" data-ar="${ar}" data-en="${en}">
        ${ar} – ${en}
      </option>`);
  });

  function updateSerial() {
    $('#items-table tbody tr').each(function(i) {
      $(this).find('td:first').text(i+1);
    });
  }
});
</script>
@endsection
