{{-- resources/views/assets/programs/assignments/_actions.blade.php --}}
@php
  // لو لم يصل من الكنترولر سيكون true افتراضياً لـ assigned
  $canReturn = $canReturn ?? ($row->status === 'assigned');

  // إغلاق التعديل في حالة سجل الإرجاع
  $isReturnRecord = ($row->status === 'returned' || !empty($row->parent_id));
@endphp

<div class="btn-group" role="group">
  @if(!$isReturnRecord)
    <a href="{{ route('assets.programs.assignments.edit', $row->id) }}" class="btn btn-sm btn-outline-primary">
      <i class="ri-edit-line"></i> @lang('assets.edit')
    </a>
  @else
    <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="لا يمكن تعديل سجل الإرجاع">
      <i class="ri-edit-line"></i> @lang('assets.edit')
    </button>
  @endif

  @if($canReturn)
    <button type="button" class="btn btn-sm btn-outline-success js-return"
            data-id="{{ $row->id }}"
            data-remaining="{{ $remainingToReturn ?? 1 }}">
      <i class="ri-logout-box-r-line"></i> @lang('assets.return_btn')
    </button>
  @endif

  <button type="button" class="btn btn-sm btn-outline-danger js-del" data-id="{{ $row->id }}">
    <i class="ri-delete-bin-6-line"></i> @lang('assets.delete')
  </button>
</div>
