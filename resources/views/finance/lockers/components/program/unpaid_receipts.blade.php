  <!-- Button Section -->

  <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
              </div>
              <!-- Table Section -->
              <div class="card-body">
                  <table id="unpaid_receipts-table" class="table dt-responsive nowrap align-middle"
                      style="width: 100%; border: aliceblue;">
                      <thead>
                          <tr>
                              <th>{{ trans('lockers_trans.sr_no') }}</th>
                              <th>{{ trans('lockers_trans.action') }}</th>
                              <th>{{ trans('lockers_trans.status') }}</th>
                              <th>{{ trans('lockers_trans.patient_id') }}</th>
                              <th>{{ trans('lockers_trans.patient_name') }}</th>
                              <th>{{ trans('lockers_trans.patient_contract') }}</th>
                              <th>{{ trans('lockers_trans.total') }}</th>
                              <th>{{ trans('lockers_trans.discount') }}</th>
                              <th>{{ trans('lockers_trans.contract_value') }}</th>
                              <th>{{ trans('lockers_trans.patient_value') }}</th>
                              <th>{{ trans('lockers_trans.paid') }}</th>
                              <th>{{ trans('lockers_trans.residual') }}</th>
                              <th>{{ trans('lockers_trans.receipt_date') }}</th>
                          </tr>
                      </thead>
                      <tbody></tbody>
                  </table>
              </div>
          </div>

      </div>
  </div>
  <script>
      $(function() {
          // Initialize the DataTable
          var table = $('#unpaid_receipts-table').DataTable({
              processing: true,
              serverSide: true,
              ajax: {
                  url: "{{ url('get-unpaid_receipts') }}",
                  data: function(d) {
                      // Add filters to the request
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
                      data: 'status',
                      name: 'status'
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
                      data: 'patient_contract',
                      name: 'patient_contract'
                  },
                  {
                      data: 'total',
                      name: 'total'
                  },
                  {
                      data: 'discount',
                      name: 'discount'
                  },
                  {
                      data: 'contract_contribution',
                      name: 'contract_contribution'
                  },
                  {
                      data: 'patient_contribution',
                      name: 'patient_contribution'
                  },
                  {
                      data: 'paid',
                      name: 'paid'
                  },
                  {
                      data: 'residual',
                      name: 'residual'
                  },
                  {
                      data: 'created_at',
                      name: 'created_at'
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

          // Reload table when filters change
          $('#reloadunpaid_receipts-table').click(function() {
              table.ajax.reload();
          });

          // Apply search filter
          $('#search-level').on('input', function() {
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
  </script>
