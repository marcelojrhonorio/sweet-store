<section id="ganhar-pontos" style="background-color: #EDFBFF">
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
    <div class="row">
      @if (false === empty($stamps))
        <div class="col-11 col-centered">
        @foreach($stamps as $stamp)  
            
            @if($stamp->data)              
              <h3><span>{{ $stamp->data[0]->typeStamp }}</span></h3>
              
              @endif
              <div class="row">
              
            @foreach($stamp->data as $stampsByType)
                      
                <div class="col-xs-12 col-sm-6 col-lg-3" style="margin-bottom: 20px;"> 
                <div class="item card-stamp">
                <div class="container cont-stamp">
                     
                <div class="row">
                      <div class="col-md-4">
                        <img
                          class="card-img-top-stamps"
                          src="{{ env('APP_STORAGE') }}/storage/{{ $stampsByType->icon }}"                          
                          alt="Selo de Pontuação!"
                        >
                      </div>    

                      <div class="col-md-8">
                              <div class="card-body-stamps">
                                <p>
                                  <span class="action stamp-title">
                                    {{ $stampsByType->title }}
                                  </span>
                                  <br>
                                      Faça {{ $stampsByType->required_amount }} {{ $stampsByType->message_stamps}}.
                                </p>
                              </div>
                      </div>

                </div> 

                <div class="row">
                     <div class="col-md-12">
                          <div class="progress">
                            @if ($stampsByType->progress_stamps <= 25)
                              <div class="progress-bar progress1" role="progressbar" style="width:{{ $stampsByType->progress_stamps }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{ $stampsByType->progress_stamps }}%</div>
                            @elseif (($stampsByType->progress_stamps > 25) and ($stampsByType->progress_stamps <= 50))
                              <div class="progress-bar progress2" role="progressbar" style="width:{{ $stampsByType->progress_stamps }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{ $stampsByType->progress_stamps }}%</div>  
                            @elseif (($stampsByType->progress_stamps > 50) and ($stampsByType->progress_stamps < 100))  
                              <div class="progress-bar progress3" role="progressbar" style="width:{{ $stampsByType->progress_stamps }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{ $stampsByType->progress_stamps }}%</div>  
                            @else 
                            <div class="progress-bar progress4" role="progressbar" style="width:{{ $stampsByType->progress_stamps }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{ $stampsByType->progress_stamps }}%</div>  
                            @endif
                          </div>
                      </div>
                </div>
                      
                      
              </div>               
              </div>               
              </div>
             
            
          @endforeach
          </div>
        @endforeach
         
     
    </div>
    @endif
  </div>
  <hr>
</section>
