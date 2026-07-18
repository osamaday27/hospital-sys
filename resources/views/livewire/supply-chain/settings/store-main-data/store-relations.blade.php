<div class="p-3">
    {{-- كود اختيار المخازن المرتبطة --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ __('supply_chain_trans.add_related_stores') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">{{ __('supply_chain_trans.choose_related_stores') }}</label>
                    <select id="relatedStoresSelect" class="form-select" wire:model="selectedStores" multiple wire:ignore>
                        @foreach ($allOtherStores as $other)
                            <option value="{{ $other->id }}">
                                {{ $other->manual_code }} – {{ $other->name_ar }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary" wire:click="addRelations">
                        {{ __('supply_chain_trans.add') }}
                    </button>
                </div>
                @error('storeId')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- جدول عرض المخازن المرتبطة --}}
    <div class="card">
        <div class="card-header">
            <h5>{{ __('supply_chain_trans.related_stores') }}</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('supply_chain_trans.code') }}</th>
                        <th>{{ __('supply_chain_trans.name') }}</th>
                        <th>{{ __('supply_chain_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($relatedStores as $s)
                        <tr>
                            <td>{{ $s->manual_code }}</td>
                            <td>{{ $s->name_ar }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $s->id }})">
                                    {{ __('supply_chain_trans.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">{{ __('supply_chain_trans.no_related_stores') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- مودال تأكيد الحذف --}}
    <div wire:ignore.self class="modal fade" id="deleteModal3" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                               colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px">
                    </lord-icon>
                    <h4 class="mt-3">{{ __('call_center_trans.h4_delete') }}</h4>
                    <p>{{ __('call_center_trans.p_delete') }}</p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('call_center_trans.cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger" id="delete-record">
                            {{ __('call_center_trans.yes_delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- سكربت التهيئة --}}
    <script>
        document.addEventListener('livewire:load', function () {
            // تهيئة Select2 لقائمة المخازن المرتبطة
            function initSelect2() {
                $('#relatedStoresSelect').select2({
                    placeholder: "{{ __('supply_chain_trans.choose_related_stores') }}",
                    allowClear: true,
                    width: '100%'
                }).on('change', function () {
                    @this.set('selectedStores', $(this).val());
                });
            }

            // أول تحميل وبعد كل تحديث Livewire
            initSelect2();
            Livewire.hook('message.processed', initSelect2);

            // زر تأكيد الحذف
            document.getElementById('delete-record').addEventListener('click', function () {
                Livewire.emit('deleteConfirmed');
            });

            // افتتاح المودال
            window.addEventListener('open-delete-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('deleteModal3'));
                modal.show();
            });

            // إغلاق المودال
            window.addEventListener('close-delete-modal', () => {
                const modalEl = document.getElementById('deleteModal3');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        });
    </script>

</div>
