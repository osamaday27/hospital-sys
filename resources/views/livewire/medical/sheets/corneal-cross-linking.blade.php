<div>
    {{-- ✅ Flash Messages --}}
    @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type)
        @if (session()->has($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg no-print"
                style="z-index:1055;">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="py-4">
        {{-- 🧠 Header --}}
        <div class="text-center bg-info text-white rounded-4 p-4 mb-4 shadow-sm">
            <h2 class="fw-bold mb-2"><i class="bi bi-eye"></i> CORNEAL CROSS LINKING REPORT</h2>
            <p class="mb-0">Ophthalmic Surgery Report</p>
            <small class="opacity-75">EyeWorld Hospital - Giza, Egypt</small>
        </div>

        {{-- 👁️ Patient Info --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-light fw-bold"><i class="bi bi-person-badge me-2"></i> Patient Information</div>
            <div class="card-body row g-3">
                <div class="col-md-4"><label class="form-label">Patient Name</label>
                    <div class="form-control-plaintext">{{ $patient->full_name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4"><label class="form-label">Age</label>
                    <div class="form-control-plaintext">{{ $age }} years</div>
                </div>
                <div class="col-md-4"><label class="form-label">Gender</label>
                    <div class="form-control-plaintext">{{ ucfirst($patient->gender ?? 'N/A') }}</div>
                </div>
            </div>
        </div>

        {{-- 🏥 Surgery Details --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard2-pulse me-2"></i> Surgery Details</span>
                @if ($completionPercentage > 0)
                    <span class="badge bg-info">{{ $completionPercentage }}% Complete</span>
                @endif
            </div>

            <div class="card-body row g-3">

                {{-- 🩺 Date of Surgery --}}
                <div class="col-md-4">
                    <label class="form-label">Date of Surgery</label>
                    <div class="form-control-plaintext">{{ $booking->booking_date ?? now()->format('Y-m-d') }}</div>
                </div>

                {{-- 👁️ Eye --}}
                <div class="col-md-4">
                    <label class="form-label">Eye</label>
                    <select wire:model="contactLens" class="form-select">
                        <option value="">Select...</option>
                        <option value="right">Right Eye (OD)</option>
                        <option value="left">Left Eye (OS)</option>
                        <option value="both">Both Eyes (OD/OS)</option>
                    </select>
                    @error('contactLens')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 🧬 Preoperative Diagnosis --}}
                <div class="col-md-4">
                    <label class="form-label">Preoperative Diagnosis</label>
                    <select wire:model="preOpDiagnosis" class="form-select">
                        <option value="">Select...</option>
                        <option value="Keratoconus OD">Keratoconus OD</option>
                        <option value="Keratoconus OS">Keratoconus OS</option>
                        <option value="Keratoconus OD/OS">Keratoconus OD/OS</option>
                        <option value="Post-LASIK ectasia">Post-LASIK Ectasia</option>
                        <option value="Other">Other (specify in notes)</option>
                    </select>
                    @error('preOpDiagnosis')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 🩸 Postoperative Diagnosis --}}
                <div class="col-md-4">
                    <label class="form-label">Postoperative Diagnosis</label>
                    <div class="form-control-plaintext">{{ $postOpDiagnosis }}</div>
                </div>

                {{-- 💉 Anesthesia --}}
                <div class="col-md-4">
                    <label class="form-label">Anesthesia</label>
                    <select wire:model="anesthesiaType" class="form-select">
                        <option value="">Select...</option>
                        <option value="Topical anesthesia">Topical anesthesia</option>
                        <option value="General anesthesia">General anesthesia</option>
                    </select>
                    @error('anesthesiaType')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- ⏱️ Drop Duration --}}
                <div class="col-md-4">
                    <label class="form-label">Riboflavin Drop Duration (minutes)</label>
                    <select wire:model="dropDuration" class="form-select">
                        <option value="">Select...</option>
                        <option value="6">6</option>
                        <option value="12">12</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                    </select>
                    @error('dropDuration')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- ☀️ UV Duration --}}
                <div class="col-md-4">
                    <label class="form-label">UV Light Duration (minutes)</label>
                    <select wire:model="uvDuration" class="form-select">
                        <option value="">Select...</option>
                        <option value="6">6</option>
                        <option value="12">12</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                    </select>
                    @error('uvDuration')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 👁️ Contact Lens --}}
                <div class="col-md-4">
                    <label class="form-label">Contact Lens Applied</label>
                    <select wire:model="contactLens" class="form-select">
                        <option value="">Select...</option>
                        <option value="right">Right Eye (OD)</option>
                        <option value="left">Left Eye (OS)</option>
                        <option value="both">Both Eyes (OD/OS)</option>
                    </select>
                    @error('contactLens')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 💊 Post-Operative Medication --}}
                <div class="col-md-4">
                    <label class="form-label">Post-Operative Medication</label>
                    <select wire:model="postOpMed" class="form-select">
                        <option value="">Select...</option>
                        <option value="steroid">Topical Steroid</option>
                        <option value="antibiotic">Topical Antibiotic</option>
                        <option value="steroid/antibiotic">Steroid + Antibiotic Mixture</option>
                    </select>
                    @error('postOpMed')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 🧠 Surgeon --}}
                <div class="col-md-4">
                    <label class="form-label">Surgeon</label>
                    <div class="form-control-plaintext">{{ $surgeon }}</div>
                </div>
            </div>
        </div>


        {{-- 📄 Narrative --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-light fw-bold"><i class="bi bi-journal-text me-2"></i> Operative Narrative
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Introductory Statement</label>
                    <textarea wire:model.defer="narratives.intro" rows="4" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Operative Details</label>
                    <textarea wire:model.defer="narratives.details" rows="6" class="form-control"></textarea>
                </div>
            </div>
        </div>

        {{-- ✍️ Signature --}}
        <div class="border-top pt-4 text-end mb-4">
            <h5 class="fw-bold text-info mb-1">Dr. Ihab Saad Othman, MD, FRCS (Glasg)</h5>
            <small class="d-block text-muted">Professor of Ophthalmology, Cairo University</small>
            <small class="d-block text-muted">Director, EyeWorld Hospital, Giza, Egypt</small>
        </div>

        {{-- 🧭 Buttons --}}
        <div class="d-flex justify-content-center gap-3 no-print">
            <button wire:click="saveReport" class="btn btn-info"><i class="bi bi-save me-1"></i> Save</button>
        </div>
    </div>

    <script>
        window.addEventListener('print-report', () => window.print());
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(a => a.remove())
        }, 4000);
    </script>
</div>
