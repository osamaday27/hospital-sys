<div class="builder-tab">
    
    {{-- 🎯 Quick Actions Bar --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-primary text-white">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="ri-magic-line fs-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">إضافة سريعة</h6>
                                    <small class="opacity-75">اختر من القوالب الجاهزة أو أنشئ قسمك الخاص</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#presetsModal">
                                    <i class="ri-apps-line"></i> قوالب جاهزة
                                </button>
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#customSectionModal">
                                    <i class="ri-add-circle-line"></i> قسم مخصص
                                </button>
                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#aiAssistModal">
                                    <i class="ri-robot-2-line"></i> مساعد AI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📦 Sections Builder --}}
    <div class="row">
        <div class="col-lg-9">
            
            {{-- Sections List --}}
            @if(count($sections) > 0)
                <div id="sections-container" class="sortable-sections">
                    @foreach($sections as $sIndex => $section)
                        <div class="section-card card border-0 shadow-sm rounded-4 mb-4" data-section-id="{{ $section['section_id'] ?? $sIndex }}">
                            
                            {{-- Section Header --}}
                            <div class="card-header bg-{{ $section['color'] ?? 'primary' }} bg-gradient text-white d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center gap-3 flex-grow-1">
                                    <span class="drag-handle">
                                        <i class="ri-draggable fs-5"></i>
                                    </span>
                                    <i class="{{ $section['icon'] ?? 'ri-folder-2-line' }} fs-5"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">{{ $section['section_name'] }}</h6>
                                        @if(!empty($section['section_description']))
                                            <small class="opacity-75">{{ $section['section_description'] }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button wire:click="toggleSectionCollapse({{ $sIndex }})" class="btn btn-sm btn-light" title="طي/فتح">
                                        <i class="ri-{{ $section['collapsed'] ?? false ? 'arrow-down' : 'arrow-up' }}-s-line"></i>
                                    </button>
                                    <button wire:click="duplicateSection({{ $sIndex }})" class="btn btn-sm btn-light" title="نسخ">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                    <button wire:click="removeSection({{ $sIndex }})" class="btn btn-sm btn-danger" title="حذف">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Section Body --}}
                            @if(!($section['collapsed'] ?? false))
                                <div class="card-body p-4">
                                    
                                    {{-- Fields List --}}
                                    @if(count($section['fields'] ?? []) > 0)
                                        <div class="fields-list sortable-fields mb-4">
                                            @foreach($section['fields'] ?? [] as $fIndex => $field)
                                                <div class="field-item d-flex align-items-center gap-3 p-3 mb-2 bg-light rounded-3 border" data-field-id="{{ $field['field_id'] }}">
                                                    <span class="drag-handle">
                                                        <i class="ri-menu-line"></i>
                                                    </span>
                                                    
                                                    <div class="field-icon">
                                                        <i class="{{ $field['icon'] ?? 'ri-input-field' }} text-primary fs-5"></i>
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <strong class="text-dark">{{ $field['label'] }}</strong>
                                                            <span class="badge bg-secondary-subtle text-secondary">{{ $field['type'] }}</span>
                                                            @if($field['required'] ?? false)
                                                                <span class="badge bg-danger-subtle text-danger">
                                                                    <i class="ri-error-warning-line"></i> مطلوب
                                                                </span>
                                                            @endif
                                                            @if(!empty($field['source']))
                                                                <span class="badge bg-info-subtle text-info">
                                                                    <i class="ri-database-2-line"></i> {{ $field['source'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if(!empty($field['help_text']))
                                                            <small class="text-muted d-block mt-1">
                                                                <i class="ri-information-line"></i> {{ $field['help_text'] }}
                                                            </small>
                                                        @endif
                                                        @if(!empty($field['options']))
                                                            <small class="text-muted d-block mt-1">
                                                                الخيارات: {{ implode(', ', $field['options']) }}
                                                            </small>
                                                        @endif
                                                    </div>

                                                    <div class="field-actions d-flex gap-1">
                                                        <button wire:click="duplicateField({{ $sIndex }}, {{ $fIndex }})" class="btn btn-sm btn-outline-primary" title="نسخ الحقل">
                                                            <i class="ri-file-copy-line"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" title="تعديل" 
                                                            data-bs-toggle="modal" data-bs-target="#editFieldModal{{ $sIndex }}_{{ $fIndex }}">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                        <button wire:click="removeField({{ $sIndex }}, {{ $fIndex }})" class="btn btn-sm btn-outline-danger" title="حذف">
                                                            <i class="ri-close-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info border-0 rounded-3 text-center">
                                            <i class="ri-inbox-line fs-2 d-block mb-2"></i>
                                            لا توجد حقول في هذا القسم بعد
                                        </div>
                                    @endif

                                    {{-- Add Field Form --}}
                                    <div class="add-field-form border-top pt-4">
                                        <h6 class="fw-bold text-dark mb-3">
                                            <i class="ri-add-box-line text-success"></i> إضافة حقل جديد
                                        </h6>
                                        
                                        <div class="row g-3">
                                            {{-- Field Label --}}
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">اسم الحقل <span class="text-danger">*</span></label>
                                                <input type="text" wire:model="newField.label" class="form-control" placeholder="مثال: ضغط العين">
                                            </div>

                                            {{-- Field Type --}}
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold">النوع</label>
                                                <select wire:model="newField.type" class="form-select">
                                                    @foreach($fieldTypes as $type => $label)
                                                        <option value="{{ $type }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Options (for dropdown/radio/checkbox) --}}
                                            @if(in_array($newField['type'], ['dropdown', 'radio', 'checkbox']))
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold">الخيارات (افصل بفاصلة)</label>
                                                    <input type="text" wire:model="newField.options" class="form-control" placeholder="خيار1, خيار2, خيار3">
                                                </div>
                                            @endif

                                            {{-- Number Range (for number type) --}}
                                            @if($newField['type'] === 'number')
                                                <div class="col-md-1">
                                                    <label class="form-label fw-semibold">Min</label>
                                                    <input type="number" wire:model="newField.min" class="form-control" placeholder="0">
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label fw-semibold">Max</label>
                                                    <input type="number" wire:model="newField.max" class="form-control" placeholder="100">
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label fw-semibold">Step</label>
                                                    <input type="number" wire:model="newField.step" class="form-control" placeholder="0.1" step="0.01">
                                                </div>
                                            @endif

                                            {{-- Placeholder --}}
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold">Placeholder</label>
                                                <input type="text" wire:model="newField.placeholder" class="form-control" placeholder="مثال: أدخل القيمة">
                                            </div>

                                            {{-- Default Value --}}
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold">قيمة افتراضية</label>
                                                <input type="text" wire:model="newField.default_value" class="form-control">
                                            </div>

                                            {{-- Data Source --}}
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold">مصدر البيانات</label>
                                                <input type="text" wire:model="newField.source" class="form-control" placeholder="patient.name">
                                                <small class="text-muted">اختياري</small>
                                            </div>

                                            {{-- Help Text --}}
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">نص مساعد</label>
                                                <input type="text" wire:model="newField.help_text" class="form-control" placeholder="معلومات إضافية">
                                            </div>

                                            {{-- Checkboxes --}}
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">خيارات</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" wire:model="newField.required" id="fieldRequired{{ $sIndex }}">
                                                        <label class="form-check-label" for="fieldRequired{{ $sIndex }}">مطلوب</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" wire:model="newField.readonly" id="fieldReadonly{{ $sIndex }}">
                                                        <label class="form-check-label" for="fieldReadonly{{ $sIndex }}">للقراءة فقط</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" wire:model="newField.repeatable" id="fieldRepeatable{{ $sIndex }}">
                                                        <label class="form-check-label" for="fieldRepeatable{{ $sIndex }}">متعدد</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Add Button --}}
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button wire:click="addField({{ $sIndex }})" class="btn btn-success w-100">
                                                    <i class="ri-add-line"></i> إضافة الحقل
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Add Static Text --}}
                                    <div class="add-static-text border-top mt-4 pt-4">
                                        <h6 class="fw-bold text-dark mb-3">
                                            <i class="ri-text text-info"></i> إضافة نص ثابت
                                        </h6>
                                        <div class="input-group">
                                            <textarea wire:model="newStaticText" class="form-control" rows="2" 
                                                placeholder="أدخل نصًا ثابتًا يظهر في التقرير (مثل: ملاحظات، تعليمات، إلخ)"></textarea>
                                            <button wire:click="addStaticText({{ $sIndex }})" class="btn btn-outline-info">
                                                <i class="ri-add-line"></i> إضافة
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="empty-state text-center py-5">
                    <div class="card border-0 shadow-sm rounded-4 p-5">
                        <i class="ri-file-add-line text-primary" style="font-size: 80px;"></i>
                        <h4 class="fw-bold text-dark mt-4">ابدأ في بناء قالبك الطبي</h4>
                        <p class="text-muted mb-4">اختر من القوالب الجاهزة أو أنشئ أقسامك الخاصة</p>
                        <div class="d-flex gap-3 justify-content-center">
                            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#presetsModal">
                                <i class="ri-apps-line"></i> استعراض القوالب
                            </button>
                            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#customSectionModal">
                                <i class="ri-add-circle-line"></i> قسم مخصص
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- 📌 Sidebar: Tools & Quick Info --}}
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 100px;">
                
                {{-- Template Info Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="ri-information-line text-primary"></i> معلومات القالب
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">اسم القالب</label>
                            <input type="text" wire:model.lazy="template_name" class="form-control form-control-sm">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">الفئة</label>
                            <select wire:model="template_category" class="form-select form-select-sm">
                                <option value="">اختر الفئة</option>
                                <option value="Cataract">Cataract</option>
                                <option value="Glaucoma">Glaucoma</option>
                                <option value="Retina">Retina</option>
                                <option value="Cornea">Cornea</option>
                                <option value="Strabismus">Strabismus</option>
                                <option value="Oculoplasty">Oculoplasty</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold small">الوصف</label>
                            <textarea wire:model.lazy="template_description" class="form-control form-control-sm" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Statistics Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-3 bg-gradient-primary text-white">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="ri-bar-chart-line"></i> إحصائيات القالب
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>عدد الأقسام:</span>
                            <strong>{{ count($sections) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>عدد الحقول:</span>
                            <strong>{{ collect($sections)->sum(fn($s) => count($s['fields'] ?? [])) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>الإصدار:</span>
                            <strong>v{{ count($version_history) }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Quick Settings --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="ri-settings-3-line text-warning"></i> إعدادات سريعة
                        </h6>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="is_printable" id="printableSwitch">
                            <label class="form-check-label" for="printableSwitch">قابل للطباعة</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="is_public" id="publicSwitch">
                            <label class="form-check-label" for="publicSwitch">عام (مشارك)</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="auto_save" id="autoSaveSwitch">
                            <label class="form-check-label" for="autoSaveSwitch">حفظ تلقائي</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="enable_ai_suggestions" id="aiSwitch">
                            <label class="form-check-label" for="aiSwitch">اقتراحات AI</label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 📝 Narrative Editor Section --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-file-list-3-line"></i> النص الطبي (Operative Narrative)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="ckeditor-wrapper">
                        <textarea id="narrative-editor" wire:model.lazy="narrative_text" class="form-control border-0"></textarea>
                    </div>
                    <div class="p-3 bg-light border-top">
                        <small class="text-muted">
                            <i class="ri-information-line"></i> 
                            <strong>نصيحة:</strong> يمكنك استخدام متغيرات الحقول مثل: 
                            <code>{patient_name}</code>, <code>{eye}</code>, <code>{anesthesia_type}</code>
                            وسيتم استبدالها تلقائيًا عند إنشاء التقرير.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- 🎨 Modals --}}
@include('livewire.medical.partials.modals.presets-modal')
@include('livewire.medical.partials.modals.custom-section-modal')
@include('livewire.medical.partials.modals.ai-assist-modal')

<script src="https://cdn.ckeditor.com/ckeditor5/35.4.0/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // CKEditor Initialization
        ClassicEditor
            .create(document.querySelector('#narrative-editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 
                         'bulletedList', 'numberedList', '|', 'undo', 'redo'],
                language: 'ar'
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    @this.set('narrative_text', editor.getData());
                });
            })
            .catch(error => {
                console.error(error);
            });

        // Sortable for Sections
        const sectionsContainer = document.getElementById('sections-container');
        if (sectionsContainer) {
            new Sortable(sectionsContainer, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    const orderedIds = Array.from(sectionsContainer.children).map(el => el.dataset.sectionId);
                    @this.call('reorderSections', orderedIds);
                }
            });
        }

        // Sortable for Fields
        document.querySelectorAll('.sortable-fields').forEach(fieldsList => {
            new Sortable(fieldsList, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost'
            });
        });

    });
</script>
