{{-- Custom Section Modal - إنشاء قسم مخصص --}}
<div class="modal fade" id="customSectionModal" tabindex="-1" aria-labelledby="customSectionModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            
            {{-- Modal Header --}}
            <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <h5 class="modal-title fw-bold" id="customSectionModalLabel">
                    <i class="ri-add-circle-line me-2"></i> إنشاء قسم مخصص
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4">
                
                {{-- Section Name --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">
                        <i class="ri-text text-info"></i> 
                        اسم القسم 
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                        wire:model="newSectionName" 
                        class="form-control form-control-lg" 
                        placeholder="مثال: Surgical Technique Details">
                    <small class="text-muted">اختر اسمًا واضحًا ومختصرًا للقسم</small>
                </div>

                {{-- Section Description --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">
                        <i class="ri-file-text-line text-info"></i> 
                        وصف القسم
                    </label>
                    <textarea wire:model="newSectionDescription" 
                        class="form-control" 
                        rows="3" 
                        placeholder="وصف مختصر لمحتوى هذا القسم وموعد استخدامه..."></textarea>
                    <small class="text-muted">اختياري - يساعد في فهم الغرض من القسم</small>
                </div>

                {{-- Icon and Color Selection --}}
                <div class="row">
                    
                    {{-- Icon Selection --}}
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">
                            <i class="ri-emotion-happy-line text-warning"></i> 
                            أيقونة القسم
                        </label>
                        <select wire:model="sectionIcon" class="form-select form-select-lg">
                            <option value="ri-folder-2-line">📁 مجلد عام</option>
                            <option value="ri-file-list-3-line">📄 ملف وثائق</option>
                            <option value="ri-stethoscope-line">🩺 سماعة طبية</option>
                            <option value="ri-syringe-line">💉 حقنة</option>
                            <option value="ri-surgical-mask-line">😷 قناع جراحي</option>
                            <option value="ri-heart-pulse-line">❤️ نبض القلب</option>
                            <option value="ri-user-heart-line">👤 معلومات المريض</option>
                            <option value="ri-medicine-bottle-line">💊 أدوية</option>
                            <option value="ri-microscope-line">🔬 مجهر</option>
                            <option value="ri-test-tube-line">🧪 أنبوب اختبار</option>
                            <option value="ri-eye-line">👁️ عين</option>
                            <option value="ri-capsule-line">💊 كبسولة</option>
                            <option value="ri-first-aid-kit-line">🏥 إسعافات أولية</option>
                            <option value="ri-pulse-line">📈 قياسات حيوية</option>
                            <option value="ri-empathize-line">😊 رعاية المريض</option>
                        </select>
                    </div>

                    {{-- Color Selection --}}
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">
                            <i class="ri-palette-line text-danger"></i> 
                            لون القسم
                        </label>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <label class="color-option">
                                <input type="radio" name="sectionColor" value="info" wire:model="newSectionColor" checked hidden>
                                <span class="color-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="ri-check-line text-white"></i>
                                </span>
                            </label>
                            <label class="color-option">
                                <input type="radio" name="sectionColor" value="success" wire:model="newSectionColor" hidden>
                                <span class="color-badge" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                    <i class="ri-check-line text-white"></i>
                                </span>
                            </label>
                            <label class="color-option">
                                <input type="radio" name="sectionColor" value="info" wire:model="newSectionColor" hidden>
                                <span class="color-badge" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <i class="ri-check-line text-white"></i>
                                </span>
                            </label>
                            <label class="color-option">
                                <input type="radio" name="sectionColor" value="warning" wire:model="newSectionColor" hidden>
                                <span class="color-badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <i class="ri-check-line text-white"></i>
                                </span>
                            </label>
                            <label class="color-option">
                                <input type="radio" name="sectionColor" value="danger" wire:model="newSectionColor" hidden>
                                <span class="color-badge" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                    <i class="ri-check-line text-white"></i>
                                </span>
                            </label>
                            <label class="color-option">
                                <input type="radio" name="sectionColor" value="secondary" wire:model="newSectionColor" hidden>
                                <span class="color-badge" style="background: linear-gradient(135deg, #868f96 0%, #596164 100%);">
                                    <i class="ri-check-line text-white"></i>
                                </span>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Preview Card --}}
                <div class="alert border-0 shadow-sm" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="ri-eye-line"></i> معاينة القسم:
                    </h6>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header text-white py-3" 
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="{{ $sectionIcon ?? 'ri-folder-2-line' }} fs-5"></i>
                                <strong>{{ $newSectionName ?: 'اسم القسم' }}</strong>
                            </div>
                            @if($newSectionDescription)
                                <small class="opacity-75 d-block mt-1">{{ $newSectionDescription }}</small>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-0">سيتم إضافة الحقول هنا بعد إنشاء القسم</p>
                        </div>
                    </div>
                </div>

                {{-- Info Alert --}}
                <div class="alert alert-info border-0">
                    <i class="ri-information-line me-2"></i>
                    <strong>ملاحظة:</strong> يمكنك إضافة الحقول للقسم بعد إنشائه مباشرة من صفحة البناء
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> إلغاء
                </button>
                <button type="button" 
                    class="btn btn-success btn-lg" 
                    wire:click="addSection" 
                    data-bs-dismiss="modal"
                    {{ empty($newSectionName) ? 'disabled' : '' }}>
                    <i class="ri-check-line me-1"></i> إنشاء القسم
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Custom Styles --}}
<style>
    .color-option {
        cursor: pointer;
        position: relative;
    }

    .color-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 3px solid transparent;
    }

    .color-option input:checked + .color-badge {
        border-color: #fff;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.2);
        transform: scale(1.1);
    }

  

    .color-badge i {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .color-option input:checked + .color-badge i {
        opacity: 1;
    }
</style>
