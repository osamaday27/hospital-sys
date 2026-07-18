  <!-- Button Section -->

  <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                      <input type="search" class="form-control" id="search-level"
                          placeholder="{{ trans('outpatient_trans.search') }}" style="width: 250px;">
                  </div>
                  <div class="hstack gap-2">
                      <a href="{{ route('outpatient_settings.create') }}"
                          class="btn btn-outline-secondary waves-effect waves-light shadow-none">
                          <i class="ri-add-line"></i> {{ trans('outpatient_trans.create_clinc') }}
                      </a>
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

              <!-- Table Section -->
              <div class="card-body">
                  <table id="clinics-table" class="table dt-responsive nowrap align-middle"
                      style="width: 100%; border: aliceblue;">
                      <thead>
                          <tr>
                              <th>{{ trans('outpatient_trans.sr_no') }}</th>
                              <th>{{ trans('outpatient_trans.action') }}</th>
                              <th>{{ trans('outpatient_trans.logo') }}</th>
                              <th>{{ trans('outpatient_trans.name_ar') }}</th>
                              <th>{{ trans('outpatient_trans.name_en') }}</th>
                              <th>{{ trans('outpatient_trans.clinc_code') }}</th>
                              <th>{{ trans('outpatient_trans.specialty') }}</th>
                              <th>{{ trans('outpatient_trans.status') }}</th>
                              <th>{{ trans('outpatient_trans.created_at') }}</th>
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
                                  colors="primary:#f7b84b,secondary:#f06548"
                                  style="width:100px;height:100px"></lord-icon>
                              <div class="mt-4">
                                  <h4>{{ __('outpatient_trans.h4_delete') }}</h4>
                                  <p>{{ __('outpatient_trans.p_delete') }}</p>
                              </div>
                          </div>
                          <div class="d-flex gap-2 justify-content-center mt-4">
                              <form action="" method="post" id="delete-form">
                                  @csrf
                                  @method('DELETE')
                                  <input type="hidden" id="id" name="id">
                                  <button type="button" class="btn btn-light"
                                      data-bs-dismiss="modal">{{ __('outpatient_trans.close') }}</button>
                                  <button type="button" class="btn btn-danger"
                                      id="delete-record">{{ __('outpatient_trans.yes_delete') }}</button>
                              </form>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <script>
      $(function() {
          var table = $('#clinics-table').DataTable({
              processing: true,
              serverSide: true,
              ajax: {
                  url: "{{ url('get-clinics') }}",
                  data: function(d) {}
              },
              columns: [{
                      data: 'id',
                      name: 'id'
                  },
                  {
                      data: 'action',
                      name: 'action',
                      orderable: false,
                      searchable: false
                  },
                  {
                      data: 'logo',
                      name: 'logo'
                  },
                  {
                      data: 'name_ar',
                      name: 'name_ar'
                  },
                  {
                      data: 'name_en',
                      name: 'name_en'
                  },
                  {
                      data: 'clinic_code',
                      name: 'clinic_code'
                  },
                  {
                      data: 'specialty',
                      name: 'specialty'
                  },

                  {
                      data: 'status',
                      name: 'status'
                  },
                  {
                      data: 'created_at',
                      name: 'created_at'
                  }
              ],
              dom: 'Bfrtip',
              buttons: [{
                      extend: 'csv',
                      title: 'service Data',
                      text: '<i class="fas fa-file-csv"></i> CSV',
                      className: 'btn btn-outline-primary'
                  },
                  {
                      extend: 'excel',
                      title: 'service Data',
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

          $('#reload-table').click(function() {
              table.ajax.reload();
          });

          $('#search-level').on('input', function() {
              table.search(this.value).draw();
          });

          $('#export-pdf').click(function() {
              table.button('.buttons-pdf').trigger();
          });
          $('#export-csv').click(function() {
              table.button('.buttons-csv').trigger();
          });
          $('#export-xlsx').click(function() {
              table.button('.buttons-excel').trigger();
          });
          $('#export-print').click(function() {
              table.button('.buttons-print').trigger();
          });
      });

      function deleteFunction(id) {
          $('#id').val(id); // وضع المعرف في حقل الإدخال المخفي
          $('#deleteModal').modal('show'); // فتح النافذة المنبثقة
      }

      $('#delete-record').click(function() {
          var id = $('#id').val(); // الحصول على ID من الحقل المخفي
          var url = '{{ route('outpatient_settings.destroy', ':id') }}';
          url = url.replace(':id', id); // استبدال الـ :id بالـ ID الفعلي

          $.ajax({
              url: url,
              type: 'DELETE', // استخدام الطريقة DELETE مباشرة
              data: {
                  "_token": "{{ csrf_token() }}", // توكن CSRF للأمان
              },
              success: function(response) {
                  $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة
                  $('#clinics-table').DataTable().ajax
                      .reload(); // إعادة تحميل الـ DataTable
                  // عرض الرسالة من الـ Controller
                  if (response.status == 'success') {
                      showAlert('success', response.message); // عرض رسالة النجاح
                  } else {
                      showAlert('error', response.message); // عرض رسالة الخطأ
                  }
              },
              error: function(xhr, status, error) {
                  console.log(xhr.responseText);
                  alert("There was an error deleting the record.");
              }
          });
      });
      // دالة لعرض الرسائل بشكل مخصص
      function showAlert(type, message) {
          // تحديد نوع التنبيه
          var alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';

          // إنشاء رسالة التنبيه
          var alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show shadow" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                    <i class="ri-${type === 'success' ? 'check-line' : 'alert-line'} align-middle"></i>
                </div>
                <div class="flex-grow-1">
                    ${message}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    `;

          // إضافة الرسالة إلى الحاوية
          $('#alert-container').append(alertHtml);
          $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة

          // إخفاء الرسالة تلقائيًا بعد 5 ثوانٍ
          setTimeout(function() {
              $('#alert-container .alert').first().alert('close');
          }, 15000);
      }
  </script>
