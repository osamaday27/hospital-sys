<div class="medical-template-builder {{ $fullscreen_mode ? 'fullscreen-mode' : '' }}" dir="rtl">
    
    {{-- 🎨 Header with Actions --}}
    <div class="builder-header sticky-top bg-white shadow-sm border-bottom py-3 mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <a  class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-right-line"></i> رجوع
                        </a>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="ri-file-edit-line text-info"></i>
                                {{ $template_name ?: 'قالب جديد' }}
                            </h5>
                            <small class="text-muted">آخر حفظ: {{ $template->updated_at?->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-center">
                    {{-- Tabs --}}
                    <div class="btn-group" role="group">
                        <button wire:click="switchTab('builder')" class="btn btn-sm {{ $active_tab === 'builder' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="ri-layout-grid-line"></i> البناء
                        </button>
                        <button wire:click="switchTab('preview')" class="btn btn-sm {{ $active_tab === 'preview' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="ri-eye-line"></i> معاينة
                        </button>
                        <button wire:click="switchTab('settings')" class="btn btn-sm {{ $active_tab === 'settings' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="ri-settings-3-line"></i> إعدادات
                        </button>
                        <button wire:click="switchTab('ai')" class="btn btn-sm {{ $active_tab === 'ai' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="ri-robot-line"></i> AI
                        </button>
                    </div>
                </div>

                <div class="col-md-4 text-start">
                    <div class="d-flex justify-content-end gap-2">
                        <button wire:click="toggleFullscreen" class="btn btn-outline-secondary btn-sm" title="شاشة كاملة">
                            <i class="ri-fullscreen-line"></i>
                        </button>
                        <button wire:click="exportTemplate" class="btn btn-outline-info btn-sm">
                            <i class="ri-download-line"></i> تصدير
                        </button>
                        <button wire:click="save" class="btn btn-success btn-sm">
                            <i class="ri-save-line"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 Main Content Area --}}
    <div class="container-fluid px-4">
        
        @if($active_tab === 'builder')
            @include('livewire.medical.partials.builder-tab')
        @elseif($active_tab === 'preview')
            @include('livewire.medical.partials.preview-tab')
        @elseif($active_tab === 'settings')
            @include('livewire.medical.partials.settings-tab')
        @elseif($active_tab === 'ai')
            @include('livewire.medical.partials.ai-tab')
        @endif

    </div>

    {{-- Toast Notifications --}}
    @if(session()->has('success'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div class="toast show align-items-center text-white bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="ri-check-line"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .medical-template-builder {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .fullscreen-mode {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            background: white;
            overflow-y: auto;
        }

        .builder-header {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
        }

        .section-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .field-item {
            transition: all 0.2s ease;
            cursor: move;
        }

       

        .preset-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }

        .drag-handle {
            cursor: grab;
            opacity: 0.5;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .ckeditor-wrapper {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</div>