<div>
    {{-- ✅ Flash Messages --}}
    @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type)
        @if (session()->has($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg no-print" role="alert" style="z-index:1055;">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- ✅ Container --}}
    <div class=" py-4">

        {{-- 🔹 Header --}}
        <div class="text-center bg-info text-white rounded-4 p-4 mb-4 shadow-sm">
            <h2 class="fw-bold mb-2"><i class="bi bi-eye"></i> ICL OPERATIVE REPORT</h2>
            <p class="mb-0">Implantable Collamer Lens Surgery</p>
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
                    <label class="form-label">Medical Number</label>
                    <div class="form-control-plaintext">{{ $patient->medical_record_number ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <div class="form-control-plaintext">{{ $patient->date_of_birth ?? 'N/A' }}</div>
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

        {{-- 🏥 Surgery Details --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard2-pulse me-2"></i> Surgery Details</span>
                @if($completionPercentage > 0)
                    <span class="badge bg-info">{{ $completionPercentage }}% Complete</span>
                @endif
            </div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Surgery Date</label>
                    <div class="form-control-plaintext">{{ $booking->booking_date ?? now()->format('Y-m-d') }}</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Eye</label>
                    <select wire:model="surgicalProcedure" class="form-select">
                        <option value="">Select...</option>
                        <option value="OD">OD (Right Eye)</option>
                        <option value="OS">OS (Left Eye)</option>
                    </select>
                    @error('surgicalProcedure') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Preoperative Diagnosis</label>
                    <select wire:model="preOpDiagnosis" class="form-select">
                        <option value="">Select...</option>
                        <option value="Keratoconus OD">Keratoconus OD</option>
                        <option value="High myopia OD">High myopia OD</option>
                        <option value="Keratoconus OS">Keratoconus OS</option>
                        <option value="High myopia OS">High myopia OS</option>
                    </select>
                    @error('preOpDiagnosis') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Anesthesia</label>
                    <select wire:model="anesthesiaType" class="form-select">
                        <option value="">Select...</option>
                        <option value="General anesthesia">General Anesthesia</option>
                        <option value="Monitored anesthesia care">Monitored Anesthesia Care</option>
                    </select>
                    @error('anesthesiaType') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Surgeon</label>
                    <div class="form-control-plaintext">{{ $surgeon }}</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Surgery Type</label>
                    <select wire:model="surgeryType" class="form-select">
                        <option value="">Select...</option>
                        <option value="ICL insertion">ICL Insertion</option>
                        <option value="Peripheral iridectomy">Peripheral Iridectomy</option>
                        <option value="Both">Both</option>
                    </select>
                    @error('surgeryType') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Incision Size</label>
                    <select wire:model="incisionSize" class="form-select">
                        <option value="">Select...</option>
                        <option value="2.2">2.2 mm</option>
                        <option value="2.4">2.4 mm</option>
                        <option value="2.8">2.8 mm</option>
                        <option value="3.0">3.0 mm</option>
                        <option value="3.2">3.2 mm</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">ICL Type</label>
                    <select wire:model="iclType" class="form-select">
                        <option value="">Select...</option>
                        <option value="STAAR ICL">STAAR ICL</option>
                        <option value="IPCL">IPCL</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Superior Iridectomy</label>
                    <select wire:model="iridectomy" class="form-select">
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Complications</label>
                    <select wire:model="complications" class="form-select">
                        <option value="None">None</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                @if($complications === 'Yes')
                    <div class="col-12">
                        <label class="form-label">Type of Complications</label>
                        <input type="text" wire:model="complicationType" class="form-control" placeholder="Describe complications...">
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

                <div class="text-center mt-3">
                    <span class="badge {{ $complications === 'None' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $complications === 'None' ? '✓ No Complications' : '⚠️ Complications Noted' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 🖋️ Signature --}}
        <div class="border-top pt-4 text-end mb-4">
            <h5 class="fw-bold text-info mb-1">Dr. Ihab Saad Othman, MD, FRCS (Glasg)</h5>
            <small class="d-block text-muted">Professor of Ophthalmology, Cairo University</small>
            <small class="d-block text-muted">Director, EyeWorld Hospital, Giza, Egypt</small>
        </div>

        {{-- 🧭 Action Buttons --}}
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4 no-print">
            <button wire:click="saveReport" class="btn btn-info">
                <i class="bi bi-save me-1"></i> Save Report
            </button>
           
        </div>
    </div>

    {{-- ✅ Scripts --}}
    <script>
        window.addEventListener('print-report', () => window.print());

        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(a => a.remove());
        }, 4000);

        window.addEventListener('auto-saved', () => {
            const div = document.createElement('div');
            div.className = 'position-fixed bottom-0 end-0 bg-success text-white px-3 py-2 rounded shadow-lg m-3 animate__animated animate__fadeInUp';
            div.style.zIndex = '1055';
            div.innerHTML = '<i class="bi bi-check-circle me-2"></i> Auto-saved successfully';
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 2500);
        });
    </script>
</div>
