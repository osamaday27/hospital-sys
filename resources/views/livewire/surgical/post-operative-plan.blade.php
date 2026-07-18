<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0" style="color: #fff">Post-Operative Plan of Care</h5>
        </div>
        <div class="card-body">
            @if(session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label class="form-label">Surgery / Procedure</label>
                    <input type="text" class="form-control" wire:model.defer="surgery_procedure">
                </div>

                <div class="mb-3">
                    <label class="form-label">Transfer To</label>
                    <select class="form-select" wire:model.defer="transfer_to">
                        <option value="">-- Select --</option>
                        <option>Home</option>
                        <option>Ward</option>
                        <option>PACU</option>
                    </select>
                </div>

                <h6 class="mt-4">Patient's Immediate Postoperative Needs</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Positioning</label>
                        <select class="form-select" wire:model.defer="positioning">
                            <option value="">-- Select --</option>
                            <option>Supine</option>
                            <option>Lateral</option>
                            <option>Semi-Sitting</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">If Other, Specify</label>
                        <input type="text" class="form-control" wire:model.defer="positioning_other">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Mobility</label>
                        <select class="form-select" wire:model.defer="mobility">
                            <option value="">-- Select --</option>
                            <option>As Soon As Possible</option>
                            <option>Strict Bed Rest</option>
                            <option>Needs Assistant</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">If Other, Specify</label>
                        <input type="text" class="form-control" wire:model.defer="mobility_other">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Feeding</label>
                        <select class="form-select" wire:model.defer="feeding">
                            <option value="">-- Select --</option>
                            <option>Oral</option>
                            <option>Parenteral Nutrition</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">If Other, Specify</label>
                        <input type="text" class="form-control" wire:model.defer="feeding_other">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Required Further Monitoring</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" wire:model.defer="required_monitoring" value="0" id="monitor_no">
                            <label class="form-check-label" for="monitor_no">No</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" wire:model.defer="required_monitoring" value="1" id="monitor_yes">
                            <label class="form-check-label" for="monitor_yes">Yes</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Wound</label>
                        <select class="form-select" wire:model.defer="wound">
                            <option value="">-- Select --</option>
                            <option>NA</option>
                            <option>Eye Patch</option>
                            <option>Compression Bandage</option>
                            <option>Drain</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Urine Output</label>
                        <select class="form-select" wire:model.defer="urine_output">
                            <option value="">-- Select --</option>
                            <option>NA</option>
                            <option>Catheter</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Instructions Post Anesthesia or Sedation</label>
                    <textarea class="form-control" wire:model.defer="instructions_post_anesthesia" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Investigations (Post-Operative)</label>
                    <textarea class="form-control" wire:model.defer="investigations" rows="2"></textarea>
                </div>

                <h6 class="mt-4">Post-Operative Medications</h6>
                <button type="button" class="btn btn-sm btn-secondary mb-2" wire:click="addMedication">+ Add Medication</button>
                @forelse($medications as $index => $med)
                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Medication" wire:model.defer="medications.{{ $index }}.medication">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Dose" wire:model.defer="medications.{{ $index }}.dose">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Route" wire:model.defer="medications.{{ $index }}.route">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Frequency" wire:model.defer="medications.{{ $index }}.frequency">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Duration" wire:model.defer="medications.{{ $index }}.duration">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeMedication({{ $index }})">X</button>
                    </div>
                </div>
                @empty
                <p class="text-muted">No medications added.</p>
                @endforelse

                <h6 class="mt-4">Fluids</h6>
                <button type="button" class="btn btn-sm btn-secondary mb-2" wire:click="addFluid">+ Add Fluid</button>
                @forelse($fluids as $index => $fluid)
                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Fluid Name" wire:model.defer="fluids.{{ $index }}.fluid_name">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Volume" wire:model.defer="fluids.{{ $index }}.volume">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Additive" wire:model.defer="fluids.{{ $index }}.additive_name">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Infusion Rate" wire:model.defer="fluids.{{ $index }}.infusion_rate">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeFluid({{ $index }})">X</button>
                    </div>
                </div>
                @empty
                <p class="text-muted">No fluids added.</p>
                @endforelse

                <div class="mb-3 mt-3">
                    <label class="form-label">General Instructions</label>
                    <textarea class="form-control" wire:model.defer="general_instructions" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Follow Up</label>
                    <textarea class="form-control" wire:model.defer="follow_up" rows="2"></textarea>
                </div>

                <h6 class="mt-4">Surgeon Signature</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Doctor Name" wire:model.defer="doctor_name">
                    </div>
                    <div class="col-md-4">
                        <input type="date" class="form-control" wire:model.defer="date">
                    </div>
                    <div class="col-md-4">
                        <input type="time" class="form-control" wire:model.defer="time">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Plan</button>
            </form>
        </div>
    </div>
</div>
