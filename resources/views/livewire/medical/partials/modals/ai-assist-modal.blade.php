{{-- AI Assist Modal --}}
<div class="modal fade" id="aiAssistModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title fw-bold">
                    <i class="ri-robot-2-line"></i> مساعد الذكاء الاصطناعي
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                
                <div class="text-center mb-4">
                    <div class="ai-avatar bg-info bg-opacity-10 text-info rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" 
                        style="width: 80px; height: 80px;">
                        <i class="ri-robot-2-line" style="font-size: 40px;"></i>
                    </div>
                    <h6 class="fw-bold text-dark">كيف يمكنني مساعدتك؟</h6>
                    <p class="text-muted">اختر أحد الخيارات أو اطرح سؤالك</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <button class="btn btn-outline-primary w-100 text-start p-3 h-100">
                            <i class="ri-file-add-line fs-5 d-block mb-2"></i>
                            <strong class="d-block mb-1">إنشاء قالب كامل</strong>
                            <small class="text-muted">أنشئ قالباً متكاملاً بناءً على نوع العملية</small>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-success w-100 text-start p-3 h-100">
                            <i class="ri-magic-line fs-5 d-block mb-2"></i>
                            <strong class="d-block mb-1">توليد نص طبي</strong>
                            <small class="text-muted">اكتب Narrative احترافي تلقائياً</small>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 text-start p-3 h-100">
                            <i class="ri-lightbulb-line fs-5 d-block mb-2"></i>
                            <strong class="d-block mb-1">اقتراحات ذكية</strong>
                            <small class="text-muted">احصل على نصائح لتحسين قالبك</small>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-warning w-100 text-start p-3 h-100">
                            <i class="ri-translate-2 fs-5 d-block mb-2"></i>
                            <strong class="d-block mb-1">ترجمة ومراجعة</strong>
                            <small class="text-muted">ترجم وراجع المحتوى الطبي</small>
                        </button>
                    </div>
                </div>

                <div class="custom-query">
                    <label class="form-label fw-bold">أو اكتب طلبك المخصص</label>
                    <textarea class="form-control mb-3" rows="4" 
                        placeholder="مثال: أنشئ لي قالب لعملية Phacoemulsification مع IOL يحتوي على جميع الحقول الضرورية..."></textarea>
                    <button class="btn btn-primary w-100">
                        <i class="ri-send-plane-fill"></i> إرسال الطلب
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>