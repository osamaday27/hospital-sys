<div>
    {{-- إضافة قسم جاهز --}}
    <div class="card shadow-sm mb-4 border-0 rounded-4">
        <div class="card-body">
            <h6 class="fw-bold text-dark mb-3"><i class="ri-layout-2-line"></i> إضافة قسم من القوالب الجاهزة</h6>
            <div class="d-flex gap-2">
                <select wire:model="selectedPreset" class="form-select w-auto">
                    <option value="">اختر قسمًا جاهزًا...</option>
                    @foreach(array_keys($availableSectionPresets) as $preset)
                        <option value="{{ $preset }}">{{ $preset }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-primary" wire:click="addPresetSection">
                    <i class="ri-add-circle-line"></i> إضافة
                </button>
            </div>
        </div>
    </div>

    {{-- إنشاء قسم جديد --}}
    <div class="card shadow-sm mb-4 border-0 rounded-4">
        <div class="card-body">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" wire:model="is_printable" id="printableSwitch">
                <label class="form-check-label fw-bold" for="printableSwitch">قابل للطباعة</label>
            </div>

            <div class="d-flex mb-3">
                <input type="text" wire:model="newSectionName" class="form-control rounded-start-3" placeholder="اسم القسم الجديد">
                <button class="btn btn-success rounded-end-3" wire:click="addSection">
                    <i class="ri-add-line"></i> إضافة قسم
                </button>
            </div>
        </div>
    </div>

    {{-- عرض الأقسام --}}
    @foreach($sections as $sIndex => $section)
        <div class="card mb-3 shadow-sm border-0 rounded-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong class="text-dark">{{ $section['section_name'] }}</strong>
                <button class="btn btn-sm btn-outline-danger" wire:click="removeSection({{ $sIndex }})">حذف القسم</button>
            </div>

            <div class="card-body bg-white">
                {{-- الحقول --}}
                @foreach($section['fields'] ?? [] as $fIndex => $field)
                    <div class="p-2 mb-2 border rounded-3 d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $field['label'] }}</strong>
                            <small class="text-muted">({{ $field['type'] }})</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" wire:click="removeField({{ $sIndex }}, {{ $fIndex }})">✕</button>
                    </div>
                @endforeach

                {{-- إضافة نص ثابت --}}
                <div class="mt-3 border-top pt-3">
                    <h6>إضافة نص ثابت</h6>
                    <div class="input-group mb-2">
                        <input type="text" wire:model="newStaticText" class="form-control" placeholder="نص ثابت داخل الشيت...">
                        <button class="btn btn-outline-primary" wire:click="addStaticText({{ $sIndex }})">+</button>
                    </div>
                </div>

                {{-- إضافة حقل جديد --}}
                <div class="mt-3 border-top pt-3">
                    <h6>إضافة حقل جديد</h6>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <input type="text" wire:model="newField.label" class="form-control" placeholder="اسم الحقل">
                        </div>
                        <div class="col-md-2">
                            <select wire:model="newField.type" class="form-select">
                                <option value="text">نص</option>
                                <option value="number">رقم</option>
                                <option value="date">تاريخ</option>
                                <option value="dropdown">قائمة</option>
                                <option value="radio">اختيار واحد</option>
                                <option value="checkbox">اختيار متعدد</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            @if(in_array($newField['type'], ['dropdown','radio']))
                                <input type="text" wire:model="newField.options" class="form-control" placeholder="خيار1, خيار2, خيار3">
                            @endif
                        </div>
                        <div class="col-md-2">
                            <input type="text" wire:model="newField.default_value" class="form-control" placeholder="قيمة افتراضية">
                        </div>
                        <div class="col-md-2">
                            <input type="text" wire:model="newField.source" class="form-control" placeholder="مصدر البيانات (اختياري)">
                        </div>
                        <div class="col-md-1 mt-2">
                            <button class="btn btn-success w-100" wire:click="addField({{ $sIndex }})">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- CKEditor Narrative --}}
    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-header bg-light">
            <strong><i class="ri-file-list-3-line"></i> Narrative / النص الطبي</strong>
        </div>
        <div class="card-body">
            <textarea id="narrative-editor" wire:model.lazy="narrative_text" class="form-control" rows="10"></textarea>
            <small class="text-muted d-block mt-2">
                يمكنك استخدام متغيرات الحقول مثل: {Patient Name}, {Eye}, {Anesthesia Type}
            </small>
        </div>
    </div>

    @push('scripts')
        <script>
            ClassicEditor
                .create(document.querySelector('#narrative-editor'))
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        Livewire.emit('updateNarrative', editor.getData());
                    });
                });
        </script>
    @endpush
</div>
