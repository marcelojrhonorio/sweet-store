@extends('layouts.store')

@section('title', 'Perfil')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-10 col-centered">
        <h1 class="page-title">
          Minha <strong>conta</strong>
        </h1>
        @include('profile.partials.form')
      </div>
    </div>
  </div>
@endsection
