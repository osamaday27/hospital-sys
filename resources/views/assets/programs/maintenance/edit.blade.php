@extends('layouts.master_table')
@section('title') {{ __('assets.maintenance.edit.title') }} @stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('assets.maintenance.edit.heading') }}</h4>
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

<form action="{{ route('assets.programs.maintenance.update', $row->id) }}" method="POST">
  @csrf @method('PUT')

  <div class="card mb-3">
    <div class="card-body row g-3">
      <div class="col-md-4">
        <label class="form-label">{{ __('assets.maintenance.form.labels.asset') }}</label>
        <select name="asset_id" id="asset_id" class="form-select" required>
          <option value="{{ $row->asset_id }}" selected>
            {{ $row->asset?->code }} - {{ $row->asset?->name_ar ?? $row->asset?->name_en }}
          </option>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">{{ __('assets.maintenance.form.labels.type') }}</label>
        <select name="maintenance_type" class="form-select" required>
          @foreach (['corrective','preventive','inspection'] as $k)
            <option value="{{ $k }}" @selected($row->maintenance_type==$k)>{{ __('assets.maintenance.types.'.$k) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">{{ __('assets.maintenance.form.labels.status') }}</label>
        <select name="status" class="form-select" required>
          @foreach (['open','in_progress','done','cancelled'] as $k)
            <option value="{{ $k }}" @selected($row->status==$k)>{{ __('assets.maintenance.statuses.'.$k) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">{{ __('assets.maintenance.form.labels.priority') }}</label>
        <select name="priority" class="form-select" required>
          @foreach (['low','medium','high'] as $k)
            <option value="{{ $k }}" @selected($row->priority==$k)>{{ __('assets.maintenance.priorities.'.$k) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">{{ __('assets.maintenance.form.labels.reported_at') }}</label>
        <input type="date" name="reported_at" class="form-control" value="{{ optional($row->reported_at)->format('Y-m-d') }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('assets.maintenance.form.labels.summary') }}</label>
        <input type="text" name="summary" class="form-control" value="{{ $row->summary }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('assets.maintenance.form.labels.description') }}</label>
        <textarea name="description" class="form-control" rows="1">{{ $row->description }}</textarea>
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('assets.maintenance.form.labels.started_at') }}</label>
        <input type="date" name="started_at" class="form-control" value="{{ optional($row->started_at)->format('Y-m-d') }}">
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('assets.maintenance.form.labels.finished_at') }}</label>
        <input type="date" name="finished_at" class="form-control" value="{{ optional($row->finished_at)->format('Y-m-d') }}">
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('assets.maintenance.form.labels.next_due_date') }}</label>
        <input type="date" name="next_due_date" class="form-control" value="{{ optional($row->next_due_date)->format('Y-m-d') }}">
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('assets.maintenance.form.labels.vendor') }}</label>
<select name="vendor_id" id="vendor_id" class="form-select">
  @if($row->vendor)
    @php
      $vendorName = app()->getLocale()==='ar'
        ? ($row->vendor->name_ar ?? $row->vendor->name_en)
        : ($row->vendor->name_en ?? $row->vendor->name_ar);
      $vendorText = trim(($row->vendor->code ? $row->vendor->code.' - ' : '').($vendorName ?? ('#'.$row->vendor_id)));
    @endphp
    <option value="{{ $row->vendor_id }}" selected>{{ $vendorText }}</option>
  @endif
</select>

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
        <tbody>
          @foreach ($row->tasks as $i=>$t)
            <tr class="task-row">
              <td><input type="text" name="tasks[{{ $i }}][description]" class="form-control" value="{{ $t->description }}" required></td>
              <td><input type="date" name="tasks[{{ $i }}][performed_at]" class="form-control" value="{{ optional($t->performed_at)->format('Y-m-d') }}"></td>
              <td><input type="number" step="0.01" min="0" name="tasks[{{ $i }}][hours]" class="form-control task-hours" value="{{ $t->hours }}"></td>
              <td><input type="number" step="0.01" min="0" name="tasks[{{ $i }}][cost]" class="form-control task-cost" value="{{ $t->cost }}"></td>
              <td><input type="text" name="tasks[{{ $i }}][technician_name]" class="form-control" value="{{ $t->technician_name }}"></td>
              <td><input type="text" name="tasks[{{ $i }}][notes]" class="form-control" value="{{ $t->notes }}"></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger js-remove-row" title="{{ __('assets.maintenance.tasks.table.delete') }}">
                  <i class="ri-close-line"></i>
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-end">{{ __('assets.maintenance.tasks.table.total_cost') }}</th>
            <th><span id="total-cost">{{ number_format($row->cost_total,2) }}</span></th>
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

  // Select2: أصول
  $('#asset_id').select2({
    theme: 'bootstrap-5',
    placeholder: "{{ __('assets.maintenance.edit.placeholders.asset') }}",
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
    placeholder: "{{ __('assets.maintenance.edit.placeholders.vendor') }}",
    allowClear: true,
    ajax: {
      url: "{{ route('assets.programs.maintenance.ajax.vendors') }}",
      dataType: 'json',
      delay: 200,
      data: params => ({ term: params.term }),
      processResults: data => data
    }
  });

  // حساب أولي
  recalcTotal();
})();
</script>
@endsection
