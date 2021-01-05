@extends('layouts/login')

@section('title', 'Login')

@section('content')
  <div class="form-box">
  <input id="page" type="hidden" value="{{ session('share_page') }}" data-page>
  <input id="site_origin"    name="site_origin" type="hidden" value={{Request::url()}}>
    @if(session('share_page') &&  'share-action' === session('share_page'))
      
      <input id="indicated_by" type="hidden" value={{ session('share_indicated_by') }} customerId>
      <input id="action_id" type="hidden" value={{ session('share_action_id') }} data-actionId>
      <input id="action_type" type="hidden" value={{ session('share_action_type') }} data-actionType>
      <input id="customer_name" type="hidden" value={{ session('share_name_indicated_by') }} data-customerName>
      
      <div class="form-top-share">
        <h3 class="top-share-title"><strong>{{ session('share_name_indicated_by') }}</strong> <span> te enviou uma oportunidade de ganhar pontos na </span>   
        <strong>Sweet Bonus</strong>!</h3> 
        
      </div>
    @else
      <div class="form-top">
        <h3>
          Sweet Bonus <br>
          <span>Faça login e continue acumulando pontos!</span>
        </h3>
      </div>
    @endif
    <form class="form-login" method="post" data-form-register>
    
      @if (session('alert'))
        <div class="alert alert-{{ session('alert.type') }}" data-form-register-alert-session>
          {{ session('alert.message') }}
        </div>
      @endif

      <div class="alert sr-only" data-form-register-alert>
        {{-- Alert Feedback --}}
      </div>

      <div class="row" data-login-email-group>
        <div class="form-group col-12">
          <label for="email">E-mail</label>
          <input
            id="email"
            class="form-control"
            name="email"
            type="email"
            placeholder="Ex: nome@gmail.com"
            required
            data-login-email-input
          >
        </div>
      </div>
      
      @if(!env('CHANGE_PASS'))
      <div class="row sr-only" data-login-password-group>
        <div class="form-group col-12">
          <label for="password">Senha</label>
          <input
            id="password"
            class="form-control"
            name="password"
            type="password"
            placeholder="Digite sua senha"  
            required        
            data-login-password-input
          >
        </div>
      </div>  

      <div class="row">
        <div class="form-group password-reset-group col-12" data-password-reset>
          Problemas de acesso ou esqueceu sua senha? 
          <a class="password-reset-link" href="{{ url('password/receive') }}">
          clique aqui
          </a> para solicitar uma nova em seu email.
          Deseja mudar sua senha atual?
          <a class="password-reset-link" href="{{ url('password/change') }}">
          redefina aqui.
          </a>
        </div>
      </div>
      @else
      <div class="row sr-only" data-login-password-group>
        <div class="form-group col-12">
          <label for="password">Data de nascimento</label>
          <input
            id="password"
            class="form-control"
            name="password"
            type="text"
            placeholder="Ex.: 01/01/1990"  
            required        
            data-login-password-input
          >
        </div>
      </div> 
      @endif      

      <div class="row sr-only" data-login-back-group>
        <div class="form-group password-reset-group col-12">
        @if(Session::get('share_page') &&  'share-action' === session('share_page'))
          <a class="password-reset-link" href="{{ env('APP_URL') }}/share-action/postback?customer_id={{ session('share_indicated_by') }}&action_id={{ session('share_action_id') }}&action_type={{ session('share_action_type') }}" data-login-link>
            Voltar  
          </a>
        @else
          <a class="password-reset-link" href="{{ url('login') }}">
            Voltar
          </a>
        @endif
        </div>
      </div>
      
      <div class="row">
        <div class="col-12 col-centered">
          <input name="_token" type="hidden" value="{{ csrf_token() }}" data-form-token>
            <input name="sweetbonus" type="hidden" value="{{ env('SWEETBONUS_URL') }}" data-login-sweetbonus-url>
            <input name="change_pass" type="hidden" value="{{ env('CHANGE_PASS') }}" data-change-pass>
          <button class="submit-button" type="submit" data-login-btn>
            <span data-login-btn-text>Entrar</span>
          </button>
        </div>
      </div>
    </form>
  </div>
@endsection