<div class="tab-pane fade" id="general_examinationeye" role="tabpanel">
   
            @livewire('eye-examination', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
    
</div>
