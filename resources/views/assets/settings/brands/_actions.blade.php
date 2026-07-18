@php($id = $x->id)
<div class="btn-group" role="group">
  <button type="button" class="btn btn-sm btn-light js-show" data-id="{{ $id }}"><i class="ri-eye-line"></i></button>
  <a href="{{ route('assets.settings.brands.edit',$id) }}" class="btn btn-sm btn-warning"><i class="ri-edit-line"></i></a>
  <button type="button" class="btn btn-sm btn-danger js-del" data-id="{{ $id }}" data-name="{{ $x->name }}"><i class="ri-delete-bin-line"></i></button>
</div>
