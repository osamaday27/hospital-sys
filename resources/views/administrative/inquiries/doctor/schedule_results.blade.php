@if(count($schedules) > 0)
    <h4 class="mb-3">المواعيد المتاحة</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>اليوم</th>
                <th>اسم العيادة</th>  <!-- عمود جديد لاسم العيادة -->
                <th>الفترة الأولى</th>
                <th>الفترة الثانية</th>
                <th>الفترة الثالثة</th>
            </tr>
        </thead>
        <tbody>
           @foreach($schedules as $schedule)
                <tr>
                    <td>{{ __('outpatient_trans.' . strtolower($schedule->day)) }}</td>
                    <td style="text-align: center;">{{ $schedule->clinic_name }}</td> <!-- عرض اسم العيادة -->
                                        <td style="direction: initial;text-align: center;">{{ $schedule->first_shift_from }} - {{ $schedule->first_shift_to }}</td>
                    <td style="direction: initial;text-align: center;">{{ $schedule->second_shift_from }} - {{ $schedule->second_shift_to }}</td>
                    <td style="direction: initial;text-align: center;">{{ $schedule->third_shift_from }} - {{ $schedule->third_shift_to }}</td>
                </tr>
           @endforeach
        </tbody>
    </table>
@else
    <p class="text-danger">لا توجد مواعيد متاحة لهذا الطبيب في الفترة المحددة.</p>
@endif
