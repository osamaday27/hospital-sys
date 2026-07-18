@extends('layouts.master_table')

@section('title')
{{ __('purchases.price_quotations_for') }}: {{ $requisition->requisition_number }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ====== Breadcrumb ====== --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('purchase_requisitions.index') }}">{{ __('purchases.requisitions') }}</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ __('purchases.price_quotations_for') }}: {{ $requisition->requisition_number }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast لعرض رسالة انتهاء الصلاحية --}}
    <div id="expiredQuotationToast"
        class="toast align-items-center bg-warning text-white border-0 position-fixed top-0 end-0 m-3"
        style="z-index: 2000;" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ __('purchases.quotation_expired') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    {{-- رسالة النجاح --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center"
            style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
{{-- رسالة الخطأ --}}
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
    {{-- ===== جدول عروض السعر لهذا الطلب ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <input type="search" id="search-quotations" class="form-control"
                            placeholder="{{ __('purchases.search_placeholder') }}" style="width:250px;">
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

                    <div class="d-flex gap-2">
                        {{-- زر المقارنة الجديد --}}
                        <button id="open-compare" class="btn btn-outline-primary">
                            <i class="ri-scales-3-line"></i> مقارنة العروض
                        </button>
                        @php $locked = in_array($requisition->status, ['closed']); @endphp

                        @if(!$locked)
                            {{-- زر إضافة عرض سعر جديد --}}
                            <a href="{{ route('price_quotations.create', ['requisition_id' => $requisition->id]) }}"
                                class="btn btn-outline-secondary">
                                <i class="ri-add-line"></i> {{ __('purchases.create_price_quotation') }}
                            </a>
                        @endif

                    </div>
                </div>

                <div id="alert-container" class="m-2"></div>

                <div class="card-body">
                    <table id="quotations-table" class="table dt-responsive nowrap align-middle w-100 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('purchases.quotation_number') }}</th>
                                <th>{{ __('purchases.start_date') }}</th>
                                <th>{{ __('purchases.end_date') }}</th>
                                <th>{{ __('purchases.thesupplier') }}</th>
                                <th>{{ __('purchases.total_offer') }}</th>
                                <th>{{ __('purchases.total_tax') }}</th>
                                <th>{{ __('purchases.final_price_including_tax') }}</th>
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

    {{-- مودال عرض تفاصيل عرض السعر --}}
    <div class="modal fade" id="quotationDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('purchases.price_quotation_details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody id="quotation-details-body">
                            {{-- يُملأ عبر AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال مقارنة العروض --}}
    <div class="modal fade" id="compareModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        مقارنة عروض السعر — {{ $requisition->requisition_number }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="compare-summary" class="mb-3"></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle text-center" id="compare-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم العرض</th>
                                    <th>المورد</th>
                                    <th>السعر الإجمالي شامل الضريبة</th>
                                    <th>فترة التسليم (يوم)</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>الضرائب</th>
                                    <th>الترتيب/التقييم</th>
                                    <th>مؤشرات</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- يُملأ عبر AJAX --}}
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-2">
                        * أفضل إجمالي يُحسب بوزن: السعر 70% + فترة التسليم 30%. العروض المنتهية الصلاحية لا تُوصى تلقائيًا.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(function () {
            // تهيئة DataTable
            var table = $('#quotations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('price_quotations.index') }}",
                    data: { requisition_id: "{{ $requisition->id }}" }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'quotation_number', name: 'quotation_number' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false },
                    { data: 'total_offer', name: 'total_offer', className: 'text-end' },
                    { data: 'total_tax', name: 'total_tax', className: 'text-end' },
                    { data: 'final_price', name: 'final_price', className: 'text-end' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
                ],
                order: [[2, 'desc']],
                columnDefs: [
                    { targets: [5, 6, 7], className: 'text-end' }
                ]
            });

            // Search & Reload & Export
            $('#reload-table').click(() => table.ajax.reload());
            $('#search-quotations').on('input', function () { table.search(this.value).draw(); });
            $('#export-csv').click(() => table.button('.buttons-csv').trigger());
            $('#export-xlsx').click(() => table.button('.buttons-excel').trigger());

            // حذف
            $(document).on('click', '.delete-btn', function () {
                $('#delete-id').val($(this).data('id'));
                $('#deleteModal').modal('show');
            });
            $('#delete-record').click(function () {
                let id = $('#delete-id').val(),
                    url = '{{ route("price_quotations.destroy", ":id") }}'.replace(':id', id);
                $.ajax({
                    url, type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                    success(resp) {
                        $('#deleteModal').modal('hide');
                        showAlert(resp.success ? 'success' : 'danger', resp.message);
                        table.ajax.reload();
                    }
                });
            });

            // عرض التفاصيل
            $(document).on('click', '.view-quotation-btn', function () {
                let id = $(this).data('id');
                $.get("{{ url('price_quotations') }}/" + id, function (data) {
                    let html = '';
                    $.each(data.details, function (label, val) {
                        html += `<tr><th>${label}</th><td>${val}</td></tr>`;
                    });
                    $('#quotation-details-body').html(html);
                    $('#quotationDetailsModal').modal('show');
                });
            });

            // اعتراض زر "إنشاء أمر شراء" إذا انتهت صلاحية العرض
            $(document).on('click', 'a[href*="/purchase_order/create"]', function (e) {
                let row = table.row($(this).closest('tr')).data();
                let endDate = row.end_date; // "YYYY-MM-DD"
                let today = new Date().toISOString().split('T')[0];
                if (endDate < today) {
                    e.preventDefault();
                    new bootstrap.Toast($('#expiredQuotationToast')).show();
                }
            });

            function showAlert(type, msg) {
                $('#alert-container').html(
                    `<div class="alert alert-${type} alert-dismissible fade show">
               ${msg}
               <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
             </div>`
                );
                setTimeout(() => $('.alert').alert('close'), 5000);
            }

            // ===== المقارنة =====
            $('#open-compare').on('click', function () {
                const url = "{{ route('price_quotations.compare') }}";
                $.get(url, { requisition_id: "{{ $requisition->id }}" }, function (resp) {
                    const list = resp.data || [];
                    const $tb = $('#compare-table tbody').empty();

                    if (!list.length) {
                        $('#compare-summary').html('<div class="alert alert-info mb-0">لا توجد عروض كافية للمقارنة.</div>');
                        $('#compareModal').modal('show');
                        return;
                    }

                    // ملخص أعلى المودال
                    const best = list.find(x => x.is_best_overall);
                    let summaryHtml = '';
                    if (best) {
                        summaryHtml = `
                <div class="alert alert-success">
                  <strong>أفضل عرض إجمالي:</strong>
                  <span class="badge bg-success ms-1">#${best.rank}</span>
                  المورد: <strong>${best.supplier_name}</strong> —
                  رقم العرض: <strong>${best.quotation_number}</strong> —
                  السعر: <strong>${Number(best.final_price_including_tax).toLocaleString(undefined, { minimumFractionDigits: 2 })}</strong> —
                  التسليم: <strong>${best.delivery_period ?? '-'}</strong> يوم
                  ${best.is_expired ? '<span class="badge bg-danger ms-2">منتهي الصلاحية</span>' : ''}
                </div>`;
                    } else {
                        summaryHtml = `<div class="alert alert-warning mb-0">لا يوجد ترشيح واضح بسبب نقص البيانات (مثل فترة التسليم) أو انتهاء صلاحية كل العروض.</div>`;
                    }
                    $('#compare-summary').html(summaryHtml);

                    // بناء الصفوف
                    list.forEach((q, i) => {
                        const badges = [];
                        if (q.is_best_price) badges.push('<span class="badge bg-primary">أفضل سعر</span>');
                        if (q.is_fastest_delivery) badges.push('<span class="badge bg-info text-dark">أسرع تسليم</span>');
                        if (q.is_best_overall) badges.push('<span class="badge bg-success">أفضل إجمالي</span>');
                        if (q.is_expired) badges.push('<span class="badge bg-danger">منتهي</span>');

                        $tb.append(`
                <tr>
                  <td>${q.rank}</td>
                  <td>${q.quotation_number}</td>
                  <td>${q.supplier_name}</td>
                  <td class="text-end">${Number(q.final_price_including_tax).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                  <td>${q.delivery_period ?? '-'}</td>
                  <td>${q.end_date}</td>
                  <td class="text-end">${Number(q.total_tax).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                  <td>${q.score.toFixed(2)}</td>
                  <td class="d-flex gap-1 justify-content-center flex-wrap">${badges.join(' ')}</td>
                </tr>
              `);
                    });

                    $('#compareModal').modal('show');
                }).fail(function () {
                    $('#compare-summary').html('<div class="alert alert-danger mb-0">تعذر جلب بيانات المقارنة.</div>');
                    $('#compareModal').modal('show');
                });
            });

        });
    </script>
@endsection
