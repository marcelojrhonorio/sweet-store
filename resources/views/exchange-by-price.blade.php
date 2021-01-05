@extends('layouts/store')

@section('title', 'Trocar pontos')

@section('content')
  <section id="ganhar-pontos">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="page-title">
            <b>Trocar pontos</b>
          </h1>
          @if ($products)
            <div class="row" data-stamps-required>
              <div class="col-10 col-centered">
                <div class="row">
                  @foreach ($products->products as $product)
                  @php
                    if((null != $product->social_network) && ('' != $product->social_network)) {
                      $seletor = 'data-social-network' . $product->id;
                    } else {
                      $seletor = '';
                    }                    
                  @endphp
                  <input type="hidden" value="{{ $product->social_network }}" {{$seletor}}>
                    <div class="col-xs-12 col-sm-12 col-lg-3">

                      <div class="item card">
                        <img src="{{ env('APP_IMAGE_CAMPAIGN_URL') . '/' . $product->path_image }}">
                        <div class="card-body">
                          <p>
                            <span class="action">
                              {{ $product->title }}
                            </span>
                            <br>
                            {{ $product->description }}
                          </p>
                        </div>

                        @if(env("STAMPS_REQUIRED")) 
                          @if($products->stamps)                      
                            @foreach($products->stamps as $stamp) 
                              @if($stamp->product_id == $product->id)
                                @if((session('points') >= $product->points) && ($stamp->status_stamp))
                                  <a href="#" class="exchange" data-btn-exchange data-points="{{ $product->points }}" data-id="{{ $product->id }}">
                                @else
                                  <a href="#" class="exchange-disabled" data-btn-exchange data-points="{{ $product->points }}" data-id="{{ $product->id }}">
                                @endif 
                                <h6>   {{ number_format($product->points,0,",",".") }} pontos</h6>
                                @if(0 != $stamp->qtd_stamps)
                                  <h6> + {{ $stamp->qtd_stamps }} selo(s)</h6>
                                @endif
                              @endif                                 
                            @endforeach  
                          @endif
                        @else
                          @if(session('points') >= $product->points)
                            <a href="#" class="exchange" data-btn-exchange data-points="{{ $product->points }}" data-id="{{ $product->id }}">
                          @else
                            <a href="#" class="exchange-disabled" data-btn-exchange data-points="{{ $product->points }}" data-id="{{ $product->id }}">
                          @endif
                          <h6>   {{ number_format($product->points,0,",",".") }} pontos<br></h6>
                        @endif 
                        <h5> Resgatar</h5>                            
                        </a>

                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @else
            <div class="alert alert-warning alert-center">
              <b>Oops!</b> Ainda não temos produtos nessa faixa de pontos.
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
@endsection
