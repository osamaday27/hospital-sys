<div class="card shadow-lg">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0"><i class="fas fa-procedures me-2"></i> Anesthesia Plan</h5>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form wire:submit.prevent="save" class="needs-validation" novalidate>
            <!-- Section 1: Basic Information -->
            <div class="section-header mb-4">
                <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Patient Preparation</h6>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="npo_hours" wire:model="npo_hours"
                            placeholder="NPO hours" required>
                        <label for="npo_hours"><i class="fas fa-clock me-2 text-muted"></i>NPO Duration (hours)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="sedation_score" wire:model="sedation_score"
                            placeholder="Sedation Score">
                        <label for="sedation_score"><i class="fas fa-brain me-2 text-muted"></i>Sedation Score</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <textarea class="form-control" id="pending_investigations" wire:model="pending_investigations"
                            placeholder="Pending investigations" style="height: 100px"></textarea>
                        <label for="pending_investigations"><i class="fas fa-clipboard-list me-2 text-muted"></i>Pending
                            Investigations/Consultations</label>
                    </div>
                </div>
            </div>

            <!-- Section 2: Anesthesia Type -->
            <div class="section-header mb-4 mt-5">
                <h6 class="text-primary"><i class="fas fa-syringe me-2"></i>Anesthesia Selection</h6>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-check-circle me-2 text-muted"></i>Type of
                        Anesthesia</label>
                    <div class="card card-body bg-light">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check form-check-inline mb-3">
                                    <input class="form-check-input" type="checkbox" id="moderate_sedation"
                                        wire:model="anaesthesia_type.moderate">
                                    <label class="form-check-label" for="moderate_sedation">Moderate Sedation</label>
                                </div>
                                <div class="form-check form-check-inline mb-3">
                                    <input class="form-check-input" type="checkbox" id="regional_anesthesia"
                                        wire:model="anaesthesia_type.regional">
                                    <label class="form-check-label" for="regional_anesthesia">Regional
                                        Anesthesia</label>
                                </div>
                                <div class="form-check form-check-inline mb-3">
                                    <input class="form-check-input" type="checkbox" id="local_infiltration"
                                        wire:model="anaesthesia_type.local">
                                    <label class="form-check-label" for="local_infiltration">Local Infiltration</label>
                                </div>
                                <div class="form-check form-check-inline mb-3">
                                    <input class="form-check-input" type="checkbox" id="general_anesthesia"
                                        wire:model="anaesthesia_type.general">
                                    <label class="form-check-label" for="general_anesthesia">General Anesthesia</label>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="form-check form-check-inline mb-3">
                                    <input class="form-check-input" type="checkbox" id="deep_sedation"
                                        wire:model="anaesthesia_type.deep">
                                    <label class="form-check-label" for="deep_sedation">Deep Sedation</label>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <select class="form-select" id="delivery_method" wire:model="delivery_method">
                                    <option value="inhaled">Inhaled Induction</option>
                                    <option value="iv">Intravenous Induction</option>
                                </select>
                                <label for="delivery_method"><i
                                        class="fas fa-truck-medical me-2 text-muted"></i>Delivery Method</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <select class="form-select" id="anaesthetic_name" wire:model="anaesthetic_name">
                                    <option value="desflurane">Desflurane</option>
                                    <option value="sevoflurane">Sevoflurane</option>
                                    <option value="other">Other Anesthetic</option>
                                </select>
                                <label for="anaesthetic_name"><i
                                        class="fas fa-prescription-bottle-alt me-2 text-muted"></i>Anesthetic
                                    Name</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Fluid & Blood Management -->
            <div class="section-header mb-4 mt-5">
                <h6 class="text-primary"><i class="fas fa-tint me-2"></i>Fluid & Blood Management</h6>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="fluid_management"
                            wire:model="fluid_management" placeholder="Fluid management">
                        <label for="fluid_management"><i class="fas fa-iv me-2 text-muted"></i>Fluid
                            Management</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-blood me-2 text-muted"></i>Blood Products
                        Required</label>
                    <div class="card card-body bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="fresh_blood"
                                        wire:model="blood_booking.fresh_blood">
                                    <label class="form-check-label" for="fresh_blood">Fresh Blood</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="rbc"
                                        wire:model="blood_booking.rbc">
                                    <label class="form-check-label" for="rbc">RBC</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="ffp"
                                        wire:model="blood_booking.ffp">
                                    <label class="form-check-label" for="ffp">FFP</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="plt"
                                        wire:model="blood_booking.plt">
                                    <label class="form-check-label" for="plt">Plt</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="cryo"
                                        wire:model="blood_booking.cryo">
                                    <label class="form-check-label" for="cryo">CRYO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-header mb-4 mt-5">
                <h6 class="text-primary"><i class="fas fa-pills me-2"></i> Premedication</h6>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="premedication_none"
                        wire:model="premedication_none">
                    <label class="form-check-label" for="premedication_none">No Premedication Required</label>
                </div>

                @if (!$premedication_none)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>Drug</th>
                                    <th>Dose</th>
                                    <th>Time</th>
                                    <th>Route</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($premedication as $index => $item)
                                    <tr>
                                        <td class="position-relative">
                                            <input type="text" class="form-control form-control-sm"
                                                placeholder="Search drug..."
                                                wire:model.lazy="searchMedications.{{ $index }}"
                                                wire:blur="selectMedication({{ $index }})">

                                            {{-- ✅ قائمة البحث --}}
                                            @if (!empty($medicationResults[$index]))
                                                <ul class="list-group position-absolute w-100 z-10"
                                                    style="max-height: 150px; overflow-y: auto;">
                                                    @foreach ($medicationResults[$index] as $result)
                                                        <li class="list-group-item list-group-item-action"
                                                            wire:click="selectMedication({{ $index }}, {{ is_object($result) ? $result->id : $result['id'] ?? 0 }})">
                                                            {{ is_object($result) ? $result->getTranslation('name', app()->getLocale()) : $result['name'] ?? '' }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                wire:model="premedication.{{ $index }}.dose"
                                                placeholder="Dose">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                wire:model="premedication.{{ $index }}.time"
                                                placeholder="Time">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                wire:model="premedication.{{ $index }}.route"
                                                placeholder="Route">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                wire:click="removePremedication({{ $index }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-primary mt-2" wire:click="addPremedication">
                        <i class="fas fa-plus me-1"></i> Add Medication
                    </button>
                @endif
            </div>


            <!-- Section 5: Monitoring & Outcomes -->
            <div class="section-header mb-4 mt-5">
                <h6 class="text-primary"><i class="fas fa-heartbeat me-2"></i>Monitoring & Outcomes</h6>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-12">
                    <div class="form-floating">
                        <textarea class="form-control" id="special_monitoring" wire:model="special_monitoring"
                            placeholder="Special monitoring" style="height: 80px"></textarea>
                        <label for="special_monitoring"><i
                                class="fas fa-monitor-heart-rate me-2 text-muted"></i>Special Monitoring
                            Requested</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-floating">
                        <textarea class="form-control" id="post_outcome" wire:model="post_outcome" placeholder="Anticipated outcome"
                            style="height: 80px"></textarea>
                        <label for="post_outcome"><i class="fas fa-prognosis me-2 text-muted"></i>Anticipated
                            Post-Anesthesia Outcome</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="discharge_to" wire:model="discharge_to"
                            placeholder="Discharge location">
                        <label for="discharge_to"><i class="fas fa-home me-2 text-muted"></i>Discharge To</label>
                    </div>
                </div>
            </div>

            <!-- Section 6: Post-Op Analgesia -->
            <div class="section-header mb-4 mt-5">
                <h6 class="text-primary"><i class="fas fa-pain-relief me-2"></i>Post-Operative Analgesia Plan</h6>
            </div>

            <div class="mb-4">
                <div class="form-floating">
                    <textarea class="form-control" id="post_analgesia_plan" wire:model="post_analgesia_plan"
                        placeholder="Post-op analgesia plan" style="height: 100px"></textarea>
                    <label for="post_analgesia_plan"><i class="fas fa-file-prescription me-2 text-muted"></i>Plan
                        Details</label>
                </div>
            </div>

            <!-- Section 7: Signatures -->
            <div class="section-header mb-4 mt-5">
                <h6 class="text-primary"><i class="fas fa-signature me-2"></i>Authorization</h6>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="doctor_name" wire:model="doctor_name"
                            placeholder="Doctor name">
                        <label for="doctor_name"><i class="fas fa-user-md me-2 text-muted"></i>Anesthesia
                            Doctor</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="assistant_name" wire:model="assistant_name"
                            placeholder="Assistant name">
                        <label for="assistant_name"><i class="fas fa-user-nurse me-2 text-muted"></i>Anesthesia
                            Assistant</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="form_date" wire:model="form_date">
                        <label for="form_date"><i class="fas fa-calendar-day me-2 text-muted"></i>Date</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="time" class="form-control" id="form_time" wire:model="form_time">
                        <label for="form_time"><i class="fas fa-clock me-2 text-muted"></i>Time</label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="reset" class="btn btn-outline-secondary me-md-2">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Anesthesia Plan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .section-header {
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 8px;
    }

    .form-floating label {
        color: #6c757d;
    }

    .card-header {
        border-radius: 0.375rem 0.375rem 0 0 !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #3f80ea 0%, #1e3c8b 100%);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(63, 128, 234, 0.1);
    }
</style>

<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('show-toast', data => {
            const toast = document.createElement('div');
            toast.className =
                `toast align-items-center text-bg-${data.type} border-0 position-fixed bottom-0 end-0 m-3 show`;
            toast.innerHTML = `<div class="d-flex">
            <div class="toast-body">${data.message}</div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        });
    });
</script>
