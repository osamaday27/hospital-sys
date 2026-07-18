<div class="container-fluid p-4">
    <div class="row">
        <!-- قسم إضافة/تحرير القالب -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="mdi mdi-file-document-edit-outline me-2"></i>
                        {{ $editMode ? 'تعديل القالب' : 'إضافة قالب جديد' }}
                    </h5>
                </div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="mdi mdi-check-circle me-2"></i>
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="mdi mdi-alert-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session()->has('info'))
                        <div class="alert alert-info alert-dismissible fade show">
                            <i class="mdi mdi-information me-2"></i>
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <!-- اسم القالب -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-tag-outline me-1"></i>
                                اسم القالب <span class="text-danger">*</span>
                            </label>
                            <input type="text" wire:model.defer="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="مثال: تقرير فحص عام - قسم الباطنية">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الوصف -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-text-box-outline me-1"></i>
                                الوصف
                            </label>
                            <textarea wire:model.defer="description" class="form-control @error('description') is-invalid @enderror"
                                rows="2" placeholder="وصف مختصر للقالب وطريقة استخدامه"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- التصنيف -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="mdi mdi-shape-outline me-1"></i>
                                    التصنيف
                                </label>
                                <select wire:model.defer="category" class="form-select @error('category') is-invalid @enderror">
                                    <option value="">-- اختر التصنيف --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- التخصص -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="mdi mdi-stethoscope me-1"></i>
                                    التخصص الافتراضي
                                </label>
                                <select wire:model.defer="specialty_id" class="form-select @error('specialty_id') is-invalid @enderror">
                                    <option value="">-- اختر التخصص --</option>
                                    @foreach ($specialties as $specialty)
                                        <option value="{{ $specialty->id }}">{{ $specialty->name_ar }}</option>
                                    @endforeach
                                </select>
                                @error('specialty_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- التخصصات المسموحة -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-hospital-building me-1"></i>
                                التخصصات المسموح لها باستخدام القالب
                            </label>
                            <select wire:model.defer="selected_specialties" class="form-select" multiple size="3">
                                @foreach ($specialties as $specialty)
                                    <option value="{{ $specialty->id }}">{{ $specialty->name_ar }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">اضغط Ctrl للاختيار المتعدد</small>
                        </div>

                        <!-- المستخدمون المسموحون -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-account-multiple-outline me-1"></i>
                                المستخدمون المسموح لهم باستخدام القالب
                            </label>
                            <select wire:model.defer="selected_users" class="form-select" multiple size="3">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} 
                                        @if($user->specialty)
                                            ({{ $user->specialty->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">اضغط Ctrl للاختيار المتعدد</small>
                        </div>

                        <!-- محرر النص -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="mdi mdi-file-document-outline me-1"></i>
                                محتوى القالب <span class="text-danger">*</span>
                            </label>
                            <div class="border rounded" style="min-height: 300px;">
                                <div wire:ignore>
                                    <textarea id="template-editor" hidden>{{ $content }}</textarea>
                                    <div id="template-editor-container"></div>
                                </div>
                            </div>
                            @error('content')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                <i class="mdi mdi-information-outline"></i>
                                يمكنك استخدام المتغيرات: {patient_name}, {patient_age}, {doctor_name}, {visit_date}, {current_date}
                            </small>
                        </div>

                        <!-- خيارات القالب -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model.defer="is_global" id="isGlobal">
                                    <label class="form-check-label" for="isGlobal">
                                        <i class="mdi mdi-earth me-1"></i>
                                        قالب عام (متاح لجميع المستخدمين)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model.defer="is_active" id="isActive">
                                    <label class="form-check-label" for="isActive">
                                        <i class="mdi mdi-check-circle-outline me-1"></i>
                                        قالب نشط
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i>
                                {{ $editMode ? 'تحديث القالب' : 'حفظ القالب' }}
                            </button>

                            @if ($editMode)
                                <button type="button" wire:click="resetForm" class="btn btn-secondary">
                                    <i class="mdi mdi-close me-1"></i>
                                    إلغاء التحرير
                                </button>
                            @endif

                            <button type="button" wire:click="resetForm" class="btn btn-outline-danger">
                                <i class="mdi mdi-refresh me-1"></i>
                                مسح الحقول
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- قسم القوالب المحفوظة -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="mdi mdi-view-list me-2"></i>
                        القوالب المحفوظة
                        <span class="badge bg-primary">{{ $templatesCount }}</span>
                    </h5>
                </div>

                <div class="card-body">
                    <!-- أدوات البحث والفلترة -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <input type="text" wire:model.debounce.500ms="search" class="form-control"
                                    placeholder="بحث في القوالب...">
                            </div>
                        </div>

                        <div class="col-md-2 mb-2">
                            <select wire:model="filterBySpecialty" class="form-select">
                                <option value="">كل التخصصات</option>
                                @foreach ($specialties as $specialty)
                                    <option value="{{ $specialty->id }}">{{ $specialty->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-2">
                            <select wire:model="filterByCategory" class="form-select">
                                <option value="">كل التصنيفات</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-2">
                            <select wire:model="filterByStatus" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="1">نشط</option>
                                <option value="0">معطل</option>
                            </select>
                        </div>
                    </div>

                    @if($search || $filterBySpecialty || $filterByCategory || $filterByStatus !== '')
                        <div class="mb-2">
                            <button wire:click="clearFilters" class="btn btn-sm btn-outline-secondary">
                                <i class="mdi mdi-filter-remove-outline me-1"></i>
                                مسح الفلاتر
                            </button>
                        </div>
                    @endif

                    <!-- جدول القوالب -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th wire:click="sortBy('name')" style="cursor: pointer;">
                                        اسم القالب
                                        @if($sortBy === 'name')
                                            <i class="mdi mdi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </th>
                                    <th>التصنيف</th>
                                    <th>التخصص</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                        التاريخ
                                        @if($sortBy === 'created_at')
                                            <i class="mdi mdi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $template)
                                    <tr class="{{ !$template->is_active ? 'table-secondary' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-file-document-outline text-primary me-2 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold">{{ $template->name }}</div>
                                                    @if($template->description)
                                                        <small class="text-muted">{{ Str::limit($template->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($template->category)
                                                <span class="badge bg-info">{{ $template->category }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($template->specialty)
                                                {{ $template->specialty->name_ar }}
                                            @else
                                                <span class="text-muted">عام</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($template->is_global)
                                                <span class="badge bg-primary">
                                                    <i class="mdi mdi-earth"></i> عام
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="mdi mdi-lock"></i> خاص
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($template->is_active)
                                                <span class="badge bg-success">
                                                    <i class="mdi mdi-check-circle"></i> نشط
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="mdi mdi-pause-circle"></i> معطل
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($template->created_at)->format('Y/m/d') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button wire:click="preview({{ $template->id }})" 
                                                    class="btn btn-outline-info" 
                                                    title="معاينة">
                                                    <i class="mdi mdi-eye-outline"></i>
                                                </button>
                                                
                                                <button wire:click="edit({{ $template->id }})" 
                                                    class="btn btn-outline-primary" 
                                                    title="تعديل">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>
                                                
                                                <button wire:click="duplicate({{ $template->id }})" 
                                                    class="btn btn-outline-secondary" 
                                                    title="نسخ">
                                                    <i class="mdi mdi-content-copy"></i>
                                                </button>
                                                
                                                <button wire:click="toggleStatus({{ $template->id }})" 
                                                    class="btn btn-outline-{{ $template->is_active ? 'warning' : 'success' }}" 
                                                    title="{{ $template->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                    <i class="mdi mdi-{{ $template->is_active ? 'pause' : 'play' }}-circle-outline"></i>
                                                </button>
                                                
                                                <button wire:click="confirmDelete({{ $template->id }})" 
                                                    class="btn btn-outline-danger" 
                                                    title="حذف">
                                                    <i class="mdi mdi-delete-outline"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="mdi mdi-file-document-remove-outline fs-1 d-block mb-2"></i>
                                            لا توجد قوالب متاحة
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تأكيد الحذف -->
<div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                    تأكيد الحذف
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">هل أنت متأكد من حذف هذا القالب؟</p>
                <p class="text-danger small mb-0">لا يمكن التراجع عن هذا الإجراء</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>
                    إلغاء
                </button>
                <button wire:click="delete" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="mdi mdi-delete me-1"></i>
                    حذف نهائياً
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal المعاينة -->
<div class="modal fade" id="previewModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-eye-outline me-2"></i>
                    معاينة القالب: {{ $previewName }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light border rounded">
                    {!! $previewContent !!}
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let templateEditor;

    document.addEventListener('livewire:load', function() {
        initializeEditor();

        Livewire.on('setEditorContent', content => {
            if (templateEditor) {
                templateEditor.setData(content || '');
            }
        });

        Livewire.on('resetEditor', () => {
            if (templateEditor) {
                templateEditor.setData('');
            }
        });

        Livewire.on('refreshEditor', () => {
            if (!templateEditor) initializeEditor();
        });
    });

    function initializeEditor() {
        if (templateEditor) return; // ✅ يمنع إعادة إنشاء المحرر مرتين

        ClassicEditor
            .create(document.querySelector('#template-editor-container'), {
                language: 'ar',
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'fontSize', 'fontColor', '|',
                        'alignment', 'outdent', 'indent', '|',
                        'bulletedList', 'numberedList', '|',
                        'link', 'insertTable', 'blockQuote', '|',
                        'undo', 'redo'
                    ]
                }
            })
            .then(editor => {
                templateEditor = editor;

                editor.model.document.on('change:data', () => {
                    @this.set('content', editor.getData());
                });
            })
            .catch(error => {
                console.error('❌ خطأ في تهيئة CKEditor:', error);
            });
    }
</script>

<style>
    .ck-editor__editable {
        min-height: 300px;
    }
    
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
</style>