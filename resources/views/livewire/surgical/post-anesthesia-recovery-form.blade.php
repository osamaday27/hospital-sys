<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0" style="color: #fff;">
            <i class="fas fa-bed-pulse me-2"></i> Post-Anesthesia/IV Sedation Recovery
        </h5>
    </div>
    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form wire:submit.prevent="save">
          
            {{-- Procedure Information --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Procedure Information</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Procedure/Surgery</label>
                            <input type="text" class="form-control" wire:model="procedure_done">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Recovery Start Time</label>
                            <input type="time" class="form-control" wire:model="recovery_time_start">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Recovery End Time</label>
                            <input type="time" class="form-control" wire:model="recovery_time_end">
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Surgeon's Name</label>
                            <input type="text" class="form-control" wire:model="surgeon_name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Anesthesiologist's Name</label>
                            <input type="text" class="form-control" wire:model="anesthesiologist_name">
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Type of Anesthesia</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="anesthesia_type.general">
                                    <label class="form-check-label">General Anesthesia</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="anesthesia_type.local">
                                    <label class="form-check-label">Local Anesthesia</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="anesthesia_type.topical">
                                    <label class="form-check-label">Topical Anesthesia</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type of IV Sedation</label>
                            <input type="text" class="form-control" wire:model="sedation_type">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Post-Procedure Status --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Post-Procedure Status</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Pulse Rate</th>
                                    <th>Blood Pressure</th>
                                    <th>Respiratory Rate</th>
                                    <th>Temperature</th>
                                    <th>Oxygen Saturation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 4; $i++)
                                    <tr>
                                        <td><input type="time" class="form-control" wire:model="status_vitals.{{ $i }}.time"></td>
                                        <td><input type="text" class="form-control" wire:model="status_vitals.{{ $i }}.hr"></td>
                                        <td><input type="text" class="form-control" wire:model="status_vitals.{{ $i }}.bp"></td>
                                        <td><input type="text" class="form-control" wire:model="status_vitals.{{ $i }}.rr"></td>
                                        <td><input type="text" class="form-control" wire:model="status_vitals.{{ $i }}.temp"></td>
                                        <td><input type="text" class="form-control" wire:model="status_vitals.{{ $i }}.spo2"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Level of Consciousness</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="level_of_consciousness" value="Conscious">
                                <label class="form-check-label">Conscious</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="level_of_consciousness" value="Unconscious">
                                <label class="form-check-label">Unconscious</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PACU Orders --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>PACU Orders</strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Are there any lines connected to the patient?</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="lines_connected" value="0">
                                <label class="form-check-label">No</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="lines_connected" value="1">
                                <label class="form-check-label">Yes</label>
                            </div>
                        </div>
                        @if($lines_connected == '1')
                            <input type="text" class="form-control mt-2" placeholder="Specify the type of connected lines" wire:model="lines_description">
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Follow-up (by Nurse)</label>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Pulse Rate</th>
                                        <th>Blood Pressure</th>
                                        <th>Respiratory Rate</th>
                                        <th>Temperature</th>
                                        <th>Oxygen Saturation</th>
                                        <th>Pain Score</th>
                                        <th>Consciousness Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 0; $i < 3; $i++)
                                        <tr>
                                            <td><input type="time" class="form-control" wire:model="nurse_followup.{{ $i }}.time"></td>
                                            <td><input type="text" class="form-control" wire:model="nurse_followup.{{ $i }}.hr"></td>
                                            <td><input type="text" class="form-control" wire:model="nurse_followup.{{ $i }}.bp"></td>
                                            <td><input type="text" class="form-control" wire:model="nurse_followup.{{ $i }}.rr"></td>
                                            <td><input type="text" class="form-control" wire:model="nurse_followup.{{ $i }}.temp"></td>
                                            <td><input type="text" class="form-control" wire:model="nurse_followup.{{ $i }}.spo2"></td>
                                            <td><input type="text" class="form-control" wire:model="nurse_followup.{{ $i }}.pain_score"></td>
                                            <td><input type="text" class="form-control" wire:model="nurse_followup.{{ $i }}.consciousness"></td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Medications and Fluids --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Medications and Fluids</strong>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Medications</label>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Medication Name</th>
                                        <th>Dosage</th>
                                        <th>Route</th>
                                        <th>Physician Name & Signature</th>
                                        <th>Administration Time</th>
                                        <th>Double Check</th>
                                        <th>Nurse Name & Signature</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 0; $i < 3; $i++)
                                        <tr>
                                            <td><input type="text" class="form-control" wire:model="medications.{{ $i }}.name"></td>
                                            <td><input type="text" class="form-control" wire:model="medications.{{ $i }}.dose"></td>
                                            <td><input type="text" class="form-control" wire:model="medications.{{ $i }}.route"></td>
                                            <td><input type="text" class="form-control" wire:model="medications.{{ $i }}.physician"></td>
                                            <td><input type="time" class="form-control" wire:model="medications.{{ $i }}.time"></td>
                                            <td><input type="text" class="form-control" wire:model="medications.{{ $i }}.double_check"></td>
                                            <td><input type="text" class="form-control" wire:model="medications.{{ $i }}.nurse"></td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Fluids/Blood</label>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fluid Name</th>
                                        <th>Volume</th>
                                        <th>Additives</th>
                                        <th>Infusion Rate</th>
                                        <th>Physician Name & Signature</th>
                                        <th>Start Time</th>
                                        <th>Double Check</th>
                                        <th>Nurse Name & Signature</th>
                                        <th>End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 0; $i < 2; $i++)
                                        <tr>
                                            <td><input type="text" class="form-control" wire:model="fluids.{{ $i }}.name"></td>
                                            <td><input type="text" class="form-control" wire:model="fluids.{{ $i }}.volume"></td>
                                            <td><input type="text" class="form-control" wire:model="fluids.{{ $i }}.additive"></td>
                                            <td><input type="text" class="form-control" wire:model="fluids.{{ $i }}.rate"></td>
                                            <td><input type="text" class="form-control" wire:model="fluids.{{ $i }}.physician"></td>
                                            <td><input type="time" class="form-control" wire:model="fluids.{{ $i }}.start_time"></td>
                                            <td><input type="text" class="form-control" wire:model="fluids.{{ $i }}.double_check"></td>
                                            <td><input type="text" class="form-control" wire:model="fluids.{{ $i }}.nurse"></td>
                                            <td><input type="time" class="form-control" wire:model="fluids.{{ $i }}.end_time"></td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unusual Events --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Unusual Events</strong>
                </div>
                <div class="card-body">
                    <textarea class="form-control" rows="3" wire:model="unusual_events"></textarea>
                </div>
            </div>

            {{-- Patient Status on Transfer --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Patient Status on Transfer from Recovery Room</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Pulse Rate</th>
                                    <th>Blood Pressure</th>
                                    <th>Respiratory Rate</th>
                                    <th>Temperature</th>
                                    <th>Oxygen Saturation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 2; $i++)
                                    <tr>
                                        <td><input type="time" class="form-control" wire:model="transfer_status.{{ $i }}.time"></td>
                                        <td><input type="text" class="form-control" wire:model="transfer_status.{{ $i }}.hr"></td>
                                        <td><input type="text" class="form-control" wire:model="transfer_status.{{ $i }}.bp"></td>
                                        <td><input type="text" class="form-control" wire:model="transfer_status.{{ $i }}.rr"></td>
                                        <td><input type="text" class="form-control" wire:model="transfer_status.{{ $i }}.temp"></td>
                                        <td><input type="text" class="form-control" wire:model="transfer_status.{{ $i }}.spo2"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label">Has the patient met discharge criteria?</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="discharge_criteria_met" value="1">
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="discharge_criteria_met" value="0">
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Patient will be transferred to</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="discharge_location" value="Ward">
                                    <label class="form-check-label">Ward</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="discharge_location" value="Home">
                                    <label class="form-check-label">Home</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Discharge Time</label>
                            <input type="time" class="form-control" wire:model="discharge_time">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aldrete Score --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Aldrete Discharge Score</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Criterion</th>
                                    <th>Details</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Activity</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="activity" wire:model="aldrete_score.activity" value="2">
                                            <label class="form-check-label">Able to move all four extremities voluntarily or on command</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="activity" wire:model="aldrete_score.activity" value="1">
                                            <label class="form-check-label">Able to move two extremities voluntarily or on command</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="activity" wire:model="aldrete_score.activity" value="0">
                                            <label class="form-check-label">Unable to move extremities voluntarily or on command</label>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $aldrete_score['activity'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Respiration</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="respiration" wire:model="aldrete_score.respiration" value="2">
                                            <label class="form-check-label">Able to breathe deeply and cough freely</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="respiration" wire:model="aldrete_score.respiration" value="1">
                                            <label class="form-check-label">Limited respiratory effort, dyspnea or tachypnea</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="respiration" wire:model="aldrete_score.respiration" value="0">
                                            <label class="form-check-label">Apnea or on ventilator</label>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $aldrete_score['respiration'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Circulation</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="circulation" wire:model="aldrete_score.circulation" value="2">
                                            <label class="form-check-label">Blood pressure ± 20% of pre-anesthetic level</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="circulation" wire:model="aldrete_score.circulation" value="1">
                                            <label class="form-check-label">Blood pressure ± 21-50% of pre-anesthetic level</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="circulation" wire:model="aldrete_score.circulation" value="0">
                                            <label class="form-check-label">Blood pressure ± 51% of pre-anesthetic level</label>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $aldrete_score['circulation'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Consciousness</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="consciousness" wire:model="aldrete_score.consciousness" value="2">
                                            <label class="form-check-label">Fully awake</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="consciousness" wire:model="aldrete_score.consciousness" value="1">
                                            <label class="form-check-label">Arousable on calling</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="consciousness" wire:model="aldrete_score.consciousness" value="0">
                                            <label class="form-check-label">No response</label>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $aldrete_score['consciousness'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Color</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="color" wire:model="aldrete_score.color" value="2">
                                            <label class="form-check-label">Normal skin color</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="color" wire:model="aldrete_score.color" value="1">
                                            <label class="form-check-label">Any skin color change</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="color" wire:model="aldrete_score.color" value="0">
                                            <label class="form-check-label">Obvious cyanosis</label>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $aldrete_score['color'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td colspan="2" class="text-center fw-bold fs-5">
                                        {{ array_sum($aldrete_score) }}/10
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i> The patient can be discharged from the recovery room if the Aldrete score is ≥ 9
                    </div>
                </div>
            </div>

            {{-- Follow-up Instructions --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Follow-up Instructions from Anesthesiologist</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NPO until</label>
                                <input type="text" class="form-control" wire:model="followup_instructions.npo">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Check vital signs every</label>
                                <input type="text" class="form-control" wire:model="followup_instructions.vitals">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Check oxygen saturation every</label>
                                <input type="text" class="form-control" wire:model="followup_instructions.oxygen">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No standing alone for 24 hours</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model="followup_instructions.no_standing" value="1">
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model="followup_instructions.no_standing" value="0">
                                        <label class="form-check-label">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No driving for 24 hours</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model="followup_instructions.no_driving" value="1">
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model="followup_instructions.no_driving" value="0">
                                        <label class="form-check-label">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">In case of any unusual event, please contact</label>
                                <input type="text" class="form-control" wire:model="followup_instructions.emergency_contact">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Anesthesiologist's Name</label>
                            <input type="text" class="form-control" wire:model="followup_instructions.anesthesiologist_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" wire:model="followup_instructions.anesthesiologist_date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Time</label>
                            <input type="time" class="form-control" wire:model="followup_instructions.anesthesiologist_time">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Nurse's Name</label>
                            <input type="text" class="form-control" wire:model="followup_instructions.nurse_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" wire:model="followup_instructions.nurse_date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Time</label>
                            <input type="time" class="form-control" wire:model="followup_instructions.nurse_time">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Save Button --}}
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Save Record
                </button>
            </div>
        </form>
    </div>
</div>