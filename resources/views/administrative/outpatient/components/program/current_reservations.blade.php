  <!-- Button Section -->

  <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                      <input type="search" class="form-control" id="search-level"
                          placeholder="{{ trans('outpatient_trans.search') }}" style="width: 250px;">
                      <div class="col-6">
                          <div class="mb-3" style="margin-right: 14px;margin-top: 17px;">

                              <select class="js-example-basic-single" name="clinc_id2" id="clinc_id2"
                                  style="margin-top: 5px;" onchange="getdoctors2();">
                                  <option selected disabled> {{ trans('outpatient_trans.choose_clinc') }}</option>
                                  @foreach ($clinic as $data)
                                      <option value="{{ $data->id }}"> {{ $data->name_ar }}</option>
                                  @endforeach
                              </select>
                          </div>
                      </div>
                      <div class="col-6">
                          <div class="mb-3" style="margin-right: 14px;margin-top: 17px;">

                              <select class="js-example-basic-single" name="id_doctor2" id="id_doctor2"
                                  style="margin-top: 5px;">
                                  <option selected disabled> {{ trans('outpatient_trans.select_doctor') }}</option>
                              </select>
                          </div>
                      </div>
                  </div>
                  <div class="hstack gap-2">
                      <button id="reload-table" class="btn btn-info btn-label waves-effect waves-light"><i
                              class="ri-repeat-2-line label-icon align-middle fs-16 me-2"></i>
                          {{ trans('outpatient_trans.update_reservations') }}</button>
                  </div>
              </div>
              <!-- Table Section -->
              <div class="card-body">
                  <table id="level1-table" class="table dt-responsive nowrap align-middle"
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
          <div class="modal fade" id="cancelReservationModal" tabindex="-1" aria-hidden="true">
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
      function getdoctors2() {
          var clinicId = $('#clinc_id2').val();
          $('#id_doctor2').prop('disabled', true);

          if (clinicId) {
              $.ajax({
                  url: '{{ route('get.doctors.by.clinic') }}',
                  type: 'GET',
                  data: {
                      clinic_id: clinicId
                  },
                  success: function(response) {
                      console.log("Doctors:", response.doctors); // للتأكد
                      $('#id_doctor2').empty().append(
                          '<option selected disabled>{{ trans('outpatient_trans.select_doctor') }}</option>'
                      );

                      if (Array.isArray(response.doctors)) {
                          response.doctors.forEach(function(doctor) {
                              if (doctor && doctor.id && doctor.full_name) {
                                  $('#id_doctor2').append(
                                      '<option value="' + doctor.id + '">' + doctor.full_name +
                                      '</option>'
                                  );
                              }
                          });
                      } else {
                          console.warn("doctors is not an array!", response.doctors);
                      }

                      $('#id_doctor2').prop('disabled', false);
                  },
                  error: function(xhr) {
                      console.error("Error fetching doctors:", xhr.responseText);
                      $('#id_doctor2').prop('disabled', false);
                  }
              });
          }
      }


     
 function arrival_confirmed(id) {
    Swal.fire({
        title: '{{ __('outpatient_trans.arrival_confirmed') }}',
        text: '{{ __('outpatient_trans.arrival_confirmed_prompt') }}',
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
                url: '{{ route('reservations.arrival_confirmed') }}',
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
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#level1-table').DataTable().ajax.reload();
                    } else if (response.redirect) {
                        Swal.fire({
                            title: '{{ __('outpatient_trans.data_incomplete') }}',
                            text: response.message,
                            icon: 'warning',
                            confirmButtonText: '{{ __('outpatient_trans.update_patient_data') }}'
                        }).then(() => {
                            window.open(response.redirect_url, '_blank');
                        });
                    } else {
                        Swal.fire({
                            title: '{{ __('outpatient_trans.error') }}',
                            text: response.message,
                            icon: 'error'
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


      $(function() {
          var table = $('#level1-table').DataTable({
              processing: true,
              serverSide: true,
              ajax: {
                  url: "{{ url('get-current_reservations') }}",
                  data: function(d) {
                      d.clinc_id = $('#clinc_id2').val();
                      d.doctor_id = $('#id_doctor2').val();
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
                      previous: "{{ __('datatables.previous') }}",
                      next: "{{ __('datatables.next') }}"
                  }
              }
          });

          $('#reload-table').click(function() {
              table.ajax.reload();
          });

          $('#clinc_id2, #id_doctor2').change(function() {
              table.ajax.reload();
          });

          $('#search-level').on('input', function() {
              table.search(this.value).draw();
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

      function cancel_reservation(id) {
          $('#id').val(id);
          $('#cancelReservationModal').modal('show');
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
                  url: '{{ route('reservations.cancel') }}',
                  type: 'POST',
                  data: {
                      _token: $('input[name="_token"]').val(),
                      id: id,
                      reason_cancel_reservation: reason
                  },
                  success: function(response) {
                      if (response.success) {
                          alert('{{ __('outpatient_trans.cancel_success') }}');
                          $('#cancelReservationModal').modal('hide');
                          $('#level1-table').DataTable().ajax.reload();
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
                                $('#level1-table').DataTable().ajax.reload();
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
