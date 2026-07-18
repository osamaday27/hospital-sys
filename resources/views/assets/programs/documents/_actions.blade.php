@php($id = $row->id)
<div class="btn-group" role="group">
  <button type="button" class="btn btn-sm btn-light js-show" data-id="{{ $id }}" title="@lang('assets.show')" aria-label="@lang('assets.show')"><i class="ri-eye-line"></i></button>
  <a href="{{ route('assets.programs.documents.edit',$id) }}" class="btn btn-sm btn-warning" title="@lang('assets.edit')" aria-label="@lang('assets.edit')"><i class="ri-edit-line"></i></a>
  <button type="button" class="btn btn-sm btn-danger js-del" data-id="{{ $id }}" data-name="{{ $row->doc_no ?? $row->doc_type }}" title="@lang('assets.delete')" aria-label="@lang('assets.delete')"><i class="ri-delete-bin-line"></i></button>
</div>
