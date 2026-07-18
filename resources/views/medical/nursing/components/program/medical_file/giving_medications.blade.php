<div class="tab-pane" id="giving_medications" role="tabpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">{{ __('doctor_trans.giving_medications') }}</div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="medicationTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#today">أدوية اليوم</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#all">جميع الأدوية</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="today">
                            <div class="card">

                                <div class="d-flex justify-content-between">
                                    <h3 class="mb-3 fw-bold">📋 قائمة الأدوية</h3>
                                    <select class="form-select w-auto" id="filterStatus">
                                        <option value="all">عرض الكل</option>
                                        <option value="given">تم إعطاؤه</option>
                                        <option value="not_given">لم يُعطَ</option>
                                    </select>
                                </div>

                                    @php
                                        $dummyMedications = [
                                            [
                                                'id' => 1,
                                                'name' => 'بانادول',
                                                'dose' => '500mg',
                                                'time' => '08:00 AM',
                                                'is_administered' => false,
                                            ],
                                            [
                                                'id' => 2,
                                                'name' => 'أوجمنتين',
                                                'dose' => '625mg',
                                                'time' => '12:00 PM',
                                                'is_administered' => true,
                                            ],
                                            [
                                                'id' => 3,
                                                'name' => 'بروفين',
                                                'dose' => '400mg',
                                                'time' => '06:00 PM',
                                                'is_administered' => false,
                                            ],
                                        ];
                                    @endphp
                                    <div class="timeline mt-4">
                                        @foreach ($dummyMedications as $medication)
                                            <div class="timeline-item position-relative p-3 mb-3 border rounded shadow-sm"
                                                style="border-left: 5px solid {{ $medication['is_administered'] ? '#28a745' : '#dc3545' }};">

                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h5 class="fw-bold">
                                                        <i class="fas fa-pills text-primary"></i>
                                                        {{ $medication['name'] }} ({{ $medication['dose'] }})
                                                    </h5>
                                                    <span
                                                        class="badge {{ $medication['is_administered'] ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $medication['is_administered'] ? '✔ تم إعطاؤه' : '❌ لم يُعطَ' }}
                                                    </span>
                                                </div>

                                                <p class="mb-1"><i class="fas fa-clock"></i> ⏰ وقت الإعطاء:
                                                    <strong>{{ $medication['time'] }}</strong></p>

                                                @if (!$medication['is_administered'])
                                                    <button class="btn btn-sm btn-outline-primary mt-2"
                                                        data-bs-toggle="modal" data-bs-target="#medicationModal"
                                                        onclick="setMedicationId({{ $medication['id'] }})">
                                                        ✅ إعطاء الدواء
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="medicationModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">تأكيد إعطاء الدواء</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">وقت الإعطاء:</label>
                                        <input type="datetime-local" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">اسم الشاهد:</label>
                                        <input type="text" class="form-control" placeholder="أدخل اسم الشاهد">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌
                                    إلغاء</button>
                                <button type="button" class="btn btn-success">✅ تأكيد</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function setMedicationId(id) {
                        console.log("تم تحديد الدواء:", id);
                    }

                    document.getElementById("filterStatus").addEventListener("change", function() {
                        let status = this.value;
                        document.querySelectorAll(".timeline-item").forEach(item => {
                            if (status === "all") {
                                item.style.display = "block";
                            } else if (status === "given" && item.classList.contains("bg-light")) {
                                item.style.display = "block";
                            } else if (status === "not_given" && item.classList.contains("bg-warning")) {
                                item.style.display = "block";
                            } else {
                                item.style.display = "none";
                            }
                        });
                    });
                </script>

            </div>
        </div>
    </div>
</div>
