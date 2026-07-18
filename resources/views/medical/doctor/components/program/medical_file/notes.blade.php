  <div class="tab-pane fade" id="notes_tab" role="tabpanel">
     @livewire('notestab', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
  </div>
