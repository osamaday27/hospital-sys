@extends('layouts.master_table')
@section('title') {{ __('assets.maintenance.create.title') }} @stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('assets.maintenance.create.heading') }}</h4>
    <a href="{{ route('assets.programs.maintenance.index') }}" class="btn btn-light">
      <i class="ri-arrow-go-back-line"></i> {{ __('assets.maintenance.buttons.back') }}
    </a>
  </div>
</div>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('assets.programs.maintenance.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="card mb-3">
    <div class="card-body row g-3">
      <div class="col-md-4">
        <label class="form-label">{{ __('assets.maintenance.form.labels.asset') }}</label>
        <select name="asset_id" id="asset_id" class="form-select" required></select>
      </div>

      <div class="col-md-2">
        <label class="form-label">{{ __('assets.maintenance.form.labels.type') }}</label>
        <select name="maintenance_type" class="form-select" required>
          @foreach (['corrective','preventive','inspection'] as $k)
            <option value="{{ $k }}">{{ __('assets.maintenance.types.'.$k) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">{{ __('assets.maintenance.form.labels.priority') }}</label>
        <select name="priority" class="form-select" required>
          @foreach (['low','medium','high'] as $k)
            <option value="{{ $k }}" @selected($k==='medium')>{{ __('assets.maintenance.priorities.'.$k) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">{{ __('assets.maintenance.form.labels.reported_at') }}</label>
        <input type="date" name="reported_at" class="form-control" value="{{ date('Y-m-d') }}" required>
      </div>

      <div class="col-md-2">
        <label class="form-label">{{ __('assets.maintenance.form.labels.vendor') }}</label>
        <select name="vendor_id" id="vendor_id" class="form-select"></select>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('assets.maintenance.form.labels.summary') }}</label>
        <input type="text" name="summary" class="form-control" placeholder="{{ __('assets.maintenance.form.placeholders.summary') }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('assets.maintenance.form.labels.description') }}</label>
        <textarea name="description" class="form-control" rows="1" placeholder="{{ __('assets.maintenance.form.placeholders.description') }}"></textarea>
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('assets.maintenance.form.labels.started_at') }}</label>
        <input type="date" name="started_at" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('assets.maintenance.form.labels.finished_at') }}</label>
        <input type="date" name="finished_at" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('assets.maintenance.form.labels.next_due_date') }}</label>
        <input type="date" name="next_due_date" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('assets.maintenance.form.labels.attachment') }}</label>
        <input type="file" name="attachment" class="form-control">
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>{{ __('assets.maintenance.tasks.card_title') }}</strong>
      <button type="button" class="btn btn-sm btn-outline-primary" id="js-add-row">
        <i class="ri-add-line"></i> {{ __('assets.maintenance.tasks.add_row') }}
      </button>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle" id="tasks-table">
        <thead>
          <tr>
            <th>{{ __('assets.maintenance.tasks.table.description') }}</th>
            <th>{{ __('assets.maintenance.tasks.table.performed_at') }}</th>
            <th>{{ __('assets.maintenance.tasks.table.hours') }}</th>
            <th>{{ __('assets.maintenance.tasks.table.cost') }}</th>
            <th>{{ __('assets.maintenance.tasks.table.technician') }}</th>
            <th>{{ __('assets.maintenance.tasks.table.notes') }}</th>
            <th style="width:60px">{{ __('assets.maintenance.tasks.table.delete') }}</th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-end">{{ __('assets.maintenance.tasks.table.total_cost') }}</th>
            <th><span id="total-cost">0.00</span></th>
            <th colspan="3"></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div class="text-end">
    <button class="btn btn-primary">
      <i class="ri-save-3-line"></i> {{ __('assets.maintenance.buttons.save') }}
    </button>
  </div>
</form>

{{-- Select2 & helpers --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
(function(){
  const rowTpl = @json(view('assets.programs.maintenance._task_row')->render());

  function recalcTotal(){
    let total = 0;
    $('#tasks-table .task-cost').each(function(){
      const v = parseFloat($(this).val() || 0);
      if (!isNaN(v)) total += v;
    });
    $('#total-cost').text(total.toFixed(2));
  }

  function addRow(){
    const index = $('#tasks-table tbody tr').length;
    const html = rowTpl.replaceAll('__NAME__', 'tasks['+index+']');
    $('#tasks-table tbody').append(html);
  }

  $(document).on('input', '.task-cost', recalcTotal);
  $(document).on('click', '.js-remove-row', function(){
    $(this).closest('tr').remove();
    recalcTotal();
  });

  $('#js-add-row').on('click', addRow);
  addRow(); // صف ابتدائي

  // Select2: أصول
  $('#asset_id').select2({
    theme: 'bootstrap-5',
    placeholder: "{{ __('assets.maintenance.form.placeholders.asset') }}",
    ajax: {
      url: "{{ route('assets.programs.maintenance.ajax.assets') }}",
      dataType: 'json',
      delay: 200,
      data: params => ({ term: params.term }),
      processResults: data => data
    }
  });

  // Select2: مزود
  $('#vendor_id').select2({
    theme: 'bootstrap-5',
    placeholder: "{{ __('assets.maintenance.form.placeholders.vendor') }}",
    allowClear: true,
    ajax: {
      url: "{{ route('assets.programs.maintenance.ajax.vendors') }}",
      dataType: 'json',
      delay: 200,
      data: params => ({ term: params.term }),
      processResults: data => data
    }
  });
})();
</script>
@endsection
