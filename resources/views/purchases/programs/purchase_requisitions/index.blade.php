@extends('layouts.master_table')

@section('title')
    {{ __('purchases.requisitions') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    // هل نعرض الطلبات السابقة؟
    $showPrevious = request()->get('previous') == '1';
@endphp

{{-- ====== العنوان وbreadcrumb ====== --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">
                        {{ __('purchases.requisitions') }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- رسالة النجاح --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-center"
         style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
        {{ session('success') }}
    </div>
    <script>
    $(function(){
        setTimeout(function(){
            $('.alert-success').fadeOut(600, function() { $(this).remove(); });
        }, 4000);
    });
    </script>
@endif

{{-- ====== جدول طلبات الاحتياج ====== --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <input type="search"
                       id="search-requisitions"
                       class="form-control"
                       placeholder="{{ __('purchases.search_placeholder') }}"
                       style="width:250px;">

                <div class="hstack gap-2">
                    {{-- إنشاء طلب جديد --}}
                    <a href="{{ route('purchase_requisitions.create') }}"
                       class="btn btn-outline-secondary">
                        <i class="ri-add-line"></i> {{ __('purchases.create_requisition') }}
                    </a>

                    {{-- تبديل بين عرض النشط والسابق --}}
                    @if(! $showPrevious)
                        <a href="{{ route('purchase_requisitions.index', ['previous' => 1]) }}"
                           class="btn btn-outline-secondary">
                            <i class="ri-history-line"></i> {{ __('purchases.previous_requisitions') }}
                        </a>
                    @else
                        <a href="{{ route('purchase_requisitions.index') }}"
                           class="btn btn-outline-secondary">
                            <i class="ri-list-unordered"></i> {{ __('purchases.active_requisitions') }}
                        </a>
                    @endif

                    <button id="reload-table" class="btn btn-info">
                        <i class="ri-repeat-2-line"></i>
                    </button>
                    <button id="export-csv" class="btn btn-primary">
                        <i class="fa fa-file-csv"></i> CSV
                    </button>
                    <button id="export-xlsx" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> XLSX
                    </button>
                </div>
            </div>

            <div id="alert-container" class="m-2"></div>

            <div class="card-body">
                <table id="requisitions-table"
                       class="table dt-responsive nowrap align-middle w-100 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('purchases.requisition_number') }}</th>
                            <th>{{ __('purchases.requisition_date') }}</th>
                            <th>{{ __('purchases.status') }}</th>
                            <th>{{ __('purchases.warehouse') }}</th>
                            <th>{{ __('purchases.department') }}</th>
                            <th>{{ __('purchases.supplier') }}</th>
                            <th>{{ __('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- مودال الحذف --}}
@include('components.modals.delete-modal')

{{-- مودال عرض التفاصيل --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('purchases.requisition_details') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="details-body">
            {{-- تعبئة عبر JS --}}
          </tbody>
        </table>
        <hr>
        <div id="attachments-section" class="mt-3">
          <h6>{{ __('purchases.attachments') }}</h6>
          <div id="attachments-list"></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- مودال رفع المرفقات --}}
<div class="modal fade" id="attachModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="attach-form" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('purchases.attach_files') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="requisition_id" id="attach-requisition-id">
          <div class="mb-3">
            <label class="form-label">{{ __('purchases.select_files') }}</label>
            <input type="file" name="attachments[]" class="form-control" multiple required>
          </div>
          <div id="attach-upload-status"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">{{ __('purchases.upload_files') }}</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(function() {
    // إنشاء DataTable
    var table = $('#requisitions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('purchase_requisitions.index') }}",
            data: {
                previous: '{{ $showPrevious ? 1 : 0 }}'
            }
        },
        columns: [
            { data: 'DT_RowIndex',      name: 'DT_RowIndex',      orderable: false, searchable: false },
            { data: 'requisition_number', name: 'purchase_requisitions.requisition_number' },
            { data: 'requisition_date',   name: 'purchase_requisitions.requisition_date' },
            { data: 'status_label',       name: 'purchase_requisitions.status', orderable: true, searchable: true },
            { data: 'warehouse_name',     orderable: false, searchable: false },
            { data: 'department_name',    orderable: false, searchable: false },
            { data: 'supplier_name',      orderable: false, searchable: false },
            { data: 'action',             orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'csv',   text: '<i class="fas fa-file-csv"></i> CSV',   className:'btn btn-outline-primary' },
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> XLSX', className:'btn btn-outline-success' }
        ],
        order: [[2, 'desc']]
    });

    // عناصر التحكم في الجدول
    $('#reload-table').click(()=> table.ajax.reload());
    $('#search-requisitions').on('input', function(){ table.search(this.value).draw(); });
    $('#export-csv').click(()=> table.button('.buttons-csv').trigger());
    $('#export-xlsx').click(()=> table.button('.buttons-excel').trigger());

    // حذف السجل
    $(document).on('click','.delete-btn',function(){
        $('#delete-id').val($(this).data('id'));
        $('#deleteModal').modal('show');
    });
    $('#delete-record').click(function(){
        let id = $('#delete-id').val();
        let url = '{{ route("purchase_requisitions.destroy", ":id") }}'.replace(':id', id);
        $.ajax({
            url, type:'DELETE', data:{ _token:'{{ csrf_token() }}' },
            success(resp){
                $('#deleteModal').modal('hide');
                showAlert(resp.success?'success':'danger', resp.message);
                table.ajax.reload();
            },
            error(){
                showAlert('danger','{{ __("purchases.error_deleting") }}');
            }
        });
    });

    // عرض التفاصيل + المرفقات
    $(document).on('click','.view-btn',function(){
        let id = $(this).data('id');
        $.get("{{ url('purchase_requisitions') }}/"+id, function(data){
            // التفاصيل
            let html = '';
            $.each(data.details, function(key, val){
                html += `<tr><th>${key}</th><td>${val}</td></tr>`;
            });
            $('#details-body').html(html);
            // المرفقات
            let files = data.attachments || [];
            let filesHtml = '';
            if(files.length === 0){
                filesHtml = '<span class="text-muted">{{ __("purchases.no_attachments") }}</span>';
            } else {
                files.forEach(function(file){
                    let filename = file.split('/').pop();
                    filesHtml += `
                      <div class="d-flex align-items-center mb-2">
                        <a href="/${file}" target="_blank" class="me-2">
                          <i class="fa fa-file"></i> ${filename}
                        </a>
                        <button class="btn btn-danger btn-sm delete-attachment-btn"
                                data-id="${id}" data-file="${file}">
                          <i class="fa fa-trash"></i> {{ __("purchases.delete") }}
                        </button>
                      </div>`;
                });
            }
            $('#attachments-list').html(filesHtml);
            $('#detailsModal').modal('show');
        });
    });

    // إزالة مرفق
    $(document).on('click', '.delete-attachment-btn', function(){
        if(!confirm('{{ __("purchases.confirm_delete_attachment") }}')) return;
        let id   = $(this).data('id');
        let file = $(this).data('file');
        $.post("{{ route('purchase_requisitions.delete_attachment') }}", {
            _token: $('meta[name="csrf-token"]').attr('content'),
            requisition_id: id,
            file: file
        }).done(function(){
            $('.view-btn[data-id="'+id+'"]').click();
        }).fail(function(){
            alert('{{ __("purchases.error_deleting_attachment") }}');
        });
    });

    // إرفاق ملفات
    $(document).on('click','.attach-btn',function(){
        $('#attach-requisition-id').val($(this).data('id'));
        $('#attachModal').modal('show');
    });
    $('#attach-form').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $('#attach-upload-status').html('<div class="text-info">{{ __("purchases.uploading_files") }}</div>');
        $.ajax({
            url: "{{ route('purchase_requisitions.attachments') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success(resp){
                $('#attach-upload-status').html('<div class="alert alert-success">'+resp.message+'</div>');
                setTimeout(()=>$('#attachModal').modal('hide'), 1200);
            },
            error(xhr){
                let msg = '{{ __("purchases.error_uploading_files") }}';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                $('#attach-upload-status').html('<div class="alert alert-danger">'+msg+'</div>');
            }
        });
    });

    function showAlert(type,msg){
        $('#alert-container').html(
            `<div class="alert alert-${type} alert-dismissible fade show">${msg}
               <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
             </div>`
        );
        setTimeout(()=>$('.alert').alert('close'),5000);
    }
});
</script>
@endsection
