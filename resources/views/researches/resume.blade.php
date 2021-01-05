@extends('layouts.store')

@section('title', 'Obrigado!')

@section('content')
  <section id="ganhar-pontos">
    <div class="container">
      <div class="row">
        <div class="col-9 col-centered text-center">
          
          @php
          
            $pointsDiff = session('points') - $comingFromResearch['previousBalance']
          
          @endphp
          
          <h3>Pesquisa concluída. <span>Obrigado!</span></h3>

          <p>Você já tinha acumulado <b>{{ $comingFromResearch['previousBalance'] }} pontos</b> 
         
          @if (0==$comingFromResearch['earnedPoints'])
            ! A pesquisa pode estar temporariamente indisponível ou você pode não ter perfil! Tente novamente mais tarde!
          @else
            e ganhou <b> {{ $comingFromResearch['earnedPoints'] }} pontos</b> por participar dessa pesquisa</p>           
          @endif        


          @if($pointsDiff !== $comingFromResearch['researchPoints'])
          
            <p> <b> + 30 pontos</b> por confirmar seu e-mail!</p>
          
          @endif

          @if ($comingFromResearch['earnedPoints'] > 0)
            <p>Parabéns! Agora você tem <b>{{ session('points') }} pontos.</b></p>
          @endif

          <p><b>Continue participando!</b></p>
          <br>
          <p><a class="btn btn-info" target="_blank" href="/researches" style="background-color: #1b6d85;-webkit-box-shadow: 3px 3px 0 0 #0c5460; box-shadow: 3px 3px 0 0 #0c5460;">Eu quero responder mais pesquisas!</a></p>
          <br>
          <center>Ou</center>
          <br>
          <p><a class="btn btn-info"  target="_blank" href="/" >Eu quero mais ações pontuadas!</a></p>
        </div>
      </div>
    </div>
  </section>
@endsection
