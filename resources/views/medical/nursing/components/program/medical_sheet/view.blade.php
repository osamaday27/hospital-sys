<div class="tab-pane fade" id="medical_sheet" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- العمود الخاص بالتبويبات (3) -->
                <div class="col-md-12">
                    <input type="text" id="tabSearch" class="form-control mb-2" placeholder="بحث عن التبويب...">
                    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" id="tabList">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#tab1">Pain
                                assessment form</a></li>
                        <li class="nav-item"><a class="nav-link " data-bs-toggle="pill" href="#tab6">patient education form</a></li>
                       
                    </ul>
                    <p id="noResultsMessage" class="text-danger mt-2" style="display:none;">لا توجد تبويبات مطابقة
                        للبحث.</p>
                </div>
                <!-- العمود الخاص بالمحتوى (9) -->
                <div class="col-md-12">
                    <div class="tab-content text-muted">
                        <div class="tab-pane fade show active" id="tab1">
                            <div class="card">
                                <div class="card-body">@include('medical.doctor.components.program.medical_sheet.pain_assessment')</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab2">
                            <div class="card">
                                <div class="card-body">@include('medical.doctor.components.program.medical_sheet.Initial_assessment')</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab3">
                            <div class="card">
                                <div class="card-body">@include('medical.doctor.components.program.medical_sheet.follow_up_assessment')</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab4">
                            <div class="card">
                                <div class="card-body">@include('medical.doctor.components.program.medical_sheet.request_medical_examination')</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab5">
                            <div class="card">
                                <div class="card-body">@include('medical.doctor.components.program.medical_sheet.consultation_form')</div>
                            </div>
                        </div>
                         <div class="tab-pane fade" id="tab6">
                            <div class="card">
                                <div class="card-body">@include('medical.doctor.components.program.medical_sheet.patient_education_form')</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById("tabSearch").addEventListener("keyup", function() {
                    let searchValue = this.value.toLowerCase();
                    let tabs = document.querySelectorAll("#tabList .nav-link");
                    let found = false;
                    let firstMatch = null;
                    tabs.forEach(tab => {
                        let text = tab.innerText.toLowerCase();
                        if (text.includes(searchValue)) {
                            tab.parentElement.style.display = "";
                            if (!firstMatch) firstMatch = tab;
                            found = true;
                        } else {
                            tab.parentElement.style.display = "none";
                        }
                    });
                    document.getElementById("noResultsMessage").style.display = found ? "none" : "block";

                    // تحديد أول تبويب مطابق تلقائيًا
                    if (firstMatch) {
                        firstMatch.click();
                    }
                });
            </script>
        </div>
    </div>
</div>
