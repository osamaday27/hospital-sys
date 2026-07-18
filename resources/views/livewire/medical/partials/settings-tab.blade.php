<div class="settings-tab">
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            {{-- General Settings --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-settings-3-line"></i> الإعدادات العامة
                    </h6>
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">اسم القالب <span class="text-danger">*</span></label>
                        <input type="text" wire:model.lazy="template_name" class="form-control">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">الفئة</label>
                        <select wire:model="template_category" class="form-select">
                            <option value="">اختر الفئة</option>
                            <option value="Cataract">Cataract Surgery</option>
                            <option value="Glaucoma">Glaucoma Surgery</option>
                            <option value="Retina">Retina Surgery</option>
                            <option value="Cornea">Cornea Surgery</option>
                            <option value="Strabismus">Strabismus Surgery</option>
                            <option value="Oculoplasty">Oculoplasty Surgery</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">الوصف</label>
                        <textarea wire:model.lazy="template_description" class="form-control" rows="4"></textarea>
                    </div>

                </div>
            </div>

            {{-- Display Settings --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-layout-line"></i> إعدادات العرض
                    </h6>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model="is_printable" id="printCheck">
                                <label class="form-check-label fw-semibold" for="printCheck">قابل للطباعة</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model="is_public" id="pubCheck">
                                <label class="form-check-label fw-semibold" for="pubCheck">عام (مشارك)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model="auto_save" id="autoCheck">
                                <label class="form-check-label fw-semibold" for="autoCheck">حفظ تلقائي</label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Version History --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-history-line"></i> سجل الإصدارات
                    </h6>
                </div>
                <div class="card-body p-4">
                    
                    @if(count($version_history) > 0)
                        @foreach(array_reverse($version_history) as $version)
                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                    style="width: 40px; height: 40px; min-width: 40px;">
                                    <strong class="small">v{{ $version['version'] }}</strong>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>الإصدار {{ $version['version'] }}</strong>
                                            <p class="text-muted small mb-0">{{ $version['changes'] ?? 'تحديثات' }}</p>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">{{ $version['saved_by'] }}</small>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($version['saved_at'])->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="ri-file-history-line fs-1"></i>
                            <p class="mb-0">لا يوجد سجل إصدارات</p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

</div>