<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0" style="color: #fff">Discharge Summary</h5>
        </div>
        <div class="card-body">
            @if(session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label class="form-label">Provisional/Final Diagnosis</label>
                    <textarea class="form-control" wire:model.defer="final_diagnosis"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Operation / Procedure Done</label>
                    <textarea class="form-control" wire:model.defer="operation_done"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Significant History & Findings</label>
                    <textarea class="form-control" wire:model.defer="significant_history"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Case Progression</label>
                    <textarea class="form-control" wire:model.defer="case_progression"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Patient Condition</label>
                    <select class="form-select" wire:model.defer="patient_condition">
                        <option value="">-- Select --</option>
                        <option>Cured</option>
                        <option>Improved</option>
                        <option>Stationary</option>
                        <option>Fair</option>
                        <option>Poor</option>
                        <option>DAMA</option>
                        <option>Transferred</option>
                    </select>
                </div>

                <h6 class="mt-4">Medications During Admission</h6>
                <button type="button" class="btn btn-sm btn-secondary mb-2" wire:click="addMedication">+ Add Medication</button>
                @forelse($medications as $index => $med)
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Medication"
                            wire:model.defer="medications.{{ $index }}.medication_name">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Dose/Route"
                            wire:model.defer="medications.{{ $index }}.dose_route">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Frequency"
                            wire:model.defer="medications.{{ $index }}.frequency">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm"
                            wire:click="removeMedication({{ $index }})">X</button>
                    </div>
                </div>
                @empty
                <p class="text-muted">No medications added.</p>
                @endforelse

                <h6 class="mt-4">Discharge Instructions</h6>
                <div class="mb-3">
                    <label class="form-label">Diet Instruction</label>
                    <select class="form-select" wire:model.defer="diet_instruction">
                        <option value="">-- Select --</option>
                        <option>صائم ولا شيء بالفم</option>
                        <option>أكل اعتيادي</option>
                        <option>أكل سهل البلع</option>
                        <option>أكل قليل الدهون</option>
                        <option>نظام غذائي لمرضى السكر</option>
                        <option>أكل قليل الملح</option>
                        <option>أكل قليل البروتين</option>
                        <option>أخرى</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Other Diet (Specify)</label>
                    <input type="text" class="form-control" wire:model.defer="diet_other">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" wire:model.defer="implanted_device" id="implanted_device">
                    <label class="form-check-label" for="implanted_device">Any discharge instructions regarding implanted devices/prosthesis?</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">If Yes, Please Specify</label>
                    <textarea class="form-control" wire:model.defer="implanted_device_instructions"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Activity Instructions</label>
                    <textarea class="form-control" wire:model.defer="follow_up_activity"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Follow Up Visit</label>
                    <select class="form-select" wire:model.defer="follow_up_visit">
                        <option value="">-- Select --</option>
                        <option>باكر</option>
                        <option>بعد أسبوع</option>
                        <option>بعد شهر</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">When to Seek Emergency Care</label>
                    <textarea class="form-control" wire:model.defer="emergency_instructions"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Additional Notes</label>
                    <textarea class="form-control" wire:model.defer="notes"></textarea>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" wire:model.defer="copy_given" id="copy_given">
                    <label class="form-check-label" for="copy_given">Patient received a copy</label>
                </div>

                <h6 class="mt-4">Signatures</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Doctor Name" wire:model.defer="doctor_name">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Doctor Signature" wire:model.defer="doctor_signature">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" wire:model.defer="report_date">
                    </div>
                    <div class="col-md-2">
                        <input type="time" class="form-control" wire:model.defer="report_time">
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
