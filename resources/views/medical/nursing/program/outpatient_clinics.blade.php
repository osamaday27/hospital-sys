@extends('layouts.masterliveware')

@section('title')
    {{ trans('nursing_trans.title') }}
@stop

@section('content')
   
  <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                  
             
              </div>
              <!-- Table Section -->
              <div class="card-body">
                @livewire('medical.nursing.outpatient-reservations')

              </div>
          </div>
        
      </div>
  </div>

@endsection
