 <!doctype html>
 <html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
     data-sidebar-image="none" data-preloader="disable">

 <head>
     <meta charset="UTF-8">
     <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="Description" content="نظام ادارة المستشفيات">
     <meta name="Author" content="نظام ادرة المستشفيات">
     <meta name="Keywords" content="المستشفيات" />

     @include('layouts.head')
     @livewireStyles
 </head>

 <body>
     <!-- Begin page -->
     <div id="layout-wrapper">
         @include('layouts.main-header')
         @include('layouts.main-sidebar')
         <div class="main-content">
             <div class="page-content">
                 <div class="container-fluid">
                    @if (session()->has('success'))
                    <script>
                        window.addEventListener('DOMContentLoaded', () => {
                            Toastify({
                                text: "{{ session('success') }}",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                className: "success",
                                close: true,
                                style: {
                                    background: "#28a745",
                                }
                            }).showToast();
                        });
                    </script>
                @endif
                
                @if (session()->has('error'))
                    <script>
                        window.addEventListener('DOMContentLoaded', () => {
                            Toastify({
                                text: "{{ session('error') }}",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                className: "danger",
                                close: true,
                                style: {
                                    background: "#dc3545",
                                }
                            }).showToast();
                        });
                    </script>
                @endif
                     @yield('content')
                 </div>
                 <!-- container-fluid -->
             </div>
             <!-- End Page-content -->
           
             @include('layouts.json_function')
             @include('layouts.javascriptdoctor')
               @include('layouts.footer')
             @livewireScripts
             
 </body>

 </html>
