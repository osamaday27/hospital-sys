@extends('layouts.master_table')
@section('title', __('assets.assignments'))
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('partials.alert-success')
@includeWhen($errors->any(),'partials.alert-errors')

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('assets.programs.items.index') }}">@lang('assets.items')</a></li>
          <li class="breadcrumb-item active">@lang('assets.assignments')</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row"><div class="col-lg-12"><div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2">
      <input type="search" id="search-assignments" class="form-control" style="width:250px;"
             placeholder="@lang('assets.search_placeholder')">
      <button id="reload-table" class="btn btn-info" type="button" title="@lang('assets.reload')">
        <i class="ri-repeat-2-line"></i>
      </button>
      <button id="export-csv"  class="btn btn-primary" type="button" title="CSV">
        <i class="fa fa-file-csv"></i> CSV
      </button>
      <button id="export-xlsx" class="btn btn-success" type="button" title="XLSX">
        <i class="fas fa-file-excel"></i> XLSX
      </button>
    </div>
    <a href="{{ route('assets.programs.assignments.create') }}" class="btn btn-outline-secondary">
      <i class="ri-add-line"></i> @lang('assets.create_assignment')
    </a>
  </div>

  <div id="alert-container" class="m-2"></div>

  <div class="card-body">
    <table id="assignments-table" class="table dt-responsive nowrap align-middle w-100 text-center">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>@lang('assets.asset')</th>
          <th>@lang('assets.assignee')</th>
          <th>@lang('assets.assigned_at')</th>
          <th>@lang('assets.due_back_at')</th>
          <th>@lang('assets.returned_at')</th>
          <th>@lang('assets.qty')</th> {{-- NEW: عمود الكمية --}}
          <th>@lang('assets.status')</th>
          <th>@lang('assets.notes')</th>
          <th>@lang('assets.file')</th>
          <th>@lang('assets.actions')</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div></div></div>

{{-- مودال الحذف العام --}}
@include('components.modals.delete-modal')

{{-- Modal: Return --}}
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h6 class="modal-title">@lang('assets.confirm_return_title')</h6>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <p>@lang('assets.confirm_return_text')</p>

      <div class="mb-2">
        <label class="form-label">@lang('assets.return_qty')</label>
        <input type="number" min="1" id="return-qty" class="form-control" value="1">
        <div class="form-text" id="return-qty-hint"></div>
      </div>

      <div class="mb-2">
        <label class="form-label">@lang('assets.notes')</label>
        <textarea id="return-notes" class="form-control" rows="2" placeholder="@lang('assets.optional')"></textarea>
      </div>

      <div class="mb-2">
        <label class="form-label">@lang('assets.handover')</label>
        <input type="file" id="return-file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
      </div>

      <input type="hidden" id="return-id">
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-bs-dismiss="modal">@lang('assets.cancel')</button>
      <button id="confirm-return" class="btn btn-primary">@lang('assets.confirm')</button>
    </div>
  </div></div>
</div>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
  // إضافة توكن CSRF للطلبات AJAX/Fetch
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
  });

  $(function(){
    const t = $('#assignments-table').DataTable({
      processing:true, serverSide:true,
      ajax:{ url:'{{ route('assets.programs.assignments.ajax') }}' },
      columns:[
        {data:'DT_RowIndex',orderable:false,searchable:false},
        {data:'asset'},
        {data:'assignee'},
        {data:'assigned_at'},
        {data:'due_back_at'},
        {data:'returned_at'},
        {data:'qty'}, // NEW: ربط عمود الكمية
        {data:'status', orderable:false, searchable:false},
        {data:'notes'},
        {data:'file', orderable:false, searchable:false},
        {data:'action', orderable:false, searchable:false}
      ],
      order:[[0,'desc']],
      dom: 'Bfrtip',
      buttons: [
        { extend:'csvHtml5',   text:'<i class="fas fa-file-csv"></i> CSV',   className:'btn btn-outline-primary' },
        { extend:'excelHtml5', text:'<i class="fas fa-file-excel"></i> XLSX', className:'btn btn-outline-success' }
      ],
      language:{ url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' }
    });

    // أدوات أعلى الجدول
    $('#reload-table').on('click', ()=> t.ajax.reload(null,false));
    $('#search-assignments').on('input', function(){ t.search(this.value).draw(); });
    $('#export-csv').on('click',  ()=> t.button('.buttons-csv').trigger());
    $('#export-xlsx').on('click', ()=> t.button('.buttons-excel').trigger());

    // Delete (باستخدام DELETE + CSRF)
    $(document).on('click','.js-del', function(){
      $('#delete-id').val($(this).data('id'));
      $('#deleteModal').modal('show');
    });

    $('#delete-record').on('click', async function(){
      const $btn = $(this).prop('disabled', true);
      try {
        const id  = $('#delete-id').val();
        const url = '{{ route("assets.programs.assignments.destroy", ":id") }}'.replace(':id', id);

        const res = await fetch(url, {
          method:'DELETE',
          headers:{
            'Accept':'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });

        const j = await res.json().catch(()=>({}));
        if (res.ok) {
          $('#deleteModal').modal('hide');
          showAlert('success', j.message || '@lang("assets.deleted_success")');
          t.ajax.reload(null,false);
        } else {
          showAlert('danger', j.message || '@lang("assets.delete_failed")');
        }
      } catch(e){
        showAlert('danger', '@lang("assets.action_failed")');
      } finally {
        $btn.prop('disabled', false);
      }
    });

    // Return (يسجل عملية إرجاع كسجل جديد مع كمية + ملاحظات + ملف)
    $(document).on('click','.js-return', function(){
      const id = $(this).data('id');
      const remaining = parseInt($(this).data('remaining') || 1, 10);
      $('#return-id').val(id);

      const $qty = $('#return-qty');
      $qty.attr('max', remaining);
      $qty.val(remaining); // افتراضي يملأ المتبقي

      // تفريغ الحقول الإضافية
      $('#return-notes').val('');
      $('#return-file').val('');

      $('#return-qty-hint').text('@lang("assets.remaining")' + ': ' + remaining);
      $('#returnModal').modal('show');
    });

    $('#confirm-return').on('click', async function(){
      const $btn = $(this).prop('disabled', true);
      try {
        const id   = $('#return-id').val();
        const qty  = parseInt($('#return-qty').val() || '0', 10);
        const max  = parseInt($('#return-qty').attr('max') || '1', 10);
        const url  = '{{ route("assets.programs.assignments.return", ":id") }}'.replace(':id', id);

        if (!qty || qty < 1 || qty > max) {
          showAlert('danger', '@lang("assets.return_qty_invalid")');
          return;
        }

        const fd = new FormData();
        fd.append('_method', 'PATCH'); // مهم لرفع الملف مع PATCH
        fd.append('qty', String(qty));
        const notes = $('#return-notes').val();
        if (notes) fd.append('notes', String(notes));
        const file = $('#return-file')[0].files[0];
        if (file) fd.append('handover', file);

        const res = await fetch(url, {
          method:'POST',
          headers:{
            'Accept':'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: fd
        });

        const j = await res.json().catch(()=>({}));
        if (res.ok && j.ok) {
          $('#returnModal').modal('hide');
          showAlert('success', j.message || '@lang("assets.returned_success")');
          t.ajax.reload(null,false);
        } else {
          showAlert('danger', j.message || '@lang("assets.action_failed")');
        }
      } catch(e){
        showAlert('danger', '@lang("assets.action_failed")');
      } finally {
        $btn.prop('disabled', false);
      }
    });

    function showAlert(type,msg){
      $('#alert-container').html(
        `<div class="alert alert-${type} alert-dismissible fade show">
           ${msg}
           <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         </div>`
      );
      setTimeout(()=>$('.alert').alert('close'),5000);
    }
  });
</script>
@endsection
