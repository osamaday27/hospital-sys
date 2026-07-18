<div class="dental-app">
    <div class="row">
        <!-- Top Control Panel -->
        <div class="col-12 dental-controls">
            <div class="controls-container">
                <!-- Selected Tooth Info -->
                <div class="selected-tooth-card">
                    <div class="tooth-icon">
                        @if($selectedTooth)
                            <div class="tooth-svg-wrapper {{ implode(' ', array_keys(array_filter($actions))) }}">
                                 <x-tooth-svg :tooth="$selectedTooth" class="tooth-svg" />
                            </div>
                        @else
                            <i class="fas fa-tooth"></i>
                        @endif
                    </div>
                    <div class="tooth-info">
                        <h5>Selected Tooth</h5>
                        <p>{{ $selectedTooth ? 'Tooth No. '.$selectedTooth : 'Not Selected' }}</p>
                    </div>
                </div>

                <!-- Tooth Actions Toolbar -->
                <div class="tooth-actions-toolbar">
                    <div class="actions-grid">
                        <button class="action-btn {{ $actions['missing'] ? 'active' : '' }}" wire:click="toggleAction('missing')" title="Missing">
                            <i class="bx bx-x-circle"></i>
                            <span>Missing</span>
                        </button>

                        <button class="action-btn {{ $actions['filled'] ? 'active' : '' }}" wire:click="toggleAction('filled')" title="Filled">
                            <i class="bx bx-fill"></i>
                            <span>Filled</span>
                        </button>

                        <button class="action-btn {{ $actions['crowned'] ? 'active' : '' }}" wire:click="toggleAction('crowned')" title="Crown">
                            <i class="bx bx-crown"></i>
                            <span>Crown</span>
                        </button>

                        <button class="action-btn {{ $actions['abscess'] ? 'active' : '' }}" wire:click="toggleAction('abscess')" title="Abscess">
                            <i class="bx bx-radio-circle"></i>
                            <span>Abscess</span>
                        </button>

                        <button class="action-btn {{ $actions['implant'] ? 'active' : '' }}" wire:click="toggleAction('implant')" title="Implant">
                            <i class="bx bx-dental"></i>
                            <span>Implant</span>
                        </button>

                        <button class="action-btn {{ $actions['extracted'] ? 'active' : '' }}" wire:click="toggleAction('extracted')" title="Extracted">
                            <i class="bx bx-minus-circle"></i>
                            <span>Extracted</span>
                        </button>
                    </div>
                </div>

                <!-- Main Controls -->
                <div class="main-controls">
                    <button wire:click="save" class="control-btn save-btn" title="Save Changes">
                        <i class="bx bxs-save"></i>
                        <span>Save</span>
                    </button>
                    <button wire:click="clearSelection" class="control-btn cancel-btn" title="Clear Selection">
                        <i class="bx bx-minus"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Dental Chart -->
        <div class="col-12 dental-chart-container">
            <!-- Dental Chart Filter -->
            <div class="chart-filter">
                <div class="view-switcher">
                    <button wire:click="setViewType('all')" class="view-btn {{ $viewType === 'all' ? 'active' : '' }}">
                        <i class="bx bxs-spreadsheet"></i>
                        <span>All</span>
                    </button>
                    <button wire:click="setViewType('permanent')" class="view-btn {{ $viewType === 'permanent' ? 'active' : '' }}">
                        <i class="bx bxs-user-detail"></i>
                        <span>Permanent</span>
                    </button>
                    <button wire:click="setViewType('primary')" class="view-btn {{ $viewType === 'primary' ? 'active' : '' }}">
                        <i class="bx bxs-user"></i>
                        <span>Primary</span>
                    </button>
                </div>
                
                <div class="zoom-controls">
                    <button class="zoom-btn" onclick="zoomIn()" title="Zoom In">
                       <i class="ri-zoom-in-line"></i>
                    </button>
                    <button class="zoom-btn" onclick="zoomOut()" title="Zoom Out">
                         <i class="ri-zoom-out-line"></i>
                    </button>
                    <button class="zoom-btn" onclick="resetZoom()" title="Reset">
                    <i class="bx bx-expand-horizontal"></i>
                    </button>
                </div>
            </div>

            <!-- Dental Chart -->
            <div class="dental-chart-wrapper">
                <div class="dental-chart" id="dentalChart">
                    {{-- Permanent Teeth --}}
                    @if ($viewType === 'permanent' || $viewType === 'all')
                        <div class="permanent-teeth-section">
                            <div class="jaw upper-jaw">
                                <div class="quadrant quadrant-1">
                                    <div class="quadrant-label">1st Quadrant (Right Upper)</div>
                                    <div class="teeth-row">
                                        @foreach ([1, 2, 3, 4, 5, 6, 7, 8] as $tooth)
                                            @include('livewire.dental.tooth-permanent', [
                                                'tooth' => $tooth,
                                                'selected' => $selectedTooth == $tooth,
                                                'status' => $toothStatuses[$tooth] ?? []
                                            ])
                                        @endforeach
                                    </div>
                                </div>

                                <div class="quadrant quadrant-2">
                                    <div class="quadrant-label">2nd Quadrant (Left Upper)</div>
                                    <div class="teeth-row">
                                        @foreach ([9, 10, 11, 12, 13, 14, 15, 16] as $tooth)
                                            @include('livewire.dental.tooth-permanent', [
                                                'tooth' => $tooth,
                                                'selected' => $selectedTooth == $tooth,
                                                'status' => $toothStatuses[$tooth] ?? []
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="jaw lower-jaw">
                                <div class="quadrant quadrant-4">
                                    <div class="quadrant-label">4th Quadrant (Right Lower)</div>
                                    <div class="teeth-row">
                                        @foreach ([32, 31, 30, 29, 28, 27, 26, 25] as $tooth)
                                            @include('livewire.dental.tooth-permanent', [
                                                'tooth' => $tooth,
                                                'selected' => $selectedTooth == $tooth,
                                                'status' => $toothStatuses[$tooth] ?? []
                                            ])
                                        @endforeach
                                    </div>
                                </div>

                                <div class="quadrant quadrant-3">
                                    <div class="quadrant-label">3rd Quadrant (Left Lower)</div>
                                    <div class="teeth-row">
                                        @foreach ([24, 23, 22, 21, 20, 19, 18, 17] as $tooth)
                                            @include('livewire.dental.tooth-permanent', [
                                                'tooth' => $tooth,
                                                'selected' => $selectedTooth == $tooth,
                                                'status' => $toothStatuses[$tooth] ?? []
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Primary Teeth --}}
                    @if ($viewType === 'primary' || $viewType === 'all')
                        <div class="primary-teeth-section">
                            <div class="jaw upper-jaw">
                                <div class="quadrant quadrant-1">
                                    <div class="quadrant-label">1st Quadrant (Right Upper)</div>
                                    <div class="teeth-row">
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $tooth)
                                            @include('livewire.dental.tooth-primary', [
                                                'tooth' => $tooth,
                                                'selected' => $selectedTooth == $tooth,
                                                'status' => $toothStatuses[$tooth] ?? []
                                            ])
                                        @endforeach
                                    </div>
                                </div>

                                <div class="quadrant quadrant-2">
                                    <div class="quadrant-label">2nd Quadrant (Left Upper)</div>
                                    <div class="teeth-row">
                                        @foreach (['F', 'G', 'H', 'I', 'J'] as $tooth)
                                            @include('livewire.dental.tooth-primary', [
                                                'tooth' => $tooth,
                                                'selected' => $selectedTooth == $tooth,
                                                'status' => $toothStatuses[$tooth] ?? []
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="jaw lower-jaw">
                                <div class="quadrant quadrant-4">
                                    <div class="quadrant-label">4th Quadrant (Right Lower)</div>
                                    <div class="teeth-row">
                                        @foreach (['T', 'S', 'R', 'Q', 'P'] as $tooth)
                                            @include('livewire.dental.tooth-primary', [
                                                'tooth' => $tooth,
                                                'selected' => $selectedTooth == $tooth,
                                                'status' => $toothStatuses[$tooth] ?? []
                                            ])
                                        @endforeach
                                    </div>
                                </div>

                                <div class="quadrant quadrant-3">
                                    <div class="quadrant-label">3rd Quadrant (Left Lower)</div>
                                    <div class="teeth-row">
                                        @foreach (['O', 'N', 'M', 'L', 'K'] as $tooth)
                                            @include('livewire.dental.tooth-primary', [
                                                'tooth' => $tooth,
                                                'selected' => $selectedTooth == $tooth,
                                                'status' => $toothStatuses[$tooth] ?? []
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Sidebar Info Panel -->
        @if ($selectedTooth && !$loadingTooth)
        <div class="col-md-4 tooth-details-sidebar {{ $selectedTooth ? 'show' : '' }}">
            <div class="tooth-details-container">
                <h4 class="sidebar-title">
                    <i class="fas fa-info-circle"></i>
                    Tooth Details {{ $selectedTooth }}
                </h4>
                
                <!-- Data Form -->
                <div class="tooth-form">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-comment-medical"></i> Complaint
                        </label>
                        <textarea wire:model.defer="complaint" class="form-control" rows="2" placeholder="Patient complaint description..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-stethoscope"></i> Diagnosis
                        </label>
                        <textarea wire:model.defer="diagnosis" class="form-control" rows="2" placeholder="Clinical examination results..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clipboard-list"></i> Treatment Plan
                        </label>
                        <textarea wire:model.defer="treatment_plan" class="form-control" rows="2" placeholder="Suggested treatments..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-history"></i> Additional Notes
                        </label>
                        <textarea wire:model.defer="history" class="form-control" rows="2" placeholder="Additional tooth notes..."></textarea>
                    </div>
                </div>

                <!-- Change History -->
                @if (count($toothHistory))
                    <div class="tooth-history">
                        <h5 class="history-title">
                            <i class="fas fa-history"></i>
                            Change History
                        </h5>
                        <div class="history-items">
                            @foreach ($toothHistory as $log)
                                <div class="history-item">
                                    <div class="history-header">
                                        <span class="history-date">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                                        <span class="history-user">{{ $log->user->name ?? 'System' }}</span>
                                    </div>
                                    <div class="history-status">
                                        @if($log->missing) <span class="status-badge missing">Missing</span> @endif
                                        @if($log->filled) <span class="status-badge filled">Filled</span> @endif
                                        @if($log->crowned) <span class="status-badge crowned">Crown</span> @endif
                                        @if($log->abscess) <span class="status-badge abscess">Abscess</span> @endif
                                        @if($log->implant) <span class="status-badge implant">Implant</span> @endif
                                        @if($log->extracted) <span class="status-badge extracted">Extracted</span> @endif
                                    </div>
                                    @if($log->complaint || $log->diagnosis || $log->treatment_plan)
                                        <div class="history-details">
                                            @if($log->complaint)
                                                <p><strong>Complaint:</strong> {{ $log->complaint }}</p>
                                            @endif
                                            @if($log->diagnosis)
                                                <p><strong>Diagnosis:</strong> {{ $log->diagnosis }}</p>
                                            @endif
                                            @if($log->treatment_plan)
                                                <p><strong>Plan:</strong> {{ $log->treatment_plan }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>



<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --accent-color: #4895ef;
    --danger-color: #f72585;
    --warning-color: #f8961e;
    --success-color: #4cc9f0;
    --info-color: #4895ef;
    --light-bg: #f8f9fa;
    --dark-text: #212529;
    --border-radius: 10px;
    --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    --transition: all 0.25s ease;
}

.dental-app {
    font-family: 'Tajawal', 'Segoe UI', sans-serif;
    background-color: #f5f7fa;
}

/* لوحة التحكم العلوية */
.dental-controls {
    background-color: white;
    padding: 1rem;
    box-shadow: var(--box-shadow);
    margin-bottom: 1rem;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.controls-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.selected-tooth-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background-color: rgba(67, 97, 238, 0.1);
    padding: 0.75rem 1.25rem;
    border-radius: var(--border-radius);
    min-width: 200px;
}

.tooth-icon {
    width: 40px;
    height: 40px;
    background-color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary-color);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.tooth-info h5 {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.2rem;
    color: #6c757d;
}

.tooth-info p {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0;
    color: var(--primary-color);
}

/* شريط أدوات التحكم */
.tooth-actions-toolbar {
    flex: 1;
    min-width: 300px;
}

.actions-grid {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background-color: white;
    border: 1px solid #e9ecef;
    border-radius: var(--border-radius);
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 2px 3px rgba(0,0,0,0.05);
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.action-btn.active {
    color: white;
    border-color: transparent;
    background-color: var(--primary-color);
}

.action-btn i {
    font-size: 1rem;
}

.action-btn.active.missing { background-color: var(--danger-color); }
.action-btn.active.filled { background-color: var(--accent-color); }
.action-btn.active.crowned { background-color: var(--warning-color); }
.action-btn.active.abscess { background-color: #dc3545; }
.action-btn.active.implant { background-color: var(--success-color); }
.action-btn.active.extracted { background-color: #6c757d; }

/* أزرار التحكم الرئيسية */
.main-controls {
    display: flex;
    gap: 0.5rem;
}

.control-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
}

.control-btn i {
    font-size: 1rem;
}

.save-btn {
    background-color: var(--primary-color);
    color: white;
}

.save-btn:hover {
    background-color: var(--secondary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.cancel-btn {
    background-color: white;
    color: #6c757d;
    border: 1px solid #e9ecef;
}

.cancel-btn:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.print-btn {
    background-color: #28a745;
    color: white;
}

.print-btn:hover {
    background-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* فلتر عرض الأسنان */
.chart-filter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.5rem 0;
}

.view-switcher {
    display: flex;
    gap: 0.5rem;
}

.view-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background-color: white;
    border: 1px solid #e9ecef;
    border-radius: var(--border-radius);
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
    transition: var(--transition);
}

.view-btn:hover {
    background-color: #f8f9fa;
}

.view-btn.active {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.view-btn i {
    font-size: 1rem;
}

.zoom-controls {
    display: flex;
    gap: 0.5rem;
}

.zoom-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: white;
    border: 1px solid #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.zoom-btn:hover {
    background-color: #f8f9fa;
    transform: scale(1.1);
}

/* خريطة الأسنان */
.dental-chart-container {
    padding: 0 1rem;
    margin-bottom: 1.5rem;
}

.dental-chart-wrapper {
    overflow: auto;
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 1rem;
    height: calc(100vh - 200px);
    min-height: 500px;
}

.dental-chart {
    min-width: 900px;
    position: relative;
    transition: all 0.3s ease;
}

/* خريطة الأسنان - التصميم */
.jaw {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.quadrant {
    flex: 1;
    margin: 0 0.5rem;
}

.quadrant-label {
    text-align: center;
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 1rem;
    font-weight: 600;
}

.teeth-row {
    display: flex;
    justify-content: center;
    gap: 0.8rem;
}

.tooth-container {
    position: relative;
    cursor: pointer;
    transition: var(--transition);
    width: 50px;
    height: 70px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.tooth-container:hover {
    transform: translateY(-5px);
}

.tooth-container.selected {
    transform: translateY(-5px) scale(1.1);
    z-index: 10;
}

.tooth-permanent, .tooth-primary {
    width: 100%;
    height: 100%;
    position: relative;
    transition: var(--transition);
}

.tooth-surface {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    backface-visibility: hidden;
}

.tooth-number {
    position: absolute;
    font-weight: bold;
    font-size: 0.8rem;
    z-index: 10;
    color: white;
    text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    background-color: var(--primary-color);
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

/* أشكال الأسنان */
.tooth-permanent .tooth-surface {
    background-color: #f8f9fa;
    border: 2px solid #ced4da;
    border-radius: 50% 50% 0 0;
    clip-path: polygon(0% 0%, 100% 0%, 80% 100%, 20% 100%);
}

.tooth-primary .tooth-surface {
    background-color: #f8f9fa;
    border: 2px solid #ced4da;
    border-radius: 50%;
    width: 80%;
    height: 80%;
    margin: 10%;
}

/* حالات الأسنان */
.tooth-permanent.missing .tooth-surface, 
.tooth-primary.missing .tooth-surface {
    background-color: #f8f9fa;
    border: 2px dashed #adb5bd;
    position: relative;
}

.tooth-permanent.missing .tooth-surface::after, 
.tooth-primary.missing .tooth-surface::after {
    content: "✖";
    position: absolute;
    font-size: 1.5rem;
    color: var(--danger-color);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.7;
}

.tooth-permanent.abscess .tooth-surface, 
.tooth-primary.abscess .tooth-surface {
    animation: pulse 1.5s infinite;
    border: 3px solid #dc3545;
}

.tooth-permanent.filled .tooth-surface, 
.tooth-primary.filled .tooth-surface {
    background-color: rgba(23, 162, 184, 0.2);
    border: 2px solid var(--accent-color);
}

.tooth-permanent.crowned .tooth-surface, 
.tooth-primary.crowned .tooth-surface {
    background-color: rgba(255, 193, 7, 0.3);
    border: 2px solid var(--warning-color);
}

.tooth-permanent.implant .tooth-surface, 
.tooth-primary.implant .tooth-surface {
    background-color: rgba(40, 167, 69, 0.2);
    border: 2px solid var(--success-color);
}

.tooth-permanent.extracted .tooth-surface, 
.tooth-primary.extracted .tooth-surface {
    background-color: rgba(108, 117, 125, 0.2);
    border: 2px dashed #6c757d;
}

/* لوحة التفاصيل الجانبية */
.tooth-details-sidebar {
    position: fixed;
    top: 0;
    right: 0;
    height: 100vh;
    width: 350px;
    background-color: white;
    box-shadow: -5px 0 15px rgba(0,0,0,0.1);
    z-index: 1050;
    overflow-y: auto;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.tooth-details-sidebar.show {
    transform: translateX(0);
}

.tooth-details-container {
    padding: 1.5rem;
}

.sidebar-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tooth-form {
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.2rem;
}

.form-label {
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-control {
    border: 1px solid #e9ecef;
    border-radius: var(--border-radius);
    padding: 0.75rem;
    width: 100%;
    transition: var(--transition);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
}

/* سجل التعديلات */
.tooth-history {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.history-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.history-items {
    max-height: 300px;
    overflow-y: auto;
}

.history-item {
    background-color: white;
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: var(--box-shadow);
}

.history-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.history-date {
    font-size: 0.8rem;
    color: #6c757d;
}

.history-user {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--primary-color);
    background-color: rgba(67, 97, 238, 0.1);
    padding: 0.2rem 0.5rem;
    border-radius: 50px;
}

.history-status {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.8rem;
}

.status-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.6rem;
    border-radius: 50px;
    font-weight: 600;
}

.status-badge.missing { background-color: rgba(247, 37, 133, 0.1); color: var(--danger-color); }
.status-badge.filled { background-color: rgba(72, 149, 239, 0.1); color: var(--accent-color); }
.status-badge.crowned { background-color: rgba(248, 150, 30, 0.1); color: var(--warning-color); }
.status-badge.abscess { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
.status-badge.implant { background-color: rgba(76, 201, 240, 0.1); color: var(--success-color); }
.status-badge.extracted { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }

.history-details {
    font-size: 0.9rem;
    color: #495057;
    line-height: 1.5;
}

.history-details p {
    margin-bottom: 0.3rem;
}

.history-details strong {
    color: var(--dark-text);
}

/* تأثيرات */
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-5px); }
    40%, 80% { transform: translateX(5px); }
}

/* التكيف مع الشاشات الصغيرة */
@media (max-width: 1200px) {
    .controls-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .tooth-actions-toolbar {
        min-width: 100%;
    }
    
    .actions-grid {
        justify-content: center;
    }
    
    .main-controls {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .action-btn span {
        display: none;
    }
    
    .action-btn {
        padding: 0.5rem;
        width: 40px;
        height: 40px;
        justify-content: center;
    }
    
    .control-btn span {
        display: none;
    }
    
    .view-btn span {
        display: none;
    }
    
    .dental-chart-wrapper {
        height: calc(100vh - 250px);
    }
    
    .tooth-details-sidebar {
        width: 100%;
    }
}
</style>

<script>
// دوال التكبير والتصغير
function zoomIn() {
    const chart = document.getElementById('dentalChart');
    const currentScale = parseFloat(chart.style.transform.replace('scale(', '').replace(')', '')) || 1;
    chart.style.transform = `scale(${currentScale + 0.1})`;
}

function zoomOut() {
    const chart = document.getElementById('dentalChart');
    const currentScale = parseFloat(chart.style.transform.replace('scale(', '').replace(')', '')) || 1;
    if (currentScale > 0.5) {
        chart.style.transform = `scale(${currentScale - 0.1})`;
    }
}

function resetZoom() {
    document.getElementById('dentalChart').style.transform = 'scale(1)';
}

// تأثيرات عند التفاعل
document.addEventListener('livewire:load', function() {
    // تأثير اهتزاز عند تحديد سن
    Livewire.on('toothSelected', () => {
        const tooth = document.querySelector('.tooth-container.selected');
        if (tooth) {
            tooth.style.animation = 'shake 0.5s';
            setTimeout(() => {
                tooth.style.animation = '';
            }, 500);
        }
        
        // إظهار لوحة التفاصيل
        document.querySelector('.tooth-details-sidebar').classList.add('show');
    });
    
    // تحديث حالة الأسنان عند التغيير
    Livewire.on('actionToggled', () => {
        const selectedTooth = document.querySelector('.tooth-container.selected .tooth-surface');
        if (selectedTooth) {
            selectedTooth.style.animation = 'pulse 0.5s';
            setTimeout(() => {
                selectedTooth.style.animation = '';
            }, 500);
        }
    });
    
    // إخفاء لوحة التفاصيل عند إلغاء التحديد
    Livewire.on('clearSelection', () => {
        document.querySelector('.tooth-details-sidebar').classList.remove('show');
    });
});

// إغلاق لوحة التفاصيل يدوياً
function closeSidebar() {
    document.querySelector('.tooth-details-sidebar').classList.remove('show');
    Livewire.emit('clearSelection');
}
</script>
