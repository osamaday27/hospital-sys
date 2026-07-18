
<div class="container">
    <h2 class="mb-4">استعلام عن مواعيد الطبيب</h2>

    <!-- نموذج البحث -->
    <form id="schedule-search-form" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label for="doctor_id" class="form-label">اختر الطبيب</label>
                <select name="doctor_id" id="doctor_id"  class="js-example-basic-single form-select" >
                    <option value="">اختر الطبيب</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ isset($selectedDoctor) && $selectedDoctor == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('doctor_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="from_date" class="form-label">من تاريخ</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date') }}">
                @error('from_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="to_date" class="form-label">إلى تاريخ</label>
                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date') }}">
                @error('to_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">بحث</button>
            </div>
        </div>
    </form>

    <!-- عرض النتائج -->
    <div id="schedules-results">
        @if(isset($schedules) && count($schedules) > 0)
            <h4 class="mb-3">المواعيد المتاحة</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>اليوم</th>
                        <th>الفترة الأولى</th>
                        <th>الفترة الثانية</th>
                        <th>الفترة الثالثة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->day }}</td>
                            <td>{{ $schedule->first_shift_from }} - {{ $schedule->first_shift_to }}</td>
                            <td>{{ $schedule->second_shift_from }} - {{ $schedule->second_shift_to }}</td>
                            <td>{{ $schedule->third_shift_from }} - {{ $schedule->third_shift_to }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif(isset($schedules))
            <p class="text-danger">لا توجد مواعيد متاحة لهذا الطبيب في الفترة المحددة.</p>
        @endif
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#schedule-search-form').on('submit', function(e){
            e.preventDefault();

            // جمع البيانات من النموذج
            var formData = $(this).serialize();

            // إرسال البيانات باستخدام AJAX
            $.ajax({
                url: '{{ route("doctor.schedule.search") }}',
                method: 'POST',
                data: formData,
                success: function(response){
                    // تحديث نتائج المواعيد
                    $('#schedules-results').html(response);
                },
                error: function(xhr, status, error){
                    alert('حدث خطأ أثناء إرسال البيانات');
                }
            });
        });
    });
</script>

