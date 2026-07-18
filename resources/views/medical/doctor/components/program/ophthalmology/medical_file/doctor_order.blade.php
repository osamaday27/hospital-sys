<div class="tab-pane fade" id="doctor_order" role="tabpanel">
    <div class="card">
        <div class="card-body">
            @livewire('doctor-order-eye', ['patientId' => $patient->id, 'visitId' => $patient_visits->id])
        </div>
    </div>
</div>
