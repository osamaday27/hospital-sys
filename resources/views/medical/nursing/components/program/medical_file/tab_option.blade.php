<!-- Nav tabs -->
<ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
    <li class="nav-item">
        <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
            <i class="bx bx-receipt"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.overview') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#vital_signs" role="tab"
            onclick="get_allergies(),get_complain(),get_surgeries(),get_finding(),get_medical_history()">
            <i class="bx bx-pulse"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.vital_signs') }}</span>
        </a>
    </li>
 <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#giving_medications" role="tab"
            onclick="get_allergies(),get_complain(),get_surgeries(),get_finding(),get_medical_history()">
            <i class="bx bx-pulse"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.giving_medications') }}</span>
        </a>
    </li>
       <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#assessmentform" role="tab">
            <i class="bx bx-pulse"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.examineing') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#reassessmentform" role="tab">
            <i class="bx bx-pulse"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.reassessmentform') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14 " data-bs-toggle="tab" href="#medical_sheet" role="tab" onclick="">
            <i class="bx bx-spreadsheet"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.medical_sheet') }}</span>
        </a>
    </li>
  

    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#notes_tab" role="tab" onclick="get_notes()">
            <i class="bx bxs-message-rounded-detail"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.notes') }}</span>
        </a>
    </li>
</ul>
<div class="flex-shrink-0 d-flex gap-2">
    @can('patients-edit')
        <a href="{{ route('patient.edit', ['id' => Crypt::encryptString($patient->id)]) }}" class="btn btn-success">
            <i class="ri-edit-box-line align-bottom"></i> {{ trans('doctor_trans.update_patient_data') }}
        </a>
    @endcan

   {{-- @can('end-nursing-session')--}} {{-- تأكد من وجود صلاحية خاصة بالتمريض لو حبيت --}}
        <form action="{{ route('nursing.end_session', ['id' => $reservations->id]) }}" method="POST"
              onsubmit="return confirm('{{ trans('doctor_trans.confirm_end_session') }}')">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="ri-stop-circle-line align-bottom"></i> {{ trans('doctor_trans.end_nursing_session') }}
            </button>
        </form>
    {{-- @endcan--}}
</div>

