<div class="d-flex gap-2">
    <a href="{{ route('hr_appraisal.show', $appraisal->id) }}" class="btn btn-sm btn-info" title="{{ trans('hr.view') }}">
        <i class="ri-eye-line"></i>
    </a>
    <a href="{{ route('hr_appraisal.edit', $appraisal->id) }}" class="btn btn-sm btn-warning" title="{{ trans('hr.edit') }}">
        <i class="ri-pencil-line"></i>
    </a>
    <button type="button" class="btn btn-sm btn-danger" title="{{ trans('hr.delete') }}" 
            onclick="deleteFunction({{ $appraisal->id }})">
        <i class="ri-delete-bin-line"></i>
    </button>
</div> 