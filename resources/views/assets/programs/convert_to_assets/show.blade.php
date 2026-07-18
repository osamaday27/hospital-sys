@extends('layouts.master_table')

@section('title')
  {{ trans('assets.convert_to_assets.show_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- البانر: الخبز --}}
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('assets.programs.convert_to_assets.index') }}">{{ trans('assets.convert_to_assets.index_title') }}</a>
          </li>
          <li class="breadcrumb-item active">
            {{ trans('assets.convert_to_assets.show_title') }}
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

{{-- رسائل --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: auto; min-width: 250px; max-width: 400px; z-index: 9999; font-size: 14px; padding: 10px;">
    {{ session('success') }}
  </div>
@endif

@if($sk = session('skipped'))
  <div class="alert alert-warning">
    <div class="fw-semibold mb-1">{{ trans('assets.convert_to_assets.skipped_rows_title') }}</div>
    <ul class="mb-0">
      @foreach($sk as $row)
        <li>{{ trans('assets.convert_to_assets.skipped_reason', ['i'=>$row['idx']+1, 'r'=>$row['reason']]) }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
  </div>
@endif

<div id="alert-container"></div>

{{-- معلومات المحضر --}}
<div class="card mb-3">
  <div class="card-body row g-3">
    <div class="col-md-3">
      <div class="text-muted small">{{ trans('assets.convert_to_assets.col_code') }}</div>
      <div class="fw-semibold">{{ $doc->code }}</div>
    </div>
    <div class="col-md-3">
      <div class="text-muted small">{{ trans('assets.convert_to_assets.col_date') }}</div>
      <div class="fw-semibold">{{ optional($doc->document_date)->format('Y-m-d') }}</div>
    </div>
    <div class="col-md-3">
      <div class="text-muted small">{{ trans('assets.convert_to_assets.col_supplier') }}</div>
      <div class="fw-semibold">{{ $doc->supplier->name_ar ?? $doc->supplier->name_en ?? '' }}</div>
    </div>
    <div class="col-md-3">
      <div class="text-muted small">{{ trans('assets.convert_to_assets.col_store') }}</div>
      <div class="fw-semibold">{{ $doc->store->name_ar ?? $doc->store->name_en ?? '' }}</div>
    </div>
  </div>
</div>

<form method="post" action="{{ route('assets.programs.convert_to_assets.convert', $doc->id) }}" id="convert-form">
  @csrf

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ trans('assets.convert_to_assets.items_table_title') }}</h5>
      <div class="text-muted small">{{ trans('assets.convert_to_assets.qty_base_hint') }}</div>
    </div>

    <div class="card-body table-responsive">
      <table id="ri-items-table" class="table table-bordered align-middle" style="width:100%">
        <thead>
          <tr class="text-center">
            <th style="width: 32px">
              <input type="checkbox" id="chk-all" title="{{ trans('assets.convert_to_assets.col_select') }}">
            </th>
            <th style="width: 40px">{{ trans('assets.convert_to_assets.col_sr') }}</th>
            <th>{{ trans('assets.convert_to_assets.col_product') }}</th>
            <th>{{ trans('assets.convert_to_assets.col_accepted_unit') }}</th>
            <th>{{ trans('assets.convert_to_assets.col_batch') }}</th>
            <th>{{ trans('assets.convert_to_assets.col_expiry') }}</th>
            <th>{{ trans('assets.convert_to_assets.col_balance_base') }}</th>
            <th style="width:180px">{{ trans('assets.convert_to_assets.col_qty_to_convert_base') }}</th>
          </tr>
        </thead>
        <tbody>
          @php $rowIdx = 0; @endphp
          @forelse($doc->items as $it)
            @php
              $balances = $balancesByProduct[$it->product_id] ?? [];
            @endphp

            @if(empty($balances))
              <tr class="table-light">
                <td class="text-center"><input type="checkbox" disabled></td>
                <td class="text-center">{{ ++$rowIdx }}</td>
                <td>
                  <div class="fw-semibold">{{ $it->product->name_ar ?? $it->product->name_en ?? ('#'.$it->product_id) }}</div>
                  <div class="text-muted small">{{ $it->product->product_code }}</div>
                </td>
                <td class="text-center">{{ $it->acceptedUnit->name_ar ?? $it->acceptedUnit->name_en ?? '-' }}</td>
                <td colspan="4" class="text-center text-muted">{{ trans('assets.convert_to_assets.no_balances') }}</td>
              </tr>
            @else
              @foreach($balances as $b)
                @php $rowIdx++; @endphp
                <tr>
                  <td class="text-center">
                    <input type="checkbox" class="chk-row" name="lines[{{ $rowIdx }}][selected]" value="1">
                  </td>
                  <td class="text-center">{{ $rowIdx }}</td>
                  <td>
                    <div class="fw-semibold">{{ $it->product->name_ar ?? $it->product->name_en ?? ('#'.$it->product_id) }}</div>
                    <div class="text-muted small">{{ $it->product->product_code }}</div>
                  </td>
                  <td class="text-center">{{ $it->acceptedUnit->name_ar ?? $it->acceptedUnit->name_en ?? '-' }}</td>
                  <td class="text-center">{{ $b->batch_no ?? '-' }}</td>
                  <td class="text-center">{{ optional($b->expiry_date)->format('Y-m-d') ?? '-' }}</td>
                  <td class="text-center"><span class="badge bg-secondary">{{ (float)$b->quantity_in_base_unit }}</span></td>
                  <td>
                    <input type="number"
                           class="form-control qty-input"
                           step="0.001"
                           min="0"
                           max="{{ (float)$b->quantity_in_base_unit }}"
                           name="lines[{{ $rowIdx }}][qty_in_base]"
                           placeholder="{{ trans('assets.convert_to_assets.ph_enter_qty_le_balance') }}">
                    <input type="hidden" name="lines[{{ $rowIdx }}][product_id]" value="{{ $it->product_id }}">
                    <input type="hidden" name="lines[{{ $rowIdx }}][balance_id]" value="{{ $b->id }}">
                    <input type="hidden" name="lines[{{ $rowIdx }}][receiving_item_id]" value="{{ $it->id }}">
                  </td>
                </tr>
              @endforeach
            @endif
          @empty
            <tr><td colspan="8" class="text-center text-muted">{{ trans('assets.convert_to_assets.no_items') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex justify-content-end">
      <button type="submit" class="btn btn-success">
        <i class="ri-check-double-line"></i> {{ trans('assets.convert_to_assets.btn_convert') }}
      </button>
      <a href="{{ route('assets.programs.convert_to_assets.index') }}" class="btn btn-light ms-2">{{ trans('assets.convert_to_assets.btn_back') }}</a>
    </div>
  </div>
</form>

{{-- DataTables (ClientSide) + تحكم الاختيار --}}
<script>
  function showAlert(type, message) {
      var alertHtml = `<div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show shadow" role="alert">
          <div class="d-flex align-items-center">
              <div class="flex-grow-1">${message}</div>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
      </div>`;
      const cont = document.getElementById('alert-container');
      cont.insertAdjacentHTML('beforeend', alertHtml);
      setTimeout(() => {
          const el = cont.querySelector('.alert');
          if (el) new bootstrap.Alert(el).close();
      }, 5000);
  }

  $(function(){
    // DataTable
    var dt = $('#ri-items-table').DataTable({
      dom: 'Bfrtip',
      buttons: [
        { extend: 'csv',   title: 'RI Items', text: '<i class="fas fa-file-csv"></i> CSV',   className: 'btn btn-outline-primary' },
        { extend: 'excel', title: 'RI Items', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
      ],
      columnDefs: [
        { targets: [0,1,6,7], orderable: false }
      ]
    });

    // Select/Deselect all
    $('#chk-all').on('change', function(){
      $('.chk-row').prop('checked', this.checked);
    });

    // إذا كتب كمية، فعل تلقائيًا اختيار الصف
    $(document).on('input', '.qty-input', function(){
      const $tr = $(this).closest('tr');
      const $chk = $tr.find('.chk-row');
      const val = parseFloat(this.value || '0');
      $chk.prop('checked', val > 0);
    });

    // تحقق قبل الإرسال
    $('#convert-form').on('submit', function(e){
      let anySelected = false;
      let valid = true;

      $('.chk-row').each(function(){
        if (this.checked) {
          anySelected = true;
          const $tr = $(this).closest('tr');
          const $qty = $tr.find('.qty-input');
          const max = parseFloat($qty.attr('max') || '0');
          const val = parseFloat($qty.val() || '0');
          if (!(val > 0) || val > max) {
            valid = false;
            $qty.addClass('is-invalid');
          } else {
            $qty.removeClass('is-invalid');
          }
        }
      });

      if (!anySelected) {
        e.preventDefault();
        showAlert('error', "{{ trans('assets.convert_to_assets.err_no_lines') }}");
        return false;
      }

      if (!valid) {
        e.preventDefault();
        showAlert('error', "{{ trans('assets.convert_to_assets.ph_enter_qty_le_balance') }}");
        return false;
      }
    });
  });
</script>
@endsection
