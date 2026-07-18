<div class="container-fluid py-4">
    <!-- إحصائيات -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي المرضى</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_patients']) }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="mdi mdi-account-multiple text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">الأسماء المثبتة</h6>
                            <h4 class="mb-0">{{ number_format($stats['verified_names']) }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="mdi mdi-checkbox-marked-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">بانتظار المراجعة</h6>
                            <h4 class="mb-0">{{ number_format($stats['pending_review']) }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="mdi mdi-alert-circle text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-danger border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">الأسماء المتجاهلة</h6>
                            <h4 class="mb-0">{{ number_format($stats['ignored_names']) }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="mdi mdi-cancel text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الجدول الرئيسي -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="mdi mdi-magnify me-2"></i>الأسماء غير المسجلة في القاموس
            </h5>
            <div class="d-flex">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text bg-white"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="ابحث...">
                </div>
                <select wire:model="perPage" class="form-select form-select-sm ms-2" style="width: 80px;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        
        @if($loading)
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p class="mt-2 text-muted">جاري تحميل البيانات...</p>
            </div>
        @else
            <div class="card-body p-0">
                @if(session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show m-3">
                        <i class="mdi mdi-check-circle me-2"></i> {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3">
                        <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">الاسم</th>
                                <th class="text-center">عدد السجلات</th>
                                <th class="text-center">المستخدم</th>
                                <th class="text-end pe-4">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($names as $name)
                                <tr wire:key="name-{{ $name->display_name }}">
                                    <td class="ps-4">
                                        <a href="javascript:void(0)" 
                                           wire:click="loadPatientIds('{{ $name->display_name }}')"
                                           data-bs-toggle="tooltip" 
                                           title="عرض الأرقام الطبية">
                                            {{ $name->display_name }}
                                            <i class="mdi mdi-information-outline ms-1"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $name->count_records }}</span>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $users[$name->user_id] ?? '-' }}</small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button wire:click="selectName(
                                                '{{ $name->display_name }}',
                                                '{{ $name->first_name_ar }}',
                                                '{{ $name->first_name_en }}',
                                                '{{ $name->gender }}'
                                            )" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nameModal">
                                                <i class="mdi mdi-plus-circle-outline me-1"></i> إضافة
                                            </button>
                                            <button wire:click="ignoreName('{{ $name->display_name }}')" 
                                                    class="btn btn-outline-danger">
                                                <i class="mdi mdi-cancel me-1"></i> تجاهل
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @if($showPatientIds && in_array($name->display_name, [$selectedName]))
                                    <tr>
                                        <td colspan="4" class="bg-light">
                                            <div class="p-3">
                                                <h6 class="mb-2"><i class="mdi mdi-card-account-details-outline me-2"></i>الأرقام الطبية:</h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($patientIds as $id)
                                                        <span class="badge bg-info">{{ $id }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-database-remove-outline me-2"></i> لا توجد أسماء جديدة
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <small class="text-muted">عرض {{ $names->firstItem() }} إلى {{ $names->lastItem() }} من {{ $names->total() }} عنصر</small>
                    {{ $names->onEachSide(1)->links('livewire::bootstrap') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal إضافة الاسم -->
<div wire:ignore.self class="modal fade" id="nameModal" tabindex="-1" aria-labelledby="nameModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-secondary text-white">
                <h5 class="modal-title" id="nameModalLabel">
                    <i class="mdi mdi-plus-circle-outline me-2"></i>إضافة الاسم للقاموس
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">الاسم العربي <span class="text-danger">*</span></label>
                    <input type="text" wire:model.defer="selectedArabicName" class="form-control" required>
                    @error('selectedArabicName')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">الاسم الإنجليزي <span class="text-danger">*</span></label>
                    <input type="text" wire:model.defer="selectedEnglishName" class="form-control" required>
                    @error('selectedEnglishName')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">الجنس <span class="text-danger">*</span></label>
                    <select wire:model.defer="selectedGender" class="form-select">
                        <option value="undefined">غير محدد</option>
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                    </select>
                    @error('selectedGender')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> إغلاق
                </button>
                <button type="button" wire:click="saveName" class="btn btn-primary">
                    <i class="mdi mdi-content-save me-1"></i> حفظ
                </button>
            </div>
        </div>
    </div>
</div>

    
</div>

<script>
    document.addEventListener('livewire:load', function() {
        // إغلاق المودال
        window.addEventListener('close-modal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('nameModal'));
            if(modal) modal.hide();
        });
        
        // تهيئة أدوات التلميح
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>
    .card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .card-header {
        padding: 1rem 1.5rem;
    }
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
    }
    .form-control, .form-select {
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
    }
    .input-group-text {
        border-radius: 8px 0 0 8px;
    }
    .badge {
        font-weight: 500;
    }
    .border-3 {
        border-width:3px !important;
    }
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
</style>