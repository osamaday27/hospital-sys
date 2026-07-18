<div class="tab-pane fade " id="prescription2" role="tabpanel">
    <div class="card">
        <div class="card-body">
@livewire('doctor-prescription', ['patientId' => $patient->id, 'reservationId' => $patient_visits->id,'id_visit' => $patient_visits->id])
            <!--end row-->
        </div>
        <!--end card-body-->
    </div>
    <!--end card-->
</div>


