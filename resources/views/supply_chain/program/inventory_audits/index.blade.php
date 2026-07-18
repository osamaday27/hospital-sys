@extends('layouts.master_table')

@section('title')
{{ __('supply_chain_trans.inventory_audits') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            {{ __('supply_chain_trans.inventory_audits') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center"
            style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
            {{ session('success') }}
        </div>
        <script>
            $(function () {
                setTimeout(function () {
                    $('.alert-success').fadeOut(600, function () { $(this).remove(); });
                }, 4000);
            });
        </script>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <input type="search" id="search-audits" class="form-control"
                        placeholder="{{ __('supply_chain_trans.search') }}" style="width:250px;">

                    <div class="hstack gap-2">
                        <a href="#" id="open-create-modal" class="btn btn-outline-secondary">
                            <i class="ri-add-line"></i> {{ __('supply_chain_trans.create_inventory_audit') }}
                        </a>

                        <button id="reload-table" class="btn btn-info" title="{{ __('supply_chain_trans.reload') }}">
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
                    <table id="audits-table" class="table dt-responsive nowrap align-middle w-100 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('supply_chain_trans.code') }}</th>
                                <th>{{ __('supply_chain_trans.store') }}</th>
                                <th>{{ __('supply_chain_trans.date') }}</th>
                                <th>{{ __('supply_chain_trans.status') }}</th>
                                <th>{{ __('supply_chain_trans.created_by') }}</th>
                                <th>{{ __('supply_chain_trans.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('components.modals.delete-modal')

    {{-- Details Modal --}}
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('supply_chain_trans.inventory_audit_details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('supply_chain_trans.close') }}"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered mb-3">
                        <tbody id="details-body"></tbody>
                    </table>

                    <h6 class="mt-2">{{ __('supply_chain_trans.items') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('supply_chain_trans.product') }}</th>
                                    <th>{{ __('supply_chain_trans.unit') }}</th>
                                    <th>{{ __('supply_chain_trans.batch_no') }}</th>
                                    <th>{{ __('supply_chain_trans.expiry_date') }}</th>
                                    <th>{{ __('supply_chain_trans.system_qty') }}</th>
                                    <th>{{ __('supply_chain_trans.actual_qty') }}</th>
                                    <th>{{ __('supply_chain_trans.diff_qty') }}</th>
                                    <th>{{ __('supply_chain_trans.status') }}</th>
                                </tr>
                            </thead>
                            <tbody id="items-body"></tbody>
                        </table>
                    </div>

                    <hr>
                    <div id="attachments-section" class="mt-3">
                        <h6>{{ __('supply_chain_trans.attachments') }}</h6>
                        <div id="attachments-list" class="d-flex flex-wrap gap-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Store Select / Confirm Lock Modal (يُستخدم للإنشاء والتعديل) --}}
    <div class="modal fade" id="storeSelectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="store-modal-title">{{ __('supply_chain_trans.select_store') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('supply_chain_trans.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div id="store-select-wrapper">
                        <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
                        <select id="store-select" class="form-control" style="width:100%"></select>
                    </div>
                    <div class="text-danger fw-bold mt-3" id="store-lock-warning">
                        {{ __('supply_chain_trans.store_lock_warning') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="confirm-store" class="btn btn-primary">{{ __('supply_chain_trans.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Settle Confirm Modal --}}
    <div class="modal fade" id="settleConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle">
                    <h6 class="modal-title">
                        <i class="ri-alert-line me-1 text-warning"></i>
                        {{ __('supply_chain_trans.settle') ?? 'تسوية' }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('supply_chain_trans.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-3 align-items-start">
                        <i class="ri-information-line fs-2 text-warning"></i>
                        <div>
                            <p class="mb-1">
                                {{ __('supply_chain_trans.settle_confirm_message')
        ?? 'سيتم إضافة/خصم فرق الجرد إلى/من رصيد المخزن (بالوحدة الصغرى) ولن يمكن التراجع عن هذه العملية. هل تريد المتابعة؟' }}
                            </p>
                            <small class="text-muted">{{ __('supply_chain_trans.store_lock_warning') }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('supply_chain_trans.cancel') }}</button>
                    <button class="btn btn-warning" id="confirm-settle">{{ __('supply_chain_trans.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(function () {
            var table = $('#audits-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: "{{ route('inventory_audits.index') }}" },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'code', name: 'inventory_audits.code' },
                    { data: 'store_name', name: 'store_name', orderable: false, searchable: true },
                    { data: 'audit_date', name: 'inventory_audits.audit_date' },
                    { data: 'status_label', name: 'inventory_audits.status', orderable: true, searchable: true },
                    { data: 'creator_name', name: 'users.name', orderable: false, searchable: false },
                    { data: 'action', orderable: false, searchable: false }
                ],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
                ],
                order: [[3, 'desc']]
            });

            $('#reload-table').click(() => table.ajax.reload());
            $('#search-audits').on('input', function () { table.search(this.value).draw(); });
            $('#export-csv').click(() => table.button('.buttons-csv').trigger());
            $('#export-xlsx').click(() => table.button('.buttons-excel').trigger());

            // حذف
            $(document).on('click', '.delete-btn', function () {
                $('#delete-id').val($(this).data('id'));
                $('#deleteModal').modal('show');
            });

            $('#delete-record').click(function () {
                let id = $('#delete-id').val();
                let url = '{{ route("inventory_audits.destroy", ":id") }}'.replace(':id', id);
                $.ajax({
                    url, type: 'DELETE',
                    success(resp) {
                        $('#deleteModal').modal('hide');
                        showAlert(resp.success ? 'success' : 'danger', resp.message || "{{ __('supply_chain_trans.deleted_successfully') }}");
                        table.ajax.reload(null, false);
                    },
                    error(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('supply_chain_trans.error_deleting') }}";
                        showAlert('danger', msg);
                    }
                });
            });

            // عرض التفاصيل (مع معاينة مرفقات مصغّرة)
            $(document).on('click', '.view-btn', function () {
                let id = $(this).data('id');
                $.get("{{ url('inventory_audits') }}/" + id, function (data) {
                    let html = '';
                    $.each(data.header || {}, function (key, val) {
                        html += `<tr><th class="text-nowrap">${key}</th><td>${val ?? ''}</td></tr>`;
                    });
                    $('#details-body').html(html);

                    let itemsHtml = '';
                    (data.items || []).forEach(function (row, idx) {
                        itemsHtml += `
                      <tr>
                        <td>${idx + 1}</td>
                        <td>${row.product || ''}</td>
                        <td>${row.unit || ''}</td>
                        <td>${row.batch_no || ''}</td>
                        <td>${row.expiry_date || ''}</td>
                        <td>${row.system_quantity ?? 0}</td>
                        <td>${row.actual_quantity ?? 0}</td>
                        <td>${row.difference_quantity ?? 0}</td>
                        <td>${row.status_label || ''}</td>
                      </tr>`;
                    });
                    $('#items-body').html(itemsHtml);

                    // ✅ معاينة المرفقات (صور مصغّرة)
                    const files = data.attachments || [];
                    let filesHtml = '';
                    if (files.length === 0) {
                        filesHtml = '<span class="text-muted">{{ __("supply_chain_trans.no_attachments") }}</span>';
                    } else {
                        files.forEach(function (url) {
                            const name = (url || '').toString().split('/').pop();
                            const ext = (name.split('.').pop() || '').toLowerCase();
                            const isImg = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext);

                            if (isImg) {
                                filesHtml += `
                              <a href="${url}" class="text-decoration-none" target="_blank" title="${name}">
                                <img src="${url}" class="rounded border" style="width:64px;height:64px;object-fit:cover">
                              </a>`;
                            } else {
                                filesHtml += `
                              <a href="${url}" target="_blank" class="d-inline-flex align-items-center gap-2 text-decoration-none border rounded px-2 py-1" title="${name}">
                                <i class="ri-file-3-line fs-5"></i>
                                <span class="text-truncate" style="max-width:180px">${name}</span>
                              </a>`;
                            }
                        });
                    }
                    $('#attachments-list').html(filesHtml);

                    $('#detailsModal').modal('show');
                });
            });

            function showAlert(type, msg) {
                $('#alert-container').html(
                    `<div class="alert alert-${type} alert-dismissible fade show">${msg}
                   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('supply_chain_trans.close') }}"></button>
                 </div>`
                );
                setTimeout(() => $('.alert').alert('close'), 5000);
            }

            // --- مودال الإنشاء
            $('#open-create-modal').on('click', function (e) {
                e.preventDefault();
                $('#store-modal-title').text("{{ __('supply_chain_trans.select_store') }}");
                $('#store-select-wrapper').show();
                $('#confirm-store').data('audit-id', null);
                $('#storeSelectModal').modal('show');

                if (!$('#store-select').hasClass('select2-hidden-accessible')) {
                    $('#store-select').select2({
                        dropdownParent: $('#storeSelectModal'),
                        placeholder: "{{ __('supply_chain_trans.search') }}",
                        ajax: {
                            url: "{{ route('inventory_audits.ajax.stores') }}",
                            data: params => ({ q: params.term }),
                            processResults: data => ({ results: data })
                        }
                    });
                }
            });

            // --- زر تعديل: نفس مودال القفل بدون اختيار مخزن
            $(document).on('click', '.edit-btn', function (e) {
                e.preventDefault();
                const auditId = $(this).data('id');

                $('#store-modal-title').text("{{ __('supply_chain_trans.confirm') }}");
                $('#store-select-wrapper').hide();
                $('#confirm-store').data('audit-id', auditId);
                $('#storeSelectModal').modal('show');
            });

            // تأكيد المودال (إنشاء أو تعديل حسب الوضع)
            $('#confirm-store').on('click', function () {
                const auditId = $(this).data('audit-id');

                if (auditId) {
                    const url = '{{ route("inventory_audits.prepare_edit", ":id") }}'.replace(':id', auditId);
                    $.post(url, {}, function (resp) {
                        if (resp.redirect) window.location.href = resp.redirect;
                    });
                } else {
                    let storeId = $('#store-select').val();
                    if (!storeId) { return; }
                    $.post("{{ route('inventory_audits.start') }}", { store_id: storeId }, function (resp) {
                        if (resp.redirect) { window.location.href = resp.redirect; }
                    });
                }
            });

            // --- تسوية: مودال تأكيد + طلب POST
            let settleAuditId = null;

            $(document).on('click', '.settle-btn', function () {
                settleAuditId = $(this).data('id') || null;
                $('#settleConfirmModal').modal('show');
            });

            $('#confirm-settle').on('click', function () {
                if (!settleAuditId) return;

                const $btn = $(this).prop('disabled', true);
                const url = '{{ route("inventory_audits.settle", ":id") }}'.replace(':id', settleAuditId);

                $.post(url, {}, function (resp) {
                    $('#settleConfirmModal').modal('hide');
                    showAlert('success', resp?.message || "{{ __('supply_chain_trans.settled_successfully') ?? 'تمت التسوية بنجاح' }}");
                    table.ajax.reload(null, false);
                })
                    .fail(function (xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('supply_chain_trans.error_occured') ?? 'حدث خطأ غير متوقع' }}";
                        showAlert('danger', msg);
                    })
                    .always(function () { $btn.prop('disabled', false); });
            });

        });
    </script>
@endsection
