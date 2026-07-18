@extends('layouts.master_table')

@section('title')
    {{ trans('suppliers.general_settings') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Breadcrumb -->
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">{{ trans('suppliers.general_settings') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div id="alert-container"></div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <!-- القسم العلوي: الإعدادات العامة -->
      <div class="card-header">
        <h5 class="mb-0">{{ trans('suppliers.general_settings') }}</h5>
      </div>
      <div class="card-body">
        <form id="general-settings-form">
          <div class="row">
            <div class="col-md-3">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="notify_return"
                  name="notify_return"
                  {{ optional($settings)->notify_return_from_warehouse ? 'checked' : '' }}
                >
                <label class="form-check-label" for="notify_return">
                  {{ trans('suppliers.notify_return_from_warehouse') }}
                </label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="notify_service_invoice"
                  name="notify_service_invoice"
                  {{ optional($settings)->notify_discount_on_service_invoice ? 'checked' : '' }}
                >
                <label class="form-check-label" for="notify_service_invoice">
                  {{ trans('suppliers.notify_discount_on_service_invoice') }}
                </label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="notify_supply_invoice"
                  name="notify_supply_invoice"
                  {{ optional($settings)->notify_discount_on_supply_invoice ? 'checked' : '' }}
                >
                <label class="form-check-label" for="notify_supply_invoice">
                  {{ trans('suppliers.notify_discount_on_supply_invoice') }}
                </label>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">{{ trans('suppliers.rounding_allowed_value') }}</label>
              <input
                type="number"
                step="0.01"
                class="form-control"
                id="rounding_value"
                name="rounding_value"
                value="{{ $settings->rounding_allowed_value ?? 0 }}"
              >
            </div>
          </div>
          <div class="mt-3 text-end">
            <button type="submit" class="btn btn-primary">{{ trans('general.save') }}</button>
          </div>
        </form>
      </div>

      <!-- القسم الوسطي: إضافة/تعديل إشعار -->
      <div class="card-header">
        <h5 class="mb-0">{{ trans('suppliers.notifications') }}</h5>
      </div>
      <div class="card-body">
        <form id="notification-form" class="row gy-2 gx-3 align-items-end">
          <input type="hidden" id="notification_id">
          <div class="col-md-3">
            <label class="form-label">{{ trans('suppliers.notification_label') }}</label>
            <input type="text" class="form-control" id="label">
          </div>
          <div class="col-md-2">
            <label class="form-label">{{ trans('suppliers.value') }}</label>
            <input type="number" step="0.01" class="form-control" id="value">
          </div>
          <div class="col-md-2">
            <label class="form-label">{{ trans('suppliers.value_type') }}</label>
            <select class="form-select" id="value_type">
              <option value="fixed">{{ trans('suppliers.fixed') }}</option>
              <option value="percentage">{{ trans('suppliers.percentage') }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">{{ trans('suppliers.action_type') }}</label>
            <select class="form-select" id="action_type">
              <option value="discount">{{ trans('suppliers.discount') }}</option>
              <option value="addition">{{ trans('suppliers.addition') }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">{{ trans('suppliers.category') }}</label>
            <select class="form-select" id="category">
              <option value="tax">{{ trans('suppliers.tax') }}</option>
              <option value="additional">{{ trans('suppliers.additional') }}</option>
              <option value="collection_tax">{{ trans('suppliers.collection_tax') }}</option>
              <option value="invoice_expense">{{ trans('suppliers.invoice_expense') }}</option>
              <option value="freelance_services">{{ trans('suppliers.freelance_services') }}</option>
              <option value="stamp_duty">{{ trans('suppliers.stamp_duty') }}</option>
              <option value="income_tax">{{ trans('suppliers.income_tax') }}</option>
            </select>
          </div>
          <div class="col-md-1 text-end">
            <button type="submit" class="btn btn-success">{{ trans('general.save') }}</button>
          </div>
        </form>
      </div>

      <!-- الجدول السفلي -->
      <div class="card-body">
        @include('components.modals.delete-modal')
        <table id="notifications-table" class="table table-striped table-bordered w-100">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ trans('suppliers.notification_label') }}</th>
              <th>{{ trans('suppliers.value') }}</th>
              <th>{{ trans('suppliers.value_type') }}</th>
              <th>{{ trans('suppliers.action_type') }}</th>
              <th>{{ trans('suppliers.category') }}</th>
              <th>{{ trans('general.actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

{{-- سكربت AJAX --}}
<script>
$(function(){

  // تهيئة DataTable مع ترجمة القيم
  let table = $('#notifications-table').DataTable({
    ajax: "{{ route('suppliers_general_settings.listNotifications') }}",
    columns: [
      { data: null, render: (d,t,r,i) => i.row+1 },
      { data: 'label' },
      { data: 'value' },
      { data: 'value_type', render: d =>
          d === 'fixed'
            ? "{{ trans('suppliers.fixed') }}"
            : "{{ trans('suppliers.percentage') }}"
      },
      { data: 'action_type', render: d =>
          d === 'discount'
            ? "{{ trans('suppliers.discount') }}"
            : "{{ trans('suppliers.addition') }}"
      },
      { data: 'category', render: d => {
          switch(d){
            case 'tax': return "{{ trans('suppliers.tax') }}";
            case 'additional': return "{{ trans('suppliers.additional') }}";
            case 'collection_tax': return "{{ trans('suppliers.collection_tax') }}";
            case 'invoice_expense': return "{{ trans('suppliers.invoice_expense') }}";
            case 'freelance_services': return "{{ trans('suppliers.freelance_services') }}";
            case 'stamp_duty': return "{{ trans('suppliers.stamp_duty') }}";
            case 'income_tax': return "{{ trans('suppliers.income_tax') }}";
            default: return d;
          }
        }
      },
      {
        data: 'id',
        orderable: false,
        render: id => `
          <button class="btn btn-sm btn-primary edit-notif" data-id="${id}">
            <i class="ri-edit-2-line"></i>
          </button>
          <button class="btn btn-sm btn-danger delete-notif" data-id="${id}">
            <i class="ri-delete-bin-5-line"></i>
          </button>
        `
      }
    ]
  });

  // حفظ/تحديث الإعدادات العامة مع القيمة الافتراضية 0
  $('#general-settings-form').submit(function(e){
    e.preventDefault();
    let rounding = $('#rounding_value').val();
    $.post("{{ route('suppliers_general_settings.saveGeneral') }}", {
      notify_return: $('#notify_return').is(':checked') ? 1 : 0,
      notify_service_invoice: $('#notify_service_invoice').is(':checked') ? 1 : 0,
      notify_supply_invoice: $('#notify_supply_invoice').is(':checked') ? 1 : 0,
      rounding_value: rounding !== '' ? rounding : 0,
      _token: $('meta[name="csrf-token"]').attr('content')
    }, res => showAlert('success', res.message));
  });

  // فتح نموذج تعديل إشعار
  $(document).on('click','.edit-notif', function(){
    let id = $(this).data('id');
    $.get("{{ url('suppliers_general_settings/notification') }}/"+id, notif => {
      $('#notification_id').val(notif.id);
      $('#label').val(notif.label);
      $('#value').val(notif.value);
      $('#value_type').val(notif.value_type);
      $('#action_type').val(notif.action_type);
      $('#category').val(notif.category);
      // تمرير الصفحة للنموذج
      document.getElementById('notification-form').scrollIntoView();
    });
  });

  // حفظ/تحديث إشعار
  $('#notification-form').submit(function(e){
    e.preventDefault();
    let id = $('#notification_id').val(),
        url = id
          ? "{{ url('suppliers_general_settings/notification') }}/" + id
          : "{{ route('suppliers_general_settings.saveNotification') }}",
        type = id ? 'PUT' : 'POST';

    $.ajax({
      url, type, data: {
        label: $('#label').val(),
        value: $('#value').val(),
        value_type: $('#value_type').val(),
        action_type: $('#action_type').val(),
        category: $('#category').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success(res){
        $('#notification-form')[0].reset();
        $('#notification_id').val('');
        table.ajax.reload();
        showAlert('success', res.message);
      }
    });
  });

  // حذف إشعار باستخدام موديل التأكيد المشترك
  $(document).on('click','.delete-notif', function(){
    $('#delete-id').val($(this).data('id'));
    $('#deleteModal').modal('show');
  });
  $('#delete-record').off('click').on('click', function(){
    let id = $('#delete-id').val();
    $.ajax({
      url: "{{ url('suppliers_general_settings/notification') }}/" + id,
      type: 'DELETE',
      data: { _token: $('meta[name="csrf-token"]').attr('content') },
      success(res){
        $('#deleteModal').modal('hide');
        table.ajax.reload();
        showAlert('success', res.message);
      }
    });
  });

  // دالة عرض التنبيه
  function showAlert(type, msg){
    let html = `
      <div class="alert alert-${type} alert-dismissible fade show text-center" role="alert">
        ${msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`;
    $('#alert-container').html(html);
    setTimeout(()=>$('.alert').alert('close'), 4000);
  }

});
</script>
@endsection
