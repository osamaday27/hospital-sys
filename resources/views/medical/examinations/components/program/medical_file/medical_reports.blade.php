    <div class="tab-pane fade" id="medical_reports" role="tabpanel">
        <div class="card">
            <div class="card-body">
                @livewire('medical-report-form', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])

            </div>
        </div>
    </div>
