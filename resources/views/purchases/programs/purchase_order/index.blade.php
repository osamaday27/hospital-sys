@extends('layouts.master_table')

@section('title')
  {{ __('purchases.purchase_orders') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Breadcrumb --}}
    <div class="row mb-3">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item active">{{ __('purchases.purchase_orders') }}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
      <div class="alert alert-success text-center"
           style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;">
        {{ session('success') }}
      </div>
      <script>
        setTimeout(function(){
          $('.alert-success').fadeOut();
        }, 3000);
      </script>
    @endif

    {{-- Error Alert --}}
    @if(session('error'))
      <div class="alert alert-danger text-center"
           style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;">
        {{ session('error') }}
      </div>
      <script>
        setTimeout(function(){ $('.alert-danger').fadeOut(); }, 3000);
      </script>
    @endif

    {{-- Search & Utilities --}}
    <div class="row mb-2">
      <div class="col-12 d-flex justify-content-end">
        <input type="search" id="search-orders"
               class="form-control me-2"
               placeholder="{{ __('purchases.search_placeholder') }}"
               style="width:250px;">
        <button id="reload-orders" class="btn btn-info me-2">
          <i class="ri-repeat-2-line"></i>
        </button>
        <button id="export-csv" class="btn btn-outline-primary me-1">
          <i class="fas fa-file-csv"></i> CSV
        </button>
        <button id="export-excel" class="btn btn-outline-success">
          <i class="fas fa-file-excel"></i> XLSX
        </button>
      </div>
    </div>

    {{-- Purchase Orders Table --}}
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <table id="purchase-orders-table"
                   class="table table-bordered dt-responsive nowrap w-100 align-middle text-center">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>{{ __('purchases.order_number') }}</th>
                  <th>{{ __('purchases.order_date') }}</th>
                  <th>{{ __('purchases.supplier') }}</th>
                  <th>{{ __('purchases.subtotal') }}</th>
                  <th>{{ __('purchases.total_tax') }}</th>
                  <th>{{ __('purchases.net_amount') }}</th>
                  <th>{{ __('purchases.priority') }}</th>
                  <th>{{ __('general.actions') }}</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @include('components.modals.delete-modal')

    {{-- Details Modal --}}
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('purchases.details') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered mb-3">
              <tbody id="details-body">
                {{-- filled by JS --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <script>
      $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
      });

      $(function(){
        // Translations
        const txtEdit     = @js(__('purchases.edit'));
        const txtDelete   = @js(__('purchases.delete'));
        const txtView     = @js(__('purchases.details'));
        const txtConfirm  = @js(__('general.confirm_delete'));

        // Initialize DataTable
        var table = $('#purchase-orders-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('purchase_order.index') }}",
          columns: [
            { data: 'DT_RowIndex',                  name:'DT_RowIndex', orderable:false, searchable:false },
            { data: 'order_number',                 name:'order_number' },
            { data: 'order_date',                   name:'order_date' },
            { data: 'supplier_name',                name:'supplier_name', orderable:false },
            { data: 'subtotal',                     name:'subtotal' },
            { data: 'total_tax',                    name:'total_tax' },
            { data: 'total_amount_including_tax',   name:'total_amount_including_tax' },
            { data: 'priority',                     name:'priority' },
            {
              data: null,
              orderable: false,
              searchable: false,
              render: function(row) {
                // إذا كان للأمر محضر استلام/فحص: تعطيل تعديل/حذف وإظهار تلميح
                const locked = (row.has_receiving === 1 || row.has_receiving === '1');

                const tipEdit = 'لا يمكن التعديل بعد إنشاء محضر استلام/فحص';
                const tipDel  = 'لا يمكن الحذف بعد إنشاء محضر استلام/فحص';

                const editHref = `{{ url('purchase_order') }}/${row.id}/edit`;

                const editBtn = locked
                  ? `<a href="${editHref}" class="btn btn-primary btn-sm me-1 disabled"
                        aria-disabled="true" data-bs-toggle="tooltip" title="${tipEdit}">
                       <i class="fa fa-edit"></i> ${txtEdit}
                     </a>`
                  : `<a href="${editHref}" class="btn btn-primary btn-sm me-1">
                       <i class="fa fa-edit"></i> ${txtEdit}
                     </a>`;

                const delBtn = locked
                  ? `<button class="btn btn-danger btn-sm me-1" disabled
                        data-bs-toggle="tooltip" title="${tipDel}">
                       <i class="fa fa-trash"></i> ${txtDelete}
                     </button>`
                  : `<button class="btn btn-danger btn-sm me-1 delete-btn" data-id="${row.id}">
                       <i class="fa fa-trash"></i> ${txtDelete}
                     </button>`;

                const printBtn = `
                  <a href="{{ url('purchase_order') }}/${row.id}/print"
                     target="_blank" rel="noopener noreferrer"
                     class="btn btn-secondary btn-sm me-1">
                    <i class="fa fa-print"></i> {{ __('purchases.print') }}
                  </a>`;

                const viewBtn = `
                  <button class="btn btn-info btn-sm view-btn" data-id="${row.id}">
                    <i class="fa fa-eye"></i> ${txtView}
                  </button>`;

                const lockedBadge = locked
                  ? `<span class="badge bg-secondary ms-1">محضر استلام/فحص موجود</span>`
                  : '';

                return `${editBtn}${delBtn}${printBtn}${viewBtn} ${lockedBadge}`;
              }
            }
          ],
          dom: 'Bfrtip',
          buttons: [
            { extend:'csv',   text:'<i class="fas fa-file-csv"></i> CSV' },
            { extend:'excel', text:'<i class="fas fa-file-excel"></i> XLSX' }
          ],
          order: [[2,'desc']]
        });

        // Enable tooltips after each draw
        table.on('draw.dt', function(){
          document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el){
            new bootstrap.Tooltip(el);
          });
        });

        // Prevent click on disabled anchors
        $('#purchase-orders-table').on('click','a.disabled', function(e){
          e.preventDefault();
        });

        // Search & Reload
        $('#search-orders').on('input', function(){
          table.search(this.value).draw();
        });
        $('#reload-orders').click(function(){
          table.ajax.reload();
        });
        $('#export-csv').click(function(){
          table.button('.buttons-csv').trigger();
        });
        $('#export-excel').click(function(){
          table.button('.buttons-excel').trigger();
        });

        // Delete action
        $('#purchase-orders-table').on('click','.delete-btn',function(){
          $('#delete-id').val($(this).data('id'));
          $('#deleteModal').modal('show');
        });
        $('#delete-record').click(function(){
          let id = $('#delete-id').val();
          let url = '{{ route("purchase_order.destroy", ":id") }}'.replace(':id', id);
          $.ajax({
            url: url,
            type:'DELETE',
            success(resp){
              $('#deleteModal').modal('hide');
              table.ajax.reload();
              showAlert('success', resp.message);
            },
            error(xhr){
              let msg = '{{ __("purchases.error_deleting") }}';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
              }
              $('#deleteModal').modal('hide');
              showAlert('danger', msg);
            }
          });
        });

        // View details
        $('#purchase-orders-table').on('click','.view-btn',function(){
          let id = $(this).data('id');
          $.get(`{{ url('purchase_order') }}/${id}`, function(res){
            let html = '';
            $.each(res.details, function(label, val){
              html += `<tr><th>${label}</th><td>${val}</td></tr>`;
            });
            $('#details-body').html(html);
            $('#detailsModal').modal('show');
          });
        });

        function showAlert(type,msg){
          $('<div class="alert alert-'+type+' alert-dismissible fade show text-center" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;">'+
            msg+
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'+
          '</div>').appendTo('body')
           .delay(3000).fadeOut(600,function(){ $(this).remove(); });
        }
      });
    </script>
@endsection
