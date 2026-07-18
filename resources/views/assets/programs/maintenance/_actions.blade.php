<div class="btn-group" role="group">
  <a href="{{ route('assets.programs.maintenance.show', $row->id) }}"
     class="btn btn-sm btn-outline-info"
     title="{{ __('assets.maintenance.buttons.view') }}">
    <i class="ri-eye-line"></i> {{ __('assets.maintenance.buttons.view') }}
  </a>

  <a href="{{ route('assets.programs.maintenance.edit', $row->id) }}"
     class="btn btn-sm btn-outline-primary"
     title="{{ __('assets.maintenance.buttons.edit') }}">
    <i class="ri-edit-line"></i> {{ __('assets.maintenance.buttons.edit') }}
  </a>

  <form method="POST" action="{{ route('assets.programs.maintenance.destroy', $row->id) }}" class="d-inline js-delete-form">
    @csrf @method('DELETE')
    <button type="button"
            class="btn btn-sm btn-outline-danger js-delete-btn"
            data-code="{{ $row->code }}"
            title="{{ __('assets.maintenance.buttons.delete') }}">
      <i class="ri-delete-bin-6-line"></i> {{ __('assets.maintenance.buttons.delete') }}
    </button>
  </form>
</div>
