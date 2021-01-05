@extends('layouts/store')

@section('title', 'Inicio')

@section('content')
  @include('partials.listing', ['category' => 'Home', 'actions' => $actions])
@endsection

@section('before-body-close')
  {{-- @include('partials.popup') --}}
@endsection
