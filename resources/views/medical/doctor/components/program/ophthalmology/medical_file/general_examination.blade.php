<div class="tab-pane fade" id="general_examination" role="tabpanel">
    <div class="row">
       @livewire('general-examination', [
                'patientId'     => $patient->id,
                'reservationId' => $reservations->id,
                'typeVisit'     => $patient_visits->type,
                'visitId'       => $patient_visits->id,
            ])
            
</div>

