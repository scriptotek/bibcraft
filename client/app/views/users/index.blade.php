@extends('master')
@section('header')
    <h2>Brukere</h2>

@stop
@section('container')

  <ul class="list-group">
@foreach ($users as $user)
    <li class="list-group-item">
      <a href="{{ URL::action('UsersController@getShow', $user->id) }}">
      	{{ $user->name ?: '(uten navn)' }}
      </a> : {{ $user->phone }}<br />

      Opprettet: {{ $user->created_at }}<br />

      Antall lån: {{ count($user->loans) }}<br />

      <a href="{{ URL::action('UsersController@getEdit', $user->id) }}"><i class="icon-pencil"></i> rediger</a>
      <a href="{{ URL::action('UsersController@getDelete', $user->id) }}"><i class="icon-trash"></i> slett</a>
    </li>
@endforeach
  </ul>

@stop