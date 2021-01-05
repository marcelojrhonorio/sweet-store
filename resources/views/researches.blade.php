@extends('layouts/store')

@section('title', 'Pesquisas Patrocinadas')

@section('content')
  @include('partials.listing-invites', ['surveys' => $surveys])
@endsection
