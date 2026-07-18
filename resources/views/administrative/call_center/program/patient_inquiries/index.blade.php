@extends('layouts.master_table')

@section('title')
  {{ trans('call_center_trans.patient_inquiries') }}
@stop

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0 font">{{ trans('call_center_trans.title') }}</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('call_center_settings.index') }}">{{ trans('call_center_trans.title') }}</a>
          </li>
          <li class="breadcrumb-item active">{{ trans('call_center_trans.patient_inquiries_mangment') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- التنبيهات -->
<div class="row">
  <div class="col-12">
    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if (session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif
  </div>
</div>

<!-- Buttons / Search / Filters -->
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <input type="search" class="form-control" id="search-inquiries"
                 placeholder="{{ trans('call_center_trans.search_inquiries') }}"
                 style="width: 250px;">
          <select id="status-filter" class="form-select" style="min-width: 180px;">
            <option value="">{{ trans('call_center_trans.all_statuses') }}</option>
            <option value="new">{{ trans('call_center_trans.status_new') }}</option>
            <option value="reviewed">{{ trans('call_center_trans.status_reviewed') }}</option>
          </select>
        </div>
        <div class="hstack gap-2">
          <!-- زر إنشاء استفسار جديد (مودال) -->
          <button type="button" class="btn btn-outline-secondary shadow-none"
                  data-bs-toggle="modal" data-bs-target="#selectPatientModal">
            <i class="ri-add-line"></i> {{ trans('call_center_trans.create_new_inquiry') }}
          </button>
          <button id="reload-table" class="btn btn-info" title="{{ __('datatables.Loading') }}">
            <i class="ri-repeat-2-line"></i>
          </button>
          <button class="btn btn-primary" id="export-csv">
            <i class="fa fa-file-csv"></i> CSV
          </button>
          <button class="btn btn-success" id="export-xlsx">
            <i class="fas fa-file-excel"></i> XLSX
          </button>
        </div>
      </div>

      <div id="alert-container"></div>

      <!-- مودال اختيار (مسجّل/غير مسجّل) -->
      <div class="modal fade" id="selectPatientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ trans('call_center_trans.insert_patient_data') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <ul class="nav nav-tabs" id="patientTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="registered-tab" data-bs-toggle="tab"
                          data-bs-target="#registered" type="button" role="tab">
                    {{ trans('call_center_trans.registered_patient_inquiry') }}
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="nonRegistered-tab" data-bs-toggle="tab"
                          data-bs-target="#nonRegistered" type="button" role="tab">
                    {{ trans('call_center_trans.non_registered_patient_inquiry') }}
                  </button>
                </li>
              </ul>

              <div class="tab-content mt-3" id="patientTabContent">
                <!-- مسجّل -->
                <div class="tab-pane fade show active" id="registered" role="tabpanel">
                  <label class="form-label">{{ trans('call_center_trans.search_patient') }}</label>
                  <select id="patient_select" class="form-control" style="width:100%"></select>

                  <div class="mt-3">
                    <label class="form-label">{{ trans('call_center_trans.inquiry_date') }}</label>
                    <input type="date" id="prefill_date_reg" class="form-control" value="{{ now()->format('Y-m-d') }}">
                  </div>

                  <div class="mt-3 text-end">
                    <button class="btn btn-primary" id="open_create_registered">
                      {{ trans('call_center_trans.open_create') }}
                    </button>
                  </div>
                </div>

                <!-- غير مسجّل -->
                <div class="tab-pane fade" id="nonRegistered" role="tabpanel">
                  <div class="mb-3">
                    <label class="form-label">{{ trans('call_center_trans.patient_name') }}</label>
                    <input type="text" id="patientName" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">{{ trans('call_center_trans.patient_gender') }}</label>
                    <select id="patientGender" class="form-control" required>
                      <option value="">{{ trans('call_center_trans.select_patient_gender') }}</option>
                      <option value="male">{{ trans('general.male') }}</option>
                      <option value="female">{{ trans('general.female') }}</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">{{ trans('call_center_trans.patient_age') }}</label>
                    <input type="number" id="patientAge" class="form-control" required min="0" max="120">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">{{ trans('call_center_trans.inquiry_date') }}</label>
                    <input type="date" id="prefill_date_unreg" class="form-control" value="{{ now()->format('Y-m-d') }}">
                  </div>
                  <div class="text-end">
                    <button class="btn btn-primary" id="open_create_unregistered">
                      {{ trans('call_center_trans.open_create') }}
                    </button>
                  </div>
                </div>
              </div>
            </div> <!-- /modal-body -->
          </div>
        </div>
      </div>

      <!-- جدول DataTables -->
      <div class="card-body">
        <table id="inquiries-table" class="table dt-responsive nowrap align-middle" style="width:100%; border: aliceblue;">
          <thead>
            <tr>
              <th>{{ trans('call_center_trans.sr_no') }}</th>
              <th>{{ trans('call_center_trans.action') }}</th>
              <th>{{ trans('call_center_trans.patient_full_name') }}</th>
              <th>{{ trans('call_center_trans.inquiry_date') }}</th>
              <th>{{ trans('call_center_trans.status') }}</th>
              <th>{{ trans('call_center_trans.created_by') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- مودال تأكيد الحذف -->
      <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('call_center_trans.confirm_delete_title') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="text-center">
                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                  colors="primary:#f7b84b,secondary:#f06548"
                  style="width:100px;height:100px"></lord-icon>
                <div class="mt-3">
                  <p class="mb-0">{{ __('call_center_trans.confirm_delete_text') }}</p>
                </div>
              </div>
            </div>
            <div class="modal-footer justify-content-center">
              <form action="" method="POST" id="delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                  {{ __('call_center_trans.close') }}
                </button>
                <button type="button" class="btn btn-danger" id="delete-record">
                  {{ __('call_center_trans.yes_delete') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- مودال التفاصيل -->
      <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ trans('general.details') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsContainer">
              <div class="text-center text-muted">{{ trans('general.loading') }}...</div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- /card -->
  </div>
</div>


<script>
$(function () {
  // لمنع تنبيه DataTables الافتراضي وإظهار تنبيه مخصص
  $.fn.dataTable.ext.errMode = 'none';

  // Select2 للمرضى داخل المودال
  $('#patient_select').select2({
    dropdownParent: $('#selectPatientModal'),
    placeholder: "{{ trans('call_center_trans.search_patient') }}",
    allowClear: true,
    ajax: {
      url: "{{ route('patient_inquiries.ajax.patients') }}",
      data: params => ({ q: params.term }),
      processResults: data => data
    }
  });

  // فتح create مع مريض مسجّل
  $('#open_create_registered').on('click', function(){
    const pid = $('#patient_select').val();
    if (!pid) { alert("{{ trans('call_center_trans.select_patient_first') }}"); return; }
    const date = $('#prefill_date_reg').val() || '';
    let url = new URL("{{ route('patient_inquiries.create') }}", window.location.origin);
    url.searchParams.set('registered','1');
    url.searchParams.set('patient_id', pid);
    url.searchParams.set('inquiry_date', date);
    window.location.href = url.toString();
  });

  // فتح create مع بيانات غير مسجّل
  $('#open_create_unregistered').on('click', function(){
    const n = $('#patientName').val().trim();
    const g = $('#patientGender').val();
    const a = $('#patientAge').val();
    const d = $('#prefill_date_unreg').val() || '';
    if (!n || !g || !a) { alert("{{ trans('general.fill_required_fields') }}"); return; }
    let url = new URL("{{ route('patient_inquiries.create') }}", window.location.origin);
    url.searchParams.set('registered','0');
    url.searchParams.set('name', n);
    url.searchParams.set('gender', g);
    url.searchParams.set('age', a);
    url.searchParams.set('inquiry_date', d);
    window.location.href = url.toString();
  });

  // DataTable
  var table = $('#inquiries-table').DataTable({
    processing: true,
    serverSide: true,
    deferRender: true,
    pageLength: 25,
    ajax: {
      url: "{{ route('patient_inquiries.datatable') }}",
      data: function (d) {
        d.status = $('#status-filter').val();
      }
    },
    columns: [
      { data: 'sr_no',        name: 'sr_no', orderable:false, searchable:false },
      { data: 'action',       name: 'action', orderable:false, searchable:false },
      { data: 'patient_name', name: 'patient_name' },
      { data: 'inquiry_date', name: 'inquiry_date' },
      { data: 'status',       name: 'status', orderable:true, searchable:false },
      { data: 'created_by',   name: 'created_by' },
    ],
    // الترتيب الافتراضي على تاريخ الاستفسار (عمود 3)
    order: [[3,'desc']],
    dom: 'Bfrtip',
    buttons: [
      { extend: 'csv',  title: 'patient_inquiries', text: '<i class="fas fa-file-csv"></i> CSV',  className: 'btn btn-outline-primary' },
      { extend: 'excel',title: 'patient_inquiries', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
    ],
    language: {
      lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
      info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
      processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
      paginate: { previous: "{{ __('datatables.previous') }}", next: "{{ __('datatables.next') }}" },
      search: "{{ __('datatables.search') }}"
    }
  });

  // مُعالج أخطاء DataTables لعرض سبب الخطأ بدلاً من التحذير العام
  $('#inquiries-table').on('error.dt', function(e, settings, techNote, message){
    console.error('DataTables error:', message);
    // لو السيرفر رجّع HTML أو رسالة خطأ، اعرض تنبيه واضح
    showAlert('danger', 'حدث خطأ في تحميل البيانات. تفقد السجلات أو أعد تحميل الصفحة.');
  });

  $('#reload-table').click(function(){ table.ajax.reload(null,false); });
  $('#search-inquiries').on('input', function(){ table.search(this.value).draw(); });
  $('#export-csv').click(function(){ table.button('.buttons-csv').trigger(); });
  $('#export-xlsx').click(function(){ table.button('.buttons-excel').trigger(); });
  $('#status-filter').on('change', function(){ table.ajax.reload(); });
});

// أكشن حذف
function deleteInquiry(id){
  var actionUrl = "{{ route('patient_inquiries.destroy', ':id') }}";
  actionUrl = actionUrl.replace(':id', id);
  $('#delete-form').attr('action', actionUrl);
  $('#deleteModal').modal('show');
}

// مودال التفاصيل
function openDetailsModalById(id){
  $('#detailsContainer').html('<div class="text-center text-muted">{{ trans("general.loading") }}...</div>');
  $('#detailsModal').modal('show');
  $.get("{{ route('patient_inquiries.modal', ':id') }}".replace(':id', id))
    .done(function(html){
      $('#detailsContainer').html(html);
    })
    .fail(function(xhr){
      console.error('Details load failed:', xhr.responseText);
      $('#detailsContainer').html('<div class="alert alert-danger m-0">{{ __("general.are_you_sure") }}</div>');
    });
}

// إرسال حذف
$('#delete-record').on('click', function(){
  $('#delete-form').submit();
});

// Helper لاظهار تنبيه
function showAlert(type, message) {
  var alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';
  var alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show shadow" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-2">
                <i class="ri-${type === 'success' ? 'check-line' : 'alert-line'} align-middle"></i>
            </div>
            <div class="flex-grow-1">
                ${message}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>`;
  $('#alert-container').append(alertHtml);
  setTimeout(function () {
      $('#alert-container .alert').first().alert('close');
  }, 8000);
}
</script>
@endsection
