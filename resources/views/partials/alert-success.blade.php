{{-- resources/views/partials/alert-success.blade.php --}}
@php
  $msg = session('success') ?? session('status') ?? session('message');
@endphp

@if($msg)
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index: 9999; min-width: 320px;">
    {{ $msg }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="@lang('assets.close')"></button>
  </div>

  <script>
    (function(){
      if (window.jQuery) {
        setTimeout(function(){
          $('.alert-success').fadeOut(600, function(){ $(this).remove(); });
        }, 3000);
      } else {
        // بدون jQuery
        setTimeout(function(){
          var el = document.querySelector('.alert-success');
          if(el){ el.style.transition='opacity .6s'; el.style.opacity='0'; setTimeout(()=>el.remove(), 700); }
        }, 3000);
      }
    })();
  </script>
@endif
