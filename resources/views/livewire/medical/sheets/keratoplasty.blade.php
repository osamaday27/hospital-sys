<div>
    {{-- ✅ Flash Messages --}}
    @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type)
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
            <h2 class="fw-bold mb-2"><i class="bi bi-eye"></i> Keratoplasty Surgery Report</h2>
            <small>EyeWorld Hospital - Giza, Egypt</small>
        </div>

        {{-- 🧍 Patient Info --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold"><i class="bi bi-person-badge me-2"></i> Patient Information</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Name</label>
                    <div class="form-control-plaintext">{{ $patient->full_name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Age</label>
                    <div class="form-control-plaintext">{{ $age }} years</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <div class="form-control-plaintext">{{ ucfirst($patient->gender ?? 'N/A') }}</div>
                </div>
            </div>
        </div>

        {{-- ⚙️ Surgery Details --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard2-pulse me-2"></i> Surgery Details</span>
                @if($completionPercentage>0)
                    <span class="badge bg-info">{{ $completionPercentage }}% Complete</span>
                @endif
            </div>
            <div class="card-body row g-3">
                {{-- Eye --}}
                <div class="col-md-4">
                    <label class="form-label">Eye</label>
                    <select wire:model="eye" class="form-select">
                        <option value="">Select...</option>
                        <option value="OD">OD (Right Eye)</option>
                        <option value="OS">OS (Left Eye)</option>
                        <option value="OD/OS">OD/OS (Both)</option>
                    </select>
                </div>

                {{-- Anesthesia --}}
                <div class="col-md-4">
                    <label class="form-label">Anesthesia</label>
                    <select wire:model="anesthesia" class="form-select">
                        <option value="">Select...</option>
                        <option value="General anesthesia">General anesthesia</option>
                        <option value="Local anesthesia">Local anesthesia</option>
                    </select>
                </div>

                {{-- Surgery Type --}}
                <div class="col-md-4">
                    <label class="form-label">Surgery Type</label>
                    <select wire:model="surgeryType" class="form-select">
                        <option value="">Select...</option>
                        <option value="Penetrating Keratoplasty (PKP)">Penetrating Keratoplasty (PKP)</option>
                        <option value="Deep Anterior Lamellar Keratoplasty (DALK)">Deep Anterior Lamellar Keratoplasty (DALK)</option>
                        <option value="Descemet Membrane Endothelial Keratoplasty (DMEK)">Descemet Membrane Endothelial Keratoplasty (DMEK)</option>
                        <option value="Descemet Stripping Endothelial Keratoplasty (DSEK)">Descemet Stripping Endothelial Keratoplasty (DSEK)</option>
                        <option value="Boston Keratoprosthesis (KPro)">Boston Keratoprosthesis (KPro)</option>
                    </select>
                </div>

                {{-- Pre-Op Diagnosis --}}
                <div class="col-md-6">
                    <label class="form-label">Preoperative Diagnosis</label>
                    <select wire:model="preOpDiagnosis" class="form-select">
                        <option value="">Select...</option>
                        <option>Corneal scar</option>
                        <option>Keratoconus</option>
                        <option>Corneal dystrophy</option>
                        <option>Corneal perforation</option>
                        <option>Corneal opacity</option>
                        <option>Graft failure</option>
                        <option>Corneal ulcer</option>
                    </select>
                </div>

                {{-- Graft Type --}}
                <div class="col-md-6">
                    <label class="form-label">Graft Type</label>
                    <select wire:model="graftType" class="form-select">
                        <option value="">Select...</option>
                        <option value="Full-thickness donor cornea">Full-thickness donor cornea</option>
                        <option value="Lamellar donor graft">Lamellar donor graft</option>
                        <option value="Descemet membrane graft">Descemet membrane graft</option>
                        <option value="Boston Keratoprosthesis">Boston Keratoprosthesis</option>
                    </select>
                </div>

                {{-- Trephine Size --}}
                <div class="col-md-4">
                    <label class="form-label">Trephine Size (mm)</label>
                    <select wire:model="trephineSize" class="form-select">
                        <option value="">Select...</option>
                        <option value="7.0">7.0 mm</option>
                        <option value="7.5">7.5 mm</option>
                        <option value="8.0">8.0 mm</option>
                        <option value="8.5">8.5 mm</option>
                        <option value="9.0">9.0 mm</option>
                    </select>
                </div>

                {{-- Recipient Bed Size --}}
                <div class="col-md-4">
                    <label class="form-label">Recipient Bed Size (mm)</label>
                    <select wire:model="recipientBedSize" class="form-select">
                        <option value="">Select...</option>
                        <option value="7.5">7.5 mm</option>
                        <option value="8.0">8.0 mm</option>
                        <option value="8.5">8.5 mm</option>
                        <option value="9.0">9.0 mm</option>
                    </select>
                </div>

                {{-- Suturing Type --}}
                <div class="col-md-4">
                    <label class="form-label">Suturing Type</label>
                    <select wire:model="suturingType" class="form-select">
                        <option value="">Select...</option>
                        <option value="Interrupted 10/0 nylon">Interrupted 10/0 nylon</option>
                        <option value="Continuous 10/0 nylon">Continuous 10/0 nylon</option>
                        <option value="Combined interrupted and continuous 10/0 nylon">Combined interrupted and continuous</option>
                    </select>
                </div>

                {{-- Complications --}}
                <div class="col-md-6">
                    <label class="form-label">Complications</label>
                    <select wire:model="complications" class="form-select">
                        <option value="None">None</option>
                        <option>Hyphema</option>
                        <option>Increased IOP</option>
                        <option>Graft detachment</option>
                        <option>Infection</option>
                        <option>Wound leak</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- 🩺 Operative Narrative --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-journal-text me-2"></i> Operative Narrative
            </div>
            <div class="card-body">
                <label class="form-label">Introductory Statement</label>
                <textarea wire:model.defer="narratives.intro" rows="4" class="form-control mb-3"></textarea>

                <label class="form-label">Operative Details</label>
                <textarea wire:model.defer="narratives.details" rows="6" class="form-control"></textarea>
            </div>
        </div>

        {{-- 🩵 Signature --}}
        <div class="border-top pt-4 text-end mb-4">
            <h5 class="fw-bold text-info mb-1">Dr. Ihab Saad Othman, MD, FRCS (Glasg)</h5>
            <small class="d-block text-muted">Professor of Ophthalmology, Cairo University</small>
            <small class="d-block text-muted">Director, EyeWorld Hospital, Giza, Egypt</small>
        </div>

        {{-- 🔘 Buttons --}}
        <div class="d-flex justify-content-center gap-3 no-print">
            <button wire:click="saveReport" class="btn btn-info">
                <i class="bi bi-save me-1"></i> Save Report
            </button>
            
        </div>
    </div>
</div>
