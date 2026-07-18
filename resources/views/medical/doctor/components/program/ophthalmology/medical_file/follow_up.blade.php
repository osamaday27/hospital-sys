<div class="tab-pane fade " id="follow_up" role="tabpanel">
    <div class="card">
        <div class="card-body">
            @livewire('doctor.follow-up-scheduler', ['patient' => $patient->id, 'reservationId' => $patient_visits->id, 'visit' => $patient_visits->id])
            <!--end row-->
        </div>
        <!--end card-body-->
    </div>
    <!--end card-->
</div>
