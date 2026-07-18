<div class="tab-pane fade" id="extraoralintraoralexams" role="tabpanel">
    <div class="card">
        <div class="card-header bg-info text-white">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="mb-0">🦷 Extra-Oral and Intra-Oral Examination</h5>
                </div>
                <div class="flex-shrink-0">
                    <ul class="list-inline card-toolbar-menu d-flex align-items-center mb-0">
                        <li class="list-inline-item">
                            <a class="align-middle minimize-card" data-bs-toggle="collapse" href="#collapseextraoralintraoralexams"
                                role="button" aria-expanded="true" aria-controls="collapseextraoralintraoralexams">
                                <i class="mdi mdi-minus align-middle"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body collapse show" id="collapseextraoralintraoralexams">
            <div class="row">
                <div class="col-12">
                     @livewire('dental.extra-oral-intra-oral-exams', [
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
