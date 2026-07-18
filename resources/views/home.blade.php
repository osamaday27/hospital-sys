 @extends('layouts.master')
 <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" />
 @section('title')
     {{ trans('main_trans.title') }}
 @stop
 <style>
     .body {
         margin: 0;
         display: flex;
         align-items: center;
         justify-content: center;
         height: 80vh;
     }

     .container {
         text-align: center;
     }

     img {
         max-width: 100%;
         max-height: 100%;
         display: block;
         margin: 0 auto;
     }
 </style>
 @section('content')
     <div class="row">
         <div class="col-12">
             <div class="page-title-box d-sm-flex align-items-center justify-content-between">
             </div>
         </div>
     </div>
     <div class="body">
         <div class="container">
             <img src="{{ URL::asset('assets/images/logo-sm.png') }}" style="width: 50%;">
         </div>
     </div>
 @endsection
