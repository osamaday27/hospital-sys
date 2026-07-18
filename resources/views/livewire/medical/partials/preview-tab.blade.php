<div class="preview-tab">
    
    <div class="row">
        <div class="col-lg-10 mx-auto">
            
            {{-- Preview Header --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-secondary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <i class="ri-eye-line"></i> معاينة القالب
                            </h5>
                            <p class="mb-0 opacity-75">{{ $template_name }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button onclick="window.print()" class="btn btn-light btn-sm">
                                <i class="ri-printer-line"></i> طباعة
                            </button>
                            <button class="btn btn-light btn-sm">
                                <i class="ri-file-pdf-line"></i> PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preview Content --}}
            <div class="card border-0 shadow-lg rounded-4 p-5 bg-white" id="printable-area">
                
                {{-- Header --}}
                <div class="text-center mb-5 pb-4 border-bottom">
                    <h3 class="fw-bold text-primary mb-1">EyeWorld Hospital</h3>
                    <p class="text-muted mb-0">Giza, Egypt | Prof. Ihab Saad Othman, MD, FRCS</p>
                </div>

                {{-- Title --}}
                <div class="text-center mb-5">
                    <h4 class="fw-bold text-dark">{{ $template_name }}</h4>
                    <small class="text-muted">Operative Report Template</small>
                </div>

                {{-- Sections --}}
                @foreach($sections as $section)
                    <div class="section-preview mb-5">
                        <div class="d-flex align-items-center gap-3 mb-3 pb-2 border-bottom border-{{ $section['color'] ?? 'primary' }}">
                            <i class="{{ $section['icon'] ?? 'ri-folder-2-line' }} text-{{ $section['color'] ?? 'primary' }} fs-4"></i>
                            <h5 class="fw-bold text-dark mb-0">{{ $section['section_name'] }}</h5>
                        </div>

                        <div class="section-fields">
                            @foreach($section['fields'] ?? [] as $field)
                                
                                @if($field['type'] === 'static')
                                    <p class="text-muted mb-3">{!! nl2br(e($field['label'])) !!}</p>

                                @elseif($field['type'] === 'mixed_text')
                                    <div class="mixed-text-field mb-4 p-3 bg-light rounded-3">
                                        @php
                                            $content = $field['content'];
                                            $inlineFields = $field['inline_fields'] ?? [];
                                            
                                            foreach ($inlineFields as $fieldId => $fieldData) {
                                                $placeholder = $fieldData['placeholder'] ?? '';
                                                
                                                if ($fieldData['type'] === 'dropdown') {
                                                    $select = '<select class="form-select form-select-sm d-inline-block w-auto mx-1" style="vertical-align: middle;">';
                                                    foreach ($fieldData['options'] as $option) {
                                                        $select .= '<option>' . $option . '</option>';
                                                    }
                                                    $select .= '</select>';
                                                    $content = str_replace($placeholder, $select, $content);
                                                }
                                                
                                                elseif ($fieldData['type'] === 'radio') {
                                                    $radio = '<span class="btn-group btn-group-sm mx-1" role="group" style="vertical-align: middle;">';
                                                    foreach ($fieldData['options'] as $index => $option) {
                                                        $id = $fieldId . '_' . $index;
                                                        $radio .= '<input type="radio" class="btn-check" name="' . $fieldId . '" id="' . $id . '">';
                                                        $radio .= '<label class="btn btn-outline-primary" for="' . $id . '">' . $option . '</label>';
                                                    }
                                                    $radio .= '</span>';
                                                    $content = str_replace($placeholder, $radio, $content);
                                                }
                                                
                                                elseif ($fieldData['type'] === 'input') {
                                                    $input = '<input type="text" class="form-control form-control-sm d-inline-block w-auto mx-1" placeholder="' . $fieldData['placeholder'] . '" style="vertical-align: middle; min-width: 150px;">';
                                                    $content = str_replace($fieldData['original'], $input, $content);
                                                }
                                            }
                                        @endphp
                                        
                                        <div class="mixed-content">{!! $content !!}</div>
                                    </div>

                                @elseif($field['type'] === 'heading')
                                    <h6 class="fw-bold text-dark mt-4 mb-2">{{ $field['label'] }}</h6>

                                @elseif($field['type'] === 'divider')
                                    <hr class="my-4">

                                @elseif($field['type'] === 'dropdown')
                                    <div class="mb-3">
                                        <label class="fw-semibold text-dark d-block mb-2">
                                            {{ $field['label'] }}
                                            @if($field['required'] ?? false)<span class="text-danger">*</span>@endif
                                        </label>
                                        <select class="form-select form-select-sm w-auto">
                                            <option value="">-- اختر --</option>
                                            @foreach($field['options'] ?? [] as $opt)
                                                <option>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                @elseif($field['type'] === 'radio')
                                    <div class="mb-3">
                                        <label class="fw-semibold text-dark d-block mb-2">
                                            {{ $field['label'] }}
                                            @if($field['required'] ?? false)<span class="text-danger">*</span>@endif
                                        </label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($field['options'] ?? [] as $opt)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="{{ $field['field_id'] }}" id="{{ $field['field_id'] }}_{{ $loop->index }}">
                                                    <label class="form-check-label" for="{{ $field['field_id'] }}_{{ $loop->index }}">{{ $opt }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @elseif($field['type'] === 'checkbox')
                                    <div class="mb-3">
                                        <label class="fw-semibold text-dark d-block mb-2">{{ $field['label'] }}</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($field['options'] ?? [] as $opt)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">{{ $opt }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @elseif($field['type'] === 'date')
                                    <div class="mb-3">
                                        <label class="fw-semibold text-dark d-block mb-2">{{ $field['label'] }}</label>
                                        <input type="date" class="form-control form-control-sm w-auto">
                                    </div>

                                @elseif($field['type'] === 'number')
                                    <div class="mb-3">
                                        <label class="fw-semibold text-dark d-block mb-2">{{ $field['label'] }}</label>
                                        <input type="number" class="form-control form-control-sm w-auto" 
                                            min="{{ $field['min'] ?? '' }}" 
                                            max="{{ $field['max'] ?? '' }}" 
                                            step="{{ $field['step'] ?? '1' }}">
                                    </div>

                                @elseif($field['type'] === 'textarea')
                                    <div class="mb-3">
                                        <label class="fw-semibold text-dark d-block mb-2">{{ $field['label'] }}</label>
                                        <textarea class="form-control form-control-sm" rows="3"></textarea>
                                    </div>

                                @elseif($field['type'] === 'signature')
                                    <div class="mb-3">
                                        <label class="fw-semibold text-dark d-block mb-2">{{ $field['label'] }}</label>
                                        <div class="border rounded-3 p-3 bg-light text-center" style="height: 120px;">
                                            <i class="ri-pen-nib-line fs-2 text-muted"></i>
                                            <p class="text-muted mb-0 mt-2 small">منطقة التوقيع</p>
                                        </div>
                                    </div>

                                @else
                                    <div class="mb-3">
                                        <label class="fw-semibold text-dark d-block mb-2">{{ $field['label'] }}</label>
                                        <input type="text" class="form-control form-control-sm" 
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            {{ ($field['readonly'] ?? false) ? 'readonly' : '' }}>
                                    </div>
                                @endif

                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Narrative --}}
                @if(!empty($narrative_text))
                    <div class="mt-5 pt-4 border-top">
                        <div class="bg-light rounded-4 p-4">
                            <h5 class="fw-bold text-dark mb-3">
                                <i class="ri-file-list-3-line text-primary"></i> Operative Narrative
                            </h5>
                            <div>{!! $narrative_text !!}</div>
                        </div>
                    </div>
                @endif

                {{-- Signature Block --}}
                <div class="mt-5 pt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="border-top border-2 border-dark pt-2 mt-5">
                                <p class="fw-bold mb-0">Surgeon Signature</p>
                                <small class="text-muted">Date & Time</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-top border-2 border-dark pt-2 mt-5">
                                <p class="fw-bold mb-0">Witness</p>
                                <small class="text-muted">Date & Time</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

@push('styles')
<style>
    @media print {
        .builder-header, .btn, .card:first-child {
            display: none !important;
        }
        #printable-area {
            box-shadow: none !important;
        }
    }
</style>
@endpush