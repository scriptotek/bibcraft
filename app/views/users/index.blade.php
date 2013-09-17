@extends('master')
@section('header')
    <h2>Brukere</h2>

@stop
@section('container')

  <ul>
@foreach ($users as $user)
    <li>
      <a href="{{ URL::action('UsersController@getShow', $user->id) }}">
      	{{ $user->name }}
      </a><br />

      Opprettet: {{ $user->created_at }}<br />

      Antall lån: {{ count($user->loans) }}<br />

      <a href="{{ URL::action('UsersController@getEdit', $user->id) }}"><i class="icon-pencil"></i> rediger</a>
      <a href="{{ URL::action('UsersController@getDelete', $user->id) }}"><i class="icon-trash"></i> slett</a>
    </li>
@endforeach
  </ul>

@stop