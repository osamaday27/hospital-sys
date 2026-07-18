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
            <h2 class="fw-bold mb-2"><i class="bi bi-eye"></i> Phakoemulsification and IOL Insertion</h2>
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
                        <div class="col-md-4">
                            <label>Eye</label>
                            <select wire:model="eye" class="form-select">
                                <option value="">Select...</option><option>OD</option><option>OS</option><option>OD/OS</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Anesthesia</label>
                            <select wire:model="anesthesia" class="form-select">
                                <option value="">Select...</option>
                                <option>General</option><option>Peribulbar</option><option>Local</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Additional Procedures</label>
                            <input wire:model="additional_procedure" type="text" class="form-control" placeholder="If any...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔬 Operative --}}
            <div class="tab-pane fade @if($activeTab==='operative') show active @endif" id="operative" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Operative Details</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3"><label>Incision Size</label>
                            <select wire:model="incision_size" class="form-select"><option>2.2 mm</option><option>2.5 mm</option><option>2.8 mm</option><option>3 mm</option><option>3.2 mm</option></select></div>
                        <div class="col-md-3"><label>Axis</label>
                            <select wire:model="axis" class="form-select"><option>70°</option><option>90°</option></select></div>
                        <div class="col-md-3"><label>Capsule Staining</label>
                            <select wire:model="staining" class="form-select"><option>Trypan blue</option><option>Brilliant blue</option></select></div>
                        <div class="col-md-3"><label>Capsulorrhexis Tool</label>
                            <select wire:model="capsulorrhexis_tool" class="form-select"><option>Rhexis forceps</option><option>Microrrhexis forceps</option><option>Cystitome</option></select></div>
                        <div class="col-md-4"><label>Technique</label>
                            <select wire:model="technique" class="form-select"><option>Divide and conquer</option><option>Quick phako-chop</option></select></div>
                        <div class="col-md-4"><label>IOL Type</label>
                            <input wire:model="iol_type" class="form-control" placeholder="Lens type..."></div>
                        <div class="col-md-4"><label>Complications</label>
                            <select wire:model="complications" class="form-select"><option>None</option><option>Yes</option></select></div>
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
