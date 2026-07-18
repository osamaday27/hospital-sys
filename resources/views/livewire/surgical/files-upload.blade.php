<div class="container-fluid px-4 py-3">
    @if (session()->has('message'))
        <div class="toast show align-items-center text-white bg-success border-0 position-fixed top-4 end-4" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1000">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="mdi mdi-check-circle-outline me-2 fs-5"></i>
                    {{ session('message') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="card mb-5 border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px);">
        <div class="card-header bg-transparent border-0 py-3">
            <h3 class="mb-0 d-flex align-items-center text-gradient-primary">
                <i class="mdi mdi-cloud-upload-outline me-2"></i>
                {{ trans("surgical_trans.upload_file_for_operation") }}
            </h3>
        </div>

        <div class="card-body px-4 py-4">
            <!-- Dropzone -->
            <div class="dropzone mb-4 p-5 text-center border-2 border-dashed rounded-3"
                 x-data="{ isDragging: false, progress: 0, showProgress: false }"
                 x-on:livewire-upload-start="showProgress = true; progress = 0"
                 x-on:livewire-upload-finish="progress = 100; setTimeout(()=>showProgress=false, 600)"
                 x-on:livewire-upload-error="showProgress = false; progress = 0"
                 x-on:livewire-upload-progress="progress = $event.detail.progress"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false; $wire.upload('file', $event.dataTransfer.files[0])"
                 :class="{ 'border-primary bg-light': isDragging, 'border-secondary': !isDragging }">

                <i class="mdi mdi-cloud-upload-outline display-4 text-muted mb-3"></i>
                <h5 class="mb-2">{{ trans("surgical_trans.drag_drop_file") }}</h5>
                <p class="text-muted mb-3">{{ trans("surgical_trans.or") }}</p>

                <label class="btn btn-primary px-4 rounded-pill">
                    <i class="mdi mdi-folder-open-outline me-1"></i>
                    {{ trans("surgical_trans.browse_files") }}
                    <input type="file" class="d-none" wire:model="file">
                </label>

                @error('file')
                    <div class="mt-3 text-danger">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        {{ $message }}
                    </div>
                @enderror

                <!-- ✅ شريط التقدم -->
                <div class="mt-4" x-show="showProgress">
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar" role="progressbar"
                             :style="`width: ${progress}%;`"
                             x-text="progress + '%'"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- ✅ رسالة: تم اختيار ملف + الاسم والحجم والنوع + زر إلغاء -->
                <div class="mt-4" wire:loading.remove wire:target="file">
                    @if($file)
                        <div class="alert alert-info d-inline-flex align-items-center gap-2 py-2 px-3 rounded-pill mb-0">
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            <span>
                                تم اختيار:
                                <strong>{{ $selected_name }}</strong>
                                <small class="text-muted">({{ $selected_size }} KB • {{ $selected_type }})</small>
                            </span>
                            <button type="button" class="btn btn-sm btn-link text-danger ms-2 p-0" wire:click="resetSelected">
                                إلغاء
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- File Details -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control border-0 border-bottom" id="file_name"
                               placeholder="{{ trans("surgical_trans.file_name_optional") }}"
                               wire:model="file_name">
                        <label for="file_name" class="d-flex align-items-center">
                            <i class="mdi mdi-rename-box-outline me-2 text-muted"></i>
                            {{ trans("surgical_trans.file_name_optional") }}
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <textarea class="form-control border-0 border-bottom"
                                  placeholder="{{ trans("surgical_trans.notes") }}"
                                  id="notes" style="height: 100px"
                                  wire:model="notes"></textarea>
                        <label for="notes" class="d-flex align-items-center">
                            <i class="mdi mdi-note-text-outline me-2 text-muted"></i>
                            {{ trans("surgical_trans.notes") }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="file,save">
                    <span wire:loading.remove wire:target="file,save">
                        <i class="mdi mdi-upload me-2"></i>{{ trans("surgical_trans.upload_file") }}
                    </span>
                    <span wire:loading wire:target="file,save">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        جاري الرفع...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Uploaded Files Card -->
    <div class="card border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px);">
        <div class="card-header bg-transparent border-0 py-3">
            <h3 class="mb-0 d-flex align-items-center text-gradient-secondary">
                <i class="mdi mdi-folder-multiple-outline me-2"></i>
                {{ trans("surgical_trans.uploaded_files") }}
            </h3>
        </div>

        <div class="card-body px-0 pb-0">
            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th class="ps-4 py-3 rounded-start"><i class="mdi mdi-file-document-outline me-1"></i> {{ trans("surgical_trans.name") }}</th>
                            <th class="py-3"><i class="mdi mdi-note-outline me-1"></i> {{ trans("surgical_trans.notes") }}</th>
                            <th class="py-3"><i class="mdi mdi-calendar-clock-outline me-1"></i> {{ trans("surgical_trans.date") }}</th>
                            <th class="py-3"><i class="mdi mdi-file-outline me-1"></i> {{ trans("surgical_trans.size") }}</th>
                            <th class="pe-4 py-3 rounded-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($files as $f)
                            <tr class="border-bottom border-light">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                                            <i class="mdi mdi-file-{{ $f->icon_type ?? 'outline' }} fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $f->file_name ?: trans("surgical_trans.unnamed_file") }}</div>
                                            <small class="text-muted">{{ $f->file_type }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $f->notes }}">
                                        {{ $f->notes ?: '--' }}
                                    </div>
                                </td>
                                <td class="py-3">
                                    <small class="text-muted">{{ $f->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="py-3">
                                    <small class="text-muted">{{ $f->size_in_kb }} KB</small>
                                </td>
                                <td class="pe-4 py-3">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ asset($f->file_path) }}" target="_blank"
                                           class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-2"
                                           data-bs-toggle="tooltip" title="{{ trans("surgical_trans.download") }}">
                                            <i class="mdi mdi-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                wire:click="delete({{ $f->id }})"
                                                data-bs-toggle="tooltip" title="{{ trans("surgical_trans.delete") }}">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="my-4"><i class="mdi mdi-folder-open-outline display-4 text-light"></i></div>
                                    <h5 class="text-muted mb-3">{{ trans("surgical_trans.no_files_uploaded") }}</h5>
                                    <p class="text-muted">{{ trans("surgical_trans.start_uploading") }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($files->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $files->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<div wire:loading.flex class="position-fixed top-0 start-0 end-0 bottom-0 bg-dark bg-opacity-25 justify-content-center align-items-center" style="z-index: 9999; display: none;">
    <div class="spinner-grow text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<style>
    .text-gradient-primary{background:linear-gradient(90deg,#4f6beb 0%,#3a56d5 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    .text-gradient-secondary{background:linear-gradient(90deg,#6c757d 0%,#495057 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    .dropzone{transition:all .3s ease}
    .file-icon{width:40px;height:40px;display:flex;align-items:center;justify-content:center}
    .btn-icon{width:36px;height:36px;display:flex;align-items:center;justify-content:center}
</style>

<script>
    document.addEventListener('livewire:init', () => {
        const tips=[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tips.map(el => new bootstrap.Tooltip(el));
    });
</script>
