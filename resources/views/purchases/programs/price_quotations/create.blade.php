@extends('layouts.master_table')

@section('title')
  {{ __('purchases.create_price_quotation') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>

<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('purchase_requisitions.index') }}">{{ __('purchases.requisitions') }}</a></li>
          <li class="breadcrumb-item active">{{ __('purchases.create_price_quotation') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

{{-- Toast container (للتنبيهات السريعة) --}}
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>

<form action="{{ route('price_quotations.store') }}" method="POST">
  @csrf
  <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">
  <input type="hidden" name="quotation_number" value="{{ $quotationNumber }}">

  <div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0">{{ __('purchases.quotation_data') }}</h5></div>
    <div class="card-body">
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.price_quotation_number') }}</label>
        <div class="col-md-4"><input type="text" class="form-control" value="{{ $quotationNumber }}" readonly></div>
      </div>

      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.start_date') }}</label>
        <div class="col-md-4"><input type="date" name="start_date" class="form-control" required></div>
        <label class="col-md-2 col-form-label">{{ __('purchases.end_date') }}</label>
        <div class="col-md-4"><input type="date" name="end_date" class="form-control" required></div>
      </div>

      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.thesupplier') }}</label>
        <div class="col-md-4">
          <select name="supplier_id" class="form-select select2" data-placeholder="{{ __('purchases.select_supplier') }}" required>
            <option value="">{{ __('purchases.select_supplier') }}</option>
            @foreach($suppliers as $sup)
              <option value="{{ $sup->id }}" {{ $sup->id === $defaultSupplierId ? 'selected' : '' }}>
                {{ $sup->name_ar }} — {{ $sup->name_en }}
              </option>
            @endforeach
          </select>
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.delivery_period') }}</label>
        <div class="col-md-4">
          <input type="number" min="0" name="delivery_period" class="form-control" placeholder="{{ __('purchases.delivery_period_placeholder') }}">
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.delivery_location') }}</label>
        <div class="col-md-4"><input type="text" name="delivery_location" class="form-control" placeholder="{{ __('purchases.delivery_location_placeholder') }}"></div>
        <label class="col-md-2 col-form-label">{{ __('purchases.payment_method') }}</label>
        <div class="col-md-4">
          <select name="payment_method" class="form-select select2" data-placeholder="{{ __('purchases.select_payment_method') }}">
            <option value="">{{ __('purchases.select_payment_method') }}</option>
            <option value="cash">{{ __('purchases.payment_cash') }}</option>
            <option value="bank_transfer">{{ __('purchases.payment_bank_transfer') }}</option>
            <option value="credit">{{ __('purchases.payment_credit') }}</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.payment_terms') }}</label>
        <div class="col-md-4"><textarea name="payment_terms" class="form-control" rows="2" placeholder="{{ __('purchases.payment_terms_placeholder') }}"></textarea></div>
        <label class="col-md-2 col-form-label">{{ __('purchases.notes') }}</label>
        <div class="col-md-4"><textarea name="notes" class="form-control" rows="2" placeholder="{{ __('purchases.notes_placeholder') }}"></textarea></div>
      </div>
    </div>
  </div>

  {{-- تنبيه حدود المخزن (قيمة الطلب) --}}
  <div class="alert alert-info py-2 small {{ (!empty($storeAmountLimits['min']) || !empty($storeAmountLimits['max'])) ? '' : 'd-none' }}">
    @php
      $minA = $storeAmountLimits['min'] ?? null;
      $maxA = $storeAmountLimits['max'] ?? null;
    @endphp
    <strong>{{ __('purchases.store_amount_limits_hint') }}:</strong>
    @if(!empty($minA))
      <span class="me-2">{{ __('purchases.min_order_total') }}: <strong>{{ number_format((float)$minA,2) }}</strong></span>
    @endif
    @if(!empty($maxA))
      <span>{{ __('purchases.max_order_total') }}: <strong>{{ number_format((float)$maxA,2) }}</strong></span>
    @endif
  </div>

  <div class="mb-3 text-end">
    <button type="button" id="addItemBtn" class="btn btn-outline-primary">
      <i class="fa fa-plus"></i> {{ __('purchases.add_item') }}
    </button>
  </div>

  <div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0">{{ __('purchases.quotation_items') }}</h5></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="quotationItemsTable">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ __('purchases.product') }}</th>
              <th>{{ __('purchases.unit') }}</th>
              <th>{{ __('purchases.quantity') }}</th>
              <th>{{ __('purchases.price') }}</th>
              <th>{{ __('purchases.vat_rate') }}</th>
              <th>{{ __('purchases.vat_amount') }}</th>
              <th>{{ __('purchases.line_total') }}</th>
              <th>{{ __('general.notes') }}</th>
              <th>{{ __('general.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requisition->items as $index => $item)
              @php
                $prod      = $item->product;
                $vatRate   = $prod && $prod->vat == 1 ? ($prod->vat_value ?? 0) : 0;
                $qty       = $item->requested_qty;
                $prc       = $item->last_price;
                $subtotal  = $qty * $prc;
                $vatAmount = $vatRate > 0 ? ($subtotal * $vatRate / 100) : 0;
                $lineTotal = $subtotal + $vatAmount;

                $limitsTxt = '';
                $lim = $limitsMap[$item->item_id] ?? null;
                if ($lim) {
                  $parts = [];
                  if (!is_null($lim['min_qty']) && $lim['min_unit_name']) $parts[] = __('purchases.item_min_limit').': '.number_format($lim['min_qty'],6).' '.$lim['min_unit_name'];
                  if (!is_null($lim['max_qty']) && $lim['max_unit_name']) $parts[] = __('purchases.item_max_limit').': '.number_format($lim['max_qty'],6).' '.$lim['max_unit_name'];
                  $limitsTxt = implode(' — ', $parts);
                }
              @endphp
              <tr data-row-index="{{ $index }}" data-vat-rate="{{ $vatRate }}">
                <td class="row-index">{{ $index + 1 }}</td>
                <td>
                  <select name="product_id[]" class="form-select product-select" required>
                    <option value="">{{ __('purchases.select_product') }}</option>
                    @foreach($products as $prodOpt)
                      <option value="{{ $prodOpt->id }}" {{ $prodOpt->id === $item->item_id ? 'selected' : '' }}>
                        {{ $prodOpt->name_ar }} / {{ $prodOpt->name_en }}
                      </option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <select name="unit_id[]" class="form-select unit-select" required>
                    <option value="">{{ __('purchases.select_unit') }}</option>
                    @foreach($unitsMap[$item->item_id] ?? [] as $u)
                      <option value="{{ $u['id'] }}" {{ $u['id'] === $item->unit_id ? 'selected' : '' }}>
                        {{ $u['name'] }}
                      </option>
                    @endforeach
                  </select>
                  @if(!empty($limitsTxt))
                    <div class="form-text text-muted mt-1 limits-hint">{!! $limitsTxt !!}</div>
                  @else
                    <div class="form-text text-muted mt-1 limits-hint d-none"></div>
                  @endif
                </td>
                <td><input type="number" name="quantity[]" min="0" step="0.01" class="form-control quantity-input" value="{{ $qty }}" required></td>
                <td><input type="number" name="price[]"    min="0" step="0.01" class="form-control price-input"    value="{{ number_format($prc,2) }}" required></td>
                <td><input type="number" name="tax_rate[]" min="0" step="0.01" class="form-control vat-rate-input" value="{{ number_format($vatRate,2) }}" required></td>
                <td><input type="text" class="form-control vat-amount-display"  readonly value="{{ number_format($vatAmount,2) }}"></td>
                <td><input type="text" class="form-control line-total-display" readonly value="{{ number_format($lineTotal,2) }}"></td>
                <td><input type="text" name="item_notes[]" class="form-control" value=""></td>
                <td class="text-center">
                  <button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fa fa-trash">{{ __('purchases.delete') }}</i></button>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">{{ __('purchases.totals') }}</th>
              <th id="sumQuantity">0</th>
              <th></th><th></th>
              <th id="sumVat">0.00</th>
              <th id="sumLineTotal">0.00</th>
              <th></th><th></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12 text-center mb-4">
      <button type="submit" class="btn btn-primary btn-lg me-2">{{ __('purchases.save') }}</button>
      <a href="{{ route('price_quotations.index', ['requisition_id' => $requisition->id]) }}" class="btn btn-warning btn-lg">{{ __('purchases.back') }}</a>
    </div>
  </div>
</form>

{{-- مودال التحذيرات (نفس أسلوب طلبات الاحتياج) --}}
<div class="modal fade" id="limitsModal" tabindex="-1" aria-labelledby="limitsModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning-subtle">
        <h5 class="modal-title" id="limitsModalLabel">{{ __('purchases.limits_warnings_title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
      </div>
      <div class="modal-body">
        @php
          $violations = session('limitViolations', []);
          $orderTotals = session('orderTotals', []);
        @endphp

        @if(!empty($orderTotals))
          <div class="mb-3">
            <strong>{{ __('purchases.total_amount_for_store') }}:</strong>
            <span>{{ number_format($orderTotals['total_amount'] ?? 0, 2) }}</span>
            @if(!empty($orderTotals['min_amount']))
              <span class="ms-3 badge bg-secondary">{{ __('purchases.min_order_total') }}: {{ number_format($orderTotals['min_amount'], 2) }}</span>
            @endif
            @if(!empty($orderTotals['max_amount']))
              <span class="ms-2 badge bg-secondary">{{ __('purchases.max_order_total') }}: {{ number_format($orderTotals['max_amount'], 2) }}</span>
            @endif
          </div>
        @endif

        @if(!empty($violations))
          <ul class="mb-0">
            @foreach($violations as $msg)
              <li class="text-danger">{{ $msg }}</li>
            @endforeach
          </ul>
        @else
          <div class="text-success">{{ __('purchases.no_limit_issues') }}</div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('general.close') }}</button>
      </div>
    </div>
  </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="duplicateToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">{{ __('purchases.duplicate_item_unit') }}</div>
      <button type="button" class="btn-close btn-close-white ms-auto me-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
  const unitsMap   = @json($unitsMap);
  const products   = @json($products);
  const limitsMap  = @json($limitsMap);          // {product_id: {min_qty,min_unit_name,max_qty,max_unit_name}}
</script>

<script>
  $(function(){
    function showToast(message, type='danger'){
      const id = `toast-${Date.now()}`;
      $('#toast-container').append(
        `<div id="${id}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" data-bs-delay="3000">
           <div class="d-flex">
             <div class="toast-body">${message}</div>
             <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
           </div>
         </div>`);
      new bootstrap.Toast(document.getElementById(id)).show();
    }

    function recalcRow($row){
      const qty = parseFloat($row.find('.quantity-input').val()) || 0;
      const price = parseFloat($row.find('.price-input').val()) || 0;
      const vatRate = parseFloat($row.find('.vat-rate-input').val()) || 0;
      const subtotal = qty * price;
      const vatAmount = subtotal * (vatRate/100);
      const lineTotal = subtotal + vatAmount;
      $row.find('.vat-amount-display').val(vatAmount.toFixed(2));
      $row.find('.line-total-display').val(lineTotal.toFixed(2));
    }
    function recalcTotals(){
      let sumQty=0, sumVat=0, sumLine=0;
      $('#quotationItemsTable tbody tr').each(function(){
        sumQty += parseFloat($(this).find('.quantity-input').val()) || 0;
        sumVat += parseFloat($(this).find('.vat-amount-display').val()) || 0;
        sumLine += parseFloat($(this).find('.line-total-display').val()) || 0;
      });
      $('#sumQuantity').text(sumQty);
      $('#sumVat').text(sumVat.toFixed(2));
      $('#sumLineTotal').text(sumLine.toFixed(2));
    }
    function checkDuplicate($row){
      const p=$row.find('.product-select').val(), u=$row.find('.unit-select').val(); let dup=false;
      $('#quotationItemsTable tbody tr').not($row).each(function(){
        if($(this).find('.product-select').val()==p && $(this).find('.unit-select').val()==u){ dup=true; return false; }
      });
      if(dup){ new bootstrap.Toast(document.getElementById('duplicateToast')).show(); }
      return dup;
    }
    function itemLimitsText(productId){
      const L = limitsMap[productId];
      if(!L) return '';
      const parts=[];
      if(L.min_qty!=null && L.min_unit_name){ parts.push(`{{ __('purchases.item_min_limit') }}: ${Number(L.min_qty).toFixed(6)} ${L.min_unit_name}`); }
      if(L.max_qty!=null && L.max_unit_name){ parts.push(`{{ __('purchases.item_max_limit') }}: ${Number(L.max_qty).toFixed(6)} ${L.max_unit_name}`); }
      return parts.join(' — ');
    }
    function applyItemLimitsHint($row){
      const pid = $row.find('.product-select').val();
      const txt = itemLimitsText(pid);
      const $hint = $row.find('.limits-hint');
      if(txt){ $hint.removeClass('d-none').html(txt); } else { $hint.addClass('d-none').empty(); }
    }

    function addRow(item=null){
      const idx = $('#quotationItemsTable tbody tr').length;
      const $row = $('<tr>').attr('data-row-index', idx);
      $row.append(`<td class="row-index">${idx+1}</td>`);

      // product
      let pOpts = `<option value="">{{ __('purchases.select_product') }}</option>`;
      products.forEach(p=>{ pOpts += `<option value="${p.id}">${p.name_ar} / ${p.name_en}</option>`; });
      $row.append(`<td><select name="product_id[]" class="form-select product-select" required>${pOpts}</select></td>`);

      // unit + limits hint
      $row.append(`<td>
        <select name="unit_id[]" class="form-select unit-select" required>
          <option value="">{{ __('purchases.select_unit') }}</option>
        </select>
        <div class="form-text text-muted mt-1 limits-hint d-none"></div>
      </td>`);

      $row.append(`<td><input type="number" name="quantity[]" min="0" step="0.01" class="form-control quantity-input" value="" required></td>`);
      $row.append(`<td><input type="number" name="price[]"    min="0" step="0.01" class="form-control price-input"    value="" required></td>`);
      $row.append(`<td><input type="number" name="tax_rate[]" min="0" step="0.01" class="form-control vat-rate-input" value="0.00" required></td>`);
      $row.append(`<td><input type="text" class="form-control vat-amount-display" readonly value="0.00"></td>`);
      $row.append(`<td><input type="text" class="form-control line-total-display" readonly value="0.00"></td>`);
      $row.append(`<td><input type="text" name="item_notes[]" class="form-control" value=""></td>`);
      $row.append(`<td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fa fa-trash">{{ __('purchases.delete') }}</i></button></td>`);

      $('#quotationItemsTable tbody').append($row);
      recalcRow($row); recalcTotals();

      enhance($row.find('.product-select')); enhance($row.find('.unit-select'));
    }

    // init existing rows
    $('#quotationItemsTable tbody tr').each(function(){
      recalcRow($(this)); applyItemLimitsHint($(this));
    });
    recalcTotals();

    $('#addItemBtn').on('click', addRow);

    // product change -> units + VAT + limits hint
    $(document).on('change', '.product-select', function(){
      const $row = $(this).closest('tr');
      const pid = $(this).val();
      const $unit = $row.find('.unit-select');
      $unit.html(`<option value="">{{ __('purchases.select_unit') }}</option>`);
      (unitsMap[pid] || []).forEach(u => $unit.append(`<option value="${u.id}">${u.name}</option>`));
      const prod = products.find(p => p.id == pid);
      const vat = prod && prod.vat == 1 ? (prod.vat_value || 0) : 0;
      $row.attr('data-vat-rate', vat);
      $row.find('.vat-rate-input').val(Number(vat).toFixed(2));
      applyItemLimitsHint($row);
      recalcRow($row); recalcTotals();
      setTimeout(()=> $unit.trigger('change.select2'), 0);
    });

    // unit change -> duplicate check
    $(document).on('change', '.unit-select', function(){
      const $row = $(this).closest('tr');
      if (checkDuplicate($row)) $(this).val('');
      recalcRow($row); recalcTotals();
    });

    // inputs
    $(document).on('input', '.quantity-input, .price-input, .vat-rate-input', function(){
      const $row = $(this).closest('tr');
      recalcRow($row); recalcTotals();
    });

    // remove row
    $(document).on('click', '.remove-row-btn', function(){
      $(this).closest('tr').remove();
      $('#quotationItemsTable tbody tr').each(function(i){ $(this).find('.row-index').text(i+1); });
      recalcTotals();
    });

    // إظهار مودال المخالفات لو وُجدت بيانات بالجلسة
    @if(session('showLimitModal') && !empty(session('limitViolations')))
      const m = new bootstrap.Modal(document.getElementById('limitsModal'));
      m.show();
    @endif
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  // Select2 helper
  function enhance($el){
    if (!$el || !$el.length || !$.fn.select2) return;
    $el.each(function(){
      const $s=$(this); if($s.data('select2')) return;
      $s.select2({ theme:'bootstrap-5', dir:'rtl', width:'100%', dropdownAutoWidth:true, minimumResultsForSearch:0, dropdownParent:$('body') });
    });
  }
  $(function(){
    enhance($('.select2'));
    enhance($('.product-select'));
    enhance($('.unit-select'));
  });
</script>
@endsection
