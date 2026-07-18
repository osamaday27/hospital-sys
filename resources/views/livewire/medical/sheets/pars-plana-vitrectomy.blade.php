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
            <h2 class="fw-bold mb-2">
                <i class="bi bi-eye"></i> Pars Plana Vitrectomy Report
            </h2>
            <small>EyeWorld Hospital - Giza, Egypt</small>
        </div>

        {{-- 🧍 Patient Info --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-person-badge me-2"></i> Patient Information
            </div>
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

        {{-- 🔹 Tabs Navigation --}}
        <ul class="nav nav-tabs mb-3" role="tablist">
            @foreach ([
                'preop' => 'Preoperative',
                'operative' => 'Operative Details',
                'postop' => 'Postoperative Care',
                'complications' => 'Complications',
            ] as $tab => $label)
                <li class="nav-item" role="presentation">
                    <button type="button"
                            class="nav-link @if($activeTab === $tab) active @endif"
                            wire:click="setActiveTab('{{ $tab }}')"
                            role="tab"
                            aria-selected="{{ $activeTab === $tab ? 'true' : 'false' }}">
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>

        {{-- 🔹 Tabs Content --}}
        <div class="tab-content">

            {{-- 🩺 1️⃣ Preoperative --}}
            <div class="tab-pane fade @if($activeTab === 'preop') show active @endif" id="preop" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Preoperative Details</div>
                    <div class="card-body row g-3">

                        <div class="col-md-3">
                            <label>Eye</label>
                            <select wire:model="eye" class="form-select">
                                <option value="">Select...</option>
                                <option>OD</option>
                                <option>OS</option>
                                <option>OD/OS</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Anesthesia</label>
                            <select wire:model="anesthesia" class="form-select">
                                <option value="">Select...</option>
                                <option>General</option>
                                <option>Local</option>
                                <option>Peribulbar</option>
                                <option>Subtenon</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Preoperative Diagnosis</label>
                            <select wire:model="preOpDiagnosis" class="form-select">
                                <option value="">Select...</option>
                                <option>Rhegmatogenous retinal detachment</option>
                                <option>Tractional retinal detachment</option>
                                <option>Macular hole</option>
                                <option>Epiretinal membrane</option>
                                <option>Vitreous hemorrhage</option>
                                <option>ROP stage 4</option>
                                <option>ROP stage 5</option>
                                <option>Combined</option>
                            </select>
                        </div>

                        @if($preOpDiagnosis === 'Rhegmatogenous retinal detachment')
                            <div class="col-md-3">
                                <label>PVR Grade</label>
                                <select wire:model="pvr_grade" class="form-select">
                                    <option>Grade A</option>
                                    <option>Grade B</option>
                                    <option>Grade C</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Type</label>
                                <select wire:model="retinal_type" class="form-select">
                                    <option>Primary</option>
                                    <option>Recurrent</option>
                                </select>
                            </div>
                        @endif

                        @if($preOpDiagnosis === 'Combined')
                            <div class="col-md-6">
                                <label>Combined With</label>
                                <select wire:model="combined" multiple class="form-select">
                                    <option>Phakoemulsification</option>
                                    <option>Ahmed glaucoma valve</option>
                                    <option>Keratoplasty</option>
                                    <option>Boston keratoprosthesis</option>
                                    <option>Others</option>
                                </select>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label>Postoperative Diagnosis</label>
                            <input type="text" wire:model="postOpDiagnosis" class="form-control"
                                   placeholder="Same as preoperative">
                        </div>

                    </div>
                </div>
            </div>

            {{-- 🔬 2️⃣ Operative Details --}}
            <div class="tab-pane fade @if($activeTab === 'operative') show active @endif" id="operative" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Operative Details</div>
                    <div class="card-body row g-3">

                        <div class="col-md-3">
                            <label>Trocar Gauge</label>
                            <select wire:model="trocarSize" class="form-select">
                                <option>20</option>
                                <option>23</option>
                                <option>25</option>
                                <option>27</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Trocar Position (mm from limbus)</label>
                            <select wire:model="trocarPosition" class="form-select">
                                <option>1.5 mm</option>
                                <option>2 mm</option>
                                <option>2.5 mm</option>
                                <option>3 mm</option>
                                <option>3.5 mm</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Membrane Type</label>
                            <select wire:model="membrane" class="form-select">
                                <option>Posterior hyaloid</option>
                                <option>Epiretinal membranes</option>
                                <option>Tractional membranes</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Laser Type</label>
                            <select wire:model="laser" class="form-select">
                                <option>360° Endolaser</option>
                                <option>PRP</option>
                                <option>Focal</option>
                                <option>Macular Grid</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Tamponade</label>
                            <select wire:model="tamponade" class="form-select">
                                <option>Air</option>
                                <option>Gas (SF6)</option>
                                <option>Gas (C3F8)</option>
                                <option>Silicone oil</option>
                                <option>Heavy Silicone (Densiron)</option>
                            </select>
                        </div>

                        @if($preOpDiagnosis === 'ROP stage 5')
                            <div class="col-md-3">
                                <label>Stage 5 Type</label>
                                <select wire:model="stage5" class="form-select">
                                    <option>5a</option>
                                    <option>5b</option>
                                    <option>5c</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Traction Direction</label>
                                <select wire:model="traction_direction" class="form-select">
                                    <option>Outside in</option>
                                    <option>Inside out</option>
                                </select>
                            </div>
                        @endif

                        <div class="col-md-12">
                            <label>Operative Narrative</label>
                            <textarea wire:model.defer="narratives.details" rows="8" class="form-control"></textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 💊 3️⃣ Postoperative --}}
            <div class="tab-pane fade @if($activeTab === 'postop') show active @endif" id="postop" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Postoperative Care</div>
                    <div class="card-body row g-3">
                        <div class="col-md-4">
                            <label>Positioning</label>
                            <select wire:model="position" class="form-select">
                                <option>Face down</option>
                                <option>Right ear down</option>
                                <option>Left ear down</option>
                                <option>Semi-sitting</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Medications</label>
                            <select wire:model="postOpMeds" class="form-select">
                                <option>Antibiotics + Steroids + Cycloplegics</option>
                                <option>Antibiotics + Steroids</option>
                                <option>Steroids only</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Follow-up</label>
                            <select wire:model="followUp" class="form-select">
                                <option>1 day</option>
                                <option>3 days</option>
                                <option>5 days</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ⚠️ 4️⃣ Complications --}}
            <div class="tab-pane fade @if($activeTab === 'complications') show active @endif" id="complications" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Operative Complications</div>
                    <div class="card-body">
                        <select wire:model="complications" multiple class="form-select" size="6">
                            <option>Hemorrhage</option>
                            <option>Retinal tear</option>
                            <option>Ciliary body dialysis</option>
                            <option>Retinal redetachment</option>
                            <option>Others</option>
                            <option>None</option>
                        </select>
                    </div>
                </div>
            </div>

        </div> {{-- tab-content --}}

        {{-- ✍️ Signature --}}
        <div class="border-top pt-4 text-end mb-4">
            <h5 class="fw-bold text-info mb-1">Dr. Ihab Saad Othman, MD, FRCS (Glasg)</h5>
            <small class="d-block text-muted">Professor of Ophthalmology, Cairo University</small>
            <small class="d-block text-muted">Director, EyeWorld Hospital, Giza, Egypt</small>
        </div>

        {{-- 🧾 Buttons --}}
        <div class="d-flex justify-content-center gap-3 no-print">
            <button wire:click="saveReport" class="btn btn-info">
                <i class="bi bi-save me-1"></i> Save Report
            </button>
           
        </div>

    </div>
</div>
