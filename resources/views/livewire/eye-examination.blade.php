<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title mb-4 text-secondary">
                <i class="mdi mdi-eye-outline me-2"></i> Eye Examination
            </h5>

            @if (session()->has('message'))
                <div class="alert alert-info alert-dismissible fade show">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <ul class="nav nav-tabs nav-justified mb-4" id="examTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($activeTab === 'input') active @endif"
                        wire:click="$set('activeTab','input')" type="button">
                        <i class="mdi mdi-form-textbox me-1"></i> Data Entry
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($activeTab === 'chart') active @endif"
                        wire:click="$set('activeTab','chart')" type="button">
                        <i class="mdi mdi-chart-line me-1"></i> Examination Trends
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($activeTab === 'history') active @endif"
                        wire:click="$set('activeTab','history')" type="button">
                        <i class="mdi mdi-history me-1"></i> Examination History
                    </button>
                </li>
            </ul>

            <div class="tab-content p-3 border-top-0 border">
                <div class="tab-pane fade @if ($activeTab === 'input') show active @endif" id="input"
                    role="tabpanel">

                    <!-- File Upload Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="mdi mdi-upload me-2"></i>Import Examination Data</h6>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <input type="file" class="form-control" wire:model="external_file"
                                        accept=".csv,.xlsx,.json">
                                    @error('external_file')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    @if ($external_file)
                                        <div class="d-flex align-items-center mt-2 mt-md-0">
                                            <span class="badge bg-info me-2">
                                                <i class="mdi mdi-file-outline me-1"></i>
                                                {{ $external_file->getClientOriginalName() }}
                                            </span>
                                            <button class="btn btn-sm btn-outline-danger" wire:click="removeFile">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Examination Data Table -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="mdi mdi-table me-2"></i>Examination Data</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 15%">Section</th>
                                            <th style="width: 20%">Right Eye</th>
                                            <th style="width: 5%">Copy</th>
                                            <th style="width: 20%">Left Eye</th>
                                            <th style="width: 30%">Comment</th>
                                            <th style="width: 10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sections as $section)
                                            @foreach ($right_eye[$section] ?? [] as $index => $rightValue)
                                                <tr>
                                                    @if ($index == 0)
                                                        <td rowspan="{{ count($right_eye[$section] ?? []) }}"
                                                            class="fw-bold text-secondary bg-light">
                                                            {{ ucfirst(str_replace('_', ' ', $section)) }}
                                                        </td>
                                                    @endif

                                                    {{-- Right Eye --}}
                                                    <td>
                                                        @php
                                                            $datalistId = null;
                                                            if ($section == 'Anterior Vitreous') {
                                                                $datalistId = 'anterior_vitreous_list';
                                                            } elseif ($section == 'Anterior chamber') {
                                                                $datalistId = 'anterior_chamber_list';
                                                            } elseif ($section == 'Eye Lids') {
                                                                $datalistId = 'eye_lids';
                                                            } elseif ($section == 'Cornea') {
                                                                $datalistId = 'cornea_list';
                                                            } elseif ($section == 'IRIS') {
                                                                $datalistId = 'iris_list';
                                                            } elseif ($section == 'Pupil') {
                                                                $datalistId = 'pupil_list';
                                                            } elseif ($section == 'Lens') {
                                                                $datalistId = 'lens_list';
                                                            } elseif ($section == 'Optic Disc') {
                                                                $datalistId = 'optic_disc_list';
                                                            } elseif ($section == 'Visual Acuity (BCVA)') {
                                                                $datalistId = 'BCVA';
                                                            } elseif ($section == 'Visual Acuity (UCVA)') {
                                                                $datalistId = 'UCVA';
                                                            }

                                                        @endphp
                                                        @if ($datalistId)
                                                            <input list="{{ $datalistId }}"
                                                                class="form-control form-control-sm"
                                                                wire:model="right_eye.{{ $section }}.{{ $index }}">
                                                        @else
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model="right_eye.{{ $section }}.{{ $index }}">
                                                        @endif
                                                    </td>

                                                    {{-- Copy --}}
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-secondary py-0 px-2"
                                                            wire:click="copyValue('{{ $section }}', {{ $index }})">
                                                            <i class="mdi mdi-content-copy"></i>
                                                        </button>
                                                    </td>

                                                    {{-- Left Eye --}}
                                                    <td>
                                                        @if ($datalistId)
                                                            <input list="{{ $datalistId }}"
                                                                class="form-control form-control-sm"
                                                                wire:model="left_eye.{{ $section }}.{{ $index }}">
                                                        @else
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model="left_eye.{{ $section }}.{{ $index }}">
                                                        @endif
                                                    </td>

                                                    {{-- Comment --}}
                                                    <td>
                                                        <textarea class="form-control form-control-sm" wire:model="comment.{{ $section }}.{{ $index }}"
                                                            rows="1" placeholder="Add comment..."></textarea>
                                                    </td>

                                                    {{-- Actions --}}
                                                    <td class="text-center">
                                                        <button class="btn btn-outline-success btn-sm"
                                                            wire:click="addRow('{{ $section }}')">
                                                            <i class="mdi mdi-plus"></i>
                                                        </button>
                                                        @if (count($right_eye[$section]) > 1)
                                                            <button class="btn btn-outline-danger btn-sm"
                                                                wire:click="removeRow('{{ $section }}', {{ $index }})">
                                                                <i class="mdi mdi-minus"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>


                                    <datalist id="anterior_vitreous_list">
                                        @foreach ($anterior_vitreous_options as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>

                                    <datalist id="anterior_chamber_list">
                                        @foreach ($anterior_chamber_options as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>

                                    <datalist id="eye_lids">
                                        @foreach ($eye_lids as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>

                                    <datalist id="cornea_list">
                                        @foreach ($Cornea as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>

                                    <datalist id="iris_list">
                                        @foreach ($IRIS as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>

                                    <datalist id="pupil_list">
                                        @foreach ($Pupil as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>

                                    <datalist id="lens_list">
                                        @foreach ($Lens as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>

                                    <datalist id="optic_disc_list">
                                        @foreach ($Optic_Disc as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>
                                    <datalist id="BCVA">
                                        @foreach ($BCVA as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>
                                    <datalist id="UCVA">
                                        @foreach ($UCVA as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="mdi mdi-draw me-2"></i>رسم Fundus</h6>
                        </div>
                        <div class="card-body">
                            @if (session()->has('fundus_message'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('fundus_message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- ✅ خزننا بيانات المريض والزيارة على الكانفس كـ data-* --}}
                            <div class="position-relative" style="height: 400px;">
                                <canvas id="eyeCanvas" width="550" height="400"
                                    data-bg="{{ asset('storage/medical_sheet/sheet1.png') }}"
                                    data-bg-alt="{{ asset('assets/images/medical_sheet/sheet1.png') }}"
                                    data-patient="{{ $id_patient }}" data-visit="{{ $id_visit }}"
                                    data-reservation="{{ $id_reservation }}"
                                    style="border:2px solid #000; background:#f8f9fa;"></canvas>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="color">لون الرسم</label>
                                    <input type="color" id="color" value="#000000"
                                        class="form-control form-control-color">
                                </div>
                                <div class="col-md-4">
                                    <label for="size">سمك الفرشاة: <span id="sizeValue">5</span>px</label>
                                    <input type="range" id="size" min="1" max="20"
                                        value="5" class="form-range">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button id="newDrawing" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-file"></i> جديد
                                    </button>
                                    <button id="undo" class="btn btn-outline-danger">
                                        <i class="fas fa-undo"></i> تراجع
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12 d-flex justify-content-between">
                                    <div>
                                        <button id="rotate" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-redo"></i> تدوير الصورة
                                        </button>
                                        <!-- ✅ زر حفظ رسم Fundus فقط مربوط بالدالة -->
                                        <button id="saveFundusBtn" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-save"></i> حفظ رسم Fundus فقط
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label for="fundusComment">ملاحظات Fundus</label>
                                    <textarea class="form-control" id="fundusComment" rows="2" placeholder="أضف ملاحظات حول فحص Fundus..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- لو مش متأكد إن الميتا موجودة في اللAYOUT، حط السطر ده فوق في <head>:
<meta name="csrf-token" content="{{ csrf_token() }}">
--}}

                    <!-- Save Button -->
                    <div class="text-end">
                        <button class="btn btn-primary" wire:click="save" id="saveImage"
                            onclick="EyeDrawingTool.saveDrawing()">
                            <i class="mdi mdi-content-save me-1"></i> Save Examination Data
                        </button>
                    </div>
                </div>

                <!-- Charts Tab -->
                <div class="tab-pane fade @if ($activeTab === 'chart') show active @endif" id="chart"
                    role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="mdi mdi-chart-areaspline me-2"></i>Examination Trends</h6>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills mb-4" id="chartTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="eom-tab" data-bs-toggle="pill"
                                        data-bs-target="#eom-chart" type="button">EOM Diagram</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="angle-tab" data-bs-toggle="pill"
                                        data-bs-target="#angle-chart" type="button">Angle Diagram</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="cornea-tab" data-bs-toggle="pill"
                                        data-bs-target="#cornea-chart" type="button">Cornea Diagram</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="eom-chart">
                                    @include('livewire.imgsection.eom-diagram')
                                </div>
                                <div class="tab-pane fade" id="angle-chart">
                                    @include('livewire.imgsection.angle-diagram')
                                </div>
                                <div class="tab-pane fade" id="cornea-chart">
                                    @include('livewire.imgsection.cornea-diagram')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade @if ($activeTab === 'history') show active @endif" id="history"
                    role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="mdi mdi-history me-2"></i> Examination History
                            </h6>

                            <div class="input-group" style="width: 320px;">
                                <input type="text" class="form-control form-control-sm"
                                    placeholder="Search by batch, section, value or comment..."
                                    wire:model.debounce.500ms="historySearch">
                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                    wire:click="$refresh">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 18%">Batch ID</th>
                                            <th style="width: 14%">Date</th>
                                            <th style="width: 18%">Examiner</th>
                                            <th style="width: 25%">Right Eye Summary</th>
                                            <th style="width: 25%">Left Eye Summary</th>
                                            <th style="width: 10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($batches as $batch)
                                            <tr>
                                                <td class="fw-bold text-info">{{ $batch->batch_id }}</td>
                                                <td>{{ \Carbon\Carbon::parse($batch->created_at)->format('M d, Y H:i') }}
                                                </td>
                                                <td>{{ $userMap[$batch->created_by] ?? 'System' }}</td>

                                                <td class="text-truncate" style="max-width: 260px;"
                                                    title="{{ $batch->right_eye_summary ?: '—' }}">
                                                    {{ \Illuminate\Support\Str::limit($batch->right_eye_summary ?: '—', 80) }}
                                                </td>

                                                <td class="text-truncate" style="max-width: 260px;"
                                                    title="{{ $batch->left_eye_summary ?: '—' }}">
                                                    {{ \Illuminate\Support\Str::limit($batch->left_eye_summary ?: '—', 80) }}
                                                </td>

                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button class="btn btn-outline-info"
                                                            wire:click="viewDetails('{{ $batch->batch_id }}')"
                                                            title="View">
                                                            <i class="mdi mdi-eye-outline"></i>
                                                        </button>
                                                        <button class="btn btn-outline-secondary"
                                                            wire:click="exportBatch('{{ $batch->batch_id }}')"
                                                            title="Export">
                                                            <i class="mdi mdi-export"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger"
                                                            wire:click="confirmDelete('{{ $batch->batch_id }}')"
                                                            title="Delete">
                                                            <i class="mdi mdi-delete-outline"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    No examination history found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-3">
                                {{ $batches->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    @if ($showDetailsModal)
        <div class="modal fade show" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel"
            aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-secondary" id="detailsModalLabel" style="padding-bottom: 10px;">
                            Examination Details - Batch
                            #{{ $selectedBatchId }}</h5>
                        <button type="button" class="btn-close btn-close-white"
                            wire:click="$set('showDetailsModal', false)" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Section</th>
                                        <th>Right Eye</th>
                                        <th>Left Eye</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedBatchRecords as $record)
                                        <tr>
                                            <td>{{ $record->section }}</td>
                                            <td>{{ $record->right_eye }}</td>
                                            <td>{{ $record->left_eye }}</td>
                                            <td>{{ $record->comment }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($selectedBatchRecords->isNotEmpty() && $selectedBatchRecords->first()->fundus_image_path)
                            <div class="mt-4">
                                <h6>Fundus Image:</h6>
                                <img src="{{ asset('storage/' . $selectedBatchRecords->first()->fundus_image_path) }}"
                                    alt="Fundus Image" class="img-fluid">
                                @if ($selectedBatchRecords->first()->fundus_comment)
                                    <div class="mt-2">
                                        <strong>Comment:</strong> {{ $selectedBatchRecords->first()->fundus_comment }}
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showDetailsModal', false)">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this examination record? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteBatch"
                        data-bs-dismiss="modal">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .drawing-container {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background-color: #f8f9fa;
        display: inline-block;
        position: relative;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .nav-tabs .nav-link {
        transition: all 0.2s ease;
    }

    .form-range::-webkit-slider-thumb {
        background: var(--bs-primary);
    }

    .form-range::-moz-range-thumb {
        background: var(--bs-primary);
    }

    .form-range::-ms-thumb {
        background: var(--bs-primary);
    }

    .modal-backdrop {
        opacity: 0.5;
    }
</style>
<script>
    // ========== Eye Fundus Drawing Tool (Fixed) ==========
    let EyeDrawingTool;

    document.addEventListener('DOMContentLoaded', function() {
        EyeDrawingTool = (() => {
            let canvas, ctx;
            let drawing = false;
            let color = '#000000';
            let size = 5;
            let shapes = []; // المسارات المرسومة
            let currentPath = []; // المسار الحالي
            let img = new Image();
            let angle = 0;
            let isImageLoaded = false;

            function init() {
                canvas = document.getElementById('eyeCanvas');
                if (!canvas) return console.error('Canvas not found');
                ctx = canvas.getContext('2d');
                if (!ctx) return console.error('Canvas context error');

                setupEventListeners();
                loadBackgroundImage();
                updateBrushSizeDisplay();
            }

            // ✅ حمّل الخلفية بمسار صحيح من الـ Blade مع بدائل + سماح بالرسم حتى لو فشلت الخلفية
            function loadBackgroundImage() {
                const primary = canvas.dataset.bg;
                const alt = canvas.dataset.bgAlt;
                const sources = [primary, alt].filter(Boolean);

                let idx = 0;
                const tryLoad = () => {
                    if (idx >= sources.length) {
                        // فشل تحميل أي خلفية → نسمح بالرسم بخلفية بيضاء
                        isImageLoaded = true;
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        redraw();
                        return;
                    }
                    img = new Image();
                    img.onload = () => {
                        isImageLoaded = true;
                        redraw();
                    };
                    img.onerror = () => {
                        idx++;
                        tryLoad();
                    };
                    img.src = sources[idx];
                };
                tryLoad();
            }

            function setupEventListeners() {
                canvas.addEventListener('mousedown', startDrawing);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', stopDrawing);
                canvas.addEventListener('mouseout', stopDrawing);

                canvas.addEventListener('touchstart', handleTouchStart, {
                    passive: false
                });
                canvas.addEventListener('touchmove', handleTouchMove, {
                    passive: false
                });
                canvas.addEventListener('touchend', handleTouchEnd, {
                    passive: false
                });

                document.getElementById('color')?.addEventListener('input', e => color = e.target
                    .value);
                document.getElementById('size')?.addEventListener('input', e => {
                    size = parseInt(e.target.value);
                    updateBrushSizeDisplay();
                });

                document.getElementById('newDrawing')?.addEventListener('click', confirmNewDrawing);
                document.getElementById('undo')?.addEventListener('click', undoLastAction);
                document.getElementById('rotate')?.addEventListener('click', rotateCanvas);

                // ✅ زر حفظ رسم Fundus فقط
                document.getElementById('saveFundusBtn')?.addEventListener('click', saveDrawing);
            }

            function startDrawing(e) {
                // حتى لو الخلفية فشلت، isImageLoaded هيكون true من fallback
                if (!isImageLoaded) return;
                drawing = true;
                currentPath = [];
                const pos = getCanvasCoordinates(e);
                startNewPath(pos.x, pos.y);
            }

            function draw(e) {
                if (!drawing || !isImageLoaded) return;
                const pos = getCanvasCoordinates(e);
                drawLine(pos.x, pos.y);
                currentPath.push({
                    x: pos.x,
                    y: pos.y
                });
            }

            function stopDrawing() {
                if (drawing && currentPath.length > 0) {
                    shapes.push({
                        path: [...currentPath],
                        color,
                        size
                    });
                }
                drawing = false;
            }

            function startNewPath(x, y) {
                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.lineWidth = size;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = color;
                // ✅ نضمن إن فيه نقطة واحدة على الأقل علشان ما تطلعش "لا يوجد رسم"
                currentPath.push({
                    x,
                    y
                });
            }

            function drawLine(x, y) {
                ctx.lineTo(x, y);
                ctx.stroke();
            }

            function getCanvasCoordinates(e) {
                const rect = canvas.getBoundingClientRect();
                const isTouch = e.type.includes('touch');
                const x = isTouch ? e.touches[0].clientX - rect.left : e.clientX - rect.left;
                const y = isTouch ? e.touches[0].clientY - rect.top : e.clientY - rect.top;
                return {
                    x,
                    y
                };
            }

            function handleTouchStart(e) {
                e.preventDefault();
                startDrawing(e);
            }

            function handleTouchMove(e) {
                e.preventDefault();
                draw(e);
            }

            function handleTouchEnd(e) {
                e.preventDefault();
                stopDrawing();
            }

            function undoLastAction() {
                if (shapes.length > 0) shapes.pop();
                redraw();
            }

            function confirmNewDrawing() {
                if (shapes.length > 0 && confirm('هل تريد بدء رسم جديد؟')) {
                    shapes = [];
                    redraw();
                }
            }

            function rotateCanvas() {
                angle = (angle + 90) % 360;
                redraw();
            }

            function redraw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.save();
                if (angle !== 0) {
                    ctx.translate(canvas.width / 2, canvas.height / 2);
                    ctx.rotate(angle * Math.PI / 180);
                    ctx.translate(-canvas.width / 2, -canvas.height / 2);
                }

                // ارسم الخلفية لو متاحة
                try {
                    if (img && img.complete && img.naturalWidth) {
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    } else {
                        // خلفية بيضاء كفfallback
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                    }
                } catch (_) {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                }

                // ارسم المسارات
                shapes.forEach(shape => {
                    ctx.beginPath();
                    ctx.strokeStyle = shape.color;
                    ctx.lineWidth = shape.size;
                    shape.path.forEach((p, i) => i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p
                        .x, p.y));
                    ctx.stroke();
                });
                ctx.restore();
            }

            function updateBrushSizeDisplay() {
                const el = document.getElementById('sizeValue');
                if (el) el.textContent = size;
            }

           function saveDrawing() {
    const canvas = document.getElementById('eyeCanvas');
    const saveBtn = document.getElementById('saveFundusBtn');
    const originalText = saveBtn.innerHTML;

    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
    saveBtn.disabled = true;

    // ⛔ مش هيعمل تحميل .. هيحول الصورة Base64 فقط
    const base64data = canvas.toDataURL('image/png');

    const data = {
        image_data: base64data,
        patient_id: canvas.dataset.patient || '',
        visit_id: canvas.dataset.visit || '',
        reservation_id: canvas.dataset.reservation || '',
        notes: document.getElementById('fundusComment')?.value || '',
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    fetch("{{ route('ajax.saveFundusImage') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": data._token,
            "Accept": "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;

        if (res.success) {
            console.log(res.message); // ✅ يظهر في الكونسول فقط
           // أو رسالة صغيرة للمستخدم
        } else {
            alert("فشل الحفظ: " + res.message);
        }
    })
    .catch(err => {
        console.error(err);
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        alert("خطأ في الاتصال بالسيرفر");
    });
}



            return {
                init,
                saveDrawing
            };
        })();

        EyeDrawingTool.init();
    });
</script>
