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
            <h2 class="fw-bold mb-2"><i class="bi bi-eye"></i> GLAUCOMA SURGERY REPORT</h2>
            <p class="mb-0">Trabeculectomy / AGV / Phako / Others</p>
            <small class="opacity-75">EyeWorld Hospital - Giza, Egypt</small>
        </div>

        {{-- 🧍‍♂️ Patient Information --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-person-circle me-2"></i> Patient Information
            </div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Patient Name</label>
                    <div class="form-control-plaintext">{{ $patient->full_name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Medical Record</label>
                    <div class="form-control-plaintext">{{ $patient->medical_record_number ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Age</label>
                    <div class="form-control-plaintext">{{ $age ?? 'N/A' }} years</div>
                </div>
            </div>
        </div>

        {{-- 🏥 Surgery Details --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-clipboard2-pulse me-2"></i> Surgery Details
            </div>

            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label class="form-label">Eye</label>
                    <select wire:model="eye" class="form-select">
                        <option value="">Select...</option>
                        <option>OD</option>
                        <option>OS</option>
                        <option>OD/OS</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Anesthesia</label>
                    <select wire:model="anesthesia" class="form-select">
                        <option value="">Select...</option>
                        <option>General anesthesia</option>
                        <option>Local anesthesia</option>
                        <option>Monitored anesthesia care</option>
                        <option>Retrobulbar block</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Surgery Type</label>
                    <select wire:model="surgeryType" class="form-select">
                        <option value="">Select...</option>
                        <option>Subscleral Trabeculectomy</option>
                        <option>Phako + Trabeculectomy + IOL</option>
                        <option>Ahmed Glaucoma Valve</option>
                        <option>Cyclophotocoagulation</option>
                        <option>Trabeculotomy</option>
                        <option>Goniotomy</option>
                        <option>Deep Sclerectomy</option>
                        <option>Non-Penetrating Glaucoma Surgery (Viscocanalostomy)</option>
                    </select>
                </div>

                {{-- Conditional Inputs --}}
                @if($surgeryType === 'Subscleral Trabeculectomy')
                    <div class="col-md-4">
                        <label class="form-label">Mitomycin</label>
                        <select wire:model="mitomycin" class="form-select">
                            <option value="">Select...</option>
                            <option>With Mitomycin C</option>
                            <option>Without Mitomycin C</option>
                        </select>
                    </div>
                @endif

                @if($surgeryType === 'Phako + Trabeculectomy + IOL')
                    <div class="col-md-4">
                        <label class="form-label">Phako Incision (mm)</label>
                        <select wire:model="phakoSize" class="form-select">
                            <option value="">Select...</option>
                            <option>2.2</option>
                            <option>2.5</option>
                            <option>2.7</option>
                            <option>3.0</option>
                        </select>
                    </div>
                @endif

                @if($surgeryType === 'Cyclophotocoagulation')
                    <div class="col-md-4">
                        <label class="form-label">Applications</label>
                        <select wire:model="applications" class="form-select">
                            <option>30</option>
                            <option>32</option>
                            <option>34</option>
                            <option>35</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Power (mW)</label>
                        <select wire:model="power" class="form-select">
                            <option>1500</option>
                            <option>2000</option>
                            <option>2500</option>
                            <option>3000</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Duration (msec)</label>
                        <select wire:model="duration" class="form-select">
                            <option>1500</option>
                            <option>2000</option>
                            <option>2500</option>
                            <option>3000</option>
                        </select>
                    </div>
                @endif

                @if($surgeryType === 'Trabeculotomy')
                    <div class="col-md-4">
                        <label class="form-label">Approach</label>
                        <select wire:model="approach" class="form-select">
                            <option>Ab Interno</option>
                            <option>Ab Externo</option>
                        </select>
                    </div>
                @endif
            </div>
        </div>

        {{-- 🩺 Operative Narrative --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-journal-text me-2"></i> Operative Narrative
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Introductory Statement</label>
                    <textarea wire:model.defer="narratives.intro" rows="4" class="form-control" placeholder="Enter introductory statement..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Operative Details</label>
                    <textarea wire:model.defer="narratives.details" rows="6" class="form-control" placeholder="Enter operative details..."></textarea>
                </div>
            </div>
        </div>

        {{-- 🖋️ Signature --}}
        <div class="border-top pt-4 text-end mb-4">
            <h5 class="fw-bold text-info mb-1">Dr. Ihab Saad Othman, MD, FRCS (Glasg)</h5>
            <small class="d-block text-muted">Professor of Ophthalmology, Cairo University</small>
            <small class="d-block text-muted">Director, EyeWorld Hospital, Giza, Egypt</small>
        </div>

        {{-- 🧭 Buttons --}}
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4 no-print">
            <button wire:click="saveReport" class="btn btn-info">
                <i class="bi bi-save me-1"></i> Save Report
            </button>
          
        </div>
    </div>

    {{-- ✅ Script --}}
    <script>
        window.addEventListener('print-report', () => window.print());
        setTimeout(() => { document.querySelectorAll('.alert').forEach(a => a.remove()); }, 4000);
    </script>
</div>
