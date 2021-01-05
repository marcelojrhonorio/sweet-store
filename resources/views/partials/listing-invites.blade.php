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
    
    <input 
      type="hidden" 
      value=@if(session('comingFromEmail')){{ session('comingFromEmail.researchLink') }}@else 0 @endif
      data-coming-from-email
    >

    <div class="row">
      @if (false === empty($surveys))
        <div class="col-11 col-centered">
          <div class="row itens">
            @foreach ($surveys as $survey)

              <div class="col-xs-12 col-sm-12 col-lg-3">
                <div class="item card">
                  <img
                    class="card-img-top"
                    src="{{ asset('images/researches_icon.jpg') }}"
                    alt="Pesquisas Patrocinada!"
                  >
                  <div class="card-body">
                    <p>
                      <span class="action">
                        Pesquisa patrocinada - {{ \Carbon\Carbon::parse($survey->created_at)->format('d/m/Y') }}
                      </span>
                      <br>
                        Mais uma vez o seu perfil foi escolhido pelos nossos parceiros para responder!
                      <br>
                        Basta clicar no link abaixo e responder a uma pesquisa.
                    </p>
                  </div>
                  @if(0 == $survey->status)
                    <a target="_blank" href="" data-btn-research data-link="{{ $survey->invite }}" data-action="{{ $survey->id }}">
                      <h6>Até 60 pontos<br /></h6>
                      <h5>Responder Agora!</h5>
                    </a>
                   @else
                    <a target="_blank" href="" onclick="return false;" style="background-color: #bbbbbb;-webkit-box-shadow: 3px 3px 0 0 #999999; box-shadow: 3px 3px 0 0 #999999;" disabled="disabled">
                      <h6>Uhu o/ !!!!></h6>
                      <h5>Respondido!</h5>
                    </a>
                  @endif
                </div>
              </div>

            @endforeach
          </div>
        </div>
    </div>
    @endif
  </div>
  <hr>
</section>
