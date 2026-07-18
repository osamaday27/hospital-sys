  <div class="tab-pane fade show active" id="glasses" role="tabpanel" aria-labelledby="glasses-tab" tabindex="0">
        <div class="cards-container">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-body">
                    {{-- Sub Nav tabs --}}
                    <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" id="glassesSubTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active old-glasses"
                               id="tab-old_glasses"
                               data-bs-toggle="tab"
                               href="#nav-border-top-old_glasses"
                               role="tab"
                               aria-controls="nav-border-top-old_glasses"
                               aria-selected="true">
                                <i class="mdi mdi-glasses"></i> Old Glasses
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a class="nav-link objective-refraction"
                               id="tab-objective_refraction"
                               data-bs-toggle="tab"
                               href="#nav-border-top-objective_refraction"
                               role="tab"
                               aria-controls="nav-border-top-objective_refraction"
                               aria-selected="false">
                                <i class="mdi mdi-eye"></i> Objective refraction
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a class="nav-link k-readings"
                               id="tab-k_readings"
                               data-bs-toggle="tab"
                               href="#nav-border-top-k_readings"
                               role="tab"
                               aria-controls="nav-border-top-k_readings"
                               aria-selected="false">
                                <i class="mdi mdi-chart-line"></i> K- Readings
                            </a>
                        </li>

                    
                    
                    </ul>

                    {{-- Sub Tab panes --}}
                    <div class="tab-content text-muted" id="glassesSubTabsContent">
                        <div class="tab-pane fade show active"
                             id="nav-border-top-old_glasses"
                             role="tabpanel"
                             aria-labelledby="tab-old_glasses"
                             tabindex="0">
                            @livewire('old-glasses', [
                                'id_patient'     => $patient->id,
                                'id_reservation' => $reservations->id,
                                'type_visit'     => $patient_visits->type,
                                'id_visit'       => $patient_visits->id
                            ])
                        </div>

                        <div class="tab-pane fade"
                             id="nav-border-top-objective_refraction"
                             role="tabpanel"
                             aria-labelledby="tab-objective_refraction"
                             tabindex="0">
                            @livewire('objective-refraction', [
                                'id_patient'     => $patient->id,
                                'id_reservation' => $reservations->id,
                                'type_visit'     => $patient_visits->type,
                                'id_visit'       => $patient_visits->id
                            ])
                        </div>

                        <div class="tab-pane fade"
                             id="nav-border-top-k_readings"
                             role="tabpanel"
                             aria-labelledby="tab-k_readings"
                             tabindex="0">
                            @livewire('k-readings', [
                                'id_patient'     => $patient->id,
                                'id_reservation' => $reservations->id,
                                'type_visit'     => $patient_visits->type,
                                'id_visit'       => $patient_visits->id
                            ])
                        </div>

                      


                       
                    </div>
                </div><!-- end card-body -->
            </div>
        </div>
        <!--end col-->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ✅ تخزين آخر تبويب فرعي داخل قسم النظارات
    const STORAGE_KEY = 'ophthalmology_last_glasses_subtab';
    const subTabSelector = '#glassesSubTabs a[data-bs-toggle="tab"]';

    // افتح تبويب من الهاش أو من التخزين أو الافتراضي
    const hashTarget = document.querySelector(`#glassesSubTabs a[href="${location.hash}"]`);
    const stored = localStorage.getItem(STORAGE_KEY);
    const storedTarget = stored ? document.querySelector(`#glassesSubTabs a[href="${stored}"]`) : null;

    if (hashTarget) {
        new bootstrap.Tab(hashTarget).show();
    } else if (storedTarget) {
        new bootstrap.Tab(storedTarget).show();
    }

    document.querySelectorAll(subTabSelector).forEach(link => {
        link.addEventListener('shown.bs.tab', function (e) {
            const target = e.target.getAttribute('href'); // e.g. #nav-border-top-old_glasses
            if (target) {
                localStorage.setItem(STORAGE_KEY, target);
                // حدّث الهاش بدون إعادة تحميل
                history.replaceState(null, '', target);
            }
        });
    });

    // ✅ سكربت مسح القيم (يدعم غياب العناصر بدون أخطاء)
    const byId = id => document.getElementById(id);
    const clearInputs = (selectors) => {
        document.querySelectorAll(selectors).forEach(function (input) {
            if (input) input.value = '';
        });
    };

    const clearOdBtn = byId('clear-od');
    if (clearOdBtn) {
        clearOdBtn.addEventListener('click', function () {
            clearInputs('#od-sph, #od-cyl, #od-axis, #od-near-add, #od-bcva, #od-comment');
        });
    }

    const clearOsBtn = byId('clear-os');
    if (clearOsBtn) {
        clearOsBtn.addEventListener('click', function () {
            clearInputs('#os-sph, #os-cyl, #os-axis, #os-near-add, #os-bcva, #os-comment');
        });
    }

    const clearOuBtn = byId('clear-ou');
    if (clearOuBtn) {
        clearOuBtn.addEventListener('click', function () {
            clearInputs('#od-sph, #od-cyl, #od-axis, #od-near-add, #od-bcva, #od-comment, #os-sph, #os-cyl, #os-axis, #os-near-add, #os-bcva, #os-comment');
        });
    }

    // ✅ تفعيل حالة الأزرار (إن وُجدت مجموعة أزرار النوع)
    const typeButtonsGroup = document.querySelectorAll('.btn-group button');
    if (typeButtonsGroup.length) {
        typeButtonsGroup.forEach(button => {
            button.addEventListener('click', function () {
                typeButtonsGroup.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
});
</script>
