@extends('layouts.store')

@section('title', 'Descadastro')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-10 col-centered">
        <h1 class="page-title">
          Descadastro
        </h1>
        @include('unsubscribe.partials.form')
      </div>
    </div>
  </div>
@endsection
