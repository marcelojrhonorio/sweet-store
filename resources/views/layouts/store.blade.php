<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   
    @if(Session::has('download.ebook.after.redirect'))
         <meta http-equiv="refresh" content="5;url={{ Session::get('download.ebook.after.redirect') }}">
    @endif

    <title>{{ env('APP_NAME') }} - @yield('title')</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900">
    <link rel="stylesheet" href="{{ asset('css/store.css') }}">

    {{-- Cropper --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css">
     
    <script src="dist/clipboard.min.js"></script>

    {{-- Google Analytics --}}
    @include('partials.analytics')
    {{-- Pixels --}}
    @include('partials.pixels')

    {{-- Smartlook --}}
    @if(isset($smartlook) and $smartlook)
    <script type='text/javascript'>
      window.smartlook||(function(d) {
        var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
        var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
        c.charset='utf-8';c.src='https://rec.smartlook.com/recorder.js';h.appendChild(c);
        })(document);
        smartlook('init', 'cf6adfb149a5935ed02c5514621714231070ef7b');
    </script>
    @endif  

    {{-- Sweet Alert --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.css" />

    {{-- OneSignal Web Push--}}
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
    var OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
        appId: "633cc1ed-e7da-4c1e-b92d-cf22807ef5e4",
        });
    });
    </script>
  
  </head>
  <body>

    <div  class="d-flex">
      <nav class="sidebar">
        {{-- Profile --}}
        @include('partials.sidebar-profile')

        {{-- Main Menu --}}
        @include('partials.sidebar-menu')
      </nav>

      <div class="content">
        {{-- Header --}}
        @include('partials.header')

        {{-- Banner --}}
        @include('partials.banner')
        <div id="loading-store">
          <div  style="display: flex;justify-content: center;float:inherit;align-items: center;align-content: center;" >

            @include('loading')
          </div>
          <br>
          <br>
          <p  style="display: flex;justify-content: center;float:inherit;align-items: center;align-content: center;">
            Melhores ações carregando!
            <br />
            Entrando na Loja ...
          </p>
        </div>
        <div id="store" style="display:none;">
        {{-- Page content --}}
        @yield('content')

       
        @include('partials.social')
         
        {{-- Featured Products --}}
        @include('partials.featured-items')
        </div>
      </div>
    </div>

    {{-- Modal SSI research --}}
    @include('partials.ssi.modal')

    {{-- Modal User indication --}}
    @include('partials.user-indication.modal')

    {{-- Modal exchange points --}}
    @include('partials.exchange-points.modal')

    {{-- Modal exchange points 2 --}}
    @include('partials.exchange-points.modal-backdrop')

    {{-- Modal List Vip --}}
    @include('partials.list-vip-sweet.modal')

    {{-- Modal Login Points --}}
    @include('partials.login-points.modal')

    {{-- Modal e-mail confirmation --}}
    @include('partials.modal-confirmation')

    {{-- Modal receive offers --}}
    @include('partials.modal-receive-offers')

    {{-- Modal email forwarding 1--}}
    @include('partials.email-forwarding.modal-home')

    {{-- Modal email forwarding 2 --}}
    @include('partials.email-forwarding.modal-how-it-works')

    {{-- Modal email forwarding 3 --}}
    @include('partials.email-forwarding.modal-send-email')

    {{-- Modal email forwarding 4 --}}
    @include('partials.email-forwarding.modal-submit-proof')

    {{-- Modal download file --}}
    @include('partials.modal-download')

    {{-- Modal invitation email sent --}}
    @include('partials.modal-invite-email')

    {{-- Modal car insurance --}}
    @include('car-insurance.modal')
    
    {{-- Modal Social Class --}}
    @include('social-class.modal')

    {{-- Before closes body --}}
    @yield('before-body-close')
    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root">
    </div>
    @if(env('FACEBOOK_CHAT'))
      <div class="fb-customerchat"
           attribution=setup_tool
           page_id="632011853849287"
           ref="{{ Session::get('id') }}"
           theme_color="#b5498d"
           logged_in_greeting="Envie uma msg e receba 50 pontos!"
           logged_out_greeting="Enviaremos ações periodicamente para você ganhar pontos!">
      </div>
    @endif
    <input type="hidden" id="facebookAppId" value="{{ env('FACEBOOK_APP_ID') }}">
    <input type="hidden" id="stamps_required" value="{{ env('STAMPS_REQUIRED') }}">
    <input type="hidden" id="sweetmedia" value="{{ env('APP_STORAGE') }}">
    <input type="hidden" id="login_points" value="{{ env('LOGIN_POINTS') }}" login-points>
    {{-- Adscence --}}
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script async src="{{ asset('js/store.js') }}"></script>
</body>
</html>
