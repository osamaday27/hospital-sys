 <div class="row" style="width: 100%;">
     <div class="col-lg-12">
         <div class="card">
             <div class="card-header d-flex justify-content-between align-items-center">
                 <div class="d-flex align-items-center">
                     <input type="search" class="form-control" id="search-level"
                         placeholder="{{ trans('outpatient_trans.search') }}" style="width: 250px;">

                 </div>
                 <div class="hstack gap-2">
                     <button id="reload_service-table" class="btn btn-info btn-label waves-effect waves-light"><i
                             class="ri-repeat-2-line label-icon align-middle fs-16 me-2"></i>
                         {{ trans('outpatient_trans.update_reservations') }}</button>
                 </div>
             </div>
             <!-- Table Section -->
             <div class="card-body">
                 <table id="service_list-table" class="table dt-responsive nowrap align-middle"
                     style="width: 100%; border: aliceblue;">
                     <thead>
                         <tr>
                             <th scope="col">#</th>
                             <th scope="col"><i class="ri-delete-bin-line"></i></th>

                             <th scope="col">
                                 {{ trans('outpatient_trans.sacend_level') }}
                             </th>
                             <th scope="col">
                                 {{ trans('outpatient_trans.last_level') }}</th>
                             <th scope="col">
                                 {{ trans('outpatient_trans.total') }}</th>
                             <th scope="col">
                                 {{ trans('outpatient_trans.contracting_value') }}
                             </th>
                             <th scope="col">
                                 {{ trans('outpatient_trans.patient_value') }}
                             </th>
                             <th scope="col">
                                 {{ trans('outpatient_trans.discount') }}</th>
                             <th scope="col">
                                 {{ trans('outpatient_trans.quantity') }}</th>
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
                                     id="delete-record_service">{{ __('outpatient_trans.yes_delete') }}</button>
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
    // Initialize the DataTable without automatic server request
    var table = $('#service_list-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('get-service_list') }}",
            data: function(d) {
               d.reservation_id = $('#service_reservations_id').val() || 0;
            }
        },
        deferLoading: 0,  // Prevent auto-loading on page load
        columns: [
            { data: 'id', name: 'id' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'category', name: 'category' },
            { data: 'service_name', name: 'service_name' },
            { data: 'total_pricing', name: 'total_pricing' },
            { data: 'contract_contribution', name: 'contract_contribution' },
            { data: 'patient_contribution', name: 'patient_contribution' },
            { data: 'discount', name: 'discount' },
            { data: 'quantity', name: 'quantity' }
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

    // Reload table manually on button click
    $('#reload_service-table').click(function() {
        table.ajax.reload();
    });
    
    $('#reload-tab').click(function() {
        table.ajax.reload();
    });

    // Apply search filter
    $('#search-level').on('input', function() {
        table.search(this.value).draw();
    });
});

     // Delete confirmation function
     function deleteFunctionservis(id) {
         $('#id').val(id); // Set the ID in the hidden input field
         $('#deleteModal').modal('show'); // Open the modal
     }

     // Handle delete action
     $('#delete-record_service').click(function() {
         var id = $('#id').val(); // Get the ID from the hidden field
         var url = '{{ route('service_list.destroy', ':id') }}';
         url = url.replace(':id', id); // Replace :id with the actual ID

         // Send DELETE request using AJAX
         $.ajax({
             url: url,
             type: 'DELETE',
             data: {
                 "_token": "{{ csrf_token() }}", // CSRF token for security
             },
             success: function(response) {
                 $('#deleteModal').modal('hide'); // Close the modal
                 $('#service_list-table').DataTable().ajax.reload(); // Reload the DataTable
             },
             error: function(xhr, status, error) {
                 console.log(xhr.responseText);
                 alert("There was an error deleting the record.");
             }
         });
     });
 </script>
