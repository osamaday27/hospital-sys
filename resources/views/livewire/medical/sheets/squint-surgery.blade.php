<div>
    {{-- ✅ Alerts --}}
    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session()->has($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg no-print" style="z-index:1055;">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="py-4">
        {{-- 🔹 Header --}}
        <div class="text-center bg-info text-white rounded-4 p-4 mb-4 shadow-sm">
            <h2 class="fw-bold mb-2"><i class="bi bi-eye"></i> Squint Surgery Report</h2>
            <small>EyeWorld Hospital - Giza, Egypt</small>
        </div>

        {{-- 🧍 Patient Info --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold"><i class="bi bi-person-badge me-2"></i> Patient Information</div>
            <div class="card-body row g-3">
                <div class="col-md-4"><label class="form-label">Name</label><div class="form-control-plaintext">{{ $patient->full_name ?? 'N/A' }}</div></div>
                <div class="col-md-4"><label class="form-label">Age</label><div class="form-control-plaintext">{{ $age }} years</div></div>
                <div class="col-md-4"><label class="form-label">Gender</label><div class="form-control-plaintext">{{ ucfirst($patient->gender ?? 'N/A') }}</div></div>
            </div>
        </div>

        {{-- 🔹 Tabs --}}
        <ul class="nav nav-tabs mb-3" role="tablist">
            @foreach (['preop'=>'Preoperative', 'operative'=>'Operative Details', 'complications'=>'Complications'] as $tab => $label)
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link @if($activeTab===$tab) active @endif" wire:click="setActiveTab('{{ $tab }}')" role="tab">
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">

            {{-- 🩺 Preoperative --}}
            <div class="tab-pane fade @if($activeTab==='preop') show active @endif" id="preop" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Preoperative Details</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3"><label>Eye</label>
                            <select wire:model="eye" class="form-select"><option value="">Select...</option><option>OD</option><option>OS</option><option>OU</option></select></div>
                        <div class="col-md-3"><label>Anesthesia</label>
                            <select wire:model="anesthesia" class="form-select"><option value="">Select...</option><option>General</option><option>Local</option></select></div>
                        <div class="col-md-6"><label>Squint Type</label>
                            <select wire:model="squint_type" class="form-select">
                                <option value="">Select...</option>
                                <option>Concomitant esotropia</option>
                                <option>Concomitant exotropia</option>
                                <option>Concomitant hypertropia</option>
                                <option>Paralytic squint (VI nerve palsy)</option>
                                <option>DVD</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔬 Operative --}}
            <div class="tab-pane fade @if($activeTab==='operative') show active @endif" id="operative" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Operative Details</div>
                    <div class="card-body row g-3">
                        <div class="col-md-4"><label>Muscle</label>
                            <select wire:model="muscle" class="form-select">
                                <option>Lateral rectus</option><option>Medial rectus</option>
                                <option>Superior rectus</option><option>Inferior rectus</option>
                                <option>Superior oblique</option><option>Inferior oblique</option>
                            </select>
                        </div>

                        <div class="col-md-4"><label>Procedure Type</label>
                            <select wire:model="procedure_type" class="form-select">
                                <option>Recession</option><option>Resection</option><option>Myotomy</option><option>Expander</option>
                            </select>
                        </div>

                        <div class="col-md-4"><label>Recession (mm)</label><input wire:model="mm_recession" type="number" step="0.1" class="form-control"></div>
                        <div class="col-md-4"><label>Resection (mm)</label><input wire:model="mm_resection" type="number" step="0.1" class="form-control"></div>
                        <div class="col-md-4"><label>Other (mm)</label><input wire:model="other_mm" type="text" class="form-control"></div>

                        <div class="col-md-6"><label>Additional Procedure</label>
                            <input wire:model="additional_procedure" type="text" class="form-control" placeholder="If any...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ⚠️ Complications --}}
            <div class="tab-pane fade @if($activeTab==='complications') show active @endif" id="complications" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Narrative & Complications</div>
                    <div class="card-body">
                        <textarea wire:model.defer="narrative" rows="8" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✍️ Signature --}}
        <div class="border-top pt-4 text-end mb-4">
            <h5 class="fw-bold text-info mb-1">Dr. Ihab Saad Othman, MD, FRCS (Glasg)</h5>
            <small class="d-block text-muted">Professor of Ophthalmology, Cairo University</small>
            <small class="d-block text-muted">Director, EyeWorld Hospital, Giza, Egypt</small>
        </div>

        {{-- 🧾 Buttons --}}
        <div class="d-flex justify-content-center gap-3 no-print">
            <button wire:click="saveReport" class="btn btn-info"><i class="bi bi-save me-1"></i> Save Report</button>
        </div>
    </div>
</div>
