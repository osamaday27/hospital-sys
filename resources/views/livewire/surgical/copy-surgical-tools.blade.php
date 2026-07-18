<div class="container-fluid py-3" dir="rtl">
    {{-- ✅ التنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-pill px-4">
            <i class="ri-check-double-line me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-pill px-4">
            <i class="ri-error-warning-line me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">
                <i class="mdi mdi-content-copy me-2"></i>
                {{ __('surgical_trans.copy_surgical_settings') }}
            </h5>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                {{-- 🧩 العملية المصدر --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">العملية المصدر (تحتوي على أدوات)</label>
                    <select wire:model="source_id" id="sourceSelect" class="form-select select2">
                        <option value="">-- اختر العملية المصدر --</option>
                        @foreach ($sourceList as $surgery)
                            <option value="{{ $surgery->id }}">
                                {{ $surgery->name_ar }} ({{ $surgery->name_en }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 🎯 العمليات الهدف --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">العمليات الهدف (من الخدمات)</label>
                    <select multiple wire:model="target_ids" id="targetSelect" class="form-select select2" style="height: 200px;">
                        @foreach ($services as $srv)
                            <option value="{{ $srv->id }}">
                                {{ $srv->getTranslation('name', 'ar') }} ({{ $srv->getTranslation('name', 'en') }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">يمكنك اختيار أكثر من عملية بالضغط على Ctrl أو Shift</small>
                    <div class="mt-2">
                        {{ $services->links() }}
                    </div>
                </div>
            </div>

            {{-- ✅ عرض الأدوات قبل النسخ --}}
            @if (!empty($tools_preview))
                <div class="alert alert-info shadow-sm mt-3">
                    <strong>🧰 الأدوات في العملية المصدر:</strong>
                    <ul class="mb-0">
                        @foreach ($tools_preview as $tool)
                            <li>
                                <i class="mdi mdi-wrench"></i>
                                {{ $tool['name_ar'] }} ({{ $tool['name_en'] }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="text-center mt-3">
                <button wire:click="copyTools" class="btn btn-success px-4 rounded-pill shadow-sm">
                    <i class="mdi mdi-content-copy me-1"></i> تنفيذ النسخ
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ✅ تفعيل Select2 مع البحث --}}
<script>
document.addEventListener('livewire:load', function () {

    function initSelect2() {
        // 🟢 المصدر
        $('#sourceSelect').select2({
            placeholder: 'اختر العملية المصدر',
            width: '100%',
            allowClear: true,
            language: {
                noResults: function() {
                    return "لا توجد نتائج مطابقة";
                }
            }
        }).on('change', function () {
            @this.set('source_id', $(this).val());
        });

        // 🟢 الهدف
        $('#targetSelect').select2({
            placeholder: 'اختر العمليات الهدف',
            width: '100%',
            allowClear: true,
            language: {
                noResults: function() {
                    return "لا توجد نتائج مطابقة";
                }
            }
        }).on('change', function () {
            @this.set('target_ids', $(this).val());
        });
    }

    // ✅ أول تحميل
    initSelect2();

    // ✅ بعد أي تحديث من Livewire (عشان يعيد تفعيل Select2)
    Livewire.hook('message.processed', (message, component) => {
        initSelect2();
    });
});
</script>
