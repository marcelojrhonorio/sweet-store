<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" data-insurance-research-card>
  
  <input type="hidden" 
  name="destination" 
  value="{{ env('SWEETBONUS_URL') }}/seguro-auto/info/postback?customer_id={{ session('id') }}"
  data-destination-card>
  
  <div class="item card">
    <img
      class="card-img-top"
      src="{{ asset('/images/sweetbonus-seguro-auto.jpg') }}"
      alt=""
    >
    <div class="card-body">
      <p>
      @if (isset($researchStep) && ('step_one' !== $researchStep))
        <span class="action">
          Você está quase lá! Volte e continue a responder sobre Seguro Auto e ganhe!
        </span>
      @else
        <span class="action">
          Responda essa pesquisa sobre Seguro Auto e ganhe!
        </span>
      @endif
      </p>
      @if(isset($category) && 'Compartilhar' === $category)
        <p style="margin-top:15%">
        <strong style="font-size:10px;color:#5ec6c6;">Ganhe 5 pontos a cada ação feita através do link compartilhado por você!</strong> 
        </p>
      @endif
    </div>
  
    @if(isset($category) && 'Compartilhar' === $category)
    <div style="margin-top: -25%">
      <button 
        class="share-facebook fab fa-facebook-square" 
        data-face
        data-customerid="{{session('id')}}"
        data-actionid="0"
        data-actiontype="insurance_research"                      
      >                   
      Compartilhar
      </button> 
      <input class="sr-only" type="text" id="inputShare">
      <button 
        class="share-copy"  
        data-customerid="{{session('id')}}"
        data-actionid="0"
        data-actiontype="insurance_research"
        btn-copy-link-share
        >
        <i class="fas fa-copy"></i>
          <span style="margin-left:inherit;font-size:14px;" span-copy-link-share>Copiar Link</span>  
      </button>  
      <button 
        class="share-whats"
        data-customerid="{{session('id')}}"
        data-actionid="0"
        data-actiontype="insurance_research" 
        btn-share-whatsapp                    
      >
      <i class="fab fa-whatsapp"></i>
        Whatsapp
      </button>   
    </div>
    @else
    <a href="{{ env('SWEETBONUS_URL') }}/seguro-auto/info/postback?customer_id={{ session('id') }}" 
     target="_blank"
     data-trigger-car-insurance
    >
      <h6>+ 100 pontos<br></h6>
      <h5>Eu quero!</h5>
    </a>
    @endif
  </div>
</div>
