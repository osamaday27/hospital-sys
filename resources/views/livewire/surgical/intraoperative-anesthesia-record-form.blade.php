<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-notes-medical me-2"></i> Intraoperative Anesthesia Record
        </h5>
    </div>

    <div class="card-body">
        <!-- Pre-Anesthesia Assessment -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Pre-Anesthesia Assessment</h6>
            </div>
            <div class="card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form wire:submit.prevent="save">
                    <!-- جميع الحقول هنا -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Patient condition changed?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                        wire:model="changed_from_pre_anesthesia" value="0">
                                    <label class="form-check-label">No</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                        wire:model="changed_from_pre_anesthesia" value="1">
                                    <label class="form-check-label">Yes</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Glasgow Coma Scale (GCS)</label>
                            <input type="text" class="form-control" wire:model="gcs">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pulse</label>
                            <input type="text" class="form-control" wire:model="pulse">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Blood Pressure</label>
                            <input type="text" class="form-control" wire:model="bp">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">SpO2 (%)</label>
                            <input type="text" class="form-control" wire:model="spo2">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">NPO Status</label>
                            <input type="text" class="form-control" wire:model="npo">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Assessment Time</label>
                            <input type="time" class="form-control" wire:model="pre_assessment_time">
                        </div>
                    </div>
            </div>
        </div>

        <!-- Intraoperative Monitoring -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Intraoperative Monitoring</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Consciousness</th>
                                <th>Skin Color</th>
                                <th>SpO2</th>
                                <th>ETCO2</th>
                                <th>O2/N2O</th>
                                <th>Iso/Sevo</th>
                                <th>CVP</th>
                                <th>Temp</th>
                                <th>BP</th>
                                <th>Pulse</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 12; $i++)
                                <tr>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.time"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.consciousness"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.skin_color"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.spo2"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.etco2"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.o2_n2o"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.iso_sevo"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.cvp"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.temp"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.bp"></td>
                                    <td><input type="text" class="form-control"
                                            wire:model="monitoring_data.{{ $i }}.p"></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Anesthesia Technique -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Anesthesia Technique</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        @foreach (['General', 'Regional', 'Sedation', 'Local'] as $type)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="anesthesia_technique"
                                    value="{{ $type }}">
                                <label class="form-check-label">{{ $type }} Anesthesia</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Medications & Fluids -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Medications & Fluids</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2"><label class="form-label">Fluid In (ml)</label><input type="text"
                            class="form-control" wire:model="fluid_in"></div>
                    <div class="col-md-2"><label class="form-label">Fluid Out (ml)</label><input type="text"
                            class="form-control" wire:model="fluid_out"></div>
                    <div class="col-md-2"><label class="form-label">Urine (ml)</label><input type="text"
                            class="form-control" wire:model="urine"></div>
                    <div class="col-md-2"><label class="form-label">Blood Loss (ml)</label><input type="text"
                            class="form-control" wire:model="blood_loss"></div>
                    <div class="col-md-2"><label class="form-label">Net Balance (ml)</label><input type="text"
                            class="form-control" wire:model="net_balance"></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Bolus</th>
                                @for ($i = 0; $i < 8; $i++)
                                    <th><input type="text" class="form-control"
                                            wire:model="medications.bolus.{{ $i }}"></th>
                                @endfor
                                <th>Total</th>
                                <th><input type="text" class="form-control" wire:model="medications.total"></th>
                            </tr>
                            <tr>
                                <th>Increments</th>
                                @for ($i = 0; $i < 8; $i++)
                                    <th><input type="text" class="form-control"
                                            wire:model="medications.increments.{{ $i }}"></th>
                                @endfor
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Post-Operative Condition -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Post-Operative Condition</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><label class="form-label">Unusual Events</label>
                        <select class="form-select" wire:model="unusual_event">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="col-md-3"><label class="form-label">Condition</label>
                        <select class="form-select" wire:model="post_op_condition">
                            <option value="Awake">Awake</option>
                            <option value="Unconscious">Unconscious</option>
                        </select>
                    </div>
                    <div class="col-md-2"><label class="form-label">BP</label><input type="text"
                            class="form-control" wire:model="bp_post"></div>
                    <div class="col-md-2"><label class="form-label">HR</label><input type="text"
                            class="form-control" wire:model="hr_post"></div>
                    <div class="col-md-2"><label class="form-label">RR</label><input type="text"
                            class="form-control" wire:model="rr_post"></div>
                </div>
            </div>
        </div>

        <!-- Signature & Discharge -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Discharge Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><label class="form-label">Discharge To</label>
                        <select class="form-select" wire:model="discharge_to">
                            <option value="PACU">PACU</option>
                            <option value="ICU">ICU</option>
                        </select>
                    </div>
                    <div class="col-md-3"><label class="form-label">Time</label><input type="datetime-local" class="form-control" wire:model="signature_datetime">
                    </div>
                    <div class="col-md-3"><label class="form-label">Anesthesiologist Signature</label><input
                            type="text" class="form-control" wire:model="anesthesiologist_signature"></div>
                </div>
            </div>
        </div>

        <!-- Remarks -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Remarks</h6>
            </div>
            <div class="card-body">
                @for ($i = 0; $i < 12; $i++)
                    <input type="text" class="form-control mb-1" wire:model="remarks.{{ $i }}"
                        placeholder="{{ $i + 1 }}.">
                @endfor
            </div>
        </div>

        <!-- Submit -->
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Save Record
            </button>
        </div>
        </form>
    </div>
</div>
