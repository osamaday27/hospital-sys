   <button type="button" class="btn btn-success waves-effect waves-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExamplelab" aria-controls="offcanvasExample" onclick="getlab_history({{$patient->id}})">
             <i class="bx bx-history"></i>

       <span>{{ trans('doctor_trans.history_lab') }}</span>
   </button>

   <button type="button" class="btn btn-info waves-effect waves-light" type="button"
       data-bs-toggle="offcanvas" data-bs-target="#offcanvasExamplerad" aria-controls="offcanvasExample"
       onclick="getrad_history({{$patient->id}})">
             <i class="bx bx-history"></i>

       <span>{{ trans('doctor_trans.history_rad') }}</span>
   </button>


