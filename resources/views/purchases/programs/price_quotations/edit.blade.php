@extends('layouts.master_table')

@section('title')
  {{ __('purchases.edit') }} - {{ $quotation->quotation_number }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ===== Select2 Assets ===== --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>
<style>
  .select2-container { width: 100% !important; }
  .select2-results__options { max-height: 260px !important; overflow-y: auto !important; }
  .select2-dropdown { max-width: 100vw !important; }
  table .select2-container { max-width: 100% !important; }
</style>
{{-- ===== End Select2 Assets ===== --}}

{{-- Breadcrumb --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('purchase_requisitions.index') }}">{{ __('purchases.requisitions') }}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('price_quotations.index', ['requisition_id' => $requisition->id]) }}">
              {{ __('purchases.price_quotations_for') }}: {{ $requisition->requisition_number }}
            </a>
          </li>
          <li class="breadcrumb-item active">{{ __('purchases.edit') }}</li>
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

{{-- تنبيه حدود المخزن (إجمالي مبلغ العرض) --}}
@php
  $minA = $storeAmountLimits['min'] ?? null;
  $maxA = $storeAmountLimits['max'] ?? null;
@endphp
<div class="alert alert-info py-2 small {{ (!empty($minA) || !empty($maxA)) ? '' : 'd-none' }}">
  <strong>{{ __('purchases.store_amount_limits_hint') }}:</strong>
  @if(!empty($minA))
    <span class="me-2">{{ __('purchases.min_order_total') }}: <strong>{{ number_format((float)$minA,2) }}</strong></span>
  @endif
  @if(!empty($maxA))
    <span>{{ __('purchases.max_order_total') }}: <strong>{{ number_format((float)$maxA,2) }}</strong></span>
  @endif
</div>

<form action="{{ route('price_quotations.update', $quotation->id) }}" method="POST">
  @csrf
  @method('PUT')

  {{-- بيانات عرض السعر --}}
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('purchases.quotation_data') }}</h5>
    </div>
    <div class="card-body">
      <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">

      {{-- رقم عرض السعر (غير قابل للتعديل) --}}
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.price_quotation_number') }}</label>
        <div class="col-md-4">
          <input type="text" class="form-control" value="{{ $quotation->quotation_number }}" readonly>
        </div>
      </div>

      {{-- تاريخ البداية والنهاية --}}
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.start_date') }}</label>
        <div class="col-md-4">
          <input type="date" name="start_date" class="form-control" value="{{ $quotation->start_date }}" required>
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.end_date') }}</label>
        <div class="col-md-4">
          <input type="date" name="end_date" class="form-control" value="{{ $quotation->end_date }}" required>
        </div>
      </div>

      {{-- المورد وفترة التسليم --}}
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.thesupplier') }}</label>
        <div class="col-md-4">
          <select name="supplier_id" class="form-select" required data-placeholder="{{ __('purchases.select_supplier') }}">
            <option value="">{{ __('purchases.select_supplier') }}</option>
            @foreach($suppliers as $sup)
              <option value="{{ $sup->id }}" {{ $sup->id == $quotation->supplier_id ? 'selected' : '' }}>
                {{ $sup->name_ar }} — {{ $sup->name_en }}
              </option>
            @endforeach
          </select>
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.delivery_period') }}</label>
        <div class="col-md-4">
          <input type="number" name="delivery_period" min="0" class="form-control"
                 placeholder="{{ __('purchases.delivery_period_placeholder') }}"
                 value="{{ $quotation->delivery_period }}">
        </div>
      </div>

      {{-- مكان التسليم وطريقة الدفع --}}
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.delivery_location') }}</label>
        <div class="col-md-4">
          <input type="text" name="delivery_location" class="form-control"
                 placeholder="{{ __('purchases.delivery_location_placeholder') }}"
                 value="{{ $quotation->delivery_location }}">
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.payment_method') }}</label>
        <div class="col-md-4">
          <select name="payment_method" class="form-select" data-placeholder="{{ __('purchases.select_payment_method') }}">
            <option value="">{{ __('purchases.select_payment_method') }}</option>
            <option value="cash" {{ $quotation->payment_method === 'cash' ? 'selected' : '' }}>{{ __('purchases.payment_cash') }}</option>
            <option value="bank_transfer" {{ $quotation->payment_method === 'bank_transfer' ? 'selected' : '' }}>{{ __('purchases.payment_bank_transfer') }}</option>
            <option value="credit" {{ $quotation->payment_method === 'credit' ? 'selected' : '' }}>{{ __('purchases.payment_credit') }}</option>
          </select>
        </div>
      </div>

      {{-- شروط الدفع وملاحظات --}}
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.payment_terms') }}</label>
        <div class="col-md-4">
          <textarea name="payment_terms" class="form-control" rows="2"
                    placeholder="{{ __('purchases.payment_terms_placeholder') }}">{{ $quotation->payment_terms }}</textarea>
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.notes') }}</label>
        <div class="col-md-4">
          <textarea name="notes" class="form-control" rows="2"
                    placeholder="{{ __('purchases.notes_placeholder') }}">{{ $quotation->notes }}</textarea>
        </div>
      </div>
    </div>
  </div>

  {{-- زر إضافة بند جديد --}}
  <div class="mb-3 text-end">
    <button type="button" id="addItemBtn" class="btn btn-outline-primary">
      <i class="fa fa-plus"></i> {{ __('purchases.add_item') }}
    </button>
  </div>

  {{-- جدول البنود --}}
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('purchases.quotation_items') }}</h5>
    </div>
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
            @foreach($quotation->items as $index => $item)
              @php
                $before      = $item->quantity * $item->price;
                $vatAmount   = $item->tax_amount;
                $vatRate     = $before > 0 ? ($vatAmount / $before * 100) : 0;
                $lineTotal   = $before + $vatAmount;

                // نص الإيضاح لحدود الصنف
                $limitsTxt = '';
                $lim = $limitsMap[$item->item_id] ?? null;
                if ($lim) {
                  $parts = [];
                  if (!is_null($lim['min_qty']) && $lim['min_unit_name']) $parts[] = __('purchases.item_min_limit').': '.number_format($lim['min_qty'],6).' '.$lim['min_unit_name'];
                  if (!is_null($lim['max_qty']) && $lim['max_unit_name']) $parts[] = __('purchases.item_max_limit').': '.number_format($lim['max_qty'],6).' '.$lim['max_unit_name'];
                  $limitsTxt = implode(' — ', $parts);
                }
              @endphp
              <tr data-row-index="{{ $index }}" data-vat-rate="{{ number_format($vatRate, 2, '.', '') }}">
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
                <td>
                  <input type="number" name="quantity[]" min="0" step="0.01"
                         class="form-control quantity-input"
                         value="{{ number_format($item->quantity, 2, '.', '') }}" required>
                </td>
                <td>
                  <input type="number" name="price[]" min="0" step="0.01"
                         class="form-control price-input"
                         value="{{ number_format($item->price, 2, '.', '') }}" required>
                </td>
                <td>
                  <input type="number" name="tax_rate[]" min="0" step="0.01"
                         class="form-control vat-rate-input"
                         value="{{ number_format($vatRate, 2, '.', '') }}" required>
                </td>
                <td>
                  <input type="text" class="form-control vat-amount-display" readonly
                         value="{{ number_format($vatAmount, 2) }}">
                </td>
                <td>
                  <input type="text" class="form-control line-total-display" readonly
                         value="{{ number_format($lineTotal, 2) }}">
                </td>
                <td>
                  <input type="text" name="item_notes[]" class="form-control" value="{{ $item->notes }}">
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-danger btn-sm remove-row-btn">
                    <i class="fa fa-trash"></i> {{ __('purchases.delete') }}
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">{{ __('purchases.totals') }}</th>
              <th id="sumQuantity">0</th>
              <th></th>
              <th></th>
              <th id="sumVat">0.00</th>
              <th id="sumLineTotal">0.00</th>
              <th></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- زر الحفظ وزر الرجوع --}}
  <div class="row">
    <div class="col-12 text-center mb-4">
      <button type="submit" class="btn btn-primary btn-lg me-2">{{ __('purchases.save') }}</button>
      <a href="{{ route('price_quotations.index', ['requisition_id' => $requisition->id]) }}" class="btn btn-warning btn-lg">
        {{ __('purchases.back') }}
      </a>
    </div>
  </div>
</form>

{{-- مودال التحذيرات الخاصة بالحدود --}}
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

{{-- Toast: تكرار نفس (صنف + وحدة) --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="duplicateToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">{{ __('purchases.duplicate_item_unit') }}</div>
      <button type="button" class="btn-close btn-close-white ms-auto me-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

{{-- Scripts --}}
<script>
  const unitsMap   = @json($unitsMap);
  const products   = @json($products);
  const limitsMap  = @json($limitsMap); // {product_id: {min_qty,min_unit_name,max_qty,max_unit_name}}
</script>

<script>
  $(function(){
    function recalcRow($row) {
      const qty      = parseFloat($row.find('.quantity-input').val()) || 0;
      const price    = parseFloat($row.find('.price-input').val()) || 0;
      const vatRate  = parseFloat($row.find('.vat-rate-input').val()) || 0;
      const subtotal = qty * price;
      const vatAmount= subtotal * vatRate / 100;
      const lineTotal= subtotal + vatAmount;
      $row.find('.vat-amount-display').val(vatAmount.toFixed(2));
      $row.find('.line-total-display').val(lineTotal.toFixed(2));
    }

    function recalcTotals() {
      let sumQty=0, sumVat=0, sumLine=0;
      $('#quotationItemsTable tbody tr').each(function(){
        sumQty  += parseFloat($(this).find('.quantity-input').val())||0;
        sumVat  += parseFloat($(this).find('.vat-amount-display').val())||0;
        sumLine += parseFloat($(this).find('.line-total-display').val())||0;
      });
      $('#sumQuantity').text(sumQty.toFixed(2));
      $('#sumVat').text(sumVat.toFixed(2));
      $('#sumLineTotal').text(sumLine.toFixed(2));
    }

    function checkDuplicate($row) {
      const pid = $row.find('.product-select').val();
      const uid = $row.find('.unit-select').val();
      let dup = false;
      $('#quotationItemsTable tbody tr').not($row).each(function(){
        if ($(this).find('.product-select').val()==pid &&
            $(this).find('.unit-select').val()==uid) {
          dup = true; return false;
        }
      });
      if (dup) {
        new bootstrap.Toast(document.getElementById('duplicateToast')).show();
      }
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

    // add new row
    $('#addItemBtn').click(function(){
      const idx = $('#quotationItemsTable tbody tr').length+1;
      let prodOpts='<option value="">{{ __("purchases.select_product") }}</option>';
      products.forEach(p=> prodOpts+=`<option value="${p.id}">${p.name_ar} / ${p.name_en}</option>`);
      let row=`
        <tr>
          <td class="row-index">${idx}</td>
          <td><select name="product_id[]" class="form-select product-select" required>${prodOpts}</select></td>
          <td>
            <select name="unit_id[]" class="form-select unit-select" required>
              <option value="">{{ __("purchases.select_unit") }}</option>
            </select>
            <div class="form-text text-muted mt-1 limits-hint d-none"></div>
          </td>
          <td><input type="number" name="quantity[]" class="form-control quantity-input" step="0.01" min="0"></td>
          <td><input type="number" name="price[]" class="form-control price-input" step="0.01" min="0"></td>
          <td><input type="number" name="tax_rate[]" class="form-control vat-rate-input" step="0.01" min="0" value="0.00"></td>
          <td><input type="text" class="form-control vat-amount-display" readonly value="0.00"></td>
          <td><input type="text" class="form-control line-total-display" readonly value="0.00"></td>
          <td><input type="text" name="item_notes[]" class="form-control"></td>
          <td><button type="button" class="btn btn-danger btn-sm remove-row-btn">
                <i class="fa fa-trash"></i> {{ __('purchases.delete') }}
              </button></td>
        </tr>`;
      $('#quotationItemsTable tbody').append(row);
    });

    // delegate events
    $(document).on('change','.product-select',function(){
      const $r=$(this).closest('tr'), pid=$(this).val();
      const $u=$r.find('.unit-select').empty()
                .append(`<option value="">{{ __("purchases.select_unit") }}</option>`);
      (unitsMap[pid]||[]).forEach(u=> $u.append(`<option value="${u.id}">${u.name}</option>`));
      const prod=products.find(p=>p.id==pid);
      $r.find('.vat-rate-input').val(prod && prod.vat==1 ? (prod.vat_value||0) : 0);
      applyItemLimitsHint($r);
      recalcRow($r); recalcTotals();
      setTimeout(()=> $u.trigger('change.select2'), 0);
    });

    $(document).on('change','.unit-select',function(){
      const $r=$(this).closest('tr');
      if(checkDuplicate($r)) return $(this).val('');
      recalcRow($r); recalcTotals();
    });

    $(document).on('input','.quantity-input, .price-input, .vat-rate-input',function(){
      const $r=$(this).closest('tr');
      recalcRow($r); recalcTotals();
    });

    $(document).on('click','.remove-row-btn',function(){
      $(this).closest('tr').remove();
      $('#quotationItemsTable tbody tr').each(function(i){
        $(this).find('.row-index').text(i+1);
      });
      recalcTotals();
    });

    // init الحالية
    $('#quotationItemsTable tbody tr').each(function(){
      recalcRow($(this));
      applyItemLimitsHint($(this));
    });
    recalcTotals();

    // إظهار مودال المخالفات لو وُجدت بيانات بالجلسة
    @if(session('showLimitModal') && !empty(session('limitViolations')))
      const m = new bootstrap.Modal(document.getElementById('limitsModal'));
      m.show();
    @endif
  });
</script>

{{-- ===== Select2 JS + Init ===== --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  (function(){
    function enhance($el){
      if (!$.fn.select2) return;
      $el.each(function(){
        const $s = $(this);
        if ($s.data('select2')) return;
        $s.select2({
          theme: 'bootstrap-5',
          dir: 'rtl',
          width: '100%',
          dropdownAutoWidth: false,
          minimumResultsForSearch: 0,
          placeholder: $s.attr('placeholder') || $s.data('placeholder') || '',
          dropdownParent: $('body')
        });
      });
    }

    $(function(){
      enhance($('select[name="supplier_id"]'));
      enhance($('select[name="payment_method"]'));
      enhance($('.product-select'));
      enhance($('.unit-select'));

      const tbody = document.querySelector('#quotationItemsTable tbody');
      if (tbody) {
        const obs = new MutationObserver(muts => {
          muts.forEach(m => {
            $(m.addedNodes).each(function(){
              const $node = $(this);
              enhance($node.find('.product-select'));
              enhance($node.find('.unit-select'));
            });
          });
        });
        obs.observe(tbody, { childList: true });
      }
    });
  })();
</script>
{{-- ===== End Select2 ===== --}}

@endsection
