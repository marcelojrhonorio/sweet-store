@if (count($products))
  <section id="featured-products">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h3>Produtos que você <span>está próximo de trocar</span></h3>
        </div>
      </div>
       <div class="col-10 col-centered">
      <div class="row">
        @foreach ($products as $item)
          <div class="col-xs-6 col-sm-6 col-lg-3">
            <a class="product product--two-columns" href="#" data-btn-exchange data-points='{{ $item->points }}' data-id='{{ $item->id }}'>
              <div class="product__figure">
                <img class="product-thumb" src="{{ env('APP_IMAGE_CAMPAIGN_URL') . '/' . $item->path_image }}">
              </div>
              <div class="product__data product-info">
                <h4 class="mb-0">
                  <span class="product-title">
                    {{ $item->title }}
                  </span>
                  <span class="product-price">
                    {{ number_format($item->points,0,",",".") }} pontos
                  </span>
                </h4>
                <button class="product-btn">
                  <span class="sr-only">
                    Resgatar
                  </span>
                </button>
              </div>
            </a>
          </div>
        @endforeach
      </div>
       </div>
    </div>
  </section>
@endif
