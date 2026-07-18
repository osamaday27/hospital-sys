{{-- Presets Modal - القوالب الجاهزة --}}
<div class="modal fade" id="presetsModal" tabindex="-1" aria-labelledby="presetsModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            
            {{-- Modal Header --}}
            <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title fw-bold" id="presetsModalLabel">
                    <i class="ri-apps-line me-2"></i> القوالب الجاهزة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4" style="background: #f8f9fa;">
                
                {{-- Search Bar --}}
                <div class="mb-4">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="ri-search-line text-muted"></i>
                        </span>
                        <input type="text" 
                            wire:model.live="search_query" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="ابحث في القوالب...">
                    </div>
                </div>

                {{-- Presets Grid --}}
                <div class="row g-4">
                    @foreach($availableSectionPresets as $presetName => $preset)
                        <div class="col-md-6 col-lg-4">
                            <div class="preset-card card border-0 shadow-sm h-100 overflow-hidden" 
                                style="cursor: pointer; transition: all 0.3s ease;">
                                
                                {{-- Card Header with Icon --}}
                                <div class="card-header border-0 text-white p-4" 
                                    style="background: linear-gradient(135deg, 
                                        @if($preset['color'] === 'primary') #667eea, #764ba2
                                        @elseif($preset['color'] === 'success') #11998e, #38ef7d
                                        @elseif($preset['color'] === 'info') #4facfe, #00f2fe
                                        @elseif($preset['color'] === 'warning') #f093fb, #f5576c
                                        @elseif($preset['color'] === 'danger') #fa709a, #fee140
                                        @else #868f96, #596164
                                        @endif
                                    );">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="preset-icon-wrapper bg-white bg-opacity-25 rounded-3 p-3">
                                            <i class="{{ $preset['icon'] ?? 'ri-folder-2-line' }} fs-2"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $presetName }}</h6>
                                            <small class="opacity-75">
                                                <i class="ri-list-check"></i> 
                                                {{ count($preset['fields'] ?? []) }} حقول
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card Body with Fields Preview --}}
                                <div class="card-body bg-white p-4">
                                    <h6 class="fw-semibold text-dark mb-3 small text-uppercase">
                                        <i class="ri-checkbox-circle-line text-success"></i> 
                                        الحقول المتضمنة:
                                    </h6>
                                    
                                    <div class="fields-preview-list">
                                        @foreach(array_slice($preset['fields'] ?? [], 0, 5) as $field)
                                            <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light rounded-2">
                                                <i class="{{ $field['icon'] ?? 'ri-checkbox-blank-circle-line' }} text-{{ $preset['color'] ?? 'primary' }} fs-6"></i>
                                                <span class="text-dark small">{{ $field['label'] }}</span>
                                                @if($field['required'] ?? false)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger ms-auto">مطلوب</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        
                                        @if(count($preset['fields'] ?? []) > 5)
                                            <div class="text-center mt-2">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    <i class="ri-more-line"></i> 
                                                    +{{ count($preset['fields']) - 5 }} حقول أخرى
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Card Footer with Action Button --}}
                                <div class="card-footer bg-white border-0 p-3">
                                    <button class="btn btn-{{ $preset['color'] ?? 'primary' }} w-100 btn-lg shadow-sm" 
                                        wire:click="$set('selectedPreset', '{{ $presetName }}')" 
                                        onclick="@this.call('addPresetSection'); bootstrap.Modal.getInstance(document.getElementById('presetsModal')).hide();">
                                        <i class="ri-add-circle-line me-2"></i> 
                                        إضافة هذا القسم
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty State --}}
                @if(count($availableSectionPresets) === 0)
                    <div class="text-center py-5">
                        <i class="ri-inbox-line text-muted" style="font-size: 64px;"></i>
                        <h5 class="text-muted mt-3">لا توجد قوالب متاحة</h5>
                        <p class="text-muted">جرب البحث بكلمات أخرى</p>
                    </div>
                @endif

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> إغلاق
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customSectionModal">
                    <i class="ri-add-circle-line me-1"></i> أو أنشئ قسمًا مخصصًا
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Preset Card Hover Effects --}}
<style>
    .preset-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent !important;
    }

    .preset-card:hover {
        transform: translateY(-8px);
        border-color: var(--bs-primary) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .preset-icon-wrapper {
        transition: transform 0.3s ease;
    }

   
    .fields-preview-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .fields-preview-list::-webkit-scrollbar {
        width: 6px;
    }

    .fields-preview-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .fields-preview-list::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    .preset-card .card-footer button {
        transition: all 0.3s ease;
    }

    .preset-card:hover .card-footer button {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .preset-card:hover {
            transform: none;
        }
    }
</style>


{{-- Animation on Modal Open --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const presetsModal = document.getElementById('presetsModal');
        if (presetsModal) {
            presetsModal.addEventListener('shown.bs.modal', function () {
                // Add stagger animation to cards
                const cards = presetsModal.querySelectorAll('.preset-card');
                cards.forEach((card, index) => {
                    card.style.animation = `fadeInUp 0.4s ease ${index * 0.1}s both`;
                });
            });
        }
    });

    // Animation keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
</script>
