<div>
    {{-- ✅ التنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔹 التابات --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'list' ? 'active' : '' }}"
                    wire:click="$set('activeTab','list')">
                <i class="mdi mdi-table"></i> {{ __('medicalreports.list_tab') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'form' ? 'active' : '' }}"
                    wire:click="createNew">
                <i class="mdi mdi-plus-circle-outline"></i>
                {{ $report_id ? __('medicalreports.edit_tab') : __('medicalreports.create_tab') }}
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- 📋 قائمة التقارير --}}
        @if ($activeTab === 'list')
            <div class="card shadow-sm rounded-4 stylish-card">
                <div class="card-header bg-gradient-primary fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="mdi mdi-table"></i> {{ __('medicalreports.list_title') }}</span>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control w-50"
                               placeholder="{{ __('medicalreports.search_placeholder') }}"
                               wire:model.debounce.500ms="search">
                        <button wire:click="createNew"
                                class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="mdi mdi-plus-circle-outline"></i> {{ __('medicalreports.add_new') }}
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <table class="table table-hover text-center mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('medicalreports.title') }}</th>
                                <th>{{ __('medicalreports.device_type') }}</th>
                                <th>{{ __('medicalreports.specialty') }}</th>
                                <th>{{ __('medicalreports.status') }}</th>
                                <th>{{ __('medicalreports.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>{{ $report->getTranslation('title', app()->getLocale()) }}</td>
                                    <td>{{ optional($report->deviceType)->getTranslation('name', app()->getLocale()) }}</td>
                                    <td>{{ optional($report->specialty)->{app()->getLocale() == 'ar' ? 'name_ar' : 'name_en'} }}</td>
                                    <td>
                                        <span class="badge {{ $report->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ __('medicalreports.' . $report->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{-- 👁️ زر عرض --}}
                                        <button wire:click="show({{ $report->id }})"
                                                class="btn btn-sm btn-info rounded-pill">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        {{-- ✏️ زر تعديل --}}
                                        <button wire:click="edit({{ $report->id }})"
                                                class="btn btn-sm btn-warning rounded-pill">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        {{-- 🗑️ زر حذف --}}
                                        <button onclick="confirmDelete({{ $report->id }})"
                                                class="btn btn-sm btn-danger rounded-pill">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        🚫 {{ __('medicalreports.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $reports->links() }}
                </div>
            </div>
        @endif

        {{-- 📝 نموذج الإنشاء / التعديل --}}
        @if ($activeTab === 'form')
            <div class="card shadow-lg rounded-4 stylish-card">
                <div class="card-header bg-gradient-success fw-bold">
                    <i class="mdi mdi-pencil-box-outline me-2"></i>
                    {{ $report_id ? __('medicalreports.edit_title') : __('medicalreports.create_title') }}
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save" class="row g-3">

                        {{-- عنوان التقرير AR --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('medicalreports.title_ar') }}</label>
                            <input type="text" class="form-control" wire:model.defer="title.ar">
                        </div>

                        {{-- عنوان التقرير EN --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('medicalreports.title_en') }}</label>
                            <input type="text" class="form-control" wire:model.defer="title.en">
                        </div>

                        {{-- نوع الجهاز --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('medicalreports.device_type') }}</label>
                            <select class="form-select" wire:model.defer="device_type_id">
                                <option value="">{{ __('general.select') }}</option>
                                @foreach($deviceTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->getTranslation('name', app()->getLocale()) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- التخصص --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('medicalreports.specialty') }}</label>
                            <select class="form-select" wire:model.defer="specialty_id">
                                <option value="">{{ __('general.select') }}</option>
                                @foreach($specialties as $spec)
                                    <option value="{{ $spec->id }}">{{ app()->getLocale() == 'ar' ? $spec->name_ar : $spec->name_en }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- المحرر (Summernote) --}}
                        <div class="col-md-12">
                            <label class="form-label">{{ __('medicalreports.template') }}</label>
                            <textarea id="summernoteEditor" wire:model.defer="template"></textarea>
                        </div>

                        {{-- الحالة --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('medicalreports.status') }}</label>
                            <select class="form-select" wire:model.defer="status">
                                <option value="active">{{ __('medicalreports.active') }}</option>
                                <option value="inactive">{{ __('medicalreports.inactive') }}</option>
                            </select>
                        </div>

                        {{-- الأزرار --}}
                        <div class="col-12 d-flex gap-2 mt-3">
                            <button class="btn btn-success rounded-pill px-4 shadow-sm">
                                <i class="mdi mdi-content-save"></i>
                                {{ $report_id ? __('general.update') : __('general.save') }}
                            </button>
                            <button type="button" wire:click="$set('activeTab','list')"
                                    class="btn btn-secondary rounded-pill px-4 shadow-sm">
                                <i class="mdi mdi-cancel"></i> {{ __('general.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    {{-- ✅ Modal عرض التقرير --}}
    @if($showReportModal && $currentReport)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="mdi mdi-file-document-outline me-2"></i>
                            {{ $currentReport->getTranslation('title', app()->getLocale()) }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showReportModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>{{ __('medicalreports.device_type') }}:</strong> {{ optional($currentReport->deviceType)->getTranslation('name', app()->getLocale()) }}</p>
                        <p><strong>{{ __('medicalreports.specialty') }}:</strong> {{ optional($currentReport->specialty)->{app()->getLocale() == 'ar' ? 'name_ar' : 'name_en'} }}</p>
                        <hr>
                        <div class="p-3 border rounded bg-light">
                            {!! $currentReport->template !!}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm"
                                wire:click="$set('showReportModal', false)">
                            <i class="mdi mdi-close"></i> {{ __('general.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ✅ Summernote Editor --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
document.addEventListener('livewire:load', function () {
    $('#summernoteEditor').summernote({
        height: 300,
        callbacks: {
            onChange: function(contents) {
                @this.set('template', contents);
            }
        }
    });
    Livewire.hook('message.processed', () => {
        if (!$('#summernoteEditor').next('.note-editor').length) {
            $('#summernoteEditor').summernote({
                height: 300,
                callbacks: {
                    onChange: function(contents) {
                        @this.set('template', contents);
                    }
                }
            });
        }
    });
});
</script>

{{-- ✅ SweetAlert2 --}}
<script>
function confirmDelete(id) {
    Swal.fire({
        title: '{{ __("medicalreports.delete_title") }}',
        text: '{{ __("medicalreports.delete_text") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#0d6efd',
        confirmButtonText: '{{ __("medicalreports.delete_confirm") }}',
        cancelButtonText: '{{ __("medicalreports.cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.emit('deleteConfirmed', id);
            Swal.fire('{{ __("medicalreports.deleted_title") }}',
                      '{{ __("medicalreports.deleted_msg") }}', 'success');
        }
    })
}
</script>
