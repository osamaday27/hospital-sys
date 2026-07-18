@php
  $canSubmit   = $row->status === 'draft';
  $canExecute  = $row->status === 'approved';
  $canCancel   = in_array($row->status, ['draft','submitted','under_review','approved']);
@endphp

<div class="btn-group btn-group-sm" role="group">
  <a class="btn btn-outline-primary" href="{{ route('assets.programs.disposal.show',$row->id) }}">
    <i class="ri-eye-line"></i> {{ __('عرض') }}
  </a>

  <a class="btn btn-outline-secondary" target="_blank"
     href="{{ route('assets.programs.disposal.print.request',$row->id) }}">
    <i class="ri-printer-line"></i> {{ __('طباعة') }}
  </a>

  @if($canSubmit)
    <form method="post" action="{{ route('assets.programs.disposal.submit',$row->id) }}" class="d-inline"
          onsubmit="return confirm('{{ __('هل تريد إرسال الطلب للاعتماد؟') }}')">
      @csrf
      <button class="btn btn-outline-success">
        <i class="ri-send-plane-line"></i> {{ __('إرسال للاعتماد') }}
      </button>
    </form>
  @endif

  @if($canExecute)
    <a class="btn btn-outline-warning" href="{{ route('assets.programs.disposal.execute.form',$row->id) }}">
      <i class="ri-play-line"></i> {{ __('تنفيذ') }}
    </a>
  @endif

  @if($canCancel)
    <form method="post" action="{{ route('assets.programs.disposal.cancel',$row->id) }}" class="d-inline"
          onsubmit="return confirm('{{ __('إلغاء الطلب؟') }}')">
      @csrf
      <button class="btn btn-outline-danger">
        <i class="ri-close-line"></i> {{ __('إلغاء') }}
      </button>
    </form>
  @endif
</div>
