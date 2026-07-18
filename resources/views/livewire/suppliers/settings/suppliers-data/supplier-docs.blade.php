<div class="p-3">
    <h5 class="mb-3">{{ __('suppliers.supplier_documents') }}</h5>

    {{-- رسالة تحذيرية إذا لم يُدخل المستخدم بيانات المورد --}}
    @unless($supplierId)
        <div class="alert alert-warning">
            {{ __('suppliers.please_enter_supplier_data_first') }}
        </div>
    @endunless

    @if($supplierId)
        <form wire:submit.prevent="save">
            <input type="hidden" wire:model="supplierId">

            <div class="row g-3 align-items-end">
                {{-- نوع الملف --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('suppliers.file_type') }} <span class="text-danger">*</span></label>
                    <select class="form-select" wire:model.defer="file_type">
                        <option value="">{{ __('suppliers.select_file_type') }}</option>
                        <option value="tax_card">{{ __('suppliers.tax_card') }}</option>
                        <option value="commercial_registration">{{ __('suppliers.commercial_registration') }}</option>
                        <option value="vat_certificate">{{ __('suppliers.vat_certificate') }}</option>
                        <option value="other">{{ __('suppliers.other') }}</option>
                    </select>
                    @error('file_type') <div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                {{-- اسم الملف --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('suppliers.document_name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model.defer="document_name">
                    @error('document_name') <div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                {{-- رفع الملف --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('suppliers.upload_file') }} <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" wire:model="file_upload">
                    @error('file_upload') <div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                {{-- زر الحفظ --}}
                <div class="col-12 text-end mt-2">
                    <button type="submit" class="btn btn-primary">
                        {{ __('suppliers.save') }}
                    </button>
                </div>
            </div>
        </form>

        <hr class="my-4">

        {{-- جدول المستندات --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>{{ __('suppliers.file_type') }}</th>
                        <th>{{ __('suppliers.document_name') }}</th>
                        <th>{{ __('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplierDocs as $doc)
                        <tr>
                            <td>{{ __('suppliers.' . $doc->file_type) }}</td>
                            <td>{{ $doc->file_name }}</td>
                            <td class="text-nowrap">
                                <a href="{{ asset('attachments/'.$doc->file_path) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-info">
                                    {{ __('suppliers.view') }}
                                </a>
                                <button wire:click="edit({{ $doc->id }})"
                                        class="btn btn-sm btn-warning">
                                    {{ __('general.edit') }}
                                </button>
                                <button wire:click="confirmDelete({{ $doc->id }})"
                                        class="btn btn-sm btn-danger">
                                    {{ __('general.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">
                                {{ __('suppliers.no_documents') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- مودال تأكيد الحذف --}}
        <div wire:ignore.self class="modal fade" id="deleteModal2" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h4>{{ __('suppliers.confirm_delete') }}</h4>
                        <p>{{ __('suppliers.delete_prompt') }}</p>
                        <div class="d-flex gap-2 justify-content-center mt-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                {{ __('suppliers.cancel') }}
                            </button>
                            <button type="button"
                                    class="btn btn-danger"
                                    data-bs-dismiss="modal"
                                    wire:click="delete">
                                {{ __('suppliers.yes_delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- هذا السكربت خارج @if ليتسجّل مرة واحدة عند التحميل --}}
<script>
document.addEventListener('livewire:load', () => {
    window.addEventListener('show-delete-modal2', () => {
        const el = document.getElementById('deleteModal2');
        if (el) new bootstrap.Modal(el).show();
    });
    window.addEventListener('hide-delete-modal2', () => {
        const el = document.getElementById('deleteModal2');
        const inst = bootstrap.Modal.getInstance(el);
        if (inst) inst.hide();
    });
    window.addEventListener('toast', event => {
        const toastEl = document.createElement('div');
        toastEl.classList.add('toast','align-items-center','text-bg-'+event.detail.type,'border-0');
        toastEl.setAttribute('role','alert');
        toastEl.innerHTML = `
          <div class="d-flex">
            <div class="toast-body">${event.detail.message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>`;
        document.getElementById('toastContainer').append(toastEl);
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    });
});
</script>
