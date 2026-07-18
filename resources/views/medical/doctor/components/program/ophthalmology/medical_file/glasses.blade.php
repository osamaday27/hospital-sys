<div class="tab-pane fade" id="glasses" role="tabpanel">
    <div class="cards-container">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active old-glasses" data-bs-toggle="tab"
                                href="#nav-border-top-old_glasses" role="tab" aria-selected="false">
                                <i class="mdi mdi-glasses"></i> Old Glasses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link objective-refraction" data-bs-toggle="tab"
                                href="#nav-border-top-objective_refraction" role="tab" aria-selected="false">
                                <i class="mdi mdi-eye"></i> Objective refraction
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link k-readings" data-bs-toggle="tab" href="#nav-border-top-k_readings"
                                role="tab" aria-selected="false">
                                <i class="mdi mdi-chart-line"></i> K- Readings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link  subjective-refraction" data-bs-toggle="tab"
                                href="#nav-border-top-subjective-refraction" role="tab" aria-selected="true">
                                <i class="mdi mdi-search"></i> Subjective refraction
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pin-hole" data-bs-toggle="tab" href="#nav-border-top-pin_hole"
                                role="tab" aria-selected="true">
                                <i class="mdi mdi-circle-notch"></i> Pin Hole
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link color-vision" data-bs-toggle="tab" href="#nav-border-top-color_vision"
                                role="tab" aria-selected="true">
                                <i class="mdi mdi-palette"></i> Color Vision
                            </a>
                        </li>

                    </ul>
                    <div class="tab-content text-muted">
                        <div class="tab-pane active" id="nav-border-top-old_glasses" role="tabpanel">
                            @livewire('old-glasses', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane" id="nav-border-top-subjective-refraction" role="tabpanel">
                            @livewire('subjective-refraction', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane" id="nav-border-top-objective_refraction" role="tabpanel">
                            @livewire('objective-refraction', ['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane" id="nav-border-top-k_readings" role="tabpanel">
                               @livewire('k-readings',['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane" id="nav-border-top-pin_hole" role="tabpanel">
                              @livewire('pinhole',['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                        <div class="tab-pane" id="nav-border-top-color_vision" role="tabpanel">
                             @livewire('color-vision',['id_patient' => $patient->id, 'id_reservation' => $reservations->id, 'type_visit' => $patient_visits->type, 'id_visit' => $patient_visits->id])
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div>
        </div>
        <!--end col-->
    </div>
</div>
<script>
    // Clear OD
    document.getElementById('clear-od').addEventListener('click', function() {
        document.querySelectorAll('#od-sph, #od-cyl, #od-axis, #od-near-add, #od-bcva, #od-comment').forEach(
            function(input) {
                input.value = '';
            });
    });

    // Clear OS
    document.getElementById('clear-os').addEventListener('click', function() {
        document.querySelectorAll('#os-sph, #os-cyl, #os-axis, #os-near-add, #os-bcva, #os-comment').forEach(
            function(input) {
                input.value = '';
            });
    });

    // Clear OU
    document.getElementById('clear-ou').addEventListener('click', function() {
        document.querySelectorAll(
            '#od-sph, #od-cyl, #od-axis, #od-near-add, #od-bcva, #od-comment, #os-sph, #os-cyl, #os-axis, #os-near-add, #os-bcva, #os-comment'
        ).forEach(function(input) {
            input.value = '';
        });
    });

    // Type Selection Buttons
    const typeButtons = document.querySelectorAll('.btn-group button');
    typeButtons.forEach(button => {
        button.addEventListener('click', function() {
            typeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
