<!-- نموذج تأكيد الحذف -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                    colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                <h4 class="mt-3">{{ trans('call_center_trans.h4_delete') }}</h4>
                <p>{{ trans('call_center_trans.p_delete') }}</p>
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <form action="" method="post" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="delete-id">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('call_center_trans.cancel') }}</button>
                        <button type="button" class="btn btn-danger" id="delete-record">{{ trans('call_center_trans.yes_delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
