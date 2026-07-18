<div class="tab-pane fade" id="medical_historydental" role="tabpanel">
    <div class="card">
        <div class="card-header bg-info text-white">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="mb-0">🦷 Medical History Form</h5>
                </div>
                <div class="flex-shrink-0">
                    <ul class="list-inline card-toolbar-menu d-flex align-items-center mb-0">
                        <li class="list-inline-item">
                            <a class="align-middle minimize-card" data-bs-toggle="collapse" href="#collapseMedicalHistory"
                                role="button" aria-expanded="true" aria-controls="collapseMedicalHistory">
                                <i class="mdi mdi-minus align-middle"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body collapse show" id="collapseMedicalHistory">
            <div class="row">
                <div class="col-12">
                   @livewire('dental.medical-history-form', [
                        'patientId' => $patient->id,
                        'reservationId' => $reservations->id,
                        'typeVisit' => $patient_visits->type,
                        'visitId' => $patient_visits->id
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
