<div class="container mt-4">
    <h3 class="mb-4 fw-bold">📋 قائمة الأدوية الموصوفة</h3>

    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        @php
        $dummyMedications = [
        ['id' => 1, 'name' => 'بانادول', 'dose' => '500mg', 'frequency' => '3 مرات يومياً', 'is_administered' => false],
        ['id' => 2, 'name' => 'أوجمنتين', 'dose' => '625mg', 'frequency' => 'مرتين يومياً', 'is_administered' => true],
        ['id' => 3, 'name' => 'بروفين', 'dose' => '400mg', 'frequency' => 'كل 8 ساعات', 'is_administered' => false],
        ];
        @endphp

        @foreach($dummyMedications as $medication)
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold">{{ $medication['name'] }}</h5>
                    <p class="card-text">💊 الجرعة: {{ $medication['dose'] }}</p>
                    <p class="card-text">⏰ التكرار: {{ $medication['frequency'] }}</p>

                    <p class="fw-bold {{ $medication['is_administered'] ? 'text-success' : 'text-danger' }}">
                        {{ $medication['is_administered'] ? '✔ تم إعطاؤه' : '❌ لم يُعطَ' }}
                    </p>

                    @if(!$medication['is_administered'])
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#medicationModal"
                        onclick="setMedicationId({{ $medication['id'] }})">
                        ✅ إعطاء الدواء
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center mt-4">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#medicationModal"
            onclick="setMedicationId('all')">
            ✅ إعطاء جميع الأدوية
        </button>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="medicationModal" tabindex="-1" aria-labelledby="medicationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="medicationModalLabel">تأكيد إعطاء الدواء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ إلغاء</button>
                <button type="button" class="btn btn-success">✅ تأكيد</button>
            </div>
        </div>
    </div>
</div>


<script>
function setMedicationId(id) {
    console.log("تم تحديد الدواء:", id);
}
</script>