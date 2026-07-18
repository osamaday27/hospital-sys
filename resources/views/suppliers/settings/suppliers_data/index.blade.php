
@extends('layouts.master_table')

@section('title')
    {{ trans('suppliers.suppliers_data') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- العنوان وbreadcrumb --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            {{ trans('suppliers.suppliers_data') }}
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
    @endif

    {{-- الجدول --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <input type="search" id="search-suppliers" class="form-control" placeholder="{{ trans('suppliers.search_placeholder') }}" style="width:250px;">
                    <div class="hstack gap-2">
                        <a href="{{ route('suppliers_data.create') }}" class="btn btn-outline-secondary">
                            <i class="ri-add-line"></i> {{ trans('suppliers.create_supplier') }}
                        </a>
                        <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
                        <button id="export-csv" class="btn btn-primary"><i class="fa fa-file-csv"></i> CSV</button>
                        <button id="export-xlsx" class="btn btn-success"><i class="fas fa-file-excel"></i> XLSX</button>
                    </div>
                </div>

                <div id="alert-container" class="m-2"></div>

                <div class="card-body">
                    <table id="suppliers-table" class="table dt-responsive nowrap align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('suppliers.code') }}</th>
                                <th>{{ trans('suppliers.name') }}</th>
                                <th>{{ trans('suppliers.branch') }}</th>
                                <th>{{ trans('suppliers.created_by') }}</th>
                                <th>{{ trans('inpatient_trans.created_at') }}</th>
                                <th>{{ trans('inpatient_trans.updated_at') }}</th>
                                <th>{{ trans('inpatient_trans.status') }}</th>
                                <th>{{ trans('general.actions') }}</th>
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
            <h5 class="modal-title">{{ trans('suppliers.supplier_details') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <tbody id="details-body">
                {{-- تُحشّى بالـ JS --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- السكربت --}}
    <script>
    $(function() {
        var table = $('#suppliers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('suppliers_data.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
                { data: 'code', name: 'code' },
                { data: 'supplier_name',  name: 'supplier_name' },
                { data: 'branch_name', name: 'branch_name' },
                { data: 'created_by_name', name: 'created_by' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'status_label', name: 'status' },
                { data: 'action', name: 'action', orderable:false, searchable:false }
            ],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className:'btn btn-outline-primary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> XLSX', className:'btn btn-outline-success' }
            ],
            order: [[4,'desc']]
        });

        // أدوات التصفية والتصدير
        $('#reload-table').click(()=> table.ajax.reload());
        $('#search-suppliers').on('input', function(){ table.search(this.value).draw(); });
        $('#export-csv').click(()=> table.button('.buttons-csv').trigger());
        $('#export-xlsx').click(()=> table.button('.buttons-excel').trigger());

        // زر الحذف
        $(document).on('click','.delete-btn',function(){
            $('#delete-id').val($(this).data('id'));
            $('#deleteModal').modal('show');
        });
        $('#delete-record').click(function(){
            let id = $('#delete-id').val();
            let url = '{{ route("suppliers_data.destroy", ":id") }}'.replace(':id', id);
            $.ajax({ url, type:'DELETE', data:{ _token:'{{ csrf_token() }}' },
                success(resp){
                    $('#deleteModal').modal('hide');
                    showAlert(resp.success ? 'success':'danger', resp.message);
                    table.ajax.reload();
                },
                error(){ showAlert('danger','حدث خطأ أثناء الحذف.'); }
            });
        });

        // زر عرض التفاصيل
        $(document).on('click','.view-btn',function(){
            let id = $(this).data('id');
            $.get("{{ url('suppliers_data') }}/"+id, function(data){
                let html = '';
                $.each(data, function(key, val){
                    html += `<tr><th>${key}</th><td>${val}</td></tr>`;
                });
                $('#details-body').html(html);
                $('#detailsModal').modal('show');
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
