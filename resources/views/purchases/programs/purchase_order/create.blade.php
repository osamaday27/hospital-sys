@extends('layouts.master_table')

@section('title')
  {{ __('purchases.create_purchase_order') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>

{{-- Breadcrumb --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('price_quotations.index') }}">{{ __('purchases.price_quotations') }}</a>
          </li>
          <li class="breadcrumb-item active">{{ __('purchases.create_purchase_order') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

{{-- Toast container (للتنبيهات السريعة) --}}
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>

<form action="{{ route('purchase_order.store') }}" method="POST" id="orderForm">
  @csrf
  <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
  <input type="hidden" name="order_number" value="{{ $orderNumber }}">

  {{-- ==== البيانات الأساسية ==== --}}
  <div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">{{ __('purchases.basic_data') }}</h5></div>
    <div class="card-body">
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.order_number') }}</label>
        <div class="col-md-4">
          <input type="text" class="form-control" value="{{ $orderNumber }}" readonly>
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.order_date') }}</label>
        <div class="col-md-4">
          <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.currency') }}</label>
        <div class="col-md-4">
          <select name="currency" class="form-select select2" data-placeholder="{{ __('purchases.select') }}">
            @foreach($currencyOptions as $c => $l)
              <option value="{{ $c }}" {{ $c == $defaultCurrency ? 'selected' : '' }}>
                {{ $l }}
              </option>
            @endforeach
          </select>
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.delivery_period') }}</label>
        <div class="col-md-4">
          <input type="number" name="delivery_period" class="form-control" min="0"
                 value="{{ $quotation->delivery_period }}">
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.supplier') }}</label>
        <div class="col-md-4">
          <select name="supplier_id" class="form-select select2" data-placeholder="{{ __('purchases.select_supplier') }}" required>
            @foreach($suppliers as $sup)
              <option value="{{ $sup->id }}" {{ $sup->id == $defaultSupplierId ? 'selected' : '' }}>
                {{ $sup->name_ar }} — {{ $sup->name_en }}
              </option>
            @endforeach
          </select>
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.priority') }}</label>
        <div class="col-md-4">
          <select name="priority" class="form-select select2" data-placeholder="{{ __('purchases.select') }}">
            @foreach($priorityOptions as $pr)
              <option value="{{ $pr }}" {{ $pr == $defaultPriority ? 'selected' : '' }}>
                {{ __("purchases.priority_{$pr}") }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.delivery_location') }}</label>
        <div class="col-md-4">
          <input type="text" name="delivery_location" class="form-control"
                 value="{{ $quotation->delivery_location }}">
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.payment_method') }}</label>
        <div class="col-md-4">
          <select name="payment_method" class="form-select select2" data-placeholder="{{ __('purchases.select_payment_method') }}">
            <option value="">{{ __('purchases.select_payment_method') }}</option>
            <option value="cash" {{ $quotation->payment_method=='cash'?'selected':'' }}>
              {{ __('purchases.payment_cash') }}
            </option>
            <option value="bank_transfer" {{ $quotation->payment_method=='bank_transfer'?'selected':'' }}>
              {{ __('purchases.payment_bank_transfer') }}
            </option>
            <option value="credit" {{ $quotation->payment_method=='credit'?'selected':'' }}>
              {{ __('purchases.payment_credit') }}
            </option>
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-md-2 col-form-label">{{ __('purchases.payment_terms') }}</label>
        <div class="col-md-4">
          <textarea name="payment_terms" class="form-control">{{ $quotation->payment_terms }}</textarea>
        </div>
        <label class="col-md-2 col-form-label">{{ __('purchases.notes') }}</label>
        <div class="col-md-4">
          <textarea name="notes" class="form-control">{{ $quotation->notes }}</textarea>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== تنبيه حدود المخزن (إجمالي قيمة الطلب) كما في عروض الأسعار ===== --}}
  @php
    $storeAmountLimits = $storeAmountLimits ?? [];
    $minA = $storeAmountLimits['min'] ?? null;
    $maxA = $storeAmountLimits['max'] ?? null;
  @endphp
  <div class="alert alert-info py-2 small {{ ($minA!==null || $maxA!==null) ? '' : 'd-none' }}">
    <strong>{{ __('purchases.store_amount_limits_hint') }}:</strong>
    @if($minA!==null)
      <span class="me-2">{{ __('purchases.min_order_total') }}:
        <strong>{{ number_format((float)$minA,2) }}</strong></span>
    @endif
    @if($maxA!==null)
      <span>{{ __('purchases.max_order_total') }}:
        <strong>{{ number_format((float)$maxA,2) }}</strong></span>
    @endif
  </div>

  {{-- ==== جدول البنود ==== --}}
  <div class="mb-2 text-end">
    <button type="button" id="addItemBtn" class="btn btn-outline-primary">
      <i class="fa fa-plus"></i> {{ __('purchases.add_item') }}
    </button>
  </div>
  <div class="card mb-4">
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle text-center" id="itemsTable">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>{{ __('purchases.item') }}</th>
            <th>{{ __('purchases.unit') }}</th>
            <th>{{ __('purchases.quantity') }}</th>
            <th>{{ __('purchases.price') }}</th>
            <th>{{ __('purchases.vat_rate') }}</th>
            <th>{{ __('purchases.vat_amount') }}</th>
            <th>{{ __('purchases.line_total') }}</th>
            <th>{{ __('general.notes') }}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @php $limitsMap = $limitsMap ?? []; @endphp
          @foreach($quotation->items as $idx => $it)
            @php
              $prod   = $it->product;
              $sub    = $it->quantity * $it->price;
              $vAmt   = $it->tax_amount;
              $line   = $sub + $vAmt;
              $taxPct = $sub>0 ? round($vAmt/$sub*100,2) : 0;
              $uOpts  = $unitsMap[$prod->id] ?? [];

              $limitsTxt = '';
              $lim = $limitsMap[$it->item_id] ?? $limitsMap[$prod->id] ?? null;
              if ($lim) {
                $parts = [];
                if (!is_null($lim['min_qty']) && !empty($lim['min_unit_name'])) {
                  $parts[] = __('purchases.item_min_limit').': '.number_format((float)$lim['min_qty'],6).' '.$lim['min_unit_name'];
                }
                if (!is_null($lim['max_qty']) && !empty($lim['max_unit_name'])) {
                  $parts[] = __('purchases.item_max_limit').': '.number_format((float)$lim['max_qty'],6).' '.$lim['max_unit_name'];
                }
                $limitsTxt = implode(' — ', $parts);
              }
            @endphp
            <tr data-vat-rate="{{ $taxPct }}">
              <td class="row-idx">{{ $idx+1 }}</td>
              <td>
                <select name="product_id[]" class="form-select product-select select2" data-placeholder="{{ __('purchases.select') }}" required>
                  <option value="">{{ __('purchases.select') }}</option>
                  @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ $p->id==$prod->id?'selected':'' }}>
                      {{ $p->name_ar }} / {{ $p->name_en }}
                    </option>
                  @endforeach
                </select>
              </td>
              <td>
                <select name="unit_id[]" class="form-select unit-select select2" data-placeholder="{{ __('purchases.select') }}" required>
                  <option value="">{{ __('purchases.select') }}</option>
                  @foreach($uOpts as $u)
                    <option value="{{ $u['id'] }}" {{ $u['id']==$it->unit_id?'selected':'' }}>
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
              <td><input type="number" name="quantity[]" class="form-control qty quantity-input"  value="{{ number_format($it->quantity,2,'.','') }}" step="0.01" min="0"></td>
              <td><input type="number" name="price[]"    class="form-control price price-input" value="{{ number_format($it->price,2,'.','') }}"   step="0.01" min="0"></td>
              <td><input type="number" name="tax_rate[]" class="form-control vat vat-rate-input"   value="{{ number_format($taxPct,2,'.','') }}"     step="0.01" min="0"></td>
              <td><input type="text" class="form-control vat-amt vat-amount-display"   value="{{ number_format($vAmt,2) }}" readonly></td>
              <td><input type="text" class="form-control line-total line-total-display" value="{{ number_format($line,2) }}" readonly></td>
              <td><input type="text" name="item_notes[]" class="form-control" value="{{ $it->notes }}"></td>
              <td><button type="button" class="btn btn-sm btn-danger delRow remove-row-btn">×</button></td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="table-light fw-bold">
          <tr>
            <td colspan="3">{{ __('purchases.totals') }}</td>
            <td id="sumQty">0</td><td></td><td></td>
            <td id="sumVat">0.00</td><td id="sumTotal">0.00</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  {{-- ==== لجنة الفحص والاستلام ==== --}}
  <div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">{{ __('purchases.committee_inspection') }}</h5></div>
    <div class="card-body">
      <button type="button" id="addCommitteeBtn" class="btn btn-outline-primary mb-2">
        <i class="fa fa-plus"></i> {{ __('purchases.add_member') }}
      </button>
      <div class="table-responsive">
        <table class="table table-bordered text-center" id="committeeTable">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>{{ __('purchases.employee_name') }}</th>
              <th>{{ __('purchases.department') }}</th>
              <th>{{ __('purchases.job_position') }}</th>
              <th>{{ __('purchases.committee_chair') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-center mb-4">
    <button type="submit" class="btn btn-primary mx-2">{{ __('purchases.save') }}</button>
    <a href="{{ route('price_quotations.index',['requisition_id'=>$quotation->requisition_id]) }}"
       class="btn btn-secondary mx-2">{{ __('purchases.back') }}</a>
  </div>
</form>

{{-- مودال التحذيرات (نفس أسلوب عروض الأسعار) --}}
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

{{-- Toasts للتكرار --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:11">
  <div id="duplicateItemToast" class="toast align-items-center bg-danger text-white border-0" role="alert">
    <div class="toast-body">{{ __('purchases.duplicate_item_unit') }}</div>
  </div>
  <div id="duplicateCommitteeToast" class="toast align-items-center bg-danger text-white border-0" role="alert">
    <div class="toast-body">{{ __('purchases.duplicate_committee_member') }}</div>
  </div>
</div>

<script>
  const unitsMap    = @json($unitsMap);
  const products    = @json($products);
  const employees   = @json($employees);
  const departments = @json($departments);
  const txtSelect   = @json(__('purchases.select'));
  const ratesMap    = {
    @foreach($quotation->items as $it)
      "{{ $it->item_id }}": {{ ($it->price * $it->quantity) > 0
        ? round($it->tax_amount / ($it->price * $it->quantity) * 100, 2)
        : 0
      }}@if(!$loop->last),@endif
    @endforeach
  };
  const limitsMap   = @json($limitsMap ?? []);
</script>

{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function bootSelect2Init(){
  function ensureSelect2(cb){
    if (window.jQuery && $.fn && $.fn.select2) return cb();
    window.addEventListener('load', function(){
      if (window.jQuery && $.fn && $.fn.select2) return cb();
      var s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
      s.onload = cb;
      document.head.appendChild(s);
    });
  }

  ensureSelect2(function(){
    $(function(){

      // Helpers
      const showItemDupToast      = () => new bootstrap.Toast($('#duplicateItemToast')).show();
      const showCommitteeDupToast = () => new bootstrap.Toast($('#duplicateCommitteeToast')).show();

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

      function parentFor($el){
        const p = $el.closest('.modal, .card, form, body');
        return p.length ? p : $('body');
      }
      function enhance($el){
        if(!$el || !$el.length || !$.fn.select2) return;
        $el.each(function(){
          const $s = $(this);
          if ($s.data('select2')) return;
          $s.select2({
            theme: 'bootstrap-5',
            dir: 'rtl',
            width: '100%',
            dropdownAutoWidth: true,
            minimumResultsForSearch: 0,
            placeholder: $s.data('placeholder') || '',
            dropdownParent: parentFor($s)
          });
        });
      }

      enhance($('.select2'));
      enhance($('.product-select'));
      enhance($('.unit-select'));

      // مراقبة العناصر المضافة
      const observeTargets = ['#itemsTable tbody', '#committeeTable tbody'];
      observeTargets.forEach(sel=>{
        const node = document.querySelector(sel);
        if(!node) return;
        new MutationObserver(muts=>{
          muts.forEach(m=>{
            $(m.addedNodes).each(function(){
              enhance($(this).find('select'));
            });
          });
        }).observe(node, {childList:true});
      });

      // ===== جدول البنود =====
      const $items = $('#itemsTable tbody');

      function calcRow($r) {
        let q = parseFloat($r.find('.quantity-input, .qty').val()) || 0,
            p = parseFloat($r.find('.price-input, .price').val()) || 0,
            v = parseFloat($r.find('.vat-rate-input, .vat').val()) || 0,
            sub = q*p,
            vatAmt = sub * v / 100,
            tot = sub + vatAmt;
        $r.find('.vat-amount-display, .vat-amt').val(vatAmt.toFixed(2));
        $r.find('.line-total-display, .line-total').val(tot.toFixed(2));
      }
      function calcTotals() {
        let Q=0, V=0, T=0;
        $items.find('tr').each(function(){
          Q += parseFloat($(this).find('.quantity-input, .qty').val()) || 0;
          V += parseFloat($(this).find('.vat-amount-display, .vat-amt').val()) || 0;
          T += parseFloat($(this).find('.line-total-display, .line-total').val()) || 0;
        });
        $('#sumQty').text(Q.toFixed(2));
        $('#sumVat').text(V.toFixed(2));
        $('#sumTotal').text(T.toFixed(2));
      }

      function itemLimitsText(productId){
        const L = limitsMap?.[productId];
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

      function checkDuplicate($row){
        const p=$row.find('.product-select').val(), u=$row.find('.unit-select').val(); let dup=false;
        $items.find('tr').not($row).each(function(){
          if($(this).find('.product-select').val()==p && $(this).find('.unit-select').val()==u){ dup=true; return false; }
        });
        if(dup){ showItemDupToast(); }
        return dup;
      }

      // التغييرات
      $items.on('change', '.product-select', function(){
        const $r = $(this).closest('tr');
        const pid = $(this).val();
        const $u = $r.find('.unit-select').empty().append(`<option value="">${txtSelect}</option>`);
        (unitsMap[pid] || []).forEach(u => $u.append(`<option value="${u.id}">${u.name}</option>`));

        // VAT الافتراضي: من ratesMap إن وُجد وإلا من خصائص الصنف
        let vatDefault = 0;
        if (ratesMap[pid] !== undefined) {
          vatDefault = Number(ratesMap[pid]) || 0;
        } else {
          const prod = products.find(p => String(p.id) === String(pid));
          if (prod && Number(prod.vat) === 1) vatDefault = Number(prod.vat_value) || 0;
        }
        $r.find('.vat, .vat-rate-input').val(Number(vatDefault).toFixed(2));

        applyItemLimitsHint($r);
        calcRow($r); calcTotals();
        setTimeout(()=> $u.trigger('change.select2'), 0);
      });

      $items.on('change', '.unit-select', function(){
        const $r = $(this).closest('tr');
        const pid = $r.find('.product-select').val();
        const uid = $(this).val();
        const dup = $items.find('tr').not($r).filter((i,tr)=>
          $(tr).find('.product-select').val()==pid &&
          $(tr).find('.unit-select').val()==uid
        ).length > 0;
        if (dup){ showItemDupToast(); $(this).val(''); return; }
        calcRow($r); calcTotals();
      });

      $items.on('input', '.qty,.price,.vat,.quantity-input,.price-input,.vat-rate-input', function(){
        calcRow($(this).closest('tr')); calcTotals();
      });

      // إضافة صف
      $('#addItemBtn').click(function(){
        const idx  = $items.find('tr').length + 1;
        const pOpts= products.map(p => `<option value="${p.id}">${p.name_ar} / ${p.name_en}</option>`).join('');
        const $row = $(`
          <tr>
            <td class="row-idx">${idx}</td>
            <td>
              <select name="product_id[]" class="form-select product-select select2" data-placeholder="${txtSelect}" required>
                <option value="">${txtSelect}</option>${pOpts}
              </select>
            </td>
            <td>
              <select name="unit_id[]" class="form-select unit-select select2" data-placeholder="${txtSelect}" required>
                <option value="">${txtSelect}</option>
              </select>
              <div class="form-text text-muted mt-1 limits-hint d-none"></div>
            </td>
            <td><input type="number" name="quantity[]" class="form-control qty quantity-input" step="0.01" min="0"></td>
            <td><input type="number" name="price[]"    class="form-control price price-input" step="0.01" min="0"></td>
            <td><input type="number" name="tax_rate[]" class="form-control vat vat-rate-input"   step="0.01" min="0"></td>
            <td><input type="text" class="form-control vat-amt vat-amount-display" readonly></td>
            <td><input type="text" class="form-control line-total line-total-display" readonly></td>
            <td><input type="text" name="item_notes[]" class="form-control"></td>
            <td><button type="button" class="btn btn-sm btn-danger delRow remove-row-btn">×</button></td>
          </tr>
        `);
        $items.append($row);
        enhance($row.find('select'));
      });

      $items.on('click', '.delRow, .remove-row-btn', function(){
        $(this).closest('tr').remove();
        $items.find('.row-idx').each((i,td)=>$(td).text(i+1));
        calcTotals();
      });

      // حساب القيم الابتدائية + إظهار حدود الأصناف إن وُجدت
      $items.find('tr').each(function(){
        calcRow($(this));
        applyItemLimitsHint($(this));
      });
      calcTotals();

      // ===== لجنة الفحص والاستلام =====
      const $comm = $('#committeeTable tbody');
      function reindexComm(){ $comm.find('.cm-idx').each((i, td) => $(td).text(i+1)); }

      $('#addCommitteeBtn').click(function(){
        let idx      = $comm.find('tr').length + 1;
        let empOpts  = employees.map(e => `<option value="${e.id}">${e.full_name}</option>`).join('');
        let deptOpts = departments.map(d => `<option value="${d.id}">${d.name_ar}</option>`).join('');
        const $row = $(`
          <tr>
            <td class="cm-idx">${idx}</td>
            <td><select name="committee_employee_id[]" class="form-select cm-emp select2" data-placeholder="{{ __('purchases.select_employee') }}" required>
                  <option value="">${txtSelect}</option>${empOpts}</select></td>
            <td><select name="committee_department_id[]" class="form-select cm-dep select2" data-placeholder="{{ __('purchases.select_department') }}">
                  <option value="">${txtSelect}</option>${deptOpts}</select></td>
            <td><input type="text" name="committee_job_position[]" class="form-control cm-job"></td>
            <td><input type="radio" name="committee_chair" class="form-check-input cm-chair" value="${idx}"></td>
            <td><button type="button" class="btn btn-sm btn-danger cm-del">×</button></td>
          </tr>
        `);
        $comm.append($row);
        enhance($row.find('select'));
        reindexComm();
      });

      $comm.on('click', '.cm-del', function(){
        $(this).closest('tr').remove();
        reindexComm();
      });

      $comm.on('change', '.cm-emp', function(){
        let id = $(this).val();
        if ($comm.find('select.cm-emp').filter((i,el) => $(el).val()==id).length > 1) {
          showCommitteeDupToast();
          $(this).val(''); return;
        }
        let emp = employees.find(e => String(e.id) === String(id)) || {};
        let $tr = $(this).closest('tr');
        $tr.find('.cm-dep').val(emp.department||'').trigger('change.select2');
        $tr.find('.cm-job').val(emp.job_position||'');
      });

      // إظهار مودال الحدود إن كانت هناك مخالفات قادمة من السيرفر
      @if(session('showLimitModal') && !empty(session('limitViolations')))
        new bootstrap.Modal(document.getElementById('limitsModal')).show();
      @endif

    });
  });
})();
</script>
@endsection
