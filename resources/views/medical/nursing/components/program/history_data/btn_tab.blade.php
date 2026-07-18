   <button type="button" class="btn btn-danger waves-effect waves-light" type="button" data-bs-toggle="offcanvas"
       data-bs-target="#offcanvasExampleallergies" aria-controls="offcanvasExample"
       onclick="get_allergies_history({{ $patient->id }})">
       <i class="bx bx-history"></i>
       <span>{{ trans('doctor_trans.history_allergies') }}</span>
   </button>

   <button type="button" class="btn btn-info waves-effect waves-light" type="button"
       data-bs-toggle="offcanvas" data-bs-target="#offcanvasExampleComplain" aria-controls="offcanvasExample"
       onclick="get_complain_history({{ $patient->id }})">
       <i class="bx bx-history"></i>
       <span>{{ trans('doctor_trans.history_complain') }}</span>
   </button>

   <button type="button" class="btn btn-secondary waves-effect waves-light" type="button"
       data-bs-toggle="offcanvas" data-bs-target="#offcanvasExamplesurgeries" aria-controls="offcanvasExample"
       onclick="get_surgeries_history({{ $patient->id }})">
        <i class="bx bx-history"></i>
       <span>{{ trans('doctor_trans.history_surgeries') }}</span>
   </button>
   <button type="button" class="btn btn-success waves-effect waves-light" type="button"
       data-bs-toggle="offcanvas" data-bs-target="#offcanvasExamplefinding" aria-controls="offcanvasExample"
       onclick="get_finding_history({{ $patient->id }})">
       <script src="{{ URL::asset('assets/images/icon/lordicon.js') }}"></script>
         <i class="bx bx-history"></i>
       <span>{{ trans('doctor_trans.history_finding') }}</span>
   </button>
   <button type="button" class="btn btn-primary  waves-effect waves-light" type="button"
       data-bs-toggle="offcanvas" data-bs-target="#offcanvasExamplehistory_medical_history"
       aria-controls="offcanvasExample" onclick="get_medical_file_medical_history({{ $patient->id }})">
        <i class="bx bx-history"></i>
       <span>{{ trans('doctor_trans.history_medical_history') }}</span>
   </button>
