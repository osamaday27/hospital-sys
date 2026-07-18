<div class="tab-pane fade" id="medical_history" role="tabpanel">
    <input id="id_patient" name="id_patient" value="{{ $patient->id }}" type="hidden" />
    <input id="id_reservation" name="id_reservation" value="{{ $reservations->id }}" type="hidden" />
    <input id="type_visit" name="type_visit" value="{{ $patient_visits->type }}" type="hidden" />
    <input id="id_visit" name="id_visit" value="{{ $patient_visits->id }}" type="hidden" />
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- Main Tab Navigation -->
            <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#ocularHistoryTab" role="tab">
                        <i class="mdi mdi-eye-outline me-2"></i>Ocular History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pastmedicalHistoryTab" role="tab">
                        <i class="mdi mdi-heart-pulse me-2"></i>Past medical History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#familyHistoryTab" role="tab">
                        <i class="mdi mdi-account-group me-2"></i>Family History
                    </a>
                </li>
                <!-- Add more main tabs as needed -->
            </ul>

            <!-- Main Tab Content -->
            <div class="tab-content">
                <!-- Ocular History Tab -->
                <div class="tab-pane fade show active" id="ocularHistoryTab" role="tabpanel">
                    <!-- Sub Tab Navigation for Ocular History -->
                    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#diagnosisHistoryTab" role="tab">
                                Diagnosis History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#surgeryHistoryTab" role="tab">
                                Surgery History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#investigationHistoryTab" role="tab">
                                Investigation History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#treatmentHistoryTab" role="tab">
                                Current medications
                            </a>
                        </li>
                    </ul>

                    <!-- Sub Tab Content for Ocular History -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="diagnosisHistoryTab" role="tabpanel">
                            @livewire('ophthalmology-history', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])

                        </div>
                        <div class="tab-pane fade" id="surgeryHistoryTab" role="tabpanel">
                            @livewire('previous-surgery', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="investigationHistoryTab" role="tabpanel">
                            @livewire('investigation-history', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="treatmentHistoryTab" role="tabpanel">
                            @livewire('treatment-history', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                    </div>
                </div>

                <!-- Past medical History Tab -->
                <div class="tab-pane fade" id="pastmedicalHistoryTab" role="tabpanel">
                    <!-- Sub Tab Navigation for Past medical History -->
                    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#dmHistoryTab" role="tab">
                                DM History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#htnHistoryTab" role="tab">
                                HTN History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#otherSystemicDiseaseHistoryTab"
                                role="tab">
                                Other Systemic Disease History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#allergyHistoryTab" role="tab">
                                Allergy History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#smokingHistoryTab" role="tab">
                                Smoking History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#othersHistoryTab" role="tab">
                                Others History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#medicationHistoryTab" role="tab">
                                Medication History
                            </a>
                        </li>
                    </ul>

                    <!-- Sub Tab Content for Past medical History -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="dmHistoryTab" role="tabpanel">
                            @livewire('diabetes-mellitus', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="htnHistoryTab" role="tabpanel">
                            @livewire('hypertension', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="otherSystemicDiseaseHistoryTab" role="tabpanel">
                            @livewire('other-systemic-disease', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="allergyHistoryTab" role="tabpanel">
                            @livewire('allergy', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="smokingHistoryTab" role="tabpanel">
                            @livewire('smoking', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="othersHistoryTab" role="tabpanel">
                            @livewire('m-d-others', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="medicationHistoryTab" role="tabpanel">
                              @livewire('m-d-oph-medication',['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                    </div>
                </div>

                <!-- Family History Tab -->
                <div class="tab-pane fade" id="familyHistoryTab" role="tabpanel">
                    <!-- Sub Tab Navigation for Family History -->
                    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#ocularFamilyHistoryTab"
                                role="tab">
                                Ocular Family History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#medicalFamilyHistoryTab" role="tab">
                                medical Family History
                            </a>
                        </li>
                    </ul>
                    <!-- Sub Tab Content for Family History -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="ocularFamilyHistoryTab" role="tabpanel">
                               @livewire('ocular-history',['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane fade" id="medicalFamilyHistoryTab" role="tabpanel">
                               @livewire('medical-historyfamily',['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
