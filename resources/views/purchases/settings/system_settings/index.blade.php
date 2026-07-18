{{-- resources/views/purchases/settings/system_settings/index.blade.php --}}
@extends('layouts.master_table')

@section('title')
    {{ trans('purchases.settings') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Breadcrumb -->
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">{{ trans('purchases.settings') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div id="alert-container"></div>

{{-- ===== الإعدادات العُليا ===== --}}
<div class="row mb-4">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">{{ trans('purchases.settings') }}</h5>
      </div>
      <div class="card-body">
        <form id="purchase-settings-form" class="row gy-3 gx-3 align-items-end">
          <div class="col-md-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="has_max_limit" name="has_max_limit"
                {{ optional($settings)->has_max_limit ? 'checked' : '' }}>
              <label class="form-check-label" for="has_max_limit">
                {{ trans('purchases.has_max_limit') }}
              </label>
            </div>
          </div>
          <div class="col-md-4" id="max_limit_container"
            style="{{ optional($settings)->has_max_limit ? '' : 'display:none' }}">
            <label class="form-label" for="max_limit_value">{{ trans('purchases.max_limit_value') }}</label>
            <input type="number" step="0.01" class="form-control" id="max_limit_value"
              name="max_limit_value" value="{{ $settings->max_limit_value ?? '' }}">
          </div>

          <div class="col-12">
            <label class="form-label">{{ trans('purchases.cost_calculation_from') }}</label>
          </div>
          <div class="col-md-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="use_last_supplier_price"
                name="use_last_supplier_price"
                {{ optional($settings)->use_last_supplier_price ? 'checked' : '' }}>
              <label class="form-check-label" for="use_last_supplier_price">
                {{ trans('purchases.use_last_supplier_price') }}
              </label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="use_sale_price" name="use_sale_price"
                {{ optional($settings)->use_sale_price ? 'checked' : '' }}>
              <label class="form-check-label" for="use_sale_price">
                {{ trans('purchases.use_sale_price') }}
              </label>
            </div>
          </div>

          <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">{{ trans('general.save') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ===== التبويبات الثلاث ===== --}}
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <ul class="nav nav-pills card-header-pills" id="system-settings-tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-eval-items" data-bs-toggle="pill"
              data-bs-target="#pane-eval-items" type="button" role="tab">
              {{ trans('purchases.evaluation_items') }}
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-delivery-places" data-bs-toggle="pill"
              data-bs-target="#pane-delivery-places" type="button" role="tab">
              {{ trans('purchases.delivery_places') }}
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-closure-reasons" data-bs-toggle="pill"
              data-bs-target="#pane-closure-reasons" type="button" role="tab">
              {{ trans('purchases.closure_reasons') }}
            </button>
          </li>
        </ul>
      </div>
      <div class="tab-content">

        {{-- بنود التقييم --}}
        <div class="tab-pane fade show active" id="pane-eval-items" role="tabpanel">
          <div class="card-body">
            <form id="evaluation-item-form" class="row g-3 align-items-end">
              <input type="hidden" id="evaluation_id">
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.name_ar') }}</label>
                <input type="text" id="evaluation_name_ar" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.name_en') }}</label>
                <input type="text" id="evaluation_name_en" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.status') }}</label>
                <select id="evaluation_status" class="form-select">
                  <option value="active">{{ trans('purchases.status_active') }}</option>
                  <option value="inactive">{{ trans('purchases.status_inactive') }}</option>
                </select>
              </div>
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">{{ trans('general.save') }}</button>
              </div>
            </form>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2 align-items-center">
              <div class="input-group w-auto">
                <button id="reload-evaluation" class="btn btn-info btn-sm me-2">
                  {{ trans('purchases.reload') }}
                </button>
                <input type="search" id="search-evaluation" class="form-control form-control-sm"
                  placeholder="{{ trans('purchases.search') }}">
              </div>
              <div class="btn-group btn-group-sm">
                <button id="export-evaluation-csv" class="btn btn-outline-secondary">
                  <i class="fa fa-file-csv"></i> CSV
                </button>
                <button id="export-evaluation-xlsx" class="btn btn-outline-secondary">
                  <i class="fas fa-file-excel"></i> XLSX
                </button>
              </div>
            </div>
            <table id="evaluation-items-table" class="table table-bordered w-100">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{ trans('purchases.name_ar') }}</th>
                  <th>{{ trans('purchases.name_en') }}</th>
                  <th>{{ trans('purchases.status') }}</th>
                  <th>{{ trans('general.actions') }}</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

        {{-- أماكن التسليم --}}
        <div class="tab-pane fade" id="pane-delivery-places" role="tabpanel">
          <div class="card-body">
            <form id="delivery-place-form" class="row g-3 align-items-end">
              <input type="hidden" id="delivery_id">
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.name_ar') }}</label>
                <input type="text" id="delivery_name_ar" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.name_en') }}</label>
                <input type="text" id="delivery_name_en" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.status') }}</label>
                <select id="delivery_status" class="form-select">
                  <option value="active">{{ trans('purchases.status_active') }}</option>
                  <option value="inactive">{{ trans('purchases.status_inactive') }}</option>
                </select>
              </div>
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">{{ trans('general.save') }}</button>
              </div>
            </form>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2 align-items-center">
              <div class="input-group w-auto">
                <button id="reload-delivery" class="btn btn-info btn-sm me-2">
                  {{ trans('purchases.reload') }}
                </button>
                <input type="search" id="search-delivery" class="form-control form-control-sm"
                  placeholder="{{ trans('purchases.search') }}">
              </div>
              <div class="btn-group btn-group-sm">
                <button id="export-delivery-csv" class="btn btn-outline-secondary">
                  <i class="fa fa-file-csv"></i> CSV
                </button>
                <button id="export-delivery-xlsx" class="btn btn-outline-secondary">
                  <i class="fas fa-file-excel"></i> XLSX
                </button>
              </div>
            </div>
            <table id="delivery-places-table" class="table table-bordered w-100">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{ trans('purchases.name_ar') }}</th>
                  <th>{{ trans('purchases.name_en') }}</th>
                  <th>{{ trans('purchases.status') }}</th>
                  <th>{{ trans('general.actions') }}</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

        {{-- أسباب الإغلاق --}}
        <div class="tab-pane fade" id="pane-closure-reasons" role="tabpanel">
          <div class="card-body">
            <form id="closure-reason-form" class="row g-3 align-items-end">
              <input type="hidden" id="closure_id">
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.name_ar') }}</label>
                <input type="text" id="closure_name_ar" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.name_en') }}</label>
                <input type="text" id="closure_name_en" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('purchases.status') }}</label>
                <select id="closure_status" class="form-select">
                  <option value="active">{{ trans('purchases.status_active') }}</option>
                  <option value="inactive">{{ trans('purchases.status_inactive') }}</option>
                </select>
              </div>
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">{{ trans('general.save') }}</button>
              </div>
            </form>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2 align-items-center">
              <div class="input-group w-auto">
                <button id="reload-closure" class="btn btn-info btn-sm me-2">
                  {{ trans('purchases.reload') }}
                </button>
                <input type="search" id="search-closure" class="form-control form-control-sm"
                  placeholder="{{ trans('purchases.search') }}">
              </div>
              <div class="btn-group btn-group-sm">
                <button id="export-closure-csv" class="btn btn-outline-secondary">
                  <i class="fa fa-file-csv"></i> CSV
                </button>
                <button id="export-closure-xlsx" class="btn btn-outline-secondary">
                  <i class="fas fa-file-excel"></i> XLSX
                </button>
              </div>
            </div>
            <table id="closure-reasons-table" class="table table-bordered w-100">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{ trans('purchases.name_ar') }}</th>
                  <th>{{ trans('purchases.name_en') }}</th>
                  <th>{{ trans('purchases.status') }}</th>
                  <th>{{ trans('general.actions') }}</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@include('components.modals.delete-modal')

<script>
$(function(){
  // Toggle + AJAX Save الأعلى
  $('#has_max_limit').on('change', function(){
    const c = this.checked;
    $('#max_limit_container').toggle(c);
    if (c) {
      $('#max_limit_value').attr('required', true);
    } else {
      $('#max_limit_value').removeAttr('required').val('');
    }
  });

  $('#purchase-settings-form').submit(function(e){
    e.preventDefault();
    if ($('#has_max_limit').is(':checked') && !$('#max_limit_value').val()) {
      return showAlert('danger','{{ trans("purchases.max_limit_value_required") }}');
    }
    $.post(
      "{{ route('purchases_system_settings.saveSettings') }}",
      {
        has_max_limit: +$('#has_max_limit').is(':checked'),
        max_limit_value: $('#max_limit_value').val() || null,
        use_last_supplier_price: +$('#use_last_supplier_price').is(':checked'),
        use_sale_price: +$('#use_sale_price').is(':checked'),
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      res => showAlert('success', res.message)
    );
  });

  function showAlert(type, msg){
    $('#alert-container').html(`
      <div class="alert alert-${type} alert-dismissible fade show text-center" role="alert">
        ${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`);
    setTimeout(()=>$('.alert').alert('close'),4000);
  }

  // CRUD helpers
  const routes = {
    evalList:    "{{ route('purchases_system_settings.evaluationItemsList') }}",
    evalStore:   "{{ route('purchases_system_settings.evaluationItemsStore') }}",
    evalShow:    id => "{{ route('purchases_system_settings.evaluationItemsShow',['id'=>'__ID__']) }}".replace('__ID__', id),
    evalUpdate:  id => "{{ route('purchases_system_settings.evaluationItemsUpdate',['id'=>'__ID__']) }}".replace('__ID__', id),
    evalDestroy: id => "{{ route('purchases_system_settings.evaluationItemsDestroy',['id'=>'__ID__']) }}".replace('__ID__', id),

    delList:     "{{ route('purchases_system_settings.deliveryPlacesList') }}",
    delStore:    "{{ route('purchases_system_settings.deliveryPlacesStore') }}",
    delShow:     id => "{{ route('purchases_system_settings.deliveryPlacesShow',['id'=>'__ID__']) }}".replace('__ID__', id),
    delUpdate:   id => "{{ route('purchases_system_settings.deliveryPlacesUpdate',['id'=>'__ID__']) }}".replace('__ID__', id),
    delDestroy:  id => "{{ route('purchases_system_settings.deliveryPlacesDestroy',['id'=>'__ID__']) }}".replace('__ID__', id),

    closeList:    "{{ route('purchases_system_settings.closureReasonsList') }}",
    closeStore:   "{{ route('purchases_system_settings.closureReasonsStore') }}",
    closeShow:    id => "{{ route('purchases_system_settings.closureReasonsShow',['id'=>'__ID__']) }}".replace('__ID__', id),
    closeUpdate:  id => "{{ route('purchases_system_settings.closureReasonsUpdate',['id'=>'__ID__']) }}".replace('__ID__', id),
    closeDestroy: id => "{{ route('purchases_system_settings.closureReasonsDestroy',['id'=>'__ID__']) }}".replace('__ID__', id),
  };

  function setupCrud({ form, table, fields, urls, reloadBtn, searchInput, csvBtn, xlsxBtn }) {
    const $form   = $(form),
          $submit = $form.find('button[type=submit]');

    const dt = $(table).DataTable({
      dom: 'Bfrtip',
      buttons: [
        { extend: 'csv',   className: 'buttons-csv',   text: '' },
        { extend: 'excel', className: 'buttons-excel', text: '' }
      ],
      ajax: urls.list,
      columns: [
        { data: null, render: (_,__,___,idx) => idx.row + 1 },
        // عرض باقي الحقول عدا الحالة
        ...fields.filter(f => f.key !== 'status').map(f => ({ data: f.key })),
        // عرض الحالة بالترجمة
        {
          data: 'status',
          render: d => d === 'active'
            ? '{{ trans("purchases.status_active") }}'
            : '{{ trans("purchases.status_inactive") }}'
        },
        // أزرار تعديل وحذف
        {
          data: 'id',
          orderable: false,
          render: id => `
            <button class="btn btn-sm btn-primary edit-btn" data-id="${id}">{{ trans('general.edit') }}</button>
            <button class="btn btn-sm btn-danger delete-btn" data-id="${id}">{{ trans('general.delete') }}</button>`
        }
      ]
    });

    // إعادة تحميل، بحث، تصدير
    $(reloadBtn).click(() => dt.ajax.reload());
    $(searchInput).on('input', function(){ dt.search(this.value).draw(); });
    $(csvBtn).click(() => dt.button('.buttons-csv').trigger());
    $(xlsxBtn).click(() => dt.button('.buttons-excel').trigger());

    // إنشاء/تعديل
    $form.submit(function(e){
      e.preventDefault();
      const id  = $form.find('[type=hidden]').val(),
            url = id ? urls.update(id) : urls.store,
            data = { _token: $('meta[name="csrf-token"]').attr('content'), _method: id ? 'PUT' : 'POST' };
      fields.forEach(f => data[f.key] = $form.find('#'+f.input).val());
      $.post(url, data, () => {
        dt.ajax.reload();
        $form[0].reset();
        $form.find('[type=hidden]').val('');
        $submit.text('{{ trans("general.save") }}');
        showAlert('success','{{ trans("general.saved_successfully") }}');
      });
    });

    // تعبئة نموذج التعديل
    $(document).on('click', `${table} .edit-btn`, function(){
      const id = $(this).data('id');
      $.get(urls.show(id), item => {
        $form.find('[type=hidden]').val(item.id);
        fields.forEach(f => $form.find('#'+f.input).val(item[f.key]));
        $submit.text('{{ trans("general.update") }}');
      });
    });

    // فتح مودال الحذف
    $(document).on('click', `${table} .delete-btn`, function(){
      const id = $(this).data('id');
      currentDeletion = { url: urls.destroy(id), table: dt };
      $('#delete-id').val(id);
      $('#deleteModal').modal('show');
    });
  }

  let currentDeletion = {};

  // تهيئة الجداول الثلاث
  setupCrud({
    form:'#evaluation-item-form', table:'#evaluation-items-table',
    fields:[
      { key:'name_ar', input:'evaluation_name_ar' },
      { key:'name_en', input:'evaluation_name_en' },
      { key:'status',  input:'evaluation_status'   }
    ],
    urls:{
      list:    routes.evalList,
      store:   routes.evalStore,
      show:    routes.evalShow,
      update:  routes.evalUpdate,
      destroy: routes.evalDestroy
    },
    reloadBtn:    '#reload-evaluation',
    searchInput:  '#search-evaluation',
    csvBtn:       '#export-evaluation-csv',
    xlsxBtn:      '#export-evaluation-xlsx'
  });

  setupCrud({
    form:'#delivery-place-form', table:'#delivery-places-table',
    fields:[
      { key:'name_ar', input:'delivery_name_ar' },
      { key:'name_en', input:'delivery_name_en' },
      { key:'status',  input:'delivery_status'    }
    ],
    urls:{
      list:    routes.delList,
      store:   routes.delStore,
      show:    routes.delShow,
      update:  routes.delUpdate,
      destroy: routes.delDestroy
    },
    reloadBtn:   '#reload-delivery',
    searchInput: '#search-delivery',
    csvBtn:      '#export-delivery-csv',
    xlsxBtn:     '#export-delivery-xlsx'
  });

  setupCrud({
    form:'#closure-reason-form', table:'#closure-reasons-table',
    fields:[
      { key:'name_ar', input:'closure_name_ar' },
      { key:'name_en', input:'closure_name_en' },
      { key:'status',  input:'closure_status'     }
    ],
    urls:{
      list:    routes.closeList,
      store:   routes.closeStore,
      show:    routes.closeShow,
      update:  routes.closeUpdate,
      destroy: routes.closeDestroy
    },
    reloadBtn:   '#reload-closure',
    searchInput: '#search-closure',
    csvBtn:      '#export-closure-csv',
    xlsxBtn:     '#export-closure-xlsx'
  });

  // تأكيد الحذف
  $('#delete-record').click(function(){
    $.ajax({
      url: currentDeletion.url,
      type: 'DELETE',
      data: { _token: $('meta[name="csrf-token"]').attr('content') },
      success(res){
        $('#deleteModal').modal('hide');
        currentDeletion.table.ajax.reload();
        showAlert('success', res.message);
      }
    });
  });

});
</script>

@endsection