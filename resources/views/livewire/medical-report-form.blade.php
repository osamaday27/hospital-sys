<div class="card border-0 shadow-lg">
    <!-- رأس البطاقة -->
    <div class="card-header bg-gradient-primary text-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="mdi mdi-file-document-outline me-2 fs-4"></i>
                    إدارة التقارير الطبية
                </h5>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <div class="badge bg-light text-primary fs-6 px-3 py-2">
                    <i class="mdi mdi-account-outline me-1"></i>
                    {{ $patient_name }} - {{ $patient_age }} سنة
                    @if($patient_gender)
                        <span class="mx-1">•</span> {{ $patient_gender }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        <!-- الإشعارات -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline me-2 fs-4"></i>
                <div>{{ session('message') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline me-2 fs-4"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center">
                <i class="mdi mdi-information-outline me-2 fs-4"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center">
                <i class="mdi mdi-alert-outline me-2 fs-4"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline me-2 fs-4"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-bold mb-2">
                    <i class="mdi mdi-alert-circle-outline me-1"></i>
                    يرجى تصحيح الأخطاء التالية:
                </div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- إحصائيات سريعة -->
        @if($reportsCount > 0)
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-info text-white border-0">
                        <div class="card-body d-flex align-items-center">
                            <i class="mdi mdi-file-document-multiple-outline fs-1 me-3"></i>
                            <div>
                                <h6 class="mb-0">عدد التقارير</h6>
                                <h3 class="mb-0">{{ $reportsCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white border-0">
                        <div class="card-body d-flex align-items-center">
                            <i class="mdi mdi-calendar-clock-outline fs-1 me-3"></i>
                            <div>
                                <h6 class="mb-0">آخر تقرير</h6>
                                <h6 class="mb-0">
                                    @if($lastReportDate)
                                        {{ \Carbon\Carbon::parse($lastReportDate)->format('Y/m/d') }}
                                    @else
                                        لا يوجد
                                    @endif
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- معلومات الزيارة -->
        <div class="card mb-4 border-0 shadow-sm bg-light">
            <div class="card-body">
                <h6 class="text-muted mb-3">
                    <i class="mdi mdi-information-outline me-1"></i>
                    معلومات الزيارة
                </h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">نوع الزيارة</small>
                        <strong>{{ $visit_type_name }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">تاريخ الزيارة</small>
                        <strong>{{ \Carbon\Carbon::parse($visit_date)->format('Y/m/d') }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">الطبيب</small>
                        <strong>{{ $doctor_name }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">التخصص</small>
                        <strong>{{ $doctor_specialty }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم إنشاء التقرير -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="mdi mdi-file-document-edit-outline me-2 text-primary"></i>
                    {{ $editingReportId ? 'تحرير التقرير' : 'تقرير جديد' }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- اختيار القالب -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="mdi mdi-file-document-outline me-1"></i>
                            اختر قالب التقرير
                        </label>
                        <select wire:model="template_id" class="form-select">
                            <option value="">-- اختر قالب التقرير --</option>
                            @foreach ($templates as $template)
                                <option value="{{ $template->id }}">
                                    {{ $template->name }}
                                    @if($template->category)
                                        ({{ $template->category }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- اسم التقرير -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="mdi mdi-tag-outline me-1"></i>
                            اسم التقرير <span class="text-danger">*</span>
                        </label>
                        <input type="text" wire:model.defer="report_name" 
                            class="form-control @error('report_name') is-invalid @enderror"
                            placeholder="أدخل اسم التقرير">
                        @error('report_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- نوع التقرير -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="mdi mdi-shape-outline me-1"></i>
                            نوع التقرير
                        </label>
                        <select wire:model.defer="report_type" class="form-select">
                            <option value="">-- اختر النوع --</option>
                            <option value="فحص عام">فحص عام</option>
                            <option value="تقرير أشعة">تقرير أشعة</option>
                            <option value="تقرير مختبر">تقرير مختبر</option>
                            <option value="تقرير عملية">تقرير عملية</option>
                            <option value="تقرير متابعة">تقرير متابعة</option>
                            <option value="شهادة طبية">شهادة طبية</option>
                            <option value="تقرير خروج">تقرير خروج</option>
                        </select>
                    </div>
                </div>

                <!-- أزرار الإدراج التلقائي -->
                <div class="card mt-3 border-0 bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">
                            <i class="mdi mdi-auto-fix me-1"></i>
                            إدراج تلقائي
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button wire:click="insertComplaint" class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-plus-circle-outline me-1"></i>
                                إدراج الشكوى
                                @if($complaints->count() > 0)
                                    <span class="badge bg-primary ms-1">{{ $complaints->count() }}</span>
                                @endif
                            </button>
                            <button wire:click="insertDiagnosis" class="btn btn-outline-success btn-sm">
                                <i class="mdi mdi-plus-circle-outline me-1"></i>
                                إدراج التشخيص
                                @if($diagnoses->count() > 0)
                                    <span class="badge bg-success ms-1">{{ $diagnoses->count() }}</span>
                                @endif
                            </button>
                        </div>
                    </div>
                </div>

                <!-- محرر النص -->
                <div class="mt-3">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-text-box-outline me-1"></i>
                        محتوى التقرير <span class="text-danger">*</span>
                    </label>
                    <div class="border rounded" style="min-height: 400px;">
                        <div wire:ignore>
                            <textarea id="report-editor" hidden>{{ $content }}</textarea>
                            <div id="report-editor-container"></div>
                        </div>
                    </div>
                    @error('content')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ملاحظات إضافية -->
                <div class="mt-3">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-note-text-outline me-1"></i>
                        ملاحظات إضافية
                    </label>
                    <textarea wire:model.defer="notes" class="form-control" rows="3"
                        placeholder="أضف أي ملاحظات إضافية هنا..."></textarea>
                </div>

                <!-- حالة التقرير -->
                <div class="mt-3">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-state-machine me-1"></i>
                        حالة التقرير
                    </label>
                    <select wire:model.defer="report_status" class="form-select w-auto">
                        <option value="draft">مسودة</option>
                        <option value="completed">مكتمل</option>
                        <option value="signed">موقع</option>
                    </select>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button wire:click="save" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i>
                        حفظ التقرير
                    </button>

                    <button wire:click="saveAsDraft" class="btn btn-secondary">
                        <i class="mdi mdi-file-document-outline me-1"></i>
                        حفظ كمسودة
                    </button>

                    <button wire:click="saveAndComplete" class="btn btn-success">
                        <i class="mdi mdi-check-circle-outline me-1"></i>
                        حفظ وإكمال
                    </button>

                    @if($editingReportId && $report_status !== 'signed')
                        <button wire:click="signReport" class="btn btn-warning">
                            <i class="mdi mdi-pen me-1"></i>
                            توقيع التقرير
                        </button>
                    @endif

                    <button wire:click="previewReport" class="btn btn-outline-info">
                        <i class="mdi mdi-eye-outline me-1"></i>
                        معاينة
                    </button>

                    <button onclick="printReport()" class="btn btn-outline-secondary">
                        <i class="mdi mdi-printer-outline me-1"></i>
                        طباعة
                    </button>

                    <button onclick="downloadPdf()" class="btn btn-outline-dark">
                        <i class="mdi mdi-file-pdf-outline me-1"></i>
                        حفظ PDF
                    </button>

                    <button wire:click="createNewReport" class="btn btn-outline-danger">
                        <i class="mdi mdi-file-plus-outline me-1"></i>
                        تقرير جديد
                    </button>
                </div>
            </div>
        </div>

        <!-- قسم التقارير السابقة -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="mdi mdi-history me-2 text-primary"></i>
                        التقارير السابقة
                        <span class="badge bg-primary">{{ $reports->count() }}</span>
                    </h6>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- أدوات البحث والفلترة -->
                @if($reports->count() > 0)
                    <div class="p-3 bg-light border-bottom">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" wire:model.debounce.500ms="searchReports" 
                                    class="form-control form-control-sm"
                                    placeholder="بحث في التقارير...">
                            </div>
                            <div class="col-md-2">
                                <select wire:model="filterByType" class="form-select form-select-sm">
                                    <option value="">كل الأنواع</option>
                                    <option value="فحص عام">فحص عام</option>
                                    <option value="تقرير أشعة">تقرير أشعة</option>
                                    <option value="تقرير مختبر">تقرير مختبر</option>
                                    <option value="تقرير عملية">تقرير عملية</option>
                                    <option value="تقرير متابعة">تقرير متابعة</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select wire:model="filterByStatus" class="form-select form-select-sm">
                                    <option value="">كل الحالات</option>
                                    <option value="draft">مسودة</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="signed">موقع</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" wire:model="filterByDate" 
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                @if($searchReports || $filterByType || $filterByStatus || $filterByDate)
                                    <button wire:click="clearReportFilters" 
                                        class="btn btn-sm btn-outline-secondary w-100">
                                        <i class="mdi mdi-filter-remove-outline me-1"></i>
                                        مسح الفلاتر
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- جدول التقارير -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">اسم التقرير</th>
                                <th style="width: 15%;">النوع</th>
                                <th style="width: 15%;">التاريخ</th>
                                <th style="width: 15%;">الطبيب</th>
                                <th style="width: 10%;">الحالة</th>
                                <th style="width: 15%;" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-file-document-outline text-primary me-2 fs-5"></i>
                                            <div>
                                                <div class="fw-bold">{{ $report->report_name }}</div>
                                                @if($report->notes)
                                                    <small class="text-muted">{{ Str::limit($report->notes, 40) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($report->report_type)
                                            <span class="badge bg-info">{{ $report->report_type }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            <i class="mdi mdi-calendar-outline me-1"></i>
                                            {{ \Carbon\Carbon::parse($report->report_date)->format('Y/m/d H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>{{ $report->doctor->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @switch($report->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">
                                                    <i class="mdi mdi-file-document-outline"></i> مسودة
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">
                                                    <i class="mdi mdi-check-circle"></i> مكتمل
                                                </span>
                                                @break
                                            @case('signed')
                                                <span class="badge bg-primary">
                                                    <i class="mdi mdi-pen"></i> موقع
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">-</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button wire:click="loadPreviousReport({{ $report->id }})"
                                                class="btn btn-outline-primary" title="عرض">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                            
                                            <button wire:click="duplicateReport({{ $report->id }})"
                                                class="btn btn-outline-secondary" title="نسخ">
                                                <i class="mdi mdi-content-copy"></i>
                                            </button>
                                            
                                            <button wire:click="previewReport({{ $report->id }})"
                                                class="btn btn-outline-info" title="معاينة">
                                                <i class="mdi mdi-file-eye-outline"></i>
                                            </button>
                                            
                                            @if($report->status !== 'signed')
                                                <button wire:click="confirmDelete({{ $report->id }})"
                                                    class="btn btn-outline-danger" title="حذف">
                                                    <i class="mdi mdi-delete-outline"></i>
                                                </button>
                                            @endif
                                            
                                            <button onclick="printSingleReport({{ $report->id }})"
                                                class="btn btn-outline-secondary" title="طباعة">
                                                <i class="mdi mdi-printer-outline"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="mdi mdi-file-document-remove-outline fs-1 d-block mb-2"></i>
                                        <div>لا توجد تقارير سابقة</div>
                                        <small>قم بإنشاء تقرير جديد للبدء</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تأكيد حذف -->
<div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                    تأكيد الحذف
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">هل أنت متأكد من حذف هذا التقرير؟</p>
                <p class="text-danger small mb-0">لا يمكن التراجع عن هذا الإجراء</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>
                    إلغاء
                </button>
                <button wire:click="deleteReport" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="mdi mdi-delete me-1"></i>
                    حذف نهائياً
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal المعاينة -->
<div class="modal fade" id="previewModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-eye-outline me-2"></i>
                    معاينة التقرير
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="preview-content">
                <div class="p-4 bg-white">
                    {!! $previewContent !!}
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="printPreview()" class="btn btn-primary">
                    <i class="mdi mdi-printer me-1"></i>
                    طباعة
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let reportEditor;

    document.addEventListener('livewire:load', function() {
        initializeReportEditor();

        // الاستماع للأحداث
        window.addEventListener('setEditorContent', function(event) {
            if (reportEditor) {
                reportEditor.setData(event.detail.content || '');
            }
        });

        window.addEventListener('appendToEditor', function(event) {
            if (reportEditor) {
                const currentContent = reportEditor.getData();
                reportEditor.setData(currentContent + event.detail.content);
            }
        });

        window.addEventListener('scroll-to-editor', function() {
            document.querySelector('#report-editor-container').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        });

        window.addEventListener('show-delete-modal', function() {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });

        window.addEventListener('show-preview-modal', function() {
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        });

        window.addEventListener('report-saved', function(event) {
            toastr.success(event.detail.message);
        });

        window.addEventListener('report-deleted', function() {
            toastr.success('تم حذف التقرير بنجاح');
        });
    });

    function initializeReportEditor() {
        ClassicEditor
            .create(document.querySelector('#report-editor-container'), {
                language: 'ar',
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                        'alignment', 'outdent', 'indent', '|',
                        'bulletedList', 'numberedList', 'todoList', '|',
                        'link', 'imageUpload', 'insertTable', 'mediaEmbed', 'blockQuote', 'horizontalLine', '|',
                        'undo', 'redo', '|',
                        'code', 'codeBlock', 'highlight'
                    ],
                    shouldNotGroupWhenFull: true
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'فقرة', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'عنوان رئيسي', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'عنوان فرعي', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'عنوان ثانوي', class: 'ck-heading_heading3' }
                    ]
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                }
            })
            .then(editor => {
                reportEditor = editor;

                // تحديث محتوى Livewire عند التغيير
                editor.model.document.on('change:data', () => {
                    const data = editor.getData();
                    @this.set('content', data);
                });
            })
            .catch(error => {
                console.error('خطأ في تهيئة المحرر:', error);
            });
    }

    function printReport() {
        if (reportEditor) {
            const content = reportEditor.getData();
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html dir="rtl" lang="ar">
                <head>
                    <meta charset="UTF-8">
                    <title>طباعة التقرير</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        @media print { body { padding: 0; } }
                    </style>
                </head>
                <body>
                    ${content}
                    <script>
                        window.onload = function() { window.print(); }
                    </script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    }

    function printSingleReport(reportId) {
        @this.call('previewReport', reportId).then(() => {
            setTimeout(() => {
                printPreview();
            }, 500);
        });
    }

    function printPreview() {
        const content = document.getElementById('preview-content').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html dir="rtl" lang="ar">
            <head>
                <meta charset="UTF-8">
                <title>طباعة التقرير</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    @media print { body { padding: 0; } }
                </style>
            </head>
            <body>
                ${content}
                <script>
                    window.onload = function() { window.print(); }
                </script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    function downloadPdf() {
        // يتطلب مكتبة jsPDF أو html2pdf
        alert('وظيفة تحميل PDF قيد التطوير');
    }
</script>

<style>
    .ck-editor__editable {
        min-height: 400px;
        max-height: 600px;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    @media print {
        .card-header, .btn, .alert { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>
@endpush