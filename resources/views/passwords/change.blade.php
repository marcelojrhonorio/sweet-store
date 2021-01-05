@extends('layouts/login')

@section('title', 'Recuperar senha')

@section('content')
  <div class="form-box">
    <div class="form-top">
      <h3>
        Redefinir senha <br>
        <span>Informe seu e-mail, sua senha atual e uma nova senha.</span>
      </h3>
    </div>
    <form class="form-login" method="post" action="/password/change">
      @if (session('alert'))
        <div class="alert alert-{{ session('alert.type') }}">
          {{ session('alert.message') }}
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
        <div class="form-group col-12">
          <label for="actual_password">Senha atual</label>
          <input
            id="actual_password"
            class="form-control"
            name="actual_password"
            type="password"
            placeholder="Digite sua senha atual"
            required
          >
        </div>
      </div>

      <div class="row">
        <div class="form-group col-12">
          <label for="password">Nova senha</label>
          <input
            id="password"
            class="form-control"
            name="password"
            type="password"
            placeholder="Digite sua nova senha"
            required
          >
        </div>
      </div>

      <div class="row">
        <div class="form-group col-12">
          <label for="password_confirmation">Confirmar senha</label>
          <input
            id="password_confirmation"
            class="form-control"
            name="password_confirmation"
            type="password"
            placeholder="Confirme sua nova senha"
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
            <span>Salvar senha</span>
          </button>
        </div>
      </div>
    </form>
  </div>
@endsection
