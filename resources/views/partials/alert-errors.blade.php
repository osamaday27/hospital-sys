{{-- resources/views/partials/alert-errors.blade.php --}}
@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show">
    <div class="fw-semibold mb-1">@lang('assets.validation_errors')</div>
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="@lang('assets.close')"></button>
  </div>
@endif
