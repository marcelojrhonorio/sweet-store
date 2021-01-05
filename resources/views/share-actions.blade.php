@extends('layouts/store')

@section('title', 'Ganhar pontos')

@section('content')
  @include('partials.listing', ['category' => $category, 'actions' => $actions])
@endsection