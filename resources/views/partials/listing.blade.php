
<section id="ganhar-pontos" style="background-color: #EDFBFF">
<input type="hidden" id="sweetbonus-url" name="sweetbonus-url" value={{ env('STORE_URL') }} data-store-url>

  @if(isset($params))
    <input type="hidden" id="indicated_by" name="indicated_by" value={{ $params['customer_id'] }} share-indicated-by>
    <input type="hidden" id="action_id" name="action_id" value={{ $params['action_id'] }} share-action-id>
    <input type="hidden" id="action_type" name="action_type" value={{ $params['action_type'] }} share-action-type>
  @endif

  <div class="container">
    <div class="row">
      <div class="col-12">
       <div id="app">
         {{--  <example-component>
         </example-component>         --}}
       </div>
        @include('partials.listing-top')
      </div>
    </div>

    @if (false === empty($actions))
      <div class="row" data-btn-actions>
        <div class="col-11 col-centered">
          <div class="row itens">
            @unless ($user['confirmed'])
              @include('partials.confirmationemail')
            @endunless
            @if(env('EMAIL_FORWARDING'))  
              @if(preg_match('/gmail/', session('email')))
                @include('partials.email-forwarding.card-email-forwarding')             
              @endif              
            @endif
            @if(isset($receive_offers))
              @if(!$receive_offers)
                @include('partials.receive-offers')
              @endif
            @endif
            @if ($shouldShowCard)
              @if ('Compartilhar' === $category)
                @include('car-insurance.card', ['category' => 'Compartilhar'])
              @else
                @include('car-insurance.card')
              @endif
            @endif
            
            @if(isset($socialClass))
              @include('social-class.card')
            @endif 

            @foreach ($actions as $action)
           
              <div class="col-xs-12 col-sm-12 col-lg-3">
                <div class="item card">
                @if (strpos($action->path_image, 'sm-exchange'))
                  <img
                    class="card-img-top"
                    src="{{ env('STORE_URL') . '/storage/' . $action->path_image }}"
                    alt=""
                  >
                @else
                  <img
                    class="card-img-top"
                    src="{{ env('APP_IMAGE_CAMPAIGN_URL') . '/' . $action->path_image }}"
                    alt=""
                  >
                @endif                 
                  <div class="card-body">
                    <p>
                      <span class="action">
                        {{ $action->title }}
                      </span>
                      <br>
                      {{ $action->description }}
                    </p>
                    @if(isset($category) && 'Compartilhar' === $category)
                      <p style="margin-top:5%">
                      <strong style="font-size:10px;color:#5ec6c6;">Ganhe 5 pontos a cada ação feita através do link compartilhado por você!</strong> 
                      </p>
                    @endif
                  </div>

                  @if(isset($category) && 'Compartilhar' === $category)
                  <div style="margin-top: -17%">
                    <button 
                      class="share-facebook" 
                      data-face
                      data-customerid="{{session('id')}}"
                      data-actionid="{{$action->id}}"
                      data-actiontype="{{$action->action_type_id}}"                       
                    > 
                      <i class="fab fa-facebook-square"></i>                  
                    Compartilhar
                    </button> 
                    <input class="sr-only" type="text" id="inputShare">
                    <button 
                      class="share-copy"
                      data-customerid="{{session('id')}}"
                      data-actionid="{{$action->id}}"
                      data-actiontype="{{$action->action_type_id}}"
                      btn-copy-link-share
                      >
                      <i class="fas fa-copy"></i>
                        <span style="margin-left:inherit;font-size:14px;" span-copy-link-share>Copiar Link</span>  
                    </button>   
                    <button 
                      class="share-whats"
                      data-customerid="{{session('id')}}"
                      data-actionid="{{$action->id}}"
                      data-actiontype="{{$action->action_type_id}}"
                      btn-share-whatsapp                    
                    >
                    <i class="fab fa-whatsapp"></i>
                      Whatsapp
                    </button>                 
                  </div>  
                  
                 
                  @elseif (isset($action->action_type_metas[0]) && ($action->action_type_metas[0]->action_type_id == 2))
                    <a
                      target="_blank"
                      href="{{ $action->action_type_metas[0]->value }}&texto2={{ session('id') }}&cw=1"
                      data-action="{{ $action->id }}"
                      data-trigger-checkin
                    >
                      @if (empty($action->grant_points))
                        Super Oportunidade! <br> Não ganha pontos <br>
                      @else
                        <h6>+ {{ $action->grant_points }} pontos<br /></h6>
                      @endif
                      <h5>Eu quero!</h5>
                    </a>
                  @else
                    @php
                      if(isset($action->action_type_metas[0])) {
                        $val = $action->action_type_metas[0]->value;
                      } else {
                        $val = env('SWEETBONUS_URL');
                      }

                    @endphp
                    <a
                      target="_blank"
                      href="{{ $val }}"
                      data-action="{{ $action->id }}"
                      data-trigger-checkin
                    >
                      @if (empty($action->grant_points))
                        Super Oportunidade! <br> Não ganha pontos <br>
                      @else
                        <h6>+ {{ $action->grant_points }} pontos<br /></h6>
                      @endif
                      <h5>Eu quero!</h5>                         
                    </a>  
                  @endif
                 
                </div>                             
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @else
      @if(!(env('MEMBER_GET_MEMBER') && isset($category) && 'Compartilhar' === $category))
        <div class="row">
          <div class="col-12">
            <div class="alert alert-warning alert-center">
              <b>Oops!</b> Volte mais tarde para ver novas ações.
            </div>
          </div>
        </div>
      @endif
    @endif
  </div>
  <hr>
</section>
