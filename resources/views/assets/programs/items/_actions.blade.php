@php($id = $row->id)
<div class="btn-group" role="group">
  <button type="button" class="btn btn-sm btn-light js-show" data-id="{{ $id }}" title="@lang('assets.show')" aria-label="@lang('assets.show')"><i class="ri-eye-line"></i></button>
  <a href="{{ route('assets.programs.items.edit',$id) }}" class="btn btn-sm btn-warning" title="@lang('assets.edit')" aria-label="@lang('assets.edit')"><i class="ri-edit-line"></i></a>
  <a href="{{ route('assets.programs.items.print',$id) }}" target="_blank" class="btn btn-sm btn-secondary" title="@lang('assets.print_card')" aria-label="@lang('assets.print_card')"><i class="ri-printer-line"></i></a>
  <button type="button" class="btn btn-sm btn-danger js-del" data-id="{{ $id }}" data-name="{{ $row->name_ar ?? $row->name_en ?? $row->code }}" title="@lang('assets.delete')" aria-label="@lang('assets.delete')"><i class="ri-delete-bin-line"></i></button>
</div>
