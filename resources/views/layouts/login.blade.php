<!doctype html>
<html lang="pt-BR">
<head>
  {{-- Required meta tags --}}
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if('uat-sweetbonus' == $domain)
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex">
  @endif

  {{-- Bootstrap CSS --}}
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  {{-- CSS --}}
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('assets/home/css/style.css') }}"> 

  {{-- Sweet Alert --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.css" />
  
  {{-- Icon --}}
  <link rel="icon" href="{{ asset('assets/home/imgs/favicon.ico') }}" type="image/x-icon"/>
  <link rel="shortcut icon" href="{{ asset('assets/home/imgs/favicon.ico') }}" type="image/x-icon"/>

  {{-- Fonts --}}
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
 

  <title>Sweet Bonus: Portal de Relacionamento - @yield('title')</title>

  {{-- Google Analytics --}}
  @include('partials.analytics')  

  {{-- Modal List Vip --}}
  @include('partials.list-vip-sweet.modal')

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
    
</head>
<body style="contain:content;">
  {{-- Facebook Page --}}
  <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = 'https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v3.1&appId=312601859545386&autoLogAppEvents=1';
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    

  <section id="main">
    <header>
      <div class="container">
        <div class="row">
          <div class="col-md-7">
            <a href="/" title="Sweet Bonus" class="logo"><img src="{{ asset('assets/home/imgs/main-logo.png') }}"></a>
          </div>
          <div class="col-md-5 menu">
            <ul id="top-menu">
              <li><a href="https://sweetbonus.com.br/blog/">Blog</a></li>
              <input name="sweetbonus" type="hidden" value="{{ session('share_indicated_by') }}" customerId>
              <input name="sweetbonus" type="hidden" value="{{ env('SWEETBONUS_URL') }}" data-login-sweetbonus-url>
              <input name="sweetbonus" type="hidden" value="{{ session('share_action_id') }}" data-actionId>
              <input name="sweetbonus" type="hidden" value="{{ session('share_action_type') }}" data-actionType>

              @if(session('share_page') && ('share-action' === session('share_page')))  
                <li><a data-link-cad>Cadastre-se</a></li>
              @else
                <li><a href="{{ env('SWEETBONUS_URL') }}">Cadastre-se</a></li>
              @endif
            </ul>
          </div>
        </div>
      </div>
    </header>
    <div class="container">
      <div class="row">
        <div class="col-lg-7">
          <h1>
            <ul>
              <li><img src="{{ asset('assets/home/imgs/main-number-1.png') }}"> Entre na nossa <strong>comunidade</strong></li>
              <li><img src="{{ asset('assets/home/imgs/main-number-2.png') }}"> Responda a <strong>pesquisas</strong></li>
              <li><img src="{{ asset('assets/home/imgs/main-number-3.png') }}"> Ganhe <strong>pontos</strong></li>
              <li><img src="{{ asset('assets/home/imgs/main-number-4.png') }}"> E troque por <strong>produtos </strong>   que receberá <span>gratuitamente</span> em casa</li>
            </ul>
          </h1>
          <img src="{{ asset('assets/home/imgs/main-products.png') }}" class="products img-fluid">
        </div>
        <div class="col-lg-5">
          {{-- Form --}}
          @yield('content')
        </div>
      </div>
    </div>
  </section>
     
  <section id="contact">
    @include('partials.login.text-support')
    <div>
    @include('partials.social')      
    </div>
  </section>

  <footer>
    © {{ now()->year }} Sweet | Todos os direitos reservados | <a target="_blank" href="http://sweetbonus.com.br/politica-de-privacidade-sweet">Política de Privacidade</a>
  </footer>

  {{-- jQuery first, then Popper.js, then Bootstrap JS --}}
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="{{ asset('assets/home/js/login.js') }}"></script>
  <script async src="{{ asset('js/store.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
</body>
</html>
