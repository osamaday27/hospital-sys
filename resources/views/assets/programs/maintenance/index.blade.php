@extends('layouts.master_table')

@section('title') {{ __('assets.maintenance.index_title') }} @stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('assets.maintenance.index_title') }}</h4>
    <a href="{{ route('assets.programs.maintenance.create') }}" class="btn btn-success">
      <i class="ri-add-line"></i> {{ __('assets.maintenance.add_btn') }}
    </a>
  </div>
</div>

{{-- فلاتر --}}
<div class="card mb-3">
  <div class="card-body row g-2">
    <div class="col-md-4">
      <label class="form-label">{{ __('assets.maintenance.filters.asset_label') }}</label>
      <select id="f_asset_id" class="form-select"></select>
    </div>
    <div class="col-md-2">
      <label class="form-label">{{ __('assets.maintenance.filters.status_label') }}</label>
      <select id="f_status" class="form-select">
        <option value="">{{ __('assets.maintenance.filters.all') }}</option>
        <option value="open">{{ __('assets.maintenance.statuses.open') }}</option>
        <option value="in_progress">{{ __('assets.maintenance.statuses.in_progress') }}</option>
        <option value="done">{{ __('assets.maintenance.statuses.done') }}</option>
        <option value="cancelled">{{ __('assets.maintenance.statuses.cancelled') }}</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">{{ __('assets.maintenance.filters.from_label') }}</label>
      <input type="date" id="f_from" class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">{{ __('assets.maintenance.filters.to_label') }}</label>
      <input type="date" id="f_to" class="form-control">
    </div>
    <div class="col-md-2 d-flex align-items-end">
      <button id="btn-filter" class="btn btn-primary w-100">
        <i class="ri-filter-3-line"></i> {{ __('assets.maintenance.filters.apply_btn') }}
      </button>
    </div>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>

<div class="card">
  <div class="card-body">
    <table id="maintenance-table" class="table table-striped w-100">
      <thead>
        <tr>
          <th>{{ __('assets.maintenance.table.code') }}</th>
          <th>{{ __('assets.maintenance.table.asset') }}</th>
          <th>{{ __('assets.maintenance.table.type') }}</th>
          <th>{{ __('assets.maintenance.table.status') }}</th>
          <th>{{ __('assets.maintenance.table.priority') }}</th>
          <th>{{ __('assets.maintenance.table.reported_at') }}</th>
          <th>{{ __('assets.maintenance.table.total') }}</th>
          <th>{{ __('assets.maintenance.table.actions') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

{{-- Modal تأكيد الحذف --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('assets.maintenance.confirm.title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('assets.maintenance.confirm.cancel') }}"></button>
      </div>
      <div class="modal-body">
        <p id="confirm-delete-msg" class="mb-0">{{ __('assets.maintenance.confirm.delete') }}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('assets.maintenance.confirm.cancel') }}</button>
        <button type="button" class="btn btn-danger" id="confirm-delete-yes">{{ __('assets.maintenance.confirm.yes_delete') }}</button>
      </div>
    </div>
  </div>
</div>

{{-- JS libs (لو مش موجودة في الـ layout) --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.10/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.10/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
$(function(){

  // Select2: الأصول
  $('#f_asset_id').select2({
    theme: 'bootstrap-5',
    placeholder: "{{ __('assets.maintenance.filters.asset_placeholder') }}",
    allowClear: true,
    ajax: {
      url: "{{ route('assets.programs.maintenance.ajax.assets') }}",
      dataType: 'json',
      delay: 200,
      data: params => ({ term: params.term }),
      processResults: data => data
    }
  });

  // DataTable
  const table = $('#maintenance-table').DataTable({
    serverSide: true,
    processing: true,
    ajax: {
      url: "{{ route('assets.programs.maintenance.index') }}",
      dataSrc: 'data',
      data: function(d){
        d.asset_id  = $('#f_asset_id').val();
        d.status    = $('#f_status').val();
        d.date_from = $('#f_from').val();
        d.date_to   = $('#f_to').val();
      }
    },
    columns: [
      { data: 'code' },
      { data: 'asset' },
      { data: 'type' },      // HTML badge
      { data: 'status' },    // HTML badge
      { data: 'priority' },  // HTML badge
      { data: 'reported_at' },
      { data: 'cost_total' },
      { data: 'actions', orderable:false, searchable:false }
    ],
    order: [[0, 'desc']],
    pageLength: 10,
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' }
  });

  // تطبيق الفلاتر
  $('#btn-filter').on('click', function(){
    table.ajax.reload();
  });

  // منطق تأكيد الحذف (Modal)
  let deleteForm = null;

  document.addEventListener('click', function(e){
    const btn = e.target.closest('.js-delete-btn');
    if(!btn) return;

    e.preventDefault();
    deleteForm = btn.closest('.js-delete-form');

    const code = btn.getAttribute('data-code') || '';
    const msg = code
      ? @json(__('assets.maintenance.confirm.delete_named', ['code' => ':code'])).replace(':code', code)
      : @json(__('assets.maintenance.confirm.delete'));

    document.getElementById('confirm-delete-msg').textContent = msg;

    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();

    document.getElementById('confirm-delete-yes').onclick = function(){
      modal.hide();
      if (deleteForm) deleteForm.submit();
    };
  });

});
</script>
@endsection
