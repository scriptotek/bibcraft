@extends('master')
@section('container')

    {{ Form::model($user, array(
        'action' => array('UsersController@deleteDestroy', $user->id),
        'method' => 'DELETE',
        'class' => 'form-horizontal'
    )) }}

      <h2>Slett brukeren {{$user->name}} ? </h2>
      <p>
        NB!! Dette vil slette alle brukerens lån også!!
      </p>

      {{ Form::submit('Pff, pff, slett i vei', array('class' => 'btn btn-danger')) }}

    {{ Form::close() }}

@stop