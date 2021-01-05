@extends('layouts/login')

@section('title', 'Receber senha')

@section('content')
  <div class="form-box">
    <div class="form-top">
      <h3>
        Receber nova senha <br>
        <span>Informe seu e-mail.</span>
      </h3>
    </div>
    <form class="form-login" method="post" action="/password/receive">
      @if (session('alert'))
        <div class="alert alert-{{ session('alert.type') }}">
        {!! session('alert.message') !!}
        </div>
      @endif

      <div class="row">
        <div class="form-group col-12">
          <label for="email">E-mail</label>
          <input
            id="email"
            class="form-control"
            name="email"
            type="email"
            placeholder="Ex: nome@gmail.com"
            required
          >
        </div>
      </div>

      <div class="row">
        <div class="form-group password-reset-group col-12">
          <a class="password-reset-link" href="{{ url('login') }}">
            Voltar para login
          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-centered">
          <input name="_token" type="hidden" value="{{ csrf_token() }}">
          <button class="submit-button" type="submit">
            <span>Receber senha</span>
          </button>
        </div>
      </div>
    </form>
  </div>
@endsection
