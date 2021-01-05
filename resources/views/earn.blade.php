@extends('layouts/store')

@section('title', 'Ganhar pontos')

@section('content')
  @if(false === empty($attributes))
    @include('partials.listing', ['category' => $attributes->name, 'actions' => $attributes->actions])
  @else
    @include('partials.listing', ['category' => '', 'actions' => array() ])
  @endif

@endsection
