{{-- Complaint Tab --}}
<div class="tab-pane fade show active" id="complaint" role="tabpanel">
    <div class="card">
        <div class="card-header bg-info text-white">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="mb-0">🦷 Dental Complaint Form</h5>
                </div>
                <div class="flex-shrink-0">
                    <ul class="list-inline card-toolbar-menu d-flex align-items-center mb-0">
                        <li class="list-inline-item">
                            <a class="align-middle minimize-card" data-bs-toggle="collapse" href="#collapseComplaint"
                                role="button" aria-expanded="true" aria-controls="collapseComplaint">
                                <i class="mdi mdi-minus align-middle"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body collapse show" id="collapseComplaint">
            @livewire('dental.teeth-chart', [
                'id_patient' => $patient->id,
                'id_reservation' => $reservations->id,
                'type_visit' => $patient_visits->type,
                'id_visit' => $patient_visits->id
            ])
        </div>
    </div>
</div>
