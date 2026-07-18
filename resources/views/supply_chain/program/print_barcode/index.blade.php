@extends('layouts.master_table')

@section('title')
  {{ __('supply_chain_trans.print_barcode') ?? 'طباعة باركود' }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Toasts --}}
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

<div class="card">
  {{-- شريط علوي مثبت للفلاتر والإعدادات --}}
  <div class="card-header p-0">
    <div class="sticky-top bg-body border-bottom">
      <div class="container-fluid py-2">
        <div class="d-flex flex-wrap align-items-center gap-3">
          <strong class="me-2">{{ __('طباعة الباركود') }}</strong>

          {{-- أوضاع التحميل --}}
          <div class="btn-group" role="group" aria-label="Mode">
            <input type="radio" class="btn-check" name="mode" id="mode_all" value="all" checked>
            <label class="btn btn-outline-primary" for="mode_all">{{ __('كل الأصناف') }}</label>

            <input type="radio" class="btn-check" name="mode" id="mode_category" value="category">
            <label class="btn btn-outline-primary" for="mode_category">{{ __('حسب المجموعات') }}</label>

            <input type="radio" class="btn-check" name="mode" id="mode_store" value="store">
            <label class="btn btn-outline-primary" for="mode_store">{{ __('حسب المخزن') }}</label>
          </div>

          {{-- فلاتر الوضع --}}
          <select id="category-select" class="form-select" style="width:240px; display:none"></select>
          <select id="store-select" class="form-select" style="width:240px; display:none"></select>

          {{-- مصدر الكود (افتراضي كود النظام) --}}
          <div class="ms-auto d-flex flex-wrap align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small">{{ __('مصدر الكود:') }}</span>
              <div class="btn-group" role="group" aria-label="Code source">
                <input class="btn-check" type="radio" name="code_type" id="ct_system" value="system_code" checked>
                <label class="btn btn-outline-secondary" for="ct_system">{{ __('كود النظام') }}</label>

                <input class="btn-check" type="radio" name="code_type" id="ct_barcode" value="barcode">
                <label class="btn btn-outline-secondary" for="ct_barcode">{{ __('الباركود') }}</label>

                <input class="btn-check" type="radio" name="code_type" id="ct_product" value="product_code">
                <label class="btn btn-outline-secondary" for="ct_product">{{ __('كود الصنف') }}</label>
              </div>
            </div>

            {{-- مقاسات الملصقات --}}
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small">{{ __('مقاس الملصق:') }}</span>
              <select id="label-size" class="form-select form-select-sm" style="width:170px">
                <option value="38x25" selected>38 × 25 مم (صغير)</option>
                <option value="50x30">50 × 30 مم</option>
                <option value="58x40">58 × 40 مم</option>
                <option value="70x35">70 × 35 مم</option>
                <option value="70x50">70 × 50 مم</option>
              </select>
            </div>

            {{-- معاينة/طباعة --}}
            <div class="btn-group">
              <button id="btn-preview" class="btn btn-primary">
                <i class="ri-eye-line"></i> {{ __('معاينة') }}
              </button>
              <button id="btn-print" class="btn btn-success" disabled>
                <i class="ri-printer-line"></i> {{ __('طباعة') }}
              </button>
            </div>
          </div>
        </div>

        {{-- شريط اختيارات الجدول --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
          <button class="btn btn-outline-secondary btn-sm" id="btn-select-page">
            <i class="ri-checkbox-multiple-line"></i> {{ __('تحديد الصفحة') }}
          </button>
          <button class="btn btn-outline-secondary btn-sm" id="btn-select-all-pages">
            <i class="ri-checkbox-line"></i> {{ __('تحديد كل النتائج') }}
          </button>
          <button class="btn btn-outline-secondary btn-sm" id="btn-clear-selection">
            <i class="ri-close-circle-line"></i> {{ __('إلغاء التحديد') }}
          </button>

          <div class="vr mx-2"></div>
          <span class="small text-muted">{{ __('المحدد:') }} <span id="sel-count" class="fw-semibold">0</span></span>

          <div class="ms-auto">
            <input type="text" id="quick-search" class="form-control form-control-sm" placeholder="{{ __('بحث...') }}" style="width:220px">
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- جدول المنتجات --}}
  <div class="card-body">
    <div class="table-responsive">
      <table id="products-table" class="table table-bordered table-striped align-middle w-100">
        <thead class="table-light">
          <tr>
            <th style="width:48px">#</th>
            <th style="width:48px">
              <input type="checkbox" id="hdr-check-page" class="form-check-input" title="{{ __('تحديد الصفحة الحالية') }}">
            </th>
            <th>{{ __('الكود المختار') }}</th>
            <th>{{ __('الصنف') }}</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal: المعاينة/الطباعة --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header no-print">
        <h6 class="modal-title">{{ __('معاينة الطباعة') }}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('إغلاق') }}"></button>
      </div>
      <div class="modal-body" id="labels-print-area">
        <div id="labels-sheet" class="labels-sheet size-38x25"></div>
      </div>
      <div class="modal-footer no-print">
        <button class="btn btn-light" data-bs-dismiss="modal">{{ __('إغلاق') }}</button>
        <button class="btn btn-success" id="modal-print">{{ __('طباعة') }}</button>
      </div>
    </div>
  </div>
</div>

<style>
.labels-sheet{
  display: flex; flex-wrap: wrap;
  gap: 2mm; align-items: flex-start;
}

/* كل باركود على ملصق مستقل */
.labels-sheet.size-38x25 .label{ width:38mm; height:25mm; }
.labels-sheet.size-50x30 .label{ width:50mm; height:30mm; }
.labels-sheet.size-58x40 .label{ width:58mm; height:40mm; }
.labels-sheet.size-70x35 .label{ width:70mm; height:35mm; }
.labels-sheet.size-70x50 .label{ width:70mm; height:50mm; }

.label{
  border: 1px dashed #ddd;
  padding: 1.5mm 1mm 1mm;
  box-sizing: border-box;
  display: flex; flex-direction: column; justify-content: space-between;
}
.label .name{
  font-size: 10px; line-height: 1.1;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-align: center;
}
.label .bar-wrap{ display:flex; align-items:center; justify-content:center; }
.label .bar-img{ width:100%; height:auto; display:block; }
.label .code{ font-size: 9px; text-align:center; margin-top: 0.5mm; }

/* الطباعة — إظهار الملصقات فقط */
@media print {
  @page { size: auto; margin: 2mm; }
  body * { visibility: hidden !important; }
  #labels-print-area, #labels-print-area * { visibility: visible !important; }
  .no-print { display: none !important; }
  .label{ border: none; }
  .modal-backdrop.show { opacity: 0 !important; }
  .modal, .modal-dialog, .modal-content { position: static !important; box-shadow:none !important; }
}
</style>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const CATEGORIES = @json($categories ?? []);
const STORES     = @json($stores ?? []);

let dt, selected = new Set();

function showToast(type, msg) {
  const id = 't'+Date.now()+Math.random().toString().slice(2);
  const html = `
    <div id="${id}" class="toast align-items-center text-bg-${type} border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body">${msg}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>`;
  $('#toast-container').append(html);
  new bootstrap.Toast(document.getElementById(id), { delay: 2500 }).show();
}
function esc(s){ return $('<div>').text(s||'').html(); }
function refreshSelCount(){ $('#sel-count').text(selected.size); }

function currentMode(){ return $('input[name="mode"]:checked').val() || 'all'; }
function currentCodeType(){ return $('input[name="code_type"]:checked').val() || 'system_code'; }
function currentCategory(){ return parseInt($('#category-select').val() || 0, 10); }
function currentStore(){ return parseInt($('#store-select').val() || 0, 10); }

/* تعبئة القوائم */
function setupPickers(){
  const $cat = $('#category-select').empty();
  CATEGORIES.forEach(c => $cat.append(new Option(c.name, c.id)));
  const $store = $('#store-select').empty();
  STORES.forEach(s => $store.append(new Option(s.name, s.id)));
}

/* DataTable */
$(function(){
  setupPickers();

  dt = $('#products-table').DataTable({
    serverSide: true,
    processing: true,
    pageLength: 10,
    lengthChange: true,
    searching: true,
    order: [[3,'asc']],
    dom: 'rt<"d-flex justify-content-between align-items-center mt-2"ip>',
    ajax: {
      url: "{{ route('print_barcode.index') }}",
      type: "GET",
      data: function(d){
        d.mode        = currentMode();
        d.category_id = currentCategory();
        d.store_id    = currentStore();
        d.code_type   = currentCodeType();
        const ext = $('#quick-search').val() || '';
        if (ext) d.q = ext;
      }
    },
    columns: [
      { data: null, orderable:false, searchable:false },
      { data: null, orderable:false, searchable:false,
        render: function(row){
          const checked = selected.has(Number(row.id)) ? 'checked' : '';
          return `<input type="checkbox" class="row-check form-check-input" data-id="${row.id}" ${checked}>`;
        }
      },
      { data: 'display_code', name:'display_code', defaultContent:'' },
      { data: 'product_name', name:'product_name', defaultContent:'' },
    ]
  });

  dt.on('draw', function(){
    dt.column(0, { page:'current' }).nodes().each((cell, i) => {
      cell.innerHTML = (dt.page.info().start + i + 1);
    });
    $('#products-table tbody input.row-check').each(function(){
      const id = Number(this.dataset.id);
      this.checked = selected.has(id);
    });
    $('#hdr-check-page').prop('checked', false);
  });

  // تبديل الأوضاع/الفلاتر
  $('input[name="mode"]').on('change', function(){
    $('#category-select').toggle(this.value==='category');
    $('#store-select').toggle(this.value==='store');
    selected.clear(); refreshSelCount();
    dt.ajax.reload();
  });
  $('#category-select,#store-select').on('change', function(){
    selected.clear(); refreshSelCount();
    dt.ajax.reload();
  });

  // تبديل مصدر الكود (لا نمسح التحديد)
  $('input[name="code_type"]').on('change', function(){
    dt.ajax.reload(null, false);
  });

  // بحث خارجي
  let t;
  $('#quick-search').on('input', function(){
    clearTimeout(t);
    t = setTimeout(()=> dt.ajax.reload(), 300);
  });

  // تحديد الصفحة الحالية من الهيدر
  $('#hdr-check-page').on('change', function(){
    const check = this.checked;
    $('#products-table tbody input.row-check').each(function(){
      const id = Number(this.dataset.id);
      if (check) { selected.add(id); this.checked = true; }
      else       { selected.delete(id); this.checked = false; }
    });
    refreshSelCount();
  });

  // أزرار التحديد
  $('#btn-select-page').on('click', function(){
    $('#hdr-check-page').prop('checked', true).trigger('change');
  });
  $('#btn-select-all-pages').on('click', function(){
    const $btn = $(this).prop('disabled', true);
    const params = {
      op: 'ids',
      mode: currentMode(),
      category_id: currentCategory(),
      store_id: currentStore(),
      code_type: currentCodeType(),
      q: $('#quick-search').val() || dt.search() || ''
    };
    $.get("{{ route('print_barcode.index') }}", params)
      .done(resp => {
        const ids = resp?.ids || [];
        ids.forEach(id => selected.add(Number(id)));
        $('#products-table tbody input.row-check').each(function(){
          const id = Number(this.dataset.id);
          this.checked = selected.has(id);
        });
        refreshSelCount();
        showToast('success', '{{ __("تم تحديد جميع النتائج المطابقة") }}');
      })
      .fail(()=> showToast('danger', '{{ __("تعذر جلب النتائج") }}'))
      .always(()=> $btn.prop('disabled', false));
  });
  $('#btn-clear-selection').on('click', function(){
    selected.clear(); refreshSelCount();
    $('#products-table tbody input.row-check').prop('checked', false);
    $('#hdr-check-page').prop('checked', false);
  });

  // اختيار صف فردي
  $(document).on('change', '.row-check', function(){
    const id = Number(this.dataset.id);
    if (this.checked) selected.add(id); else selected.delete(id);
    refreshSelCount();
  });

  // تغيير مقاس الملصق
  $('#label-size').on('change', function(){
    const v = $(this).val();
    const $sheet = $('#labels-sheet').removeClass(function(i,cls){
      return (cls.match(/size-\S+/g)||[]).join(' ');
    });
    $sheet.addClass('size-'+v);
  });

  // معاينة عبر milon/barcode (SVG data URL)
  $('#btn-preview').on('click', function(e){
    e.preventDefault();
    if (selected.size === 0) {
      showToast('warning', '{{ __("لم يتم اختيار أي عناصر") }}');
      return;
    }

    const ids = Array.from(selected);
    const size = $('#label-size').val();

    $.get("{{ route('print_barcode.index') }}", {
      op: 'preview',
      ids: ids,
      code_type: currentCodeType(),
      size: size
    })
    .done(resp => {
      if (!resp?.ok) {
        showToast('danger', resp?.message || '{{ __("حدث خطأ أثناء المعاينة") }}');
        return;
      }
      const labels = resp.labels || [];
      if (!labels.length) {
        showToast('info', '{{ __("لا توجد بيانات لعرضها") }}');
        return;
      }

      const $sheet = $('#labels-sheet').empty();
      labels.forEach(l => {
        const $label = $(`
          <div class="label">
            <div class="name"></div>
            <div class="bar-wrap"><img class="bar-img" loading="lazy"></div>
            <div class="code"></div>
          </div>
        `);
        $label.find('.name').text(l.name || '');
        $label.find('.code').text(l.code || '');
        if (l.svg) $label.find('.bar-img').attr('src', l.svg);
        else $label.find('.bar-wrap').html('<div class="text-danger small">Invalid</div>');
        $sheet.append($label);
      });

      // تطبيق المقاس الحالي
      $('#label-size').trigger('change');

      // تفعيل زر الطباعة وإظهار المودال
      $('#btn-print').prop('disabled', false);
      new bootstrap.Modal(document.getElementById('previewModal')).show();
    })
    .fail(xhr => {
      const msg = xhr?.responseJSON?.message || '{{ __("حدث خطأ أثناء المعاينة") }}';
      showToast('danger', msg);
    });
  });

  // طباعة من الزر العلوي (يفتح المودال إن لم يكن مفتوحًا)
  $('#btn-print').on('click', function(){
    const el = document.getElementById('previewModal');
    const m  = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    m.show();
    setTimeout(()=> window.print(), 200);
  });

  // طباعة من داخل المودال
  $('#modal-print').on('click', function(){
    window.print();
  });
});
</script>
@endsection
