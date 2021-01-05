<style>

/* Style all font awesome icons */
.fa {
    width: 50px;
    height: 50px;
    position: relative;
    z-index: 1;
    vertical-align: middle;
    display: inline-block;
    overflow: hidden;
    transition: all .2s ease-in-out;
    margin: 10px;
    border-radius: 50%;
    padding: 6%;
    cursor: pointer;
}

.wrapper {
  display: inline-flex;
  margin: 0;
  padding: 0;
  align-items: center;
  /*justify-content: center;*/
}

.wrapper i:nth-child(1) {
  cursor: pointer;
  text-shadow: 0px 7px 10px rgba(0, 0, 0, 0.4);
  transition: all ease-in-out 150ms;
}



.wrapper i:nth-child(2) {
  cursor: pointer;
  text-shadow: 0px 7px 10px rgba(0, 0, 0, 0.4);
  transition: all ease-in-out 150ms;
}


.wrapper i:nth-child(3) {
  cursor: pointer;
  text-shadow: 0px 7px 10px rgba(0, 0, 0, 0.5);
  transition: all ease-in-out 150ms;
}


.wrapper i:nth-child(4) {
  cursor: pointer;
  text-shadow: 0px 7px 10px rgba(0, 0, 0, 0.4);
  transition: all ease-in-out 150ms;
}



/* Add a hover effect if you want */


/* Set a specific color for each brand */

/* Facebook */
.face {
  background: rgb(60,90,153);
  font-size: 30px;
  color: white;
  padding: 4%;
}

.face-2 {
  background: rgb(60,90,153);
  font-size: 22px;
  color: white;
  padding: 4.7%;
  margin-left: 6%;
  margin-top: 4%;
}

.wrapper i {
  text-align: center;
}

.fa-instagram {
  background: rgb(188, 42, 141);
  font-size: 30px;
  color: white;
}

.whats {
  background: #25d366;
  font-size: 22px;
  color: white;
  padding: 10%;
  margin-left: 35%;
}

.app-mobile {
  background: #eaeaea;
  font-size: 22px;
  color: white;
  margin-left: 40%;
  padding: 3.5%;
}

.app-mobile-2{
  background: #eaeaea;
  font-size: 22px;
  color: white;
  margin-left: 46%;
  padding: 3.5%;
}

.download-app-mobile-2 img{
  padding: 3.5%;
}

.insta {
  padding: 3%;
}

.insta-2 {
  padding: 4%;
  margin-top: 4%;
  font-size: 25px;
}

.social-media-login {
  padding: 100px; 
  margin-right: 169px;
}

.face-insta-page {
  margin-left: -128%;
  margin-top: 20%;
}

.follow-us {
  text-align: center; 
  color: #fff; 
  font-size: 15px;
  margin-left: -48%;
}

.download-app-mobile{
  margin-right: -100%;
  margin-top: -66%;
}

.download-app-mobile-2{
  margin-right: -200%;
  margin-top: -74%;
}

.btn-vip-list {
  margin-top: -71%;
  margin-right: 6%;
}

.social-media-store {
  margin-left: 49%;
}

.download-app-mobile-3 {
  margin-right: -230%;
  margin-top: -73%;
}

.download-app-mobile-3 img{
  padding: 3%;
}

@media (max-width: 768px) {
  .social-media-login {
    margin-right: -26px;
    margin-top: -10%;
    padding-top: initial;
    padding-bottom: 7%; 
  }
  
  .face-insta-page {
    margin-left: -70%;
    margin-top: 20%;
  }

  .download-app-mobile {
    margin-right/: -158%;
    margin-top: -72%;
  } 

  .face {
    background: rgb(60,90,153);
    font-size: 30px;
    color: white;
    padding: 6%;
    margin-left: -15%;
  }

  .social-media-store {
    margin-right: 35%;
    margin-left: 30%;
  }

  .insta {
  padding: 5%;
  }

  .app-mobile {
    padding: 3%;
    margin-left: 43%;
  } 
  
  .btn-vip-list {
    margin-top: 14%;
    margin-right: 6%;
  }

  .download-app-mobile-2 {
    margin-right: -108%;
    margin-top: -70%;
  }

  .follow-us {
    text-align: center;
    color: #fff;
    font-size: 15px;
    margin-left: -27%;
  }
  
  .whats {
    background: #25d366;
    font-size: 22px;
    color: white;
    padding: 9%;
    margin-left: 31%;
  }

  .insta-2 {
    padding: 5%;
    margin-top: 4%;
  }

  .face-2 {
    background: rgb(60,90,153);
    font-size: 23px;
    color: white;
    padding: 6%;
    margin-left: 12%;
    margin-top: 4%;
  }

  .download-app-mobile-3 {
    margin-right: -100%;
    margin-top: -73.5%;
  }

  .download-app-mobile-3 img {
    padding: 4.5%;
  }
  .app-mobile-2 {
    background: #eaeaea;
    font-size: 22px;
    color: white;
    margin-left: 46%;
    padding: 3.5%;
    margin-top: 2%;
  }
  .wrapper {
    display: inline-flex;
    margin: 0;
    padding: 0;
    align-items: center;
    justify-content: center;
  }
  
}

</style>

@php
  $face = 'face';
  $insta = 'insta';
  $app = 'app-mobile';
  $classFace = 'face-insta-page';
  $classApp = 'download-app-mobile';
@endphp

@if(env('BUTTON_VIP_LIST'))
  @php
    $face = 'face-2';
    $insta = 'insta-2';
    $app = 'app-mobile-2';
    $classFace = 'face-insta-page';
    $classApp = 'download-app-mobile-2';
  @endphp
@endif

@if(\Request::is('login') || \Request::is('password/change') || \Request::is('password/receive') || \Request::is('password/create') || \Request::is('share-action'))  
<section>  
  <div class="container wrapper">
    <div class="social-media-login">   
      <div class="{{ $classFace }}">
        <h3 class="follow-us">Acompanhe-nos</h3> 
        <a href="https://www.facebook.com/PortalSweetBonus/" target="_blank"><i class="fa fa-facebook {{ $face }}"></i></a>
        <a href="https://www.instagram.com/portalsweetbonus/" target="_blank"><i class="fa fa-instagram {{ $insta }}"></i></a>        
      </div>
      <div class="{{ $classApp }}">
        <h3 style="text-align: center; color: #fff; font-size: 15px;">Baixe nosso app</h3>          
        <a href="{{ env('URL_DOWNLOAD_APP') }}" target="_blank"> <img src="https://img.icons8.com/color/48/000000/google-play.png" class="fa {{ $app }}"></a>
      </div> 
      @if(env('BUTTON_VIP_LIST'))
        <div class="btn-vip-list"> 
          <h3 style="text-align: center; color: #fff; font-size: 15px;">Lista Vip da Sweet</h3>     
          <a data-list-vip><i class="fa fa-whatsapp whats"></i></a> 
        </div>     
      @endif
    </div>
  </div>
</section>
@else
@if(env('BUTTON_VIP_LIST'))
  @php
    $classApp = 'download-app-mobile-3';
  @endphp
@endif
<section>          
  <div class="container wrapper">
    <div class="social-media-store">   
      <div class="{{ $classFace }}">
        <h3 class="follow-us" style="color: #77787a">Acompanhe-nos</h3>
        <a href="https://www.facebook.com/PortalSweetBonus/" target="_blank"><i class="fa fa-facebook {{ $face }}"></i></a>
        <a href="https://www.instagram.com/portalsweetbonus/" target="_blank"><i class="fa fa-instagram {{ $insta }}"></i></a>
      </div>
      <div class="{{ $classApp }}">
        <h3 style="text-align: center; color: #77787a; font-size: 15px;">Baixe nosso app</h3>          
        <a href="{{ env('URL_DOWNLOAD_APP') }}" target="_blank"><img src="https://img.icons8.com/color/48/000000/google-play.png" class="fa {{ $app }}"></a>
      </div> 
      @if(env('BUTTON_VIP_LIST'))
        <div class="btn-vip-list"> 
          <h3 style="text-align: center; color: #77787a; font-size: 15px;">Lista Vip da Sweet</h3>     
          <a data-list-vip><i class="fa fa-whatsapp whats"></i></a> 
        </div>     
      @endif     
    </div>
  </div>
</section>
@endif



