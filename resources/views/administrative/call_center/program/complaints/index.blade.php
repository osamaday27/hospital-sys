@extends('layouts.master_table')

@section('title')
{{ trans('call_center_trans.complaints') }}
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
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.complaints_mangment') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- التنبيهات -->
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <input type="search" class="form-control" id="search-nursing" placeholder="{{ trans('call_center_trans.search_complaints') }}"
                            style="width: 250px;">
                    </div>
                    <div class="hstack gap-2">
                        <!-- الزر المعدل لاستدعاء المودال -->
<button type="button" class="btn btn-outline-secondary waves-effect waves-light shadow-none" data-bs-toggle="modal" data-bs-target="#selectPatientModal">
    <i class="ri-add-line"></i> {{ trans('call_center_trans.create_new_complaints') }}
</button>


                        <button id="reload-table" class="btn btn-info">
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
<!-- مودال اختيار المريض -->
<div class="modal fade" id="selectPatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ trans('call_center_trans.insert_patient_data') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- تبويبات لتقسيم الخيارات -->
          <ul class="nav nav-tabs" id="patientTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="registered-tab" data-bs-toggle="tab" data-bs-target="#registered" type="button" role="tab">
                {{ trans('call_center_trans.registered_patient') }}
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="nonRegistered-tab" data-bs-toggle="tab" data-bs-target="#nonRegistered" type="button" role="tab">
                {{ trans('call_center_trans.non_registered_patient') }}
              </button>
            </li>
          </ul>
          <div class="tab-content mt-3" id="patientTabContent">
            <!-- القسم الخاص بالمرضى المسجلين -->
            <div class="tab-pane fade show active" id="registered" role="tabpanel">
              <div id="registered-content">
                <!-- زر اختيار المريض بدلاً من تحميل المحتوى عبر AJAX -->
                <button type="button" class="btn btn-primary select-registered-patient-btn">
                  {{ trans('call_center_trans.choose_patient') }}
                </button>
              </div>
            </div>
            <!-- القسم الخاص بالمرضى غير المسجلين -->
            <div class="tab-pane fade" id="nonRegistered" role="tabpanel">
                <form id="nonRegisteredForm">
                    <div class="mb-3">
                      <label for="patientName" class="form-label">{{ trans('call_center_trans.patient_name') }}</label>
                      <input type="text" class="form-control" id="patientName" name="patientName" required>
                    </div>
                    <div class="mb-3">
                      <label for="patientAge" class="form-label">{{ trans('call_center_trans.patient_age') }}</label>
                      <input type="number" class="form-control" id="patientAge" name="patientAge" required>
                    </div>
                    <div class="mb-3">
                      <label for="patientGender" class="form-label">{{ trans('call_center_trans.patient_gender') }}</label>
                      <select class="form-control" id="patientGender" name="patientGender" required>
                        <option value="">{{ trans('call_center_trans.select_patient_gender') }}</option>
                        <option value="male">{{ trans('general.male') }}</option>
                        <option value="female">{{ trans('general.female') }}</option>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ trans('call_center_trans.save') }}</button>
                  </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


                <!-- Table Section -->
                <div class="card-body">
                    <table id="all_questionnaires-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('call_center_trans.sr_no') }}</th>
                                <th>{{ trans('call_center_trans.action') }}</th>
                                <th>{{ trans('call_center_trans.complaint_title') }}</th>
                                <th>{{ trans('call_center_trans.priority') }}</th>
                                <th>{{ trans('call_center_trans.patient_name') }}</th>
                                <th>{{ trans('call_center_trans.complaint_date') }}</th>
                                <th>{{ trans('inpatient_trans.status') }}</th>


                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                    colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                                <div class="mt-4">
                                    <h4>{{ __('call_center_trans.h4_delete') }}</h4>
                                    <p>{{ __('call_center_trans.p_delete') }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <form action="" method="post" id="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" id="id" name="id">
                                    <button type="button" class="btn btn-light"
                                        data-bs-dismiss="modal">{{ __('call_center_trans.close') }}</button>
                                    <button type="button" class="btn btn-danger"
                                        id="delete-record">{{ __('call_center_trans.yes_delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
  // عند فتح تبويب المرضى المسجلين، نقوم بإضافة زر "اختيار مريض" إذا كان المحتوى فارغاً
  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    var target = $(e.target).attr("data-bs-target");
    if(target === "#registered") {
      if($('#registered-content').is(':empty')) {
        // إنشاء زر "اختيار مريض"
        var buttonHtml = '<button type="button" class="btn btn-primary select-registered-patient-btn">{{ trans("call_center_trans.choose_patient") }}</button>';
        $('#registered-content').html(buttonHtml);
      }
    }
  });

  // عند الضغط على زر "اختيار مريض" في الجزء الخاص بالمرضى المسجلين
  $(document).on('click', '.select-registered-patient-btn', function() {
    // التوجيه إلى صفحة اختيار المريض والزيارة
    window.location.href = '/choose-patient';
  });

  // عند تقديم نموذج المرضى غير المسجلين كما هو دون تغيير
  $('#nonRegisteredForm').on('submit', function(e) {
    e.preventDefault();
    var name = $('#patientName').val();
    var age = $('#patientAge').val();
    var gender = $('#patientGender').val();
    // إغلاق المودال
    $('#selectPatientModal').modal('hide');
    // التوجيه إلى صفحة إنشاء الشكوى مع تمرير اسم وعمر المريض
    window.location.href = '/create-complaints?name=' + encodeURIComponent(name) + '&age=' + encodeURIComponent(age) + '&gender=' + encodeURIComponent(gender)+ "&registered_patient=0";
  });
});


        $(function () {
            var table = $('#all_questionnaires-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('get-complaints') }}",
                    data: function (d) { }
                },
                columns: [{
                    data: null,
                    name: 'sr_no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'priority',
                    name: 'priority'
                },

                {
                    data: 'patient_name',
                    name: 'patient_name'
                },
                {
                    data: 'complaint_date',
                    name: 'complaint_date'
                },
                {
                    data: 'status',
                    name: 'status'
                },

                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'csv',
                    title: 'questionnaires Data',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-outline-primary'
                },
                {
                    extend: 'excel',
                    title: 'questionnaires Data',
                    text: '<i class="fas fa-file-excel"></i> XLSX',
                    className: 'btn btn-outline-success'
                }
                ],
                language: {
                    lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                    info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                    processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
                    paginate: {
                        "previous": "{{ __('datatables.previous') }}",
                        "next": "{{ __('datatables.next') }}"
                    }
                }
            });

            $('#reload-table').click(function () {
                table.ajax.reload();
            });

            $('#search-nursing').on('input', function () {
                table.search(this.value).draw();
            });

            $('#export-csv').click(function () {
                table.button('.buttons-csv').trigger();
            });
            $('#export-xlsx').click(function () {
                table.button('.buttons-excel').trigger();
            });
        });

        function deleteFunction(id) {
            $('#id').val(id);
            $('#deleteModal').modal('show');
        }


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
            }, 15000);
        }
        function deleteFunction(encryptedId, id) {
        // وضع القيمة (المشفرة) في الحقل المخفي داخل المودال
        $('#delete-id').val(encryptedId);
        // تحديث مسار الفورم باستخدام الـ route مع المعرف المشفر
        var actionUrl = "{{ route('complaints.destroy', ['complaint' => ':id']) }}";
        actionUrl = actionUrl.replace(':id', encryptedId);
        $('#delete-form').attr('action', actionUrl);
        // عرض المودال
        $('#deleteModal').modal('show');
    }

    // عند الضغط على زر "نعم، احذف" داخل المودال، يتم إرسال الفورم
    $('#delete-record').on('click', function() {
        $('#delete-form').submit();
    });
    </script>
@endsection
