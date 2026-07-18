<div class="ai-tab">
    
    <div class="row">
        <div class="col-lg-10 mx-auto">
            
            {{-- AI Assistant Header --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ai-icon bg-white text-success rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 60px; height: 60px;">
                            <i class="ri-robot-2-line fs-2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">مساعد الذكاء الاصطناعي</h5>
                            <p class="mb-0 opacity-75">احصل على اقتراحات ذكية لتحسين قالبك الطبي</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                
                {{-- AI Suggestions Panel --}}
                <div class="col-lg-8">
                    
                    {{-- Smart Suggestions --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="ri-lightbulb-line text-warning"></i> اقتراحات ذكية
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            
                            <div class="suggestion-item p-3 mb-3 bg-light rounded-3 border-start border-4 border-info">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-2">
                                            <i class="ri-add-circle-line text-info"></i>
                                            إضافة قسم "Post-Operative Instructions"
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            معظم تقارير العمليات تحتوي على تعليمات ما بعد العملية. هل تريد إضافة هذا القسم؟
                                        </p>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-info">
                                                <i class="ri-check-line"></i> تطبيق
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="ri-close-line"></i> تجاهل
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="suggestion-item p-3 mb-3 bg-light rounded-3 border-start border-4 border-info">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-2">
                                            <i class="ri-file-list-line text-info"></i>
                                            تحسين النص الطبي (Narrative)
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            يمكننا إضافة جمل طبية احترافية ومعتمدة لتحسين جودة التقرير.
                                        </p>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-info">
                                                <i class="ri-magic-line"></i> تحسين الآن
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                لاحقاً
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="suggestion-item p-3 mb-3 bg-light rounded-3 border-start border-4 border-warning">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-2">
                                            <i class="ri-error-warning-line text-warning"></i>
                                            حقل "Complications" غير محدد كمطلوب
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            من المهم تحديد ما إذا كانت هناك مضاعفات. نقترح جعل هذا الحقل مطلوباً.
                                        </p>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-warning">
                                                <i class="ri-check-line"></i> اجعله مطلوباً
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                إبقاءه اختيارياً
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center py-3">
                                <button class="btn btn-outline-info">
                                    <i class="ri-refresh-line"></i> تحديث الاقتراحات
                                </button>
                            </div>

                        </div>
                    </div>

                    {{-- AI Chat Assistant --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-gradient-info text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="ri-chat-4-line"></i> محادثة مع المساعد الذكي
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            
                            <div class="chat-messages p-4" style="height: 400px; overflow-y: auto;">
                                
                                <div class="message ai-message mb-3">
                                    <div class="d-flex gap-3">
                                        <div class="avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 40px; height: 40px; min-width: 40px;">
                                            <i class="ri-robot-line"></i>
                                        </div>
                                        <div class="message-content bg-light rounded-3 p-3">
                                            <p class="mb-0">مرحباً! أنا هنا لمساعدتك في بناء قالب طبي احترافي. ماذا تريد أن تفعل اليوم؟</p>
                                            <small class="text-muted">منذ دقيقة</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="message user-message mb-3">
                                    <div class="d-flex gap-3 justify-content-end">
                                        <div class="message-content bg-info text-white rounded-3 p-3">
                                            <p class="mb-0">أريد إضافة حقل لقياس IOP</p>
                                            <small class="opacity-75">منذ دقيقة</small>
                                        </div>
                                        <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 40px; height: 40px; min-width: 40px;">
                                            <i class="ri-user-line"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="message ai-message mb-3">
                                    <div class="d-flex gap-3">
<div class="avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 40px; height: 40px; min-width: 40px;">
                                            <i class="ri-robot-line"></i>
                                        </div>
                                        <div class="message-content bg-light rounded-3 p-3">
                                            <p class="mb-2">ممتاز! سأضيف حقل IOP (Intraocular Pressure) بالمواصفات التالية:</p>
                                            <ul class="mb-2">
                                                <li>نوع الحقل: Number</li>
                                                <li>الوحدة: mmHg</li>
                                                <li>النطاق: 0-100</li>
                                                <li>القيمة الافتراضية: 15</li>
                                            </ul>
                                            <button class="btn btn-sm btn-success">
                                                <i class="ri-check-line"></i> تطبيق
                                            </button>
                                            <small class="text-muted d-block mt-2">الآن</small>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="chat-input border-top p-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="اكتب سؤالك أو طلبك هنا...">
                                    <button class="btn btn-info">
                                        <i class="ri-send-plane-fill"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- AI Tools Sidebar --}}
                <div class="col-lg-4">
                    
                    {{-- Quick AI Actions --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="ri-dashboard-line text-success"></i> أدوات سريعة
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            
                            <button class="btn btn-outline-info w-100 mb-2 text-start">
                                <i class="ri-file-text-line"></i> توليد نص طبي تلقائي
                            </button>
                            
                            <button class="btn btn-outline-success w-100 mb-2 text-start">
                                <i class="ri-translate-2"></i> ترجمة إلى الإنجليزية
                            </button>
                            
                            <button class="btn btn-outline-info w-100 mb-2 text-start">
                                <i class="ri-file-copy-line"></i> إنشاء قالب مشابه
                            </button>
                            
                            <button class="btn btn-outline-warning w-100 mb-2 text-start">
                                <i class="ri-shield-check-line"></i> فحص الأخطاء الطبية
                            </button>
                            
                            <button class="btn btn-outline-secondary w-100 text-start">
                                <i class="ri-book-open-line"></i> اقتراحات من قوالب مشابهة
                            </button>

                        </div>
                    </div>

                    {{-- Template Analysis --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="ri-line-chart-line text-info"></i> تحليل القالب
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            
                            <div class="analysis-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">مستوى الاكتمال</span>
                                    <strong class="text-success">85%</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 85%"></div>
                                </div>
                            </div>

                            <div class="analysis-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">جودة المحتوى</span>
                                    <strong class="text-info">جيد جداً</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: 90%"></div>
                                </div>
                            </div>

                            <div class="analysis-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">عدد الأقسام</span>
                                    <strong>{{ count($sections) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">عدد الحقول</span>
                                    <strong>{{ collect($sections)->sum(fn($s) => count($s['fields'] ?? [])) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">الحقول المطلوبة</span>
                                    <strong>{{ collect($sections)->sum(fn($s) => collect($s['fields'] ?? [])->where('required', true)->count()) }}</strong>
                                </div>
                            </div>

                            <div class="alert alert-success border-0 mb-0">
                                <small>
                                    <i class="ri-checkbox-circle-line"></i>
                                    قالبك يتوافق مع المعايير الطبية الدولية
                                </small>
                            </div>

                        </div>
                    </div>

                    {{-- AI Recommendations --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="ri-star-line text-warning"></i> توصيات AI
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            
                            <div class="recommendation-item p-2 mb-2 bg-warning bg-opacity-10 rounded-2 border-start border-3 border-warning">
                                <small class="fw-semibold text-dark d-block mb-1">نقص حقل مهم</small>
                                <small class="text-muted">أضف حقل "Patient Consent"</small>
                            </div>

                            <div class="recommendation-item p-2 mb-2 bg-info bg-opacity-10 rounded-2 border-start border-3 border-info">
                                <small class="fw-semibold text-dark d-block mb-1">تحسين مقترح</small>
                                <small class="text-muted">استخدم قوائم منسدلة بدلاً من النص الحر</small>
                            </div>

                            <div class="recommendation-item p-2 mb-2 bg-success bg-opacity-10 rounded-2 border-start border-3 border-success">
                                <small class="fw-semibold text-dark d-block mb-1">ممتاز!</small>
                                <small class="text-muted">القالب يحتوي على جميع الأقسام الأساسية</small>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

<style>
    .chat-messages {
        background: #f8f9fa;
    }
    
    .message {
        animation: fadeInUp 0.3s ease;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .ai-icon {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
</style>
