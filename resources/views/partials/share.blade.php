@if (empty($category) || 'Compartilhar' === $category || 'Home' === $category)
  <h3>
    <span>Compartilhe com seus amigos</span> <br>
    E a cada <span>cadastro</span> ou <span>download do app</span> através do seu link você ganha <span>10 pontos!</span>
    <br><br> Veja as informações em nossos <a href="{{ env('SWEETBONUS_URL') }}/termos-e-condicoes" target="_blank"><span>Termos e Condições</span></a> 
  </h3>

  <input type="hidden" value="{{env('STORE_URL')}}" data-store-url>
  <input type="hidden" id="customer" name="customer" value="{{ session('id') }}" data-customer-id>
  <input type="hidden" id="sweetbonus-url" name="sweetbonus-url" value={{ env('SWEETBONUS_URL') }} data-sweetbons-url>
  
  <ul class="invite-friends" data-share-buttons>

   <li>
      <input class="sr-only" type="text" id="myInputApp">
      <button class="btn-download-app" data-btn-download-app>
        <i class="fab fa-google-play"></i>
        <span data-span-download-app>Link App </span>           
      </button>      
    </li>
    <li>
      <input class="sr-only" type="text" id="myInput">
      <button class="btn-copy-link" data-btn-copy-link>
        <i class="fas fa-copy"></i>
          <span data-span-copy-link>Copiar Link</span>  
      </button>
    </li>   
    <li>
      <button class="btn-twitter" data-btn-twitter>
        <i class="fab fa-twitter"></i>
        Twitter
      </button>
    </li>
    <li>
      <button class="btn-facebook" data-btn-facebook>
      <i class="fab fa-facebook-square"></i>
        Facebook
      </button>
    </li>
    <li>
      <button class="btn-whatsapp" data-btn-whatsapp>
      <i class="fab fa-whatsapp"></i>
        Whatsapp
      </button>
    </li>
    @if (session('clicks_share_mail') < 5)
      <li>
        <button class="btn-email" data-btn-email>
          <i class="fas fa-envelope"></i>
          E-mail
        </button>
      </li>
    @endif   
  </ul>
@else
  @include('partials.listing-top-title')
@endif