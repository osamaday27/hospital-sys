  <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                  <div class="d-flex flex-wrap align-items-center">
                      <!-- Search Input -->
                      <div class="mb-3 me-3">
                          <label for="search-future_reservations"
                              class="form-label">{{ trans('outpatient_trans.search') }}</label>
                          <input type="search" class="form-control" id="search-future_reservations"
                              placeholder="{{ trans('outpatient_trans.search') }}" style="min-width: 170px;">
                      </div>
                      <!-- Clinic Dropdown -->
                      <div class="mb-3 me-3" style="min-width: 170px;">
                          <label for="clinc_id_future_reservations"
                              class="form-label">{{ trans('outpatient_trans.choose_clinc') }}</label>
                          <select class="js-example-basic-single form-select" name="clinc_id_future_reservations"
                              id="clinc_id_future_reservations" onchange="getDoctors2();" style="min-width: 170px;">
                              <option selected disabled> {{ trans('outpatient_trans.choose_clinc') }}</option>
                              @foreach ($clinic as $data)
                                  <option value="{{ $data->id }}"> {{ $data->name_ar }}</option>
                              @endforeach
                          </select>
                      </div>

                      <!-- Doctor Dropdown -->
                      <div class="mb-3 me-3" style="min-width: 170px;">
                          <label for="id_doctor_future_reservations"
                              class="form-label">{{ trans('outpatient_trans.select_doctor') }}</label>
                          <select class="js-example-basic-single form-select" name="id_doctor_future_reservations"
                              id="id_doctor_future_reservations" style="min-width: 170px;">
                              <option selected disabled> {{ trans('outpatient_trans.select_doctor') }}</option>
                          </select>
                      </div>


                      <!-- Date From -->
                      <div class="mb-3 me-3">
                          <label for="date_from" class="form-label">{{ trans('outpatient_trans.from_date') }}</label>
                          <input type="date" class="form-control" id="date_from"
                              placeholder="{{ trans('outpatient_trans.search') }}" style="min-width: 170px;">
                      </div>

                      <!-- Date To -->
                      <div class="mb-3 me-3">
                          <label for="date_to" class="form-label">{{ trans('outpatient_trans.to_date') }}</label>
                          <input type="date" class="form-control" id="date_to"
                              placeholder="{{ trans('outpatient_trans.search') }}" style="min-width: 170px;">
                      </div>
                  </div>

                  <!-- Reload Button -->
                  <div class="hstack gap-2 mt-3 mt-md-0">
                      <button id="search-future_table" class="btn btn-primary btn-label waves-effect waves-light">
                          <i class="ri-search-2-line label-icon align-middle fs-16 me-2"></i>
                          {{ trans('outpatient_trans.search') }}
                      </button>
                      <button id="reload-future_table" class="btn btn-info btn-label waves-effect waves-light">
                          <i class="ri-repeat-2-line label-icon align-middle fs-16 me-2"></i>
                          {{ trans('outpatient_trans.update_reservations') }}
                      </button>

                  </div>
              </div>


              <div class="card-body">
                  <table id="future_reservations-table" class="table dt-responsive nowrap align-middle"
                      style="width: 100%; border: aliceblue;">
                      <thead>
                          <tr>
                              <th>{{ trans('outpatient_trans.sr_no') }}</th>
                              <th>{{ trans('outpatient_trans.action') }}</th>
                              <th>{{ trans('outpatient_trans.status') }}</th>
                              <th>{{ trans('outpatient_trans.clinc_doctor') }}</th>
                              <th>{{ trans('outpatient_trans.q_number') }}</th>
                              <th>{{ trans('outpatient_trans.patient_id') }}</th>
                              <th>{{ trans('outpatient_trans.patient_name') }}</th>
                              <th>{{ trans('outpatient_trans.patient_phone') }}</th>

                              <th>{{ trans('outpatient_trans.age') }}</th>
                              <th>{{ trans('outpatient_trans.patient_contract') }}</th>
                              <th>{{ trans('outpatient_trans.reservations_date') }}</th>

                          </tr>
                      </thead>
                      <tbody></tbody>
                  </table>
              </div>
          </div>
          <!-- Cancel Modal -->
          <div class="modal fade" id="future_cancelReservationModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title">{{ __('outpatient_trans.cancel_reservation_title') }}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                          <div class="text-center">
                              <lord-icon src="https://cdn.lordicon.com/sgtmgpft.json" trigger="loop"
                                  colors="primary:#3a3347,secondary:#ebe6ef,tertiary:#1373c3,quaternary:#f24c00"
                                  style="width:100px;height:100px"></lord-icon>
                              <div class="mt-4">
                                  <h4>{{ __('outpatient_trans.cancel_reservation_p') }}</h4>
                              </div>
                          </div>
                          <form method="POST" action="" id="cancel-form">
                              @csrf
                              <input type="hidden" id="id" name="id">
                              <div class="mb-3">
                                  <label for="reason_cancel_reservation" class="form-label">
                                      {{ __('outpatient_trans.reason_for_cancellation') }}
                                  </label>
                                  <textarea class="form-control" id="reason_cancel_reservation" name="reason_cancel_reservation" rows="3" required></textarea>
                              </div>
                              <div class="d-flex gap-2 justify-content-center mt-4">
                                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                      {{ __('outpatient_trans.close') }}
                                  </button>
                                  <button type="button" class="btn btn-danger" id="cancel-record">
                                      {{ __('outpatient_trans.yes_cancel') }}
                                  </button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>

      </div>
  </div>
  <script>
      function getDoctors2() {
          var clinicId = $('#clinc_id_future_reservations').val();
          document.getElementById("id_doctor_future_reservations").disabled = true;

          if (clinicId) {
              $.ajax({
                  url: '{{ route('get.doctors.by.clinic') }}', // Define this route
                  type: 'GET',
                  data: {
                      clinic_id: clinicId
                  },
                  success: function(response) {
                      // Clear the doctor dropdown first
                      $('#id_doctor_future_reservations').empty();

                      // Add a default "select doctor" option
                      $('#id_doctor_future_reservations').append(
                          '<option selected disabled> {{ trans('outpatient_trans.select_doctor') }} </option>'
                      );

                      // Append the doctors to the dropdown
                      response.doctors.forEach(function(doctor) {
                          $('#id_doctor_future_reservations').append('<option value="' + doctor.id +
                              '">' + doctor
                              .full_name + '</option>');
                      });
                      document.getElementById("id_doctor_future_reservations").disabled = false;

                  },
                  error: function(xhr) {
                      console.log(xhr.responseText);
                  }
              });
          }
      }
      $(function() {
          // Initialize the DataTable
          var table = $('#future_reservations-table').DataTable({
              processing: true,
              serverSide: true,
              ajax: {
                  url: "{{ url('get-future_reservations') }}",
                  data: function(d) {
                      // Add filters to the request
                      d.clinc_id = $('#clinc_id_future_reservations').val();
                      d.doctor_id = $('#id_doctor_future_reservations').val();
                      d.date_from = $('#date_from').val();
                      d.date_to = $('#date_to').val();
                  }
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
                      data: 'status_and_visit',
                      name: 'status_and_visit'
                  },
                  {
                      data: 'doctor_clinic',
                      name: 'doctor_clinic'
                  },
                  {
                      data: 'q_number',
                      name: 'q_number'
                  },
                  {
                      data: 'patient_id',
                      name: 'patient_id'
                  },
                  {
                      data: 'patient_name',
                      name: 'patient_name'
                  },
                  {
                      data: 'patient_phone',
                      name: 'patient_phone'
                  },
                  {
                      data: 'age',
                      name: 'age'
                  },
                  {
                      data: 'patient_contract',
                      name: 'patient_contract'
                  },
                  {
                      data: 'reservations_details',
                      name: 'reservations_details'
                  },
              ],
              dom: 'Bfrtip',
              buttons: [],
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

          $('#search-future_table').click(function() {
              // Check if date_from is greater than date_to
              let dateFrom = $('#date_from').val();
              let dateTo = $('#date_to').val();

              if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
                  Swal.fire({
                      title: "{{ trans('outpatient_trans.date_error') }}",
                      icon: "danger",
                      showConfirmButton: false,
                      timer: 3000
                  });
              }

              table.ajax.reload();
          });
          // Reload table when filters change
          $('#reload-future_table').click(function() {
              table.ajax.reload();
          });

          // Apply clinic and doctor filters
          $('#clinc_id_future_reservations, #id_doctor_future_reservations').change(function() {
              table.ajax.reload(); // Reload table when filters change
          });

          // Apply search filter
          $('#search-future_reservations').on('input', function() {
              table.search(this.value).draw();
          });

          // Export buttons functionality
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

      // cancel confirmation function
      function cancel_reservation(id) {
          $('#id').val(id); // Set the ID in the hidden input field
          $('#future_cancelReservationModal').modal('show'); // Open the modal
      }
      $(document).ready(function() {
          $('#cancel-record').click(function(e) {
              e.preventDefault();

              let id = $('#id').val();
              let reason = $('#reason_cancel_reservation').val();

              if (!reason) {
                  alert('{{ __('outpatient_trans.reason_required') }}');
                  return;
              }

              $.ajax({
                  url: '{{ route('reservations.cancel') }}', // استخدم route لضمان المسار الصحيح
                  type: 'POST',
                  data: {
                      _token: $('input[name="_token"]').val(),
                      id: id,
                      reason_cancel_reservation: reason
                  },
                  success: function(response) {
                      if (response.success) {
                          alert('{{ __('outpatient_trans.cancel_success') }}');
                          $('#future_cancelReservationModal').modal('hide');
                          table.ajax.reload();
                      } else {
                          alert(response.message ||
                              '{{ __('outpatient_trans.cancel_error') }}');
                      }
                  },
                  error: function(xhr, status, error) {
                      console.error(error);
                      alert('{{ __('outpatient_trans.cancel_error') }}');
                  }
              });
          });
      });
function confirmReservation(id) {
    Swal.fire({
        title: '{{ __('outpatient_trans.confirm_reservation') }}',
        text: '{{ __('outpatient_trans.confirm_reservation_prompt') }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __('outpatient_trans.confirm') }}',
        cancelButtonText: '{{ __('outpatient_trans.cancel') }}',
        customClass: {
            popup: 'animated bounceIn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('reservations.confirm') }}', // 🔹 Route لتأكيد الحجز
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id
                },
                beforeSend: function() {
                    Swal.showLoading();
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '{{ __('outpatient_trans.success') }}',
                            text: '{{ __('outpatient_trans.confirm_success') }}',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            didClose: () => {
                                $('#future_reservations-table').DataTable().ajax.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: '{{ __('outpatient_trans.error') }}',
                            text: response.message || '{{ __('outpatient_trans.confirm_error') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('outpatient_trans.ok') }}'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: '{{ __('outpatient_trans.error') }}',
                        text: '{{ __('outpatient_trans.confirm_error') }}',
                        icon: 'error',
                        confirmButtonText: '{{ __('outpatient_trans.ok') }}'
                    });
                }
            });
        }
    });
}

  </script>
