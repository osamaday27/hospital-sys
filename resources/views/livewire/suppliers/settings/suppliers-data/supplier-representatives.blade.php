<div class="p-3">
    <h5 class="mb-3">{{ __('suppliers.supplier_tab_representatives') }}</h5>

    {{-- خطأ في حال لم يتم تمرير المعرف --}}
    @error('supplierId')
        <div class="text-danger small">{{ $message }}</div>
    @enderror

    {{-- نموذج الإضافة والتعديل --}}
    <form wire:submit.prevent="save">
        {{-- تمرير supplierId من الصفحة السابقة --}}
        <input type="hidden" wire:model="supplierId">

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.name_ar') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model.defer="name_ar">
                @error('name_ar') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.name_en') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model.defer="name_en">
                @error('name_en') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.job_title') }}</label>
                <input type="text" class="form-control" wire:model.defer="job_title">
                @error('job_title') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.email') }}</label>
                <input type="email" class="form-control" wire:model.defer="email">
                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('suppliers.phone') }}</label>
                <input type="text" class="form-control" wire:model.defer="phone">
                @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('general.status') }}</label>
                <select class="form-select" wire:model.defer="status">
                    <option value="active">{{ __('suppliers.active') }}</option>
                    <option value="inactive">{{ __('suppliers.inactive') }}</option>
                </select>
                @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 text-end mt-3">
                <button type="submit" class="btn btn-success">
                    {{ __('general.save') }}
                </button>
            </div>
        </div>
    </form>

    <hr class="my-4">

    {{-- جدول عرض المندوبين --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>{{ __('suppliers.name_ar') }}</th>
                    <th>{{ __('suppliers.name_en') }}</th>
                    <th>{{ __('suppliers.job_title') }}</th>
                    <th>{{ __('suppliers.email') }}</th>
                    <th>{{ __('suppliers.phone') }}</th>
                    <th>{{ __('general.status') }}</th>
                    <th>{{ __('general.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($representatives as $rep)
                    <tr>
                        <td>{{ $rep->name_ar }}</td>
                        <td>{{ $rep->name_en }}</td>
                        <td>{{ $rep->job_title }}</td>
                        <td>{{ $rep->email }}</td>
                        <td>{{ $rep->phone }}</td>
                        <td>
                            <span class="badge {{ $rep->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $rep->status === 'active' ? __('suppliers.active') : __('suppliers.inactive') }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" wire:click="edit({{ $rep->id }})">
                                {{ __('general.edit') }}
                            </button>
                            <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $rep->id }})">
                                {{ __('general.delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- مودال تأكيد الحذف --}}
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h4>{{ __('suppliers.confirm_delete') }}</h4>
                    <p>{{ __('suppliers.delete_prompt') }}</p>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('general.cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="delete">
                            {{ __('suppliers.yes_delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast container لرسائل النجاح --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>

    {{-- سكربت المودال والـ Toast --}}
    <script>
        document.addEventListener('livewire:load', () => {
            // فتح/إغلاق مودال الحذف
            window.addEventListener('show-delete-modal', () => {
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
            window.addEventListener('hide-delete-modal', () => {
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            });

            // عرض رسالة النجاح
            window.addEventListener('notify', event => {
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
</div>
