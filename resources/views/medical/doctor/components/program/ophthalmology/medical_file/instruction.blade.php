<div class="tab-pane fade  " id="instruction_tab" role="tabpanel">
    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="card-title mb-0">{{ __('doctor_trans.instruction') }}</h6>
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
            @livewire('medical.program.doctor-instruction', [
                'patientId'     => $patient->id,
                'reservationId' => $reservations->id,
                'typeVisit'     => $patient_visits->type,
                'visitId'       => $patient_visits->id,
            ])
            
        </div>
    </div>
</div>
