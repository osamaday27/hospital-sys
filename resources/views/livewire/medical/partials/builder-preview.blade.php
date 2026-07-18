<div class="card border-0 rounded-4 shadow-sm p-4 bg-white" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-primary mb-0">
            🩺 معاينة الشيت الطبي - {{ $template->template_name }}
        </h5>
        <span class="badge bg-secondary">
            @if($is_printable)
                قابل للطباعة ✅
            @else
                غير قابل للطباعة ❌
            @endif
        </span>
    </div>

    {{-- عرض الأقسام --}}
    @foreach($sections as $section)
        <div class="mb-4 border rounded-4 p-3 bg-light-subtle">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                <i class="ri-folder-2-line text-primary"></i>
                {{ $section['section_name'] }}
            </h6>

            @foreach($section['fields'] ?? [] as $field)
                <div class="mb-2">
                    @if($field['type'] === 'static')
                        <p class="text-muted small mb-1">
                            {!! nl2br(e($field['label'])) !!}
                        </p>
                    @elseif($field['type'] === 'dropdown')
                        <div class="mb-2">
                            <label class="fw-bold">{{ $field['label'] }}</label><br>
                            <select class="form-select form-select-sm w-auto d-inline-block">
                                @foreach($field['options'] ?? [] as $opt)
                                    <option>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($field['type'] === 'radio')
                        <div class="mb-2">
                            <label class="fw-bold">{{ $field['label'] }}:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($field['options'] ?? [] as $opt)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field['field_id'] }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($field['type'] === 'checkbox')
                        <div class="mb-2">
                            <label class="fw-bold">{{ $field['label'] }}:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($field['options'] ?? [] as $opt)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($field['type'] === 'date')
                        <div class="mb-2">
                            <label class="fw-bold">{{ $field['label'] }}:</label>
                            <input type="date" class="form-control form-control-sm w-auto d-inline-block">
                        </div>
                    @elseif($field['type'] === 'number')
                        <div class="mb-2">
                            <label class="fw-bold">{{ $field['label'] }}:</label>
                            <input type="number" class="form-control form-control-sm w-auto d-inline-block">
                        </div>
                    @else
                        <div class="mb-2">
                            <label class="fw-bold">{{ $field['label'] }}:</label>
                            <input type="text" class="form-control form-control-sm w-auto d-inline-block" placeholder="{{ $field['default_value'] ?? '' }}">
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    {{-- 🩺 عرض النص الطبي (Narrative) --}}
    @if(!empty($narrative_text))
        <div class="card border-0 shadow-sm bg-light mt-4">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="ri-file-list-3-line"></i> Narrative / النص الطبي
            </div>
            <div class="card-body bg-white">
                {!! $narrative_text !!}
            </div>
        </div>
    @endif
</div>
