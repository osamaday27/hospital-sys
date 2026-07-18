@extends('layouts.master_table')

@section('title')
    {{ trans('suppliers.supplier_companies') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            {{ trans('suppliers.supplier_companies') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center"
             style="position: fixed; top:20px; left:50%; transform: translateX(-50%); z-index:9999;">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <input
                        type="search"
                        id="search-companies"
                        class="form-control"
                        placeholder="{{ trans('suppliers.supplier_companies') }}"
                        style="width: 250px;"
                    >
                    <div class="hstack gap-2">
                        <a href="{{ route('suppliers_company.create') }}" class="btn btn-outline-secondary">
                            <i class="ri-add-line"></i> {{ trans('suppliers.create_supplier_company') }}
                        </a>
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

                <div id="alert-container"></div>

                <div class="card-body">
                    <table id="companies-table" class="table dt-responsive nowrap align-middle w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('suppliers.manual_code') }}</th>
                                <th>{{ trans('suppliers.name_ar') }}</th>
                                <th>{{ trans('suppliers.name_en') }}</th>
                                <th>{{ trans('suppliers.activity') }}</th>
                                <th>{{ trans('suppliers.status') }}</th>
                                <th>{{ trans('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    @include('components.modals.delete-modal')

    <!-- Details Modal -->
    <div class="modal fade" id="companyDetailModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ trans('suppliers.company_details') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <dl class="row">
              <dt class="col-sm-4">{{ trans('suppliers.manual_code') }}</dt>
              <dd class="col-sm-8" id="detail-manual_code"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.name_ar') }}</dt>
              <dd class="col-sm-8" id="detail-name_ar"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.name_en') }}</dt>
              <dd class="col-sm-8" id="detail-name_en"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.activity') }}</dt>
              <dd class="col-sm-8" id="detail-activity"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.commercial_registration_no') }}</dt>
              <dd class="col-sm-8" id="detail-commercial_registration_no"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.tax_card_no') }}</dt>
              <dd class="col-sm-8" id="detail-tax_card_no"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.domain') }}</dt>
              <dd class="col-sm-8" id="detail-domain"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.phone') }}</dt>
              <dd class="col-sm-8" id="detail-phone"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.email') }}</dt>
              <dd class="col-sm-8" id="detail-email"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.fax') }}</dt>
              <dd class="col-sm-8" id="detail-fax"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.country') }}</dt>
              <dd class="col-sm-8" id="detail-country"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.government') }}</dt>
              <dd class="col-sm-8" id="detail-governorate"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.city') }}</dt>
              <dd class="col-sm-8" id="detail-city"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.notes') }}</dt>
              <dd class="col-sm-8" id="detail-notes"></dd>

              <dt class="col-sm-4">{{ trans('suppliers.status') }}</dt>
              <dd class="col-sm-8" id="detail-status"></dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <script>
    $(function() {
        var table = $('#companies-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('suppliers_company.index') }}",
            columns: [
                { data: null, orderable: false, render: (d,t,r,m) => m.row + 1 },
                { data: 'manual_code', name: 'manual_code' },
                { data: 'name_ar',      name: 'name_ar'      },
                { data: 'name_en',      name: 'name_en'      },
                { data: 'activity',     name: 'activity'     },
                {
                    data: 'status', name: 'status',
                    render: s => s==='active'
                        ? '<span class="badge bg-success">'+s+'</span>'
                        : '<span class="badge bg-secondary">'+s+'</span>'
                },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv',   className: 'btn btn-outline-primary', text: '<i class="fa fa-file-csv"></i> CSV'   },
                { extend: 'excel', className: 'btn btn-outline-success', text: '<i class="fas fa-file-excel"></i> XLSX' }
            ]
        });

        $('#reload-table').click(() => table.ajax.reload());
        $('#search-companies').on('input', e => table.search(e.target.value).draw());
        $('#export-csv').click(() => table.button('.buttons-csv').trigger());
        $('#export-xlsx').click(() => table.button('.buttons-excel').trigger());

        // حذف
        $(document).on('click', '.delete-btn', function() {
            $('#delete-id').val($(this).data('id'));
            $('#deleteModal').modal('show');
        });
        $('#delete-record').click(function() {
            var id = $('#delete-id').val();
            $.ajax({
                url: "{{ url('suppliers_company') }}/" + id,
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success(res) {
                    $('#deleteModal').modal('hide');
                    showAlert('success', res.message);
                    table.ajax.reload();
                },
                error() {
                    showAlert('danger', "{{ trans('general.error_occurred') }}");
                }
            });
        });

        // تفاصيل
        $(document).on('click', '.details-btn', function() {
    var id = $(this).data('id');
    $.get(`{{ url('suppliers_company') }}/${id}`, function(res) {
        $('#detail-manual_code').text(res.manual_code);
        $('#detail-name_ar').text(res.name_ar);
        $('#detail-name_en').text(res.name_en);
        $('#detail-activity').text(res.activity || '-');
        $('#detail-commercial_registration_no').text(res.commercial_registration_no || '-');
        $('#detail-tax_card_no').text(res.tax_card_no || '-');
        $('#detail-domain').text(res.domain || '-');
        $('#detail-phone').text(res.phone || '-');
        $('#detail-email').text(res.email || '-');
        $('#detail-fax').text(res.fax || '-');

        // هذه تحوي النصوص مباشرةً
        $('#detail-country').text(res.country_name);
        $('#detail-governorate').text(res.government_name);
        $('#detail-city').text(res.city_name);

        $('#detail-notes').text(res.notes || '-');
        $('#detail-status').text(res.status);
        $('#companyDetailModal').modal('show');
    });
});


        function showAlert(type, message) {
            var html = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">'
                     + message
                     + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
                     + '</div>';
            $('#alert-container').html(html);
            setTimeout(() => $('.alert').alert('close'), 5000);
        }
    });
    </script>
@endsection
