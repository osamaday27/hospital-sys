<div class="tab-pane fade show active" id="complaint" role="tabpanel">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Complaint</h6>
                        </div>
                        <div class="flex-shrink-0">
                            <ul class="list-inline card-toolbar-menu d-flex align-items-center mb-0">
                                <li class="list-inline-item">
                                    <a class="align-middle minimize-card" data-bs-toggle="collapse"
                                        href="#collapseExample3" role="button" aria-expanded="true"
                                        aria-controls="collapseExample3">
                                        <i class="mdi mdi-minus align-middle"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body collapse show" id="collapseExample3">
                    @livewire('complaint-history', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                </div>
            </div>
</div>
