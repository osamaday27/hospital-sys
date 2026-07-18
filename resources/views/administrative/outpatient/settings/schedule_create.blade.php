@extends('layouts.master')
@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ trans('outpatient_trans.scheduling_settings') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('schedule_settings.store') }}" method="post">
                    @csrf
                    <input type="hidden" id="id_clinic" name="id_clinic" value="{{ $admsclinic->id }}">
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="id_doctor" class="form-label">{{ trans('outpatient_trans.doctor') }}</label>
                            <select name="id_doctor" id="id_doctor" class="form-select js-example-basic-single" required>
                                <option value="">{{ trans('outpatient_trans.select_doctor') }}</option>
                                @foreach ($doctor as $data)
                                    <option value="{{ $data->id }}">{{ $data->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="examination_time" class="form-label">{{ trans('outpatient_trans.examination_time') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="examination_time" name="examination_time"
                                       placeholder="{{ trans('outpatient_trans.example_time') }}">
                                <button type="button" class="btn btn-outline-secondary copy-btn" data-target="examination_time">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    @php
                        $days = [
                            'saturday' => trans('outpatient_trans.saturday'),
                            'sunday' => trans('outpatient_trans.sunday'),
                            'monday' => trans('outpatient_trans.monday'),
                            'tuesday' => trans('outpatient_trans.tuesday'),
                            'wednesday' => trans('outpatient_trans.wednesday'),
                            'thursday' => trans('outpatient_trans.thursday'),
                            'friday' => trans('outpatient_trans.friday'),
                        ];
                    @endphp

                    <div class="schedule-container">
                        @foreach ($days as $dayKey => $dayName)
                            <div class="card day-card mb-3">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input day-checkbox" type="checkbox" role="switch"
                                                id="SwitchCheck{{ $dayKey }}" name="day[]" value="{{ $dayKey }}"
                                                onclick="toggleDay('{{ $dayKey }}')">
                                            <label class="form-check-label fw-bold" for="SwitchCheck{{ $dayKey }}">
                                                {{ $dayName }}
                                            </label>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary copy-day-btn" data-day="{{ $dayKey }}">
                                                <i class="ri-file-copy-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary paste-day-btn" data-day="{{ $dayKey }}">
                                                <i class="ri-clipboard-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row shifts-row">
                                        @for ($shift = 1; $shift <= 3; $shift++)
                                            <div class="col-md-4 mb-3 shift-container">
                                                <div class="border p-3 rounded">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="form-check-label fw-semibold">
                                                            {{ trans(str_replace('%s', $shift, 'outpatient_trans.shift_%s')) }}
                                                        </label>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary copy-shift-btn" 
                                                                data-day="{{ $dayKey }}" data-shift="{{ $shift }}">
                                                                <i class="ri-file-copy-line"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-primary paste-shift-btn" 
                                                                data-day="{{ $dayKey }}" data-shift="{{ $shift }}">
                                                                <i class="ri-clipboard-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col">
                                                            <label class="form-label small">{{ trans('outpatient_trans.from') }}</label>
                                                            <input type="time" class="form-control shift-{{ $dayKey }}" 
                                                                id="{{ $dayKey }}_shift{{ $shift }}_from"
                                                                name="{{ $dayKey }}_shift{{ $shift }}_from" disabled>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label small">{{ trans('outpatient_trans.to') }}</label>
                                                            <input type="time" class="form-control shift-{{ $dayKey }}" 
                                                                id="{{ $dayKey }}_shift{{ $shift }}_to"
                                                                name="{{ $dayKey }}_shift{{ $shift }}_to" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">{{ trans('buttons.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ⚡ نظام النسخ / اللصق --}}
<script>
    let clipboardData = { type:null, day:null, shift:null, examination_time:null, shifts:{} };

    function toggleDay(dayKey) {
        const checkbox = document.getElementById(`SwitchCheck${dayKey}`);
        const inputs = document.querySelectorAll(`.shift-${dayKey}`);
        inputs.forEach(input => {
            if (checkbox.checked) input.removeAttribute("disabled");
            else { input.setAttribute("disabled","disabled"); input.value=''; }
        });
    }

    // نسخ وقت الكشف
    document.querySelectorAll('.copy-btn').forEach(btn=>{
        btn.addEventListener('click',function(){
            const target=this.dataset.target;
            const value=document.getElementById(target).value;
            if(value){
                clipboardData={type:'examination_time',examination_time:value};
                showToast("{{ trans('outpatient_trans.copied_examination_time') }}",'success');
            }
        });
    });

    // نسخ يوم كامل
    document.querySelectorAll('.copy-day-btn').forEach(btn=>{
        btn.addEventListener('click',function(){
            const dayKey=this.dataset.day;
            clipboardData={type:'day',day:dayKey,shifts:{}};
            for(let s=1;s<=3;s++){
                clipboardData.shifts[s]={
                    from:document.getElementById(`${dayKey}_shift${s}_from`).value,
                    to:document.getElementById(`${dayKey}_shift${s}_to`).value
                };
            }
            clipboardData.examination_time=document.getElementById('examination_time').value;
            showToast("{{ trans('outpatient_trans.copied_day') }} "+dayKey,'success');
        });
    });

    // لصق يوم
    document.querySelectorAll('.paste-day-btn').forEach(btn=>{
        btn.addEventListener('click',function(){
            const dayKey=this.dataset.day;
            if(clipboardData.type==='day'){
                document.getElementById(`SwitchCheck${dayKey}`).checked=true;
                toggleDay(dayKey);
                for(let s=1;s<=3;s++){
                    document.getElementById(`${dayKey}_shift${s}_from`).value=clipboardData.shifts[s].from;
                    document.getElementById(`${dayKey}_shift${s}_to`).value=clipboardData.shifts[s].to;
                }
                if(clipboardData.examination_time){
                    document.getElementById('examination_time').value=clipboardData.examination_time;
                }
                showToast("{{ trans('outpatient_trans.pasted_to_day') }} "+dayKey,'success');
            }
        });
    });

    // نسخ شيفت
    document.querySelectorAll('.copy-shift-btn').forEach(btn=>{
        btn.addEventListener('click',function(){
            const dayKey=this.dataset.day;
            const s=this.dataset.shift;
            clipboardData={
                type:'shift',day:dayKey,shift:s,
                from:document.getElementById(`${dayKey}_shift${s}_from`).value,
                to:document.getElementById(`${dayKey}_shift${s}_to`).value
            };
            showToast("{{ trans('outpatient_trans.copied_shift') }} "+s,'success');
        });
    });

    // لصق شيفت
    document.querySelectorAll('.paste-shift-btn').forEach(btn=>{
        btn.addEventListener('click',function(){
            const dayKey=this.dataset.day;
            const s=this.dataset.shift;
            if(clipboardData.type==='shift'){
                document.getElementById(`SwitchCheck${dayKey}`).checked=true;
                toggleDay(dayKey);
                document.getElementById(`${dayKey}_shift${s}_from`).value=clipboardData.from;
                document.getElementById(`${dayKey}_shift${s}_to`).value=clipboardData.to;
                showToast("{{ trans('outpatient_trans.pasted_to_shift') }} "+s,'success');
            }
        });
    });

    // تغيير الطبيب → تحميل مواعيده
    $('#id_doctor').change(function(){
        var doctorId=$(this).val();
        var clinicId=$('#id_clinic').val();
        if(doctorId&&clinicId){
            $.get('{{ route("get.doctor.schedule") }}',{doctor_id:doctorId,clinic_id:clinicId},function(res){
                if(res.schedule) updateScheduleForm(res.schedule);
                else clearScheduleForm();
            });
        }
    });

    function updateScheduleForm(schedule){
        clearScheduleForm();
        Object.keys(schedule).forEach(day=>{
            var d=schedule[day];
            if(d){
                document.getElementById(`SwitchCheck${day}`).checked=true;
                toggleDay(day);
                for(let s=1;s<=3;s++){
                    document.getElementById(`${day}_shift${s}_from`).value=d['shift'+s].from;
                    document.getElementById(`${day}_shift${s}_to`).value=d['shift'+s].to;
                }
                if(d.examination_time) document.getElementById('examination_time').value=d.examination_time;
            }
        });
    }

    function clearScheduleForm(){
        document.querySelectorAll('.day-checkbox').forEach(ch=>{
            ch.checked=false;
            toggleDay(ch.value);
        });
        document.getElementById('examination_time').value='';
    }

    function showToast(msg,type){
        Swal.fire({toast:true,position:'top-end',showConfirmButton:false,timer:2000,icon:type,title:msg});
    }
</script>
@endsection
